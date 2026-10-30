<?php

use App\Http\Controllers\AttachmentController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // طباعة بيانات حساب موظف: لمدير المنظومة فقط
    Route::get('/print-user/{user}', function (User $user) {
        abort_unless(auth()->user()->isAdmin() && auth()->user()->is_active, 403);

        return view('filament.admin.user-print-card', ['user' => $user]);
    })->name('user.print');

    // فتح المرفقات عبر رابط محمي بالصلاحيات (بدلاً من رابط عام على storage)
    Route::get('/attachments/{media}', AttachmentController::class)
        ->name('attachments.show')
        ->middleware('throttle:120,1');
});
