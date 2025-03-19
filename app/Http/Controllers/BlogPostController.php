<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogPostRequest;
use App\Http\Requests\ListBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Services\ACLService;
use App\Services\BlogPostService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Blog Post Controller
 *
 * Handles CRUD operations for Blog Posts.
 * Includes methods to list, create, update, delete, restore for Blog records.
 *
 * @package App\Http\Controllers
 * @tags Blog Post
 */
class BlogPostController extends Controller
{
    /**
     * @var BlogPostService
     */
    protected BlogPostService $blogPostService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var bool
     */
    protected bool $isPublicRoute = false;

    /**
     * BlogPostController constructor.
     *
     * @param BlogPostService $blogPostService
     * @param ACLService $ACLService
     * @param Request $request
     */
    public function __construct(BlogPostService $blogPostService, ACLService $ACLService, Request $request)
    {
        $this->blogPostService = $blogPostService;
        $this->ACLService = $ACLService;

        // Check if this is a public route
        $this->isPublicRoute = str_contains($request->route()->getPrefix(), 'public');
    }

    /**
     * Fetch a paginated list of blog posts based on search and filter parameters.
     *
     * @param ListBlogPostRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: BlogPostResource[],
     *      meta: array{
     *          current_page: int,
     *          last_page: int,
     *          per_page: int,
     *          total: int
     *      }
     *  }
     */
    public function index(ListBlogPostRequest $request): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_view.name'));
        }

        $blogPosts = $this->blogPostService->list($request);
        return response()->paginatedSuccess(BlogPostResource::collection($blogPosts), 'Blog posts retrieved successfully');
    }

    /**
     * Create and store a new blog post.
     *
     * @requestMediaType multipart/form-data
     * @param BlogPostRequest $request
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: BlogPostResource
     *   }
     */
    public function store(BlogPostRequest $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_create.name'));
            $blogPost = $this->blogPostService->create($request->validated());
            return response()->success(new BlogPostResource($blogPost), 'Blog post created successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to create blog post: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve and display a specific blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: BlogPostResource
     *   }
     */
    public function show(BlogPost $blogPost): JsonResponse
    {
        // Skip permission check for public routes
        if (!$this->isPublicRoute) {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_view.name'));
        }

        return response()->success(new BlogPostResource($blogPost->load(['user', 'images'])), 'Blog post retrieved successfully');
    }

    /**
     * Update an existing blog post.
     *
     * @requestMediaType multipart/form-data
     * @param BlogPostRequest $request
     * @param BlogPost $blogPost
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: BlogPostResource
     *   }
     */
    public function update(BlogPostRequest $request, BlogPost $blogPost): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_update.name'));
            $updatedBlogPost = $this->blogPostService->update($blogPost, $request->validated());
            return response()->success(new BlogPostResource($updatedBlogPost), 'Blog post updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update blog post: ' . $e->getMessage());
        }
    }

    /**
     * Delete a blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function destroy(BlogPost $blogPost): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_delete.name'));
            $this->blogPostService->delete($blogPost);
            return response()->success(null, 'Blog post deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to delete blog post: ' . $e->getMessage());
        }
    }

    /**
     * Force delete a blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string
     *   }
     */
    public function forceDestroy(BlogPost $blogPost): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_delete.name'));
            $this->blogPostService->forceDelete($blogPost);
            return response()->success(null, 'Blog post force deleted successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to force delete blog post: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted blog post.
     *
     * @param BlogPost $blogPost
     * @return JsonResponse
     * @response array{
     *       status: boolean,
     *       message: string,
     *       data: BlogPostResource
     *   }
     */
    public function restore(BlogPost $blogPost): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.blog_restore.name'));
            $restoredBlogPost = $this->blogPostService->restore($blogPost);
            return response()->success(new BlogPostResource($restoredBlogPost), 'Blog post restored successfully');
        } catch (ModelNotFoundException $e) {
            return response()->notFound('Blog post not found');
        } catch (Exception $e) {
            return response()->serverError('Failed to restore blog post: ' . $e->getMessage());
        }
    }
}
