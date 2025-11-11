<?php

namespace App\Actions\Actions\V1\Auth;

use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class Login
{
    use AsAction;

    /**
     * Попытка аунтефикации и предоставления токена пользователю с гарантией единственноей сессии
     *
     * @param array $credentials
     * @return array|null ['user' => User, 'token' => string]
     */
    public function handle(array $credentials): ?array
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        $user->tokens()->delete();

        $token = $user->createToken("Token of user: {$user->name}")->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
