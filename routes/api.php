<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\StateController;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(CountryController::class)->prefix('country')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(StateController::class)->prefix('state')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(CityController::class)->prefix('city')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(ServiceTypeController::class)->prefix('service-type')->group(function () {
    Route::post('list', 'list');
    Route::post('create', 'create');
    Route::get('get/{id}', 'get');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
    Route::delete('force-delete/{id}', 'forceDelete');
});

Route::controller(GarageController::class)->prefix('garage')->group(function () {
});
