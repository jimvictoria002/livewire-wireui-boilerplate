<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div {{ $attributes->merge(['class' => 'bg-white rounded-full size-18 flex items-center justify-center shadow-md']) }}>
    <img src="{{ asset('icons/icon.webp') }}"
         alt="Logo"
         class="w-full"/>
</div>
