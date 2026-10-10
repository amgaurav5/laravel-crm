<?php

use Illuminate\Support\Facades\Route;
use Webkul\ViaSocket\Http\Controllers\ViaSocketController;
use Webkul\ViaSocket\Support\IntegrationRegistry;

Route::controller(ViaSocketController::class)->group(function () {
    Route::get('token', 'token')->name('admin.settings.integrations.token');

    Route::post('connections', 'store')->name('admin.settings.integrations.connections.store');

    Route::delete('connections/{serviceId}', 'destroy')->name('admin.settings.integrations.connections.destroy');

    foreach (IntegrationRegistry::categories() as $category) {
        foreach ($category['apps'] as $app) {
            Route::get($app['slug'], 'show')
                ->defaults('serviceId', $app['service_id'])
                ->name($app['route']);
        }
    }
});
