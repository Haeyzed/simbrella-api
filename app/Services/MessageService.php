<?php

namespace App\Services;

use App\Enums\MessageStatusEnum;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class MessageService
 *
 * Handles business logic for message management.
 *
 * @package App\Services
 */
class MessageService
{
    /**
     * @var EmailService
     */
    protected EmailService $emailService;

    /**
     * MessageService constructor.
     *
     * @param EmailService $emailService
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * List messages based on given criteria.
     *
     * @param object $request The request object containing filter and pagination parameters.
     * @return LengthAwarePaginator The paginated list of messages.
     */
    public function list(object $request): LengthAwarePaginator
    {
        return Message::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search(['first_name', 'last_name', 'email', 'message', 'response'], $request->search);
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
            ->filterByDateRange(
                $request->input('start_date'),
                $request->input('end_date')
            )
            ->with(['respondedBy'])
            ->paginate($request->integer('per_page', config('app.pagination.per_page', 15)));
    }

    /**
     * Create a new message.
     *
     * @param array $data The validated data for creating a new message.
     * @return Message The newly created message.
     */
    public function create(array $data): Message
    {
        return DB::transaction(function () use ($data) {
            // Create message
            $message = Message::query()->create($data);

            try {
                // Send confirmation email to the sender
                $this->emailService->sendMessageConfirmation($message->email, $message->first_name);

                // Send notification email to the organization
                $this->notifyOrganization($message);
            } catch (\Exception $e) {
                // Log the error but don't fail the transaction
                report($e);
            }

            return $message;
        });
    }

    /**
     * Notify the organization about a new message.
     *
     * @param Message $message The new message.
     * @return void Whether the notification was sent successfully.
     */
    private function notifyOrganization(Message $message): void
    {
        $organizationEmail = config('mail.organization_email');

        if (!$organizationEmail) {
            return;
        }

        $this->emailService->sendOrganizationNotification($organizationEmail, $message);
    }

    /**
     * Respond to a message.
     *
     * @param Message $message The message to respond to.
     * @param array $data The validated data for responding to the message.
     * @return Message The updated message.
     */
    public function respond(Message $message, array $data): Message
    {
        return DB::transaction(function () use ($message, $data) {
            // Update message with response
            $message->update([
                'response' => $data['response'],
                'status' => MessageStatusEnum::RESPONDED,
                'responded_by_id' => auth()->id(),
                'responded_at' => Carbon::now(),
            ]);

            try {
                // Send response email to the sender
                if (isset($data['send_email']) && $data['send_email']) {
                    $this->emailService->sendMessageResponse($message->email, $data['response']);
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the transaction
                report($e);
            }

            return $message->refresh();
        });
    }

    /**
     * Mark a message as read.
     *
     * @param Message $message The message to mark as read.
     * @return Message The updated message.
     */
    public function markAsRead(Message $message): Message
    {
        return DB::transaction(function () use ($message) {
            if ($message->status === MessageStatusEnum::UNREAD) {
                $message->update([
                    'status' => MessageStatusEnum::READ,
                ]);
            }
            return $message->refresh();
        });
    }

    /**
     * Archive a message.
     *
     * @param Message $message The message to archive.
     * @return Message The updated message.
     */
    public function archive(Message $message): Message
    {
        return DB::transaction(function () use ($message) {
            $message->update([
                'status' => MessageStatusEnum::ARCHIVED,
            ]);
            return $message->refresh();
        });
    }

    /**
     * Delete a message.
     *
     * @param Message $message The message to delete.
     * @return bool|null The result of the delete operation.
     */
    public function delete(Message $message): ?bool
    {
        return DB::transaction(function () use ($message) {
            return $message->delete();
        });
    }

    /**
     * Force delete a message.
     *
     * @param Message $message The message to force delete.
     * @return bool|null The result of the force delete operation.
     */
    public function forceDelete(Message $message): ?bool
    {
        return DB::transaction(function () use ($message) {
            return $message->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted message.
     *
     * @param Message $message The message to restore.
     * @return Message The restored message.
     */
    public function restore(Message $message): Message
    {
        return DB::transaction(function () use ($message) {
            $message->restore();
            return $message->refresh();
        });
    }
}
