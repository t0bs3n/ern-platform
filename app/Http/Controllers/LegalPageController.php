<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Markdown;
use Illuminate\View\View;

/** Code of Conduct, Imprint and Privacy Policy (R1-05): pages that editors change in the Content tab. */
class LegalPageController extends Controller
{
    public function conduct(): View
    {
        return $this->show('code-of-conduct', 'legal.conduct');
    }

    public function imprint(): View
    {
        return $this->show('imprint', 'legal.page');
    }

    public function privacy(): View
    {
        return $this->show('privacy', 'legal.page');
    }

    private function show(string $slug, string $view): View
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view($view, ['page' => $page] + Markdown::withSections($page->body));
    }
}
