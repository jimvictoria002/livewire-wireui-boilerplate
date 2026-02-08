<?php

use Livewire\Livewire;

test('nav item renders without a route', function () {
    Livewire::test('nav-item', [
        'label' => 'Help',
        'route' => null,
    ])->assertSee('Help');
});
