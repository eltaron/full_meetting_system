<?php

namespace Modules\Meetings\Providers;

use Illuminate\Support\ServiceProvider;

class MeetingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(Filament\AdminPanelProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Meetings', 'database/migrations'));
        $this->loadViewsFrom(module_path('Meetings', 'resources/views'), 'meetings');
        $this->app['view']->addLocation(module_path('Meetings', 'resources/views'));
    }
}
