<?php

namespace App\Traits;

use App\Http\Repositories\CustomFieldRepository;
use Illuminate\Validation\ValidationException;

trait CustomFieldRequiredFieldValidationTrait
{
    /**
     * @param string $entityType
     * @param array $customFields
     * @return void
     * @throws ValidationException
     */
    protected function validateRequiredCustomFields(string $entityType, array $customFields): void
    {
        /** @var CustomFieldRepository $customFieldRepository */
        $customFieldRepository = resolve(CustomFieldRepository::class);
        $requiredCustomFields = $customFieldRepository->getAllRequiredByType($entityType);

        foreach ($requiredCustomFields as $field) {
            if (empty($customFields[$field])) {
                throw ValidationException::withMessages([$field => 'Field is required']);
            }
        }
    }
}
