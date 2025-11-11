<?php

namespace App\Actions\Actions\V1\Email;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyEmail
{
    use AsAction;

    /**
     * Подтверждение email адреса пользователя
     *
     * @param  User  $user  Пользователь для верификации
     * @return array Результат верификации
     *
     * @throws \Throwable
     */
    public function handle(User $user): array
    {
        try {
            if ($user->hasVerifiedEmail()) {
                return [
                    'message' => 'Email уже подтвержден.',
                    'verified' => true,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ];
            }

            $user->markEmailAsVerified();

            return [
                'message' => 'Email успешно подтвержден.',
                'verified' => true,
                'user_id' => $user->id,
                'email' => $user->email,
            ];
        } catch (\Throwable $e) {
            Log::error('Ошибка при верификации email: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            throw $e;
        }
    }
}
