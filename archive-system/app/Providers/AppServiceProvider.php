<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Subject;
use App\Observers\SubjectObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Subject::observe(SubjectObserver::class);

        // تسجيل وقت آخر دخول لكل موظف لمتابعة مدير الإدارة
        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            ActivityLog::record('login', user: $event->user);
        });
    }
}
