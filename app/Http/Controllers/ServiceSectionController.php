<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceSectionRequest;
use App\Http\Resources\ServiceSectionResource;
use App\Models\ServiceSection;
use App\Services\ACLService;
use App\Services\ServiceSectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Service Section Controller
 *
 * Handles operations for managing service information.
 * Includes methods to view and update service information.
 *
 * @package App\Http\Controllers
 * @tags Service Section
 */
class ServiceSectionController extends Controller
{
    /**
     * @var ServiceSectionService
     */
    protected ServiceSectionService $serviceSectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * ServiceSectionController constructor.
     *
     * @param ServiceSectionService $serviceSectionService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(ServiceSectionService $serviceSectionService, ACLService $ACLService, Request $request)
    {
        $this->serviceSectionService = $serviceSectionService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Display a listing of the service sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: ServiceSectionResource[],
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

        $serviceSections = $this->serviceSectionService->list($request);
        return response()->paginatedSuccess(ServiceSectionResource::collection($serviceSections), 'Service sections retrieved successfully');
    }

    /**
     * Store a newly created service section.
     *
     * @requestMediaType multipart/form-data
     * @param ServiceSectionRequest $request
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: ServiceSectionResource
     *    }
     */
    public function store(ServiceSectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $serviceSection = $this->serviceSectionService->create($request->validated());
            return response()->success(new ServiceSectionResource($serviceSection), 'Service section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create service section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified service section.
     *
     * @param ServiceSection $serviceSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: ServiceSectionResource
     *    }
     */
    public function show(ServiceSection $serviceSection): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        }

        return response()->success(new ServiceSectionResource($serviceSection), 'Service section retrieved successfully');
    }

    /**
     * Update the specified service section.
     *
     * @requestMediaType multipart/form-data
     * @param ServiceSectionRequest $request
     * @param ServiceSection $serviceSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: ServiceSectionResource
     *    }
     */
    public function update(ServiceSectionRequest $request, ServiceSection $serviceSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedServiceSection = $this->serviceSectionService->update($serviceSection, $request->validated());
            return response()->success(new ServiceSectionResource($updatedServiceSection), 'Service section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update service section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service section.
     *
     * @param ServiceSection $serviceSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string
     *    }
     */
    public function destroy(ServiceSection $serviceSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->serviceSectionService->delete($serviceSection);
            return response()->success(null, 'Service section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete service section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified service section.
     *
     * @param ServiceSection $serviceSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string
     *    }
     */
    public function forceDestroy(ServiceSection $serviceSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->serviceSectionService->forceDelete($serviceSection);
            return response()->success(null, 'Service section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete service section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified service section.
     *
     * @param ServiceSection $serviceSection
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: ServiceSectionResource
     *    }
     */
    public function restore(ServiceSection $serviceSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredServiceSection = $this->serviceSectionService->restore($serviceSection);
            return response()->success(new ServiceSectionResource($restoredServiceSection), 'Service section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Service section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore service section: ' . $e->getMessage());
        }
    }

    /**
     * Reorder service sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *    }
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $request->validate([
                'ordered_ids' => ['required', 'array'],
                'ordered_ids.*' => ['required', 'exists:service_sections,id'],
            ]);

            $this->serviceSectionService->reorder($request->input('ordered_ids'));
            return response()->success(null, 'Service sections reordered successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to reorder service sections: ' . $e->getMessage());
        }
    }
}
