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

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password.post');

    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::get('/admin/activity-logs', [UserController::class, 'logs'])->name('users.logs');
        Route::post('/users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
        Route::resource('users', UserController::class)->except(['show']);
    });
});
