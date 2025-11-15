<?php

use App\Http\Controllers\API\DriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TruckController;

Route::get('/demo', function (Request $request) {
    return ["message" => "This is a demo API route."];
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('posts',  PostController::class);
        Route::apiResource('drivers',  DriverController::class);
        Route::apiResource('trucks',  TruckController::class);
        Route::apiResource('bookings',  BookingController::class);
    
    });
});



require __DIR__.'/auth.php';