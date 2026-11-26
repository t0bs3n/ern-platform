<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;

/**
 * One line of the events list and one VEVENT of the .ics feed. Built from an editor's
 * event or from a conference (its days and its key dates).
 */
final class CalendarEntry
{
    public function __construct(
        public readonly string $uid,
        public readonly string $title,
        public readonly string $kind,
        public readonly bool $allDay,
        /** All-day: the first day. Timed: the start, in UTC. */
        public readonly CarbonImmutable $start,
        /** All-day: the last day (inclusive). Timed: the end in UTC, or null. */
        public readonly ?CarbonImmutable $end,
        public readonly string $timezone,
        public readonly ?string $place,
        public readonly ?string $link,
    ) {}

    /** The moment after which the entry is over. */
    public function endsAt(): CarbonImmutable
    {
        if ($this->allDay) {
            return ($this->end ?? $this->start)->setTimezone($this->timezone)->endOfDay();
        }

        return $this->end ?? $this->start;
    }

    public function startLocal(): CarbonImmutable
    {
        return $this->start->setTimezone($this->timezone);
    }

    /** "Monday 4 January 2027", "9 to 11 November 2027", "Wednesday 16 December 2026, 14:00 (Oslo time)". */
    public function whenText(): string
    {
        if ($this->allDay) {
            $first = $this->start;
            $last = $this->end;
            if ($last === null || $last->isSameDay($first)) {
                return $first->format('l j F Y');
            }
            if ($first->isSameMonth($last)) {
                return $first->format('j').' to '.$last->format('j F Y');
            }

            return $first->format('j F').' to '.$last->format('j F Y');
        }

        $start = $this->startLocal();
        $text = $start->format('l j F Y, H:i');
        if ($this->end !== null) {
            $end = $this->end->setTimezone($this->timezone);
            $text .= $end->isSameDay($start) ? ' to '.$end->format('H:i') : ' to '.$end->format('l j F Y, H:i');
        }

        return $text.' ('.self::zoneName($this->timezone).')';
    }

    /** "Europe/Oslo" becomes "Oslo time", "UTC" stays "UTC". */
    public static function zoneName(string $timezone): string
    {
        if ($timezone === 'UTC') {
            return 'UTC';
        }

        $city = str_replace('_', ' ', substr($timezone, strrpos($timezone, '/') + 1));

        return $city.' time';
    }
}
