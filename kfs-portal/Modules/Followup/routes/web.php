<?php

use Illuminate\Support\Facades\Route;
use Modules\Followup\Http\Controllers\Admin\PermissionController;
use Modules\Followup\Http\Controllers\Admin\RoleController;
use Modules\Followup\Http\Controllers\Admin\UserController;
use Modules\Followup\Http\Controllers\Admin\UserPermissionController;
use Modules\Followup\Http\Controllers\SignatureOfficerController;
use Modules\Followup\Http\Controllers\TaskAssignmentController;
use Modules\Followup\Http\Controllers\TaskAssignmentDashboardController;
use Modules\Followup\Http\Controllers\TaskEntityController;
use Modules\Followup\Http\Controllers\TaskNotificationController;
use Modules\Followup\Http\Controllers\TaskSignatureController;
use Modules\Followup\Http\Controllers\TaskSourceController;

/*
|--------------------------------------------------------------------------
| Follow-up system routes
|--------------------------------------------------------------------------
| Mounted by FollowupServiceProvider under /followup, behind the
| "system.followup.access" permission. Logout is handled by the portal.
*/

Route::get('/dashboard', [TaskAssignmentDashboardController::class, 'index'])->name('dashboard');
Route::view('profile', 'profile')->name('profile');

Route::get('tasks/dashboard', [TaskAssignmentDashboardController::class, 'index'])->name('tasks.dashboard');
Route::get('tasks/{task}/document', [TaskAssignmentController::class, 'showDocument'])->name('tasks.document');
Route::post('tasks/{task}/signatures', [TaskSignatureController::class, 'store'])->name('tasks.signatures.store');
Route::resource('tasks', TaskAssignmentController::class)->parameters(['tasks' => 'task']);

Route::prefix('notifications')->name('task-notifications.')->group(function () {
    Route::get('/recent', [TaskNotificationController::class, 'recent'])->name('recent');
    Route::get('/{id}/open', [TaskNotificationController::class, 'open'])->name('open');
    Route::post('/mark-all-read', [TaskNotificationController::class, 'markAllRead'])->name('mark-all-read');
});

Route::middleware('can:tasks.view')->group(function () {
    Route::get('task-sources', [TaskSourceController::class, 'index'])->name('task-sources.index');
});
Route::middleware('can:tasks.create')->group(function () {
    Route::get('task-sources/create', [TaskSourceController::class, 'create'])->name('task-sources.create');
    Route::post('task-sources', [TaskSourceController::class, 'store'])->name('task-sources.store');
});
Route::middleware('can:tasks.edit')->group(function () {
    Route::get('task-sources/{taskSource}/edit', [TaskSourceController::class, 'edit'])->name('task-sources.edit');
    Route::put('task-sources/{taskSource}', [TaskSourceController::class, 'update'])->name('task-sources.update');
});
Route::middleware('can:tasks.delete')->group(function () {
    Route::delete('task-sources/{taskSource}', [TaskSourceController::class, 'destroy'])->name('task-sources.destroy');
});

Route::middleware('can:tasks.view')->group(function () {
    Route::get('task-entities', [TaskEntityController::class, 'index'])->name('task-entities.index');
});
Route::middleware('can:tasks.create')->group(function () {
    Route::get('task-entities/create', [TaskEntityController::class, 'create'])->name('task-entities.create');
    Route::post('task-entities', [TaskEntityController::class, 'store'])->name('task-entities.store');
});
Route::middleware('can:tasks.edit')->group(function () {
    Route::get('task-entities/{taskEntity}/edit', [TaskEntityController::class, 'edit'])->name('task-entities.edit');
    Route::put('task-entities/{taskEntity}', [TaskEntityController::class, 'update'])->name('task-entities.update');
});
Route::middleware('can:tasks.delete')->group(function () {
    Route::delete('task-entities/{taskEntity}', [TaskEntityController::class, 'destroy'])->name('task-entities.destroy');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['permission:roles.manage'])->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);

        Route::get('user-permissions', [UserPermissionController::class, 'index'])->name('user-permissions.index');
        Route::get('user-permissions/{user}/edit', [UserPermissionController::class, 'edit'])->name('user-permissions.edit');
        Route::put('user-permissions/{user}', [UserPermissionController::class, 'update'])->name('user-permissions.update');
    });

    Route::middleware(['permission:users.manage'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

Route::resource('signature-officers', SignatureOfficerController::class)->except(['show']);
Route::patch('signature-officers/{signature_officer}/toggle', [SignatureOfficerController::class, 'toggle'])
    ->name('signature-officers.toggle');
