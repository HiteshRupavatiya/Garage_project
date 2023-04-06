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

/**
 * Authenticate user globally access routes
 */
Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('verify-email/{token}', 'verifyEmail');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

/**
 * Authenticate user routes
 */
Route::controller(UserController::class)->middleware(['auth:api'])->prefix('user')->group(function () {
    Route::post('logout', 'logout');
    Route::post('change-password', 'changePassword');
});

/**
 * Admin group routes for access country, state, city, service_type
 */
Route::middleware(['auth:api', 'admin'])->group(function () {
    /**
     * country access group routes
     */
    Route::controller(CountryController::class)->prefix('country')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * state access group routes
     */
    Route::controller(StateController::class)->prefix('state')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * city access group routes
     */
    Route::controller(CityController::class)->prefix('city')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * service types access group routes
     */
    Route::controller(ServiceTypeController::class)->prefix('service-type')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });
});

/**
 * Authenticate user routes for multiple type wise access
 */
Route::middleware(['auth:api'])->group(function () {

    /**
     * Garage owner access only create, add-mechanic, get, update, delete, force-delete routes for manage garages
     * list route can only be access to Customer as a user
     */
    Route::controller(GarageController::class)->middleware(GarageOwner::class)->prefix('garage')->group(function () {
        Route::post('list', 'list')->middleware([Customer::class])->withoutMiddleware([GarageOwner::class]);
        Route::post('create', 'create');
        Route::post('add-mechanic', 'addMechanic');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * Car owner like Customer as a user can access this group routes for manage multiple cars
     */
    Route::controller(CarController::class)->middleware(Customer::class)->prefix('car')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create');
        Route::get('get/{id}', 'get');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * Garage owner access only list, update, delete, force-delete routes for manage Customer car services
     * Customer can access only create route for apply their cars to specific garage with service type
     */
    Route::controller(CarServicingController::class)->middleware(GarageOwner::class)->prefix('car-service')->group(function () {
        Route::post('list', 'list');
        Route::post('create', 'create')->middleware(Customer::class)->withoutMiddleware([GarageOwner::class]);
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'delete');
        Route::delete('force-delete/{id}', 'forceDelete');
    });

    /**
     * Garage owner access only create route for assign work to mechanic service job
     * Mechanic only access update route for manage car service job status
     */
    Route::controller(CarServicingJobController::class)->middleware(GarageOwner::class)->prefix('car-service-job')->group(function () {
        Route::post('create', 'create');
        Route::put('update/{id}', 'update')->middleware(Mechanic::class)->withoutMiddleware([GarageOwner::class]);
    });
});
