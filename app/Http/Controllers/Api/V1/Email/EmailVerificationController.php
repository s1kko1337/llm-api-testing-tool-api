<?php

namespace App\Http\Controllers\Api\V1\Email;

use App\Actions\Actions\V1\Email\CheckEmailVerificationStatus;
use App\Actions\Actions\V1\Email\ResendEmailVerification;
use App\Actions\Actions\V1\Email\VerifyEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Email\VerifyEmailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Показать статус верификации email.
     */
    public function notice(Request $request): JsonResponse
    {
        $result = CheckEmailVerificationStatus::run(Auth::user());

        return response()->json($result);
    }

    /**
     * Подтвердить email пользователя.
     */
    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        try {
            $result = VerifyEmail::run($request->user());

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Произошла ошибка при подтверждении email.',
                'verified' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Повторно отправить письмо для подтверждения email.
     */
    public function resend(): JsonResponse
    {
        $result = ResendEmailVerification::run(Auth::user());

        return response()->json($result);
    }
}
