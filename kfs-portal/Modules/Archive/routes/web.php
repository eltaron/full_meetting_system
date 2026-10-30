<?php

use Illuminate\Support\Facades\Route;
use Modules\Archive\Http\Controllers\AttachmentController;
use App\Models\User;

Route::get('/print-user/{user}', function (User $user) {
    $authUser = auth()->user();

    abort_unless($authUser && $authUser->is_active && $authUser->isArchiveAdmin(), 403);

    return view('filament.admin.user-print-card', ['user' => $user]);
})->middleware('auth')->name('user.print');

Route::get('/attachments/{media}', AttachmentController::class)
    ->name('attachments.show')
    ->middleware('throttle:120,1');
