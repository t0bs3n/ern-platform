<?php

namespace App\Auth;

use App\Enums\Role;

/**
 * Which roles pass which gate. The single source for the gates and their tests.
 * Admin is named in every gate on purpose (no Gate::before), so that later
 * record-level policies can decide for themselves whether admin passes.
 */
final class Permissions
{
    /** @var array<string, list<Role>> */
    public const GATES = [
        'manage-members' => [Role::Admin],
        'manage-conference' => [Role::ConferenceTeam, Role::Admin],
        'review-submissions' => [Role::Reviewer, Role::Admin],
        'decide-submissions' => [Role::FinalEditor, Role::Admin],
        'manage-database' => [Role::DatabaseLead, Role::Admin],
        'manage-teaching' => [Role::LectureTeam, Role::Admin],
        'edit-content' => [Role::Editor, Role::Admin],
        'administer' => [Role::Admin],
        // Anyone with a role beyond member: the admin area and its footer link.
        'open-admin-area' => [
            Role::Reviewer, Role::FinalEditor, Role::ConferenceTeam, Role::DatabaseLead,
            Role::LectureTeam, Role::Editor, Role::Admin,
        ],
    ];
}
