<?php

namespace App\Enums;

/**
 * The roles of docs/DECISIONS.md. One user can hold several.
 * Labels and descriptions as in the mockup's rights editor (js/65-admin.js).
 */
enum Role: string
{
    case Member = 'member';
    case Reviewer = 'reviewer';
    case FinalEditor = 'final_editor';
    case ConferenceTeam = 'conference_team';
    case DatabaseLead = 'database_lead';
    case LectureTeam = 'lecture_team';
    case Editor = 'editor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Member',
            self::Reviewer => 'Reviewer',
            self::FinalEditor => 'Final editor',
            self::ConferenceTeam => 'Conference team',
            self::DatabaseLead => 'Database Lead',
            self::LectureTeam => 'Lecture team',
            self::Editor => 'Editor',
            self::Admin => 'Admin',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Member => 'Sees the member area',
            self::Reviewer => 'Reviews the abstracts assigned to them',
            self::FinalEditor => 'Gives the final result on every submission. One person holds this role.',
            self::ConferenceTeam => 'Manages conferences, submissions, programme and sponsors',
            self::DatabaseLead => 'Checks new articles and edits the reading lists',
            // Not in the rights editor; from the mockup's "Who sees what".
            self::LectureTeam => 'Proposals, the autumn programme and the teaching units',
            self::Editor => 'Writes news, lectures and notices',
            self::Admin => 'Everything, including the rights of other members',
        };
    }
}
