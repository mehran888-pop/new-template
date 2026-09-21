/**
 * Neomorph front-end behaviors:
 * mobile drawer, search toggle, sticky header state, mini cart drawer, OTP UI helper, panel tabs.
 */
(function () {
	'use strict';

	var Neo = window.NeomorphData || window.neomorphData || {};

	/* Mobile nav */
	document.addEventListener('click', function (e) {
		var toggle = e.target.closest('.nav-toggle');
		if (toggle) {
			var drawer = document.getElementById('mobile-drawer');
			if (drawer) {
				var open = toggle.getAttribute('aria-expanded') === 'true';
				toggle.setAttribute('aria-expanded', String(!open));
				drawer.hidden = open;
			}
		}

		/* Search toggle */
		var search = e.target.closest('.search-toggle');
		if (search) {
			var box = document.getElementById('header-search');
			if (box) {
				box.hidden = !box.hidden;
				if (!box.hidden) {
					var input = box.querySelector('input[type=search]');
					if (input) { input.focus(); }
				}
			}
		}

		/* Mini cart drawer */
		var cart = e.target.closest('.neo-cart__toggle');
		if (cart) {
			var drawer2 = cart.parentElement.querySelector('.neo-cart__drawer');
			if (drawer2) {
				var open2 = cart.getAttribute('aria-expanded') === 'true';
				cart.setAttribute('aria-expanded', String(!open2));
				drawer2.hidden = open2;
			}
		}

		/* Panel tab switching (no-page-load) */
		var tab = e.target.closest('[data-neo-tab]');
		if (tab) {
			e.preventDefault();
			var scope = tab.closest('.neo-panel, .neo-panel-widget-wrap, body');
			if (!scope) { return; }
			scope.querySelectorAll('[data-neo-tab]').forEach(function (t) { t.classList.remove('is-active'); });
			tab.classList.add('is-active');
			scope.querySelectorAll('.neo-panel__section').forEach(function (sec) {
				sec.hidden = sec.getAttribute('data-neo-panel') !== tab.getAttribute('data-neo-tab');
			});
		}
	});

	/* Sticky header shadow on scroll */
	var header = document.querySelector('.site-header.is-sticky, .neo-header-widget.is-sticky');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 12);
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* OTP boxes: auto-advance */
	document.querySelectorAll('.otp-boxes').forEach(function (wrap) {
		var inputs = wrap.querySelectorAll('input');
		inputs.forEach(function (input, idx) {
			input.addEventListener('input', function () {
				input.value = input.value.replace(/\D/g, '').slice(0, 1);
				if (input.value && inputs[idx + 1]) { inputs[idx + 1].focus(); }
				var code = '';
				inputs.forEach(function (i) { code += i.value; });
				var hidden = wrap.parentElement.querySelector('input[name="otp_code"]');
				if (hidden) { hidden.value = code; }
			});
			input.addEventListener('keydown', function (e) {
				if (e.key === 'Backspace' && !input.value && inputs[idx - 1]) { inputs[idx - 1].focus(); }
			});
		});
	});

	/* Countdown timer for resend */
	document.querySelectorAll('[data-otp-timer]').forEach(function (el) {
		var left = parseInt(el.getAttribute('data-otp-timer'), 10) || 120;
		var resend = el.parentElement.querySelector('[data-otp-resend]');
		if (resend) { resend.disabled = true; }
		var t = setInterval(function () {
			left -= 1;
			el.textContent = (Neo.i18n && Neo.i18n.loading ? '' : '') + left + ' ثانیه تا ارسال مجدد';
			if (left <= 0) {
				clearInterval(t);
				el.textContent = '';
				if (resend) { resend.disabled = false; }
			}
		}, 1000);
	});

	/* Multi-step wizard generic (recruitment / apply) */
	document.querySelectorAll('[data-neo-wizard]').forEach(function (wizard) {
		var steps = wizard.querySelectorAll('[data-neo-step]');
		var current = 0;
		var bar = wizard.querySelector('.neo-progress__bar');

		function show(i) {
			steps.forEach(function (s, idx) { s.hidden = idx !== i; });
			wizard.querySelectorAll('[data-neo-step-dot]').forEach(function (d, idx) {
				d.classList.toggle('is-active', idx === i);
			});
			if (bar) { bar.style.width = Math.round(((i + 1) / steps.length) * 100) + '%'; }
			current = i;
		}

		wizard.addEventListener('click', function (e) {
			var next = e.target.closest('[data-neo-next]');
			var prev = e.target.closest('[data-neo-prev]');
			if (next) {
				e.preventDefault();
				var stepEl = steps[current];
				var ok = true;
				stepEl.querySelectorAll('[required]').forEach(function (f) {
					if (!f.value) { ok = false; f.classList.add('is-invalid'); }
					else { f.classList.remove('is-invalid'); }
				});
				if (ok && current < steps.length - 1) { show(current + 1); }
			}
			if (prev) {
				e.preventDefault();
				if (current > 0) { show(current - 1); }
			}
		});
		show(0);
	});
})();
