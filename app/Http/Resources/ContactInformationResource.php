<?php

namespace App\Http\Resources;

use App\Models\ContactInformation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ContactInformationResource
 *
 * @property ContactInformation $resource
 */
class ContactInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the contact information.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The address of the company.
             *
             * @var string $address
             * @example "123 Business Street, City, Country"
             */
            'address' => $this->address,

            /**
             * The phone number of the company.
             *
             * @var string $phone
             * @example "+1234567890"
             */
            'phone' => $this->phone,

            /**
             * The email address of the company.
             *
             * @var string $email
             * @example "contact@company.com"
             */
            'email' => $this->email,

            /**
             * The Facebook link of the company.
             *
             * @var string|null $facebook_link
             * @example "https://facebook.com/company"
             */
            'facebook_link' => $this->facebook_link,

            /**
             * The Instagram link of the company.
             *
             * @var string|null $instagram_link
             * @example "https://instagram.com/company"
             */
            'instagram_link' => $this->instagram_link,

            /**
             * The LinkedIn link of the company.
             *
             * @var string|null $linkedin_link
             * @example "https://linkedin.com/company"
             */
            'linkedin_link' => $this->linkedin_link,

            /**
             * The Twitter link of the company.
             *
             * @var string|null $twitter_link
             * @example "https://twitter.com/company"
             */
            'twitter_link' => $this->twitter_link,

            /**
             * The ID of the user who manages this contact information.
             *
             * @var int $user_id
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * The user who manages this contact information.
             *
             * @var array|null $user
             */
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),

            /**
             * The creation timestamp.
             *
             * @var string|null $created_at
             * @example "2024-03-06 10:00:00"
             */
            'created_at' => $this->created_at,

            /**
             * The last update timestamp.
             *
             * @var string|null $updated_at
             * @example "2024-03-06 11:00:00"
             */
            'updated_at' => $this->updated_at,

            /**
             * The deletion timestamp (if soft deleted).
             *
             * @var string|null $deleted_at
             * @example null
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}
