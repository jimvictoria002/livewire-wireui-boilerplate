<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth')]
class extends Component {
    use WireUiActions;

    public function mount()
    {
        $user = Auth::user();

        if ($user?->hasVerifiedEmail()) {
            return $this->redirect(config('fortify.home'), navigate: true);
        }
    }

    public function resendVerification()
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return $this->redirect(config('fortify.home'), navigate: true);
        }

        $user->sendEmailVerificationNotification();

        $this->notification()->success(title: 'Success', description: 'A new verification link has been sent.');
    }
};
?>

<x-slot:heading>{{ __('Verify Email') }}</x-slot:heading>
<x-slot:description>{{ __('Please verify your email address by clicking on the link we just emailed to you.') }}</x-slot:description>

<div class="mt-4 flex flex-col gap-6">
    <div class="flex flex-col items-center justify-between space-y-3">
        <form wire:submit.prevent="resendVerification">
            <x-button type="submit"
                      data-test="resend-button"
                      label="{{ __('Resend verification email') }}"
                      wire:loading.attr="disabled"
                      wire:target="resendVerification"/>
        </form>

        <form method="POST"
              action="{{ route('logout') }}">
            @csrf
            <x-button variant="flat"
                      class="text-sm"
                      type="submit"
                      data-test="logout-button">
                {{ __('Log out') }}
            </x-button>
        </form>
    </div>
</div>
