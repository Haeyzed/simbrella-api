<?php

namespace App\Services;

use App\Models\AboutSection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AboutSectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * AboutSectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List about sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of about sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return AboutSection::query()
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
                    $query->latest();
                }
            )
            ->when($request->boolean('trashed_only'), function ($query) {
                $query->onlyTrashed();
            })
            ->with(['user'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new about section.
     *
     * @param array $data The validated data for creating a new about section.
     * @return AboutSection The newly created about section.
     */
    public function create(array $data): AboutSection
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
                    config('filestorage.paths.about_images')
                );
                unset($data['image']);
            }

            // Create about section
            return AboutSection::create($data)->load(['user']);
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
     * Update an existing about section.
     *
     * @param AboutSection $aboutSection The about section to update.
     * @param array $data The validated data for updating the about section.
     * @return AboutSection The updated about section.
     */
    public function update(AboutSection $aboutSection, array $data): AboutSection
    {
        return DB::transaction(function () use ($aboutSection, $data) {
            // Handle image
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($aboutSection->image_path) {
                    $this->storageService->delete($aboutSection->image_path);
                }

                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.about_images')
                );
                unset($data['image']);
            }

            // Update about section
            $aboutSection->update($data);

            return $aboutSection->load(['user']);
        });
    }

    /**
     * Delete an about section.
     *
     * @param AboutSection $aboutSection The about section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(AboutSection $aboutSection): ?bool
    {
        return DB::transaction(function () use ($aboutSection) {
            return $aboutSection->delete();
        });
    }

    /**
     * Permanently delete an about section and its related files.
     *
     * @param AboutSection $aboutSection The about section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(AboutSection $aboutSection): ?bool
    {
        return DB::transaction(function () use ($aboutSection) {
            // Delete image
            if ($aboutSection->image_path) {
                $this->storageService->delete($aboutSection->image_path);
            }

            return $aboutSection->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted about section.
     *
     * @param AboutSection $aboutSection The about section to restore.
     * @return AboutSection The restored about section.
     */
    public function restore(AboutSection $aboutSection): AboutSection
    {
        return DB::transaction(function () use ($aboutSection) {
            $aboutSection->restore();
            return $aboutSection->load(['user']);
        });
    }
}
