<?php

use App\Http\Controllers\Api\Designs\DesignController;
use App\Http\Controllers\Api\Designs\UploadController;
use App\Http\Controllers\Api\Comments\CommentController;
use App\Http\Controllers\Api\Teams\InvitationController;
use App\Http\Controllers\Api\Teams\TeamController;
use App\Http\Controllers\Api\Users\UserController;
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

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

// designs
Route::get("designs/slug/{slug}", [DesignController::class, "findBySlug"]);

// search
Route::get("search/designs", [DesignController::class, "search"]);
Route::get("search/designers", [UserController::class, "search"]);

// teams
Route::get("teams/slug/{slug}", [TeamController::class, "findBySlug"]);
Route::get("teams/slug/{slug}/designs", [TeamController::class, "getTeamDesigns"]);

// users
Route::get("users/{id}/info", [UserController::class, "getUserInfo"]);
Route::get("users/{user}/designs", [UserController::class, "getUserDesigns"]);

Route::middleware("auth:sanctum")->group(function () {
    // designs
    Route::get("designs/{id}", [DesignController::class, "findById"]);
    Route::post("designs", [UploadController::class, "upload"]);
    Route::put("designs/{design}", [DesignController::class, "update"]);
    Route::delete("designs/{design}", [DesignController::class, "destroy"]);

    // users
    Route::put("user/settings", [UserController::class, "updateProfile"]);
    Route::put("user/contact-information", [UserController::class, "updateContacts"]);
    Route::get("users/designs", [UserController::class, "getDesigns"]);
    Route::get("user/designs/liked", [UserController::class, "getLikedDesigns"]);

    // comments
    Route::post("designs/{design}/comments", [CommentController::class, "store"]);
    Route::get("designs/{design}/comments/{comment}", [CommentController::class, "show"]);
    Route::put("designs/{design}/comments/{comment}", [CommentController::class, "update"]);
    Route::delete("designs/{design}/comments/{comment}", [CommentController::class, "destroy"]);

    // likes
    Route::post("designs/{design}/like", [DesignController::class, "like"]);
    Route::post("designs/{design}/liked", [DesignController::class, "likedByUser"]);

    // teams
    Route::get("teams", [TeamController::class, "index"]);
    Route::post("teams", [TeamController::class, "store"]);
    Route::get("teams/{team}", [TeamController::class, "show"]);
    Route::put("teams/{team}", [TeamController::class, "update"]);
    Route::delete("teams/{team}", [TeamController::class, "destroy"]);
    Route::get("users/teams", [TeamController::class, "getUserTeams"]);
    Route::delete("teams/{team}/user/{user}", [TeamController::class, "removeUserFromTeam"]);

    // invitations
    Route::post("invitations/{team}", [InvitationController::class, "invite"]);
    Route::post("invitations/{invitation}/resend", [InvitationController::class, "resend"]);
    Route::post("invitations/{invitation}/respond", [InvitationController::class, "respond"]);
    Route::delete("invitations/{invitation}", [InvitationController::class, "destroy"]);
});
