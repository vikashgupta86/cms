<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Backend\ContentsController;

/*
|--------------------------------------------------------------------------
| Content Module — Admin Routes
|--------------------------------------------------------------------------
| Prefix "admin" and name "backend." are applied by RouteServiceProvider.
| Do NOT add them again here.
*/

Route::get('contents',                [ContentsController::class, 'index'])->name('contents.index');
Route::get('contents/create',         [ContentsController::class, 'create'])->name('contents.create');
Route::post('contents',               [ContentsController::class, 'store'])->name('contents.store');
Route::get('contents/{content}/edit', [ContentsController::class, 'edit'])->name('contents.edit');
Route::put('contents/{content}',      [ContentsController::class, 'update'])->name('contents.update');
Route::delete('contents/{content}',   [ContentsController::class, 'destroy'])->name('contents.destroy');
