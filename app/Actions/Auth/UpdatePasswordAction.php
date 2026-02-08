<?php

namespace App\Actions\Auth;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdatePasswordAction
{
    use PasswordValidationRules;

    /**
     * Handle the password update action.
     *
     * @param  array{current_password: string, password: string, password_confirmation: string}  $data
     *
     * @throws ValidationException
     */
    public function handle(User $user, array $data): void
    {
        $this->ensureIsNotRateLimited($user);

        $validator = Validator::make($data, [
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ]);

        if ($validator->fails()) {
            // Hit the rate limiter on failed validation (wrong password)
            RateLimiter::hit($this->throttleKey($user));

            throw new ValidationException($validator);
        }

        RateLimiter::clear($this->throttleKey($user));

        $user->update([
            'password' => $data['password'],
        ]);
    }

    /**
     * Ensure the request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(User $user): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($user), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($user));

        throw ValidationException::withMessages([
            'current_password' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(User $user): string
    {
        return 'update-password|'.Str::transliterate($user->id.'|'.request()->ip());
    }
}
