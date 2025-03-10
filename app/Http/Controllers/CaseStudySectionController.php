<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseStudySectionRequest;
use App\Http\Resources\CaseStudySectionResource;
use App\Models\CaseStudySection;
use App\Services\ACLService;
use App\Services\CaseStudySectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Case Study Section Controller
 *
 * Handles CRUD operations for Case Study Sections.
 * Includes methods to list, create, update, delete, restore for Case Study Section records.
 *
 * @package App\Http\Controllers
 * @tags Case Study Section
 */
class CaseStudySectionController extends Controller
{
    /**
     * @var CaseStudySectionService
     */
    protected CaseStudySectionService $caseStudySectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * CaseStudySectionController constructor.
     *
     * @param CaseStudySectionService $caseStudySectionService
     * @param ACLService $ACLService
     */
    public function __construct(CaseStudySectionService $caseStudySectionService, ACLService $ACLService)
    {
        $this->caseStudySectionService = $caseStudySectionService;
        $this->ACLService = $ACLService;
    }

    /**
     * Display a listing of the case study sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: CaseStudySectionResource[],
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
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        $caseStudySections = $this->caseStudySectionService->list($request);
        return response()->paginatedSuccess(CaseStudySectionResource::collection($caseStudySections), 'Case study sections retrieved successfully');
    }

    /**
     * Store a newly created case study section.
     *
     * @requestMediaType multipart/form-data
     * @param CaseStudySectionRequest $request
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *          data: CaseStudySectionResource
     *      }
     */
    public function store(CaseStudySectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $caseStudySection = $this->caseStudySectionService->create($request->validated());
            return response()->success(new CaseStudySectionResource($caseStudySection), 'Case study section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create case study section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified case study section.
     *
     * @param CaseStudySection $caseStudySection
     * @return JsonResponse
     * @response array{
     *           status: boolean,
     *           message: string,
     *           data: CaseStudySectionResource
     *       }
     */
    public function show(CaseStudySection $caseStudySection): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        return response()->success(new CaseStudySectionResource($caseStudySection->load('client')), 'Case study section retrieved successfully');
    }

    /**
     * Update the specified case study section.
     *
     * @requestMediaType multipart/form-data
     * @param CaseStudySectionRequest $request
     * @param CaseStudySection $caseStudySection
     * @return JsonResponse
     * @response array{
     *           status: boolean,
     *           message: string,
     *           data: CaseStudySectionResource
     *       }
     */
    public function update(CaseStudySectionRequest $request, CaseStudySection $caseStudySection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedCaseStudySection = $this->caseStudySectionService->update($caseStudySection, $request->validated());
            return response()->success(new CaseStudySectionResource($updatedCaseStudySection), 'Case study section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update case study section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified case study section.
     *
     * @param CaseStudySection $caseStudySection
     * @return JsonResponse
     * @response array{
     *           status: boolean,
     *           message: string
     *       }
     */
    public function destroy(CaseStudySection $caseStudySection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->caseStudySectionService->delete($caseStudySection);
            return response()->success(null, 'Case study section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete case study section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified case study section.
     *
     * @param CaseStudySection $caseStudySection
     * @return JsonResponse
     * @response array{
     *           status: boolean,
     *           message: string
     *       }
     */
    public function forceDestroy(CaseStudySection $caseStudySection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->caseStudySectionService->forceDelete($caseStudySection);
            return response()->success(null, 'Case study section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete case study section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified case study section.
     *
     * @param CaseStudySection $caseStudySection
     * @return JsonResponse
     * @response array{
     *           status: boolean,
     *           message: string
     *       }
     */
    public function restore(CaseStudySection $caseStudySection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredCaseStudySection = $this->caseStudySectionService->restore($caseStudySection);
            return response()->success(new CaseStudySectionResource($restoredCaseStudySection), 'Case study section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Case study section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore case study section: ' . $e->getMessage());
        }
    }
}
