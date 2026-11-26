<?php

namespace App\Members;

use App\Enums\Role;
use App\Models\MembershipApplication;
use App\Models\MembershipDecision;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Approves an application (R1-10): creates the approved member with the member role,
 * logs the decision and deletes the application, whose details now live on the member.
 * The city is kept too, but only the member and the admins see it, never the directory.
 */
final class ApproveApplication
{
    public function __invoke(MembershipApplication $application, User $decider): User
    {
        return DB::transaction(function () use ($application, $decider) {
            $application = MembershipApplication::whereKey($application->id)->lockForUpdate()->first();

            if ($application === null || $application->status !== MembershipApplication::OPEN) {
                throw new DecisionException('This application was already decided.');
            }
            if (User::where('email', $application->email)->exists()) {
                throw new DecisionException('This e-mail address already belongs to a member. The application stays open.');
            }

            $member = new User([
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'email' => $application->email,
                'institution' => $application->institution,
                'country' => $application->country,
                'city' => $application->city,
                'career_stage' => $application->career_stage,
                'interests' => $application->interests,
                'discord_name' => $application->discord_name,
                'listed_in_directory' => false,
            ]);
            $member->slug = User::uniqueSlug($application->first_name, $application->last_name);
            $member->forceFill(['password' => null, 'approved_at' => now()])->save();
            $member->giveRole(Role::Member);

            $decision = new MembershipDecision;
            $decision->forceFill([
                'application_id' => $application->id,
                'decision' => MembershipDecision::APPROVED,
                'decided_by' => $decider->id,
                'member_id' => $member->id,
                'applied_at' => $application->created_at->toDateString(),
                'agreed_at' => $application->agreed_at,
            ])->save();

            $application->delete();

            return $member;
        });
    }
}
