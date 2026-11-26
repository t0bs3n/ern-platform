<?php

namespace App\Http\Requests\Admin;

use App\Models\News;
use App\Support\Markdown;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
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
            'category' => ['nullable', 'string', 'max:50'],
            'summary' => ['required', 'string', 'max:600'],
            'body' => ['nullable', 'string', 'max:60000', function (string $attribute, mixed $value, \Closure $fail) {
                if (Markdown::hasExternalImage($value)) {
                    $fail('Images from other websites cannot be shown. Use the image field instead.');
                }
            }],
            'status' => ['required', Rule::in([News::DRAFT, News::PUBLISHED])],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:200'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Enter a title.',
            'summary.required' => 'Enter a short summary.',
            'image.image' => 'Choose a JPG, PNG or WebP image.',
            'image.mimes' => 'Choose a JPG, PNG or WebP image.',
            'image.max' => 'The image may be at most 2 MB.',
            'published_at.date' => 'Enter a valid date.',
        ];
    }

    /** An image needs a description for people who cannot see it. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $news = $this->route('news');
            $keepsImage = $news instanceof News && $news->image_path && ! $this->boolean('remove_image');

            if (($this->hasFile('image') || $keepsImage) && trim((string) $this->input('image_alt')) === '') {
                $validator->errors()->add('image_alt', 'Describe the image in a few words.');
            }
        });
    }
}
