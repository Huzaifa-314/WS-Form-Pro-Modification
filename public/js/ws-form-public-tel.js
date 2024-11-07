(function($) {

	'use strict';

	// Adds international telephone input elements
	$.WS_Form.prototype.form_tel = function() {

		var ws_this = this;

		// Get tel objects
		var tel_objects = $('[data-intl-tel-input]:not([data-init-intl-tel-input])', this.form_canvas_obj);
		if(!tel_objects.length) { return false;}

		// Process each tel object
		tel_objects.each(function() {

			// Flag so it only initializes once
			$(this).attr('data-init-intl-tel-input', '');

			// Stylesheet
			if(!$('#wsf-intl-tel-input').length) {

				var image_path = (ws_form_settings.url_cdn == 'cdn') ? 'https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.13/build/img/' : ws_form_settings.url_plugin + 'public/images/external/';

				$('body').append("<style id=\"wsf-intl-tel-input\">\n	.iti { width: 100%; }\n	.iti__flag { background-image: url(\"" + image_path + "flags.png\");}\n	.iti--allow-dropdown input, .iti--allow-dropdown input[type=tel], .iti--allow-dropdown input[type=text], .iti--separate-dial-code input, .iti--separate-dial-code input[type=tel], .iti--separate-dial-code input[type=text] {\n		padding-right: 6px;\n		padding-left: 52px;\n		margin-left: 0;\n	}\n	@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {\n		.iti__flag { background-image: url(\"" + image_path + "flags@2x.png\"); }\n	}\n\n");
			}

			// Build config
			var config = {

				utilsScript: ((ws_form_settings.url_cdn == 'cdn') ? 'https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.13/build/js/utils.js' : ws_form_settings.url_plugin + 'public/js/external/utils.js?ver=17.0.13')
			}

			// Get field wrapper
			var field_wrapper_obj = ws_this.get_field_wrapper($(this));

			// Get field ID
			var field_id = ws_this.get_field_id($(this));

			// Get field
			var field = ws_this.field_data_cache[field_id];

			// Config - Allow dropdown
			config.allowDropdown = (ws_this.get_object_meta_value(field, 'intl_tel_input_allow_dropdown', 'on') == 'on');

			// Config - Auto placeholder
			config.autoPlaceholder = (ws_this.get_object_meta_value(field, 'intl_tel_input_auto_placeholder', 'on') == 'on') ? 'polite' : 'off';

			// Config - National mode
			config.nationalMode = (ws_this.get_object_meta_value(field, 'intl_tel_input_national_mode', 'on') == 'on');

			// Config - Separate dial code
			config.separateDialCode = (ws_this.get_object_meta_value(field, 'intl_tel_input_separate_dial_code', '') == 'on');

			// Config - Initial country
			config.initialCountry = ws_this.get_object_meta_value(field, 'intl_tel_input_initial_country', '');

			// Config - Geolookup
			if(config.initialCountry == 'auto') {

				config.geoIpLookup = function(callback) {

					$.get('https://ipinfo.io', function() {}, 'jsonp').always(function(resp) {

						var country_code = (resp && resp.country) ? resp.country : 'us';

						callback(country_code);
					});
				};
			}

			// Config - Only countries
			var only_countries = ws_this.get_object_meta_value(field, 'intl_tel_input_only_countries', []);

			if(
				(typeof(only_countries) === 'object') &&
				(only_countries.length > 0)
			) {

				config.onlyCountries = only_countries.map(function(row) { return row.country_alpha_2; });
			}

			// Config - Preferred countries
			var preferred_countries = ws_this.get_object_meta_value(field, 'intl_tel_input_preferred_countries', []);

			if(
				(typeof(preferred_countries) === 'object') &&
				(preferred_countries.length > 0)
			) {

				config.preferredCountries = preferred_countries.map(function(row) { return row.country_alpha_2; });
			}

			// Initialize intlTelInput
			var iti = window.intlTelInput($(this)[0], config);

			// Set flag container height (so invalid feedback does not break the styling)
			$('.iti__flag-container', field_wrapper_obj).css({height:$('input', field_wrapper_obj).outerHeight()});

			// Custom invalid feedback text
			var invalid_feedback_obj = ws_this.get_invalid_feedback_obj($(this));

			// Move invalid feedback div
			invalid_feedback_obj.insertAfter($(this));

			// Validation
			$(this).on('keyup change input', function() {

				// Get iti instance
				var iti = window.intlTelInputGlobals.getInstance($(this)[0]);

				// Check if valid
				if(
					($(this).val() == '') ||
					iti.isValidNumber()
				) {

					// Reset feedback
					ws_this.set_invalid_feedback($(this), '');

				} else {

					// Get field ID
					var field_id = ws_this.get_field_id($(this));

					// Get field
					var field = ws_this.field_data_cache[field_id];

					// Config - Allow dropdown
					var intl_tel_input_errors = [

						ws_this.get_object_meta_value(field, 'intl_tel_input_label_number', ws_this.language('iti_number')),
						ws_this.get_object_meta_value(field, 'intl_tel_input_label_country_code', ws_this.language('iti_country_code')),
						ws_this.get_object_meta_value(field, 'intl_tel_input_label_short', ws_this.language('iti_short')),
						ws_this.get_object_meta_value(field, 'intl_tel_input_label_long', ws_this.language('iti_long')),
						ws_this.get_object_meta_value(field, 'intl_tel_input_label_number', ws_this.language('iti_number'))
					];

					// Get error number
					var error_code = iti.getValidationError();

					// Get invalid feedback
					var invalid_feedback = (typeof(intl_tel_input_errors[error_code]) !== 'undefined') ? intl_tel_input_errors[error_code] : '';

					// Invalid feedback
					ws_this.set_invalid_feedback($(this), invalid_feedback);
				}
			});

			// Fire real time form validation
			ws_this.form_validate_real_time_process(false);
		});
	}

})(jQuery);
