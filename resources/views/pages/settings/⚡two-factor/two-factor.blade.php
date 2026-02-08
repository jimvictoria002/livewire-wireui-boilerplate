<div>
    <livewire:settings.layout :heading="__('Two Factor Authentication')"
                              :subheading="__('Manage your two-factor authentication settings.')">
        <div class="flex flex-col w-full mx-auto space-y-6 text-sm"
             wire:cloak>
            @if ($twoFactorEnabled)
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <x-badge md
                                 color="positive"
                                 label="{{ __('Enabled') }}"/>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                    </p>

                    <livewire:settings.recovery-codes :$requiresConfirmation/>

                    <div class="flex justify-start">
                        <x-button color="negative"
                                  icon="shield-exclamation"
                                  wire:click="disable">
                            {{ __('Disable 2FA') }}
                        </x-button>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <x-badge md
                                 color="negative"
                                 label="{{ __('Disabled') }}"/>
                    </div>

                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                    </p>

                    <x-button label="{{ __('Enable 2FA') }}"
                              full
                              icon="shield-check"
                              wire:click="enable"/>

                </div>
            @endif
        </div>
    </livewire:settings.layout>
    <x-modal-card name="two-factor-modal"
                  wire:model="showModal"
                  align="center"
                  width="lg"
                  x-on:close="$wire.closeModal()">
        <div class="space-y-6 z-55 bg-surface rounded-lg max-w-lg">
            <div class="space-y-6 p-6 z-55 max-w-md ">
                <div class="space-y-2 text-center">
                    <h3 class="text-lg font-semibold">{{ $this->modalConfig['title'] }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $this->modalConfig['description'] }}</p>
                </div>

                @if ($showVerificationStep)
                    <div class="space-y-6">
                        <div class="flex flex-col items-center space-y-3 justify-center">
                            <x-input wire:model="code"
                                     icon="shield-check"
                                     name="code"
                                     label="{{ __('TOTP Code') }}"
                                     type="text"
                                     inputmode="numeric"
                                     pattern="[0-9]*"
                                     maxlength="6"
                                     required
                                     autofocus
                                     autocomplete="one-time-code"
                                     placeholder="000000"
                                     oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-button variant="outline"
                                      full
                                      wire:click="resetVerification"
                                      label="{{ __('Back') }}"/>

                            <x-button color="primary"
                                      full
                                      wire:click="confirmTwoFactor"
                                      x-bind:disabled="$wire.code.length < 6"
                                      label="{{ __('Confirm') }}"/>
                        </div>
                    </div>
                @else
                    @error('setupData')
                    <x-alert color="negative"
                             icon="x-circle"
                             :title="$message"/>
                    @enderror

                    <div class="flex justify-center">
                        <div class="relative w-64 overflow-hidden border rounded-lg aspect-square">
                            @empty($qrCodeSvg)
                                <div class="absolute inset-0 flex items-center justify-center animate-pulse">
                                    <livewire:loading-spinner/>
                                </div>
                            @else
                                <div x-data
                                     class="flex items-center justify-center h-full p-4">
                                    <div class="bg-white p-3 rounded"
                                         :style="document.documentElement.classList.contains('dark') ?
                                        'filter: invert(1) brightness(1.5)' : ''">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                </div>
                            @endempty
                        </div>
                    </div>

                    <div>
                        <x-button :disabled="$errors->has('setupData')"
                                  color="primary"
                                  class="w-full"
                                  wire:click="showVerificationIfNecessary">
                            {{ $this->modalConfig['buttonText'] }}
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <div class="relative flex items-center justify-center w-full">
                        <span class="relative px-2 text-sm ">
                            {{ __('or, enter the code manually') }}
                        </span>
                        </div>

                        <div class="flex items-center space-x-2"
                             x-data="{
                        copied: false,
                        async copy() {
                            try {
                                await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                this.copied = true;
                                setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                                console.warn('Could not copy to clipboard');
                            }
                        }
                    }">
                            <div class="flex items-stretch w-full border rounded-xl ">
                                @empty($manualSetupKey)
                                    <div class="flex items-center justify-center w-full p-3 ">
                                        <livewire:loading-spinner size="sm"/>
                                    </div>
                                @else
                                    <input type="text"
                                           readonly
                                           value="{{ $manualSetupKey }}"
                                           class="w-full p-3 bg-transparent outline-none"/>

                                    <button @click="copy()"
                                            class="px-3 transition-colors border-l cursor-pointer ">
                                        <x-icon name="document-duplicate"
                                                x-show="!copied"
                                                class="h-5 w-5"
                                                outline/>
                                        <x-icon name="check"
                                                x-show="copied"
                                                class="h-5 w-5 text-green-500"
                                                outline/>
                                    </button>
                                @endempty
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-modal-card>

</div>
