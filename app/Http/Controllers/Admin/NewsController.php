<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/** Editors write news posts (R1-01). Routes carry can:edit-content; the request checks it again. */
class NewsController extends Controller
{
    public function create(): View
    {
        return view('admin.content.news-form', ['news' => new News(['status' => News::DRAFT])]);
    }

    public function store(NewsRequest $request): View|RedirectResponse
    {
        if ($request->boolean('preview')) {
            return $this->preview($request, new News);
        }

        $news = new News;
        $news->slug = News::uniqueSlug($request->string('title'));
        $this->fill($news, $request);

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$news->title);
    }

    public function edit(News $news): View
    {
        return view('admin.content.news-form', ['news' => $news]);
    }

    public function update(NewsRequest $request, News $news): View|RedirectResponse
    {
        if ($request->boolean('preview')) {
            return $this->preview($request, $news);
        }

        $this->fill($news, $request);

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$news->title);
    }

    /** Show the entered text as it will look, without saving anything. */
    private function preview(NewsRequest $request, News $news): View
    {
        // Old input for this page only, so it cannot spill into the next form.
        $request->session()->now('_old_input', $request->except(['image', '_token', '_method']));
        $draft = (clone $news)->fill($request->safe()->except(['image', 'remove_image', 'published_at']));

        return view('admin.content.news-form', ['news' => $news, 'preview' => $draft]);
    }

    private function fill(News $news, NewsRequest $request): void
    {
        $news->fill($request->safe()->only(['title', 'category', 'summary', 'body', 'status']));
        $news->image_alt = $request->filled('image_alt') ? $request->string('image_alt')->toString() : null;

        $date = $request->date('published_at');
        $news->published_at = $date ?? ($news->status === News::PUBLISHED ? ($news->published_at ?? now()) : null);

        if ($request->boolean('remove_image') || $request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('local')->delete($news->image_path);
            }
            $news->image_path = null;
        }

        if ($request->hasFile('image')) {
            // Private storage; served by NewsImageController, which checks who may see it.
            $news->image_path = $request->file('image')->store('news-images', 'local');
        }

        if ($news->image_path === null) {
            $news->image_alt = null;
        }

        $news->save();
    }
}
