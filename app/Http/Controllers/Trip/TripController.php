<?php

namespace App\Http\Controllers\Trip;

use App\Http\Controllers\Controller;
use App\Http\Resources\tripResource;
use App\Http\Resources\userResource;
use App\Models\ReserveTrip;
use App\Models\Retrieve;
use App\Models\Trip;
use App\Models\TripRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Traits\Response;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class TripController extends Controller
{
    use Response;
    public function addTrip(Request $request)
    {
        if ($request->Retrieve == 'true') {
            $validator = Validator::make($request->all(), [
                'Title' => 'required | string',
                'Location' => 'required | string',
                'StartDate' => 'required|date|before_or_equal:EndDate',
                'EndDate' => 'required|date|after_or_equal:StartDate',
                'StartBooking' => 'required|date|before_or_equal:EndBooking',
                'EndBooking' => 'required|date|after_or_equal:StartBooking|before_or_equal:StartDate',
                'Type' => 'required | string',
                'Level' => 'required | string',
                'SubLimit' => 'required | integer',
                'Cost' => 'required | integer',
                'Description' => 'required | String',
                'Requirements' => 'String',
                'TripPhoto' => 'required',
                'RetrieveEndDate' => 'required | date ',
                'Percent' => 'required | integer | min:1 | max:100',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'Title' => 'required | string',
                'Location' => 'required | string',
                'StartDate' => 'required|date|before_or_equal:EndDate',
                'EndDate' => 'required|date|after_or_equal:StartDate',
                'StartBooking' => 'required|date|before_or_equal:EndBooking',
                'EndBooking' => 'required|date|after_or_equal:StartBooking|before_or_equal:StartDate',
                'Type' => 'required | string',
                'Level' => 'required | string',
                'SubLimit' => 'required | integer',
                'Cost' => 'required | integer',
                'Description' => 'required | String',
                'Requirements' => 'String',
                'TripPhoto' => 'required',
            ]);
        }

        if (Trip::where('Title', $request->Title)->first())
            return $this->error('', $request->Title . " is already added!", 401);

        if ($validator->fails()) {
            return $this->error('', $validator->errors()->first(), 401);
        }

        $trip = new Trip();
        $trip->TeamId = auth()->user()->id;
        $trip->Title = $request->Title;
        $trip->Location = $request->Location;
        $trip->StartDate = $request->StartDate;
        $trip->EndDate = $request->EndDate;
        $trip->StartBooking = $request->StartBooking;
        $trip->EndBooking = $request->EndBooking;
        $trip->Type = $request->Type;
        $trip->Level = $request->Level;
        $trip->SubLimit = $request->SubLimit;
        $trip->Cost = $request->Cost;
        $trip->Description = $request->Description;
        $trip->Requirements = $request->Requirements;
        $trip->Rate = 0;

        $retrieveValue = ($request->Retrieve == 'true') ? 'true' : 'false';
        $trip->Retrieve = $retrieveValue;

        $trip->TripPhoto = $request->TripPhoto;

        $trip->save();

        if ($trip->Retrieve) {

            $EndDate = isset($request->RetrieveEndDate) ? $request->RetrieveEndDate : now();
            $Percent = isset($request->Percent) ? $request->Percent : 100;

            $retrieveData = [
                'TripId' => $trip->id,
                'EndDate' => $EndDate,
                'Percent' => $Percent,
            ];
            $retrieve = Retrieve::create($retrieveData);
            $trip->retrieve()->save($retrieve);
        } else
            $trip->retrieve()->delete();

        return $this->success([
            'Trip' => new tripResource($trip)
        ], $request->Title . ' added!');
    }


    public function getTrip($tripId)
    {
        if (!filter_var($tripId, FILTER_VALIDATE_INT)) {
            return $this->error('', 'Invalid trip ID format', 400);
        }

        $trip = Trip::where('id', $tripId)->first();
        if ($trip) {
            if (Route::currentRouteNamed('user.getTrip')) {
                if (DB::table('reserve_trips')->where([['UserId', auth()->id()], ['TripId', $tripId]])->exists())
                    return response()->json([
                        'status' => true,
                        'message' => 'Trip was found',
                        'data' => new tripResource($trip),
                        'isReserved' => true,
                    ], 200);
            }
            return $this->success(new tripResource($trip), 'Trip was found');
        } else
            return $this->error('', 'Trip id not found', 402);
    }

    public function showTrips($type, $internalCall = false)
    {
        $trips = [];
        switch ($type) {
            case "all":
                $trips = Trip::all();
                break;
            case "tour":
                $trips = Trip::where("Type", "Tour")->get();
                break;
            case "adventure":
                $trips = Trip::where("Type", "Adventure")->get();
                break;
            case "cultural":
                $trips = Trip::where("Type", "Cultural")->get();
                break;
            case "excursions":
                $trips = Trip::where("Type", "Excursions")->get();
                break;
            case "leisure":
                $trips = Trip::where("Type", "Leisure")->get();
                break;
            case "easy":
                $trips = Trip::where("Level", "Easy")->get();
                break;
            case "medium":
                $trips = Trip::where("Level", "Medium")->get();
                break;
            case "hard":
                $trips = Trip::where("Level", "Hard")->get();
                break;
            default:
                return $this->error('', "wrong type entered", 401);
                break;
        }
        if ($internalCall)
            return $trips;
        else
        if ($trips->isEmpty())
            return $this->success('', 'No trips for now');
        return $this->success(tripResource::collection($trips), 'All trips in app');
    }

    public function cancelTrip($tripId)
    {

        if (!filter_var($tripId, FILTER_VALIDATE_INT)) {
            return $this->error('', 'Invalid trip ID format', 400);
        }

        $trip = Trip::where('id', $tripId)->first();
        if (!$trip = Trip::find($tripId))
            return $this->error('', 'Trip was not found, wrong given id', 402);
        $status = $trip->Status;
        switch ($status) {
            case "Cancelled":
                return $this->error('', 'Trips already cancelled', 401);
            case "Done":
                return $this->error('', 'Trips already finished you can\'t cancel it', 401);
            case "Running":
                $title = $trip->Title;
                $usersInTrip = ReserveTrip::where('TripId', $tripId)->get();
                foreach ($usersInTrip as $userInTrip) {
                    $user = User::find($userInTrip->UserId);
                    $user->Wallet = $user->Wallet + ($userInTrip->Count * $trip->Cost);
                }
                $trip->Status = "Cancelled"; //needs error handling
                $trip->save();
                return $this->success('', $title . ' is cancelled');
        }
    }



    public function rateTrip(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'TripId' => 'required|integer',
                'Value' => 'required|numeric|min:1|max:5',
                'Review' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error('', $validator->errors()->first(), 401);
            }

            $trip = Trip::find($request->TripId);
            if (!$trip) {
                return $this->error('', 'Trip not found!', 401);
            }

            $reservation = ReserveTrip::where([
                ['UserId', Auth::id()],
                ['TripId', $request->TripId]
            ])->first();

            if (!$reservation) {
                return $this->error('', 'You must reserve or go to the trip before rating it.', 403);
            }

            $existingRating = TripRate::where('ReserveTripId', $reservation->id)->first();

            if ($existingRating) {
                $existingRating->Value = $request->Value;
                $existingRating->Review = $request->Review;
                $existingRating->save();
            } else {
                TripRate::create([
                    'ReserveTripId' => $reservation->id,
                    'Value' => $request->Value,
                    'Review' => $request->Review
                ]);
            }

            $averageRating = TripRate::join('reserve_trips', 'trip_rates.ReserveTripId', '=', 'reserve_trips.id')
                                     ->where('reserve_trips.TripId', $request->TripId)
                                     ->avg('trip_rates.Value');

            $trip->Rate = $averageRating;
            $trip->save();

            return $this->success([
                'newRate' => $this->formatFloat($trip->Rate),
            ], 'Rating updated successfully');

        } catch (\Exception $exception) {
            return response()->json([
                'Message' => $exception->getMessage(),
            ]);
        }
    }
    //need logic for reserved users to not reserve again
    public function reserveTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TripId' => 'required|integer',
            'Count' => 'required|integer',
            'Names' => 'nullable|string',
        ]);

        if ($validator->fails()){
            return response()->json(['Message' => $validator->errors()->count() > 0], 401);
        }

        $namesString = $request->input('Names');
        $namesArray = array_filter(array_map('trim', explode(',', $namesString)));

        if (count($namesArray) !== (int) $request->input('Count')) {
            return response()->json(['Message' => 'The number of names must match the count of people coming.'], 401);
        }

        $user = auth()->user();

        $trip = Trip::find($request->TripId);
        $team = Team::find($trip->TeamId);

        if (!$trip) {
            return response()->json(['message' => 'Trip not found'], 404);
        }

        if ($trip->Status != 'Running') {
            return response()->json([
                'Message' => "Reservation is not allowed for this trip as it is not currently running.",
            ], 403);
        }

        $totalCost = $trip->Cost * $request->Count;

        $walletBalance = $user->Wallet;

        if ($walletBalance < $totalCost) {
            return response()->json([
                'Message' => "You do not have enough money in your wallet.",
            ], 402);
        }

        if (now()->between($trip->StartBooking, $trip->EndBooking)) {
            $existingReservation = ReserveTrip::where('TripId', $request->TripId)
                                              ->where('UserId', $user->id)
                                              ->first();

            if ($existingReservation) {
                return response()->json([
                    'Message' => "You have already reserved to this trip.",
                ], 409);
            }

            $reserveTrip = new ReserveTrip();
            $reserveTrip->UserId = $user->id;
            $reserveTrip->TripId = $request->TripId;
            $reserveTrip->Count = $request->Count;

            $reserveTrip->save();

            foreach ($namesArray as $name) {
                $contestant = new countestantInfo();
                $contestant->reserveTripsId = $reserveTrip->id;
                $contestant->name = $name;
                $contestant->isAttended = true;
                $contestant->save();
            }

            $user->Wallet -= $totalCost;
            $user->save();

            $team->Wallet += $totalCost;
            $team->save();

            return response()->json([
                'Message' => "Reservation Completed!",
                'Reservation' => $reserveTrip,
                'Names' => $namesArray,
                'Wallet After Reservation' => $user->Wallet,
            ]);
        } else {
            return response()->json([
                'Message' => "Reservation is out of the booking window.",
            ], 400);
        }
    }

    public function cancelReservationInfo(Request $request)
    {
        $currentDate = Carbon::now();

        $validator = Validator::make($request->all(), [
            'TripId' => 'required | integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'Message' => $validator->errors()->first()
            ], 401);
        }

        $trip = Trip::find($request->TripId);

        if ($trip != null) {
            $reserveInfo = DB::table('reserve_trips')->where([
                ['UserId', auth()->id()],
                ['TripId', $trip->id],
            ])->first();
            if ($reserveInfo != null) {
                $uniqueKey = Str::random(16);
                $totalAmount = $trip->Cost * $reserveInfo->Count;
                $paybackAmount = 0;
                //case #1: no retrieve
                if ($trip->Retrieve == 'false') {
                    $dataToStore = [$trip->id, $totalAmount, $paybackAmount];
                    Cache::put($uniqueKey, $dataToStore, now()->addMinutes(10));
                    return $this->success([
                        'totalAmount' => $totalAmount,
                        'paybackAmount' => $paybackAmount,
                        'generatedKey' => $uniqueKey
                    ], 'Reservation will be cancelled without payback due to trip info, would you like to continue?');
                }
                $retrieveInfo = DB::table('retrieve_for_trips')->where('TripId', $trip->id)->first();
                $deadlineString = $retrieveInfo->EndDate;
                $percentRetrieving = $retrieveInfo->Percent;
                $deadline = Carbon::parse($deadlineString);

                //Case #2: End date has passed, no retrieve
                if ($currentDate->gt($deadline)) {
                    $dataToStore = [$trip->id, $totalAmount, $paybackAmount];
                    Cache::put($uniqueKey, $dataToStore, now()->addMinutes(10));
                    return $this->success([
                        'totalAmount' => $totalAmount,
                        'paybackAmount' => $paybackAmount,
                        'generatedKey' => $uniqueKey,
                    ], 'Payback date has ended due to trip info so no cash will be retrived, would you like to continue?');
                }
                $paybackAmount = ($trip->Cost * $reserveInfo->Count) * $percentRetrieving / 100;
                $dataToStore = [$trip->id, $totalAmount, $paybackAmount];
                Cache::put($uniqueKey, $dataToStore, now()->addMinutes(10));
                //Case #3: End date hasn't passed yet, there is retrieve
                return $this->success([
                    'totalAmount' => $totalAmount,
                    'paybackAmount' => $paybackAmount,
                    'generatedKey' => $uniqueKey,
                ], 'You will get ' . $percentRetrieving . '% from the total amount you payed, would you like to continue?');
            } else
                return $this->error('', 'User not subscribed to this trip', 402);
        } else
            return $this->error('', 'Invalid trip id', 402);
    }

    public function confirmCancellation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'generatedKey' => 'required | string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Message' => $validator->errors()->first()
            ], 400);
        }
        $data = Cache::get($request->generatedKey);
        Cache::forget($request->generatedKey);
        if (!$data)
            return $this->error('', 'The key you\'ve sent is Invalid or got deleted', 402);
        $tripId = $data[0];
        $totalAmount = $data[1];
        $paybackAmount = $data[2];

        $team = Trip::find($tripId)->team;
        $team->Wallet = $team->Wallet - $paybackAmount;
        auth()->user()->Wallet = auth()->user()->Wallet + $paybackAmount + 10000;
        auth()->user()->save();
        $team->save();
        DB::table('reserve_trips')->where([
            ['UserId', auth()->id()],
            ['TripId', $tripId],
        ])->delete();
        return $this->success([
            "userWallet" => auth()->user()->Wallet
        ], 'Booking has been cancelled');
    }

    // this function return the first one digits after . in float numbers since we need the whole digits in database so we can calculate the rate accurately, but in app one digits after . is enough
    function formatFloat($number)
    {
        if (!is_float($number))
            return $number;
        $decimalPlaces = strlen(substr(strrchr($number, "."), 1));
        return number_format($number, min(1, $decimalPlaces));
    }
    function getTeamTrips()
    {

        $teamTrips = Trip::where('TeamId', auth()->user()->id)->get();
        $numberOfTeamTrips = $teamTrips->count();
        if ($numberOfTeamTrips == 0)
            return $this->success('', 'No trips added from this team');
        return $this->success(tripResource::collection($teamTrips), 'Sent ' . $numberOfTeamTrips . ' trips');
    }
    function showCountestantsInTrip($tripId)
    {
        if (!is_int($tripId)) {
            abort(400, 'Invalid team ID provided.');
        }
        $trip = Trip::where('id', $tripId)->first();
        if ($trip->TeamId == Auth::id()) {
            $usersIds = DB::table('reserve_trips')->where('TripId', $tripId)->get();
            foreach ($usersIds as $userId) {
                $user = User::find($userId);
                $users[] =  new userResource($user);
            }
            return $this->success([
                'Countestants' => $users,
            ]);
        }
        return $this->error('', 'This trip doesn\'t belong to the given team\'s id', 401);
    }

    //this function should be called at the first day of the trip started so we can calculate how many ppl are in the trip right now (for an orgnize purpose to the team)
    function checklist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TripId' => 'required | integer',
            'ListOfCountestants' => 'required | array',
            'TotalNumberOfCountestants' => 'required | integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Message' => $validator->errors()->first()
            ], 400);
        }
        $ListOfCountestants  = $request->ListOfCountestants;

        foreach ($ListOfCountestants as $ListOfCountestant) {
        }
        return $request->TotalNumberOfCountestants;
    }
    function getUpCommingTrips()
    {

        $reserveTrips = DB::table("reserve_trips")->where("UserId", auth()->id())->get();
        if ($reserveTrips->isEmpty())
            return $this->success('', 'User didn\'t join a trip yet');
        $trips[] = [];
        foreach ($reserveTrips as $reserveTrip) {
            $trip = Trip::find($reserveTrip->TripId);
            if ($trip->Status == "Opened")
                $trips[] = new tripResource($trip);
        }

        return $this->success($trips, 'Up Comming trips"');
    }
}
