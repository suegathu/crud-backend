<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        // If using Laravel Sanctum for API authentication
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'Login Successful', 'token' => $token, 'user' => $user]);
        }
   
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            "status" => true,
            "message" => "User profile retrieved successfully",
            "user" => $user
        ]);
    }
    public function logout()
    {
       Auth::logout();


        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully']);
    }
}