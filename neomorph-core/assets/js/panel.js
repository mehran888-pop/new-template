/**
 * Panel behaviors: ticket reply + reward redeem.
 */
(function ($) {
	'use strict';

	$(function () {
		var cfg = window.nmcPanel || {};
		if (!cfg.ajaxUrl) { return; }

		$(document).on('submit', '[data-ticket-reply]', function (e) {
			e.preventDefault();
			var $form = $(this);
			var $msg = $form.find('.neo-form__msg');
			$msg.text('…');
			$.post(cfg.ajaxUrl, {
				action: 'nmc_ticket_reply',
				nonce: cfg.nonce,
				ticket_id: $form.data('ticket'),
				message: $form.find('[name="message"]').val()
			}).done(function (res) {
				$msg.text(res && res.data && res.data.message ? res.data.message : (res.success ? 'ثبت شد' : 'خطا'));
				if (res && res.success) {
					setTimeout(function () { window.location.reload(); }, 900);
				}
			}).fail(function () { $msg.text('خطای شبکه'); });
		});

		$(document).on('click', '[data-redeem]', function (e) {
			e.preventDefault();
			var $btn = $(this);
			$btn.prop('disabled', true);
			$.post(cfg.ajaxUrl, {
				action: 'nmc_redeem_reward',
				nonce: cfg.nonce,
				reward: $btn.data('redeem')
			}).done(function (res) {
				if (res && res.success) {
					alert(res.data.message || 'ثبت شد');
					window.location.reload();
				} else {
					alert(res && res.data ? res.data : 'خطا');
					$btn.prop('disabled', false);
				}
			}).fail(function () {
				alert('خطای شبکه');
				$btn.prop('disabled', false);
			});
		});
	});
})(jQuery);
