<?php

namespace App\Http\Requests\Admin\StorefrontStudio;

use Illuminate\Foundation\Http\FormRequest;

class AddSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_key' => 'required|string',
            'type' => 'required|string',
            'after_order' => 'nullable|integer',
            'slot' => 'nullable|string|in:before,after',
        ];
    }
}
