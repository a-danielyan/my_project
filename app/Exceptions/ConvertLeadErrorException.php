<?php

namespace App\Exceptions;

use App\Http\Resource\AccountMinimalResource;
use App\Http\Resource\ContactMinimalResource;
use App\Http\Resource\LeadResource;
use App\Http\Resource\OpportunityMinimalResource;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ConvertLeadErrorException extends Exception
{
    public function __construct(
        private ?Account $duplicatedAccount,
        private ?Contact $duplicatedContact,
        private ?Lead $leadData,
        private ?Collection $opportunity,
        string $message = 'Duplicated record founded',
        int $code = 409,
    ) {
        parent::__construct($message, $code);
    }

    public function getData(): array
    {
        return [
            'account' => new AccountMinimalResource($this->duplicatedAccount),
            'contact' => new ContactMinimalResource($this->duplicatedContact),
            'lead' => new LeadResource($this->leadData),
            'opportunity' => !empty($this->opportunity) ? OpportunityMinimalResource::collection(
                $this->opportunity,
            ) : null,
        ];
    }
}
