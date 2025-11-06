<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Give Super Admin all permissions
        Gate::before(function ($user, $ability) {
            try {
                if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // ignore if roles table not ready (during first migration)
            }
        });

        // Register explicit policy for LearningLog
        try {
            Gate::policy(\App\Models\LearningLog::class, \App\Policies\LearningLogPolicy::class);
        } catch (\Throwable $e) {
            // During early bootstrap before classes exist
        }
    }
}
