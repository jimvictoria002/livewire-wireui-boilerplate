<?php

namespace App\Actions\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendPasswordResetLinkAction
{
    /**
     * Handle sending the password reset link.
     *
     * @param  array{email: string}  $data
     *
     * @throws ValidationException
     */
    public function handle(array $data): string
    {
        $this->ensureIsNotRateLimited($data['email']);

        $status = Password::sendResetLink(['email' => $data['email']]);

        RateLimiter::hit($this->throttleKey($data['email']));

        return $status;
    }

    /**
     * Ensure the request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(string $email): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(string $email): string
    {
        return 'password-reset|'.Str::transliterate(Str::lower($email).'|'.request()->ip());
    }
}
