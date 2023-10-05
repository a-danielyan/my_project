<?php

namespace App\Http\Requests\User;

use App\Http\Services\DashboardService;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Rules\AlphaSpaceHyphen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BaseUserProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge_recursive(
            [
                'firstName' => [
                    new AlphaSpace(),
                    'min:1',
                    'max:50',
                ],
                'lastName' => [
                    new AlphaSpaceHyphen(),
                    'min:1',
                    'max:50',
                ],
                'phone' => [
                    'nullable',
                    'phone:AUTO',
                ],
                'status' => [
                    'string',
                    Rule::in([
                        User::STATUS_ACTIVE,
                        User::STATUS_INACTIVE,
                    ]),
                ],
                'userSignature' => [
                    'string',
                ],
                'dashboardBlocks' => [
                    'array',
                ],
                'dashboardBlocks.*.' . DashboardService::DASHBOARD_MARKETING_SECTION => [
                    'bool',
                ],
                'dashboardBlocks.*.' . DashboardService::DASHBOARD_SALES_AND_REVENUE_SECTION => [
                    'bool',
                ],
                'dashboardBlocks.*.' . DashboardService::DASHBOARD_ACCOUNT_SECTION => [
                    'bool',
                ],
                'dashboardBlocks.*.' . DashboardService::DASHBOARD_PRODUCTION_SECTION => [
                    'bool',
                ],
                'dashboardBlocks.*.' . DashboardService::DASHBOARD_SHIPPING_SECTION => [
                    'bool',
                ],
            ],
        );
    }

    /**
     * @return void
     * @throws ValidationException
     */
    protected function prepareForValidation(): void
    {
        $dashboardBlocks = $this->dashboardBlocks;
        if (is_array($dashboardBlocks)) {
            foreach ($dashboardBlocks as $block => $value) {
                $dashboardBlocks[$block] = $this->toBoolean($value);
                if (!in_array($block, DashboardService::DASHBOARD_ALLOWED_SECTIONS)) {
                    throw ValidationException::withMessages(
                        ['dashboardBlocks' => $block . ' section name is incorrect'],
                    );
                }
            }
            $this->merge([
                'dashboardBlocks' => $dashboardBlocks,
            ]);
        }
        parent::prepareForValidation();
    }


    /**
     * Convert to boolean
     *
     * @param $param
     * @return ?boolean
     */
    private function toBoolean($param): ?bool
    {
        if ($param === null) {
            return null;
        }

        return filter_var($param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
