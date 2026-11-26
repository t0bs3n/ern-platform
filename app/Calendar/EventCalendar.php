<?php

namespace App\Calendar;

use App\Models\Conference;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Events and deadlines in one list (R1-08): the events editors add, plus the days and
 * key dates of upcoming conferences, which appear automatically.
 */
final class EventCalendar
{
    /**
     * Every entry that is not over yet, in order.
     *
     * @return Collection<int, CalendarEntry>
     */
    public function upcoming(?string $kind = null): Collection
    {
        $now = CarbonImmutable::now();

        return $this->all()
            ->filter(fn (CalendarEntry $entry) => $entry->endsAt()->greaterThanOrEqualTo($now))
            ->when($kind !== null, fn (Collection $entries) => $entries->filter(fn (CalendarEntry $entry) => $entry->kind === $kind))
            ->values();
    }

    /**
     * Every entry, past ones included, in order.
     *
     * @return Collection<int, CalendarEntry>
     */
    public function all(): Collection
    {
        return $this->fromEvents()
            ->concat($this->fromConferences())
            // By the moment each entry begins. An all-day entry begins at midnight in its own zone.
            ->sortBy(fn (CalendarEntry $entry) => [$entry->start->getTimestamp(), $entry->title])
            ->values();
    }

    /** @return Collection<int, CalendarEntry> */
    private function fromEvents(): Collection
    {
        return Event::query()->get()->map(function (Event $event) {
            $allDay = $event->all_day || $event->starts_at === null;

            return new CalendarEntry(
                uid: 'event-'.$event->id,
                title: $event->title,
                kind: $event->kind,
                allDay: $allDay,
                start: $allDay ? CarbonImmutable::parse($event->date->format('Y-m-d'), $event->timezone) : $event->starts_at->utc(),
                end: $allDay ? null : $event->ends_at?->utc(),
                timezone: $event->timezone,
                place: $event->place,
                link: $event->link,
            );
        });
    }

    /** @return Collection<int, CalendarEntry> */
    private function fromConferences(): Collection
    {
        $timezone = (string) config('ern.event_timezone', 'Europe/Berlin');

        return Conference::upcoming()->get()->flatMap(function (Conference $conference) use ($timezone) {
            $link = route('conferences.show', $conference);
            $place = $conference->place;

            $entries = $conference->keyDates()->map(fn (array $date, int $n) => new CalendarEntry(
                uid: 'conference-'.$conference->slug.'-date-'.($n + 1),
                title: $conference->name.': '.Str::lcfirst($date['label']),
                kind: 'Deadline',
                allDay: true,
                start: CarbonImmutable::parse($date['date']->format('Y-m-d'), $timezone),
                end: null,
                timezone: $timezone,
                place: null,
                link: $link,
            ));

            return $entries->push(new CalendarEntry(
                uid: 'conference-'.$conference->slug,
                title: $conference->name.': '.$conference->theme,
                kind: 'Conference',
                allDay: true,
                start: CarbonImmutable::parse($conference->start_date->format('Y-m-d'), $timezone),
                end: CarbonImmutable::parse($conference->end_date->format('Y-m-d'), $timezone),
                timezone: $timezone,
                place: $place,
                link: $link,
            ));
        });
    }
}
