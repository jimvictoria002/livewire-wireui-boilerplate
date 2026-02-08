<?php

use App\Actions\Auth\LoginAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    RateLimiter::clear('login');
});

test('login action authenticates user with valid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $action = new LoginAction;

    $result = $action->handle([
        'email' => $user->email,
        'password' => 'password123',
        'remember' => false,
    ]);

    expect($result['user']->id)->toBe($user->id);
    expect($result['requiresTwoFactor'])->toBeFalse();
    expect(auth()->check())->toBeTrue();
});

test('login action throws exception for invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $action = new LoginAction;

    $action->handle([
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
})->throws(ValidationException::class);

test('login action redirects to two factor challenge when enabled', function () {
    $user = User::factory()->withTwoFactor()->create([
        'password' => Hash::make('password123'),
    ]);

    $action = new LoginAction;

    $result = $action->handle([
        'email' => $user->email,
        'password' => 'password123',
        'remember' => true,
    ]);

    expect($result['requiresTwoFactor'])->toBeTrue();
    expect(session('login.id'))->toBe($user->id);
    expect(session('login.remember'))->toBeTrue();
    expect(auth()->check())->toBeFalse();
});

test('login action is throttled after too many attempts', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $action = new LoginAction;

    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        try {
            $action->handle([
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        } catch (ValidationException) {
            // Expected
        }
    }

    // The 6th attempt should be throttled
    try {
        $action->handle([
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->fail('Expected ValidationException due to throttling');
    } catch (ValidationException $e) {
        expect($e->errors()['email'][0])->toContain('Too many');
    }
});
