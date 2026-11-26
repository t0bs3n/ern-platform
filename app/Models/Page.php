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
    public function bodyHtml(): HtmlString
    {
        return Markdown::toHtml($this->body);
    }
}
