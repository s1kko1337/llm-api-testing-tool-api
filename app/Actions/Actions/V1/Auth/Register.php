<?php

namespace App\Actions\Actions\V1\Auth;

use App\Enums\Roles;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class Register
{
    use AsAction;

    /**
     * Создание нового пользоваеля с ролью "user"
     *
     * @param array $data
     * @return array ['user' => User, 'token' => string]
     */
    public function handle(array $data): array
    {
        $user = User::create($data);
        $user->assignRole(Roles::USER_ROLE);

        // event(new Registered($user));

        $token = $user->createToken("Token of user: {$user->name}")->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
