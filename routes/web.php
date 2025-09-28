<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeownerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Email Verification Routes
Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Email verification routes
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
    // View user details
    Route::get('/users/{user}/view', [\App\Http\Controllers\Admin\AdminController::class, 'viewUser'])->name('users.view');
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::resource('users', UserController::class);
    
    // Homeowners Management
    Route::resource('homeowners', HomeownerController::class)->names([
        'index' => 'homeowners.index',
        'create' => 'homeowners.create',
        'store' => 'homeowners.store',
        'show' => 'homeowners.show',
        'edit' => 'homeowners.edit',
        'update' => 'homeowners.update',
        'destroy' => 'homeowners.destroy',
    ]);
    
    // Service Providers Management
    Route::resource('serviceproviders', \App\Http\Controllers\Admin\ServiceProviderController::class)->names([
        'index' => 'serviceproviders.index',
        'edit' => 'serviceproviders.edit',
        'update' => 'serviceproviders.update',
        'destroy' => 'serviceproviders.destroy',
    ])->only(['index', 'edit', 'update', 'destroy']);
    
    // Update user status
    Route::put('/users/{user}/status/{status}', [AdminController::class, 'updateStatus'])
        ->name('users.updateStatus');
        
    // Handle both GET and POST for verification
    Route::match(['get', 'post'], '/users/{user}/verify', [AdminController::class, 'updateStatus'])
        ->name('users.verify')
        ->defaults('status', 'verified');
        
    Route::match(['get', 'post'], '/users/{user}/reject', [AdminController::class, 'updateStatus'])
        ->name('users.reject')
        ->defaults('status', 'rejected');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === \App\Models\User::ROLE_ADMIN) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Debug route - can be removed later
Route::get('/debug/routes', function() {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $routeList = [];
    foreach ($routes as $route) {
        $routeList[] = [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
        ];
    }
    return response()->json($routeList);
});

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    // Messages
    Route::prefix('messages')->group(function () {
        Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
        Route::get('/conversation/{user}', [\App\Http\Controllers\MessageController::class, 'getConversation'])->name('messages.conversation');
        Route::get('/unread-count', [\App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
        Route::post('/{user}/mark-as-read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
        Route::post('/send', [\App\Http\Controllers\MessageController::class, 'sendMessage'])->name('messages.send');
        Route::put('/{message}', [\App\Http\Controllers\MessageController::class, 'update'])->name('messages.update');
        Route::get('/ajax/unread-count', [\App\Http\Controllers\MessageController::class, 'getUnreadCount'])
            ->name('messages.ajax.unread-count');
        Route::delete('/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])
            ->name('messages.destroy');
    });

    // Profile routes
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [ProfileController::class, 'showSettings'])->name('profile.settings');
    Route::put('/settings/username', [ProfileController::class, 'updateUsername'])->name('profile.update-username');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    // Regular user profile edit/update
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Admin profile edit/update for any user
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit.user');
    Route::patch('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update.user');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // NewsFeed - Accessible by all authenticated users
    Route::get('/newsfeed', [PostController::class, 'index'])
        ->name('page.newsfeed')
        ->middleware('auth');

    // Service Provider Dashboard
    Route::get('/service-provider/dashboard', [ServiceProviderController::class, 'dashboard'])
        ->name('service-provider.dashboard');

    // Service Providers Routes
    Route::get('/service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');
    Route::get('/service-providers/{serviceProvider}', [ServiceProviderController::class, 'show'])->name('service-providers.show');

    // Service Provider Profile View
    Route::get('/service-provider/{user}/profile', [ProfileController::class, 'showServiceProvider'])
        ->name('service-provider.profile')
        ->middleware('auth');

    Route::middleware(['auth'])->group(function () {
        Route::get('/jobs', [JobsController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/create', [JobsController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [JobsController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{job}', [JobsController::class, 'show'])->name('jobs.show');
        Route::get('/jobs/{job}/edit', [JobsController::class, 'edit'])->name('jobs.edit');
        Route::put('/jobs/{job}', [JobsController::class, 'update'])->name('jobs.update');
        Route::delete('/jobs/{job}', [JobsController::class, 'destroy'])->name('jobs.destroy');
        
        // Job Status Update Routes
        Route::patch('/jobs/{job}/complete', [JobsController::class, 'complete'])->name('jobs.complete');
        Route::patch('/jobs/{job}/cancel', [JobsController::class, 'cancel'])->name('jobs.cancel');
        
        // Review Routes
        Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    });

    // API Routes for Jobs
    Route::get('/api/service-providers/{service}', [JobsController::class, 'getServiceProviders'])->name('api.service-providers');

    // Jobs Page Routes
    Route::get('/jobs-page', [JobsController::class, 'index'])->name('jobs-page.index');
    Route::get('/jobs-page/create', [JobsController::class, 'create'])->name('jobs-page.create');
    Route::post('/jobs-page', [JobsController::class, 'store'])->name('jobs-page.store');
    Route::get('/jobs-page/{job}', [JobsController::class, 'show'])->name('jobs-page.show');

    // Posts Routes
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
        ->name('posts.edit')
        ->middleware(['auth', 'verified']);
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    // Comment routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('comments.store');
        
    Route::put('/comments/{comment}', [CommentController::class, 'update'])
        ->middleware(['auth', 'verified'])
        ->name('comments.update');
        
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('comments.destroy');
    Route::post('/posts/{post}/likes', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy'])->name('likes.destroy');

    // Ratings Routes
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');

    // Service Provider Job Posts
    Route::get('/service-provider/job-posts', [ServiceProviderController::class, 'jobPosts'])
        ->name('service-providers.job-posts')
        ->middleware('auth');

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

Route::get('/services/list', [ServiceController::class, 'list'])->name('services.list');
Route::post('/check-unique', [RegisteredUserController::class, 'checkUnique'])
    ->name('check.unique');
require __DIR__.'/auth.php';
