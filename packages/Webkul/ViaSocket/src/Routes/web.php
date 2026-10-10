<?php

use Illuminate\Support\Facades\Route;
use Webkul\ViaSocket\Http\Controllers\FlowController;
use Webkul\ViaSocket\Http\Controllers\IntegrationController;

Route::controller(IntegrationController::class)->group(function () {
    Route::get('', 'index')->name('admin.integrations.index');

    Route::get('token', 'token')->name('admin.integrations.token');

    Route::get('apps', 'apps')->name('admin.integrations.apps');

    Route::get('apps/{serviceId}', 'app')->name('admin.integrations.app');

    Route::get('apps/{serviceId}/{kind}/{versionId}', 'schema')
        ->where('kind', 'actions|triggers')
        ->name('admin.integrations.schema');

    Route::post('apps/{serviceId}/options', 'options')->name('admin.integrations.options');

    Route::get('connections', 'connections')->name('admin.integrations.connections.index');

    Route::post('connections', 'store')->name('admin.integrations.connections.store');

    Route::delete('connections/{serviceId}', 'destroy')->name('admin.integrations.connections.destroy');
});

Route::controller(FlowController::class)->prefix('flows')->group(function () {
    Route::get('', 'index')->name('admin.integrations.flows.index');

    Route::post('', 'store')->name('admin.integrations.flows.store');

    Route::put('{id}', 'update')->name('admin.integrations.flows.update');

    Route::get('{id}/history', 'history')->name('admin.integrations.flows.history');

    Route::post('{id}/status', 'status')->name('admin.integrations.flows.status');

    Route::delete('{id}', 'destroy')->name('admin.integrations.flows.destroy');
});
