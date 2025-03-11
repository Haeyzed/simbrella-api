<?php

namespace App\Http\Controllers;

use App\Http\Requests\AboutSectionRequest;
use App\Http\Resources\AboutSectionResource;
use App\Models\AboutSection;
use App\Services\ACLService;
use App\Services\AboutSectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * About Section Controller
 *
 * Handles operations for managing about information.
 * Includes methods to view and update about information.
 *
 * @package App\Http\Controllers
 * @tags About Section
 */
class AboutSectionController extends Controller
{
    /**
     * @var AboutSectionService
     */
    protected AboutSectionService $aboutSectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * AboutSectionController constructor.
     *
     * @param AboutSectionService $aboutSectionService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(AboutSectionService $aboutSectionService, ACLService $ACLService, Request $request)
    {
        $this->aboutSectionService = $aboutSectionService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Display a listing of the about sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: AboutSectionResource[],
     *       meta: array{
     *           current_page: int,
     *           last_page: int,
     *           per_page: int,
     *           total: int
     *       }
     *   }
     */
    public function index(Request $request): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        }

        $aboutSections = $this->aboutSectionService->list($request);
        return response()->paginatedSuccess(AboutSectionResource::collection($aboutSections), 'About sections retrieved successfully');
    }

    /**
     * Store a newly created about section.
     *
     * @requestMediaType multipart/form-data
     * @param AboutSectionRequest $request
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: AboutSectionResource
     *    }
     */
    public function store(AboutSectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $aboutSection = $this->aboutSectionService->create($request->validated());
            return response()->success(new AboutSectionResource($aboutSection), 'About section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create about section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified about section.
     *
     * @param AboutSection $aboutSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: AboutSectionResource
     *    }
     */
    public function show(AboutSection $aboutSection): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        }

        return response()->success(new AboutSectionResource($aboutSection), 'About section retrieved successfully');
    }

    /**
     * Update the specified about section.
     *
     * @requestMediaType multipart/form-data
     * @param AboutSectionRequest $request
     * @param AboutSection $aboutSection
     * @return JsonResponse
     * @response array{
     *         status: boolean,
     *         message: string,
     *         data: AboutSectionResource
     *     }
     */
    public function update(AboutSectionRequest $request, AboutSection $aboutSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedAboutSection = $this->aboutSectionService->update($aboutSection, $request->validated());
            return response()->success(new AboutSectionResource($updatedAboutSection), 'About section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update about section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified about section.
     *
     * @param AboutSection $aboutSection
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *      }
     */
    public function destroy(AboutSection $aboutSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->aboutSectionService->delete($aboutSection);
            return response()->success(null, 'About section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete about section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified about section.
     *
     * @param AboutSection $aboutSection
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *      }
     */
    public function forceDestroy(AboutSection $aboutSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->aboutSectionService->forceDelete($aboutSection);
            return response()->success(null, 'About section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete about section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified about section.
     *
     * @param AboutSection $aboutSection
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *          data: AboutSectionResource
     *      }
     */
    public function restore(AboutSection $aboutSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredAboutSection = $this->aboutSectionService->restore($aboutSection);
            return response()->success(new AboutSectionResource($restoredAboutSection), 'About section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('About section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore about section: ' . $e->getMessage());
        }
    }
}
