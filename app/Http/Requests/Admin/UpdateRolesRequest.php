<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('administer');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::enum(Role::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['roles.*' => 'Choose roles from the list.', 'roles.array' => 'Choose roles from the list.'];
    }

    /** @return list<Role> */
    public function roles(): array
    {
        return array_map(fn (string $value) => Role::from($value), array_values((array) $this->validated('roles', [])));
    }
}
