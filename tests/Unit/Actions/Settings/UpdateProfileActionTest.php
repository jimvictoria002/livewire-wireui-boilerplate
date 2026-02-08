<?php

use App\Actions\Settings\UpdateProfileAction;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('update profile action updates user profile', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'email_verified_at' => now(),
    ]);

    $action = new UpdateProfileAction;

    $result = $action->handle($user, [
        'name' => 'New Name',
        'email' => 'original@example.com',
    ]);

    expect($result['emailChanged'])->toBeFalse();
    expect($user->fresh()->name)->toBe('New Name');
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('update profile action clears email verification when email changes', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'original@example.com',
        'email_verified_at' => now(),
    ]);

    $action = new UpdateProfileAction;

    $result = $action->handle($user, [
        'name' => 'Test User',
        'email' => 'new@example.com',
    ]);

    expect($result['emailChanged'])->toBeTrue();
    expect($user->fresh()->email)->toBe('new@example.com');
    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('update profile action validates required fields', function () {
    $user = User::factory()->create();

    $action = new UpdateProfileAction;

    $action->handle($user, [
        'name' => '',
        'email' => '',
    ]);
})->throws(ValidationException::class);

test('update profile action validates unique email', function () {
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $user = User::factory()->create([
        'email' => 'original@example.com',
    ]);

    $action = new UpdateProfileAction;

    $action->handle($user, [
        'name' => 'Test User',
        'email' => 'existing@example.com',
    ]);
})->throws(ValidationException::class);

test('update profile action allows user to keep their own email', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'email_verified_at' => now(),
    ]);

    $action = new UpdateProfileAction;

    $result = $action->handle($user, [
        'name' => 'Updated Name',
        'email' => 'test@example.com',
    ]);

    expect($result['emailChanged'])->toBeFalse();
    expect($user->fresh()->email_verified_at)->not->toBeNull();
});
