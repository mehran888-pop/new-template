/**
 * OTP flows (login + register) and ticket reply AJAX.
 */
(function () {
	'use strict';

	var cfg = window.nmcOtp || {};

	function msg(form, text, isError) {
		var el = form.querySelector('.neo-form__msg');
		if (el) {
			el.textContent = text || '';
			el.classList.toggle('is-error', !!isError);
		}
	}

	function post(data) {
		var body = new URLSearchParams();
		Object.keys(data).forEach(function (k) { body.append(k, data[k]); });
		return fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body }).then(function (r) { return r.json(); });
	}

	var timerHandle = null;

	function startTimer(form, seconds) {
		var text = form.querySelector('[data-otp-timer-text]');
		var resend = form.querySelector('[data-otp-resend]');
		if (timerHandle) { clearInterval(timerHandle); }
		if (resend) { resend.disabled = true; }
		var left = seconds;
		timerHandle = setInterval(function () {
			left -= 1;
			if (text) { text.textContent = left > 0 ? left + ' ثانیه تا ارسال مجدد' : ''; }
			if (left <= 0) {
				clearInterval(timerHandle);
				if (resend) { resend.disabled = false; }
			}
		}, 1000);
	}

	document.addEventListener('click', function (e) {
		var form = e.target.closest('[data-otp-form]');
		if (!form) { return; }

		if (e.target.closest('[data-otp-send]') || e.target.closest('[data-otp-resend]')) {
			e.preventDefault();
			var phoneEl = form.querySelector('input[name="phone"]');
			if (!phoneEl || !phoneEl.value) { msg(form, 'شماره موبایل را وارد کنید.', true); return; }
			msg(form, cfg.messages ? cfg.messages.sending : '…');
			post({
				action: 'nmc_otp_send',
				nonce: cfg.nonce,
				phone: phoneEl.value
			}).then(function (res) {
				msg(form, res.data && res.data.message ? res.data.message : (res.success ? 'ارسال شد' : 'خطا'), !res.success);
				if (res.success) {
					var row = form.querySelector('.otp-verify-row');
					if (row) { row.hidden = false; }
					startTimer(form, 120);
					var boxes = form.querySelector('.otp-boxes input');
					if (boxes) { boxes.focus(); }
				}
			}).catch(function () { msg(form, cfg.messages ? cfg.messages.error : 'خطا', true); });
		}

		if (e.target.closest('[data-otp-verify]')) {
			e.preventDefault();
			var phoneEl2 = form.querySelector('input[name="phone"]');
			var codeEl = form.querySelector('input[name="otp_code"]');
			var code = codeEl ? codeEl.value : '';
			if (!code) {
				// Collect from boxes.
				code = Array.prototype.map.call(form.querySelectorAll('.otp-boxes input'), function (i) { return i.value; }).join('');
			}
			if (!code || code.length < 4) { msg(form, 'کد تأیید را کامل وارد کنید.', true); return; }

			var name = '';
			var first = form.querySelector('input[name="first_name"]');
			var last = form.querySelector('input[name="last_name"]');
			if (first || last) {
				name = ((first && first.value) || '') + ' ' + ((last && last.value) || '');
			}
			var emailEl = form.querySelector('input[name="email"]');

			msg(form, cfg.messages ? cfg.messages.verify : '…');
			post({
				action: 'nmc_otp_verify',
				nonce: cfg.nonce,
				phone: phoneEl2 ? phoneEl2.value : '',
				code: code,
				mode: form.closest('[data-mode]') ? form.closest('[data-mode]').getAttribute('data-mode') : 'login',
				name: name.trim(),
				email: emailEl ? emailEl.value : ''
			}).then(function (res) {
				var data = res.data || {};
				msg(form, data.message || '', !res.success);
				if (res.success && data.redirect) {
					window.location.href = data.redirect;
				}
			}).catch(function () { msg(form, cfg.messages ? cfg.messages.error : 'خطا', true); });
		}
	});
})();
