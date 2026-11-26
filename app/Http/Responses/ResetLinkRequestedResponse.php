<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;

/**
 * The one answer to every valid reset request: known address, unknown address,
 * unapproved applicant or a repeated request. Nobody can learn who is a member.
 */
class ResetLinkRequestedResponse implements FailedPasswordResetLinkRequestResponse, SuccessfulPasswordResetLinkRequestResponse
{
    public function __construct(protected string $status = '') {}

    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('password.request')->with('reset_link_requested', true);
    }
}
