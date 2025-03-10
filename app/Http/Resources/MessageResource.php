<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResource
 *
 * @property Message $resource
 */
class MessageResource extends JsonResource
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
             * The unique identifier of the message.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The first name of the sender.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => $this->first_name,

            /**
             * The last name of the sender.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => $this->last_name,

            /**
             * The full name of the sender.
             *
             * @var string $full_name
             * @example "John Doe"
             */
            'full_name' => $this->full_name,

            /**
             * The email address of the sender.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => $this->email,

            /**
             * The message content.
             *
             * @var string $message
             * @example "I would like to inquire about your services."
             */
            'message' => $this->message,

            /**
             * The response to the message.
             *
             * @var string|null $response
             * @example "Thank you for your inquiry. We will get back to you soon."
             */
            'response' => $this->response,

            /**
             * The status of the message.
             *
             * @var string $status
             * @example "unread"
             */
            'status' => $this->status,

            /**
             * The human-readable status label.
             *
             * @var string|null $status_label
             * @example "Unread"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color associated with the status.
             *
             * @var string|null $status_color
             * @example "red"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The ID of the user who responded to the message.
             *
             * @var int|null $responded_by_id
             * @example 1
             */
            'responded_by_id' => $this->responded_by_id,

            /**
             * The user who responded to the message.
             *
             * @var array|null $responded_by
             */
            'responded_by' => $this->whenLoaded('respondedBy', function () {
                return [
                    'id' => $this->respondedBy->id,
                    'first_name' => $this->respondedBy->first_name,
                    'last_name' => $this->respondedBy->last_name,
                    'full_name' => $this->respondedBy->full_name,
                    'email' => $this->respondedBy->email,
                ];
            }),

            /**
             * The timestamp when the message was responded to.
             *
             * @var string|null $responded_at
             * @example "2024-03-06 12:00:00"
             */
            'responded_at' => $this->responded_at,

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

            /**
             * The formatted creation date.
             *
             * @var string|null $formatted_created_at
             * @example "March 6, 2024"
             */
            'formatted_created_at' => $this->created_at ? $this->created_at->format('F j, Y') : null,

            /**
             * The formatted response date.
             *
             * @var string|null $formatted_responded_at
             * @example "March 6, 2024"
             */
            'formatted_responded_at' => $this->responded_at ? $this->responded_at->format('F j, Y') : null,
        ];
    }
}
