<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    /**
     * List pages based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of pages.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return Page::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->boolean('published_only'), function ($query) {
                $query->published();
            })
            ->when(
                $request->filled('order_by') && $request->filled('order_direction'),
                function ($query) use ($request) {
                    $query->orderBy($request->order_by, $request->order_direction);
                },
                function ($query) {
                    $query->orderBy('order', 'asc')->orderBy('title', 'asc');
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new page.
     *
     * @param array $data The validated data for creating a new page.
     * @return Page The newly created page.
     */
    public function create(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Create page
            return Page::create($data);
        });
    }

    /**
     * Update an existing page.
     *
     * @param Page $page The page to update.
     * @param array $data The validated data for updating the page.
     * @return Page The updated page.
     */
    public function update(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data) {
            // Generate slug if not provided
            if (empty($data['slug']) && isset($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Update page
            $page->update($data);

            return $page;
        });
    }

    /**
     * Delete a page.
     *
     * @param Page $page The page to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(Page $page): ?bool
    {
        return DB::transaction(function () use ($page) {
            return $page->delete();
        });
    }

    /**
     * Permanently delete a page.
     *
     * @param Page $page The page to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(Page $page): ?bool
    {
        return DB::transaction(function () use ($page) {
            return $page->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted page.
     *
     * @param Page $page The page to restore.
     * @return Page The restored page.
     */
    public function restore(Page $page): Page
    {
        return DB::transaction(function () use ($page) {
            $page->restore();
            return $page;
        });
    }

    /**
     * Find a page by its slug.
     *
     * @param string $slug The slug to search for.
     * @return Page|null The page with the given slug, or null if not found.
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)->published()->first();
    }

    /**
     * Toggle the published status of a page.
     *
     * @param Page $page The page to toggle.
     * @return Page The updated page.
     */
    public function togglePublished(Page $page): Page
    {
        return DB::transaction(function () use ($page) {
            $page->update([
                'is_published' => !$page->is_published,
            ]);
            return $page;
        });
    }
}