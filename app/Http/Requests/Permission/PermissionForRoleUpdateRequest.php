<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionForRoleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'permissionIds' => [
                'array',
                'required',
            ],
            'permissionIds.*' => [
                'int',
                'exists:permission,id',
            ]
        ];
    }
}
