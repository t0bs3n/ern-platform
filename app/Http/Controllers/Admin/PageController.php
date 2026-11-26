<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Editors write and change pages (R1-01). Routes carry can:edit-content; the request checks it again. */
class PageController extends Controller
{
    public function create(): View
    {
        return view('admin.content.page-form', ['page' => new Page]);
    }

    public function store(PageRequest $request): View|RedirectResponse
    {
        if ($request->boolean('preview')) {
            $request->session()->now('_old_input', $request->except(['_token', '_method']));

            return view('admin.content.page-form', ['page' => new Page, 'preview' => new Page($request->safe()->only(['title', 'body']))]);
        }

        $page = new Page($request->safe()->only(['title', 'body']));
        $page->slug = $request->string('slug')->toString();
        $page->save();

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$page->title);
    }

    public function edit(Page $page): View
    {
        return view('admin.content.page-form', ['page' => $page]);
    }

    public function update(PageRequest $request, Page $page): View|RedirectResponse
    {
        if ($request->boolean('preview')) {
            $request->session()->now('_old_input', $request->except(['_token', '_method']));

            return view('admin.content.page-form', ['page' => $page, 'preview' => (clone $page)->fill($request->safe()->only(['title', 'body']))]);
        }

        $page->update($request->safe()->only(['title', 'body']));

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$page->title);
    }
}
