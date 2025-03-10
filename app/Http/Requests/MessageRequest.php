<?php

namespace App\Http\Requests;

use App\Enums\MessageStatusEnum;
use Illuminate\Validation\Rules\Enum;

class MessageRequest extends BaseRequest
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
             * The first name of the sender.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => ['required', 'string', 'max:255'],

            /**
             * The last name of the sender.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => ['required', 'string', 'max:255'],

            /**
             * The email address of the sender.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => ['required', 'email', 'max:255'],

            /**
             * The message content.
             *
             * @var string $message
             * @example "I would like to inquire about your services."
             */
            'message' => ['required', 'string', 'max:5000'],

            /**
             * The response to the message.
             *
             * @var string|null $response
             * @example "Thank you for your inquiry. We will get back to you soon."
             */
            'response' => ['nullable', 'string', 'max:5000'],

            /**
             * The status of the message.
             *
             * @var MessageStatusEnum $status
             * @example \App\Enums\MessageStatusEnum::UNREAD
             */
            'status' => ['sometimes', new Enum(MessageStatusEnum::class)],
        ];
    }
}
