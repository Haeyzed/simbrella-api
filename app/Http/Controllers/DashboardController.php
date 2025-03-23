<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Services\ACLService;
use App\Services\DashboardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dashboard Controller
 *
 * Handles dashboard data retrieval operations.
 * Includes methods to get summary statistics, visitor data, top content, and recent activity.
 *
 * @package App\Http\Controllers
 * @tags Dashboard
 */
class DashboardController extends Controller
{
    /**
     * @var DashboardService
     */
    protected DashboardService $dashboardService;

    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * DashboardController constructor.
     *
     * @param DashboardService $dashboardService
     * @param ACLService $ACLService
     */
    public function __construct(DashboardService $dashboardService, ACLService $ACLService)
    {
        $this->dashboardService = $dashboardService;
        $this->ACLService = $ACLService;
    }

    /**
     * Get dashboard data including summary statistics, visitor data, top blogs, and recent activity.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          summary: array,
     *          visitors: array,
     *          top_blogs: array,
     *          recent_activity: array
     *      }
     *  }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.dashboard_view.name'));

            $visitorPeriod = $request->input('visitor_period', 'daily');

            $data = [
                'summary' => $this->dashboardService->getSummaryStats(),
                'visitors' => $this->dashboardService->getVisitorStats($visitorPeriod),
                'top_blogs' => $this->dashboardService->getTopBlogPosts(),
                'recent_activity' => $this->dashboardService->getRecentActivity(),
            ];

            return response()->success(new DashboardResource($data), 'Dashboard data retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed statistics for a specific content type.
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          detailed_stats: array
     *      }
     *  }
     */
    public function getDetailedStats(Request $request, string $type): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.dashboard_stats.name'));

            $data = [
                'detailed_stats' => $this->dashboardService->getDetailedStats($type)
            ];

            return response()->success(new DashboardResource($data), 'Detailed statistics retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve detailed statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get visitor statistics with period filtering.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          visitors: array
     *      }
     *  }
     */
    public function getVisitorStats(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.dashboard_view.name'));

            $period = $request->input('period', 'daily');

            $data = [
                'visitors' => $this->dashboardService->getVisitorStats($period)
            ];

            return response()->success(new DashboardResource($data), 'Visitor statistics retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve visitor statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get top blog posts by views.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          top_blogs: array
     *      }
     *  }
     */
    public function getTopBlogPosts(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.dashboard_view.name'));

            $limit = $request->input('limit', 5);

            $data = [
                'top_blogs' => $this->dashboardService->getTopBlogPosts($limit)
            ];

            return response()->success(new DashboardResource($data), 'Top blog posts retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve top blog posts: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activity across the platform.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          recent_activity: array
     *      }
     *  }
     */
    public function getRecentActivity(Request $request): JsonResponse
    {
        try {
            $this->ACLService->checkUserPermission(config('acl.permissions.dashboard_view.name'));

            $limit = $request->input('limit', 5);

            $data = [
                'recent_activity' => $this->dashboardService->getRecentActivity($limit)
            ];

            return response()->success(new DashboardResource($data), 'Recent activity retrieved successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to retrieve recent activity: ' . $e->getMessage());
        }
    }
}
