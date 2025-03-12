<?php

namespace App\Services;

use App\Models\ServiceSection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ServiceSectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * ServiceSectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List service sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of service sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return ServiceSection::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['title', 'summary', 'title_short', 'summary_short'], $request->search);
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
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->with(['user'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new service section.
     *
     * @param array $data The validated data for creating a new service section.
     * @return ServiceSection The newly created service section.
     */
    public function create(array $data): ServiceSection
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.services'),
//                    ['resize' => [800, null]]
                );
            }

            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                $data['icon_path'] = $this->uploadImage(
                    $data['icon'],
                    config('filestorage.paths.services'),
//                    ['resize' => [800, null]]
                );
            }

            // Create service section
            return ServiceSection::create($data)->load(['user']);
        });
    }

    /**
     * Update an existing service section.
     *
     * @param ServiceSection $serviceSection The service section to update.
     * @param array $data The validated data for updating the service section.
     * @return ServiceSection The updated service section.
     */
    public function update(ServiceSection $serviceSection, array $data): ServiceSection
    {
        return DB::transaction(function () use ($serviceSection, $data) {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($serviceSection->image_path) {
                    $this->storageService->delete($serviceSection->image_path);
                }

                $data['image_path'] = $this->uploadImage(
                    $data['image'],
                    config('filestorage.paths.services')
                );
                unset($data['image']);
            }

            // Handle icon upload
            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                // Delete old icon if exists
                if ($serviceSection->icon_path) {
                    $this->storageService->delete($serviceSection->icon_path);
                }

                $data['icon_path'] = $this->uploadImage(
                    $data['icon'],
                    config('filestorage.paths.services')
                );
                unset($data['icon']);
            }

            // Update service section
            $serviceSection->update($data);

            return $serviceSection->load(['user']);
        });
    }

    /**
     * Delete a service section.
     *
     * @param ServiceSection $serviceSection The service section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(ServiceSection $serviceSection): ?bool
    {
        return DB::transaction(function () use ($serviceSection) {
            return $serviceSection->delete();
        });
    }

    /**
     * Permanently delete a service section.
     *
     * @param ServiceSection $serviceSection The service section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(ServiceSection $serviceSection): ?bool
    {
        return DB::transaction(function () use ($serviceSection) {
            // Delete image if exists
            if ($serviceSection->image_path) {
                $this->storageService->delete($serviceSection->image_path);
            }
            // Delete icon if exists
            if ($serviceSection->icon_path) {
                $this->storageService->delete($serviceSection->icon_path);
            }

            return $serviceSection->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted service section.
     *
     * @param ServiceSection $serviceSection The service section to restore.
     * @return ServiceSection The restored service section.
     */
    public function restore(ServiceSection $serviceSection): ServiceSection
    {
        return DB::transaction(function () use ($serviceSection) {
            $serviceSection->restore();
            return $serviceSection->load(['user']);
        });
    }

    /**
     * Reorder service sections.
     *
     * @param array $orderedIds Array of service section IDs in the desired order.
     * @return bool Whether the reordering was successful.
     */
    public function reorder(array $orderedIds): bool
    {
        return DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                ServiceSection::query()->where('id', $id)->update(['order' => $index]);
            }
            return true;
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
}
