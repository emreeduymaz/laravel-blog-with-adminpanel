<?php

namespace Emre\AdminPanel\Providers;

use Illuminate\Support\ServiceProvider;

class AdminPanelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/admin-panel.php', 'admin-panel');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/admin-panel.php' => config_path('admin-panel.php'),
        ], 'admin-panel-config');

        $this->app->register(AdminPanelProvider::class);
    }
}


