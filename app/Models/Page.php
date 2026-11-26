<?php

namespace App\Models;

use App\Support\Markdown;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/**
 * A page of the site, written in Markdown. Its slug is fixed once created, because addresses depend on it.
 */
#[Fillable(['title', 'body'])]
class Page extends Model
{
    /**
     * Words of the privacy placeholder (database/seeders/content/privacy.md). While the
     * privacy page still contains them, the live site must not go up (ern:check-launch).
     */
    public const PRIVACY_PLACEHOLDER = 'The privacy policy for this platform is being prepared';

    public function bodyHtml(): HtmlString
    {
        return Markdown::toHtml($this->body);
    }
}
