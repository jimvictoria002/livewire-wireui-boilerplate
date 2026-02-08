<?php

use Livewire\Component;

new class extends Component {
    /** @var SidebarGroup[] */
    public array $items = [];
    public bool $mobile = false;
};
?>

<div>
    @foreach ($items as $item)
        <x-dropdown.header :label="$item->name">
            @foreach ($item->items as $subItem)
                @php
                    $key = $subItem->route
                        ? 'nav-' . $subItem->route
                        : 'nav-missing-' . $loop->parent->index . '-' . $loop->index;
                @endphp

                <livewire:nav-item
                    :icon="$subItem->icon"
                    :label="$subItem->label"
                    :route="$subItem->route"
                    :mobile="$mobile"
                    :key="$key"
                />
            @endforeach
        </x-dropdown.header>
    @endforeach
</div>
