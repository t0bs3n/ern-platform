<footer class="site-footer">
    <div class="container-ern">
        <div class="footer-grid">
            <div>
                <div class="footer-logo"><img src="{{ asset('img/logo.png') }}" alt="Esports Research Network" width="104" height="48"></div>
                <p class="footer-description">An international network of researchers advancing interdisciplinary esports studies. We connect scholars and industry to explore esports’ global, digital, and innovative impact.</p>
            </div>
            <nav class="footer-section" aria-label="Explore">
                <h2 class="x-footer-h">Explore</h2>
                <ul class="footer-links">
                    <li><a href="{{ url('/news') }}">News</a></li>
                    <li><a href="{{ url('/conferences') }}">Conferences</a></li>
                    <li><a href="{{ url('/database') }}">Database</a></li>
                    <li><a href="{{ url('/events') }}">Events and deadlines</a></li>
                    <li><a href="{{ url('/live') }}">ERN Live</a></li>
                    <li><a href="{{ url('/teaching') }}">Teaching library</a></li>
                    <li><a href="{{ url('/podcast') }}">Podcast</a></li>
                </ul>
            </nav>
            <nav class="footer-section" aria-label="Network">
                <h2 class="x-footer-h">Network</h2>
                <ul class="footer-links">
                    <li><a href="{{ url('/about') }}">About Us</a></li>
                    <li><a href="{{ url('/get-involved') }}">Get Involved</a></li>
                    <li><a href="{{ url('/members') }}">Member area</a></li>
                    @can('open-admin-area')
                        <li><a href="{{ route('admin') }}">Admin area</a></li>
                    @endcan
                    <li><a href="{{ url('/code-of-conduct') }}">Code of Conduct</a></li>
                    <li><a href="{{ url('/report-a-concern') }}">Report a Concern</a></li>
                </ul>
            </nav>
            <div class="footer-section">
                <h2 class="x-footer-h">Contact</h2>
                <ul class="footer-links x-contact">
                    <li><span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>board@esportsresearch.net</span></li>
                    <li><span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>ombudsperson@esportsresearch.net</span></li>
                </ul>
                <div style="margin-top:1rem">
                    <h2 class="x-footer-h" style="margin-bottom:.75rem">Follow Us</h2>
                    <div class="x-social">
                        @foreach (config('ern.social', []) as $profile)
                            <a href="{{ $profile['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $profile['label'] }}"><svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $profile['icon'] }}"></path></svg></a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; Esports Research Network e.V. {{ now()->year }}</p>
            <div class="footer-bottom-links"><a href="{{ url('/imprint') }}">Imprint</a><a href="{{ url('/privacy') }}">Privacy Policy</a></div>
        </div>
    </div>
</footer>
