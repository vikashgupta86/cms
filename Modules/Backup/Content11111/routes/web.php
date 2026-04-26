<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Frontend\CmsController;

/*
|--------------------------------------------------------------------------
| Content Module — Frontend CMS Catch-All Routes
|--------------------------------------------------------------------------
| IMPORTANT: These MUST be loaded LAST so they do not shadow admin,
| login, api, or any other named routes.
|
| Loaded via ContentServiceProvider::boot() with booted() callback,
| which runs after all other service providers have registered their routes.
*/

// /{path}/{slug}  →  single content item
Route::get('{path}/{slug}', [CmsController::class, 'showContent'])
    ->middleware('web')
    ->where('path', '[a-zA-Z0-9\-\_\/]+')
    ->where('slug', '[a-zA-Z0-9\-\_]+')
    ->name('cms.content');

// /{path}  →  menu listing or child-menu grid
Route::get('{path}', [CmsController::class, 'show'])
    ->middleware('web')
    ->where('path', '[a-zA-Z0-9\-\_\/]+')
    ->name('cms.show');
