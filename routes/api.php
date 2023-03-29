<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarServicingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Models\CarServicing;
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

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('verify-email/{token}', 'verifyEmail');
});

Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::post('user/logout', [UserController::class, 'logout'])->withoutMiddleware(['admin']);

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
});

Route::middleware(['auth:api', 'garage_owner'])->group(function () {
    Route::controller(GarageController::class)->prefix('garage')->group(function () {
        Route::post('list', 'list')->withoutMiddleware(['garage_owner']);
        Route::post('create', 'create');
        Route::post('add-mechanic', 'addMechanic');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });
});

Route::middleware(['auth:api', 'customer'])->group(function () {
    Route::controller(CarController::class)->prefix('car')->group(function () {
        Route::post('list', 'list');
        Route::post('search-garage', 'searchGarage');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    Route::controller(CarServicingController::class)->prefix('car-service')->group(function () {
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
    });
});
