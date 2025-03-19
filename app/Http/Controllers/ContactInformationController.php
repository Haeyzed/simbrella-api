<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInformationRequest;
use App\Http\Resources\ContactInformationResource;
use App\Models\ContactInformation;
use App\Services\ACLService;
use App\Services\ContactInformationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contact Information Controller
 *
 * Handles operations for managing contact information.
 * Includes methods to view and update contact information.
 *
 * @package App\Http\Controllers
 * @tags Contact Information
 */
class ContactInformationController extends Controller
{
    /**
     * The contact information service instance.
     *
     * @var ContactInformationService
     */
    protected ContactInformationService $contactInformationService;

    /**
     * The ACL service instance.
     *
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * ContactInformationController constructor.
     *
     * @param ContactInformationService $contactInformationService The contact information service instance.
     * @param ACLService $ACLService The ACL service instance.
     * @param Request $request
     * @return void
     */
    public function __construct(ContactInformationService $contactInformationService, ACLService $ACLService, Request $request)
    {
        $this->contactInformationService = $contactInformationService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Display the current contact information.
     *
     * This endpoint retrieves the most recent contact information record.
     * It is publicly accessible and does not require authentication.
     *
     * @return JsonResponse The JSON response containing the contact information.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: ContactInformationResource
     * }
     */
    public function show(): JsonResponse
    {
        // No permission check needed as this is always accessible
        $contactInformation = ContactInformation::with('user')->latest()->first();
        return response()->success(
            new ContactInformationResource($contactInformation),
            'Contact information retrieved successfully'
        );
    }

    /**
     * Update the contact information.
     *
     * This endpoint updates the contact information or creates a new record if none exists.
     * It requires authentication and the 'contact_update' permission.
     *
     * @param ContactInformationRequest $request The validated request containing contact information data.
     * @return JsonResponse The JSON response containing the updated contact information.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: ContactInformationResource
     * }
     * @throws Exception If the update operation fails.
     */
    public function update(ContactInformationRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.contact_update.name'));
            $contactInformation = $this->contactInformationService->updateOrCreate($request->validated());
            return response()->success(
                new ContactInformationResource($contactInformation),
                'Contact information updated successfully'
            );
        } catch (Exception $e) {
            return response()->serverError('Failed to update contact information: ' . $e->getMessage());
        }
    }
}
