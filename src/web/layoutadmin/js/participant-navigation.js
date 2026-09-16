(function () {
    'use strict';

    var mobileBreakpoint = 768;
    var body = document.querySelector('.participant-shell');
    var sidebar = document.getElementById('accordionSidebar');
    var toggle = document.querySelector('[data-participant-menu-toggle]');
    var closeControls = document.querySelectorAll('[data-participant-menu-close]');
    var lastFocusedElement = null;

    if (!body || !sidebar || !toggle) {
        return;
    }

    function isMobile() {
        return window.innerWidth < mobileBreakpoint;
    }

    function setMenuState(isOpen, restoreFocus) {
        var open = Boolean(isOpen && isMobile());

        body.classList.toggle('mobile-nav-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.querySelector('.sr-only').textContent = open ? 'Fechar menu' : 'Abrir menu';
        sidebar.setAttribute('aria-hidden', open && isMobile() ? 'false' : (isMobile() ? 'true' : 'false'));

        if (open) {
            lastFocusedElement = document.activeElement;
            window.setTimeout(function () {
                var firstLink = sidebar.querySelector('a, button');
                if (firstLink) {
                    firstLink.focus();
                }
            }, 0);
        } else if (restoreFocus && lastFocusedElement) {
            lastFocusedElement.focus();
        }
    }

    toggle.addEventListener('click', function () {
        setMenuState(!body.classList.contains('mobile-nav-open'), true);
    });

    Array.prototype.forEach.call(closeControls, function (control) {
        control.addEventListener('click', function () {
            setMenuState(false, true);
        });
    });

    sidebar.addEventListener('click', function (event) {
        if (isMobile() && event.target.closest('a')) {
            setMenuState(false, false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && body.classList.contains('mobile-nav-open')) {
            setMenuState(false, true);
        }
    });

    window.addEventListener('resize', function () {
        setMenuState(false, false);
    });

    setMenuState(false, false);
}());
