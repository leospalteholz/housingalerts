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
    return view('welcome');
});


// Admin-only routes
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\OrganizationController;

// Dashboard - accessible by both admins and superusers
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function() {
        // Redirect superusers to their dedicated dashboard
        if (auth()->user()->is_superuser) {
            return redirect()->route('superuser.dashboard');
        } 
        // Redirect admins to their dashboard
        else if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        // Regular users get the standard dashboard
        return view('dashboard');
    })->name('dashboard');
    
    // Admin dashboard
    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('admin')
        ->name('admin.dashboard');
        
    // Superuser dashboard
    Route::get('/superuser/dashboard', [App\Http\Controllers\SuperuserDashboardController::class, 'index'])
        ->middleware('superuser')
        ->name('superuser.dashboard');
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

require __DIR__.'/auth.php';
