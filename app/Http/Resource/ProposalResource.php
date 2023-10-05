<?php

namespace App\Http\Resource;

use App\Exceptions\NotFoundException;
use App\Helpers\CommonHelper;
use App\Http\Services\Publish\PublishTokenStrategy\PublishTokenServiceFactory;
use App\Models\Proposal;
use App\Models\PublishDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Proposal
 */
class ProposalResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws NotFoundException
     */
    public function toArray(Request $request): array
    {
        if ($this->resource == null) {
            return [];
        }

        return
            [
                'id' => $this->id,
                'isExpired' => $this->checkExpired(),
                'opportunityId' => $this->opportunity_id,
                'status' => $this->status,
                'pdfLink' => $this->pdf_link,
                'publicToken' => $this->getPublicToken(),
                'template' => new TemplateResource($this->template),
                'estimates' => EstimateResource::collection($this->estimates),
            ];
    }

    /**
     * @return string|null
     * @throws NotFoundException
     */
    private function getPublicToken(): ?string
    {
        /** @var PublishDetail $publishDetail */
        $publishDetail = PublishDetail::query()->where('entity_type', PublishDetail::ENTITY_TYPE_PROPOSAL)->where(
            'entity_id',
            $this->id,
        )->first();

        if (empty($publishDetail)) {
            $user = CommonHelper::getCronUser();
            $params['entity_type'] = PublishDetail::ENTITY_TYPE_PROPOSAL;
            $params['entity_id'] = $this->id;
            $publishDetail = PublishTokenServiceFactory::getService(PublishDetail::ENTITY_TYPE_PROPOSAL)
                ->createToken($params, $user);
        }

        return $publishDetail?->token;
    }

    private function checkExpired(): bool
    {
        $availableEstimates = $this->estimates->filter(function ($item) {
            return $item->estimate_validity_duration > now();
        });

        return $availableEstimates->isEmpty();
    }
}
