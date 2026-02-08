<?php

use App\Actions\Auth\ResetPasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

test('reset password action resets user password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::createToken($user);

    $action = new ResetPasswordAction;

    $status = $action->handle([
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'token' => $token,
    ]);

    expect($status)->toBe(Password::PASSWORD_RESET);
    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('reset password action fails with invalid token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $action = new ResetPasswordAction;

    $status = $action->handle([
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'token' => 'invalid-token',
    ]);

    expect($status)->toBe(Password::INVALID_TOKEN);
    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});
