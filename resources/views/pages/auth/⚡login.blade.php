<?php

use App\Actions\Auth\LoginAction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth'), Title('Login')]
class extends Component {
    use WireUiActions;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (session('status')) {
            $this->notification()->success(title: 'Success', description: session('status'));
        }
    }

    public function login(LoginAction $loginAction): void
    {
        $this->validate();

        $result = $loginAction->handle([
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ]);

        if ($result['requiresTwoFactor']) {
            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        $this->redirect(config('fortify.home'), navigate: true);
    }
};
?>

<x-slot:heading>Sign in to your account</x-slot:heading>
<x-slot:description>Enter your credentials to continue.</x-slot:description>

<form wire:submit="login"
      class="space-y-4">
    <div>
        <x-input wire:model="email"
                 icon="envelope"
                 name="email"
                 label="{{ __('Email address') }}"
                 type="email"
                 required
                 autofocus
                 autocomplete="email"
                 placeholder="email@example.com"/>
    </div>

    <div>
        <x-password wire:model="password"
                    icon="lock-closed"
                    name="password"
                    label="Password"
                    placeholder="Enter your password"/>
        <div class="flex justify-end mt-2">
            <x-link label="{{ __('Forgot password?') }}"
                    href="{{ route('password.request') }}"
                    wire:navigate
                    secondary
                    sm/>
        </div>
    </div>

    <x-button type="submit"
              data-test="login-button"
              label="{{ __('Sign In') }}"
              full
              wire:loading.attr="disabled"/>

    <x-errors/>

</form>
