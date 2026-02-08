<?php

use App\Actions\Auth\SendPasswordResetLinkAction;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    RateLimiter::clear('password-reset');
});

test('send password reset link action sends reset link', function () {
    Notification::fake();

    $user = User::factory()->create();

    $action = new SendPasswordResetLinkAction;

    $action->handle(['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('send password reset link action is throttled after too many attempts', function () {
    Notification::fake();

    $user = User::factory()->create();

    $action = new SendPasswordResetLinkAction;

    // Make 5 attempts
    for ($i = 0; $i < 5; $i++) {
        $action->handle(['email' => $user->email]);
    }

    // The 6th attempt should be throttled
    try {
        $action->handle(['email' => $user->email]);
        $this->fail('Expected ValidationException due to throttling');
    } catch (ValidationException $e) {
        expect($e->errors()['email'][0])->toContain('Too many');
    }
});
