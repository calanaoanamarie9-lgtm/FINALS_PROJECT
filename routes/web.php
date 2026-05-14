<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\InboxController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\StaffApplicationController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\ServiceBrowseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Staff\InboxController as StaffInboxController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('cleanswift-welcome');
});

Route::get('/services', [ServiceBrowseController::class, 'index'])->name('services.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:customer')->group(function () {
        Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('customer.bookings.index');
        Route::get('/bookings/create', [CustomerBookingController::class, 'create'])->name('customer.bookings.create');
        Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('customer.bookings.store');
        Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('customer.bookings.show');
        Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('customer.bookings.cancel');
        Route::patch('/bookings/{booking}/payment-method', [CustomerBookingController::class, 'updatePaymentMethod'])->name('customer.bookings.payment-method');
        Route::patch('/bookings/{booking}/reschedule', [CustomerBookingController::class, 'reschedule'])->name('customer.bookings.reschedule');
        Route::post('/bookings/{booking}/review', [CustomerBookingController::class, 'storeReview'])->name('customer.bookings.review');
    });

    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/bookings', [StaffBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/history', [StaffBookingController::class, 'history'])->name('bookings.history');
        Route::get('/bookings/{booking}', [StaffBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/advance', [StaffBookingController::class, 'advance'])->name('bookings.advance');

        Route::get('/notifications', [StaffInboxController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [StaffInboxController::class, 'read'])->name('notifications.read');
        Route::get('/notifications/unread-count', [StaffInboxController::class, 'unreadCount'])->name('notifications.unread-count');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/staff/create', [UserManagementController::class, 'createStaff'])->name('users.staff.create');
        Route::post('/users/staff', [UserManagementController::class, 'storeStaff'])->name('users.staff.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/staff-applications', [StaffApplicationController::class, 'index'])->name('staff-applications.index');
        Route::post('/staff-applications/{user}/approve', [StaffApplicationController::class, 'approve'])->name('staff-applications.approve');
        Route::post('/staff-applications/{user}/reject', [StaffApplicationController::class, 'reject'])->name('staff-applications.reject');

        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
        Route::patch('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::post('/bookings/{booking}/mark-paid', [AdminBookingController::class, 'markPaymentPaid'])->name('bookings.mark-paid');
        Route::post('/bookings/{booking}/mark-unpaid', [AdminBookingController::class, 'markPaymentUnpaid'])->name('bookings.mark-unpaid');
        Route::delete('/bookings', [AdminBookingController::class, 'destroyAll'])->name('bookings.destroy-all');

        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reports', ReportController::class)->name('reports');
        Route::get('/notifications', [InboxController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [InboxController::class, 'read'])->name('notifications.read');
    });
});

require __DIR__.'/auth.php';
