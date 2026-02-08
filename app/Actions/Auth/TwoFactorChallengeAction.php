<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeAction
{
    /**
     * Handle the two-factor challenge action.
     *
     * @param  array{code?: string, recovery_code?: string, use_recovery_code: bool}  $data
     *
     * @throws ValidationException
     */
    public function handle(array $data): User
    {
        $userId = session('login.id');
        $user = User::find($userId);

        if (! $user) {
            throw ValidationException::withMessages([
                'code' => __('Authentication failed.'),
            ]);
        }

        if ($data['use_recovery_code']) {
            $this->validateRecoveryCode($user, $data['recovery_code'] ?? '');
        } else {
            $this->validateTotpCode($user, $data['code'] ?? '');
        }

        // Authentication successful
        $remember = session('login.remember', false);
        Auth::login($user, $remember);

        session()->forget(['login.id', 'login.remember']);
        session()->regenerate();

        return $user;
    }

    /**
     * Validate the TOTP code.
     *
     * @throws ValidationException
     */
    protected function validateTotpCode(User $user, string $code): void
    {
        $google2fa = app(Google2FA::class);
        $secret = decrypt($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $code);

        if (! $valid) {
            throw ValidationException::withMessages([
                'code' => __('The provided code is invalid.'),
            ]);
        }
    }

    /**
     * Validate the recovery code.
     *
     * @throws ValidationException
     */
    protected function validateRecoveryCode(User $user, string $recoveryCode): void
    {
        if (! $user->two_factor_recovery_codes) {
            throw ValidationException::withMessages([
                'recovery_code' => __('Recovery code is invalid.'),
            ]);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (! in_array($recoveryCode, $recoveryCodes)) {
            throw ValidationException::withMessages([
                'recovery_code' => __('Recovery code is invalid.'),
            ]);
        }

        // Remove used recovery code
        $recoveryCodes = array_values(array_diff($recoveryCodes, [$recoveryCode]));
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();
    }
}
