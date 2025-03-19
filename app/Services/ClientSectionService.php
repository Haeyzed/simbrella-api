<?php

namespace App\Services;

use App\Models\ClientSection;
use App\Services\Storage\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientSectionService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * ClientSectionService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List client sections based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of client sections.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return ClientSection::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['company_name'], $request->search);
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
            ->when($request->boolean('with_case_study'), function ($query) {
                $query->has('caseStudy');
            })
            ->when($request->boolean('without_case_study'), function ($query) {
                $query->doesntHave('caseStudy');
            })
            ->with(['user', 'caseStudy'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new client section.
     *
     * @param array $data The validated data for creating a new client section.
     * @return ClientSection The newly created client section.
     */
    public function create(array $data): ClientSection
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Handle logo
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                $data['logo_path'] = $this->uploadImage(
                    $data['logo'],
                    config('filestorage.paths.client_logos')
                );
                unset($data['logo']);
            }

            // Create client section
            return ClientSection::create($data)->load(['user', 'caseStudy']);
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
     * Permanently delete a client section and its related files.
     *
     * @param ClientSection $clientSection The client section to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(ClientSection $clientSection): ?bool
    {
        return DB::transaction(function () use ($clientSection) {
            // Delete logo
            if ($clientSection->logo_path) {
                $this->storageService->delete($clientSection->logo_path);
            }

            // Delete case study if exists
            if ($clientSection->caseStudy) {
                app(CaseStudySectionService::class)->forceDelete($clientSection->caseStudy);
            }

            return $clientSection->forceDelete();
        });
    }

    /**
     * Delete a client section.
     *
     * @param ClientSection $clientSection The client section to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(ClientSection $clientSection): ?bool
    {
        return DB::transaction(function () use ($clientSection) {
            return $clientSection->delete();
        });
    }

    /**
     * Restore a soft-deleted client section.
     *
     * @param ClientSection $clientSection The client section to restore.
     * @return ClientSection The restored client section.
     */
    public function restore(ClientSection $clientSection): ClientSection
    {
        return DB::transaction(function () use ($clientSection) {
            $clientSection->restore();
            return $clientSection->load(['user', 'caseStudy']);
        });
    }

    /**
     * Reorder client sections.
     *
     * @param array $orderedIds Array of client section IDs in the desired order.
     * @return bool Whether the reordering was successful.
     */
    public function reorder(array $orderedIds): bool
    {
        return DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                ClientSection::where('id', $id)->update(['order' => $index]);
            }
            return true;
        });
    }

    /**
     * Update an existing client section.
     *
     * @param ClientSection $clientSection The client section to update.
     * @param array $data The validated data for updating the client section.
     * @return ClientSection The updated client section.
     */
    public function update(ClientSection $clientSection, array $data): ClientSection
    {
        return DB::transaction(function () use ($clientSection, $data) {
            // Handle logo
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                // Delete old logo if exists
                if ($clientSection->logo_path) {
                    $this->storageService->delete($clientSection->logo_path);
                }

                $data['logo_path'] = $this->uploadImage(
                    $data['logo'],
                    config('filestorage.paths.client_logos')
                );
                unset($data['logo']);
            }

            // Update client section
            $clientSection->update($data);

            return $clientSection->load(['user', 'caseStudy']);
        });
    }
}
