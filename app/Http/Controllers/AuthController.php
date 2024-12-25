<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(
            [
                'user' => new UserResource($user),
                'token' => $token,
                'message' => 'Logged in successfully!',
            ]
        );
    }

    public function logout(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'You are not logged in.'], 401); // Return a 401 Unauthorized response
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    public function validateLogin(Request $request)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        $user = $accessToken->tokenable;

        if (!$accessToken || !$user) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }
}

