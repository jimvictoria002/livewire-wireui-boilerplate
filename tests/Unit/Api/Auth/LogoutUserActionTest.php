<?php

use App\Actions\Api\Auth\LogoutUserAction;
use App\Models\User;

test('it revokes user token on logout', function () {
    $user = User::factory()->create();

    // Create a token for the user
    $token = $user->createToken('auth-token');

    // Verify token exists
    expect($user->tokens()->count())->toBe(1);

    // Set the current token
    $user->withAccessToken($token->accessToken);

    $action = app(LogoutUserAction::class);
    $action->handle($user);

    // Verify token was deleted
    expect($user->tokens()->count())->toBe(0);
});

test('it handles logout when no current token exists', function () {
    $user = User::factory()->create();

    expect($user->tokens()->count())->toBe(0);

    $action = app(LogoutUserAction::class);

    // Should not throw exception when no token exists
    expect(fn () => $action->handle($user))->not->toThrow(Exception::class);

    expect($user->tokens()->count())->toBe(0);
});

test('it only deletes current token, not all tokens', function () {
    $user = User::factory()->create();

    // Create multiple tokens
    $token1 = $user->createToken('auth-token-1');
    $token2 = $user->createToken('auth-token-2');

    expect($user->tokens()->count())->toBe(2);

    // Set current token to token1
    $user->withAccessToken($token1->accessToken);

    $action = app(LogoutUserAction::class);
    $action->handle($user);

    // Verify only the current token was deleted
    $user->refresh();
    expect($user->tokens()->count())->toBe(1);
    expect($user->tokens()->first()->token)->toBe($token2->accessToken->token);
});
