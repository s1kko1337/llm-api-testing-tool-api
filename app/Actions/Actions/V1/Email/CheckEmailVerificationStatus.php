<?php

namespace App\Actions\Actions\V1\Email;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckEmailVerificationStatus
{
    use AsAction;

    /**
     * Проверка статуса верификации email пользователя
     *
     * @param  User  $user  Пользователь для проверки
     * @return array Данные о статусе верификации
     */
    public function handle(User $user): array
    {
        $isVerified = $user->hasVerifiedEmail();

        return [
            'message' => $isVerified
                ? 'Email уже подтвержден.'
                : 'Email требует подтверждения.',
            'verified' => $isVerified,
            'email' => $user->email,
        ];
    }
}
