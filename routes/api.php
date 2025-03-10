<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactInformationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AboutSectionController;
use App\Http\Controllers\CaseStudySectionController;
use App\Http\Controllers\ClientSectionController;
use App\Http\Controllers\HeroSectionController;
use App\Http\Controllers\ProductSectionController;
use App\Http\Controllers\ServiceSectionController;
use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    // Password Reset
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPasswordWithOTP']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    // Email Verification
    Route::post('email/verify/{email}', [AuthController::class, 'verifyEmail']);
    Route::post('email/resend', [AuthController::class, 'resendVerification']);

    // Profile
    Route::post('profile', [AuthController::class, 'updateProfile']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('blog-posts', BlogPostController::class);

    Route::prefix('blog-posts')->name('blog-posts.')->group(function () {
        // Soft delete management
        Route::delete('/{blogPost}/force', [BlogPostController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{blogPost}/restore', [BlogPostController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    //User Route
    Route::apiResource('users', UserController::class);

    Route::prefix('users')->name('users.')->group(function () {
        Route::post('{user}/restore', [UserController::class, 'restore'])
            ->name('restore')
            ->withTrashed();

        Route::delete('{user}/force', [UserController::class, 'forceDelete'])
            ->name('force-delete')
            ->withTrashed();

        Route::post('{user}/roles', [UserController::class, 'assignRole'])
            ->name('assign-role');

        Route::delete('{user}/roles', [UserController::class, 'removeRole'])
            ->name('remove-role');

        Route::post('{user}/permissions', [UserController::class, 'givePermission'])
            ->name('give-permission');

        Route::delete('{user}/permissions', [UserController::class, 'revokePermission'])
            ->name('revoke-permission');

        Route::post('{user}/password', [UserController::class, 'changePassword'])
            ->name('change-password');

        Route::post('{user}/status', [UserController::class, 'changeStatus'])
            ->name('change-status');
    });

    //Role Routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/all', [RoleController::class, 'getAllRoles'])->name('all');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::post('/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('assign-permissions');
        Route::delete('/{role}/permissions', [RoleController::class, 'removePermissions'])->name('remove-permissions');
    });

    // Permission Routes
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/all', [PermissionController::class, 'getAllPermissions'])->name('all');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });

    // Career Routes
    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('careers')->name('careers.')->group(function () {
            Route::get('/', [CareerController::class, 'index'])->name('index');
            Route::post('/', [CareerController::class, 'store'])->name('store');
            Route::get('/{career}', [CareerController::class, 'show'])->name('show');
            Route::put('/{career}', [CareerController::class, 'update'])->name('update');
            Route::delete('/{career}', [CareerController::class, 'destroy'])->name('destroy');

            // Soft delete management
            Route::delete('/{career}/force', [CareerController::class, 'forceDestroy'])
                ->name('force-destroy')
                ->withTrashed();
            Route::post('/{career}/restore', [CareerController::class, 'restore'])
                ->name('restore')
                ->withTrashed();
        });
    });

    // Hero Section Routes
    Route::prefix('hero-sections')->name('hero-sections.')->group(function () {
        Route::get('/', [HeroSectionController::class, 'index'])->name('index');
        Route::post('/', [HeroSectionController::class, 'store'])->name('store');
        Route::get('/{heroSection}', [HeroSectionController::class, 'show'])->name('show');
        Route::put('/{heroSection}', [HeroSectionController::class, 'update'])->name('update');
        Route::delete('/{heroSection}', [HeroSectionController::class, 'destroy'])->name('destroy');

        // Soft delete management
        Route::delete('/{heroSection}/force', [HeroSectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{heroSection}/restore', [HeroSectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    // Service Section Routes
    Route::prefix('service-sections')->name('service-sections.')->group(function () {
        Route::get('/', [ServiceSectionController::class, 'index'])->name('index');
        Route::post('/', [ServiceSectionController::class, 'store'])->name('store');
        Route::get('/{serviceSection}', [ServiceSectionController::class, 'show'])->name('show');
        Route::put('/{serviceSection}', [ServiceSectionController::class, 'update'])->name('update');
        Route::delete('/{serviceSection}', [ServiceSectionController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [ServiceSectionController::class, 'reorder'])->name('reorder');

        // Soft delete management
        Route::delete('/{serviceSection}/force', [ServiceSectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{serviceSection}/restore', [ServiceSectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    // About Section Routes
    Route::prefix('about-sections')->name('about-sections.')->group(function () {
        Route::get('/', [AboutSectionController::class, 'index'])->name('index');
        Route::post('/', [AboutSectionController::class, 'store'])->name('store');
        Route::get('/{aboutSection}', [AboutSectionController::class, 'show'])->name('show');
        Route::put('/{aboutSection}', [AboutSectionController::class, 'update'])->name('update');
        Route::delete('/{aboutSection}', [AboutSectionController::class, 'destroy'])->name('destroy');

        // Soft delete management
        Route::delete('/{aboutSection}/force', [AboutSectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{aboutSection}/restore', [AboutSectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    // Product Section Routes
    Route::prefix('product-sections')->name('product-sections.')->group(function () {
        Route::get('/', [ProductSectionController::class, 'index'])->name('index');
        Route::post('/', [ProductSectionController::class, 'store'])->name('store');
        Route::get('/{productSection}', [ProductSectionController::class, 'show'])->name('show');
        Route::put('/{productSection}', [ProductSectionController::class, 'update'])->name('update');
        Route::delete('/{productSection}', [ProductSectionController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [ProductSectionController::class, 'reorder'])->name('reorder');

        // Soft delete management
        Route::delete('/{productSection}/force', [ProductSectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{productSection}/restore', [ProductSectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    // Client Section Routes
    Route::prefix('client-sections')->name('client-sections.')->group(function () {
        Route::get('/', [ClientSectionController::class, 'index'])->name('index');
        Route::post('/', [ClientSectionController::class, 'store'])->name('store');
        Route::get('/{clientSection}', [ClientSectionController::class, 'show'])->name('show');
        Route::put('/{clientSection}', [ClientSectionController::class, 'update'])->name('update');
        Route::delete('/{clientSection}', [ClientSectionController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [ClientSectionController::class, 'reorder'])->name('reorder');

        // Soft delete management
        Route::delete('/{clientSection}/force', [ClientSectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{clientSection}/restore', [ClientSectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });

    // Case Study Section Routes
    Route::prefix('case-study-sections')->name('case-study-sections.')->group(function () {
        Route::get('/', [CaseStudySectionController::class, 'index'])->name('index');
        Route::post('/', [CaseStudySectionController::class, 'store'])->name('store');
        Route::get('/{caseStudySection}', [CaseStudySectionController::class, 'show'])->name('show');
        Route::put('/{caseStudySection}', [CaseStudySectionController::class, 'update'])->name('update');
        Route::delete('/{caseStudySection}', [CaseStudySectionController::class, 'destroy'])->name('destroy');

        // Soft delete management
        Route::delete('/{caseStudySection}/force', [CaseStudySectionController::class, 'forceDestroy'])
            ->name('force-destroy')
            ->withTrashed();
        Route::post('/{caseStudySection}/restore', [CaseStudySectionController::class, 'restore'])
            ->name('restore')
            ->withTrashed();
    });
});

// Public routes
Route::get('contact-information', [ContactInformationController::class, 'show']);
Route::post('messages', [MessageController::class, 'store']);

// Protected routes
Route::middleware(['auth:api'])->group(function () {
    // Contact Information
    Route::put('contact-information', [ContactInformationController::class, 'update']);

    // Messages
    Route::get('messages', [MessageController::class, 'index']);
    Route::get('messages/{message}', [MessageController::class, 'show']);
    Route::post('messages/{message}/respond', [MessageController::class, 'respond']);
    Route::patch('messages/{message}/mark-as-read', [MessageController::class, 'markAsRead']);
    Route::patch('messages/{message}/archive', [MessageController::class, 'archive']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);
    Route::delete('messages/{message}/force', [MessageController::class, 'forceDestroy']);
    Route::patch('messages/{message}/restore', [MessageController::class, 'restore']);
});

Route::get('/documentation-api', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents(base_path('api.json')),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
})->middleware(Scramble::getGeneratorConfig('default')->get('middleware', [RestrictedDocsAccess::class]));
