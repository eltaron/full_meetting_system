<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserPermissionController;
use App\Http\Controllers\SignatureOfficerController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskAssignmentDashboardController;
use App\Http\Controllers\TaskEntityController;
use App\Http\Controllers\TaskNotificationController;
use App\Http\Controllers\TaskSignatureController;
use App\Http\Controllers\TaskSourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {

    // الصفحة الرئيسية = لوحة متابعة التكليفات
    Route::get('/dashboard', [TaskAssignmentDashboardController::class, 'index'])->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // -----------------------------------------------------------------
    // متابعة التكليفات
    // -----------------------------------------------------------------

    // لازم قبل resource عشان تتسجل قبل tasks/{task}
    Route::get('tasks/dashboard', [TaskAssignmentDashboardController::class, 'index'])
        ->name('tasks.dashboard');

    Route::get('tasks/{task}/document', [TaskAssignmentController::class, 'showDocument'])
        ->name('tasks.document');

    // تأشيرات التكليفات
    Route::post('tasks/{task}/signatures', [TaskSignatureController::class, 'store'])
        ->name('tasks.signatures.store');

    Route::resource('tasks', TaskAssignmentController::class)
        ->parameters(['tasks' => 'task']);

    Route::prefix('notifications')->name('task-notifications.')->group(function () {
        Route::get('/recent', [TaskNotificationController::class, 'recent'])->name('recent');
        Route::get('/{id}/open', [TaskNotificationController::class, 'open'])->name('open');
        Route::post('/mark-all-read', [TaskNotificationController::class, 'markAllRead'])->name('mark-all-read');
    });

    // مصادر التكليف
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

    // الجهات المختصة للتعامل
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

    // -----------------------------------------------------------------
    // إدارة المستخدمين والصلاحيات
    // -----------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {

        // الأدوار والصلاحيات (تتطلب صلاحية roles.manage)
        Route::middleware(['permission:roles.manage'])->group(function () {
            Route::resource('roles', RoleController::class)->except(['show']);
            Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);

            Route::get('user-permissions', [UserPermissionController::class, 'index'])->name('user-permissions.index');
            Route::get('user-permissions/{user}/edit', [UserPermissionController::class, 'edit'])->name('user-permissions.edit');
            Route::put('user-permissions/{user}', [UserPermissionController::class, 'update'])->name('user-permissions.update');
        });

        // المستخدمون (إضافة/تعديل/حذف) - يتطلب users.manage
        Route::middleware(['permission:users.manage'])->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
        });
    });

        Route::resource('signature-officers', SignatureOfficerController::class)->except(['show']);
    Route::patch('signature-officers/{signature_officer}/toggle', [SignatureOfficerController::class, 'toggle'])
        ->name('signature-officers.toggle'); 
});

require __DIR__.'/auth.php';
