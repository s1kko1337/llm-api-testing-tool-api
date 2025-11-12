<?php

namespace App\Http\Controllers\Api\V1\Password;

use App\Actions\Actions\V1\Password\ChangePassword;
use App\Actions\Actions\V1\Password\ResetPassword;
use App\Actions\Actions\V1\Password\ResetPasswordByToken;
use App\Actions\Actions\V1\Password\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Password\PasswordChangeRequest;
use App\Http\Requests\Api\V1\Password\PasswordResetRequest;
use App\Http\Requests\Api\V1\Password\PasswordRevokeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{
    /**
     * Отправка ссылки для сброса пароля на email пользователя
     */
    public function revoke(PasswordRevokeRequest $request): JsonResponse
    {
        $result = SendPasswordResetLink::run($request->validated());

        return $result['success']
            ? response()->json([
                'message' => 'Password reset link sent',
                'status' => $result['status'],
            ], 200)
            : response()->json([
                'message' => 'Failed to send password reset link',
                'error' => $result['status'],
            ], 500);
    }

    /**
     * Автоматический сброс пароля по токену с генерацией нового пароля и отправкой на email
     *
     * Этот метод использует стандартный механизм Laravel Password для валидации токена
     * и автоматически генерирует новый пароль, который отправляется на email пользователя
     *
     * @param  string  $token  Токен для сброса пароля
     */
    public function invoke(string $token): JsonResponse
    {
        $result = ResetPasswordByToken::run($token);

        return $result['success']
            ? response()->json([
                'message' => 'Пароль успешно сброшен. Новый пароль отправлен на вашу почту.',
                'status' => $result['status'],
            ], 200)
            : response()->json([
                'message' => 'Ошибка при сбросе пароля',
                'error' => $result['error'],
            ], $result['status'] === 'invalid_token' ? 400 : 500);
    }

    /**
     * Сброс пароля пользователя по токену с установкой нового пароля
     *
     * Пользователь предоставляет токен, email и новый пароль для сброса
     */
    public function update(PasswordResetRequest $request): JsonResponse
    {
        $result = ResetPassword::run($request->validated());

        return $result['success']
            ? response()->json([
                'message' => 'Password updated successfully',
                'status' => $result['status'],
            ], 200)
            : response()->json([
                'message' => 'Password update error',
                'error' => $result['status'],
            ], 500);
    }

    /**
     * Изменение пароля для аутентифицированного пользователя
     *
     * Пользователь должен предоставить текущий пароль и новый пароль
     */
    public function change(PasswordChangeRequest $request): JsonResponse
    {
        $result = ChangePassword::run(Auth::user(), $request->validated());

        if (! $result['success']) {
            return response()->json([
                'message' => 'Текущий пароль указан неверно.',
                'errors' => [
                    'current_password' => ['Текущий пароль указан неверно.'],
                ],
            ], 422);
        }

        return response()->json([
            'message' => 'Пароль успешно изменен',
        ], 200);
    }
}
