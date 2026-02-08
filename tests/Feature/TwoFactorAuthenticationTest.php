<?php

use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

test('user with 2fa enabled is redirected to challenge page on login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    // Enable 2FA for the user
    $user->forceFill([
        'two_factor_secret' => encrypt(app(Google2FA::class)->generateSecretKey()),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('two-factor.login'));

    $this->assertGuest();
    // Session should have login.id for 2FA challenge
    expect(session('login.id'))->toBe($user->id);
});

test('user without 2fa is logged in normally', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($user);
});

test('user can complete 2fa challenge with valid code', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $user->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    // Simulate login session
    session()->put('login.id', $user->id);
    session()->put('login.remember', false);

    // Get valid 2FA code
    $validCode = $google2fa->getCurrentOtp($secret);

    // Submit 2FA code
    Livewire::test('pages::auth.two-factor-challenge')
        ->set('code', $validCode)
        ->call('challenge')
        ->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($user);
});

test('user can complete 2fa challenge with recovery code', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $user->forceFill([
        'two_factor_secret' => encrypt(app(Google2FA::class)->generateSecretKey()),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    // Simulate login session
    session()->put('login.id', $user->id);
    session()->put('login.remember', false);

    // Submit recovery code
    Livewire::test('pages::auth.two-factor-challenge')
        ->set('useRecoveryCode', true)
        ->set('recovery_code', 'recovery-code-1')
        ->call('challenge')
        ->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($user);

    // Recovery code should be removed
    $user->refresh();
    $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
    expect($recoveryCodes)->not->toContain('recovery-code-1');
    expect($recoveryCodes)->toContain('recovery-code-2');
});

test('two-factor challenge page redirects if not in challenge state', function () {
    Livewire::test('pages::auth.two-factor-challenge')
        ->assertRedirect(route('login'));
});

test('invalid 2fa code shows error', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $user->forceFill([
        'two_factor_secret' => encrypt(app(Google2FA::class)->generateSecretKey()),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1', 'recovery-code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    // Simulate login session
    session()->put('login.id', $user->id);

    // Submit invalid code
    Livewire::test('pages::auth.two-factor-challenge')
        ->set('code', '000000')
        ->call('challenge')
        ->assertHasErrors(['code']);

    $this->assertGuest();
});
