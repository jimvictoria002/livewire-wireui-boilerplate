<?php

use App\Actions\Auth\UpdatePasswordAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    RateLimiter::clear('update-password');
});

test('update password action updates user password', function () {
    $currentPassword = 'CurrentAa1!';
    $newPassword = 'NewAa1!'.Str::random(12);

    $user = User::factory()->create([
        'password' => Hash::make($currentPassword),
    ]);

    $this->actingAs($user);

    $action = new UpdatePasswordAction;

    $action->handle($user, [
        'current_password' => $currentPassword,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

test('update password action fails with incorrect current password', function () {
    $currentPassword = 'CurrentAa1!';
    $newPassword = 'NewAa1!'.Str::random(12);

    $user = User::factory()->create([
        'password' => Hash::make($currentPassword),
    ]);

    $this->actingAs($user);

    $action = new UpdatePasswordAction;

    $action->handle($user, [
        'current_password' => 'wrong-password',
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);
})->throws(ValidationException::class);

test('update password action is throttled after too many attempts', function () {
    $currentPassword = 'CurrentAa1!';
    $newPassword = 'NewAa1!'.Str::random(12);

    $user = User::factory()->create([
        'password' => Hash::make($currentPassword),
    ]);

    $this->actingAs($user);

    $action = new UpdatePasswordAction;

    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        try {
            $action->handle($user, [
                'current_password' => 'wrong-password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);
        } catch (ValidationException) {
            // Expected
        }
    }

    // The 6th attempt should be throttled
    try {
        $action->handle($user, [
            'current_password' => $currentPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $this->fail('Expected ValidationException due to throttling');
    } catch (ValidationException $e) {
        expect($e->errors()['current_password'][0])->toContain('Too many');
    }
});
