<?php

use App\Actions\Auth\ConfirmPasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('confirm password action confirms password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user);

    $action = new ConfirmPasswordAction;

    $action->handle($user, ['password' => 'password123']);

    expect(session()->get('auth.password_confirmed_at'))->not->toBeNull();
});

test('confirm password action fails with incorrect password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user);

    $action = new ConfirmPasswordAction;

    $action->handle($user, ['password' => 'wrong-password']);
})->throws(ValidationException::class);
