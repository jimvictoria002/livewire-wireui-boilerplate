<?php

use App\Actions\Auth\SendPasswordResetLinkAction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth'), Title('Forgot Password')]
class extends Component {
    use WireUiActions;

    #[Validate('required|email')]
    public $email;

    public function sendPasswordResetLink(SendPasswordResetLinkAction $sendPasswordResetLinkAction)
    {
        $this->validate();

        $sendPasswordResetLinkAction->handle(['email' => $this->email]);

        $this->notification()->success(title: 'Success', description: __('Email verification link will be sent to your email address if it exists in our system.'));
    }
};
?>

<x-slot:heading>{{ __('Forgot Password') }}</x-slot:heading>
<x-slot:description>{{ __('Enter your email to reset your password.') }}</x-slot:description>

<div class="flex flex-col gap-6">

    <form wire:submit.prevent="sendPasswordResetLink"
          class="flex flex-col gap-6">

        <!-- Email Address -->
        <x-input wire:model="email"
                 name="email"
                 label="{{ __('Email Address') }}"
                 type="email"
                 required
                 autofocus
                 autocomplete="email"
                 placeholder="email@example.com"/>

        <x-button type="submit"
                  label="{{__('Email password reset link') }}"
                  full
                  data-test="email-password-reset-link-button"
                  wire:model.attr="disabled"/>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-muted-foreground">
        <span>{{ __('Or, return to') }}</span>
        <x-link label="{{ __('log in') }}"
                href="{{ route('login') }}"
                wire:navigate
                secondary
                sm/>
    </div>
</div>
