<?php

namespace App\Http\Services;

use App\Http\Repositories\SequenceRepository;
use App\Http\Resource\SequenceResource;
use App\Models\Contact;
use App\Models\Sequence\SequenceEntityAssociation;
use App\Models\Sequence\SequenceTemplateAssociation;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\JsonResource;

class SequenceService extends BaseService
{
    public function __construct(
        SequenceRepository $sequenceRepository,
    ) {
        $this->repository = $sequenceRepository;
    }

    public function resource(): string
    {
        return SequenceResource::class;
    }

    /**
     * @param array $params
     * @param Authenticatable|User $user
     * @return array
     */
    public function getAll(array $params, Authenticatable|User $user): array
    {
        return $this->paginate($this->repository->get($user, $params));
    }

    public function show(Model $model, string $resource = null): JsonResource
    {
        $model->load(['templatesAssociation', 'entityRelation']);
        $resource = $resource ?? $this->resource();

        return new $resource($model);
    }


    protected function afterStore(Model $model, array $data, Authenticatable|User $user): void
    {
        foreach ($data['templates'] as $template) {
            SequenceTemplateAssociation::query()->create([
                'sequence_id' => $model->getKey(),
                'template_id' => $template['templateId'],
                'send_after' => $template['sendAfter'],
                'send_after_unit' => $template['sendAfterUnit'],
            ]);
        }

        foreach ($data['entity'] as $entity) {
            $entityType = match ($entity['type']) {
                'Lead' => Lead::class,
                default => Contact::class,
            };


            SequenceEntityAssociation::query()->create([
                'entity_type' => $entityType,
                'entity_id' => $entity['id'],
                'count_emails_sent' => 0,
                'sequence_id' => $model->getKey(),
            ]);
        }

        parent::afterStore($model, $data, $user);
    }
}
