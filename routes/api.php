<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarServicingController;
use App\Http\Controllers\CarServicingJobController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Customer;
use App\Http\Middleware\GarageOwner;
use App\Http\Middleware\Mechanic;
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
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::controller(UserController::class)->middleware(['auth:api'])->prefix('user')->group(function () {
    Route::post('logout', 'logout');
    Route::post('change-password', 'changePassword');
});

Route::middleware(['auth:api', 'admin'])->group(function () {
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

Route::middleware(['auth:api'])->group(function () {
    Route::controller(GarageController::class)->middleware(GarageOwner::class)->prefix('garage')->group(function () {
        Route::post('list', 'list')->withoutMiddleware(['garage_owner']);
        Route::post('create', 'create');
        Route::post('add-mechanic', 'addMechanic');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    Route::controller(CarController::class)->middleware(Customer::class)->prefix('car')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    Route::controller(CarServicingController::class)->middleware(GarageOwner::class)->prefix('car-service')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create')->middleware(Customer::class)->withoutMiddleware([GarageOwner::class]);
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    Route::controller(CarServicingJobController::class)->middleware(GarageOwner::class)->prefix('car-service-job')->group(function () {
        Route::post('create', 'create');
        Route::put('update/{id}', 'update')->middleware(Mechanic::class)->withoutMiddleware([GarageOwner::class]);
    });
});
