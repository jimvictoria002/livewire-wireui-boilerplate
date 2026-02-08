<?php

use Livewire\Component;

new class extends Component {
    public $heading;
    public $subheading;
};
?>

<div class="h-full w-full">
    @include('partials.settings-heading')
    <div class="flex w-full lg:flex-row flex-col p-3 gap-5">
        <aside class="lg:w-44 space-y-1">
            <livewire:nav-item icon="user-circle"
                               label="{{ __('Profile') }}"
                               wire:navigate
                               route="profile.edit"
                               class="!px-0 lg:!px-2"/>
            <livewire:nav-item icon="shield-check"
                               label="{{ __('Two factor') }}"
                               wire:navigate
                               route="two-factor.show"
                               class="!px-0 lg:!px-2"/>
            <livewire:nav-item icon="key"
                               label="{{ __('Password') }}"
                               wire:navigate
                               route="user-password.edit"
                               class="!px-0 lg:!px-2"/>
            <livewire:nav-item icon="swatch"
                               label="{{ __('Appearance') }}"
                               wire:navigate
                               route="appearance.edit"
                               class="!px-0 lg:!px-2"/>
        </aside>
        <div class="flex-1 ">
            <livewire:heading :title="$heading ?? ''"
                              :subtitle="$subheading ?? ''"
                              size="text-xl"
                              weight="font-semibold"
                              class="!mb-4"/>
            <div class="max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
