<?php

use App\Actions\Auth\TwoFactorChallengeAction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth'), Title('Two-Factor Challenge')]
class extends Component {
    use WireUiActions;

    #[Validate('nullable|string')]
    public string $code = '';

    #[Validate('nullable|string')]
    public string $recovery_code = '';

    public bool $useRecoveryCode = false;

    public function mount(): void
    {
        // Redirect if not in a 2FA challenge state
        if (!session()->has('login.id')) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if (session('status')) {
            $this->notification()->success(title: 'Success', description: session('status'));
        }
    }

    public function challenge(TwoFactorChallengeAction $twoFactorChallengeAction): void
    {
        $this->validate();

        $twoFactorChallengeAction->handle([
            'code' => $this->code,
            'recovery_code' => $this->recovery_code,
            'use_recovery_code' => $this->useRecoveryCode,
        ]);

        $this->redirect(config('fortify.home'), navigate: true);
    }

    public function toggleRecoveryCode(): void
    {
        $this->useRecoveryCode = !$this->useRecoveryCode;
        $this->reset('code', 'recovery_code');
        $this->resetErrorBag();
    }
};
?>

<x-slot:heading>Two-Factor Authentication</x-slot:heading>
<x-slot:description>Enter your authentication code to continue.</x-slot:description>

<form wire:submit="challenge"
      class="space-y-4">
    @if (!$useRecoveryCode)
        <div>
            <x-input
                wire:model="code"
                icon="shield-check"
                name="code"
                label="{{ __('Authentication Code') }}"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="000000"
            />
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Enter the 6-digit code from your authenticator app.') }}
            </p>
        </div>
    @else
        <div>
            <x-input
                wire:model="recovery_code"
                icon="key"
                name="recovery_code"
                label="{{ __('Recovery Code') }}"
                type="text"
                required
                autofocus
                autocomplete="off"
                placeholder="abcd-efgh-ijkl"
            />
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Enter one of your recovery codes.') }}
            </p>
        </div>
    @endif

    <x-button
        type="submit"
        label="{{ __('Verify') }}"
        full
        wire:loading.attr="disabled"
    />

        <x-errors/>

    <div class="text-center">
        <button
            type="button"
            wire:click="toggleRecoveryCode"
            class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
        >
            @if (!$useRecoveryCode)
                {{ __('Use a recovery code') }}
            @else
                {{ __('Use an authentication code') }}
            @endif
        </button>
    </div>
</form>
