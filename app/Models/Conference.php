<?php

namespace App\Models;

use App\Support\Markdown;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * An ERN conference (R1-06). Key dates are a list of {label, date}.
 */
#[Fillable(['theme', 'place', 'mode', 'host', 'start_date', 'end_date', 'status', 'key_dates'])]
class Conference extends Model
{
    public const UPCOMING = 'upcoming';

    public const PAST = 'past';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'facts' => 'array',
            'key_dates' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Conference>  $query
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('status', self::UPCOMING)->orderBy('start_date');
    }

    /**
     * @param  Builder<Conference>  $query
     */
    public function scopePast(Builder $query): void
    {
        $query->where('status', self::PAST)->orderByDesc('start_date');
    }

    /** The conference shown on the start page and at the top of /conferences. */
    public static function featured(): ?self
    {
        return static::upcoming()->first();
    }

    /** "9 to 11 November 2027", "30 October to 1 November 2024", "31 December 2026 to 2 January 2027". */
    public function datesText(): string
    {
        $start = $this->start_date;
        $end = $this->end_date;

        if ($start->isSameDay($end)) {
            return $end->format('j F Y');
        }
        if ($start->isSameMonth($end)) {
            return $start->format('j').' to '.$end->format('j F Y');
        }
        if ($start->isSameYear($end)) {
            return $start->format('j F').' to '.$end->format('j F Y');
        }

        return $start->format('j F Y').' to '.$end->format('j F Y');
    }

    /**
     * The key dates in order, as label and date.
     *
     * @return Collection<int, array{label: string, date: Carbon}>
     */
    public function keyDates(): Collection
    {
        return collect($this->key_dates ?? [])
            ->filter(fn ($row) => is_array($row) && filled($row['label'] ?? null) && filled($row['date'] ?? null))
            ->map(fn (array $row) => ['label' => (string) $row['label'], 'date' => Carbon::parse($row['date'])])
            ->sortBy('date')
            ->values();
    }

    public function introHtml(): HtmlString
    {
        return Markdown::toHtml($this->intro);
    }

    public function summaryHtml(): HtmlString
    {
        return Markdown::toHtml($this->summary);
    }
}
