<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Trip\TripController;
use App\Http\Resources\siteResource;
use App\Http\Resources\teamResource;
use App\Http\Resources\tripResource;
use App\Http\Resources\userForPublicResource;
use App\Http\Resources\userResource;
use App\Models\Site;
use App\Models\Team;
use App\Models\Trip;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use Response;
    function appWallet()
    {
        $totalTeamWallet = Team::sum('Wallet');
        return $this->success($totalTeamWallet, 'App profit');
    }
    function allTeams()
    {
        $teams = Team::all();
        $teams = teamResource::collection($teams);
        return $this->success($teams, 'All teams in the app');
    }
    function allUsers()
    {
        $users = User::all();
        $users = userForPublicResource::collection($users);
        return $this->success($users, 'All users in the app');
    }
    function allTrips()
    {
        return (new TripController)->showTrips('all');
    }
    //by level, by type
    function sortedTrips()
    {
        $allTrips = [
            [
                "by level:" =>
                [
                    "easy:" => (new TripController)->showTrips('easy', true),
                    "medium:" => (new TripController)->showTrips('medium', true),
                    "hard:" => (new TripController)->showTrips('hard', true)
                ],
                "by type:" =>
                [
                    "tour:" => (new TripController)->showTrips('tour', true),
                    "adventure:" => (new TripController)->showTrips('adventure', true),
                    "cultural:" => (new TripController)->showTrips('cultural', true),
                    "excursions:" => (new TripController)->showTrips('excursions', true),
                    "leisure:" => (new TripController)->showTrips('leisure', true)
                ],

            ]
        ];
        return $this->success($allTrips, 'All the trips in the app being sorted');
    }

    function addSite(Request $request) {
        $request->validate([
            "SiteName" => "required | string",
            "Loction" => "required | string",
            "Details" => "required | string"
        ]);

        $site = Site::create([
            "SiteName" => $request->SiteName,
            "Location" => $request->Loction,
            "Details" => $request->Details,
        ]);
        return $this->success('', "Site " . $site->SiteName . " added successfully!");
    }

    function getSite($siteId) {
        $site = Site::find($siteId);
        if($site)
            return $this->success(new siteResource($site), "Site details:");
        return $this->error('', "Invalid site ID", 401);
    }
    function allSites() {
        $sites = Site::all();
        if($sites->isEmpty())
            return $this->success('', 'No sites yet');
        return $this->success(siteResource::collection($sites), "All sites details:");
    }

    function addTeam(Request $request)
    {

        // $request->validate([
        //     "TeamName" => "required|string",
        //     "Email" => "required|email|unique:teams",
        //     "Password" => "required|confirmed|string|min:6",
        //     //"Description" => "required|string",
        //     "ContactInfo" => "required|string",
        //     //"ProfilePhoto" => "required|string",
        // ]);
            return $this->success($request->TeamName, '');
        $team = Team::create([
            "TeamName" => $request->TeamName,
            "Email" => $request->Email,
            "Password" => Hash::make($request->Password),
            "Description" => "dummy",
            "ContactInfo" => $request->ContactInfo,
            "ProfilePhoto" => "Dummy",
        ]);
        return $this->success('', "Team " . $team->TeamName . " created successfully!");
    }
}
