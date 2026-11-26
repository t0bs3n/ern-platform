<?php

namespace App\Models;

use App\Support\Markdown;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * A news post. The public sees it only once it is published and its date has come.
 */
#[Fillable(['title', 'category', 'summary', 'body', 'image_alt', 'status', 'published_at'])]
class News extends Model
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    protected $table = 'news';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /**
     * @param  Builder<News>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', self::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublic(): bool
    {
        return $this->status === self::PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function bodyHtml(): HtmlString
    {
        return Markdown::toHtml($this->body);
    }

    /** A slug made from the title that no other post uses. */
    public static function uniqueSlug(string $title): string
    {
        $base = Str::limit(Str::slug($title), 150, '') ?: 'post';
        $slug = $base;

        for ($n = 2; static::where('slug', $slug)->exists(); $n++) {
            $slug = $base.'-'.$n;
        }

        return $slug;
    }
}
