<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;

new class extends Component {
    #[Modelable]
    public string $value = '';

    public array $options = [];
};
?>

<div class="flex gap-3 bg-secondary-200 dark:bg-secondary-700 rounded-lg w-fit p-1.5">
    @foreach ($options as $key => $option)
        <button
            type="button"
            wire:click="$set('value', '{{ $key }}')"
            x-on:click="$dispatch('value-changed', { value: '{{ $key }}' })"
            @class([
                'flex items-center gap-2 rounded-lg  px-3 py-1.5 cursor-pointer transition-all text-sm',
                'bg-surface text-surface-foreground' =>
                    $value === $key,
                'border-transparent  text-muted-foreground hover:bg-surface hover:text-surface-foreground' =>
                    $value !== $key,
            ])
        >
            @if (isset($option['icon']))
                <x-icon :name="$option['icon']"
                        class="size-4"/>
            @endif
            <span>{{ $option['label'] }}</span>
        </button>
    @endforeach
</div>
