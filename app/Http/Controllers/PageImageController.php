<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageImageRequest;
use App\Http\Resources\PageImageResource;
use App\Models\PageImage;
use App\Services\ACLService;
use App\Services\PageImageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Page Image Controller
 *
 * Handles CRUD operations for Page Images.
 * Includes methods to list, create, update, delete, restore for Page Image records.
 *
 * @package App\Http\Controllers
 * @tags Page Image
 */
class PageImageController extends Controller
{
    /**
     * @var PageImageService
     */
    protected PageImageService $pageImageService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * PageImageController constructor.
     *
     * @param PageImageService $pageImageService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(PageImageService $pageImageService, ACLService $ACLService, Request $request)
    {
        $this->pageImageService = $pageImageService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Fetch a paginated list of page images based on search and filter parameters.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: PageImageResource[],
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

        $pageImages = $this->pageImageService->list($request);
        return response()->paginatedSuccess(PageImageResource::collection($pageImages), 'Page images retrieved successfully');
    }

    /**
     * Create and store a new page image.
     *
     * @requestMediaType multipart/form-data
     * @param PageImageRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageImageResource
     *   }
     */
    public function store(PageImageRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $pageImage = $this->pageImageService->create($request->validated());
            return response()->success(new PageImageResource($pageImage), 'Page image created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create page image: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve and display a specific page image.
     *
     * @param PageImage $pageImage
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageImageResource
     *   }
     */
    public function show(PageImage $pageImage): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission('view settings');
        }

        return response()->success(new PageImageResource($pageImage), 'Page image retrieved successfully');
    }

    /**
     * Update an existing page image.
     *
     * @requestMediaType multipart/form-data
     * @param PageImageRequest $request
     * @param PageImage $pageImage
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageImageResource
     *   }
     */
    public function update(PageImageRequest $request, PageImage $pageImage): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $updatedPageImage = $this->pageImageService->update($pageImage, $request->validated());
            return response()->success(new PageImageResource($updatedPageImage), 'Page image updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update page image: ' . $e->getMessage());
        }
    }

    /**
     * Delete a page image.
     *
     * @param PageImage $pageImage
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(PageImage $pageImage): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $this->pageImageService->delete($pageImage);
            return response()->success(null, 'Page image deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete page image: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a page image.
     *
     * @param PageImage $pageImage
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function forceDestroy(PageImage $pageImage): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $this->pageImageService->forceDelete($pageImage);
            return response()->success(null, 'Page image force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete page image: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted page image.
     *
     * @param PageImage $pageImage
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageImageResource
     *   }
     */
    public function restore(PageImage $pageImage): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission('edit settings');
            $restoredPageImage = $this->pageImageService->restore($pageImage);
            return response()->success(new PageImageResource($restoredPageImage), 'Page image restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Page image not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore page image: ' . $e->getMessage());
        }
    }

    /**
     * Get the latest image by type.
     *
     * @param string $type
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: PageImageResource
     *   }
     */
    public function getByType(string $type): JsonResponse
    {
        try {
            $pageImage = $this->pageImageService->getLatestByType($type);
            
            if (!$pageImage) {
                return response()->notFound('No image found for this type');
            }
            
            return response()->success(new PageImageResource($pageImage), 'Page image retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve page image: ' . $e->getMessage());
        }
    }
}