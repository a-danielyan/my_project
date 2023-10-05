<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Exceptions\ModelCreateErrorException;
use App\Exceptions\ModelUpdateErrorException;
use App\Helpers\CustomFieldValuesHelper;
use App\Helpers\StorageHelper;
use App\Http\Repositories\EstimateAttachmentRepository;
use App\Http\Repositories\EstimateItemRepository;
use App\Http\Repositories\EstimateRepository;
use App\Http\Repositories\ProductRepository;
use App\Http\Repositories\ProposalRepository;
use App\Http\Repositories\TemplateRepository;
use App\Http\Resource\EstimateResource;
use App\Jobs\CreateStripeQuote;
use App\Jobs\FinalizeStripeQuote;
use App\Jobs\UpdateStripeQuote;
use App\Models\Contact;
use App\Models\Estimate;
use App\Models\EstimateAttachment;
use App\Models\Opportunity;
use App\Models\Proposal;
use App\Models\Stage;
use App\Models\User;
use App\Traits\FillShippingAndBillingAddressTrait;
use App\Traits\TaxableModelTotals;
use App\Traits\TaxCalculationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Jobs\GenerateInvoice;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class EstimateService extends BaseService
{
    use TaxCalculationTrait;
    use FillShippingAndBillingAddressTrait;
    use TaxableModelTotals;

    private EstimateItemRepository $estimateItemRepository;
    private OpportunityService $opportunityService;

    public function __construct(
        EstimateRepository $estimateRepository,
        EstimateItemRepository $estimateItemRepository,
        OpportunityService $opportunityService,
        private EstimateAttachmentRepository $estimateAttachmentRepository,
        private ProductRepository $productRepository,
        private ProposalRepository $proposalRepository,
        private TemplateRepository $templateRepository,
    ) {
        $this->repository = $estimateRepository;
        $this->estimateItemRepository = $estimateItemRepository;
        $this->opportunityService = $opportunityService;
    }

    public function resource(): string
    {
        return EstimateResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     * @throws ValidationException
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate(
            $this->repository->getAllWithCustomFields($params, $user, [
                'opportunity',
                'account',
                'contact',
            ]),
        );
    }

    /**
     * @param array $data
     * @param Authenticatable|User $user
     * @return EstimateResource
     * @throws CustomErrorException
     * @throws ModelCreateErrorException
     */
    public function store(array $data, Authenticatable|User $user): EstimateResource
    {
        DB::beginTransaction();
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Estimate::class;
        $data['status'] = Estimate::ESTIMATE_STATUS_DRAFT;
        $data['estimate_number_for_opportunity'] = $this->repository
                ->getCountByParams(where: ['opportunity_id' => $data['opportunity_id']]) + 1;

        $data = $this->addMissedAddresses($data);

        /** @var Estimate $model */
        $model = $this->repository->create($data);

        $this->estimateItemRepository->saveEstimateItems($data['itemGroups'], $model->getKey());


        $this->updateModelTotals($data, $model);


        $this->opportunityService->update(
            ['estimates_count' => $data['estimate_number_for_opportunity']],
            $model->opportunity,
            $user,
        );

        CreateStripeQuote::dispatch($model);
        if ($this->repository->save($model)) {
            $this->updateTags($data, $model);
            $this->createOrAddToProposal($model, $user);

            $resource = $this->resource();
            DB::commit();

            return new $resource($model);
        }
        throw new ModelCreateErrorException();
    }

    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['updated_by'] = $user->getKey();
        $data['entity_type'] = Estimate::class;

        return $data;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param Authenticatable|User $user
     * @return void
     * @throws CustomErrorException
     * @throws ModelUpdateErrorException
     */
    protected function afterUpdate(Model $model, array $data, Authenticatable|User $user): void
    {
        /** @var Estimate $model */
        if (isset($data['itemGroups'])) {
            $this->estimateItemRepository->saveEstimateItems($data['itemGroups'], $model->getKey());
            UpdateStripeQuote::dispatch($model);

            //update summary amount
            $this->updateModelTotals($data, $model);
        }

        /** @var Estimate $model */
        if ($model->status === Estimate::ESTIMATE_STATUS_SENT) {
            FinalizeStripeQuote::dispatch($model);
        }

        if (isset($data['status']) && $data['status'] === Estimate::ESTIMATE_STATUS_ACCEPTED) {
            GenerateInvoice::dispatch($model);
            $this->markOpportunityClosedWon($model, $user);
        }
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            [
                'tag',
                'customFields',
                'customFields.customField',
                'estimateItemGroup',
                'estimateItemGroup.contact',
                'estimateItemGroup.items',
                'estimateItemGroup.items.product',
                'estimateItemGroup.items.product.customFields',
                'estimateItemGroup.items.product.customFields.customField',
                'contact',
                'contact.customFields',
                'contact.customFields.customField',
                'account',
                'account.customFields',
                'account.customFields.customField',
                'account.contacts',
                'account.contacts.customFields',
                'account.contacts.customFields.customField',
                'account.contacts.customFields.relatedContactType',
                'account.contacts.customFields.relatedUser',
                'opportunity',
                'opportunity.customFields',
                'opportunity.customFields.customField',
                'attachments',
                'invoice',
            ],
        );

        return parent::show($model, $resource);
    }

    /**
     * @param Estimate $model
     * @param Authenticatable $author
     * @return void
     * @throws CustomErrorException
     * @throws ModelUpdateErrorException
     */
    private function markOpportunityClosedWon(Estimate $model, Authenticatable $author): void
    {
        $opportunity = $model->opportunity;
        if (!$opportunity) {
            throw new CustomErrorException('This estimate doesnt have assigned opportunity', 422);
        }
        $closedWonStage = Stage::query()->where('name', Stage::CLOSED_WON_STAGE)->first();

        $this->opportunityService->update(
            ['stage_id' => $closedWonStage->getKey(), 'closed_at' => now()],
            $opportunity,
            $author,
        );
    }

    public function previewEstimate(Estimate $estimate)
    {
        //todo implement this method
    }

    public function generatePdf(Estimate $estimate)
    {
        //todo implement this method
    }

    public function createInvoice(Estimate $estimate): void
    {
        GenerateInvoice::dispatch($estimate);
    }

    /**
     * @param array $data
     * @param Estimate $estimate
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Estimate $estimate, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->estimateAttachmentRepository->create([
                'estimate_id' => $estimate->getKey(),
                'name' => $data['name'] ?? '',
                'attachment_link' => $data['link'],
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/estimate/' . $estimate->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->estimateAttachmentRepository->create([
                        'estimate_id' => $estimate->getKey(),
                        'attachment_file' => $savedFile,
                        'name' => $data['name'] ?? '',
                        'created_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    /**
     * @param array $data
     * @param Estimate $estimate
     * @param EstimateAttachment $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Estimate $estimate,
        EstimateAttachment $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->estimateAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'attachment_file' => null,
                'name' => $data['name'] ?? '',
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/estimate/' . $estimate->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->estimateAttachmentRepository->update($attachment, [
                        'attachment_file' => $savedFile,
                        'attachment_link' => null,
                        'name' => $data['name'] ?? '',
                        'updated_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    public function deleteAttachment(EstimateAttachment $attachment): void
    {
        $this->estimateAttachmentRepository->delete($attachment);
    }

    /**
     * Addresses workflow 4.1 and 4.2  #58
     * @param array $data
     * @return array
     * @throws CustomErrorException
     */
    private function addMissedAddresses(array $data): array
    {
        /** @var Opportunity $opportunity */
        $opportunity = Opportunity::query()->find($data['opportunity_id']);
        $opportunityCustomFields = CustomFieldValuesHelper::getCustomFieldValues($opportunity, ['contact-name']);

        if (empty($opportunityCustomFields['contact-name'])) {
            throw new CustomErrorException('Opportunity must contain selected contact', 422);
        }

        $savedBillingAddress = [];
        $savedShippingAddress = [];

        /** @var Contact $contact */
        $contact = Contact::query()->find($data['contact_id']);
        $this->convertSavedAddressArray($contact, $savedBillingAddress, $savedShippingAddress);

        return $this->fillSavedBillingAndShippingAddresses($data, $savedBillingAddress, $savedShippingAddress);
    }

    /**
     * @param Estimate $model
     * @param Authenticatable|User $user
     * @return void
     * @throws CustomErrorException
     * @throws ModelCreateErrorException
     */
    private function createOrAddToProposal(Estimate $model, Authenticatable|User $user): void
    {
        /** @var Proposal $existedProposal */
        $existedProposal = $this->proposalRepository->getForOpportunity($model->opportunity_id);
        if (!$existedProposal) {
            /** @var ProposalService $proposalService */
            $proposalService = resolve(ProposalService::class);

            $defaultProposalTemplate = $this->templateRepository->getDefaultProposalTemplate();
            if (empty($defaultProposalTemplate)) {
                throw new CustomErrorException('There no default template for proposals.', 422);
            }
            $proposalData = [
                'opportunity_id' => $model->opportunity_id,
                'template_id' => $defaultProposalTemplate->getKey(),
                'estimates' => [
                    $model->getKey(),
                ],
            ];
            $proposalService->store($proposalData, $user);
        } else {
            $estimate = $existedProposal->estimateProposalRelation->last();
            $sortOrder = $estimate->sort_order + 1;

            $existedProposal->estimates()->attach(
                [$model->getKey()],
                ['created_at' => now(), 'sort_order' => $sortOrder],
            );
        }
    }
}
