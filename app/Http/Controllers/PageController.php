<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Services\ACLService;
use App\Services\PageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Page Controller
 *
 * Handles CRUD operations for Pages.
 * Includes methods to list, create, update, delete, restore for Page records.
 *
 * @package App\Http\Controllers
 * @tags Page
 */
class PageController extends Controller
{
    /**
     * @var PageService
     */
    protected PageService $pageService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * PageController constructor.
     *
     * @param PageService $pageService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(PageService $pageService, ACLService $ACLService, Request $request)
    {
        $this->pageService = $pageService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Fetch a paginated list of pages based on search and filter parameters.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: PageResource[],
     *      meta: array{
     *          current_page: int,
     *          last_page: int,
     *          per_page: int,
     *          total: int
     *      }
     *  }
     */
    public function index(Request $request): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission('view settings');
        }

        $pages = $this->pageService->list($request);
        return response()->paginatedSuccess(PageResource::collection($pages), 'Pages retrieved successfully');
    }

    /**
     * Create and store a new page.
     *
     * @param PageRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function store(PageRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $page = $this->pageService->create($request->validated());
            return response()->success(new PageResource($page), 'Page created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create page: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve and display a specific page.
     *
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function show(Page $page): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission('view settings');
        }

        return response()->success(new PageResource($page), 'Page retrieved successfully');
    }

    /**
     * Retrieve a page by its slug.
     *
     * @param string $slug
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $page = $this->pageService->findBySlug($slug);
        
        if (!$page) {
            return response()->notFound('Page not found');
        }
        
        return response()->success(new PageResource($page), 'Page retrieved successfully');
    }

    /**
     * Update an existing page.
     *
     * @param PageRequest $request
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function update(PageRequest $request, Page $page): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $updatedPage = $this->pageService->update($page, $request->validated());
            return response()->success(new PageResource($updatedPage), 'Page updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update page: ' . $e->getMessage());
        }
    }

    /**
     * Delete a page.
     *
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(Page $page): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $this->pageService->delete($page);
            return response()->success(null, 'Page deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete page: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a page.
     *
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function forceDestroy(Page $page): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $this->pageService->forceDelete($page);
            return response()->success(null, 'Page force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete page: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted page.
     *
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function restore(Page $page): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $restoredPage = $this->pageService->restore($page);
            return response()->success(new PageResource($restoredPage), 'Page restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Page not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore page: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the published status of a page.
     *
     * @param Page $page
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageResource
     *   }
     */
    public function togglePublished(Page $page): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $updatedPage = $this->pageService->togglePublished($page);
            return response()->success(
                new PageResource($updatedPage),
                $updatedPage->is_published ? 'Page published successfully' : 'Page unpublished successfully'
            );
        } catch (Exception $e) {
            return response()->serverError('Failed to toggle page status: ' . $e->getMessage());
        }
    }
}