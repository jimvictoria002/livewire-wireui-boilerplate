<?php

namespace App\Actions\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UpdateProfileAction
{
    use ProfileValidationRules;

    /**
     * Handle the profile update action.
     *
     * @param  array{name: string, email: string}  $data
     * @return array{emailChanged: bool}
     */
    public function handle(User $user, array $data): array
    {
        Validator::make($data, $this->profileRules($user->id))->validate();

        $emailChanged = false;

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $emailChanged = true;
        }

        $user->save();

        return ['emailChanged' => $emailChanged];
    }
}
