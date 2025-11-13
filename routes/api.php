<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;


Route::get('/demo', function (Request $request) {
    return ["message" => "This is a demo API route."];
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function () {
        Route::apiResource('posts',  PostController::class);
    
    });
});



require __DIR__.'/auth.php';