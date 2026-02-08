<?php

use App\Actions\Auth\TwoFactorChallengeAction;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

test('two factor challenge action fails when no login session exists', function () {
    $action = new TwoFactorChallengeAction;

    $action->handle([
        'code' => '123456',
        'use_recovery_code' => false,
    ]);
})->throws(ValidationException::class);

test('two factor challenge action authenticates with valid totp code', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
    ]);

    session()->put('login.id', $user->id);
    session()->put('login.remember', true);

    $validCode = $google2fa->getCurrentOtp($secret);

    $action = new TwoFactorChallengeAction;

    $result = $action->handle([
        'code' => $validCode,
        'use_recovery_code' => false,
    ]);

    expect($result->id)->toBe($user->id);
    expect(auth()->check())->toBeTrue();
    expect(session()->has('login.id'))->toBeFalse();
});

test('two factor challenge action fails with invalid totp code', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
    ]);

    session()->put('login.id', $user->id);

    $action = new TwoFactorChallengeAction;

    $action->handle([
        'code' => '000000',
        'use_recovery_code' => false,
    ]);
})->throws(ValidationException::class);

test('two factor challenge action authenticates with valid recovery code', function () {
    $recoveryCodes = ['code-1', 'code-2', 'code-3'];

    $user = User::factory()->create([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        'two_factor_confirmed_at' => now(),
    ]);

    session()->put('login.id', $user->id);
    session()->put('login.remember', false);

    $action = new TwoFactorChallengeAction;

    $result = $action->handle([
        'recovery_code' => 'code-1',
        'use_recovery_code' => true,
    ]);

    expect($result->id)->toBe($user->id);
    expect(auth()->check())->toBeTrue();

    // Verify recovery code was removed
    $user->refresh();
    $remainingCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
    expect($remainingCodes)->not->toContain('code-1');
    expect($remainingCodes)->toHaveCount(2);
});

test('two factor challenge action fails with invalid recovery code', function () {
    $recoveryCodes = ['code-1', 'code-2', 'code-3'];

    $user = User::factory()->create([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        'two_factor_confirmed_at' => now(),
    ]);

    session()->put('login.id', $user->id);

    $action = new TwoFactorChallengeAction;

    $action->handle([
        'recovery_code' => 'invalid-code',
        'use_recovery_code' => true,
    ]);
})->throws(ValidationException::class);
