<?php

use App\Models\User;

use function Pest\Laravel\postJson;
use function Pest\Laravel\withHeader;

test('a user can logout', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token')->plainTextToken;

    $response = withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/logout');

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Logged out successfully.',
        ]);

    expect($user->tokens()->count())->toBe(0);
});

test('guest cannot logout', function () {
    $response = postJson('/api/logout');

    $response->assertUnauthorized();
});
