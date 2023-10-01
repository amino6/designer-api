<?php

use App\Http\Controllers\Api\Designs\DesignController;
use App\Http\Controllers\Api\Designs\UploadController;
use App\Http\Controllers\Comments\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get("designs", [DesignController::class, "index"]);

Route::middleware('auth:sanctum')->group(function() {
    // designs
    Route::post("designs", [UploadController::class, "upload"]);
    Route::put("designs/{design}", [DesignController::class, "update"]);
    Route::delete("designs/{design}", [DesignController::class, "destroy"]);

    // comments
    Route::post("designs/{design}/comments", [CommentController::class, "store"]);
    Route::get("designs/{design}/comments/{comment}", [CommentController::class, "show"]);
    Route::put("designs/{design}/comments/{comment}", [CommentController::class, "update"]);
    Route::delete("designs/{design}/comments/{comment}", [CommentController::class, "destroy"]);
});
