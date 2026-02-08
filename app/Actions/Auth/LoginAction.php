<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Handle the login action.
     *
     * @param  array{email: string, password: string, remember?: bool}  $data
     * @return array{user: User, requiresTwoFactor: bool}
     *
     * @throws ValidationException
     */
    public function handle(array $data): array
    {
        $this->ensureIsNotRateLimited($data['email']);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey($data['email']));

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($data['email']));

        if ($user->hasEnabledTwoFactorAuthentication()) {
            session()->put('login.id', $user->getKey());
            session()->put('login.remember', $data['remember'] ?? false);

            return ['user' => $user, 'requiresTwoFactor' => true];
        }

        Auth::login($user, $data['remember'] ?? false);
        session()->regenerate();

        return ['user' => $user, 'requiresTwoFactor' => false];
    }

    /**
     * Ensure the login request is not rate limited.
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
        return Str::transliterate(Str::lower($email).'|'.request()->ip());
    }
}
