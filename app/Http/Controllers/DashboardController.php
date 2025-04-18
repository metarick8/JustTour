<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Trip;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use Response;
    function getCount(){
        $tripsCount =  Trip::count();
        $usersCount = User::count();
        $teamsCount = Team::count();
        $allDatabaseCount[] = [["UserCount" => $usersCount],["TeamCount" => $teamsCount], ["TripCount" => $tripsCount]];
        return $this->success($allDatabaseCount, 'All database count');
    }
    //function
}
