<?php

use App\Actions\Settings\ResendVerificationNotificationAction;
use App\Actions\Settings\UpdateProfileAction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Title('Profile')]
class extends Component {
    use WireUiActions;

    public string $name = '';

    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(UpdateProfileAction $updateProfileAction): void
    {
        $updateProfileAction->handle(Auth::user(), [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->notification()->success(title: 'Success', description: 'Profile information updated successfully.');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(ResendVerificationNotificationAction $resendVerificationNotificationAction): void
    {
        $result = $resendVerificationNotificationAction->handle(Auth::user());

        if ($result['alreadyVerified']) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $this->notification()->success(title: 'Success', description: 'Verification link sent successfully.');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }
};
?>

<div>
    <livewire:settings.layout :heading="__('Profile')"
                              :subheading="__('Update your account\'s profile information.')">
        <form wire:submit="updateProfileInformation"
              class="my-6 w-full space-y-6 ">
            <x-input wire:model="name"
                     label="{{ __('Name') }}"
                     type="text"
                     required
                     autofocus
                     autocomplete="name"/>

            <div>
                <x-input wire:model="email"
                         label="{{ __('Email') }}"
                         type="email"
                         required
                         autocomplete="email"/>

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Your email address is unverified.') }}

                            <button class="text-indigo-600 hover:underline dark:text-indigo-400 text-sm cursor-pointer"
                                    type="button"
                                    wire:click.prevent="resendVerificationNotification"
                                    wire:loading.attr="disabled">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>

            <x-button type="submit"
                      label="{{ __('Save') }}"
                      full
                      wire:loading.attr="disabled"/>
        </form>
    </livewire:settings.layout>
</div>
