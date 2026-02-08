<?php

use App\Actions\Auth\UpdatePasswordAction;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Title('Password')]
class extends Component {
    use WireUiActions;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(UpdatePasswordAction $updatePasswordAction): void
    {
        try {
            $updatePasswordAction->handle(auth()->user(), [
                'current_password' => $this->current_password,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);
        } catch (ValidationException $e) {
            $this->reset('password', 'password_confirmation');

            throw $e;
        }

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->notification()->success(title: 'Success', description: 'Password updated successfully.');
    }
};
?>

<div>
    <livewire:settings.layout :heading="__('Password')"
                              :subheading="__('Update your account\'s password.')">
        <form method="POST"
              wire:submit="updatePassword"
              class="mt-6 space-y-6">
            <x-password wire:model="current_password"
                        label="{{ __('Current password') }}"
                        placeholder="{{ __('Enter your current password') }}"
                        required
                        autocomplete="current-password"/>
            <x-password wire:model="password"
                        label="{{ __('New password') }}"
                        required
                        autocomplete="new-password"
                        placeholder="{{ __('Enter your new password') }}"/>
            <x-password wire:model="password_confirmation"
                        placeholder="{{ __('Confirm your new password') }}"
                        label="{{ __('Confirm Password') }}"
                        required
                        autocomplete="new-password"/>

            <x-button label="{{ __('Save') }}"
                      type="submit"
                      wire:loading.attr="disabled"
                      full/>

            <x-errors/>
        </form>
    </livewire:settings.layout>
</div>
