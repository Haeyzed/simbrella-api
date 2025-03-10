<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListCareerRequest;
use App\Http\Requests\CareerRequest;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use App\Services\ACLService;
use App\Services\CareerService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CareerController extends Controller
{
    /**
     * @var CareerService
     */
    protected CareerService $careerService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * CareerController constructor.
     *
     * @param CareerService $careerService
     * @param ACLService $ACLService
     */
    public function __construct(CareerService $careerService, ACLService $ACLService)
    {
        $this->careerService = $careerService;
        $this->ACLService = $ACLService;
    }

    /**
     * Display a listing of careers.
     *
     * @param ListCareerRequest $request
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: CareerResource[],
     *        meta: array{
     *            current_page: int,
     *            last_page: int,
     *            per_page: int,
     *            total: int
     *        }
     *    }
     */
    public function index(ListCareerRequest $request): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.career_view.name'));
        $careers = $this->careerService->list($request);
        return response()->paginatedSuccess(CareerResource::collection($careers), 'Career postings retrieved successfully');
    }

    /**
     * Store a newly created career.
     *
     * @requestMediaType multipart/form-data
     * @param CareerRequest $request
     * @return JsonResponse
     * @response array{
     *         status: boolean,
     *         message: string,
     *         data: CareerResource
     *     }
     */
    public function store(CareerRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.career_create.name'));
            $career = $this->careerService->create($request->validated());
            return response()->success(new CareerResource($career), 'Career posting created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create career posting: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified career.
     *
     * @param Career $career
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *          data: CareerResource
     *      }
     */
    public function show(Career $career): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.career_view.name'));
        return response()->success(new CareerResource($career), 'Career posting retrieved successfully');
    }

    /**
     * Update the specified career.
     *
     * @requestMediaType multipart/form-data
     * @param CareerRequest $request
     * @param Career $career
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string,
     *          data: CareerResource
     *      }
     */
    public function update(CareerRequest $request, Career $career): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.career_update.name'));
            $updatedCareer = $this->careerService->update($career, $request->validated());
            return response()->success(new CareerResource($updatedCareer), 'Career posting updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update career posting: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified career.
     *
     * @param Career $career
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string
     *      }
     */
    public function destroy(Career $career): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.career_delete.name'));
            $this->careerService->delete($career);
            return response()->success(null, 'Career posting deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete career posting: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified career.
     *
     * @param Career $career
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string
     *      }
     */
    public function forceDestroy(Career $career): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.career_delete.name'));
            $this->careerService->forceDelete($career);
            return response()->success(null, 'Career posting force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete career posting: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified career.
     *
     * @param Career $career
     * @return JsonResponse
     * @response array{
     *          status: boolean,
     *          message: string
     *      }
     */
    public function restore(Career $career): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.career_restore.name'));
            $restoredCareer = $this->careerService->restore($career);
            return response()->success(new CareerResource($restoredCareer), 'Career posting restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Career posting not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore career posting: ' . $e->getMessage());
        }
    }
}
