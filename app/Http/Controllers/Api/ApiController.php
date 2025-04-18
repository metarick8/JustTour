<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\teamResource;
use App\Http\Resources\tripResource;
use App\Http\Resources\userResource;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserTeam;
use App\Traits\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class ApiController extends Controller
{
    use Response;

    public function login(Request $request)
    {
        $request->validate([
            "Email" => "required|email|string",
            "Password" => "required|string"
        ]);

        $user = User::where('Email', $request->Email)->first();
        $isUser = $user !== null;
        $team = Team::where('Email', $request->Email)->first();
        $isTeam = $team !== null;

        if ($isUser) {
            if (Hash::check($request->Password, $user->Password)) {
                $token = JWTAuth::fromUser($user);

                return response()->json([
                    "status" => true,
                    "message" => "User $user->FirstName Logged in successsfully!",
                    "data" => new userResource($user),
                    "token" => $token,
                    "isUser" => true
                ]);
            }
        }

        if ($isTeam) {
            if (Hash::check($request->Password, $team->Password)) {
                $token = JWTAuth::fromUser($team);
                return response()->json([
                    "status" => true,
                    "message" => "Team $team->TeamName Logged in successsfully!",
                    "data" => new teamResource($team),
                    "token" => $token,
                    "isTeam" => true
                ]);
            }
        }
        return $this->error('', 'Invalid login credentials', 401);
    }

    public function profile()
    {
        $user_Data = JWTAuth::user();
        return $this->success(new userResource($user_Data), '');
    }

    public function refreshToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();
        return response()->json([
            "Status" => true,
            "Message" => "New Access Token Generated",
            "Token" => $newToken
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return $this->success('', 'Logged out successfully');
    }

    public function followTeam($teamId)
    {
        if (!filter_var($teamId, FILTER_VALIDATE_INT)) {
            return $this->error('', 'Invalid trip ID format', 400);
        }

        if (!Team::where('id', $teamId)->exists())
            return $this->error('', 'the team id does not exist', 402);
        if (DB::table('user_team')->where([['UserId', Auth::id()], ['TeamId', $teamId]])->exists()) {
            $this->unfollowTeam($teamId);
            return $this->success('', 'Unfollowed team successfully');
        }
        UserTeam::create([
            'UserId' => Auth::id(),
            'TeamId' => $teamId,
        ]);
        return $this->success('', 'Followed team successfully');
    }

    public function unfollowTeam($teamId)
    {
        if (!filter_var($teamId, FILTER_VALIDATE_INT)) {
            return $this->error('', 'Invalid trip ID format', 400);
        }

        if (!Team::where('id', $teamId)->exists())
            return $this->error('', 'the team id does not exist', 402);
        DB::table('user_team')->where([['UserId', Auth::id()], ['TeamId', $teamId]])->delete();
        return $this->success('', 'Unfollowed team successfully');

    }

    public function getFollowingTeams()
    {
        $followedTeams = UserTeam::where('UserId', Auth::id())->get('TeamId');
        if ($followedTeams->isEmpty())
            return $this->success([
                'followedTeams' => []
            ], 'User haven\'t followed any team yet');
        foreach ($followedTeams as $followedTeam) {
            $teamId = $followedTeam->TeamId;
            $team = Team::where('id', $teamId)->first();
            $result[] = new teamResource($team);
        }
        return $this->success([
            'followedTeams' => $result
        ]);
    }

    function log()
    {
        $trips = DB::table('reserve_trips')
            ->join('trips', 'reserve_trips.TripId', '=', 'trips.id')
            ->where('reserve_trips.UserId', Auth::id())
            ->orderBy('trips.StartDate', 'desc')
            ->get();

        return $this->success(tripResource::collection($trips));
    }
}
