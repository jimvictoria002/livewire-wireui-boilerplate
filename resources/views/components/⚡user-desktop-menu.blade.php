<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <x-dropdown width="xl">
        <x-slot name="trigger">
            <livewire:user-desktop class="hover:bg-accent"/>
        </x-slot>
        <div class="flex justify-start w-full mb-2.5 pt-1">
            <livewire:user-desktop reverse/>
        </div>
        <livewire:nav-item icon="cog"
                           label="{{ __('Settings') }}"
                           route="profile.edit"/>
        <form action="{{ route('logout') }}"
              method="POST">
            @csrf
            <button type="submit"
                    class="w-full text-left">
                <livewire:nav-item icon="arrow-left-start-on-rectangle"
                                   label="{{ __('Logout') }}"/>
            </button>
        </form>
    </x-dropdown>

</div>
