<?php

namespace App\Http\Requests\Admin\StorefrontStudio;

use Illuminate\Foundation\Http\FormRequest;

class ReorderSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_key' => 'required|string',
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:storefront_sections,id',
        ];
    }
}
