<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ConfirmPasswordAction
{
    /**
     * Handle the password confirmation action.
     *
     * @param  array{password: string}  $data
     *
     * @throws ValidationException
     */
    public function handle(User $user, array $data): void
    {
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('Password does not match our records.'),
            ]);
        }

        session()->passwordConfirmed();
    }
}
