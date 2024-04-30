<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    //Register Api (POST , formdata)
    public function register(Request $request)
    {
        //Data validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        //Date save
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        //Response
        return response()->json([
            "Status" => true,
            "Message" => "User created successfully!"
        ]);
    }

    //Login Api (POST , formdata)
    public function login(Request $request)
    {
        //Data validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        //JWTAuth and attempt
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if(!empty($token)){
            //Response
            return response()->json([
                "Status" => true,
                "Message" => "User Logged in successsfully!",
                "Token" => $token
            ]);
        }

        return response()->json([
            "Status" => false,
            "Message" => "Invalid Login Details"
        ]);
    }

    //Profile Api (GET)
    public function profile()
    {
        $user_Data = auth()->user();

        return response()->json([
            "Status" => true,
            "Message" => "Profile Data",
            "User" => $user_Data
        ]);
    }

    //Refresh Token Api (GET)
    public function refreshToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();

        return response()->json([
            "Status" => true,
            "Message" => "New Access Token Generated",
            "Token" => $newToken
        ]);
    }

    //Logout Api (GET)
    public function logout()
    {
        auth()->logout();

        return response()->json([
            "Status" => true,
            "Message" => "User Logged out successfully"
        ]);
    }
}
