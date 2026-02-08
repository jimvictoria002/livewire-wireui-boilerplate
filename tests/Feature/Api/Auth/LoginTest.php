<?php

use App\Models\User;
use function Pest\Laravel\postJson;

test('users can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['access_token']);
});

test('users cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonFragment([
            'email' => 'These credentials do not match our records.',
        ]);
});
