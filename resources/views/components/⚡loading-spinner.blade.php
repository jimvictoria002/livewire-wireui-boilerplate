<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public $size = 'md';

    #[Computed]
    public function getSizeClassesProperty()
    {
        return match ($this->size) {
            'sm' => 'size-4',
            'md' => 'size-6',
            'lg' => 'size-8',
            _ => 'size-6',
        };
    }
};
?>

<svg {{ $attributes->merge(['class' => "animate-spin text-gray-400 " . $this->getSizeClassesProperty]) }} xmlns="http://www.w3.org/2000/svg"
     fill="none"
     viewBox="0 0 24 24">
    <circle class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"></circle>
    <path class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
</svg>
