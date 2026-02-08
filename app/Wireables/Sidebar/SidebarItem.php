<?php

namespace App\Wireables\Sidebar;

use Livewire\Wireable;

class SidebarItem implements Wireable
{
    public function __construct(
        public ?string $route = null,
        public ?string $icon = null,
        public ?string $label = null,
    ) {}

    public function toLivewire(): array
    {
        return [
            'route' => $this->route,
            'icon' => $this->icon,
            'label' => $this->label,
        ];
    }

    public static function fromLivewire($value): static
    {
        $route = $value['route'] ?? null;
        $icon = $value['icon'] ?? null;
        $label = $value['label'] ?? null;

        return new static($route, $icon, $label);
    }
}
