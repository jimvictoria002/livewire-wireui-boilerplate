<?php

use Livewire\Component;

new class extends Component {
    public ?string $route = null;
    public ?string $label = null;
    public ?string $icon = null;
    public bool $mobile = false;

    public function getIsActiveProperty(): bool
    {
        return $this->route ? request()->routeIs($this->route) : false;
    }

    public function getHrefProperty(): ?string
    {
        return $this->route && !$this->isActive ? route($this->route) : null;
    }

    public function getClassesProperty(): string
    {
        return $this->isActive
            ? '!px-2 !text-accent-foreground !bg-accent'
            : '!px-2 hover:!text-accent-foreground hover:!bg-accent';
    }
};
?>

@php
    $attributes = $mobile
        ? $attributes->merge(['x-on:click' => "\$dispatch('hide-sidebar')"])
        : $attributes;
@endphp

@if ($this->href)
    <x-dropdown.item
        :icon="$icon"
        :label="$label ?? ''"
        wire:navigate
        :href="$this->href"
        {{ $attributes->merge(['class' => $this->classes]) }}
    />
@else
    <x-dropdown.item
        :icon="$icon"
        :label="$label ?? ''"
        {{ $attributes->merge(['class' => $this->classes]) }}
    />
@endif
