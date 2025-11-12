<?php

namespace App\Actions\Actions\V1\Password;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class ChangePassword
{
    use AsAction;

    /**
     * Изменение пароля для аутентифицированного пользователя
     *
     * @param  User  $user  Пользователь, который меняет пароль
     * @param  array  $data  Данные с текущим и новым паролем
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function handle(User $user, array $data): array
    {
        // Проверяем текущий пароль
        if (! Hash::check($data['current_password'], $user->password)) {
            return [
                'success' => false,
                'error' => 'current_password_invalid',
            ];
        }

        // Обновляем пароль
        $user->password = Hash::make($data['password']);
        $user->save();

        return [
            'success' => true,
            'error' => null,
        ];
    }
}
