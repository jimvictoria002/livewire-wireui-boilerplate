<?php

use Livewire\Component;

new class extends Component {
    public bool $reverse = false;
};
?>

<div
    {{ $attributes->merge(['class' => 'flex items-center  gap-2  py-1.5 px-3 rounded-lg ' . ($reverse ? 'flex-row-reverse' : '')]) }}>
    <div class=" leading-4 max-w-40 ">
        <p class="font-semibold text-sm truncate text-center">{{ auth()->user()->name }}</p>
        <p class="text-muted-foreground tracking-wider  text-xs truncate text-center ">
            {{ auth()->user()?->roles?->first()?->name ?? "Role" }}</p>
    </div>
    <x-avatar
        sm
        label="{{ auth()->user()->initials() }}"
    />
</div>
