<?php

namespace App\Http\Requests\Admin;

use App\Models\Notice;
use App\Support\Markdown;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class NoticeRequest extends FormRequest
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
            'kind' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:5000', function (string $attribute, mixed $value, \Closure $fail) {
                if (Markdown::hasExternalImage($value)) {
                    $fail('Images from other websites cannot be shown.');
                }
            }],
            'status' => ['required', Rule::in([Notice::DRAFT, Notice::PUBLISHED])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kind.required' => 'Enter a label, for example General Assembly.',
            'title.required' => 'Enter a title.',
            'body.required' => 'Enter the text of the notice.',
        ];
    }
}
