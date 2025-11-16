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

Route::apiResource('users', \App\Http\Controllers\UserController::class)->only(['index','show', 'destroy']);

Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\VerificationController::class, 'verify'])
    ->name('api.verify')
    ->middleware('signed'); // ensures signature & expiry are valid

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('posts',  PostController::class);
        Route::apiResource('drivers',  DriverController::class);
        Route::apiResource('trucks',  TruckController::class);
        Route::apiResource('bookings',  BookingController::class)->only(['index', 'show', 'store']);
    
    });
});



require __DIR__.'/auth.php';