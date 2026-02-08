<?php

use App\Actions\Auth\ConfirmPasswordAction;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth')]
class extends Component {
    use WireUiActions;

    public bool $remember = false;

    public string $password = '';

    public function mount(): void
    {
        if (session('status')) {
            $this->notification()->success(title: 'Success', description: session('status'));
        }
    }

    public function confirmPassword(ConfirmPasswordAction $confirmPasswordAction)
    {
        $confirmPasswordAction->handle(auth()->user(), ['password' => $this->password]);

        redirect()->intended();
    }
};
?>

<x-slot:heading>Confirm Password</x-slot:heading>
<x-slot:description>Confirm your password before continuing.</x-slot:description>


<div class="flex flex-col gap-6">

    <form wire:submit.prevent="confirmPassword"
          class="space-y-6">
        <x-password icon="lock-closed"
                    wire:model="password"
                    name="password"
                    label="Password"
                    placeholder="Enter your password"/>

        <x-button type="submit"
                  data-test="login-button"
                  label="Confirm Password"
                  full
                  wire:loading.attr="disabled"/>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-muted-foreground">
        <span>{{ __('Or, return to') }}</span>
        <x-link label="{{ __('Back') }}"
                href="{{ route('dashboard') }}"
                secondary
                sm
                wire:navigate/>
    </div>
</div>
