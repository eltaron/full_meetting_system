<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// الإعدادات المشتركة بين لوحة الإدارة ولوحة الموظفين (الهوية البصرية، الأمان، السكانر)
trait ConfiguresArchivePanel
{
    protected function applyArchiveDefaults(Panel $panel): Panel
    {
        return $panel
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->profile()
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('3.5rem')
            ->brandName(config('archive.system_name') . ' - ' . config('archive.organization'))
            ->favicon(asset('favicon/favicon.ico'))
            ->font('Cairo')
            ->colors([
                'primary' => Color::hex('#0f3d6e'), // الكحلي الرسمي
                'warning' => Color::hex('#c9a227'), // الذهبي
                'gray' => Color::Slate,
            ])
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseTransactions()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ملفات التصميم والسكانر محلية (تعمل على شبكة المحافظة الداخلية بدون إنترنت)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="' . asset('css/archive.css') . '?v=1">'
                    . '<script src="' . asset('scannerjs/scanner.js') . '"></script>',
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.auth.login-header')->render(),
            )
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer')->render(),
            )
            ->userMenuItems([
                'profile' => MenuItem::make()->label('تعديل الحساب'),
            ]);
    }
}
