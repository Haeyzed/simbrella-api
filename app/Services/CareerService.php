<?php

namespace App\Services;

use App\Enums\CareerStatusEnum;
use App\Models\Career;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CareerService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * CareerService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List career postings based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of career postings.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return Career::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['title', 'subtitle', 'description', 'location', 'department'], $request->search);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('format'), function ($query) use ($request) {
                $query->where('format', $request->format);
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                $query->where('department', $request->department);
            })
            ->when($request->filled('employment_type'), function ($query) use ($request) {
                $query->where('employment_type', $request->employment_type);
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
            ->when($request->boolean('active_only'), function ($query) {
                $query->active();
            })
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->with(['user'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new career posting.
     *
     * @param array $data The validated data for creating a new career posting.
     * @return Career The newly created career posting.
     */
    public function create(array $data): Career
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Handle banner image
            if (isset($data['banner_image']) && $data['banner_image'] instanceof UploadedFile) {
                $data['banner_image'] = $this->uploadImage(
                    $data['banner_image'],
                    config('filestorage.paths.career_banners')
                );
            }

            // Set published_at if status is published
            if (isset($data['status']) && $data['status'] === CareerStatusEnum::PUBLISHED->value) {
                $data['published_at'] = $data['published_at'] ?? now();
            }

            // Create career posting
            return Career::create($data)->load(['user']);
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
     * Update an existing career posting.
     *
     * @param Career $career The career posting to update.
     * @param array $data The validated data for updating the career posting.
     * @return Career The updated career posting.
     */
    public function update(Career $career, array $data): Career
    {
        return DB::transaction(function () use ($career, $data) {
            // Handle banner image
            if (isset($data['banner_image']) && $data['banner_image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($career->banner_image) {
                    $this->storageService->delete($career->banner_image);
                }

                $data['banner_image'] = $this->uploadImage(
                    $data['banner_image'],
                    config('filestorage.paths.career_banners')
                );
            }

            // Handle published_at date
            if (isset($data['status'])) {
                if ($data['status'] === CareerStatusEnum::PUBLISHED->value && !$career->published_at) {
                    $data['published_at'] = $data['published_at'] ?? now();
                } elseif ($data['status'] !== CareerStatusEnum::PUBLISHED->value) {
                    $data['published_at'] = null;
                }
            }

            // Update career posting
            $career->update($data);

            return $career->load(['user']);
        });
    }

    /**
     * Delete a career posting.
     *
     * @param Career $career The career posting to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(Career $career): ?bool
    {
        return DB::transaction(function () use ($career) {
            return $career->delete();
        });
    }

    /**
     * Permanently delete a career posting and its related files.
     *
     * @param Career $career The career posting to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(Career $career): ?bool
    {
        return DB::transaction(function () use ($career) {
            // Delete banner image
            if ($career->banner_image) {
                $this->storageService->delete($career->banner_image);
            }

            return $career->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted career posting.
     *
     * @param Career $career The career posting to restore.
     * @return Career The restored career posting.
     */
    public function restore(Career $career): Career
    {
        return DB::transaction(function () use ($career) {
            $career->restore();
            return $career->load(['user']);
        });
    }
}
