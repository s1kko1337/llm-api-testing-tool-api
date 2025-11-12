<?php

namespace App\Actions\Actions\V1\Password;

use App\Mail\PasswordResetNotification;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPasswordByToken
{
    use AsAction;

    /**
     * Автоматический сброс пароля по токену с генерацией нового пароля и отправкой на email
     *
     * @param  string  $token  Токен для сброса пароля
     * @return array ['success' => bool, 'status' => string, 'error' => ?string]
     */
    public function handle(string $token): array
    {
        // Получаем все записи из таблицы password_reset_tokens для проверки хеша
        $passwordResets = DB::table('password_reset_tokens')->get();

        $validReset = null;
        foreach ($passwordResets as $reset) {
            if (Hash::check($token, $reset->token)) {
                $validReset = $reset;
                break;
            }
        }

        if (! $validReset) {
            return [
                'success' => false,
                'status' => 'invalid_token',
                'error' => 'Invalid token',
            ];
        }

        // Генерируем новый случайный пароль
        $newPassword = Str::random(12);
        $userEmail = $validReset->email;

        // Используем стандартный механизм Laravel Password::reset
        $status = Password::reset(
            [
                'email' => $userEmail,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
                'token' => $token,
            ],
            function (User $user) use ($newPassword) {
                // Обновляем пароль пользователя
                $user->forceFill([
                    'password' => Hash::make($newPassword),
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Отправляем email с новым паролем
                try {
                    Mail::to($user->email)->send(
                        new PasswordResetNotification($user->email, $newPassword)
                    );
                } catch (\Exception $e) {
                    // Логируем ошибку, но не прерываем процесс
                    logger()->error('Failed to send password reset email: '.$e->getMessage());
                }

                event(new PasswordReset($user));
            }
        );

        return [
            'success' => $status === Password::PASSWORD_RESET,
            'status' => $status,
            'error' => $status !== Password::PASSWORD_RESET ? $status : null,
        ];
    }
}
