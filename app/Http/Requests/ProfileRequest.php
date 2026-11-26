<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** "My details" and "Visibility" (R1-12). The e-mail address is changed separately. */
class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('use-member-area');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'career_stage' => ['nullable', Rule::in(User::CAREER_STAGES)],
            'interests' => ['nullable', 'string', 'max:1000'],
            'orcid' => ['nullable', 'string', 'regex:/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/'],
            'discord_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];
        foreach (['first_name', 'last_name', 'institution', 'country', 'interests', 'discord_name'] as $field) {
            $messages[$field.'.required'] = 'This field is needed.';
            $messages[$field.'.string'] = 'This field is needed.';
            $messages[$field.'.max'] = 'This is too long.';
        }

        return $messages + [
            'career_stage.in' => 'Please choose one.',
            'orcid.regex' => 'Enter the ORCID iD as 16 digits in four groups, for example 0000-0002-1825-0097.',
        ];
    }
}
