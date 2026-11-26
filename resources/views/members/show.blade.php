@extends('layouts.site', ['nav' => 'members', 'title' => $person->full_name])

@section('content')
    <div class="container-ern content-wrap x-abstract">
        <ol class="x-crumbs"><li><a href="{{ route('members') }}">Member area</a></li><li><a href="{{ route('members.directory') }}">Directory</a></li><li aria-current="page">{{ $person->full_name }}</li></ol>
        @if (session('status'))
            <div class="x-card" role="status" style="margin-bottom:1rem"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
        @endif
        <div class="x-cols">
            <article class="x-stack" style="gap:1.5rem">
                <div class="x-row">
                    <span class="x-av" aria-hidden="true" style="width:3.5rem;height:3.5rem;font-size:1.125rem">{{ $person->initials() }}</span>
                    <div>
                        <h1 style="margin:0">{{ $person->full_name }}</h1>
                        <span class="x-muted">{{ collect([$person->career_stage, $person->country])->filter()->implode(' · ') }}</span>
                        @if ($person->institution)<span class="x-muted" style="display:block">{{ $person->institution }}</span>@endif
                    </div>
                </div>
                @if ($person->interestTags())
                    <div>
                        <h2 style="font-size:1.0625rem;margin-bottom:.5rem">Research interests</h2>
                        <div class="x-item-tags">
                            @foreach ($person->interestTags() as $tag)<span class="x-tag x-grey">{{ $tag }}</span>@endforeach
                        </div>
                    </div>
                @endif
                @if ($person->open_to_collaboration)
                    <div><h2 style="font-size:1.0625rem;margin-bottom:.5rem">Open to</h2><p style="margin:0">Collaboration</p></div>
                @endif
                @if ($person->orcid)
                    <div><h2 style="font-size:1.0625rem;margin-bottom:.5rem">ORCID iD</h2><p style="margin:0"><a href="https://orcid.org/{{ $person->orcid }}" target="_blank" rel="noopener noreferrer">{{ $person->orcid }}<span class="x-vh"> (opens in a new tab)</span></a></p></div>
                @endif
            </article>
            <aside class="x-stack" style="gap:1rem">
                @if ($me->is($person))
                    <div class="x-card">
                        <p class="x-small" style="margin:0 0 .75rem">This is your page as other members see it.</p>
                        <a class="btn btn-outline x-btn-sm" href="{{ route('members.profile') }}">Update my details</a>
                    </div>
                @else
                    <form class="x-card" method="POST" action="{{ route('members.contact', $person->slug) }}" novalidate>
                        @csrf
                        <h2 class="x-card-h">Write to {{ $person->first_name }}</h2>
                        @if ($failed ?? false)
                            <p class="x-err" role="alert">Your message could not be sent. Please try again later.</p>
                        @endif
                        <label class="x-field" for="rmsg-in" style="margin-bottom:.75rem">
                            <span class="x-vh">Your message</span>
                            <textarea class="x-input" id="rmsg-in" name="contact_message" rows="4" maxlength="5000" placeholder="Introduce yourself and say what you have in mind." @error('contact_message') aria-invalid="true" aria-describedby="rmsg-err" @enderror>{{ $draft ?? '' }}</textarea>
                            @error('contact_message') <span class="x-err" id="rmsg-err">{{ $message }}</span> @enderror
                        </label>
                        <button class="btn btn-primary x-btn-sm" type="submit">Send through ERN</button>
                        <p class="x-small x-muted" style="margin:.75rem 0 0">ERN forwards your message by e-mail. Your address is shown, theirs stays private until they answer.</p>
                    </form>
                @endif
                @if ($person->directoryDiscord())
                    <div class="x-card"><h2 class="x-card-h">On Discord</h2><p class="x-small x-dc" style="margin:0">{{ $person->directoryDiscord() }}</p></div>
                @endif
            </aside>
        </div>
    </div>
@endsection
