<?php

namespace App\Services;

use App\Models\PageImage;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PageImageService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * PageImageService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List page images based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of page images.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return PageImage::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('alt_text', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when(
                $request->filled('order_by') && $request->filled('order_direction'),
                function ($query) use ($request) {
                    $query->orderBy($request->order_by, $request->order_direction);
                },
                function ($query) {
                    $query->latest();
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new page image.
     *
     * @param array $data The validated data for creating a new page image.
     * @return PageImage The newly created page image.
     */
    public function create(array $data): PageImage
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.page_images')
                );
            }

            // Remove the image field as it's not in the database
            if (isset($data['image'])) {
                unset($data['image']);
            }

            // Create page image
            return PageImage::create($data);
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
     * Update an existing page image.
     *
     * @param PageImage $pageImage The page image to update.
     * @param array $data The validated data for updating the page image.
     * @return PageImage The updated page image.
     */
    public function update(PageImage $pageImage, array $data): PageImage
    {
        return DB::transaction(function () use ($pageImage, $data) {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($pageImage->image_path) {
                    $this->storageService->delete($pageImage->image_path);
                }

                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.page_images')
                );
            }

            // Remove the image field as it's not in the database
            if (isset($data['image'])) {
                unset($data['image']);
            }

            // Update page image
            $pageImage->update($data);

            return $pageImage;
        });
    }

    /**
     * Delete a page image.
     *
     * @param PageImage $pageImage The page image to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(PageImage $pageImage): ?bool
    {
        return DB::transaction(function () use ($pageImage) {
            return $pageImage->delete();
        });
    }

    /**
     * Permanently delete a page image and its related file.
     *
     * @param PageImage $pageImage The page image to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(PageImage $pageImage): ?bool
    {
        return DB::transaction(function () use ($pageImage) {
            // Delete image file
            if ($pageImage->image_path) {
                $this->storageService->delete($pageImage->image_path);
            }

            return $pageImage->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted page image.
     *
     * @param PageImage $pageImage The page image to restore.
     * @return PageImage The restored page image.
     */
    public function restore(PageImage $pageImage): PageImage
    {
        return DB::transaction(function () use ($pageImage) {
            $pageImage->restore();
            return $pageImage;
        });
    }

    /**
     * Get the latest image by type.
     *
     * @param string $type The type of image to retrieve.
     * @return PageImage|null The latest image of the specified type.
     */
    public function getLatestByType(string $type): ?PageImage
    {
        return PageImage::where('type', $type)->latest()->first();
    }
}