<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GanttController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubprojectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\AgendaKegiatanController;
use App\Http\Controllers\HariLiburController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/convert-to-subproject', [ProjectController::class, 'convertToSubproject'])->name('projects.convert');
    Route::resource('subprojects', SubprojectController::class);
    
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::put('/comments/{comment}', [TaskCommentController::class, 'update'])->name('tasks.comments.update');
    Route::delete('/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::delete('/attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name('tasks.attachments.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/gantt', [GanttController::class, 'index'])->name('gantt');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin unit kerja & Super Admin routes
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::get('/admin/activity-logs', [UserController::class, 'logs'])->name('activity-log.index');
        Route::post('/users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Super Admin only — Unit Kerja management
    Route::middleware('can:viewAny,App\Models\UnitKerja')->group(function () {
        Route::resource('unit-kerja', UnitKerjaController::class);
        Route::get('/super-admin/overview', [UnitKerjaController::class, 'overview'])->name('unit-kerja.overview');
    });

    // Agenda Tahunan
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [AgendaKegiatanController::class, 'index'])->name('index');
        Route::post('/', [AgendaKegiatanController::class, 'store'])->name('store');
        Route::put('/{agenda}', [AgendaKegiatanController::class, 'update'])->name('update');
        Route::delete('/{agenda}', [AgendaKegiatanController::class, 'destroy'])->name('destroy');
        Route::post('/{agenda}/mapping', [AgendaKegiatanController::class, 'mapping'])->name('mapping');
        Route::delete('/{agenda}/mapping', [AgendaKegiatanController::class, 'resetMapping'])->name('resetMapping');
    });

    // Hari Libur (Admin & Super Admin)
    Route::middleware('can:viewAny,App\Models\User')->prefix('hari-libur')->name('hari-libur.')->group(function () {
        Route::get('/', [HariLiburController::class, 'index'])->name('index');
        Route::post('/', [HariLiburController::class, 'store'])->name('store');
        Route::delete('/{hariLibur}', [HariLiburController::class, 'destroy'])->name('destroy');
        Route::post('/sync/{tahun?}', [HariLiburController::class, 'sync'])->name('sync');
    });
});
