<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\userResource;
use App\Traits\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRegisterController extends Controller
{
    public function userRegister(Request $request)
    {
        $request->validate([
            "FirstName" => "required|string",
            "LastName" => "required|string",
            "Email" => "required|email|unique:users",
            "Password" => "required|confirmed|string|min:6",
            "Number" => "required|string",
            "Age" => "required|numeric"
        ]);

        $user = User::create([
            "FirstName" => $request->FirstName,
            "LastName" => $request->LastName,
            "Email" => $request->Email,
            "Password" => Hash::make($request->Password),
            "Number" => $request->Number,
            "Age" => $request->Age,
            "Wallet" => 0
        ]);

        return response()->json([
            "Status" => true,
            "Message" => "User created successfully!",
            'Token' => JWTAuth::fromUser($user)
        ]);
    }

    public function editUserProfile(Request $request)
    {
        $request->validate([
            'FirstName' => 'nullable|string|max:255',
            'LastName' => 'nullable|string|max:255',
            'Number' => 'nullable|string',
            'Password' => 'nullable|string|min:6',
        ]);

        $user = auth()->user();

        if ($request->FirstName) {
            $user->FirstName = $request->FirstName;
        }

        if ($request->LastName) {
            $user->LastName = $request->LastName;
        }

        if ($request->Number) {
            $user->Number = $request->Number;
        }

        if ($request->Password) {
            $user->Password = Hash::make($request->Password);
        }

        $user->save();
        return response()->json([
            'Message' => 'User profile updated successfully',
            'User' => $user
        ]);
    }
    use Response;
    public function showUsers()
    {
        $users = User::all();
        if ($users->isEmpty())
            return $this->success('', "No users in the app yet");

        return $this->success(UserResource::collection($users), "All users in the app:");
        //     $response = [
        //         'data' => UserResource::collection($users),
        //         'meta' => [
        //             'total' => $users->count(),
        //         ],
        //     ];
        //     return $response;
    }
}
