<?php

namespace App\Services;

use App\Models\ContactInformation;
use Illuminate\Support\Facades\DB;

/**
 * Class ContactInformationService
 *
 * Handles business logic for contact information management.
 *
 * @package App\Services
 */
class ContactInformationService
{
    /**
     * Update or create contact information.
     *
     * @param array $data The validated data for updating or creating contact information.
     * @return ContactInformation The updated or created contact information.
     */
    public function updateOrCreate(array $data): ContactInformation
    {
        return DB::transaction(function () use ($data) {
            // Set user_id if not provided
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }

            // Get the latest contact information or create a new one
            $contactInformation = ContactInformation::query()->latest()->first();

            if ($contactInformation) {
                $contactInformation->update($data);
            } else {
                $contactInformation = ContactInformation::query()->create($data);
            }

            return $contactInformation;
        });
    }

    /**
     * Get the latest contact information.
     *
     * @return ContactInformation|null The latest contact information or null if none exists.
     */
    public function getLatest(): ?ContactInformation
    {
        return ContactInformation::query()->latest()->first();
    }

    /**
     * Delete contact information.
     *
     * @param ContactInformation $contactInformation The contact information to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(ContactInformation $contactInformation): ?bool
    {
        return DB::transaction(function () use ($contactInformation) {
            return $contactInformation->delete();
        });
    }

    /**
     * Force delete contact information.
     *
     * @param ContactInformation $contactInformation The contact information to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(ContactInformation $contactInformation): ?bool
    {
        return DB::transaction(function () use ($contactInformation) {
            return $contactInformation->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted contact information.
     *
     * @param ContactInformation $contactInformation The contact information to restore.
     * @return ContactInformation The restored contact information.
     */
    public function restore(ContactInformation $contactInformation): ContactInformation
    {
        return DB::transaction(function () use ($contactInformation) {
            $contactInformation->restore();
            return $contactInformation;
        });
    }
}
