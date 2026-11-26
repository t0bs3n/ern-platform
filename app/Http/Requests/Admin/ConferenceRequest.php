<?php

namespace App\Http\Requests\Admin;

use App\Models\Conference;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ConferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-conference');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', 'max:250'],
            'place' => ['required', 'string', 'max:250'],
            'mode' => ['required', 'string', 'max:40'],
            'host' => ['nullable', 'string', 'max:250'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in([Conference::UPCOMING, Conference::PAST])],
            'key_dates' => ['nullable', 'array', 'max:12'],
            'key_dates.*.label' => ['nullable', 'string', 'max:80', 'required_with:key_dates.*.date'],
            'key_dates.*.date' => ['nullable', 'date', 'required_with:key_dates.*.label'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'theme.required' => 'Enter the theme.',
            'place.required' => 'Enter the place.',
            'mode.required' => 'Enter the format, for example In person.',
            'start_date.required' => 'Enter the first day.',
            'end_date.required' => 'Enter the last day.',
            'end_date.after_or_equal' => 'The last day cannot be before the first day.',
            'key_dates.*.label.required_with' => 'Name this date.',
            'key_dates.*.date.required_with' => 'Enter the date.',
            'key_dates.*.date.date' => 'Enter a valid date.',
        ];
    }

    /**
     * The filled key date rows, without the empty ones.
     *
     * @return list<array{label: string, date: string}>
     */
    public function keyDates(): array
    {
        return collect($this->validated('key_dates') ?? [])
            ->filter(fn ($row) => filled($row['label'] ?? null) && filled($row['date'] ?? null))
            ->map(fn ($row) => ['label' => trim($row['label']), 'date' => date('Y-m-d', strtotime($row['date']))])
            ->sortBy('date')
            ->values()
            ->all();
    }
}
