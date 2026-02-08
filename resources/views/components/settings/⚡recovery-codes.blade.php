<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception $e) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
};
?>

<div class="space-y-6   rounded-xl my-10"
     wire:cloak
     x-data="{ showRecoveryCodes: false }">
    <div class="space-y-2">
        <div class="flex items-center gap-2">
            <x-icon name="lock-closed"
                    class="h-4 w-4"
                    outline/>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('2FA Recovery Codes') }}</h3>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </p>
    </div>

    <div class="space-y-2 ">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-button x-show="!showRecoveryCodes"
                      icon="eye"
                      color="primary"
                      @click="showRecoveryCodes = true;"
                      aria-expanded="false"
                      aria-controls="recovery-codes-section"
                      label="{{ __('View Recovery Codes') }}"/>

            <x-button x-show="showRecoveryCodes"
                      icon="eye-slash"
                      color="primary"
                      @click="showRecoveryCodes = false"
                      aria-expanded="true"
                      aria-controls="recovery-codes-section"
                      label="{{ __('Hide Recovery Codes') }}"/>

            @if (filled($recoveryCodes))
                <x-button x-show="showRecoveryCodes"
                          icon="arrow-path"
                          variant="outline"
                          wire:click="regenerateRecoveryCodes">
                    {{ __('Regenerate Codes') }}
                </x-button>
            @endif
        </div>

        <div x-show="showRecoveryCodes"
             x-transition
             id="recovery-codes-section"
             class="relative overflow-hidden"
             x-bind:aria-hidden="!showRecoveryCodes">
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                <x-alert color="negative"
                         icon="x-circle"
                         :title="$message"/>
                @enderror

                @if (filled($recoveryCodes))
                    <div class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-100 dark:bg-white/5"
                         role="list"
                         aria-label="{{ __('Recovery codes') }}">
                        @foreach ($recoveryCodes as $code)
                            <div role="listitem"
                                 class="select-text"
                                 wire:loading.class="opacity-50 animate-pulse">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate Codes above.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
