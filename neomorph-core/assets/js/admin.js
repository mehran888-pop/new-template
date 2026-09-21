/* Neomorph Core admin scripts: copy helper, row repeater, media picker */
(function ($) {
	'use strict';
	$(function () {
		/* Copy-to-clipboard helper */
		$(document).on('click', '[data-nmc-copy]', function (e) {
			e.preventDefault();
			navigator.clipboard.writeText($(this).data('nmc-copy'));
		});

		/* Generic repeater rows */
		$(document).on('click', '.nmc-add-row', function (e) {
			e.preventDefault();
			var tpl = $(this).data('template');
			if (!tpl) { return; }
			var $wrap = $($(this).data('target'));
			var html = $(tpl).html().replace(/__INDEX__/g, $wrap.children().length);
			$wrap.append(html);
		});
		$(document).on('click', '.nmc-remove-row, .neo-rows__remove', function (e) {
			e.preventDefault();
			var $row = $(this).closest('.nmc-rows__row, .neo-rows__row');
			var $wrap = $row.parent();
			if ($wrap.children().length > 1) {
				$row.remove();
			} else {
				$row.find('input, textarea').val('');
			}
		});

		/* Media picker (service icon…) */
		$(document).on('click', '.nmc-media-pick', function (e) {
			e.preventDefault();
			if (typeof wp === 'undefined' || !wp.media) { return; }
			var $btn = $(this);
			var frame = wp.media({ title: 'انتخاب تصویر', multiple: false, library: { type: 'image' } });
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				var $wrap = $btn.closest('.nmc-media');
				$wrap.find('input.nmc-media-id').val(att.id);
				$wrap.find('img.nmc-media-preview')
					.attr('src', (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url)
					.show();
				$wrap.find('.nmc-media-clear').show();
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
