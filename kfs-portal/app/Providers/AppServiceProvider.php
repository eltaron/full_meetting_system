<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class,
            \App\Http\Responses\FilamentLogoutResponse::class,
        );
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | مدير البوابة الأعلى يتجاوز كل بوابات الصلاحيات
        |--------------------------------------------------------------------------
        */
        Gate::before(function (User $user, string $ability): ?bool {
            if (! $user->is_active) {
                return false;
            }

            return $user->hasRole('super_admin') ? true : null;
        });

        /*
        |--------------------------------------------------------------------------
        | تسجيل آخر دخول للمستخدم
        |--------------------------------------------------------------------------
        */
        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | سياسات الأنظمة (لأن نماذجها داخل Modules)
        |--------------------------------------------------------------------------
        */
        Gate::policy(\Modules\Archive\Models\Subject::class, \Modules\Archive\Policies\SubjectPolicy::class);
        Gate::policy(\Modules\Followup\Models\TaskAssignment::class, \Modules\Followup\Policies\TaskAssignmentPolicy::class);
    }
}
