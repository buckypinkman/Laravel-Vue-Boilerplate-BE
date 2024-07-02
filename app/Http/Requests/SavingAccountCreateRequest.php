<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SavingAccountCreateRequest extends FormRequest
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
            'title' => 'required',
            'target_amount' => 'required|numeric',
            'target_period' => 'required|date',
            'saving_account_category_id' => 'required|exists:saving_account_categories,id',
            'member_id' => 'required|exists:members,id',
        ];
    }

    public function prepareForValidation()
    {
        $memberId = $this->all()['member_id'] ?? null;
        if(empty($memberId)) {
            Log::info(Auth::user());
            $this->merge([
                'member_id' => Auth::user()->member_id,
            ]);

        }
    }
}
