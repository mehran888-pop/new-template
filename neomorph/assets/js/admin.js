/* Neomorph admin scripts: color picker + social repeater */
(function ($) {
	'use strict';
	$(function () {
		if ($.fn.wpColorPicker) {
			$('.neo-color-field').wpColorPicker();
		}

		var $rows = $('#neomorph-socials');
		$('#neomorph-add-social').on('click', function () {
			if (!$rows.length) { return; }
			var index = $rows.children().length;
			var $clone = $rows.children().first().clone();
			$clone.find('input').each(function () {
				var name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
				$(this).attr('name', name).val('');
			});
			$rows.append($clone);
		});

		$(document).on('click', '.neomorph-remove-social', function () {
			if ($rows.children().length > 1) {
				$(this).closest('.neomorph-social-row').remove();
			} else {
				$(this).closest('.neomorph-social-row').find('input').val('');
			}
		});
	});
})(jQuery);
