<?php

namespace App\Actions\Api\Auth;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutUserAction
{
    /**
     * Log the user out (revoke the token).
     */
    public function handle(User $user): void
    {
        $accessToken = $user->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        }
    }
}
