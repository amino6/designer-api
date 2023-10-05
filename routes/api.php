<?php

use App\Http\Controllers\Api\Designs\DesignController;
use App\Http\Controllers\Api\Designs\UploadController;
use App\Http\Controllers\Api\Comments\CommentController;
use App\Http\Controllers\Api\Teams\TeamController;
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

// designs
Route::get("designs", [DesignController::class, "index"]);

// teams
Route::get('/teams/slug/{slug}', [TeamController::class, 'findBySlug']);
//Route::get('/teams/slug/{slug}/designs', [TeamController::class, 'getTeamDesigns']);

Route::middleware('auth:sanctum')->group(function () {
    // designs
    Route::post("designs", [UploadController::class, "upload"]);
    Route::put("designs/{design}", [DesignController::class, "update"]);
    Route::delete("designs/{design}", [DesignController::class, "destroy"]);

    // comments
    Route::post("designs/{design}/comments", [CommentController::class, "store"]);
    Route::get("designs/{design}/comments/{comment}", [CommentController::class, "show"]);
    Route::put("designs/{design}/comments/{comment}", [CommentController::class, "update"]);
    Route::delete("designs/{design}/comments/{comment}", [CommentController::class, "destroy"]);

    // likes
    Route::post('/designs/{design}/like', [DesignController::class, 'like']);
    Route::post('/designs/{design}/liked', [DesignController::class, 'likedByUser']);

    // teams
    Route::get("teams", [TeamController::class, "index"]);
    Route::post("teams", [TeamController::class, "store"]);
    Route::get("teams/{team}", [TeamController::class, "show"]);
    Route::put("teams/{team}", [TeamController::class, "update"]);
    Route::delete("teams/{team}", [TeamController::class, "destroy"]);
    Route::get('/users/teams', [TeamController::class, 'getUserTeams']);
});
