<?php

namespace App\Actions\Actions\V1\Password;

use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

class SendPasswordResetLink
{
    use AsAction;

    /**
     * Отправка ссылки для сброса пароля на email пользователя
     *
     * @param  array  $data  Данные с email пользователя
     * @return array ['success' => bool, 'status' => string]
     */
    public function handle(array $data): array
    {
        $status = Password::sendResetLink(
            ['email' => $data['email']]
        );

        return [
            'success' => $status === Password::RESET_LINK_SENT,
            'status' => $status,
        ];
    }
}
