<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\Media\MediaController;
use App\Http\Controllers\Api\Reservations\HotelController;
use App\Http\Controllers\Api\Reservations\RoomController;
use App\Http\Controllers\Api\Reservations\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::group(['middleware' => ['auth:jwt.auth']], function () {
            Route::post('logout', 'logout');
            Route::post('refresh', 'refresh');
        });
    });

    // Route::apiResource('media',         MediaController::class);

    Route::group(['middleware' => ['auth:api', 'jwt.auth']], function () {
        Route::apiResource('media',         MediaController::class);
        Route::apiResource('users',         UserController::class);
        Route::apiResource('countries',     CountryController::class);
        Route::apiResource('cities',        CityController::class);
        Route::apiResource('hotels',        HotelController::class);
        Route::apiResource('rooms',         RoomController::class);
        Route::apiResource('reservations',  ReservationController::class);
    });
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});
