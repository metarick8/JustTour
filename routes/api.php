<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\TeamAccountsController;
use App\Http\Controllers\Api\UserRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Trip\TripController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Api Routes
Route::post("register", [TeamAccountsController::class, "register"]);
Route::post("userRegister", [UserRegisterController::class, "userRegister"]);
Route::post("login", [ApiController::class, "login"]);
Route::get("showTrips/{type}", [TripController::class, "showTrips"]); //being edited
//Route::get('showTeams', [TeamAccountsController::class, 'showTeams']);

Route::group(["middleware" => ["auth:api"]], function () {
    Route::prefix('user')->group(function () {
        Route::post("editProfile", [UserRegisterController::class, "editUserProfile"]);
        Route::get("profile", [ApiController::class, "profile"]);
        Route::get("refresh", [ApiController::class, "refreshToken"]);
        Route::get("logout", [ApiController::class, "logout"]);
        Route::post("cancelReservationInfo", [TripController::class, "cancelReservationInfo"]);
        Route::post("confirmCancelReservationInfo", [TripController::class, "confirmCancellation"]);
        //Route::get("showTrips", [TripController::class, "showTrips"]);
        Route::get("showTeams", [TeamAccountsController::class, "showTeams"]);
        Route::post("reserveTrip", [TripController::class, "reserveTrip"]);
        Route::post("rateTrip", [TripController::class, "rateTrip"]);
        Route::post("follow/{teamId}", [ApiController::class, "followTeam"]);
        Route::post("followingTeams", [ApiController::class, "getFollowingTeams"]);
        Route::get("getTrip/upComming", [TripController::class, "getUpCommingTrips"]);
        Route::get("getTrip/{id}", [TripController::class, "getTrip"])->name('user.getTrip');
        Route::get("getTeam/{id}", [TeamAccountsController::class, "showTeam"])->name('user.showTeam');

    });
});

Route::group(["middleware" => ["auth:team_api"]], function () {
    Route::prefix('team')->group(function () {
        Route::post("editProfile", [TeamAccountsController::class, "editTeamProfile"]);
        Route::get('showTeams', [TeamAccountsController::class, 'showTeams']);
        Route::get("profile", [ApiController::class, "profile"]);
        Route::get("refresh", [ApiController::class, "refreshToken"]);
        Route::get("logout", [ApiController::class, "logout"]);
        Route::post("addTrip", [TripController::class, "addTrip"]);
        Route::get("getTrip/{id}", [TripController::class, "getTrip"])->name('team.getTrip');;
        Route::post("deleteTrip", [TripController::class, "cancelTrip"]);
        Route::get('showUsers', [UserRegisterController::class, 'showUsers']);
        Route::get('myTrips', [TripController::class, 'getTeamTrips']);
        Route::get('showFollowers', [TeamAccountsController::class, 'getFollowers']);
        Route::get('showCountestants/{tripId}', [TripController::class, 'showCountestantsInTrip']);
        Route::post('checklist', [TripController::class, 'checklist']);
        Route::get("getTeam/{id}", [TeamAccountsController::class, "showTeam"])->name('team.showTeam');
    });
});

Route::prefix('admin')->group(function () {
    Route::prefix('team')->group(function () {
        Route::get('all', [AdminController::class, 'allTeams']);
        Route::post("add", [AdminController::class, "addTeam"]);
        Route::get("{id}", [TeamAccountsController::class, "showTeam"])->name('admin.showTeam');

    });
    Route::prefix('site')->group(function () {
        Route::post("add", [AdminController::class, "addSite"]);
        Route::get("all", [AdminController::class, "allSites"]);
        Route::get("{siteId}", [AdminController::class, "getSite"]);
});
Route::prefix('trip')->group(function () {
    Route::get('{type}', [TripController::class, 'showTrips']);
    Route::get('details/{id}', [TripController::class, 'getTrip']);
});
    Route::get('getWallet', [AdminController::class, 'appWallet']);
    Route::get('user/all', [AdminController::class, 'allUsers']);
    Route::get('getCount', [DashboardController::class, 'getCount']);

});
