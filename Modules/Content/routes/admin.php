<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\Backend\ContentsController;

/*
|--------------------------------------------------------------------------
| Content Module — Admin Routes
| Prefix "admin" and name "backend." applied by RouteServiceProvider.
|--------------------------------------------------------------------------
*/

// Single index route — Livewire handles all CRUD via AJAX
Route::get('contents', [ContentsController::class, 'index'])->name('contents.index');
