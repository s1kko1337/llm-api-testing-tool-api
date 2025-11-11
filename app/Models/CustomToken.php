<?php

namespace App\Models;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class CustomToken extends SanctumPersonalAccessToken
{
    public $table = 'personal_access_tokens';

    public function can($ability)
    {
        if (!$this->tokenable->can($ability)) {
            return false;
        }

        $abilities = collect($this->abilities)->filter(function ($ability) {
            return $ability !== '*';
        })->toArray();

        if (count($abilities) > 0) {
            return $this->canDb($abilities);
        }

        return true;
    }

        protected function canDb($ability): bool
    {
        return array_key_exists($ability, array_flip($this->abilities));
    }
}
