<?php

namespace App\Http\Requests\Admin\StorefrontStudio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'config' => 'required|array',
            'style' => 'nullable|array',
        ];
    }
}
