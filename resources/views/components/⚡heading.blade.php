<?php

use Livewire\Component;
use Livewire\Attributes\Reactive;

new class extends Component {
    public $title = '';
    public $subtitle = '';
    public $size = 'text-2xl';
    public $weight = 'font-bold';
    public $separator = false;
}; ?>

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($title)
        <h2 class="{{ $size }} {{ $weight }} ">{{ $this->title }}</h2>
    @endif

    @if ($subtitle)
        <p class="mt-1 text-sm text-muted-foreground">{{ $subtitle }}</p>
    @endif

    @if ($separator)
            <hr class="mt-4 border-gray-200 dark:border-gray-700 "/>
    @endif
</div>
