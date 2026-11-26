/* ERN site scripts. Comfort only: every page works without them. */
(function () {
    'use strict';

    // Mobile menu: without JavaScript the button is a link to #mobile-menu,
    // which CSS opens with :target. With JavaScript it becomes a toggle.
    var toggle = document.getElementById('mobile-menu-toggle');
    var menu = document.getElementById('mobile-menu');
    if (toggle && menu) {
        var setOpen = function (open) {
            menu.classList.toggle('active', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        // Opened through the address (#mobile-menu): hand it over to the toggle, so it can close.
        if (window.location.hash === '#mobile-menu') {
            history.replaceState(null, '', window.location.pathname + window.location.search);
            setOpen(true);
        }
        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            setOpen(!menu.classList.contains('active'));
        });
        toggle.addEventListener('keydown', function (event) {
            if (event.key === ' ') {
                event.preventDefault();
                setOpen(!menu.classList.contains('active'));
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && menu.classList.contains('active')) {
                setOpen(false);
                toggle.focus();
            }
        });
    }

    // Header: a thin line under it once the page is scrolled.
    var header = document.getElementById('site-header');
    if (header) {
        var onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > 8);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ERN Live: the Twitch player loads only after the visitor presses "Start the player here".
    var player = document.getElementById('live-player');
    var start = player && player.querySelector('[data-live-start]');
    if (player && start) {
        start.hidden = false;
        start.addEventListener('click', function () {
            var frame = document.createElement('iframe');
            frame.src = 'https://player.twitch.tv/?channel=' + encodeURIComponent(player.getAttribute('data-channel')) +
                '&parent=' + encodeURIComponent(window.location.hostname);
            frame.title = 'ERN Live on Twitch';
            frame.allowFullscreen = true;
            frame.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;border:0';
            player.innerHTML = '';
            player.appendChild(frame);
            frame.focus();
        });
    }
})();
