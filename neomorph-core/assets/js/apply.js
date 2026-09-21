/**
 * Recruitment wizard extras: review summary before submit.
 */
(function () {
	'use strict';
	document.querySelectorAll('[data-neo-wizard]').forEach(function (wizard) {
		wizard.addEventListener('submit', function (e) {
			var name = wizard.querySelector('[name="app_name"]');
			var phone = wizard.querySelector('[name="app_phone"]');
			if (name && !name.value.trim()) {
				e.preventDefault();
				alert('نام را وارد کنید.');
				return;
			}
			if (phone && !/^09[0-9]{9}$/.test(phone.value.replace(/\D/g, ''))) {
				e.preventDefault();
				alert('شماره موبایل معتبر وارد کنید (مثال: 09123456789).');
			}
		});
	});
})();
