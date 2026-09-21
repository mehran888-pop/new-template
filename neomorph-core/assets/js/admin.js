/* Neomorph Core admin scripts */
(function ($) {
	'use strict';
	$(function () {
		// Confirm destructive actions / copy helpers.
		$(document).on('click', '[data-nmc-copy]', function (e) {
			e.preventDefault();
			var val = $(this).data('nmc-copy');
			navigator.clipboard.writeText(val);
		});
	});
})(jQuery);
