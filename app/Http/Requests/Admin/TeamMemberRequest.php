<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TeamMemberRequest extends FormRequest
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
            'group' => ['required', Rule::in(array_keys(TeamMember::GROUPS))],
            'name' => ['required', 'string', 'max:120'],
            'affiliation' => ['nullable', 'string', 'max:200'],
            'role_title' => ['required', 'string', 'max:120'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter the name.',
            'role_title.required' => 'Enter the role, for example Board Member.',
            'group.in' => 'Choose where the person is shown.',
        ];
    }
}
