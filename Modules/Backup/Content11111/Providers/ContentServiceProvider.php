<?php

namespace Modules\Content\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider
{
    protected string $moduleName      = 'Content';
    protected string $moduleNameLower = 'content';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(base_path('Modules/Content/database/migrations'));

        // 1. Admin routes — loaded immediately so they are registered before
        //    any catch-all route can shadow them.
        $this->loadRoutesFrom(base_path('Modules/Content/routes/admin.php'));

        // 2. Catch-all CMS routes — deferred to AFTER all providers have booted
        //    so they are always last in the route list and never shadow
        //    admin, api, login, or asset routes.
        $this->booted(function () {
            $this->loadRoutesFrom(base_path('Modules/Content/routes/web.php'));
        });
    }

    public function register(): void
    {
        // RouteServiceProvider removed — routes are handled in boot() above
        // to guarantee load order (admin first, catch-all last).
        $this->app->register(EventServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            base_path('Modules/Content/Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            base_path('Modules/Content/Config/config.php'),
            $this->moduleNameLower
        );
    }

    protected function registerViews(): void
    {
        $viewPath   = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = base_path('Modules/Content/Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(
            array_merge($this->getPublishableViewPaths(), [$sourcePath]),
            $this->moduleNameLower
        );
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(
            base_path('Modules/Content/lang'),
            'content'
        );
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }
}
