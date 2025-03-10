<?php

namespace App\Http\Requests;

class ListCareerRequest extends BaseRequest
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
             * Search term to filter careers.
             *
             * @query
             * @var string|null $search
             * @example "Software Engineer"
             */
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],

            /**
             * Filter by status.
             *
             * @query
             * @var string|null $status
             * @example "open"
             */
            'status' => ['sometimes', 'nullable', 'string'],

            /**
             * Work format type.
             *
             * @query
             * @var string|null $format
             * @example "remote"
             */
            'format' => ['sometimes', 'nullable', 'string', 'in:remote,onsite,hybrid'],

            /**
             * Department name.
             *
             * @query
             * @var string|null $department
             * @example "Engineering"
             */
            'department' => ['sometimes', 'nullable', 'string'],

            /**
             * Type of employment.
             *
             * @query
             * @var string|null $employment_type
             * @example "full-time"
             */
            'employment_type' => ['sometimes', 'nullable', 'string', 'in:full-time,part-time,contract'],

            /**
             * Number of items per page.
             *
             * @query
             * @var int|null $per_page
             * @example 15
             */
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],

            /**
             * Page number.
             *
             * @query
             * @var int|null $page
             * @example 1
             */
            'page' => ['sometimes', 'integer', 'min:1'],

            /**
             * Field to order the results by.
             *
             * @query
             * @var string|null $order_by
             * @example "created_at"
             */
            'order_by' => ['sometimes', 'string', 'in:id,title,created_at,updated_at,status,published_at,expires_at'],

            /**
             * Direction to order the results.
             *
             * @query
             * @var string|null $order_direction
             * @example "desc"
             */
            'order_direction' => ['sometimes', 'string', 'in:asc,desc'],

            /**
             * Whether to include only trashed (deleted) items.
             *
             * @query
             * @var bool|null $trashed_only
             * @example false
             */
            'trashed_only' => ['sometimes', 'boolean'],

            /**
             * Whether to include only active items.
             *
             * @query
             * @var bool|null $active_only
             * @example true
             */
            'active_only' => ['sometimes', 'boolean'],

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
        ];
    }
}
