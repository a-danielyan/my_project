<?php

namespace App\Http\Services;

use App\Exceptions\ApolloRateLimitErrorException;
use App\Exceptions\CustomErrorException;
use App\Helpers\StorageHelper;
use App\Http\Repositories\ContactAttachmentRepository;
use App\Http\Repositories\ContactRepository;
use App\Http\Resource\ContactResource;
use App\Models\Contact;
use App\Models\ContactAttachments;
use App\Models\User;
use App\Traits\SyncDataFromApolloTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use Throwable;

class ContactService extends BaseService
{
    use SyncDataFromApolloTrait;

    private ContactAttachmentRepository $contactAttachmentRepository;

    public const APOLLO_TO_CUSTOM_FIELD_MAPPING = [
        'id' => 'apollo_id',
        'first_name' => 'first-name',
        'last_name' => 'last-name',
        'linkedin_url' => 'linkedin_url',
        'title' => 'apollo_title',
        'photo_url' => 'avatar',
        'twitter_url' => 'twitter_url',
        'github_url' => 'github_url',
        'facebook_url' => 'facebook_url',
        'email' => 'email',
        'headline' => 'headline',
        'contact_id' => 'apollo_contact_id',
        'contact' => 'apollo_contact',
        'organization_id' => 'apollo_organization_id',
        'organization' => 'apollo_organization',
        'personal_emails' => 'personal_emails',
        'departments' => 'departments',
        'subdepartments' => 'subdepartments',
        'functions' => 'functions',
        'seniority' => 'seniority',
    ];

    public function __construct(
        ContactRepository $contactRepository,
        ContactAttachmentRepository $contactAttachmentRepository,
        private ApolloIoService $apolloIoService,
    ) {
        $this->repository = $contactRepository;
        $this->contactAttachmentRepository = $contactAttachmentRepository;
    }

    public function resource(): string
    {
        return ContactResource::class;
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
                'customFields',
                'customFields.customField',
                'attachments',
                'account',
                'account.contacts',
                'account.invoices',
                'account.opportunities',
                'account.accountsPayable',
                'account.internalNote',
                'account.customFields',
                'account.customFields.customField',
                'account.createdBy',
                'account.updatedBy',
                'createdBy',
                'updatedBy',
            ]),
        );
    }

    protected function beforeStore(array $data, Authenticatable|User $user): array
    {
        $data['created_by'] = $user->getKey();
        $data['entity_type'] = Contact::class;

        return $data;
    }

    /**
     * @param array $data
     * @param Model $model
     * @param Authenticatable|User $user
     * @return array
     * @throws CustomErrorException
     */
    protected function beforeUpdate(array $data, Model $model, Authenticatable|User $user): array
    {
        $data['entity_type'] = Contact::class;
        $data['updated_by'] = $user->getKey();

        if (isset($data['avatarFile'])) {
            try {
                $savePath = '/contact/' . $model->getKey() . '/avatar';
                $savedFile = StorageHelper::storeFile($data['avatarFile'], $savePath);
                $data['avatar'] = $savedFile;
            } catch (Throwable $e) {
                throw new CustomErrorException($e->getMessage(), 422);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param Contact $contact
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function storeAttachment(array $data, Contact $contact, User|Authenticatable $user): void
    {
        if (isset($data['link'])) {
            $this->contactAttachmentRepository->create([
                'contact_id' => $contact->getKey(),
                'attachment_link' => $data['link'],
                'name' => $data['name'] ?? '',
                'created_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/contact/' . $contact->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->contactAttachmentRepository->create([
                        'contact_id' => $contact->getKey(),
                        'name' => $data['name'] ?? '',
                        'attachment_file' => $savedFile,
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
     * @param Contact $contact
     * @param ContactAttachments $attachment
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     */
    public function updateAttachment(
        array $data,
        Contact $contact,
        ContactAttachments $attachment,
        User|Authenticatable $user,
    ): void {
        if (isset($data['link'])) {
            $this->contactAttachmentRepository->update($attachment, [
                'attachment_link' => $data['link'],
                'name' => $data['name'] ?? '',
                'attachment_file' => null,
                'updated_by' => $user->getKey(),
            ]);
        } else {
            if (isset($data['file'])) {
                try {
                    $savePath = '/contact/' . $contact->getKey() . '/attachments';
                    $savedFile = StorageHelper::storeFile($data['file'], $savePath);

                    $this->contactAttachmentRepository->update($attachment, [
                        'attachment_file' => $savedFile,
                        'name' => $data['name'] ?? '',
                        'attachment_link' => null,
                        'updated_by' => $user->getKey(),
                    ]);
                    //we can get later  file with StorageHelper::getSignedStorageUrl($filename, 's3');
                } catch (Throwable $e) {
                    throw new CustomErrorException($e->getMessage(), 422);
                }
            }
        }
    }

    public function deleteAttachment(ContactAttachments $attachment): void
    {
        $this->contactAttachmentRepository->delete($attachment);
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model = $model->load(
            'attachments',
            'tag',
            'customFields',
            'customFields.customField',
        );

        return parent::show($model, $resource);
    }

    /**
     * @param Contact $contact
     * @param User|Authenticatable $user
     * @return void
     * @throws CustomErrorException
     * @throws ApolloRateLimitErrorException
     */
    public function getDataFromApollo(
        Contact $contact,
        User|Authenticatable $user,
    ): void {
        $this->syncPeopleDataFromApollo($contact, $user);
    }
}
