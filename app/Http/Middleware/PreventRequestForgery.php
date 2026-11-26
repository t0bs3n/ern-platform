<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Base;

/**
 * The framework's CSRF protection, without its extra XSRF-TOKEN cookie.
 * Our forms carry the token in a hidden field (@csrf), and only the session cookie
 * may be set (docs/DECISIONS.md, "Third parties").
 */
class PreventRequestForgery extends Base
{
    protected $addHttpCookie = false;
}
