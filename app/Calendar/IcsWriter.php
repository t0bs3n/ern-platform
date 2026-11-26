<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;
use DateTimeZone;
use Illuminate\Support\Collection;

/**
 * Writes an iCalendar file (RFC 5545) for calendar apps: one VEVENT per entry, timed
 * entries in their own time zone (TZID) with a VTIMEZONE that describes it.
 */
final class IcsWriter
{
    /**
     * @param  Collection<int, CalendarEntry>  $entries
     */
    public function write(Collection $entries, string $calendarName, string $host): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Esports Research Network//Platform//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:'.$this->text($calendarName),
        ];

        $timed = $entries->reject(fn (CalendarEntry $entry) => $entry->allDay);
        foreach ($timed->groupBy(fn (CalendarEntry $entry) => $entry->timezone) as $timezone => $group) {
            array_push($lines, ...$this->timezone($timezone, $group));
        }

        $stamp = CarbonImmutable::now('UTC')->format('Ymd\THis\Z');
        foreach ($entries as $entry) {
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:'.$entry->uid.'@'.$host;
            $lines[] = 'DTSTAMP:'.$stamp;
            if ($entry->allDay) {
                $lines[] = 'DTSTART;VALUE=DATE:'.$entry->start->format('Ymd');
                $lines[] = 'DTEND;VALUE=DATE:'.($entry->end ?? $entry->start)->addDay()->format('Ymd');
            } else {
                $lines[] = 'DTSTART;TZID='.$entry->timezone.':'.$entry->startLocal()->format('Ymd\THis');
                if ($entry->end !== null) {
                    $lines[] = 'DTEND;TZID='.$entry->timezone.':'.$entry->end->setTimezone($entry->timezone)->format('Ymd\THis');
                }
            }
            $lines[] = 'SUMMARY:'.$this->text($entry->title);
            $lines[] = 'CATEGORIES:'.$this->text($entry->kind);
            if ($entry->place) {
                $lines[] = 'LOCATION:'.$this->text($entry->place);
            }
            $url = $this->absoluteUrl($entry->link);
            if ($url !== null) {
                $lines[] = 'URL:'.$url;
            }
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", array_map(fn (string $line) => $this->fold($line), $lines))."\r\n";
    }

    /**
     * A VTIMEZONE with the offset changes of the zone around the entries.
     *
     * @param  Collection<int, CalendarEntry>  $entries
     * @return list<string>
     */
    private function timezone(string $timezone, Collection $entries): array
    {
        $zone = new DateTimeZone($timezone);
        $from = $entries->min(fn (CalendarEntry $entry) => $entry->start->getTimestamp()) - 366 * 86400;
        $to = $entries->max(fn (CalendarEntry $entry) => ($entry->end ?? $entry->start)->getTimestamp()) + 366 * 86400;
        $transitions = $zone->getTransitions($from, $to) ?: [];

        $lines = ['BEGIN:VTIMEZONE', 'TZID:'.$timezone];
        $previousOffset = $transitions[0]['offset'] ?? 0;

        foreach ($transitions as $i => $transition) {
            // The first item only states the offset at $from; it is not a change.
            $offsetFrom = $i === 0 ? $transition['offset'] : $previousOffset;
            $local = CarbonImmutable::createFromTimestampUTC($transition['ts'] + $offsetFrom);
            $lines[] = 'BEGIN:'.($transition['isdst'] ? 'DAYLIGHT' : 'STANDARD');
            $lines[] = 'DTSTART:'.$local->format('Ymd\THis');
            $lines[] = 'TZOFFSETFROM:'.$this->offset($offsetFrom);
            $lines[] = 'TZOFFSETTO:'.$this->offset($transition['offset']);
            $lines[] = 'TZNAME:'.$transition['abbr'];
            $lines[] = 'END:'.($transition['isdst'] ? 'DAYLIGHT' : 'STANDARD');
            $previousOffset = $transition['offset'];
        }

        $lines[] = 'END:VTIMEZONE';

        return $lines;
    }

    /** An absolute address for URL (RFC 5545), or null if the link is unusable. */
    private function absoluteUrl(?string $link): ?string
    {
        if ($link === null || $link === '' || preg_match('/[\x00-\x20\x7F\\\\]/', $link)) {
            return null;
        }

        return str_starts_with($link, '/') ? url($link) : $link;
    }

    private function offset(int $seconds): string
    {
        $sign = $seconds < 0 ? '-' : '+';
        $seconds = abs($seconds);

        return sprintf('%s%02d%02d', $sign, intdiv($seconds, 3600), intdiv($seconds % 3600, 60));
    }

    /** Escapes text values: backslash, semicolon, comma and line breaks. */
    private function text(string $value): string
    {
        return str_replace(['\\', ';', ',', "\r\n", "\n", "\r"], ['\\\\', '\;', '\\,', '\\n', '\\n', '\\n'], $value);
    }

    /** Lines longer than 75 bytes continue on the next line after a space, without splitting a character. */
    private function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $parts = [];
        $current = '';
        foreach (mb_str_split($line) as $char) {
            $limit = $parts === [] ? 75 : 74;
            if (strlen($current) + strlen($char) > $limit) {
                $parts[] = $current;
                $current = '';
            }
            $current .= $char;
        }
        $parts[] = $current;

        return implode("\r\n ", $parts);
    }
}
