<?php

namespace App\Http\Requests\Admin;

use App\Models\Event;
use Carbon\CarbonImmutable;
use DateTimeZone;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('edit-content');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'kind' => ['required', Rule::in(Event::KINDS)],
            'date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
            'place' => ['nullable', 'string', 'max:200'],
            // A whole web address (https://…) or an address on this site (/…), without spaces,
            // line breaks or backslashes, which could break the calendar feed or lead off the site.
            'link' => ['nullable', 'string', 'max:500', 'regex:/^(https:\/\/[^\s\x00-\x1F\x7F\\\\]+|\/(?![\/\\\\])[^\s\x00-\x1F\x7F\\\\]*)$/D'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Enter a title.',
            'kind.in' => 'Choose the kind of event.',
            'date.required' => 'Enter the date.',
            'date.date_format' => 'Enter a valid date.',
            'start_time.date_format' => 'Enter the time as hours and minutes, for example 14:00.',
            'end_time.date_format' => 'Enter the time as hours and minutes, for example 16:00.',
            'timezone.in' => 'Choose a time zone from the list.',
            'link.regex' => 'Enter a web address starting with https:// or an address on this site starting with /.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('end_time') && ! $this->filled('start_time')) {
                $validator->errors()->add('start_time', 'Enter the start time too, or leave both times empty.');
            }
            if ($this->filled('start_time') && $this->filled('end_time') && $this->input('end_time') <= $this->input('start_time')) {
                $validator->errors()->add('end_time', 'The end must be after the start.');
            }
        });
    }

    /**
     * The values to store: times are turned into UTC from the event's time zone.
     *
     * @return array<string, mixed>
     */
    public function eventValues(): array
    {
        $data = $this->validated();
        $timed = filled($data['start_time'] ?? null);

        return [
            'title' => $data['title'],
            'kind' => $data['kind'],
            'date' => $data['date'],
            'all_day' => ! $timed,
            'starts_at' => $timed ? CarbonImmutable::parse($data['date'].' '.$data['start_time'], $data['timezone'])->utc() : null,
            'ends_at' => $timed && filled($data['end_time'] ?? null) ? CarbonImmutable::parse($data['date'].' '.$data['end_time'], $data['timezone'])->utc() : null,
            'timezone' => $data['timezone'],
            'place' => $data['place'] ?? null,
            'link' => $data['link'] ?? null,
        ];
    }
}
