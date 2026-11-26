<?php

namespace App\Members;

use App\Models\MembershipApplication;
use App\Models\MembershipDecision;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Declines an application (R1-10). The application is kept for 30 days, then the
 * scheduled job deletes it. The decision log keeps no personal data.
 */
final class DeclineApplication
{
    public function __invoke(MembershipApplication $application, User $decider): MembershipApplication
    {
        return DB::transaction(function () use ($application, $decider) {
            $application = MembershipApplication::whereKey($application->id)->lockForUpdate()->first();

            if ($application === null || $application->status !== MembershipApplication::OPEN) {
                throw new DecisionException('This application was already decided.');
            }

            $application->forceFill(['status' => MembershipApplication::DECLINED, 'decided_at' => now()])->save();

            $decision = new MembershipDecision;
            $decision->forceFill([
                'application_id' => $application->id,
                'decision' => MembershipDecision::DECLINED,
                'decided_by' => $decider->id,
                'applied_at' => $application->created_at->toDateString(),
            ])->save();

            return $application;
        });
    }
}
