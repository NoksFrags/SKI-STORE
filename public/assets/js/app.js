/* Ski Store - front-end interactions (vanilla JS, no dependencies) */
(function () {
    'use strict';

    var body = document.body;

    /* ---------- Mobile navigation ---------- */
    var navToggle = document.querySelector('[data-nav-toggle]');

    if (navToggle) {
        navToggle.addEventListener('click', function () {
            var isOpen = body.classList.toggle('nav-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    document.querySelectorAll('[data-nav-link]').forEach(function (link) {
        link.addEventListener('click', function () {
            body.classList.remove('nav-open');
        });
    });

    var navClose = document.querySelector('[data-nav-close]');

    if (navClose) {
        navClose.addEventListener('click', function () {
            body.classList.remove('nav-open');
        });
    }

    /* Tapping the dimmed page (the overlay) closes the drawers. */
    document.addEventListener('click', function (event) {
        if (body.classList.contains('nav-open') && event.target === document.body) {
            body.classList.remove('nav-open');
        }

        if (body.classList.contains('admin-nav-open')
            && !event.target.closest('.admin-sidebar')
            && !event.target.closest('[data-admin-toggle]')) {
            body.classList.remove('admin-nav-open');
        }
    });

    document.querySelectorAll('.admin-nav__link').forEach(function (link) {
        link.addEventListener('click', function () {
            body.classList.remove('admin-nav-open');
        });
    });

    /* ---------- Dropdown menus (account + categories) ---------- */
    document.querySelectorAll('[data-dropdown]').forEach(function (wrapper) {
        var trigger = wrapper.querySelector('[data-dropdown-toggle]');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var willOpen = !wrapper.classList.contains('is-open');

            document.querySelectorAll('[data-dropdown].is-open').forEach(function (other) {
                other.classList.remove('is-open');
                var otherTrigger = other.querySelector('[data-dropdown-toggle]');
                if (otherTrigger) {
                    otherTrigger.setAttribute('aria-expanded', 'false');
                }
            });

            wrapper.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-dropdown]')) {
            return;
        }

        document.querySelectorAll('[data-dropdown].is-open').forEach(function (wrapper) {
            wrapper.classList.remove('is-open');
            var trigger = wrapper.querySelector('[data-dropdown-toggle]');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        body.classList.remove('nav-open');
        document.querySelectorAll('[data-dropdown].is-open').forEach(function (wrapper) {
            wrapper.classList.remove('is-open');
        });
    });

    /* ---------- Quantity steppers ---------- */
    document.querySelectorAll('[data-qty]').forEach(function (stepper) {
        var input = stepper.querySelector('input');

        if (!input) {
            return;
        }

        stepper.querySelectorAll('[data-step]').forEach(function (button) {
            button.addEventListener('click', function () {
                var step = parseInt(button.getAttribute('data-step'), 10) || 0;
                var min = parseInt(input.getAttribute('min') || '1', 10);
                var max = parseInt(input.getAttribute('max') || '99', 10);
                var next = (parseInt(input.value, 10) || min) + step;

                input.value = Math.min(max, Math.max(min, next));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    /* ---------- Auto-submit cart quantities ---------- */
    document.querySelectorAll('[data-auto-submit]').forEach(function (form) {
        var timer = null;

        form.addEventListener('change', function () {
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                form.submit();
            }, 600);
        });
    });

    /* ---------- Destructive action confirmation ---------- */
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Are you sure?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    /* ---------- Flash messages ---------- */
    document.querySelectorAll('[data-dismiss]').forEach(function (button) {
        button.addEventListener('click', function () {
            var alert = button.closest('.alert');

            if (alert) {
                alert.classList.add('is-leaving');
                window.setTimeout(function () {
                    alert.remove();
                }, 220);
            }
        });
    });

    /* ---------- Condensed header on scroll ---------- */
    var header = document.querySelector('[data-header]');

    if (header) {
        var updateHeader = function () {
            header.classList.toggle('is-scrolled', window.scrollY > 6);
        };

        window.addEventListener('scroll', updateHeader, { passive: true });
        updateHeader();
    }

    /* ---------- Password visibility ---------- */
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-password-toggle'));

            if (!input) {
                return;
            }

            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Hide' : 'Show';
        });
    });

    /* ---------- Admin sidebar on small screens ---------- */
    var adminToggle = document.querySelector('[data-admin-toggle]');

    if (adminToggle) {
        adminToggle.addEventListener('click', function () {
            body.classList.toggle('admin-nav-open');
        });
    }

    /* ---------- Sort menu ----------
       Same open/close behaviour as the account and category dropdowns. */
    document.querySelectorAll('[data-sort-menu]').forEach(function (menu) {
        var toggle = menu.querySelector('[data-sort-toggle]');

        if (!toggle) {
            return;
        }

        var setOpen = function (open) {
            menu.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            setOpen(!menu.classList.contains('is-open'));
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && menu.classList.contains('is-open')) {
                setOpen(false);
                toggle.focus();
            }
        });
    });

    /* ---------- Scroll reveal ----------
       Cards and section headings fade up once, staggered by their position
       inside a row. Anything already in view on load still animates, so the
       first screen never looks static; reduced-motion users get no motion. */
    var revealTargets = document.querySelectorAll(
        '.product-card, .category-card, .feature, .stat-card, .order-card, .promo, .section__head'
    );
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window) || !revealTargets.length) {
        revealTargets.forEach(function (el) { el.classList.add('reveal', 'is-in'); });
    } else {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-in');
                revealObserver.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });

        revealTargets.forEach(function (el) {
            var siblings = el.parentElement ? Array.prototype.slice.call(el.parentElement.children) : [];
            var position = siblings.indexOf(el);
            var step = Math.min(position < 0 ? 0 : position, 4);

            el.classList.add('reveal');
            el.style.setProperty('--reveal-delay', step * 70 + 'ms');
            revealObserver.observe(el);
        });
    }

    /* ---------- Loading states ----------
       Feedback for the gap between clicking submit and the next page arriving.
       The button keeps its footprint and spins, so the layout never jumps.
       Forms that ask for confirmation first are left alone. */
    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!form || form.tagName !== 'FORM' || form.hasAttribute('data-confirm')) {
            return;
        }

        window.setTimeout(function () {
            if (event.defaultPrevented) {
                return;
            }

            var button = event.submitter || form.querySelector('[type="submit"]');

            if (!button) {
                return;
            }

            button.classList.add('is-loading');
            button.setAttribute('aria-busy', 'true');
            form.setAttribute('aria-busy', 'true');
        }, 0);
    }, false);

    /* ---------- Counter feedback ----------
       A cart or wishlist number that changes gets one small pop, so the result
       of an add or remove is visible without hunting for the flash message. */
    var counters = document.querySelectorAll('.icon-btn__badge');

    if (counters.length) {
        var counterKey = 'skistore:counters';
        var previousCounts = {};

        try {
            previousCounts = JSON.parse(window.sessionStorage.getItem(counterKey) || '{}') || {};
        } catch (error) {
            previousCounts = {};
        }

        var currentCounts = {};

        counters.forEach(function (badge, index) {
            var link = badge.closest('a');
            var name = link && link.getAttribute('aria-label') ? link.getAttribute('aria-label') : 'counter-' + index;
            var value = parseInt(badge.textContent, 10) || 0;

            currentCounts[name] = value;

            if (typeof previousCounts[name] === 'number' && previousCounts[name] !== value) {
                badge.classList.add('is-bumped');
                window.setTimeout(function () {
                    badge.classList.remove('is-bumped');
                }, 700);
            }
        });

        try {
            window.sessionStorage.setItem(counterKey, JSON.stringify(currentCounts));
        } catch (error) {
            /* Private mode: the pop is a nicety, not a requirement. */
        }
    }
})();
