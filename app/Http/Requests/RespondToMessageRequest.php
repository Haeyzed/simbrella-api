<?php

namespace App\Http\Requests;

class RespondToMessageRequest extends BaseRequest
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
            /**
             * The response to the message.
             *
             * @var string $response
             * @example "Thank you for your inquiry. We will get back to you soon."
             */
            'response' => ['required', 'string', 'max:5000'],

            /**
             * Whether to send an email with the response.
             *
             * @var bool $send_email
             * @example true
             */
            'send_email' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'response' => 'Response message',
            'send_email' => 'Send email',
        ];
    }
}
