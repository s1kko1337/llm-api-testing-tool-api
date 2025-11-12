<?php

namespace App\Actions\Actions\V1\Password;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPassword
{
    use AsAction;

    /**
     * Сброс пароля пользователя по токену с установкой нового пароля
     *
     * @param  array  $data  Данные с email, токеном и новым паролем
     * @return array ['success' => bool, 'status' => string]
     */
    public function handle(array $data): array
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            static function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return [
            'success' => $status === Password::PASSWORD_RESET,
            'status' => $status,
        ];
    }
}
