<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressSuggestionRequest extends FormRequest
{
    /**
     * The search page is public, so anyone may ask for suggestions (rate limited by route).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:3', 'max:200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Enter an address to search for.',
            'query.min' => 'Enter at least 3 characters.',
            'query.max' => 'The address is too long.',
        ];
    }
}
