<?php

use App\Http\Controllers\ProfileController;
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
    return view('home');
});


// Admin-only routes
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;

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
});

// Superuser-only routes
Route::middleware(['auth', 'superuser'])->group(function () {
    // Organization management (superuser only)
    Route::resource('organizations', OrganizationController::class);
});

// Regular admin routes (organization-specific)
Route::middleware(['auth', 'admin'])->group(function () {
    // Region management routes
    Route::resource('regions', RegionController::class);
    // Hearing management routes (create, edit, update, delete)
    Route::resource('hearings', HearingController::class)->except(['index', 'show']);
    // User management routes
    Route::resource('users', UserController::class);
});

// Routes accessible to all authenticated users
Route::middleware(['auth'])->group(function () {
    // Allow all users to view hearings list and individual hearings
    Route::get('/hearings', [HearingController::class, 'index'])->name('hearings.index');
    Route::get('/hearings/{hearing}', [HearingController::class, 'show'])->name('hearings.show');
    
    // Calendar functionality for hearings
    Route::get('/hearings/{hearing}/calendar/{provider}', [HearingController::class, 'addToCalendar'])->name('hearings.calendar');
    Route::get('/hearings/{hearing}/calendar.ics', [HearingController::class, 'downloadIcs'])->name('hearings.ics');
    
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

require __DIR__.'/auth.php';
