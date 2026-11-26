<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;

class PasswordResetResponse implements PasswordResetResponseContract
{
    public function __construct(protected string $status = '') {}

    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('login')->with('status', 'Your new password is saved. Sign in with it.');
    }
}
