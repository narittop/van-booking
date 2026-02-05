<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DirectorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
      return view('auth.login');
});

// Public QR Code verification route
Route::get('/bookings/verify/{booking}', [BookingController::class, 'verify'])->name('bookings.verify');

Route::get('/dashboard', function () {
    if (auth()->user()->isDirector()) {
        return redirect()->route('director.dashboard');
    }
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('bookings.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Super Admin View Mode Switch
    Route::get('/switch-view-mode/{mode}', function ($mode) {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        if (in_array($mode, ['admin', 'director', 'user'])) {
            session(['super_admin_view_mode' => $mode]);
        }
        return back();
    })->name('switch.view.mode');

    // Booking Routes
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
    Route::get('bookings/{booking}/pdf', [BookingController::class, 'downloadPdf'])->name('bookings.pdf');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Van Management
    Route::resource('vans', VanController::class);
    
    // User Management (Super Admin only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{booking}/receive', [AdminController::class, 'receive'])->name('bookings.receive');
    Route::post('/bookings/{booking}/reject', [AdminController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/complete', [AdminController::class, 'complete'])->name('bookings.complete');
});

// Director Routes
Route::middleware(['auth', 'director'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', [DirectorController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [DirectorController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [DirectorController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{booking}/approve', [DirectorController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [DirectorController::class, 'reject'])->name('bookings.reject');
});

require __DIR__.'/auth.php';
