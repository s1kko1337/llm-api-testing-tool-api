<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Actions\V1\Auth\Login;
use App\Actions\Actions\V1\Auth\Logout;
use App\Actions\Actions\V1\Auth\Register;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Http\Requests\Api\V1\Auth\StoreUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request): JsonResponse
    {
        $result = Register::run($request->validated());

        return response()->json([
            ...(new UserResource($result['user']))->resolve(),
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $result = Login::run($request->validated());

        if ($result === null) {
            return response()->json([
                'errors' => 'Wrong email or password',
            ], 401);
        }

        return response()->json([
            ...(new UserResource($result['user']))->resolve(),
            'token' => $result['token'],
        ], 200);
    }

    public function logout(): JsonResponse
    {
        Logout::run(Auth::user());

        return response()->json([
            'message' => 'Logged out, token removed',
        ]);
    }
}
