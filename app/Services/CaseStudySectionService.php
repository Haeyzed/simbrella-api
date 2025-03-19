<?php

namespace App\Services;

use App\Models\CaseStudySection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CaseStudySectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * CaseStudySectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List case study sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of case study sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return CaseStudySection::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['company_name', 'subtitle', 'description'], $request->search);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('client_id'), function ($query) use ($request) {
                $query->where('client_section_id', $request->client_id);
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
            ->with(['user', 'client'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new case study section.
     *
     * @param array $data The validated data for creating a new case study section.
     * @return CaseStudySection The newly created case study section.
     */
    public function create(array $data): CaseStudySection
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
                    config('filestorage.paths.case_study_banners')
                );
            }

            // Create case study section
            return CaseStudySection::create($data)->load(['user', 'client']);
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
     * Update an existing case study section.
     *
     * @param CaseStudySection $caseStudySection The case study section to update.
     * @param array $data The validated data for updating the case study section.
     * @return CaseStudySection The updated case study section.
     */
    public function update(CaseStudySection $caseStudySection, array $data): CaseStudySection
    {
        return DB::transaction(function () use ($caseStudySection, $data) {
            // Handle banner image
            if (isset($data['banner_image']) && $data['banner_image'] instanceof UploadedFile) {
                // Delete old banner image if exists
                if ($caseStudySection->banner_image) {
                    $this->storageService->delete($caseStudySection->banner_image);
                }

                $data['banner_image'] = $this->uploadImage(
                    $data['banner_image'],
                    config('filestorage.paths.case_study_banners')
                );
            }

            // Update case study section
            $caseStudySection->update($data);

            return $caseStudySection->load(['user', 'client']);
        });
    }

    /**
     * Delete a case study section.
     *
     * @param CaseStudySection $caseStudySection The case study section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(CaseStudySection $caseStudySection): ?bool
    {
        return DB::transaction(function () use ($caseStudySection) {
            return $caseStudySection->delete();
        });
    }

    /**
     * Permanently delete a case study section and its related files.
     *
     * @param CaseStudySection $caseStudySection The case study section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(CaseStudySection $caseStudySection): ?bool
    {
        return DB::transaction(function () use ($caseStudySection) {
            // Delete banner image
            if ($caseStudySection->banner_image) {
                $this->storageService->delete($caseStudySection->banner_image);
            }

            return $caseStudySection->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted case study section.
     *
     * @param CaseStudySection $caseStudySection The case study section to restore.
     * @return CaseStudySection The restored case study section.
     */
    public function restore(CaseStudySection $caseStudySection): CaseStudySection
    {
        return DB::transaction(function () use ($caseStudySection) {
            $caseStudySection->restore();
            return $caseStudySection->load(['user', 'client']);
        });
    }
}
