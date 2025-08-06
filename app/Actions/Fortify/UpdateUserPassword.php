<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
       Validator::make($input, [
    'current_password' => ['required', 'string', 'current_password'],
    'password' => $this->passwordRules(),
], [
    'current_password.current_password' => __('Le mot de passe fourni ne correspond pas à votre mot de passe actuel.'),
])->validateWithBag('updatePassword');

$user->forceFill([
    'password' => Hash::make($input['password']),
])->save();


  // Révoquer tous les tokens sauf celui en cours
    $currentToken = $user->currentAccessToken();

    $user->tokens()
        ->when($currentToken, fn ($q) => $q->where('id', '!=', $currentToken->id))
        ->delete();

    }
}
