<?php

use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('approval-requests', [ApprovalRequestController::class, 'store'])->name('approval-requests.store');
    Route::post('approval-requests/{approval}/approve', [ApprovalRequestController::class, 'approve'])->name('approval-requests.approve');
    Route::post('approval-requests/{approval}/reject', [ApprovalRequestController::class, 'reject'])->name('approval-requests.reject');
});

// Organization invitations
Route::post('invitations', [\App\Http\Controllers\OrganizationInvitationController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('invitations.store');

Route::get('invitations/{token}', [\App\Http\Controllers\OrganizationInvitationController::class, 'acceptForm'])
    ->name('invitations.accept.form');

Route::post('invitations/{token}/accept', [\App\Http\Controllers\OrganizationInvitationController::class, 'accept'])
    ->name('invitations.accept');

// Admin invite UI (Inertia page)
Route::inertia('admin/invitations', 'admin/invitations/invite')
    ->middleware(['auth', 'verified'])
    ->name('admin.invitations.invite');

// Admin specialists management
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('specialists', [\App\Http\Controllers\Admin\SpecialistController::class, 'index'])->name('specialists.index');
    Route::post('specialists/{user}/status', [\App\Http\Controllers\Admin\SpecialistController::class, 'updateStatus'])->name('specialists.updateStatus');
});

require __DIR__.'/settings.php';
