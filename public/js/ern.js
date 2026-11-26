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
})();
