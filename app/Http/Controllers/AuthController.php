<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (!$token = Auth::attempt($credentials)) {
            return $this->errorResponse('Unauthorized', 401);
        }
        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        Log::info('Registration request:', $request->validated());

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'Customer',
            ]);

            return $this->successResponse([
                'message' => 'User successfully registered',
                'user' => $user->only(['id', 'name', 'email', 'contact_number', 'role']),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error:', ['error' => $e->getMessage()]);
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function me(): JsonResponse
    {
        return $this->successResponse(['user' => Auth::user()]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->successResponse(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user(),
        ]);
    }
}
