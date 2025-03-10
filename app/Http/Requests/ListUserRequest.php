<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;

class ListUserRequest extends BaseRequest
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
             * Search term to filter users.
             *
             * @query
             * @var string|null $search
             * @example "John"
             */
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * Field to order the results by.
             *
             * @query
             * @var string|null $order_by
             * @example "created_at"
             */
            'order_by' => ['sometimes', 'nullable', 'string', 'in:id,name,email,created_at,updated_at,status'],

            /**
             * Direction to order the results.
             *
             * @query
             * @var string|null $order_direction
             * @example "desc"
             */
            'order_direction' => ['sometimes', 'nullable', 'string', 'in:asc,desc'],

            /**
             * Number of items per page.
             *
             * @query
             * @var int|null $per_page
             * @example 15
             */
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],

            /**
             * Page number.
             *
             * @query
             * @var int|null $page
             * @example 1
             */
            'page' => ['sometimes', 'integer', 'min:1'],

            /**
             * Whether to include only trashed (deleted) items.
             *
             * @query
             * @var bool|null $trashed_only
             * @example false
             */
            'trashed_only' => ['sometimes', 'nullable', 'boolean'],

            /**
             * Start date for filtering by date range.
             *
             * @query
             * @var string|null $start_date
             * @example "2023-01-01"
             */
            'start_date' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],

            /**
             * End date for filtering by date range.
             *
             * @query
             * @var string|null $end_date
             * @example "2023-12-31"
             */
            'end_date' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],

            /**
             * Filter by status.
             *
             * @query
             * @var string|null $status
             * @example "active"
             */
            'status' => ['sometimes', 'nullable', 'string', Rule::in(StatusEnum::values())],

            /**
             * Filter by role.
             *
             * @query
             * @var string|null $role
             * @example "admin"
             */
            'role' => ['sometimes', 'nullable', 'string', 'exists:roles,name'],
        ];
    }
}
