<?php

namespace App\Services;

use App\Models\ProductSection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductSectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * ProductSectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List product sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of product sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return ProductSection::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['title', 'summary'], $request->search);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when(
                $request->filled('order_by') && $request->filled('order_direction'),
                function ($query) use ($request) {
                    $query->orderBy($request->order_by, $request->order_direction);
                },
                function ($query) {
                    $query->orderBy('order')->latest();
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->with(['user'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new product section.
     *
     * @param array $data The validated data for creating a new product section.
     * @return ProductSection The newly created product section.
     */
    public function create(array $data): ProductSection
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Handle image
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.product_images')
                );
                unset($data['image']);
            }

            // Create product section
            return ProductSection::create($data)->load(['user']);
        });
    }

    /**
     * Upload an image to storage.
     *
     * @param UploadedFile $image The image file to upload.
     * @param string $path The storage path.
     * @param array $options Additional options for the upload.
     * @return string The path to the uploaded image.
     */
    private function uploadImage(UploadedFile $image, string $path, array $options = []): string
    {
        return $this->storageService->upload($image, $path, $options);
    }

    /**
     * Permanently delete a product section and its related files.
     *
     * @param ProductSection $productSection The product section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(ProductSection $productSection): ?bool
    {
        return DB::transaction(function () use ($productSection) {
            // Delete image
            if ($productSection->image_path) {
                $this->storageService->delete($productSection->image_path);
            }

            return $productSection->forceDelete();
        });
    }

    /**
     * Delete a product section.
     *
     * @param ProductSection $productSection The product section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(ProductSection $productSection): ?bool
    {
        return DB::transaction(function () use ($productSection) {
            return $productSection->delete();
        });
    }

    /**
     * Restore a soft-deleted product section.
     *
     * @param ProductSection $productSection The product section to restore.
     * @return ProductSection The restored product section.
     */
    public function restore(ProductSection $productSection): ProductSection
    {
        return DB::transaction(function () use ($productSection) {
            $productSection->restore();
            return $productSection->load(['user']);
        });
    }

    /**
     * Reorder product sections.
     *
     * @param array $orderedIds Array of product section IDs in the desired order.
     * @return bool Whether the reordering was successful.
     */
    public function reorder(array $orderedIds): bool
    {
        return DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                ProductSection::where('id', $id)->update(['order' => $index]);
            }
            return true;
        });
    }

    /**
     * Update an existing product section.
     *
     * @param ProductSection $productSection The product section to update.
     * @param array $data The validated data for updating the product section.
     * @return ProductSection The updated product section.
     */
    public function update(ProductSection $productSection, array $data): ProductSection
    {
        return DB::transaction(function () use ($productSection, $data) {
            // Handle image
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($productSection->image_path) {
                    $this->storageService->delete($productSection->image_path);
                }

                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.product_images')
                );
                unset($data['image']);
            }

            // Update product section
            $productSection->update($data);

            return $productSection->load(['user']);
        });
    }
}
