<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ResendVerificationNotificationAction
{
    /**
     * Handle the resend verification notification action.
     *
     * @return array{sent: bool, alreadyVerified: bool}
     */
    public function handle(User $user): array
    {
        if (! ($user instanceof MustVerifyEmail)) {
            return ['sent' => false, 'alreadyVerified' => true];
        }

        if ($user->hasVerifiedEmail()) {
            return ['sent' => false, 'alreadyVerified' => true];
        }

        $user->sendEmailVerificationNotification();

        return ['sent' => true, 'alreadyVerified' => false];
    }
}
