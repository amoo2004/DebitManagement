<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SmsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('customers/{customer}/pdf', [CustomerController::class, 'loanHistoryPdf'])->name('customers.pdf');
    Route::resource('customers', CustomerController::class);
    Route::get('loans/export-excel', [LoanController::class, 'exportExcel'])->name('loans.export-excel');
    Route::get('loans/export-pdf', [LoanController::class, 'exportPdf'])->name('loans.export-pdf');
    Route::get('loans/{loan}/pdf', [LoanController::class, 'singlePdf'])->name('loans.pdf');
    Route::resource('loans', LoanController::class);
    Route::resource('payments', PaymentController::class);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('sms/send-manual', [SmsController::class, 'sendManual'])->name('sms.send-manual');
    Route::post('loans/{loan}/send-reminder', [SmsController::class, 'sendReminder'])->name('loans.send-reminder');

    Route::get('settings/sms', [SettingsController::class, 'smsSettings'])->name('settings.sms');
    Route::post('settings/sms', [SettingsController::class, 'updateSmsSettings'])->name('settings.sms.update');
    Route::get('settings/users', [SettingsController::class, 'users'])->name('settings.users');
    Route::post('settings/users', [SettingsController::class, 'createUser'])->name('settings.users.create');
    Route::put('settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('settings/users/{user}', [SettingsController::class, 'deleteUser'])->name('settings.users.delete');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
});

require __DIR__ . '/auth.php';
