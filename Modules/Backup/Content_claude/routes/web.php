<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Backend\ContentsController;
use Modules\Content\Http\Controllers\Frontend\CmsController;

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => ['web', 'auth'],
    'prefix'     => 'admin',
    'as'         => 'backend.',
], function () {
    Route::get('contents',                   [ContentsController::class, 'index'])->name('contents.index');
    Route::get('contents/create',            [ContentsController::class, 'create'])->name('contents.create');
    Route::post('contents',                  [ContentsController::class, 'store'])->name('contents.store');
    Route::get('contents/{content}/edit',    [ContentsController::class, 'edit'])->name('contents.edit');
    Route::put('contents/{content}',         [ContentsController::class, 'update'])->name('contents.update');
    Route::delete('contents/{content}',      [ContentsController::class, 'destroy'])->name('contents.destroy');
});

/*
|--------------------------------------------------------------------------
| Frontend CMS catch-all routes  — MUST be registered last
|--------------------------------------------------------------------------
| These are loaded from RouteServiceProvider with a low priority so they
| do not shadow admin, api, login, or asset routes.
*/
Route::group(['middleware' => 'web'], function () {
    // /{path}/{slug}  →  show individual content
    Route::get('{path}/{slug}', [CmsController::class, 'showContent'])
        ->where('path', '.*')
        ->where('slug', '[^/]+')
        ->name('cms.content');

    // /{path}  →  show menu listing or child-menu grid
    Route::get('{path}', [CmsController::class, 'show'])
        ->where('path', '.*')
        ->name('cms.show');
});
