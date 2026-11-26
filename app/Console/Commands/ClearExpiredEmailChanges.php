<?php

namespace App\Console\Commands;

use App\Members\EmailChangeLink;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/** Forgets new e-mail addresses that were not confirmed within 24 hours (R1-12). */
#[Signature('ern:clear-expired-email-changes')]
#[Description('Forget unconfirmed new e-mail addresses after 24 hours')]
class ClearExpiredEmailChanges extends Command
{
    public function handle(): int
    {
        $cleared = User::whereNotNull('pending_email')
            ->where('pending_email_requested_at', '<=', now()->subHours(EmailChangeLink::HOURS))
            ->update(['pending_email' => null, 'pending_email_requested_at' => null]);

        $this->info("Cleared {$cleared} unconfirmed address(es).");

        return self::SUCCESS;
    }
}
