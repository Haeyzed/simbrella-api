<?php

namespace App\Http\Requests;

use App\Enums\MessageStatusEnum;
use Illuminate\Validation\Rules\Enum;

class ListMessageRequest extends BaseRequest
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
             * The search term to filter messages.
             *
             * @var string|null $search
             * @example "product inquiry"
             */
            'search' => ['nullable', 'string', 'max:255'],

            /**
             * The status to filter messages.
             *
             * @var string|null $status
             * @example "unread"
             */
            'status' => ['nullable', new Enum(MessageStatusEnum::class)],

            /**
             * The field to order messages by.
             *
             * @var string|null $order_by
             * @example "created_at"
             */
            'order_by' => ['nullable', 'string', 'in:id,first_name,last_name,email,status,created_at,responded_at'],

            /**
             * The direction to order messages.
             *
             * @var string|null $order_direction
             * @example "desc"
             */
            'order_direction' => ['nullable', 'string', 'in:asc,desc'],

            /**
             * Whether to include only trashed messages.
             *
             * @var bool|null $trashed_only
             * @example false
             */
            'trashed_only' => ['nullable', 'boolean'],

            /**
             * The start date to filter messages.
             *
             * @var string|null $start_date
             * @example "2024-01-01"
             */
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],

            /**
             * The end date to filter messages.
             *
             * @var string|null $end_date
             * @example "2024-12-31"
             */
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],

            /**
             * The number of messages per page.
             *
             * @var int|null $per_page
             * @example 15
             */
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
