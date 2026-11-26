<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * An event or deadline added by an editor (R1-08). An all-day entry has only a date
 * (a deadline ends with that day). A timed entry has starts_at (and maybe ends_at) in UTC,
 * shown in its own time zone. Conference key dates are not stored here: see EventCalendar.
 */
#[Fillable(['title', 'kind', 'all_day', 'date', 'starts_at', 'ends_at', 'timezone', 'place', 'link'])]
class Event extends Model
{
    public const KINDS = ['Conference', 'Lecture', 'Deadline', 'Association'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'all_day' => 'boolean',
            'date' => 'date',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
        ];
    }
}
