<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A person on About Us: a board member, or a volunteer under "Governance & Volunteers".
 * Election candidates are never shown on the platform (docs/DECISIONS.md).
 */
#[Fillable(['group', 'name', 'affiliation', 'role_title', 'position'])]
class TeamMember extends Model
{
    public const GROUPS = [
        'board' => 'Board Members',
        'governance' => 'Governance & Volunteers',
    ];

    /**
     * @param  Builder<TeamMember>  $query
     */
    public function scopeInGroup(Builder $query, string $group): void
    {
        $query->where('group', $group)->orderBy('position')->orderBy('id');
    }

    public function initials(): string
    {
        return Str::upper(collect(preg_split('/\s+/', trim($this->name)))->map(fn ($word) => Str::substr($word, 0, 1))->take(2)->implode(''));
    }
}
