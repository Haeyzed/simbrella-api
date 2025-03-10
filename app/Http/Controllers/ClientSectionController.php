<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientSectionRequest;
use App\Http\Resources\ClientSectionResource;
use App\Models\ClientSection;
use App\Services\ACLService;
use App\Services\ClientSectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Client Section Controller
 *
 * Handles operations for managing client information.
 * Includes methods to view and update client information.
 *
 * @package App\Http\Controllers
 * @tags Client Section
 */
class ClientSectionController extends Controller
{
    /**
     * @var ClientSectionService
     */
    protected ClientSectionService $clientSectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * ClientSectionController constructor.
     *
     * @param ClientSectionService $clientSectionService
     * @param ACLService $ACLService
     */
    public function __construct(ClientSectionService $clientSectionService, ACLService $ACLService)
    {
        $this->clientSectionService = $clientSectionService;
        $this->ACLService = $ACLService;
    }

    /**
     * Display a listing of the client sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *        status: boolean,
     *        message: string,
     *        data: ClientSectionResource[],
     *        meta: array{
     *            current_page: int,
     *            last_page: int,
     *            per_page: int,
     *            total: int
     *        }
     *    }
     */
    public function index(Request $request): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        $clientSections = $this->clientSectionService->list($request);
        return response()->paginatedSuccess(ClientSectionResource::collection($clientSections), 'Client sections retrieved successfully');
    }

    /**
     * Store a newly created client section.
     *
     * @requestMediaType multipart/form-data
     * @param ClientSectionRequest $request
     * @return JsonResponse
     * @response array{
     *            status: boolean,
     *            message: string,
     *            data: ClientSectionResource
     *        }
     */
    public function store(ClientSectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $clientSection = $this->clientSectionService->create($request->validated());
            return response()->success(new ClientSectionResource($clientSection), 'Client section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create client section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified client section.
     *
     * @param ClientSection $clientSection
     * @return JsonResponse
     */
    public function show(ClientSection $clientSection): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        return response()->success(new ClientSectionResource($clientSection->load('caseStudy')), 'Client section retrieved successfully');
    }

    /**
     * Update the specified client section.
     *
     * @requestMediaType multipart/form-data
     * @param ClientSectionRequest $request
     * @param ClientSection $clientSection
     * @return JsonResponse
     */
    public function update(ClientSectionRequest $request, ClientSection $clientSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedClientSection = $this->clientSectionService->update($clientSection, $request->validated());
            return response()->success(new ClientSectionResource($updatedClientSection), 'Client section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update client section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified client section.
     *
     * @param ClientSection $clientSection
     * @return JsonResponse
     */
    public function destroy(ClientSection $clientSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->clientSectionService->delete($clientSection);
            return response()->success(null, 'Client section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete client section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified client section.
     *
     * @param ClientSection $clientSection
     * @return JsonResponse
     */
    public function forceDestroy(ClientSection $clientSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->clientSectionService->forceDelete($clientSection);
            return response()->success(null, 'Client section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete client section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified client section.
     *
     * @param ClientSection $clientSection
     * @return JsonResponse
     */
    public function restore(ClientSection $clientSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredClientSection = $this->clientSectionService->restore($clientSection);
            return response()->success(new ClientSectionResource($restoredClientSection), 'Client section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Client section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore client section: ' . $e->getMessage());
        }
    }

    /**
     * Reorder client sections.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $request->validate([
                'ordered_ids' => ['required', 'array'],
                'ordered_ids.*' => ['required', 'exists:client_sections,id'],
            ]);

            $this->clientSectionService->reorder($request->input('ordered_ids'));
            return response()->success(null, 'Client sections reordered successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to reorder client sections: ' . $e->getMessage());
        }
    }
}
