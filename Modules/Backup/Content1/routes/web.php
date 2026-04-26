<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Frontend\CmsController;

/*
|--------------------------------------------------------------------------
| Content Module — Frontend CMS Catch-All Routes
|--------------------------------------------------------------------------
| These are loaded AFTER the admin routes inside RouteServiceProvider::map().
| The regex patterns exclude "admin", "login", "api" prefixes explicitly
| so they can never match those paths.
*/

Route::get('{path}/{slug}', [CmsController::class, 'showContent'])
    ->where('path', '^(?!admin|api|login|logout|register|storage).*')
    ->where('slug', '[a-zA-Z0-9\-\_]+')
    ->name('cms.content');

Route::get('{path}', [CmsController::class, 'show'])
    ->where('path', '^(?!admin|api|login|logout|register|storage).*')
    ->name('cms.show');
