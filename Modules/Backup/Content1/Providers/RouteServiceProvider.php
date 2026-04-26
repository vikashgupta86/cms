<?php

namespace Modules\Content\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        // Admin routes — registered first, no wildcard patterns
        $this->mapAdminRoutes();

        // Catch-all CMS routes — registered last so they never shadow admin routes
        $this->mapCmsRoutes();
    }

    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->name('backend.')
            ->group(base_path('Modules/Content/routes/admin.php'));
    }

    protected function mapCmsRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('Modules/Content/routes/web.php'));
    }
}
