<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * At least eight characters, as the mockup says. No check against outside services.
     *
     * @return array<int, mixed>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::min(8), 'confirmed'];
    }

    /**
     * @return array<string, string>
     */
    protected function passwordMessages(): array
    {
        return [
            'password.required' => 'Choose a password of at least eight characters.',
            'password.min' => 'Choose a password of at least eight characters.',
            'password.confirmed' => 'The two passwords differ.',
        ];
    }
}
