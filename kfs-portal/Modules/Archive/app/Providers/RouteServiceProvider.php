<?php

namespace Modules\Archive\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware(['web', 'auth', 'can:system.archive.access'])
            ->group(module_path('Archive', 'routes/web.php'));
    }
}
