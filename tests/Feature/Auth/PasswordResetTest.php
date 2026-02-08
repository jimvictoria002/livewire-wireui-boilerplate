<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));
    $response->assertOk();
});

test('password can be reset with valid token', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    expect(auth()->validate(['email' => $user->email, 'password' => 'new-password']))->toBeTrue();
});
