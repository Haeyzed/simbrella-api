<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListMessageRequest;
use App\Http\Requests\MessageRequest;
use App\Http\Requests\RespondToMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Services\ACLService;
use App\Services\MessageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * Message Controller
 *
 * Handles CRUD operations for Messages.
 * Includes methods to list, create, view, respond to, archive, delete, and restore messages.
 *
 * @package App\Http\Controllers
 * @tags Message
 */
class MessageController extends Controller
{
    /**
     * The message service instance.
     *
     * @var MessageService
     */
    protected MessageService $messageService;

    /**
     * The ACL service instance.
     *
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * MessageController constructor.
     *
     * @param MessageService $messageService The message service instance.
     * @param ACLService $ACLService The ACL service instance.
     * @return void
     */
    public function __construct(MessageService $messageService, ACLService $ACLService)
    {
        $this->messageService = $messageService;
        $this->ACLService = $ACLService;
    }

    /**
     * Display a listing of messages.
     *
     * This endpoint retrieves a paginated list of messages based on search and filter parameters.
     * It requires authentication and the 'message_view' permission.
     *
     * @param ListMessageRequest $request The validated request containing filter and pagination parameters.
     * @return JsonResponse The JSON response containing the paginated list of messages.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource[],
     *     meta: array{
     *         current_page: int,
     *         last_page: int,
     *         per_page: int,
     *         total: int
     *     }
     * }
     */
    public function index(ListMessageRequest $request): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.message_view.name'));
        $messages = $this->messageService->list($request);
        return response()->paginatedSuccess(MessageResource::collection($messages), 'Messages retrieved successfully');
    }

    /**
     * Store a newly created message.
     *
     * This endpoint creates a new message from the contact form.
     * It is publicly accessible and does not require authentication.
     * It sends a confirmation email to the sender and a notification to the organization.
     *
     * @param MessageRequest $request The validated request containing message data.
     * @return JsonResponse The JSON response containing the created message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     * @throws Exception If the message creation fails.
     */
    public function store(MessageRequest $request): JsonResponse
    {
        try {
            $message = $this->messageService->create($request->validated());
            return response()->success(new MessageResource($message), 'Message sent successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified message.
     *
     * This endpoint retrieves a specific message by its ID.
     * It requires authentication and the 'message_view' permission.
     *
     * @param Message $message The message to retrieve.
     * @return JsonResponse The JSON response containing the message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     */
    public function show(Message $message): JsonResponse
    {
        $this->ACLService->checkUserPermission(config('acl.permissions.message_view.name'));
        return response()->success(new MessageResource($message), 'Message retrieved successfully');
    }

    /**
     * Respond to a message.
     *
     * This endpoint adds a response to a message and optionally sends an email to the sender.
     * It requires authentication and the 'message_respond' permission.
     *
     * @param RespondToMessageRequest $request The validated request containing the response data.
     * @param Message $message The message to respond to.
     * @return JsonResponse The JSON response containing the updated message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     * @throws Exception If the response operation fails.
     */
    public function respond(RespondToMessageRequest $request, Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_respond.name'));
            $updatedMessage = $this->messageService->respond($message, $request->validated());
            return response()->success(new MessageResource($updatedMessage), 'Response sent successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to send response: ' . $e->getMessage());
        }
    }

    /**
     * Mark a message as read.
     *
     * This endpoint changes the status of a message from 'unread' to 'read'.
     * It requires authentication and the 'message_update' permission.
     *
     * @param Message $message The message to mark as read.
     * @return JsonResponse The JSON response containing the updated message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     * @throws Exception If the operation fails.
     */
    public function markAsRead(Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_update.name'));
            $updatedMessage = $this->messageService->markAsRead($message);
            return response()->success(new MessageResource($updatedMessage), 'Message marked as read');
        } catch (Exception $e) {
            return response()->serverError('Failed to mark message as read: ' . $e->getMessage());
        }
    }

    /**
     * Archive a message.
     *
     * This endpoint changes the status of a message to 'archived'.
     * It requires authentication and the 'message_archive' permission.
     *
     * @param Message $message The message to archive.
     * @return JsonResponse The JSON response containing the updated message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     * @throws Exception If the archive operation fails.
     */
    public function archive(Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_archive.name'));
            $updatedMessage = $this->messageService->archive($message);
            return response()->success(new MessageResource($updatedMessage), 'Message archived successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to archive message: ' . $e->getMessage());
        }
    }

    /**
     * Delete a message (soft delete).
     *
     * This endpoint soft deletes a message, making it retrievable later.
     * It requires authentication and the 'message_delete' permission.
     *
     * @param Message $message The message to delete.
     * @return JsonResponse The JSON response indicating success or failure.
     * @response array{
     *     status: boolean,
     *     message: string
     * }
     * @throws Exception If the delete operation fails.
     */
    public function destroy(Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_delete.name'));
            $this->messageService->delete($message);
            return response()->success(null, 'Message deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete message: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a message (permanent delete).
     *
     * This endpoint permanently deletes a message, making it unrecoverable.
     * It requires authentication and the 'message_delete' permission.
     *
     * @param Message $message The message to force delete.
     * @return JsonResponse The JSON response indicating success or failure.
     * @response array{
     *     status: boolean,
     *     message: string
     * }
     * @throws Exception If the force delete operation fails.
     */
    public function forceDestroy(Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_delete.name'));
            $this->messageService->forceDelete($message);
            return response()->success(null, 'Message permanently deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to permanently delete message: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted message.
     *
     * This endpoint restores a previously soft-deleted message.
     * It requires authentication and the 'message_restore' permission.
     *
     * @param Message $message The message to restore.
     * @return JsonResponse The JSON response containing the restored message.
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: MessageResource
     * }
     * @throws ModelNotFoundException If the message is not found.
     * @throws Exception If the restore operation fails.
     */
    public function restore(Message $message): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.message_restore.name'));
            $restoredMessage = $this->messageService->restore($message);
            return response()->success(new MessageResource($restoredMessage), 'Message restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Message not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore message: ' . $e->getMessage());
        }
    }
}
