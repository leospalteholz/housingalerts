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
    // Hearing management routes
    Route::resource('hearings', HearingController::class);
    // User management routes
    Route::resource('users', UserController::class);
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
