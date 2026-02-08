<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('Appearance')]
class extends Component {
    public string $appearance = '';

    #[Computed]
    public function options(): array
    {
        return [
            'light' => ['icon' => 'sun', 'label' => __('Light')],
            'dark' => ['icon' => 'moon', 'label' => __('Dark')],
            'system' => ['icon' => 'computer-desktop', 'label' => __('System')],
        ];
    }
};
?>

<div>
    <livewire:settings.layout :heading="__('Appearance')"
                              :subheading="__('Manage your appearance settings.')">
        <div x-cloak
             x-data="{
            init() {
                const stored = localStorage.getItem('appearance') || 'system';
                this.$wire.set('appearance', stored);
            }
        }"
             @value-changed.window="
                localStorage.setItem('appearance', $event.detail.value);
                window.setAppearance($event.detail.value);
            ">
            <livewire:tab-button x-cloak
                                 wire:model.live="appearance"
                                 :options="$this->options"/>
        </div>
    </livewire:settings.layout>
</div>
