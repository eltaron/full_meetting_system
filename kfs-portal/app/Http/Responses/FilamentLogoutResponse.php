<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * بعد الخروج من أي لوحة Filament نعود إلى صفحة الدخول الموحّدة
 * بدلاً من صفحة دخول اللوحة (غير المفعّلة).
 */
class FilamentLogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->route('login')->with('status', 'تم تسجيل الخروج بنجاح.');
    }
}
