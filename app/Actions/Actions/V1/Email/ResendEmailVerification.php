<?php

namespace App\Actions\Actions\V1\Email;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendEmailVerification
{
    use AsAction;

    /**
     * Повторная отправка письма для подтверждения email
     *
     * @param  User  $user  Пользователь для отправки письма
     * @return array Результат отправки
     */
    public function handle(User $user): array
    {
        // Если email уже подтвержден
        if ($user->hasVerifiedEmail()) {
            return [
                'message' => 'Email уже подтвержден.',
                'verified' => true,
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'message' => 'Ссылка для подтверждения отправлена.',
            'email' => $user->email,
        ];
    }
}
