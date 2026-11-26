{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Esports Research Network: News</title>
        <link>{{ route('news.index') }}</link>
        <atom:link href="{{ route('feed.news') }}" rel="self" type="application/rss+xml" />
        <description>Announcements and updates from the Esports Research Network.</description>
        <language>en-gb</language>
@if ($news->isNotEmpty())
        <lastBuildDate>{{ $news->first()->published_at->toRssString() }}</lastBuildDate>
@endif
@foreach ($news as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('news.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('news.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            <description>{{ $post->summary }}</description>
@if ($post->category)
            <category>{{ $post->category }}</category>
@endif
        </item>
@endforeach
    </channel>
</rss>
