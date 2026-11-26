<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoticeRequest;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Editors write notices for members (R1-14). Routes carry can:edit-content. */
class NoticeAdminController extends Controller
{
    public function create(): View
    {
        return view('admin.content.notice-form', ['notice' => new Notice(['status' => Notice::DRAFT])]);
    }

    public function store(NoticeRequest $request): RedirectResponse
    {
        $notice = new Notice;
        $this->fill($notice, $request);

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$notice->title);
    }

    public function edit(Notice $notice): View
    {
        return view('admin.content.notice-form', ['notice' => $notice]);
    }

    public function update(NoticeRequest $request, Notice $notice): RedirectResponse
    {
        $this->fill($notice, $request);

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$notice->title);
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        abort_unless(Gate::allows('edit-content'), 403);
        $notice->delete();

        return redirect()->route('admin.tab', 'content')->with('status', 'Removed: '.$notice->title);
    }

    private function fill(Notice $notice, NoticeRequest $request): void
    {
        $notice->fill($request->validated());
        if ($notice->status === Notice::PUBLISHED && $notice->published_at === null) {
            $notice->published_at = now();
        }
        if ($notice->status === Notice::DRAFT) {
            $notice->published_at = null;
        }
        $notice->save();
    }
}
