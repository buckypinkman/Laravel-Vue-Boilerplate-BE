<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse {
        try {
            $payload = $request->validate([
                'username'=>'required|string',
                'password'=>'required|min:6'
            ]);

            $user = User::where('username', $payload['username'])
            ->with([
                'roles',
                'modelHasRole'
            ])
            ->first();

            if(!$user || !Hash::check($payload['password'],$user->password)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Credentials',
                    'data' => []
                ],401);
            }

            $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Success',
                'data' => ['access_token' => $token],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout Success',
                'data' => [],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => true,
                'message' => 'Logout Success',
                'data' => [],
            ]);
        }
    }
}
