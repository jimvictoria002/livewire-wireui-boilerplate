<?php

use App\Actions\Settings\ResendVerificationNotificationAction;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('resend verification notification action sends notification', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $action = new ResendVerificationNotificationAction;

    $result = $action->handle($user);

    expect($result['sent'])->toBeTrue();
    expect($result['alreadyVerified'])->toBeFalse();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('resend verification notification action does not send for verified users', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $action = new ResendVerificationNotificationAction;

    $result = $action->handle($user);

    expect($result['sent'])->toBeFalse();
    expect($result['alreadyVerified'])->toBeTrue();

    Notification::assertNothingSent();
});
