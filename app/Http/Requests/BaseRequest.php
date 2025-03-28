<?php

namespace App\Http\Requests;

use App\Helpers\TranslateTextHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt and throw a JSON response with validation errors.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $translatedErrors = collect($validator->errors()->toArray())
            ->mapWithKeys(fn($messages, $field) => [
                $field => array_map([TranslateTextHelper::class, 'translate'], $messages)
            ])
            ->toArray();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => TranslateTextHelper::translate('Validation failed'),
                'errors' => $translatedErrors
            ], 422)
        );
    }
}
