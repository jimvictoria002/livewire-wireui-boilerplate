<?php

use App\Actions\Auth\ResetPasswordAction;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

new #[Layout('layouts::auth'), Title('Reset password')]
class extends Component {
    use WireUiActions;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    #[Validate('required')]
    public string $token = '';

    public function mount($token): void
    {
        $this->token = $token;
        $this->email = request()->email ?? '';
    }

    public function resetPassword(ResetPasswordAction $resetPasswordAction)
    {
        $this->validate();

        $status = $resetPasswordAction->handle([
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ]);

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        } else {
            $this->notification()->error(title: 'Error', description: __('Something went wrong while resetting your password.'));

            return redirect()->back()->withInput();
        }
    }
};
?>

<x-slot:heading>{{ __('Reset password') }}</x-slot:heading>
<x-slot:description>{{ __('Enter your new password below.') }}</x-slot:description>

<div class="flex flex-col gap-6">
    <form wire:submit.prevent="resetPassword"
          class="flex flex-col gap-6">
        <x-password name="password"
                    wire:model="password"
                    label="{{ __('Password') }}"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Password') }}"/>

        <x-password name="password_confirmation"
                    wire:model="password_confirmation"
                    label="{{ __('Confirm password') }}"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm password') }}"/>

        <div class="flex items-center justify-end">
            <x-button type="submit"
                      label="{{ __('Reset password') }}"
                      class="w-full"
                      data-test="reset-password-button"/>
        </div>
    </form>
    <x-errors/>
</div>
