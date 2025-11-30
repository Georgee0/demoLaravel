<?php

use App\Http\Controllers\API\ActivateUserController;
use App\Http\Controllers\API\InviteUserController;
use App\Http\Controllers\API\DriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\ChangePasswordController;
use App\Http\Controllers\API\TruckController;



Route::apiResource('users', \App\Http\Controllers\API\UserController::class)->only(['index','show', 'destroy']);

// Email verification route
Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\API\VerificationController::class, 'verify'])
    ->name('api.verify')
    ->middleware('signed');


// Activate user account on invite
Route::post('/activate/{token} ', [ActivateUserController::class, 'activate'])->name('activate');


Route::post('/invite', [InviteUserController::class, 'inviteUser']);
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::middleware('can:invite-users')->group(function () {
        // User invitation
        
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
    
    Route::post('/user/change-password', [ChangePasswordController::class, 'store'])->name('password.change');

    Route::prefix('v1')->group(function () {
        Route::middleware('role:admin|transporter')->group(function () {
            // Admin-only routes can be placed here
            Route::apiResource('posts',  PostController::class);
            Route::apiResource('drivers',  DriverController::class);
            Route::apiResource('trucks',  TruckController::class);
            Route::apiResource('bookings',  BookingController::class)->only(['index', 'show', 'store']);
        });
    
    });
});



require __DIR__.'/auth.php';