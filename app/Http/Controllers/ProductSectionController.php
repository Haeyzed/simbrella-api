<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSectionRequest;
use App\Http\Resources\ProductSectionResource;
use App\Models\ProductSection;
use App\Services\ACLService;
use App\Services\ProductSectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Product Section Controller
 *
 * Handles operations for managing product information.
 * Includes methods to view and update product information.
 *
 * @package App\Http\Controllers
 * @tags Product Section
 */
class ProductSectionController extends Controller
{
    /**
     * @var ProductSectionService
     */
    protected ProductSectionService $productSectionService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * ProductSectionController constructor.
     *
     * @param ProductSectionService $productSectionService
     * @param ACLService $ACLService
     */
    public function __construct(ProductSectionService $productSectionService, ACLService $ACLService)
    {
        $this->productSectionService = $productSectionService;
        $this->ACLService = $ACLService;
    }

    /**
     * Display a listing of the product sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *         status: boolean,
     *         message: string,
     *         data: ProductSectionResource[],
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
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        $productSections = $this->productSectionService->list($request);
        return response()->paginatedSuccess(ProductSectionResource::collection($productSections), 'Product sections retrieved successfully');
    }

    /**
     * Store a newly created product section.
     *
     * @requestMediaType multipart/form-data
     * @param ProductSectionRequest $request
     * @return JsonResponse
     * @response array{
     *              status: boolean,
     *              message: string,
     *              data: ProductSectionResource
     *          }
     */
    public function store(ProductSectionRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_create.name'));
            $productSection = $this->productSectionService->create($request->validated());
            return response()->success(new ProductSectionResource($productSection), 'Product section created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create product section: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product section.
     *
     * @param ProductSection $productSection
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string,
     *               data: ProductSectionResource
     *           }
     */
    public function show(ProductSection $productSection): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.home_page_view.name'));
        return response()->success(new ProductSectionResource($productSection), 'Product section retrieved successfully');
    }

    /**
     * Update the specified product section.
     *
     * @requestMediaType multipart/form-data
     * @param ProductSectionRequest $request
     * @param ProductSection $productSection
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string,
     *               data: ProductSectionResource
     *           }
     */
    public function update(ProductSectionRequest $request, ProductSection $productSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $updatedProductSection = $this->productSectionService->update($productSection, $request->validated());
            return response()->success(new ProductSectionResource($updatedProductSection), 'Product section updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update product section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product section.
     *
     * @param ProductSection $productSection
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string
     *           }
     */
    public function destroy(ProductSection $productSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->productSectionService->delete($productSection);
            return response()->success(null, 'Product section deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete product section: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified product section.
     *
     * @param ProductSection $productSection
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string
     *           }
     */
    public function forceDestroy(ProductSection $productSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_delete.name'));
            $this->productSectionService->forceDelete($productSection);
            return response()->success(null, 'Product section force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete product section: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified product section.
     *
     * @param ProductSection $productSection
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string
     *           }
     */
    public function restore(ProductSection $productSection): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_restore.name'));
            $restoredProductSection = $this->productSectionService->restore($productSection);
            return response()->success(new ProductSectionResource($restoredProductSection), 'Product section restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Product section not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore product section: ' . $e->getMessage());
        }
    }

    /**
     * Reorder product sections.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *               status: boolean,
     *               message: string
     *           }
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.home_page_update.name'));
            $request->validate([
                'ordered_ids' => ['required', 'array'],
                'ordered_ids.*' => ['required', 'exists:product_sections,id'],
            ]);

            $this->productSectionService->reorder($request->input('ordered_ids'));
            return response()->success(null, 'Product sections reordered successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to reorder product sections: ' . $e->getMessage());
        }
    }
}
