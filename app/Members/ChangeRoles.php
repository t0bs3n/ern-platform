<?php

namespace App\Members;

use App\Enums\Role;
use App\Models\MemberAdminLog;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

/**
 * Changes a member's roles (R1-15), with every rule in one place:
 * the member role always stays; nobody removes their own admin role; the last admin stays;
 * one person holds the final editor role, so giving it moves it. Every change is logged.
 */
final class ChangeRoles
{
    /**
     * @param  list<Role>  $wanted  the roles to hold besides member
     * @return array{given: list<Role>, removed: list<Role>, movedFrom: ?User}
     */
    public function __invoke(User $actor, User $member, array $wanted): array
    {
        return DB::transaction(function () use ($actor, $member, $wanted) {
            // Lock the rows that the safeguards count, so two admins cannot undo each other at once.
            UserRole::whereIn('role', [Role::Admin->value, Role::FinalEditor->value])->lockForUpdate()->get();

            $wanted = array_values(array_unique(array_filter($wanted, fn (Role $role) => $role !== Role::Member), SORT_REGULAR));
            $held = $member->roles()->get()->map(fn (UserRole $r) => $r->role)->reject(fn (Role $role) => $role === Role::Member)->values()->all();

            $give = array_values(array_filter($wanted, fn (Role $role) => ! in_array($role, $held, true)));
            $remove = array_values(array_filter($held, fn (Role $role) => ! in_array($role, $wanted, true)));

            if (in_array(Role::Admin, $remove, true)) {
                if ($actor->is($member)) {
                    throw new DecisionException('You cannot remove your own admin role. Ask another admin to do it.');
                }
                $otherAdmins = User::whereNotNull('approved_at')->whereKeyNot($member->id)
                    ->whereHas('roles', fn ($q) => $q->where('role', Role::Admin->value))->count();
                if ($otherAdmins === 0) {
                    throw new DecisionException('The platform needs at least one admin. Give the role to someone else first.');
                }
            }

            $movedFrom = null;
            if (in_array(Role::FinalEditor, $give, true)) {
                $movedFrom = User::whereKeyNot($member->id)->whereHas('roles', fn ($q) => $q->where('role', Role::FinalEditor->value))->first();
                if ($movedFrom !== null) {
                    $movedFrom->roles()->where('role', Role::FinalEditor->value)->delete();
                    MemberAdminLog::write(MemberAdminLog::ROLE_REMOVED, $actor->id, $movedFrom->id, Role::FinalEditor->value);
                }
            }

            foreach ($give as $role) {
                $member->roles()->create(['role' => $role]);
                MemberAdminLog::write(MemberAdminLog::ROLE_GIVEN, $actor->id, $member->id, $role->value);
            }
            foreach ($remove as $role) {
                $member->roles()->where('role', $role->value)->delete();
                MemberAdminLog::write(MemberAdminLog::ROLE_REMOVED, $actor->id, $member->id, $role->value);
            }

            // Every member keeps the member role.
            $member->giveRole(Role::Member);
            $member->unsetRelation('roles');

            return ['given' => $give, 'removed' => $remove, 'movedFrom' => $movedFrom];
        });
    }
}
