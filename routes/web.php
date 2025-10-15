<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CouncillorController;
use App\Http\Controllers\HearingVoteController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\NotificationSettingsController;
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
    // Get regions for the rotating header animation
    $regions = \App\Models\Region::orderBy('name')->pluck('name')->toArray();
    return view('home', compact('regions'));
});

// Passwordless authentication routes
use App\Http\Controllers\PasswordlessAuthController;
Route::post('/signup-passwordless', [PasswordlessAuthController::class, 'signup'])->name('signup.passwordless');
Route::get('/dashboard/{token}', [PasswordlessAuthController::class, 'dashboard'])->name('dashboard.token');

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Region management routes
    Route::resource('regions', RegionController::class);
    // Hearing management routes (create, edit, update, delete) - MUST be before public hearing routes
    Route::resource('hearings', HearingController::class)->except(['index', 'show']);
    // User management routes
    Route::resource('users', UserController::class);
});

// Dashboard - accessible by all authenticated users
Route::middleware(['auth'])->group(function () {
    // Main dashboard route that routes users to appropriate dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Specific dashboard routes
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])
        ->middleware('admin')
        ->name('admin.dashboard');
    
    Route::get('/user/dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])
        ->name('user.dashboard');
    
    Route::post('/user/resubscribe', [App\Http\Controllers\UserDashboardController::class, 'resubscribe'])
        ->name('user.resubscribe');
    
    Route::post('/user/notification-preferences', [App\Http\Controllers\UserDashboardController::class, 'updateNotificationPreferences'])
        ->name('user.notification-preferences');
    
    Route::get('/user/hearings', [App\Http\Controllers\UserDashboardController::class, 'getUpcomingHearings'])
        ->name('user.hearings');
    
    // Notification settings
    Route::get('/notification-settings', [NotificationSettingsController::class, 'show'])
        ->name('notification-settings');
    Route::post('/notification-settings', [NotificationSettingsController::class, 'update'])
        ->name('notification-settings.update');
});

// Admin routes - for both superusers and regular admins
Route::middleware(['auth', 'admin'])->group(function () {
    // Allow admins to edit their own organization
    Route::get('/my-organization/edit', [OrganizationController::class, 'editOwn'])->name('organizations.edit-own');
    Route::put('/my-organization', [OrganizationController::class, 'updateOwn'])->name('organizations.update-own');
    
    // Councillor management (accessible to all admins)
    Route::resource('councillors', CouncillorController::class);
    
    // Hearing votes management (accessible to all admins)
    Route::resource('hearing-votes', HearingVoteController::class);
});

// Superuser-only routes
Route::middleware(['auth', 'superuser'])->group(function () {
    // Full organization management (superuser only)
    Route::resource('organizations', OrganizationController::class);
});

// Public routes - accessible to everyone
// Individual hearing details and calendar functionality
Route::get('/hearings/{hearing}', [HearingController::class, 'show'])->name('hearings.show');
Route::get('/hearings/{hearing}/calendar/{provider}', [HearingController::class, 'addToCalendar'])->name('hearings.calendar');
Route::get('/hearings/{hearing}/calendar.ics', [HearingController::class, 'downloadIcs'])->name('hearings.ics');

// Routes accessible to all authenticated users
Route::middleware(['auth'])->group(function () {
    // Allow all users to view hearings list
    Route::get('/hearings', [HearingController::class, 'index'])->name('hearings.index');
    
    // Allow all users to view regions list (for monitoring)
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/{region}', [RegionController::class, 'show'])->name('regions.show');
    
    // Region subscription endpoints for regular users
    Route::post('/regions/{id}/subscribe', [RegionController::class, 'subscribe'])->name('regions.subscribe');
    Route::post('/regions/{id}/unsubscribe', [RegionController::class, 'unsubscribe'])->name('regions.unsubscribe');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Signup routes (accessible to guests)
use App\Http\Controllers\SignupController;
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
