<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CouncillorController;
use App\Http\Controllers\HearingVoteController;
use App\Http\Controllers\PublicHearingSubmissionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SubscriberAdminController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    if (auth()->check()) {
        return redirect()->to(RouteServiceProvider::homeRoute(auth()->user()));
    }

    if (Auth::guard('subscriber')->check()) {
        return redirect()->to(RouteServiceProvider::homeRoute(Auth::guard('subscriber')->user()));
    }

    // Get regions for the rotating header animation
    $regions = \App\Models\Region::orderBy('name')->pluck('name')->toArray();
    return view('home', compact('regions'));
});

// Passwordless authentication routes
use App\Http\Controllers\PasswordlessAuthController;
Route::post('/signup-passwordless', [PasswordlessAuthController::class, 'signup'])
    ->middleware('throttle:passwordless-signup')
    ->name('signup.passwordless');
Route::get('/dashboard/{token}', [PasswordlessAuthController::class, 'dashboard'])
    ->middleware('throttle:passwordless-dashboard')
    ->name('dashboard.token');

// Superuser-only routes
Route::middleware(['auth', 'superuser'])->group(function () {
    // Full organization management (superuser only)
    Route::resource('organizations', OrganizationController::class);
});

// Authenticated routes scoped by organization slug
Route::middleware(['auth:web'])->group(function () {
    Route::prefix('{organization:slug}')
        ->middleware('organization.access')
        ->group(function () {
            // Dashboard routing
            Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
                ->name('dashboard');

            // Admin-specific routes
            Route::middleware('admin')->group(function () {
                Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
                    ->name('admin.dashboard');

                Route::resource('regions', RegionController::class);
                Route::resource('hearings', HearingController::class)->except(['index', 'show']);
                Route::patch('hearings/{hearing}/approve', [HearingController::class, 'approve'])
                    ->name('hearings.approve');
                Route::resource('users', UserController::class);
                Route::resource('subscribers', SubscriberAdminController::class)->only(['index', 'destroy']);
                Route::resource('councillors', CouncillorController::class);
                Route::resource('hearing-votes', HearingVoteController::class);

                Route::get('/my-organization/edit', [OrganizationController::class, 'editOwn'])
                    ->name('organizations.edit-own');
                Route::put('/my-organization', [OrganizationController::class, 'updateOwn'])
                    ->name('organizations.update-own');
            });

            // User dashboards and preferences
            // Hearings & regions accessible to authenticated users
            Route::get('/hearings', [HearingController::class, 'index'])->name('hearings.index');
            Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
            Route::get('/regions/{region}', [RegionController::class, 'show'])->name('regions.show');

            // Profile management
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });
});

Route::middleware(['auth:subscriber'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('subscriber.dashboard');
    Route::post('/resubscribe', [UserDashboardController::class, 'resubscribe'])->name('subscriber.resubscribe');
    Route::post('/notification-preferences', [UserDashboardController::class, 'updateNotificationPreferences'])->name('subscriber.notification-preferences');
    Route::get('/hearings/upcoming', [UserDashboardController::class, 'getUpcomingHearings'])->name('subscriber.hearings');
});

Route::middleware(['auth:subscriber'])->prefix('{organization:slug}')->group(function () {
    Route::post('/regions/{region}/subscribe', [RegionController::class, 'subscribe'])->name('regions.subscribe');
    Route::post('/regions/{region}/unsubscribe', [RegionController::class, 'unsubscribe'])->name('regions.unsubscribe');
});

Route::get('/{organization:slug}/hearings/export', [HearingController::class, 'export'])
    ->name('organization.hearings.export');
Route::get('/{organization:slug}/hearings/embed', [HearingController::class, 'embed'])
    ->name('organization.hearings.embed');
Route::get('/{organization:slug}/regions/{region}/voting-embed', [RegionController::class, 'votingEmbed'])
    ->name('regions.voting-embed');

// Public hearing submission flow (no authentication required)
Route::get('/{organization:slug}/submit-hearing', [PublicHearingSubmissionController::class, 'create'])
    ->name('public.hearings.submit');
Route::post('/{organization:slug}/submit-hearing', [PublicHearingSubmissionController::class, 'store'])
    ->name('public.hearings.submit.store');
Route::get('/{organization:slug}/submit-hearing/thank-you', [PublicHearingSubmissionController::class, 'thankYou'])
    ->name('public.hearings.submit.thank-you');

// Public routes - accessible to everyone
// Individual hearing details and calendar functionality
Route::get('/hearings/{hearing}', [HearingController::class, 'show'])->name('hearings.show');
Route::get('/hearings/{hearing}/calendar/{provider}', [HearingController::class, 'addToCalendar'])->name('hearings.calendar');
Route::get('/hearings/{hearing}/calendar.ics', [HearingController::class, 'downloadIcs'])->name('hearings.ics');

// Signup routes (accessible to guests)
Route::middleware(['web'])->group(function () {
    Route::get('/signup', [SignupController::class, 'showSignupForm'])->name('signup');
    Route::post('/signup', [SignupController::class, 'processSignup'])->name('signup.process');
    Route::get('/signup/regions', [SignupController::class, 'getRegions'])->name('signup.regions');
    Route::get('/signup/thankyou', [SignupController::class, 'showThankYou'])->name('signup.thankyou');
});

// Unsubscribe routes (accessible to guests with signed URLs)
use App\Http\Controllers\UnsubscribeController;
Route::middleware(['web', 'signed'])->group(function () {
    Route::get('/unsubscribe', [UnsubscribeController::class, 'show'])->name('unsubscribe.show');
    Route::post('/unsubscribe', [UnsubscribeController::class, 'unsubscribe'])->name('unsubscribe.confirm');
});

require __DIR__.'/auth.php';
