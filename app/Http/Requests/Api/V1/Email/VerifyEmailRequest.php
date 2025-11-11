<?php

namespace App\Http\Requests\Api\V1\Email;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    protected ?User $userToVerify = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::findOrFail($this->route('id'));

        if (! hash_equals(
            (string) $this->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return false;
        }

        $this->userToVerify = $user;

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Получить пользователя для верификации
     *
     * @param  string|null  $guard
     * @return \App\Models\User|null
     */
    public function user($guard = null): ?User
    {
        return $this->userToVerify;
    }
}
