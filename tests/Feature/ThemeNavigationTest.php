<?php

test('login page re-applies theme preference on navigation', function () {
    $response = $this->get('/login');

    $response->assertSuccessful();
    $response->assertSee('applyThemePreference');
    $response->assertSee('livewire:navigated');
    $response->assertSee('livewire:navigating');
});
