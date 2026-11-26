<?php

namespace App\Http\Requests\Admin;

use App\Support\Markdown;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
        $rules = [
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:100000', function (string $attribute, mixed $value, \Closure $fail) {
                if (Markdown::hasExternalImage($value)) {
                    $fail('Images from other websites cannot be shown. Use the image field instead.');
                }
            }],
        ];

        // The address part is set once, when the page is created.
        if ($this->route('page') === null) {
            $rules['slug'] = ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('pages', 'slug')];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Enter a title.',
            'body.required' => 'Enter the text of the page.',
            'slug.required' => 'Enter the address of the page.',
            'slug.regex' => 'Use small letters, digits and hyphens only, for example code-of-conduct.',
            'slug.unique' => 'Another page already uses this address.',
        ];
    }
}
