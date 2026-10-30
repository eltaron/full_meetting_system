<?php

namespace Modules\Followup\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FollowupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Followup', 'database/migrations'));
        $this->loadViewsFrom(module_path('Followup', 'resources/views'), 'followup');
        $this->app['view']->addLocation(module_path('Followup', 'resources/views'));

        if (file_exists(module_path('Followup', 'app/helpers.php'))) {
            require_once module_path('Followup', 'app/helpers.php');
        }

        Gate::policy(
            \Modules\Followup\Models\TaskAssignment::class,
            \Modules\Followup\Policies\TaskAssignmentPolicy::class,
        );

        Route::middleware(['web', 'auth', 'can:system.followup.access'])
            ->prefix('followup')
            ->group(module_path('Followup', 'routes/web.php'));
    }
}
