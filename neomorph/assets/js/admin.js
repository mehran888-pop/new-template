/* Neomorph admin scripts: color picker, social repeater, slider sync, row repeater */
(function ($) {
	'use strict';
	$(function () {
		if ($.fn.wpColorPicker) {
			$('.neo-color-field').wpColorPicker({
				change: function () {
					var color = $(this).val();
					$(this).closest('.neo-color').find('.neo-color__chip').css('background', color);
				}
			});
			// Sync chip initially.
			$('.neo-color').each(function () {
				var val = $(this).find('.neo-color-field').val();
				if (val) { $(this).find('.neo-color__chip').css('background', val); }
			});
		}

		/* Slider ↔ number sync */
		$('.neo-slider__range').on('input', function () {
			var target = $(this).data('sync');
			$('#' + target).val($(this).val());
		});
		$('.neo-slider__num').on('input', function () {
			$(this).closest('.neo-slider').find('.neo-slider__range').val($(this).val());
		});

		/* Social rows */
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
		$(document).on('click', '.neomorph-remove-social, .nmc-remove-row', function () {
			var $row = $(this).closest('.neo-rows__row, .nmc-rows__row');
			var $wrap = $row.parent();
			if ($wrap.children().length > 1) {
				$row.remove();
			} else {
				$row.find('input, textarea').val('');
			}
		});

		/* Generic repeater (service features etc.) */
		$(document).on('click', '.nmc-add-row', function (e) {
			e.preventDefault();
			var tpl = $(this).data('template');
			if (!tpl) { return; }
			var $wrap = $($(this).data('target'));
			var html = $(tpl).html().replace(/__INDEX__/g, $wrap.children().length);
			$wrap.append(html);
		});

		/* Media picker buttons (service icon / project image) */
		$(document).on('click', '.nmc-media-pick', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var frame = wp.media({
				title: 'انتخاب تصویر',
				multiple: false,
				library: { type: 'image' }
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				$btn.closest('.nmc-media').find('input.nmc-media-id').val(att.id);
				$btn.closest('.nmc-media').find('img.nmc-media-preview').attr('src', att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url).show();
				$btn.closest('.nmc-media').find('.nmc-media-clear').show();
			});
			frame.open();
		});
		$(document).on('click', '.nmc-media-clear', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('.nmc-media');
			$wrap.find('input.nmc-media-id').val('');
			$wrap.find('img.nmc-media-preview').hide();
			$(this).hide();
		});
	});
})(jQuery);
