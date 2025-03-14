<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationRequestController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
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

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';

// User profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard routes based on user role
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect to appropriate dashboard based on user role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isCharity()) {
            return redirect()->route('charity.dashboard');
        } else {
            return redirect()->route('donations.index');
        }
    })->name('dashboard');
});

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/charities', [AdminController::class, 'charities'])->name('admin.charities');
    Route::patch('/charities/{charity}/approve', [AdminController::class, 'approveCharity'])->name('admin.charities.approve');
    Route::patch('/charities/{charity}/reject', [AdminController::class, 'rejectCharity'])->name('admin.charities.reject');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
});

// Charity routes
Route::resource('charities', CharityController::class);
Route::middleware(['auth', 'verified', 'auth.charity'])->prefix('charity')->group(function () {
    Route::get('/dashboard', [CharityController::class, 'dashboard'])->name('charity.dashboard');
});

// Donation request routes
Route::resource('requests', DonationRequestController::class);

// Donation routes
Route::resource('donations', DonationController::class);
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/donations/{donation}/thank-you', [DonationController::class, 'sendThankYou'])->name('donations.thank-you');
});

// Inventory routes
Route::resource('inventory', InventoryController::class);
Route::middleware(['auth', 'verified', 'auth.charity'])->group(function () {
    Route::get('/inventory/{inventory}/create-request', [InventoryController::class, 'createRequest'])->name('inventory.create-request');
});

// Category routes
Route::resource('categories', CategoryController::class);

// Item routes
Route::resource('items', ItemController::class);

// Notification routes
Route::middleware(['auth', 'verified'])->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
