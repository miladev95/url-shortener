<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Miladev\ApiResponse\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse(data: ['token' => $token],statusCode: 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->failResponse(message: 'Invalid credentials', statusCode: 401);
        }

        $user = User::where('email', $request->input('email'))->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse(data: ['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(message: 'Logged out');
    }
}
