<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;

test('password can be updated', function () {
    $currentPassword = 'CurrentAa1!';
    $newPassword = 'NewAa1!'.Str::random(12);

    $user = User::factory()->create([
        'password' => Hash::make($currentPassword),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.password')
        ->set('current_password', $currentPassword)
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check($newPassword, $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $currentPassword = 'CurrentAa1!';
    $newPassword = 'NewAa1!'.Str::random(12);

    $user = User::factory()->create([
        'password' => Hash::make($currentPassword),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.password')
        ->set('current_password', 'wrong-password')
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});
