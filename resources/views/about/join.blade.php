@extends('layouts.site', ['nav' => 'involved', 'title' => 'Become a member', 'description' => 'Apply online to become a member of the Esports Research Network. Membership is free.'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('involved') }}">Get Involved</a></li><li aria-current="page">Become a member</li></ol>
                <h1>Become a member</h1>
                <p>Apply online. The board confirms every application.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap">
        <div class="x-cols">
            <div class="x-stack">
                @if ($joined)
                    <div class="x-card" role="status">
                        <div class="x-success">
                            <x-icon name="check" />
                            <div>
                                <h2 class="x-card-h">Thank you, {{ $joined }}. Your application is received</h2>
                                <p style="margin:0">We sent a copy to your e-mail address.</p>
                            </div>
                        </div>
                        <ol class="x-timeline" style="margin-top:1.25rem">
                            <li><strong>Application received</strong><span>Today</span></li>
                            <li class="x-wait"><strong>Waiting for the board</strong><span>The board confirms every application. You do not need to do anything.</span></li>
                            <li class="x-todo"><strong>Welcome e-mail</strong><span>It holds the link to set your password for the member area.</span></li>
                        </ol>
                    </div>
                    <div class="x-card">
                        <h2 class="x-card-h">No e-mail from us?</h2>
                        <p class="x-small" style="margin:0">Look in the spam folder first. If nothing is there, write to <strong>board@esportsresearch.net</strong> and name the address you applied with.</p>
                    </div>
                    <div><a href="{{ route('involved') }}" class="btn btn-outline x-btn-sm">Other ways to get involved</a></div>
                @else
                    <form class="x-card x-form" method="POST" action="{{ route('join.store') }}" novalidate>
                        @csrf
                        @if ($errors->any())
                            <p class="x-err" role="alert" style="margin:0">Please check the marked fields.</p>
                        @endif
                        <p class="x-small x-muted" style="margin:0">Fields marked with <span class="x-req">*</span> are required.</p>
                        <div class="x-two">
                            <x-field name="first_name" label="First name" mark="required" :value="old('first_name')" autocomplete="given-name" />
                            <x-field name="last_name" label="Last name" mark="required" :value="old('last_name')" autocomplete="family-name" />
                        </div>
                        <x-field name="email" label="E-mail address" mark="required" type="email" :value="old('email')" placeholder="you@university.edu" autocomplete="email" />
                        <x-field name="institution" label="University or organization" mark="required" :value="old('institution')" autocomplete="organization" />
                        <div class="x-two">
                            <x-field name="country" label="Country" mark="required" :value="old('country')" autocomplete="country-name" />
                            <x-field name="city" label="City" mark="optional" :value="old('city')" autocomplete="address-level2" />
                        </div>
                        <x-select name="career_stage" label="I am a" mark="required" :options="\App\Models\User::CAREER_STAGES" :value="old('career_stage')" />
                        <x-textarea name="interests" label="Research interests" mark="optional" rows="3" :value="old('interests')" help="A few keywords are enough. Other members find you by them." />
                        <x-field name="discord_name" label="Discord name" mark="optional" :value="old('discord_name')" help="Optional. With it you get the member role on the ERN Discord." />

                        {{-- For spam robots only: people never see or fill this field. --}}
                        <div class="x-hp" aria-hidden="true">
                            <label for="f-website">Leave this field empty</label>
                            <input id="f-website" name="website" type="text" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label class="x-check" for="f-agree">
                                <input type="checkbox" id="f-agree" name="agree" value="1" aria-required="true" @error('agree') aria-invalid="true" aria-describedby="f-agree-err" @enderror>
                                <span>I have read the <a href="{{ route('conduct') }}">Code of Conduct</a> and the <a href="{{ route('privacy') }}">privacy policy</a>. <span class="x-req" aria-hidden="true">*</span></span>
                            </label>
                            @error('agree') <span class="x-err" id="f-agree-err">{{ $message }}</span> @enderror
                        </div>
                        <div><button class="btn btn-primary" type="submit">Send application</button></div>
                    </form>
                @endif
            </div>
            <aside class="x-stack" style="gap:1rem">
                @unless ($joined)
                    <div class="x-card">
                        <h2 class="x-card-h">Before you apply</h2>
                        <dl class="x-facts x-small">
                            <div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Cost</dt><dd>Membership is free.</dd></div>
                            <div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Who can join</dt><dd>Researchers, students and practitioners with an interest in esports research.</dd></div>
                            <div style="grid-template-columns:6.5rem minmax(0,1fr)"><dt>Approval</dt><dd>The board confirms every application. You hear from us by e-mail.</dd></div>
                        </dl>
                    </div>
                @endunless
                <div class="x-card">
                    <h2 class="x-card-h">What happens next</h2>
                    <ol class="x-steps">
                        <li>The board reviews your application.</li>
                        <li>You receive a welcome e-mail with your sign-in for the member area.</li>
                        <li>From then on you keep your own details up to date in your profile.</li>
                    </ol>
                </div>
            </aside>
        </div>
    </div>
@endsection
