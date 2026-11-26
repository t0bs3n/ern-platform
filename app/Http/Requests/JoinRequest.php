<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** The application form (R1-09). Messages as in the mockup (js/50-about.js). */
class JoinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'max:255', 'email:rfc'],
            'institution' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'career_stage' => ['required', Rule::in(User::CAREER_STAGES)],
            'interests' => ['nullable', 'string', 'max:1000'],
            'discord_name' => ['nullable', 'string', 'max:100'],
            'agree' => ['accepted'],
            // The hidden field for spam robots. Any value is accepted here; the controller ignores such sends.
            'website' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];
        foreach (['first_name', 'last_name', 'email', 'institution', 'country', 'city', 'interests', 'discord_name'] as $field) {
            $messages[$field.'.required'] = 'This field is needed.';
            $messages[$field.'.string'] = 'This field is needed.';
            $messages[$field.'.max'] = 'This is too long.';
        }

        return $messages + [
            'email.email' => 'Enter an e-mail address, for example you@university.edu.',
            'career_stage.required' => 'Please choose one.',
            'career_stage.in' => 'Please choose one.',
            'agree.accepted' => 'Please confirm that you have read both.',
        ];
    }
}
