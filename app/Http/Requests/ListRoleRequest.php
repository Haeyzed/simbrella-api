<?php

namespace App\Http\Requests;

class ListRoleRequest extends BaseRequest
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
            'order_by' => ['sometimes', 'string', 'in:id,name,created_at,updated_at'],

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
        ];
    }
}
