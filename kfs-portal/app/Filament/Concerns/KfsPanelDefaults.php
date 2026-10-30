<?php

namespace App\Filament\Concerns;

use Filament\Panel;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Shared "KFS Royal Egyptian" panel defaults.
 *
 * Every system panel (Archive, Meetings, Portal) applies this trait so the
 * three systems stay visually identical: Egyptian green as the primary
 * colour, gold as the accent, calm warm surfaces and Arabic RTL typography.
 */
trait KfsPanelDefaults
{
    protected function applyKfsDefaults(Panel $panel, string $systemName, string $systemTagline = 'محافظة كفر الشيخ'): Panel
    {
        return $panel
            ->brandName($systemName)
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('favicon/favicon.ico'))
            ->font('Tajawal')
            ->colors([
                'primary' => [
                    50 => '#EFF7F3',
                    100 => '#D8EDE3',
                    200 => '#B3DBC7',
                    300 => '#84C3A6',
                    400 => '#52A582',
                    500 => '#2E8A66',
                    600 => '#1F6E51',
                    700 => '#185640',
                    800 => '#144536',
                    900 => '#11392D',
                    950 => '#0A241C',
                ],
                'warning' => [
                    50 => '#FBF7E9',
                    100 => '#F5EDC9',
                    200 => '#EBDC96',
                    300 => '#E0C75E',
                    400 => '#D4B33A',
                    500 => '#C9A227',
                    600 => '#A8821E',
                    700 => '#85641A',
                    800 => '#6B511B',
                    900 => '#5A441B',
                    950 => '#34260B',
                ],
                'danger' => [
                    50 => '#FBEFED',
                    100 => '#F6D9D5',
                    200 => '#EDB4AC',
                    300 => '#DF887B',
                    400 => '#CC5D4D',
                    500 => '#B23A2E',
                    600 => '#953027',
                    700 => '#782820',
                    800 => '#5E211B',
                    900 => '#4C1D18',
                    950 => '#2A0F0D',
                ],
                'gray' => \Filament\Support\Colors\Color::Stone,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
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
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true)
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseTransactions()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('portal.partials.panel-head', ['systemTagline' => $systemTagline]),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): View => view('portal.partials.sidebar-footer'),
            )
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): View => view('portal.partials.panel-footer'),
            );
    }
}
