<?php

namespace App\Http\Resource;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Estimate;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Traits\ColumnsFilterOnResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends ActivityMinimalResource
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            parent::toArray($request),
            [

                'relatedToEntity' => str_replace('App\Models\\', '', $this->related_to_entity),
                'relatedItem' => $this->getRelatedItem(),
            ],
        );
    }

    /**
     * @return JsonResource|null
     */
    private function getRelatedItem(): JsonResource|null
    {
        return match ($this->related_to_entity) {
            Account::class => new AccountResource($this->relatedItem, customFieldList: [
                'first-name',
                'last-name',
                'account-name',
            ]),
            Contact::class => new ContactResource($this->relatedItem, customFieldList: ['first-name', 'last-name']),
            Lead::class => new LeadResource($this->relatedItem, customFieldList: ['first-name', 'last-name']),
            Opportunity::class => new OpportunityResource($this->relatedItem, customFieldList: ['opportunity-name']),
            Estimate::class => new EstimateResource($this->relatedItem, customFieldList: ['estimate_name']),
            default => null,
        };
    }
}
