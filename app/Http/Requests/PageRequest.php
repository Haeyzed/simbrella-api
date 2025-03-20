<?php

namespace App\Http\Requests;

class PageRequest extends BaseRequest
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
             * The title of the page.
             *
             * @var string $title
             * @example "About Us"
             */
            'title' => ['required', 'string', 'max:255'],

            /**
             * The slug of the page.
             *
             * @var string|null $slug
             * @example "about-us"
             */
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,' . $this->route('page.id')],

            /**
             * The subtitle of the page.
             *
             * @var string|null $subtitle
             * @example "Learn more about our company"
             */
            'subtitle' => ['nullable', 'string', 'max:255'],

            /**
             * The content of the page.
             *
             * @var string $content
             * @example "<p>Welcome to our company...</p>"
             */
            'content' => ['required', 'string'],

            /**
             * The meta title of the page.
             *
             * @var string|null $meta_title
             * @example "About Our Company | Example Inc."
             */
            'meta_title' => ['nullable', 'string', 'max:255'],

            /**
             * The meta description of the page.
             *
             * @var string|null $meta_description
             * @example "Learn about our company history, mission, and values."
             */
            'meta_description' => ['nullable', 'string', 'max:255'],

            /**
             * The meta keywords of the page.
             *
             * @var string|null $meta_keywords
             * @example "company, about us, history, mission, values"
             */
            'meta_keywords' => ['nullable', 'string', 'max:255'],

            /**
             * Whether the page is published.
             *
             * @var bool $is_published
             * @example true
             */
            'is_published' => ['boolean'],

            /**
             * The order of the page.
             *
             * @var int|null $order
             * @example 1
             */
            'order' => ['nullable', 'integer', 'min:0'],
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
            'title' => 'Page title',
            'slug' => 'Page URL slug',
            'subtitle' => 'Page subtitle',
            'content' => 'Page content',
            'meta_title' => 'SEO title',
            'meta_description' => 'SEO description',
            'meta_keywords' => 'SEO keywords',
            'is_published' => 'Published status',
            'order' => 'Display order',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The page title is required.',
            'content.required' => 'The page content is required.',
            'slug.unique' => 'This URL slug is already in use. Please choose a different one.',
        ];
    }
}