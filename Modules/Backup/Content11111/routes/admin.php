<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Backend\ContentsController;

/*
|--------------------------------------------------------------------------
| Content Module — Admin Routes
|--------------------------------------------------------------------------
| Loaded early, before the catch-all CMS routes.
*/

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix'     => 'admin',
    'as'         => 'backend.',
], function () {
    Route::get('contents',                [ContentsController::class, 'index'])->name('contents.index');
    Route::get('contents/create',         [ContentsController::class, 'create'])->name('contents.create');
    Route::post('contents',               [ContentsController::class, 'store'])->name('contents.store');
    Route::get('contents/{content}/edit', [ContentsController::class, 'edit'])->name('contents.edit');
    Route::put('contents/{content}',      [ContentsController::class, 'update'])->name('contents.update');
    Route::delete('contents/{content}',   [ContentsController::class, 'destroy'])->name('contents.destroy');
});
