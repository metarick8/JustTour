<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\teamResource;
use App\Http\Resources\teamWithTripsResource;
use App\Http\Resources\userResource;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\UserTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TeamAccountsController extends Controller
{
    use Response;

    public function editTeamProfile(Request $request)
    {
        $request->validate([
            "ContactInfo" => "nullable|string|max:255",
            "Description" => "nullable|string|max:255",
        ]);

        $team = auth()->user();

        if ($request->ContactInfo) {
            $team->ContactInfo = $request->ContactInfo;
        }

        if ($request->Description) {
            $team->Description = $request->Description;
        }

        $team->save();

        return response()->json([
            'Message' => 'Team profile updated successfully',
            'Team' => new teamResource($team),
        ]);
    }
    public function showTeams()
    {
        $teams = Team::all();
        return $this->success(teamResource::collection($teams), 'all teams shown');
    }

    public function showTeam($teamId)
    {

        if (!filter_var($teamId, FILTER_VALIDATE_INT)) {
            return $this->error('', 'Invalid team ID format', 400);
        }
        $team = Team::where('id', $teamId)->first();
        if ($team) {
            if (Route::currentRouteNamed('user.showTeam')) {
                if (DB::table('user_team')->where([['UserId', auth()->id()], ['TeamId', $teamId]])->exists())
                    return response()->json([
                        'status' => true,
                        'message' => 'Team was found',
                        'data' => new teamResource($team),
                        'isFollowed' => true,
                    ], 200);
            } else
                return $this->success(new teamWithTripsResource($team), 'Team was found');
        } else
            return $this->error('', 'Invalid team ID', 402);
    }
    public function getFollowers()
    {
        $followers = UserTeam::where('TeamId', Auth::id())->get();
        if ($followers->isEmpty())
            return $this->success('', 'you don\'t have any followers yet');
        else {
            $userIds = $followers->pluck('UserId');
            $usersDetails = User::whereIn('id', $userIds)->get();
            return $this->success(UserResource::collection($usersDetails));
        }
    }
}
