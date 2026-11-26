<?php

namespace App\Console\Commands;

use App\Models\MembershipApplication;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Deletes declined applications 30 days after the decision (R1-10). The decision log,
 * which holds no personal data, stays. Only the number is reported, never names.
 */
#[Signature('ern:delete-declined-applications')]
#[Description('Delete declined applications 30 days after the decision')]
class DeleteDeclinedApplications extends Command
{
    public function handle(): int
    {
        $deleted = MembershipApplication::where('status', MembershipApplication::DECLINED)
            ->where('decided_at', '<=', now()->subDays(MembershipApplication::KEEP_DECLINED_DAYS))
            ->delete();

        $this->info("Deleted {$deleted} declined application(s).");

        return self::SUCCESS;
    }
}
