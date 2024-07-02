<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavingAccountCategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'slug' => 'required|unique:saving_account_categories,slug,' . $this->slug . ',slug,deleted_at,NULL',
            'color' => 'nullable',
            'icon' => 'nullable',
        ];
    }
}
