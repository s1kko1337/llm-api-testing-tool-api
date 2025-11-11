<?php

namespace App\Actions\Actions\V1\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class Logout
{
    use AsAction;

    /**
     * Выход из приложения, удаление текущего токена
     */
    public function handle(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
