<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BusinessApprovalController;
use App\Http\Controllers\Admin\ListingApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingBrowseController;
use App\Http\Controllers\Owner\BookingManagementController;
use App\Http\Controllers\Owner\BusinessController as OwnerBusinessController;
use App\Http\Controllers\Owner\CalendarController;
use App\Http\Controllers\Owner\ListingController as OwnerListingController;
use App\Http\Controllers\Owner\ListingUnitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListingBrowseController::class, 'index'])->name('home');
Route::get('/listings', [ListingBrowseController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing:slug}', [ListingBrowseController::class, 'show'])->name('listings.show');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('owner/pending-approval', [OwnerBusinessController::class, 'pendingApproval'])
        ->name('owner.pending-approval');

    // Profile editing is available to every authenticated role
    // (customer, business owner, super admin) regardless of approval state.
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'customer'])->name('dashboard');

    Route::get('listings/{listing:slug}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('listings/{listing:slug}/book', [BookingController::class, 'store'])->name('bookings.store');

    Route::get('my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('my-bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::middleware(['auth', 'verified', 'role:business_owner'])->group(function () {
    Route::get('owner/business/create', [OwnerBusinessController::class, 'create'])->name('owner.business.create');
    Route::post('owner/business', [OwnerBusinessController::class, 'store'])->name('owner.business.store');
});

Route::middleware(['auth', 'verified', 'role:business_owner', 'business.approved'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');

    Route::get('business/edit', [OwnerBusinessController::class, 'edit'])->name('business.edit');
    Route::put('business', [OwnerBusinessController::class, 'update'])->name('business.update');

    Route::resource('listings', OwnerListingController::class)->except(['show']);
    Route::post('listings/{listing}/publish', [OwnerListingController::class, 'publish'])->name('listings.publish');
    Route::delete('listings/{listing}/images/{image}', [OwnerListingController::class, 'destroyImage'])->name('listings.images.destroy');

    Route::post('listings/{listing}/units', [ListingUnitController::class, 'store'])->name('listings.units.store');
    Route::put('listings/{listing}/units/{unit}', [ListingUnitController::class, 'update'])->name('listings.units.update');
    Route::delete('listings/{listing}/units/{unit}', [ListingUnitController::class, 'destroy'])->name('listings.units.destroy');

    Route::get('listings/{listing}/calendar', [CalendarController::class, 'show'])->name('listings.calendar');
    Route::get('listings/{listing}/calendar/events', [CalendarController::class, 'events'])->name('listings.calendar.events');
    Route::post('listings/{listing}/calendar/blocks', [CalendarController::class, 'storeBlock'])->name('listings.calendar.blocks.store');
    Route::delete('listings/{listing}/calendar/blocks/{block}', [CalendarController::class, 'destroyBlock'])->name('listings.calendar.blocks.destroy');

    Route::get('bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
    Route::post('bookings/{booking}/confirm', [BookingManagementController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/reject', [BookingManagementController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/{booking}/complete', [BookingManagementController::class, 'complete'])->name('bookings.complete');
    Route::post('bookings/{booking}/no-show', [BookingManagementController::class, 'noShow'])->name('bookings.no-show');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('businesses', [BusinessApprovalController::class, 'index'])->name('businesses.index');
    Route::get('businesses/{business}', [BusinessApprovalController::class, 'show'])->name('businesses.show');
    Route::post('businesses/{business}/approve', [BusinessApprovalController::class, 'approve'])->name('businesses.approve');
    Route::post('businesses/{business}/reject', [BusinessApprovalController::class, 'reject'])->name('businesses.reject');
    Route::post('businesses/{business}/suspend', [BusinessApprovalController::class, 'suspend'])->name('businesses.suspend');

    Route::get('listings', [ListingApprovalController::class, 'index'])->name('listings.index');
    Route::get('listings/{listing}', [ListingApprovalController::class, 'show'])->name('listings.show');
    Route::post('listings/{listing}/approve', [ListingApprovalController::class, 'approve'])->name('listings.approve');
    Route::post('listings/{listing}/reject', [ListingApprovalController::class, 'reject'])->name('listings.reject');
    Route::post('listings/{listing}/suspend', [ListingApprovalController::class, 'suspend'])->name('listings.suspend');

    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');
});