<?php

use App\Models\User;
use App\Wireables\Sidebar\SidebarGroup;
use App\Wireables\Sidebar\SidebarItem;
use Livewire\Livewire;

test('sidebar renders grouped navigation', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('sidebar')
        ->assertSee(__('Platform'))
        ->assertSee(__('Dashboard'));
});

test('sidebar menu hides on mobile click', function () {
    $items = [
        new SidebarGroup('Platform', [
            new SidebarItem('dashboard', 'chart-bar-square', 'Dashboard'),
        ]),
    ];

    Livewire::test('sidebar-menu', ['items' => $items, 'mobile' => true])
        ->assertSeeHtml('x-on:click="$dispatch(&#039;hide-sidebar&#039;)"');
});
