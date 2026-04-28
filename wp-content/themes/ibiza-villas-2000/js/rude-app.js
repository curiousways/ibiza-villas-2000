/**
 * Pass 2 strip: legacy Foundation/Slick/tooltip deps removed — minimal jQuery for forms and toggles.
 */
(function ($) {
	'use strict';

	function getParameterByName(name) {
		name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	}

	function syncSearchFormFromQuery() {
		var js_location = getParameterByName('location');
		var js_min = getParameterByName('min');
		var $loc = $('.property-search-form-location');
		var $min = $('.property-search-form-min');
		if (!$loc.length) {
			return;
		}
		var map = {
			'san-antonio': 1,
			'ibiza-town': 2,
			'playa-den-bossa': 3,
			'san-rafel': 4,
			'north-island': 5,
			'san-josep': 6,
		};
		if (map.hasOwnProperty(js_location)) {
			$loc.find('option').eq(map[js_location]).prop('selected', true);
		}
		if (js_min !== '') {
			var idx = parseInt(js_min, 10);
			if (!isNaN(idx) && idx >= 1 && idx <= 10) {
				$min.find('option').eq(idx).prop('selected', true);
			}
		}
	}

	$(function () {
		var pretty = $('.pretty-name').first().text();
		if (pretty) {
			$('#propertyContactForm #input_1_5').val(pretty);
			$('form[id^="gform_"] input, form[id^="gform_"] textarea').each(function () {
				var id = $(this).attr('id');
				if (id && id.indexOf('input_') === 0 && id.indexOf('_5') !== -1 && $(this).val() === '') {
					$(this).val(pretty);
				}
			});
		}

		$('.property-more-info button').on('click', function (e) {
			e.preventDefault();
			$('.property-more-info .revealJs').slideToggle();
		});

		$('.revealBrands').on('click', function (e) {
			e.preventDefault();
			$('.brands.revealJs').slideToggle();
			$('html, body').animate({ scrollTop: $(window).scrollTop() + 300 });
		});

		$('.accordion h3').on('click', function (e) {
			e.preventDefault();
			$(this).toggleClass('active');
			$(this).next().toggle();
		});

		$('.searchToggle i, .closeSearch, .language-selector').on('click', function (e) {
			e.preventDefault();
			$('.search-top').slideToggle();
			$('html, body').animate({ scrollTop: 0 }, 0);
		});

		syncSearchFormFromQuery();
	});
})(jQuery);
