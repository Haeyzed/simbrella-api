<?php

namespace App\Http\Controllers;

use App\Http\Requests\HeroSectionRequest;
use App\Http\Resources\HeroSectionResource;
use App\Models\HeroSection;
use App\Services\ACLService;
use App\Services\HeroSectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Hero Section Controller
 *
 * Handles operations for managing hero information.
 * Includes methods to view and update hero information.
 *
 * @package App\Http\Controllers
 * @tags Hero Section
 */
class HeroSectionController extends Controller
{
    /**
     * @var HeroSectionService
     */
    protected HeroSectionService $heroSectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * HeroSectionController constructor.
     *
     * @param HeroSectionService $heroSectionService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(HeroSectionService $heroSectionService, ACLService $ACLService, Request $request)
    {
        $this->heroSectionService = $heroSectionService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Display a listing of the hero sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *         status: boolean,
     *         message: string,
     *         data: HeroSectionResource[],
     *         meta: array{
     *             current_page: int,
     *             last_page: int,
     *             per_page: int,
     *             total: int
     *         }
     *     }
     */
    public function index(Request $request): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        }

        $heroSections = $this->heroSectionService->list($request);
        return response()->paginatedSuccess(HeroSectionResource::collection($heroSections), 'Hero sections retrieved successfully');
    }

    /**
     * Store a newly created hero section.
     *
     * @requestMediaType multipart/form-data
     * @param HeroSectionRequest $request
     * @return JsonResponse
     * @response array{
     *             status: boolean,
     *             message: string,
     *             data: HeroSectionResource
     *         }
     */
    public function store(HeroSectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $heroSection = $this->heroSectionService->create($request->validated());
            return response()->success(new HeroSectionResource($heroSection), 'Hero section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create hero section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified hero section.
     *
     * @param HeroSection $heroSection
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string,
     *              data: HeroSectionResource
     *          }
     */
    public function show(HeroSection $heroSection): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        }

        return response()->success(new HeroSectionResource($heroSection->load(['images'])), 'Hero section retrieved successfully');
    }

    /**
     * Update the specified hero section.
     *
     * @requestMediaType multipart/form-data
     * @param HeroSectionRequest $request
     * @param HeroSection $heroSection
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string,
     *              data: HeroSectionResource
     *          }
     */
    public function update(HeroSectionRequest $request, HeroSection $heroSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedHeroSection = $this->heroSectionService->update($heroSection, $request->validated());
            return response()->success(new HeroSectionResource($updatedHeroSection), 'Hero section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update hero section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified hero section.
     *
     * @param HeroSection $heroSection
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string
     *          }
     */
    public function destroy(HeroSection $heroSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->heroSectionService->delete($heroSection);
            return response()->success(null, 'Hero section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete hero section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified hero section.
     *
     * @param HeroSection $heroSection
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string
     *          }
     */
    public function forceDestroy(HeroSection $heroSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->heroSectionService->forceDelete($heroSection);
            return response()->success(null, 'Hero section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete hero section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified hero section.
     *
     * @param HeroSection $heroSection
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string
     *          }
     */
    public function restore(HeroSection $heroSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredHeroSection = $this->heroSectionService->restore($heroSection);
            return response()->success(new HeroSectionResource($restoredHeroSection), 'Hero section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Hero section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore hero section: ' . $e->getMessage());
        }
    }
}
