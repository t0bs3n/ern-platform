<?php

namespace App\Models;

use App\Support\Markdown;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/** A notice for members (R1-14). Members see it once it is published. */
#[Fillable(['kind', 'title', 'body', 'status'])]
class Notice extends Model
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /**
     * @param  Builder<Notice>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', self::PUBLISHED)->whereNotNull('published_at')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function bodyHtml(): HtmlString
    {
        return Markdown::toHtml($this->body);
    }
}
