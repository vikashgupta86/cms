<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => '\\Modules\\Content\\Http\\Controllers\\Frontend',
    'middleware' => 'web',
    'prefix' => 'cms',
], function () {
    Route::get('/{menuPath}/{contentSlug}', 'CmsController@showItem')
        ->where('menuPath', '.*')
        ->name('cms.item');

    Route::get('/{menuPath}', 'CmsController@show')
        ->where('menuPath', '.*')
        ->name('cms.show');
});

Route::group([
    'namespace' => '\\Modules\\Content\\Http\\Controllers\\Backend',
    'as' => 'backend.',
    'middleware' => ['web', 'auth', 'can:view_backend'],
    'prefix' => 'admin',
], function () {
    $module_name = 'contents';
    $controller_name = 'ContentsController';
    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@trashed"]);
    Route::patch("$module_name/trashed/{id}", ['as' => "$module_name.restore", 'uses' => "$controller_name@restore"]);
    Route::resource("$module_name", "$controller_name");
});
