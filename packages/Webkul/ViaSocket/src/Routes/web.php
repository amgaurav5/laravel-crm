<?php

use Illuminate\Support\Facades\Route;
use Webkul\ViaSocket\Http\Controllers\ViaSocketController;

Route::controller(ViaSocketController::class)->group(function () {
    Route::get('', 'index')->name('admin.settings.viasocket.index');

    Route::get('token', 'token')->name('admin.settings.viasocket.token');

    Route::post('flows', 'storeFlow')->name('admin.settings.viasocket.flows.store');
});
