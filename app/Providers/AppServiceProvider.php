<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Services\AdminFieldPermissionService;
use App\Services\AdminPermissionRegistry;
use App\Services\AdminPermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdminPermissionRegistry::class);
        $this->app->scoped(AdminPermissionService::class);
        $this->app->scoped(AdminFieldPermissionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        if (env('APP_URL') === 'https://api.talentyar.com') {
            URL::forceScheme('https');
        }
    }
}
