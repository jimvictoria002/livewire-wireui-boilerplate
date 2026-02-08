<?php

namespace App\Actions\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordAction
{
    /**
     * Handle the password reset action.
     *
     * @param  array{email: string, password: string, password_confirmation: string, token: string}  $data
     */
    public function handle(array $data): string
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function ($user, $password) {
                $user
                    ->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])
                    ->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }
}
