<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves a news image from private storage: to everyone once the post is public,
 * otherwise only to editors.
 */
class NewsImageController extends Controller
{
    public function __invoke(News $news): StreamedResponse
    {
        abort_if($news->image_path === null, 404);
        abort_unless($news->isPublic() || Gate::allows('edit-content'), 404);
        abort_unless(Storage::disk('local')->exists($news->image_path), 404);

        return Storage::disk('local')->response($news->image_path, null, [
            'Cache-Control' => $news->isPublic() ? 'public, max-age=3600' : 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
