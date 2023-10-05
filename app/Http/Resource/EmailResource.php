<?php

namespace App\Http\Resource;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Estimate;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Traits\ColumnsFilterOnResourceTrait;
use App\Traits\GetRecordStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Email
 */
class EmailResource extends BaseResourceWithCustomField
{
    /**
     * Trait for column-wise filtering in resource
     */
    use ColumnsFilterOnResourceTrait;
    use GetRecordStatusTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'emailId' => $this->email_id,
            'status' => $this->status,
            'tokenId' => $this->token_id,
            'receivedDate' => $this->received_date,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'content' => $this->content,
            'relatedEntity' => $this->getRelatedItem(),
            'relatedToEntity' => str_replace('App\Models\\', '', $this->relatedEmailAssociation?->entity),
        ];
    }

    private function getRelatedItem(): JsonResource|null
    {
        return match ($this->relatedEmailAssociation?->entity) {
            Account::class => new AccountResource($this->relatedEmailAssociation->relatedItem, customFieldList: [
                'first-name',
                'last-name',
                'account-name',
            ]),
            Contact::class => new ContactResource(
                $this->relatedEmailAssociation->relatedItem,
                customFieldList: ['first-name', 'last-name']
            ),
            Lead::class => new LeadResource($this->relatedEmailAssociation->relatedItem, customFieldList: [
                'first-name',
                'last-name',
            ]),
            Opportunity::class => new OpportunityResource(
                $this->relatedEmailAssociation->relatedItem,
                customFieldList: ['opportunity-name']
            ),
            Estimate::class => new EstimateResource(
                $this->relatedEmailAssociation->relatedItem,
                customFieldList: ['estimate_name']
            ),
            default => null,
        };
    }
}
