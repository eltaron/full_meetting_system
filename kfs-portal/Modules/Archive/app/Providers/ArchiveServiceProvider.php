<?php

namespace Modules\Archive\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Archive\Models\ActivityLog;
use Modules\Archive\Models\Subject;
use Modules\Archive\Observers\SubjectObserver;
use Modules\Archive\Policies\SubjectPolicy;

class ArchiveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(Filament\AdminPanelProvider::class);
        $this->app->register(Filament\StaffPanelProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Archive', 'database/migrations'));
        $this->loadViewsFrom(module_path('Archive', 'resources/views'), 'archive');
        $this->mergeConfigFrom(module_path('Archive', 'config/archive.php'), 'archive');

        // The legacy views use unqualified names such as "filament.footer";
        // register the module view path as a fallback location for those.
        $this->app['view']->addLocation(module_path('Archive', 'resources/views'));

        Subject::observe(SubjectObserver::class);
        Gate::policy(Subject::class, SubjectPolicy::class);

        Event::listen(Login::class, function (Login $event): void {
            $user = $event->user;

            if (! $user || ! $user->exists) {
                return;
            }

            try {
                ActivityLog::record('login', user: $user);
            } catch (\Throwable) {
                // لا نُفشل تسجيل الدخول إذا لم يكن سجل النشاط جاهزاً بعد.
            }
        });
    }
}
