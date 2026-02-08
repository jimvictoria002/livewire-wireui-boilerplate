<?php

use App\Wireables\Sidebar\SidebarGroup;
use App\Wireables\Sidebar\SidebarItem;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function navItems(): array
    {
        return [
            new SidebarGroup(__('Platform'), [
                new SidebarItem('dashboard', 'chart-bar-square', __('Dashboard')),
            ]),
        ];
    }
};
?>

<div x-data="{ open: $persist(false).as('sidebar-open') }"
     @hide-sidebar.window="open = false"
     @keydown.window.ctrl.b.prevent="open = !open"
     class="h-screen flex overflow-hidden">

    <div x-show="open"
         x-transition.opacity
         @click="open = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Mobile --}}
    <aside x-show="open"
           x-cloak
           x-transition:enter="transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           class="fixed inset-y-0 left-0 w-sidebar bg-surface z-50 lg:hidden shadow-xl">
        <div class="flex flex-col h-full">
            <div class="flex items-center gap-2 p-3 h-16 shrink-0">
                <livewire:logo-icon class="!size-7 shrink-0"/>
                <p class="truncate font-bold text-sm uppercase tracking-wider">{{ config('app.name') }}</p>
            </div>

            <div class="px-2 flex-1 overflow-y-auto">
                <livewire:sidebar-menu :items="$this->navItems"
                                       :mobile="true"/>
            </div>
        </div>
    </aside>

    {{-- Desktop --}}
    <aside x-show="open"
           x-cloak
           x-transition:enter="transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="w-sidebar bg-surface lg:block hidden shrink-0 fixed inset-y-0 left-0 z-30">
        <div class="flex flex-col h-full">
            <div class="flex items-center gap-2 p-3 h-16 shrink-0">
                <livewire:logo-icon class="!size-7 shrink-0"/>
                <p class="truncate font-bold text-sm uppercase tracking-wider">{{ config('app.name') }}</p>
            </div>

            <div class="px-2 flex-1 overflow-y-auto">
                <livewire:sidebar-menu :items="$this->navItems"/>
            </div>
        </div>
    </aside>

    <main x-cloak
          class="flex-1 lg:rounded-tl-2xl lg:rounded-bl-2xl bg-background flex flex-col h-screen overflow-hidden transition-[margin,border-radius] duration-300 ease-in-out"
          :class="{ 'lg:ml-sidebar': open, 'ml-0 !rounded-none': !open }">

        <nav class="border-b border-b-surface p-3 flex items-center justify-between h-16 shrink-0">
            <div class="flex items-center gap-4">
                <x-icon name="bars-2"
                        class="size-6 cursor-pointer text-gray-600 hover:text-primary transition-colors"
                        @click="open = !open"/>
            </div>
            <livewire:user-desktop-menu/>
        </nav>

        <div class="flex-1 overflow-y-auto lg:p-4 p-2"
             data-scroll-area>
            {{ $slot }}
            <div class="my-16"></div>
        </div>
    </main>
</div>
