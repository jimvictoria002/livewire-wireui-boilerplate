<?php

namespace App\Wireables\Sidebar;

use Livewire\Wireable;

class SidebarGroup implements Wireable
{
    /**
     * @param  SidebarItem[]  $items
     */
    public function __construct(public string $name, public array $items = []) {}

    public function toLivewire(): array
    {
        return [
            'name' => $this->name,
            'items' => array_map(fn (SidebarItem $item) => $item->toLivewire(), $this->items),
        ];
    }

    public static function fromLivewire($value): static
    {
        $name = $value['name'];
        $items = array_map(fn ($item) => SidebarItem::fromLivewire($item), $value['items'] ?? []);

        return new static($name, $items);
    }
}
