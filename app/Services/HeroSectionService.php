<?php

namespace App\Services;

use App\Models\HeroSection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HeroSectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * HeroSectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List hero sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of hero sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return HeroSection::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['title', 'subtitle'], $request->search);
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
                    $query->latest();
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->with(['user', 'images'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new hero section.
     *
     * @param array $data The validated data for creating a new hero section.
     * @return HeroSection The newly created hero section.
     */
    public function create(array $data): HeroSection
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Create hero section
            $heroSection = HeroSection::query()->create($data);

            // Handle images
            if (isset($data['images']) && is_array($data['images'])) {
                $this->handleImages($heroSection, $data['images']);
            }

            return $heroSection->load(['user', 'images']);
        });
    }

    /**
     * Update an existing hero section.
     *
     * @param HeroSection $heroSection The hero section to update.
     * @param array $data The validated data for updating the hero section.
     * @return HeroSection The updated hero section.
     */
    public function update(HeroSection $heroSection, array $data): HeroSection
    {
        return DB::transaction(function () use ($heroSection, $data) {
            // Update hero section
            $heroSection->update($data);

            // Handle images
            if (isset($data['images']) && is_array($data['images'])) {
                // Delete old images
                foreach ($heroSection->images as $image) {
                    $this->storageService->delete($image->image_path);
                    $image->delete();
                }

                $this->handleImages($heroSection, $data['images']);
            }

            return $heroSection->load(['user', 'images']);
        });
    }

    /**
     * Delete a hero section.
     *
     * @param HeroSection $heroSection The hero section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(HeroSection $heroSection): ?bool
    {
        return DB::transaction(function () use ($heroSection) {
            return $heroSection->delete();
        });
    }

    /**
     * Permanently delete a hero section and its related files.
     *
     * @param HeroSection $heroSection The hero section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(HeroSection $heroSection): ?bool
    {
        return DB::transaction(function () use ($heroSection) {
            // Delete images
            foreach ($heroSection->images as $image) {
                $this->storageService->delete($image->image_path);
                $image->delete();
            }

            return $heroSection->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted hero section.
     *
     * @param HeroSection $heroSection The hero section to restore.
     * @return HeroSection The restored hero section.
     */
    public function restore(HeroSection $heroSection): HeroSection
    {
        return DB::transaction(function () use ($heroSection) {
            $heroSection->restore();
            return $heroSection->load(['user', 'images']);
        });
    }

    /**
     * Handle images for a hero section.
     *
     * @param HeroSection $heroSection The hero section.
     * @param array $images The array of image files.
     * @return void
     */
    private function handleImages(HeroSection $heroSection, array $images): void
    {
        $order = 0;

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $heroSection->images()->create([
                    'image_path' => $this->uploadImage(
                        $image,
                        config('filestorage.paths.hero_images')
                    ),
                    'order' => $order++,
                ]);
            }
        }
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
}
