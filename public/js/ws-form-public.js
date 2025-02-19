(function($) {

	'use strict';

	// Set is_admin
	$.WS_Form.prototype.set_is_admin = function() { return false; }

	// One time init for admin page
	$.WS_Form.prototype.init = function() {

		// Build data cache
		this.data_cache_build();

		// Set global variables once for performance
		this.set_globals();
	}

	// Continue initialization after submit data retrieved
	$.WS_Form.prototype.init_after_get_submit = function(submit_retrieved) {

		// Debug
		if($.WS_Form.debug_rendered) {

			// Form data source debug
			if(this.submit_auto_populate !== false) { this.log('debug_action_get', this.submit_auto_populate['action_label']); }
			if(this.submit !== false) { this.log('debug_submit_loaded'); }
		}

		// Build form
		this.form_build();
	}

	// Set global variables once for performance
	$.WS_Form.prototype.set_globals = function() {

		// Get framework ID
		this.framework_id = $.WS_Form.settings_plugin.framework;

		// Get framework settings
		this.framework = $.WS_Form.frameworks.types[this.framework_id];

		// Get current framework
		this.framework_fields = this.framework['fields']['public'];

		// Get invalid_feedback placeholder mask
		this.invalid_feedback_mask_placeholder = '';
		if(typeof($.WS_Form.meta_keys['invalid_feedback']) !== 'undefined') {

			if(typeof($.WS_Form.meta_keys['invalid_feedback']['p']) !== 'undefined') {

				this.invalid_feedback_mask_placeholder = $.WS_Form.meta_keys['invalid_feedback']['p'];
			}
		}

		// Custom action URL
		this.form_action_custom = (this.form_obj.attr('action') != (ws_form_settings.url_ajax + 'submit'));

		// Get validated class
		var class_validated_array = (typeof(this.framework.fields.public.class_form_validated) !== 'undefined') ? this.framework.fields.public.class_form_validated : [];
		this.class_validated = class_validated_array.join(' ');

		// Render debug console
		if(typeof(this.debug) === 'function') { this.debug(); }

		// Hash
		if(
			ws_form_settings.wsf_hash &&
			(typeof(ws_form_settings.wsf_hash) === 'object')
		) {

			// Set hash from query string
			for(var hash_index in ws_form_settings.wsf_hash) {

				if(!ws_form_settings.wsf_hash.hasOwnProperty(hash_index)) { continue; }

				var wsf_hash = ws_form_settings.wsf_hash[hash_index];

				if(
					(typeof(wsf_hash.id) !== 'undefined') &&
					(typeof(wsf_hash.hash) !== 'undefined') &&
					(typeof(wsf_hash.token) !== 'undefined') &&
					(wsf_hash.id == this.form_id)
				) {

					this.hash_set(wsf_hash.hash, wsf_hash.token, true);
				}
			}

		} else {

			// Set hash from cookie
			this.hash_set(this.cookie_get('hash', ''), false, true);
		}

		// Visual editor?
		this.visual_editor = (typeof(this.form_canvas_obj.attr('data-visual-builder')) !== 'undefined');

		// Read submission data if hash is defined
		var ws_this = this;
		if(this.hash) {

			var url = 'submit/hash/' + this.hash + '/';
			if(this.token) { url += this.token + '/'; }

			// Call AJAX request
			$.WS_Form.this.api_call(url, 'GET', false, function(response) {

				if(typeof(response.data) !== 'undefined') {

					// Save the submissions data
					ws_this.submit = response.data;
				}

				// Initialize after getting submit
				ws_this.init_after_get_submit(true);

				// Finished with submit data
				ws_this.submit = false;

			}, function(response) {

				// Read auto populate data instead
				ws_this.read_json_populate();

				// Initialize after getting submit
				ws_this.init_after_get_submit(false);
			});

		} else {

			// Read auto populate data
			this.read_json_populate();

			// Initialize after getting submit
			this.init_after_get_submit(false);
		}
	}

	// Read auto populate data
	$.WS_Form.prototype.read_json_populate = function() {

		if(typeof(wsf_form_json_populate) !== 'undefined') {

			if(typeof(wsf_form_json_populate[this.form_id]) !== 'undefined') {

				this.submit_auto_populate = wsf_form_json_populate[this.form_id];
			}
		}
	}

	// Render a log message
	$.WS_Form.prototype.log = function(language_id, variable, log_class) {

		if(typeof(variable) == 'undefined') { variable = ''; }
		if(typeof(log_class) == 'undefined') { log_class = ''; }

		return ($.WS_Form.debug_rendered) ? this.debug_audit_add('log', this.language(language_id, variable, false).replace(/%s/g, variable), log_class) : false;
	}

	// Render an error message
	$.WS_Form.prototype.error = function(language_id, variable, error_class) {

		if(typeof(variable) == 'undefined') { variable = ''; }
		if(typeof(error_class) == 'undefined') { error_class = ''; }

		// Build error message
		var error_message = this.language(language_id, variable, false).replace(/%s/g, variable);

		// Show error message
//		if(!this.visual_editor && this.get_object_meta_value(this.form, 'submit_show_errors', true)) {

//			this.action_message(error_message);
//		}

		return $.WS_Form.debug_rendered ? this.debug_audit_add('error', error_message, error_class) : false;
	}

	// Render any interface elements that rely on the form object
	$.WS_Form.prototype.form_render = function() {

		// Timer
		this.form_timer();

		// Get conditional actions
		if(
			(typeof(this.form.meta.action) !== 'undefined')
		) {

			for(var key in this.form.meta.action) {

				if(!this.form.meta.action.hasOwnProperty(key)) { continue; }

				// Get action
				var action = this.form.meta.action[key];

				// Get action ID
				var action_id = action.id.toString();

				// Actions - All
				this.conditional_actions_run_action.push(action_id);

				// Actions - Save
				if(action.save) {

					this.conditional_actions_run_save.push(action_id);
				}

				// Actions - Submit
				if(action.submit) {

					this.conditional_actions_run_submit.push(action_id);
				}

				if(action.ga) {

					this.action_ga = true;
				}
			}
		}

		// Reset arrays
		this.conditional_actions_changed = false;
		this.signatures = [];
		this.signatures_by_name = [];
		this.recaptchas = [];
		this.recaptchas_v2_default = [];
		this.recaptchas_v2_invisible = [];
		this.recaptchas_v3_default = [];
		this.recaptchas_conditions = [];
		this.hcaptchas = [];
		this.hcaptchas_default = [];
		this.hcaptchas_invisible = [];
		this.turnstiles = [];
		this.turnstiles_default = [];

		// Initialize framework
		this.form_framework();

		// Form preview
		this.form_preview();

		// Groups - Tabs - Initialize
		if(typeof(this.form_tab) === 'function') { this.form_tab(); }

		// Repeatable sections
		if(typeof(this.form_section_repeatable) === 'function') { this.form_section_repeatable(); }

		// Navigation
		this.form_navigation();

		// Analytics
		if(typeof(this.form_analytics) === 'function') { this.form_analytics(); }

		// Client side form validation
		this.form_validation();

		// Select min max
		if(typeof(this.form_select_min_max) === 'function') { this.form_select_min_max(); }

		// Select2
		if(typeof(this.form_select2) === 'function') { this.form_select2(); }

		// Dedupe value scope
		if(typeof(this.form_dedupe_value_scope) === 'function') { this.form_dedupe_value_scope(); }

		// Checkbox min max
		if(typeof(this.form_checkbox_min_max) === 'function') { this.form_checkbox_min_max(); }

		// Checkbox select all
		if(typeof(this.form_checkbox_select_all) === 'function') { this.form_checkbox_select_all(); }

		// Text input and textarea character and word count
		this.form_character_word_count();

		// Telephone
		if(typeof(this.form_tel) === 'function') { this.form_tel(); }

		// Dates
		if(typeof(this.form_date) === 'function') { this.form_date(); }

		// Colors
		if(typeof(this.form_color) === 'function') { this.form_color(); }

		// Signatures
		if(typeof(this.form_signature) === 'function') { this.form_signature(); }

		// Honeypot
		this.form_honeypot();

		// reCAPTCHA
		if(typeof(this.form_recaptcha) === 'function') { this.form_recaptcha(); }

		// hCAPTCHA
		if(typeof(this.form_hcaptcha) === 'function') { this.form_hcaptcha(); }

		// Turnstile
		if(typeof(this.form_turnstile) === 'function') { this.form_turnstile(); }

		// Label
		this.form_label();
		// Legal
		if(typeof(this.form_legal) === 'function') { this.form_legal(); }

		// Rating
		if(typeof(this.form_rating) === 'function') { this.form_rating(); }

		// Google Map
		if(typeof(this.form_google_map) === 'function') { this.form_google_map(); }

		// Google Place Search
		if(typeof(this.form_google_map) === 'function') { this.form_google_address(); }

		// E-Commerce
		if(typeof(this.form_ecommerce) === 'function') { this.form_ecommerce(); }

		// File inputs
		if(typeof(this.form_file) === 'function') { this.form_file(); }

		// Client side form conditions (Initial run = true)
		if(typeof(this.form_conditional) === 'function') { this.form_conditional(true); }

		// Conversational
		if(typeof(this.form_conversational) === 'function') { this.form_conversational(); }

		// Progress
		if(typeof(this.form_progress) === 'function') { this.form_progress(); }

		// Range sliders
		this.form_help_value();

		// Text areas
		if(typeof(this.form_textarea) === 'function') { this.form_textarea(); }

		// Required
		this.form_required();

		// Input masks
		this.form_inputmask();

		// Tracking
		if(typeof(this.form_tracking) === 'function') { this.form_tracking(); }

		// Password visibility toggle
		if(typeof(this.form_password_visibility_toggle) === 'function') { this.form_password_visibility_toggle(); }

		// Password generate
		if(typeof(this.form_password_generate) === 'function') { this.form_password_generate(); }

		// Password strength meter
		if(typeof(this.form_password_strength_meter) === 'function') { this.form_password_strength_meter(); }

		// Credit Card
		this.form_credit_card();
		// Transform
		this.form_transform();

		// Bypass
		this.form_bypass_enabled = true;
		this.form_bypass(false);

		// Form validation - Real time
		this.form_validate_real_time();
		// Calculations
		if(typeof(this.form_calc) === 'function') { this.form_calc(); }

		// Form stats
		this.form_stat();

		// Select Cascading
		if(typeof(this.form_cascade) === 'function') { this.form_cascade(); }
		// Tab validation
		if(typeof(this.form_tab_validation) === 'function') { this.form_tab_validation(); }
		// Log
		this.log('debug_form_rendered');

		// Trigger rendered event
		this.trigger('rendered');

		// Section repeatable hidden field (This triggers repeatable section events also)
		if(typeof(this.section_repeatable_hidden_field) === 'function') { this.section_repeatable_hidden_field(); }

		// Set data-wsf-rendered attribute
		this.form_obj.attr('data-wsf-rendered', '');
	}

	// Duration tracking timer
	$.WS_Form.prototype.form_timer = function() {

		// Check for date start cookie
		this.date_start = this.cookie_get('date_start', false);

		if((this.date_start === false) || isNaN(this.date_start) || (this.date_start == '')) {

			this.date_start = new Date().getTime();

			// Set cookie if duration track enabled
			if(this.get_object_meta_value(this.form, 'tracking_duration', 'on') == 'on') {

				this.cookie_set('date_start', this.date_start, false);
			}
		}
	}

	// Trigger events
	$.WS_Form.prototype.trigger = function(slug) {

		// New method
		var action_type = 'wsf-' + slug;
		$(document).trigger(action_type, [this.form, this.form_id, this.form_instance_id, this.form_obj, this.form_canvas_obj]);
		this.log('log_trigger', action_type, 'event');

		// Legacy method - Instance
		var trigger_instance = 'wsf-' + slug + '-instance-' + this.form_instance_id;
		$(window).trigger(trigger_instance);

		// Legacy method - Form
		var trigger_form = 'wsf-' + slug + '-form-' + this.form_id;
		$(window).trigger(trigger_form);
	}

	// Initialize JS
	$.WS_Form.prototype.form_framework = function() {

		// Add framework form attributes
		if(
			(typeof(this.framework.form.public) !== 'undefined') &&
			(typeof(this.framework.form.public.attributes) === 'object')
		) {

			for(var attribute in this.framework.form.public.attributes) {

				var attribute_value = this.framework.form.public.attributes[attribute];

				this.form_obj.attr(attribute, attribute_value);
			}
		}

		// Check framework init_js
		if(typeof(this.framework.init_js) !== 'undefined') {

			// Framework init JS values
			var framework_init_js_values = {'form_canvas_selector': '#' + this.form_obj_id};
			var framework_init_js = this.mask_parse(this.framework.init_js, framework_init_js_values);

			try {

				$.globalEval('(function($) { ' + framework_init_js + ' })(jQuery);');

			} catch(e) {

				this.error('error_js', action_javascript);
			}
		}
	}

	// Form - Reset
	$.WS_Form.prototype.form_reset = function(e) {

		var ws_this = this;

		// Trigger
		this.trigger('reset-before');

		// Unmark as validated
		this.form_obj.removeClass(this.class_validated);

		// HTML form reset
		this.form_obj[0].reset();
		// Reset special fields
		for(var key in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(key)) { continue; }

			var field = this.field_data_cache[key];

			var field_id = field.id;
			var field_name = this.field_name_prefix + field_id;

			var field_type_config = $.WS_Form.field_type_cache[field.type];
			var trigger = (typeof(field_type_config.trigger) !== 'undefined') ? field_type_config.trigger : 'change';

			var field_obj = $('[name="' + field_name + '"], [name^="' + field_name + '["]', this.form_canvas_obj);

			switch(field.type) {

				case 'textarea' :
					
					field_obj.each(function() {

						if(typeof(ws_this.textarea_set_value) === 'function') { ws_this.textarea_set_value($(this), $(this).val()); }

						$(this).trigger(trigger);
					});

					break;

				case 'color' :

					field_obj.each(function() {

						if($(this).hasClass('minicolors-input')) {

							$(this).minicolors('value', $(this).val()).trigger(trigger);
						}
					});

					break;

				case 'file' :

					// Regular file uploads
					field_obj.each(function() {

						$(this).trigger(trigger);
					});

					// Dropzone file uploads
					if(
						(typeof(ws_this.form_file_dropzonejs_populate) === 'function') &&
						(typeof(Dropzone) !== 'undefined')
					) {

						$('[name="' + field_name + '"][data-file-type="dropzonejs"], [name^="' + field_name + '["][data-file-type="dropzonejs"]', this.form_canvas_obj).each(function() {

							ws_this.form_file_dropzonejs_populate($(this), false);
						});
					}

					break;

				default :

					field_obj.each(function() {

						$(this).trigger(trigger);
					});
			}
		}

		// Clear signatures
		if(typeof(this.signatures_clear) === 'function') { this.signatures_clear(); }
		// Reset reCAPTCHA
		if(typeof(this.recaptcha_reset) === 'function') { this.recaptcha_reset(); }

		// Reset hCaptcha
		if(typeof(this.hcaptcha_reset) === 'function') { this.hcaptcha_reset(); }

		// Reset turnstile
		if(typeof(this.turnstile_reset) === 'function') { this.turnstile_reset(); }
		// Form conditional=
		if(typeof(this.form_conditional) === 'function') { this.form_conditional(); }
		// Trigger
		this.trigger('reset-complete');
	}

	// Form - Clear
	$.WS_Form.prototype.form_clear = function() {

		var ws_this = this;

		// Trigger
		this.trigger('clear-before');

		// Unmark as validated
		this.form_obj.removeClass(this.class_validated);

		// Clear fields
		for(var key in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(key)) { continue; }

			var field = this.field_data_cache[key];

			var field_id = field.id;
			var field_name = this.field_name_prefix + field_id;

			var field_type_config = $.WS_Form.field_type_cache[field.type];
			var trigger = (typeof(field_type_config.trigger) !== 'undefined') ? field_type_config.trigger : 'change';

			var field_obj = $('[name="' + field_name + '"], [name^="' + field_name + '["]', this.form_canvas_obj);

			// Clear value
			switch(field.type) {

				case 'checkbox' :
				case 'price_checkbox' :
				case 'radio' :
				case 'price_radio' :

					field_obj.each(function() {

						if($(this).is(':checked')) {
	
							$(this).prop('checked', false).trigger(trigger);
						}
					});

					break;

				case 'select' :
				case 'price_select' :

					$('[name="' + field_name + '"], [name^="' + field_name + '["] option', this.form_canvas_obj).each(function() {

						if($(this).is(':selected')) {
	
							$(this).prop('selected', false);
							$(this).closest('select').trigger(trigger);
						}
					});

					break;

				case 'textarea' :

					field_obj.each(function() {

						if($(this).val() != '') {

							$(this).val('').trigger(trigger);

							if(typeof(ws_this.textarea_set_value) === 'function') { ws_this.textarea_set_value($(this), ''); }
						}
					});

					break;

				case 'color' :

					field_obj.each(function() {

						if($(this).val() != '') {

							$(this).val('').trigger(trigger);

							if($(this).hasClass('minicolors-input')) {

								$(this).minicolors('value', '');
							}
						}
					});

					break;

				case 'file' :

					// Regular file uploads
					field_obj.each(function() {

						if($(this).val() != '') {

							$(this).val('').trigger(trigger);
						}
					});

					// Dropzone file uploads
					if(
						(typeof(ws_this.form_file_dropzonejs_populate) === 'function') &&
						(typeof(Dropzone) !== 'undefined')
					) {

						$('[name="' + field_name + '"][data-file-type="dropzonejs"], [name^="' + field_name + '["][data-file-type="dropzonejs"]', this.form_canvas_obj).each(function() {

							ws_this.form_file_dropzonejs_populate($(this), true);
						});
					}

					break;

				default:

					field_obj.each(function() {

						if($(this).val() != '') {

							$(this).val('').trigger(trigger);
						}
					});
			}
		}

		// Clear signatures
		if(typeof(this.signatures_clear) === 'function') { this.signatures_clear(); }
		// Reset reCAPTCHA
		if(typeof(this.recaptcha_reset) === 'function') { this.recaptcha_reset(); }

		// Reset hCaptcha
		if(typeof(this.hcaptcha_reset) === 'function') { this.hcaptcha_reset(); }

		// Reset turnstile
		if(typeof(this.turnstile_reset) === 'function') { this.turnstile_reset(); }
		// Form conditional
		if(typeof(this.form_conditional) === 'function') { this.form_conditional(); }
		// Trigger
		this.trigger('clear-complete');
	}

	// Form reload
	$.WS_Form.prototype.form_reload = function() {

		// Read submission data if hash is defined
		var ws_this = this;
		if(this.hash != '') {

			// Call AJAX request
			$.WS_Form.this.api_call('submit/hash/' + this.hash, 'GET', false, function(response) {

				// Save the submissions data
				ws_this.submit = response.data;

				ws_this.form_reload_after_get_submit(true);

				// Finished with submit data
				ws_this.submit = false;

			}, function(response) {

				ws_this.form_reload_after_get_submit(false);
			});

		} else {

			// Reset submit
			this.submit = false;

			this.form_reload_after_get_submit(false);
		}
	}

	// Form reload - After get submit
	$.WS_Form.prototype.form_reload_after_get_submit = function(submit_retrieved) {

		// Clear any messages
		$('[data-wsf-message][data-wsf-instance-id="' + this.form_instance_id + '"]').remove();

		// Show the form
		this.form_canvas_obj.show();

		// Reset form tag
		this.form_canvas_obj.removeClass(this.class_validated)

		// Clear ecommerce real time validation hooks
		this.form_validation_real_time_hooks = [];

		// Empty form object
		this.form_canvas_obj.empty();

		// Build form
		this.form_build();
	}

	// Form - Hash reset
	$.WS_Form.prototype.form_hash_clear = function() {

		// Clear hash variable
		this.hash = '';

		// Clear hash cookie
		this.cookie_clear('hash')

		// Update debug interface
		if($.WS_Form.debug_rendered) {

			this.debug_info('debug_info_hash', this.language('debug_hash_empty'));
			this.debug_info('debug_info_submit_count', 0);
		}

		// Log
		this.log('log_hash_clear');
	}

	// Form - Transform
	$.WS_Form.prototype.form_transform = function() {

		var ws_this = this;

		$('[data-wsf-transform]:not([data-wsf-transform-init])', this.form_canvas_obj).each(function() {

			// Mark so it is not initialized again
			$(this).attr('data-wsf-transform-init', '');

			// Get transform method
			var transform_method = $(this).attr('data-wsf-transform');

			switch(transform_method) {

				// Uppercase
				case 'uc' :

					$(this).on('change input paste', function() {

						ws_this.form_transform_process_uppercase($(this));
					})

					ws_this.form_transform_process_uppercase($(this));

					break;

				// Lowercase
				case 'lc' :

					$(this).on('change input paste', function() {

						ws_this.form_transform_process_lowercase($(this));
					})

					ws_this.form_transform_process_lowercase($(this));

					break;

				// Capitalize
				case 'capitalize' :

					$(this).on('change input paste', function() {

						ws_this.form_transform_process_capitalize($(this));
					})

					ws_this.form_transform_process_capitalize($(this));

					break;

				// Sentence
				case 'sentence' :

					$(this).on('change input paste', function() {

						ws_this.form_transform_process_sentence($(this));
					})

					ws_this.form_transform_process_sentence($(this));

					break;
			}
		});
	}

	// Form - Transform - Process uppercase
	$.WS_Form.prototype.form_transform_process_uppercase = function(obj) {

		var input_value = obj.val();

		if(input_value && (typeof(input_value) === 'string')) {

			obj.val(input_value.toUpperCase());
		}
	}

	// Form - Transform - Process lowercase
	$.WS_Form.prototype.form_transform_process_lowercase = function(obj) {

		var input_value = obj.val();

		if(input_value && (typeof(input_value) === 'string')) {

			obj.val(input_value.toLowerCase());
		}
	}

	// Form - Transform - Process capitalize
	$.WS_Form.prototype.form_transform_process_capitalize = function(obj) {

		var input_value = obj.val();

		if(input_value && (typeof(input_value) === 'string')) {

			obj.val(this.ucwords(input_value.toLowerCase()));
		}
	}

	// Form - Transform - Process sentence
	$.WS_Form.prototype.form_transform_process_sentence = function(obj) {

		var input_value = obj.val();

		if(input_value && (typeof(input_value) === 'string')) {

			obj.val(this.ucfirst(input_value.toLowerCase()));
		}
	}

	// Form credit card
	$.WS_Form.prototype.form_credit_card = function() {

		var ws_this = this;

		$('input[data-credit-card]:not([data-init-credit-card])', this.form_canvas_obj).each(function() {

			// Set attribute so it doesn't initialize again
			$(this).attr('data-init-credit-card', '');

			// Set event
			$(this).on("input", function() {

				if(typeof($(this).inputmask) === 'undefined') { return; }

				var value = $(this).val().replace(/\D/g, '');
				var mask;

				if ((/^3[47]\d{0,13}$/).test(value)) {

					// American Express
					mask = '9999 999999 99999'

				} else if ((/^3(?:0[0-5]|[68]\d)\d{0,11}$/).test(value)) { 

					// Diner's Club
					mask = '9999 999999 9999'

				} else if ((/^\d{0,16}$/).test(value)) {

					// Other Credit Cards
					mask = '9999 9999 9999 9999'

				} else {

					// Unknown
					mask = 'remove'
				}

				if($(this).attr('data-currentmask') != mask) {

					$(this).attr('data-currentmask', mask)
					$(this).inputmask(mask, { placeholder: '', clearMaskOnLostFocus: false });
				}
			});
		});
	}

	// Form navigation
	$.WS_Form.prototype.form_navigation = function() {

		var ws_this = this;

		var group_count = $('.wsf-group-tabs', this.form_canvas_obj).children(':not([data-wsf-group-hidden])').length;

		// Buttons - Next
		$('[data-action="wsf-tab_next"]', this.form_canvas_obj).each(function() {

			// Remove existing click event
			$(this).off('click');

			// Get next group
			var group_next = $(this).closest('[data-group-index]').nextAll(':not([data-wsf-group-hidden])').first();

			// If there are no tabs, or no next tab, disable the next button
			if(
				(group_count <= 1) ||
				(!group_next.length)
			) {
				$(this).attr('disabled', '').attr('data-wsf-disabled', '');

			} else {

				if(typeof($(this).attr('data-wsf-disabled')) !== 'undefined') { $(this).removeAttr('disabled').removeAttr('data-wsf-disabled'); }
			}

			// If button is disabled, then don't initialize
			if(typeof($(this).attr('disabled')) !== 'undefined') { return; }

			// Add click event
			$(this).on('click', function() {

				var tab_validation = ws_this.get_object_meta_value(ws_this.form, 'tab_validation');
				var tab_validation_show = ws_this.get_object_meta_value(ws_this.form, 'tab_validation_show');

				if(tab_validation && tab_validation_show) {

					var group_obj = ws_this.get_group($(this));

					if(typeof(group_obj.attr('data-wsf-validated')) === 'undefined') {

						// Show validation errors
						group_obj.addClass('wsf-validated');

						// Focus object
						if(ws_this.get_object_meta_value(ws_this.form, 'invalid_field_focus', true)) {

							$(':invalid', group_obj).filter(':visible').not('fieldset').first().focus().trigger('focus');
						}

					} else {

						// Remove wsf-validated
						group_obj.removeClass('wsf-validated');

						// Progress to the next tab
						if(typeof(ws_this.form_tab_group_index_new) === 'function') {

							ws_this.form_tab_group_index_new($(this), group_next.attr('data-group-index'));
						}
					}

				} else {

					// Progress to the next tab
					if(typeof(ws_this.form_tab_group_index_new) === 'function') {

						ws_this.form_tab_group_index_new($(this), group_next.attr('data-group-index'));
					}
				}
			});
		});

		// Buttons - Previous
		$('[data-action="wsf-tab_previous"]', this.form_canvas_obj).each(function() {

			// Remove existing click event
			$(this).off('click');

			// Get previous group
			var group_previous = $(this).closest('[data-group-index]').prevAll(':not([data-wsf-group-hidden])').first();

			// If there are no tabs, or no previous tab, disable the previous button
			if(
				(group_count <= 1) ||
				(!group_previous.length)
			) {
				$(this).attr('disabled', '').attr('data-wsf-disabled', '');

			} else {

				if(typeof($(this).attr('data-wsf-disabled')) !== 'undefined') { $(this).removeAttr('disabled').removeAttr('data-wsf-disabled'); }
			}

			// If button is disabled, then don't initialize
			if(typeof($(this).attr('disabled')) !== 'undefined') { return; }

			// Add click event
			if(typeof(ws_this.form_tab_group_index_new) === 'function') {

				$(this).on('click', function() {

					ws_this.form_tab_group_index_new($(this), group_previous.attr('data-group-index'));
				});
			}
		});

		// Buttons - Save
		this.form_canvas_obj.off('click', '[data-action="wsf-save"]').on('click', '[data-action="wsf-save"]', function() {

			// Get field
			var field = ws_this.get_field($(this));

			if(typeof(field) !== 'undefined') {

				var validate_form = ws_this.get_object_meta_value(field, 'validate_form', '');

				if(validate_form) {

					ws_this.form_post_if_validated('save');

				} else {

					ws_this.form_post('save');
				}
			}
		});

		// Buttons - Reset
		this.form_canvas_obj.off('click', '[data-action="wsf-reset"]').on('click', '[data-action="wsf-reset"]', function(e) {

			// Prevent default
			e.preventDefault();

			ws_this.form_reset();
		});

		// Buttons - Clear
		this.form_canvas_obj.off('click', '[data-action="wsf-clear"]').on('click', '[data-action="wsf-clear"]', function() {

			ws_this.form_clear();
		});
	}

	// Get tab index object resides in
	$.WS_Form.prototype.get_group_index = function(obj) {

		// Get group
		var group_single = this.get_group(obj);
		if(group_single === false) { return false; }

		// Get group index
		var group_index = group_single.first().attr('data-group-index');
		if(group_index == undefined) { return false; }

		return parseInt(group_index, 10);
	}

	// Get group object resides in
	$.WS_Form.prototype.get_group = function(obj) {

		// Check that tabs exist
		var group_count = $('.wsf-tabs', this.form_canvas_obj).children(':visible').length;
		if(group_count <= 1) { return false; }

		// Get group
		var group_single = obj.closest('[data-group-index]');
		if(group_single.length == 0) { return false; }

		return group_single;
	}

	// Get section id from object
	$.WS_Form.prototype.get_section_id = function(obj) {

		var section_id = obj.closest('[id^="' + this.form_id_prefix + 'section-"]').attr('data-id');

		return (typeof(section_id) !== 'undefined') ? parseInt(section_id, 10) : false;
	}

	// Get section repeatable index from object
	$.WS_Form.prototype.get_section_repeatable_index = function(obj) {

		var section_repeatable_index = obj.closest('[id^="' + this.form_id_prefix + 'section-"]').attr('data-repeatable-index');

		return (section_repeatable_index > 0) ? parseInt(section_repeatable_index, 10) : 0;
	}

	// Get section repeatable suffix from object
	$.WS_Form.prototype.get_section_repeatable_suffix = function(obj) {

		var section_repeatable_index = this.get_section_repeatable_index(obj);

		return section_repeatable_index ? '-repeat-' + section_repeatable_index : '';
	}

	// Get field from obj
	$.WS_Form.prototype.get_field = function(obj) {

		var field_id = this.get_field_id(obj);

		return field_id ? this.field_data_cache[field_id] : false;
	}

	// Get field wrapper from object
	$.WS_Form.prototype.get_field_wrapper = function(obj) {

		return obj.closest('[data-id]')
	}

	// Get field id from object
	$.WS_Form.prototype.get_field_id = function(obj) {

		var field_id = obj.closest('[data-type]').attr('data-id');

		return (typeof(field_id) !== 'undefined') ? parseInt(field_id, 10) : false;
	}

	// Get field type from object
	$.WS_Form.prototype.get_field_type = function(obj) {

		var field_type = obj.closest('[data-type]').attr('data-type');

		return (typeof(field_type) !== 'undefined') ? field_type : false;
	}

	// Get help from object
	$.WS_Form.prototype.get_help_obj = function(obj) {

		var field_id = this.get_field_id(obj);
		var section_repeatable_suffix = this.get_section_repeatable_suffix(obj);

		return $('#' + this.form_id_prefix + 'help-' + field_id + section_repeatable_suffix, this.form_canvas_obj);
	}

	// Get invalid feedback from object
	$.WS_Form.prototype.get_invalid_feedback_obj = function(obj) {

		var field_id = this.get_field_id(obj);
		var section_repeatable_suffix = this.get_section_repeatable_suffix(obj);

		return $('#' + this.form_id_prefix + 'invalid-feedback-' + field_id + section_repeatable_suffix, this.form_canvas_obj);
	}

	// Set invalid feedback on object
	$.WS_Form.prototype.set_invalid_feedback = function(obj, message, object_row_id) {

		// Check for object row
		if(typeof(object_row_id) === 'undefined') { object_row_id = 0; }

		// Get invalid feedback obj
		var invalid_feedback_obj = this.get_invalid_feedback_obj(obj);

		// Get section ID
		var section_id = this.get_section_id(obj);

		// Get section repeatable index
		var section_repeatable_index = this.get_section_repeatable_index(obj);

		// Get field ID
		var field_id = this.get_field_id(obj);

		// Check for false message
		if(message === false) { message = invalid_feedback_obj.html(); }

		var message_invalid_feedback = message;

		// HTML 5 custom validity
		if(obj.length && obj[0].willValidate) {

			if(message !== '') {

				// Store message
				if(typeof(this.validation_message_cache[section_id]) === 'undefined') { this.validation_message_cache[section_id] = []; }
				if(typeof(this.validation_message_cache[section_id][section_repeatable_index]) === 'undefined') { this.validation_message_cache[section_id][section_repeatable_index] = []; }
				if(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id]) === 'undefined') { this.validation_message_cache[section_id][section_repeatable_index][field_id] = []; }

				this.validation_message_cache[section_id][section_repeatable_index][field_id][object_row_id] = message;

			} else {

				// Recall message
				if(
					(typeof(this.validation_message_cache[section_id]) !== 'undefined') &&
					(typeof(this.validation_message_cache[section_id][section_repeatable_index]) !== 'undefined') &&
					(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id]) !== 'undefined') &&
					(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id][object_row_id]) !== 'undefined')
				) {

					delete this.validation_message_cache[section_id][section_repeatable_index][field_id][object_row_id];
				}
			}

			// Set custom validity
			obj[0].setCustomValidity(message);

			// Run validate real time processing
			this.form_validate_real_time_process(false);
		}

		// Invalid feedback text
		if(invalid_feedback_obj.length) {

			if(message !== '') {

				// Store invalid feedback
				if(typeof(this.invalid_feedback_cache[section_id]) === 'undefined') { this.invalid_feedback_cache[section_id] = []; }
				if(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index]) === 'undefined') { this.invalid_feedback_cache[section_id][section_repeatable_index] = []; }
				if(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index][field_id]) === 'undefined') { this.invalid_feedback_cache[section_id][section_repeatable_index][field_id] = []; }

				if(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index][field_id][object_row_id]) === 'undefined') {

					this.invalid_feedback_cache[section_id][section_repeatable_index][field_id][object_row_id] = invalid_feedback_obj.html();
				}

				// Set invalid feedback
				invalid_feedback_obj.html(message);

			} else {

				// Recall invalid feedback
				if(
					(typeof(this.invalid_feedback_cache[section_id]) !== 'undefined') &&
					(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index]) !== 'undefined') &&
					(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index][field_id]) !== 'undefined') &&
					(typeof(this.invalid_feedback_cache[section_id][section_repeatable_index][field_id][object_row_id]) !== 'undefined')
				) {

					invalid_feedback_obj.html(this.invalid_feedback_cache[section_id][section_repeatable_index][field_id][object_row_id]);

					delete this.invalid_feedback_cache[section_id][section_repeatable_index][field_id][object_row_id];
				}
			}
		}
	}

	// Form preview
	$.WS_Form.prototype.form_preview = function() {

		if(this.form_canvas_obj[0].hasAttribute('data-preview')) {

			this.form_add_hidden_input('wsf_preview', 'true');
		}
	}

	// Honeypot
	$.WS_Form.prototype.form_honeypot = function() {

		// Honeypot
		var honeypot = this.get_object_meta_value(this.form, 'honeypot', false);

		if(honeypot) {

			// Add honeypot field
			var honeypot_hash = (this.form.published_checksum != '') ? this.form.published_checksum : ('honeypot_unpublished_' + this.form_id);

			// Build honeypot input
			var framework_type = $.WS_Form.settings_plugin.framework;
			var framework = $.WS_Form.frameworks.types[framework_type];
			var fields = this.framework['fields']['public'];
			var honeypot_attributes = (typeof(fields.honeypot_attributes) !== 'undefined') ? ' ' + fields.honeypot_attributes.join(' ') : '';

			// Add to form
			this.form_add_hidden_input('field_' + honeypot_hash, '', false, 'autocomplete="off" style="position: absolute; left: -9999em;"' + honeypot_attributes, false, 'text');

			// Debug
			this.log('log_honeypot', '', 'spam-protect');
		}
	}

	// Addition styling for labels
	$.WS_Form.prototype.form_label = function() {

		// Find all fields with inside label positioning
		$('.wsf-label-position-inside', this.form_canvas_obj).each(function() {

			// Get prefix object
			var prefix_obj = $('.wsf-input-group-prepend', $(this));

			// Check if a prefix exists
			if(prefix_obj.length) {

				// Get label object
				var label_obj = $('label', $(this));

				// Get existing left offset
				var left_offset_old = parseFloat(label_obj.css('left').replace('px', ''));

				// Get width of prefix
				var prefix_width = prefix_obj.outerWidth();

				// Calculate new left offset
				var left_offset_new = left_offset_old + prefix_width;

				// Set label object left CSS attribute
				label_obj.css('left', left_offset_new + 'px');
			}
		});
	}

	// Adds required string (if found in framework config) to all labels
	$.WS_Form.prototype.form_required = function() {

		var ws_this = this;

		// Get required label HTML
		var label_required = this.get_object_meta_value(this.form, 'label_required', false);
		if(!label_required) { return false; }

		var label_mask_required = this.get_object_meta_value(this.form, 'label_mask_required', '', true, true);
		if(label_mask_required == '') {

			// Use framework mask_required_label
			var framework_type = $.WS_Form.settings_plugin.framework;
			var framework = $.WS_Form.frameworks.types[framework_type];
			var fields = this.framework['fields']['public'];

			if(typeof(fields.mask_required_label) === 'undefined') { return false; }
			var label_mask_required = fields.mask_required_label;
			if(label_mask_required == '') { return false; }
		}

		// Get all labels in this form
		$('label', this.form_canvas_obj).each(function() {

			// Get 'for' attribute of label
			var label_for = $(this).attr('for');
			if(label_for === undefined) { return; }

			// Get field related to 'for'
			var field_obj = $('[id="' + label_for + '"]', ws_this.form_canvas_obj);
			if(!field_obj.length) { return; }

			// Check if field should be processed
			if(typeof(field_obj.attr('data-init-required')) !== 'undefined') { return; }

			// Check if field is required
			var field_required = (typeof(field_obj.attr('data-required')) !== 'undefined');

			// Check if the require string should be added to the parent label (e.g. for radios)
			var label_required_id = $(this).attr('data-label-required-id');
			if((typeof(label_required_id) !== 'undefined') && (label_required_id !== false)) {

				var label_obj = $('#' + label_required_id, ws_this.form_canvas_obj);

			} else {

				var label_obj = $(this);
			}

			// Check if wsf-required-wrapper span exists, if not, create it (You can manually insert it in config using #required)
			var required_wrapper = $('.wsf-required-wrapper', label_obj);
			if(!required_wrapper.length && field_required) {

				var required_wrapper_html = '<span class="wsf-required-wrapper"></span>';

				// If field is wrapped in label, find the first the first element to inject the required wrapper before
				var first_child = label_obj.children('div,[name]').first();

				// Add at appropriate place
				if(first_child.length) {

					first_child.before(required_wrapper_html);

				} else {

					label_obj.append(required_wrapper_html);
				}

				required_wrapper = $('.wsf-required-wrapper', label_obj);
			}

			if(field_required) {

				// Add it
				required_wrapper.html(label_mask_required);
				field_obj.attr('data-init-required', '');

			} else {

				// Remove it
				required_wrapper.html('');
				field_obj.removeAttr('data-init-required');
			}
		});
	}

	// Field required bypass
	$.WS_Form.prototype.form_bypass = function(conditional_initiated) {

		if(!this.form_bypass_enabled) { return; }

		var ws_this = this;

		// Look for number fields and add step="1" if none present
		// This ensures that if a number field is hidden that the data-step-bypass is added with step="any" so that the form submits
		$('input[type="number"]:not([step]):not([data-step-bypass])', this.form_canvas_obj).attr('step', 1);

		// Process attributes that should be bypassed if a field is hidden
		var attributes = {

			'required':						{'bypass': 'data-required-bypass', 'not': '[type="hidden"]'},
			'aria-required':				{'bypass': 'data-aria-required-bypass', 'not': '[type="hidden"]'},
			'min':							{'bypass': 'data-min-bypass', 'not': '[type="hidden"],[type="range"]'},
			'max':							{'bypass': 'data-max-bypass', 'not': '[type="hidden"],[type="range"]'},
			'minlength':					{'bypass': 'data-minlength-bypass', 'not': '[type="hidden"]'},
			'maxlength':					{'bypass': 'data-maxlength-bypass', 'not': '[type="hidden"]'},
			'pattern':						{'bypass': 'data-pattern-bypass', 'not': '[type="hidden"]'},
			'step':							{'bypass': 'data-step-bypass', 'not': '[type="hidden"],[type="range"]', 'replace': 'any'},
			'data-ecommerce-price':			{'bypass': 'data-ecommerce-price-bypass', 'not': '[data-ecommerce-persist]'},
			'data-ecommerce-cart-price':	{'bypass': 'data-ecommerce-cart-price-bypass', 'not': '[data-ecommerce-persist]'}
		};

		for(var attribute_source in attributes) {

			if(!attributes.hasOwnProperty(attribute_source)) { continue; }

			var attribute_config = attributes[attribute_source];

			var attribute_bypass = attribute_config.bypass;
			var attribute_not = attribute_config.not;
			var attribute_replace = (typeof(attribute_config.replace) !== 'undefined') ? attribute_config.replace : false;

			// If a group is visible, and contains fields that have a data bypass attribute, reset that attribute
			if($('[' + attribute_bypass + '-group]', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'group-"]:not([data-wsf-group-hidden]) [' + attribute_bypass + '-group]:not(' + attribute_not + ')', this.form_canvas_obj).attr(attribute_source, function() { return $(this).attr(attribute_bypass + '-group'); }).removeAttr(attribute_bypass + '-group');
			}

			// If a group is not visible, and contains validation attributes, add bypass attributes
			if($('[' + attribute_source + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'group-"][data-wsf-group-hidden] [' + attribute_source + ']:not(' + attribute_not + '), [id^="' + this.form_id_prefix + 'group-"][data-wsf-group-hidden] [' + attribute_source + ']:not(' + attribute_not + ')').attr(attribute_bypass + '-group', function() { return ws_this.form_bypass_hidden($(this), attribute_source, attribute_replace); });
			}

			// If a hidden field is in a hidden group, convert bypass address to group level
			if($('[' + attribute_bypass + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'group-"][data-wsf-group-hidden] [' + attribute_bypass + ']:not(' + attribute_not + '), [id^="' + this.form_id_prefix + 'group-"][data-wsf-group-hidden] [' + attribute_bypass + ']:not(' + attribute_not + ')').attr(attribute_bypass + '-group', function() { return ws_this.form_bypass_visible($(this), attribute_bypass); }).removeAttr(attribute_bypass);
			}


			// If a section is visible, and contains fields that have a data bypass attribute, reset that attribute
			if($('[' + attribute_bypass + '-section]', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'section-"][style!="display:none;"][style!="display: none;"] [' + attribute_bypass + '-section]:not(' + attribute_not + ')', this.form_canvas_obj).attr(attribute_source, function() { return $(this).attr(attribute_bypass + '-section'); }).removeAttr(attribute_bypass + '-section');
			}

			// If a section is not visible, and contains validation attributes, add bypass attributes
			if($('[' + attribute_source + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'section-"][style="display:none;"] [' + attribute_source + ']:not(' + attribute_not + '), [id^="' + this.form_id_prefix + 'section-"][style="display: none;"] [' + attribute_source + ']:not(' + attribute_not + ')').attr(attribute_bypass + '-section', function() { return ws_this.form_bypass_hidden($(this), attribute_source, attribute_replace); });
			}

			// If a hidden field is in a hidden section, convert bypass address to section level
			if($('[' + attribute_bypass + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'section-"][style="display:none;"] [' + attribute_bypass + ']:not(' + attribute_not + '), [id^="' + this.form_id_prefix + 'section-"][style="display: none;"] [' + attribute_bypass + ']:not(' + attribute_not + ')').attr(attribute_bypass + '-section', function() { return ws_this.form_bypass_visible($(this), attribute_bypass); }).removeAttr(attribute_bypass);
			}


			// If field is visible, add validation attributes back that have a bypass data tag
			if($('[' + attribute_bypass + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'field-wrapper-"][style!="display:none;"][style!="display: none;"] [' + attribute_bypass + ']:not(' + attribute_not + ')', this.form_canvas_obj).attr(attribute_source, function() { return ws_this.form_bypass_visible($(this), attribute_bypass); }).removeAttr(attribute_bypass);
			}

			// If field is not visible, add contain validation attributes, add bypass attributes
			if($('[' + attribute_source + ']', this.form_canvas_obj).length) {

				$('[id^="' + this.form_id_prefix + 'field-wrapper-"][style="display:none;"] [' + attribute_source + ']:not(' + attribute_not + '), [id^="' + this.form_id_prefix + 'field-wrapper-"][style="display: none;"] [' + attribute_source + ']:not(' + attribute_not + ')', this.form_canvas_obj).attr(attribute_bypass, function() { return ws_this.form_bypass_hidden($(this), attribute_source, attribute_replace); });
			}
		}

		// Process custom validity messages - Groups
		$('[id^="' + this.form_id_prefix + 'group-"]:not([data-wsf-group-hidden])', this.form_canvas_obj).find('[name]:not([type="hidden"]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {

			ws_this.form_bypass_process($(this), '-group', false);
		});

		$('[id^="' + this.form_id_prefix + 'group-"][data-wsf-group-hidden]').find('[name]:not([type="hidden"]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {

			ws_this.form_bypass_process($(this), '-group', true);
		});

		// Process custom validity messages - Sections
		$('[id^="' + this.form_id_prefix + 'section-"][style!="display:none;"][style!="display: none;"]', this.form_canvas_obj).find('[name]:not([type="hidden"],[data-hidden-group]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {

			ws_this.form_bypass_process($(this), '-section', false);
		});

		$('[id^="' + this.form_id_prefix + 'section-"][style="display:none;"], [id^="' + this.form_id_prefix + 'section-"][style="display: none;"]').find('[name]:not([type="hidden"]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {

			ws_this.form_bypass_process($(this), '-section', true);
		});

		// Process custom validity messages - Fields
		$('[id^="' + this.form_id_prefix + 'field-wrapper-"][style!="display:none;"][style!="display: none;"]', this.form_canvas_obj).find('[name]:not([type="hidden"],[data-hidden-section],[data-hidden-group]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {

			ws_this.form_bypass_process($(this), '', false);
		});

		$('[id^="' + this.form_id_prefix + 'field-wrapper-"][style="display:none;"], [id^="' + this.form_id_prefix + 'field-wrapper-"][style="display: none;"]', this.form_canvas_obj).find('[name]:not([type="hidden"]),[data-static],[data-recaptcha],[data-hcaptcha],[data-turnstile]').each(function() {
			ws_this.form_bypass_process($(this), '', true);
		});

		// Process form progress
		if(typeof(this.form_progress_process) === 'function') { this.form_progress_process(); }

		// Recalculate e-commerce
		if(
			this.has_ecommerce &&
			(typeof(this.form_ecommerce_calculate) === 'function')
		) {
			this.form_ecommerce_calculate();
		}

		// Fire real time form validation
		this.form_validate_real_time_process(conditional_initiated);
	}

	// Form bypass - Hidden
	$.WS_Form.prototype.form_bypass_hidden = function(obj, attribute_source, attribute_replace) {

		var attribute_source_value = obj.attr(attribute_source);

		if(attribute_replace) {

			obj.attr(attribute_source, attribute_replace);

		} else {

			obj.removeAttr(attribute_source);
		}

		return attribute_source_value;
	}

	// Form bypass - Visible
	$.WS_Form.prototype.form_bypass_visible = function(obj, attribute_bypass) {

		return obj.attr(attribute_bypass);
	}

	// Form bypass process
	$.WS_Form.prototype.form_bypass_process = function(obj, attr_suffix, set) {

		var section_id = this.get_section_id(obj);
		var section_repeatable_index = this.get_section_repeatable_index(obj);
		var field_id = this.get_field_id(obj);

		if(set) {

			if(obj[0].willValidate) {

				var validation_message = obj[0].validationMessage;

				if(validation_message !== '') {

					if(typeof(this.validation_message_cache[section_id]) === 'undefined') { this.validation_message_cache[section_id] = []; }
					if(typeof(this.validation_message_cache[section_id][section_repeatable_index]) === 'undefined') { this.validation_message_cache[section_id][section_repeatable_index] = []; }
					if(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id]) === 'undefined') { this.validation_message_cache[section_id][section_repeatable_index][field_id] = []; }

					this.validation_message_cache[section_id][section_repeatable_index][field_id][0] = validation_message;

					// Set custom validation message to blank
					obj[0].setCustomValidity('');
				}
			}

			// Add data-hidden attribute
			if(typeof(obj.attr('data-hidden-bypass')) === 'undefined') {

				obj.attr('data-hidden' + attr_suffix, '');
			}

		} else {

			if(
				obj[0].willValidate &&
				(typeof(this.validation_message_cache[section_id]) !== 'undefined') &&
				(typeof(this.validation_message_cache[section_id][section_repeatable_index]) !== 'undefined') &&
				(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id]) !== 'undefined') &&
				(typeof(this.validation_message_cache[section_id][section_repeatable_index][field_id][0]) !== 'undefined')
			) {

				// Recall custom validation message
				obj[0].setCustomValidity(this.validation_message_cache[section_id][section_repeatable_index][field_id][0]);
			}

			// Remove data-hidden attribute
			if(typeof(obj.attr('data-hidden-bypass')) === 'undefined') {

				obj.removeAttr('data-hidden' + attr_suffix);
			}
		}
	}

	// Form - Input Mask
	$.WS_Form.prototype.form_inputmask = function() {

		$('[data-inputmask]', this.form_canvas_obj).each(function () {

			if(typeof($(this).inputmask) !== 'undefined') {

				$(this).inputmask().off('invalid');
			}
		});
	}

	// Form - Client side validation
	$.WS_Form.prototype.form_validation = function() {

		// WS Form forms are set with novalidate attribute so we can manage that ourselves
		var ws_this = this;

		// Disable submit on enter
		if(!this.get_object_meta_value(this.form, 'submit_on_enter', false)) {

			this.form_obj.on('keydown', ':input:not(textarea)', function(e) {

				if(e.keyCode == 13) {

					e.preventDefault();
					return false;
				}
			});
		}

		// On submit
		this.form_obj.on('submit', function(e) {

			e.preventDefault();
			e.stopPropagation();

			// Post if form validates
			ws_this.form_post_if_validated('submit');
		});
	}

	// Form - Post if validated
	$.WS_Form.prototype.form_post_if_validated = function(post_mode) {

		// Trigger
		this.trigger(post_mode + '-before');

		// If form post is locked, return
		if(this.form_post_locked) { return; }

		// Recalculate e-commerce
		if(
			this.has_ecommerce &&
			(typeof(this.form_ecommerce_calculate) === 'function')
		) {
			this.form_ecommerce_calculate();
		}

		// Mark form as validated
		this.form_obj.addClass(this.class_validated);

		// Check validity of form
		if(this.form_validate(this.form_obj)) {

			// Trigger
			this.trigger(post_mode + '-validate-success');

			// reCAPTCHA
			if(this.recaptchas_v2_invisible.length > 0) {

				// Execute (Once reCAPTCHA executes, it calls form_submit)
				this.recaptcha_v2_invisible_execute();

			// hCaptcha
			} else if (this.hcaptchas_invisible.length > 0) {

				// Execute (Once hCaptcha executes, it calls form_submit)
				this.hcaptcha_invisible_execute();

			} else {

				// Submit form
				this.form_post(post_mode);
			}

		} else {

			// Trigger
			this.trigger(post_mode + '-validate-fail');
		}
	}

	// Form - Validate
	$.WS_Form.prototype.form_validate = function(form) {

		if(typeof(form) === 'undefined') { form = this.form_obj; }

		// Trigger rendered event
		this.trigger('validate-before');

		// Tab focussing
		var group_index_focus = false;
		var object_focus = false;

		// Get form as element
		var form_el = form[0];

		// Execute browser validation
		var form_validated = form_el.checkValidity();

		if(!form_validated) {

			// Get all invalid fields
			var fields_invalid = $(':invalid', form).filter(':visible').not('fieldset');

			if(fields_invalid) {

				// Get first invalid field
				object_focus = fields_invalid.first();

				// Get group index
				group_index_focus = this.get_group_index(object_focus);
			}
		}

		if(typeof(this.form_validate_captcha) === 'function') {

			// reCAPTCHA validation
			var captcha_validate_return = this.form_validate_captcha(this.recaptchas_v2_default, 'recaptcha', form);
			if(typeof(captcha_validate_return) === 'object') {

				form_validated = false;
				if(object_focus === false) { object_focus = captcha_validate_return.object_focus; }
				if(group_index_focus === false) { group_index_focus = captcha_validate_return.group_index_focus; }
			}

			// hCaptcha validation
			var captcha_validate_return = this.form_validate_captcha(this.hcaptchas_default, 'hcaptcha', form);
			if(typeof(captcha_validate_return) === 'object') {

				form_validated = false;
				if(object_focus === false) { object_focus = captcha_validate_return.object_focus; }
				if(group_index_focus === false) { group_index_focus = captcha_validate_return.group_index_focus; }
			}

			// Turnstile validation
			var captcha_validate_return = this.form_validate_captcha(this.turnstiles_default, 'turnstile', form);
			if(typeof(captcha_validate_return) === 'object') {

				form_validated = false;
				if(object_focus === false) { object_focus = captcha_validate_return.object_focus; }
				if(group_index_focus === false) { group_index_focus = captcha_validate_return.group_index_focus; }
			}
		}

		// Focus
		if(!form_validated) {

			if(object_focus !== false) {

				// Focus object
				if(this.get_object_meta_value(this.form, 'invalid_field_focus', true)) {

					if(group_index_focus !== false) { 

						this.object_focus = object_focus;

					} else {

						object_focus.trigger('focus');
					}
				}
			}

			// Focus tab
			if(
				(typeof(this.form_tab_group_index_set) === 'function') &&
				(group_index_focus !== false)
			) {
				this.form_tab_group_index_set(group_index_focus);
			}
		}

		// Trigger rendered event
		this.trigger('validate-after');

		return form_validated;
	}

	// Form - Validate - Real time
	$.WS_Form.prototype.form_validate_real_time = function(form) {

		var ws_this = this;

		// Set up form validation events
		for(var field_index in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(field_index)) { continue; }

			var field_type = this.field_data_cache[field_index].type;
			var field_type_config = $.WS_Form.field_type_cache[field_type];

			// Get events
			if(typeof(field_type_config.events) === 'undefined') { continue; }
			var form_validate_event = field_type_config.events.event;

			// Get field ID
			var field_id = this.field_data_cache[field_index].id;

			// Check to see if this field is submitted as an array
			var submit_array = (typeof(field_type_config.submit_array) !== 'undefined') ? field_type_config.submit_array : false;

			// Check to see if field is in a repeatable section
			var field_wrapper = $('[data-type][data-id="' + field_id + '"],input[type="hidden"][data-id-hidden="' + field_id + '"]', this.form_canvas_obj);

			// Run through each wrapper found (there might be repeatables)
			field_wrapper.each(function() {

				var section_repeatable_index = $(this).attr('data-repeatable-index');
				var section_repeatable_suffix = (section_repeatable_index > 0) ? '[' + section_repeatable_index + ']' : '';

				if(submit_array) {

					var field_obj = $('[name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '[]"]:not([data-init-validate-real-time]), [name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '[]"]:not([data-init-validate-real-time])', ws_this.form_canvas_obj);

				} else {

					var field_obj = $('[name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '"]:not([data-init-validate-real-time]), [name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '"]:not([data-init-validate-real-time])', ws_this.form_canvas_obj);
				}

				if(field_obj.length) {

					// Flag so it only initializes once
					field_obj.attr('data-init-validate-real-time', '');

					// Check if field should be bypassed
					var event_validate_bypass = (typeof(field_type_config.event_validate_bypass) !== 'undefined') ? field_type_config.event_validate_bypass : false;

					// Create event (Also run on blur, this prevents the mask component from causing false validation results)
					field_obj.on(form_validate_event + ' blur', function(e) {

						// Form validation
						if(!event_validate_bypass) {

							// Run validate real time processing
							ws_this.form_validate_real_time_process(false);
						}

						// Run calculation
						if(e.type !== 'blur') {

							var field_obj_id = $(this).attr('id');
							if(field_obj_id) {

								if(typeof(ws_this.form_calc) === 'function') { ws_this.form_calc(field_obj_id); }
							}
						}
					});
				}
			});
		}

		// Initial validation fire
		this.form_validate_real_time_process(false);
	}

	$.WS_Form.prototype.form_validate_real_time_process = function(conditional_initiated) {

		// Validate
		this.form_valid = this.form_validate_silent(this.form_obj);

		// Run conditional logic
		if(!conditional_initiated) { this.form_canvas_obj.trigger('wsf-validate-silent'); }

		// Check for form validation changes
		if(
			(this.form_valid_old === null) ||
			(this.form_valid_old != this.form_valid)
		) {

			// Run conditional logic
			if(!conditional_initiated) { this.form_canvas_obj.trigger('wsf-validate'); }
		}

		this.form_valid_old = this.form_valid;

		// Execute hooks and pass form_valid to them
		for(var hook_index in this.form_validation_real_time_hooks) {

			if(!this.form_validation_real_time_hooks.hasOwnProperty(hook_index)) { continue; }

			var hook = this.form_validation_real_time_hooks[hook_index];

			if(typeof(hook) === 'undefined') {

				delete(this.form_validation_real_time_hooks[hook_index]);

			} else {

				hook(this.form_valid, this.form, this.form_id, this.form_instance_id, this.form_obj, this.form_canvas_obj);
			}
		}

		return this.form_valid;
	}

	$.WS_Form.prototype.form_validate_real_time_register_hook = function(hook) {

		this.form_validation_real_time_hooks.push(hook);
	}

	// Form - Validate - Silent
	$.WS_Form.prototype.form_validate_silent = function(form) {

		// Get form as element
		var form_el = form[0];

		// aria-invalid="true"
		$(':valid[aria-invalid="true"]:not(fieldset)', form).removeAttr('aria-invalid');
		$(':invalid:not([aria-invalid="true"]):not(fieldset)', form).attr('aria-invalid', 'true');

		// Execute browser validation
		var form_validated = form_el.checkValidity();
		if(!form_validated) { return false; }

		if(typeof(this.form_validate_silent_captchas) === 'function') {

			// Validate reCAPTCHAs
			if(!this.form_validate_silent_captchas(this.recaptchas_v2_default, 'recaptcha', form)) {

				return false;
			}

			// Validate hCaptchas
			if(!this.form_validate_silent_captchas(this.hcaptchas_default, 'hcaptcha', form)) {

				return false;
			}

			// Validate Turnstiles
			if(!this.form_validate_silent_captchas(this.turnstiles_default, 'turnstile', form)) {

				return false;
			}
		}

		return true;
	}

	// Validate any form object
	$.WS_Form.prototype.object_validate = function(obj) {

		var radio_field_processed = [];		// This ensures correct progress numbers of radios

		if(typeof(obj) === 'undefined') { return false; }

		var ws_this = this;

		var valid = true;

		// Get fields
		$('input,select,textarea', obj).filter(':not([data-hidden],[data-hidden-section],[data-hidden-group],[disabled],[type="hidden"])').each(function() {

			// Get field
			var field = ws_this.get_field($(this));
			var field_type = field.type;

			// Get repeatable suffix
			var section_repeatable_index = ws_this.get_section_repeatable_index($(this));
			var section_repeatable_suffix = (section_repeatable_index > 0) ? '[' + section_repeatable_index + ']' : '';

			// Build field name
			var field_name = ws_form_settings.field_prefix + ws_this.get_field_id($(this)) + section_repeatable_suffix;

			// Determine field validity based on field type
			var validity = false;
			switch(field_type) {

				case 'radio' :
				case 'price_radio' :

					if(typeof(radio_field_processed[field_name]) === 'undefined') { 

						validity = $(this)[0].checkValidity();

					} else {

						return;
					}
					break;

				case 'email' :

					if(typeof($(this).attr('required')) !== 'undefined') {

						var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
						validity = email_regex.test($(this).val());

					} else {

						validity = true;
					}

					break;

				default :

					validity = $(this)[0].checkValidity();
			}

			radio_field_processed[field_name] = true;

			if(!validity) { valid = false; return false; }
		});

		return valid;
	}

	// Convert hex color to RGB values
	$.WS_Form.prototype.hex_to_hsl = function(color) {

		// Get RGB of hex color
		var rgb = this.hex_to_rgb(color);
		if(rgb === false) { return false; }

		// Get HSL of RGB
		var hsl = this.rgb_to_hsl(rgb);

		return hsl;
	}

	// Convert hex color to RGB values
	$.WS_Form.prototype.hex_to_rgb = function(color) {

		// If empty, return false
		if(color == '') { return false; }

		// Does color have a hash?
		var color_has_hash = (color[0] == '#');

		// Check
		if(color_has_hash && (color.length != 7)) { return false; }
		if(!color_has_hash && (color.length != 6)) { return false; }

		// Strip hash
		var color = color_has_hash ? color.substr(1) : color;

		// Get RGB values
		var r = parseInt(color.substr(0,2), 16);
		var g = parseInt(color.substr(2,2), 16);
		var b = parseInt(color.substr(4,2), 16);

		return {'r': r, 'g': g, 'b': b};
	}

	// Convert RGB to HSL
	$.WS_Form.prototype.rgb_to_hsl = function(rgb) {

		if(typeof(rgb.r) === 'undefined') { return false; }
		if(typeof(rgb.g) === 'undefined') { return false; }
		if(typeof(rgb.b) === 'undefined') { return false; }

		var r = rgb.r;
		var g = rgb.g;
		var b = rgb.b;

		r /= 255, g /= 255, b /= 255;

		var max = Math.max(r, g, b), min = Math.min(r, g, b);
		var h, s, l = (max + min) / 2;

		if(max == min){
	
			h = s = 0;
	
		} else {
	
			var d = max - min;
			s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

			switch(max){
				case r: h = (g - b) / d + (g < b ? 6 : 0); break;
				case g: h = (b - r) / d + 2; break;
				case b: h = (r - g) / d + 4; break;
			}

			h /= 6;
		}

		return {'h': h, 's': s, 'l': l};
	}

	$.WS_Form.prototype.group_fields_reset = function(group_id, field_clear) {

		if(typeof(this.group_data_cache[group_id]) === 'undefined') { return false; }

		// Get group
		var group = this.group_data_cache[group_id];
		if(typeof(group.sections) === 'undefined') { return false; }

		// Get all fields in group
		var sections = group.sections;

		for(var section_index in sections) {

			if(!sections.hasOwnProperty(section_index)) { continue; }

			var section = sections[section_index];

			this.section_fields_reset(section.id, field_clear, false);
		}
	}

	$.WS_Form.prototype.section_fields_reset = function(section_id, field_clear, section_repeatable_index) {

		if(typeof(this.section_data_cache[section_id]) === 'undefined') { return false; }

		// Get section
		var section = this.section_data_cache[section_id];
		if(typeof(section.fields) === 'undefined') { return false; }

		// Get all fields in section
		var fields = section.fields;

		for(var field_index in fields) {

			if(!fields.hasOwnProperty(field_index)) { continue; }

			var field = fields[field_index];
			var field_id = field.id;

			if(section_repeatable_index === false) {

				var object_selector_wrapper = '[id^="' + this.form_id_prefix + 'field-wrapper-' + field_id + '"][data-id="' + field.id + '"]';

			} else {

				var object_selector_wrapper = '#' + this.form_id_prefix + 'field-wrapper-' + field_id + '-repeat-' + section_repeatable_index;
			}

			var obj_wrapper = $(object_selector_wrapper, this.form_canvas_obj);

			this.field_reset(field_id, field_clear, obj_wrapper);
		}
	}

	$.WS_Form.prototype.field_reset = function(field_id, field_clear, obj_wrapper) {

		var ws_this = this;

		if(typeof(obj_wrapper) === 'undefined') { obj_wrapper = false; }

		if(typeof(this.field_data_cache[field_id]) === 'undefined') { return; }

		var field = this.field_data_cache[field_id];

		var field_type_config = $.WS_Form.field_type_cache[field.type];
		var trigger_action = (typeof(field_type_config.trigger) !== 'undefined') ? field_type_config.trigger : 'change';

		switch(field.type) {

			case 'select' :
			case 'price_select' :

				$('option', obj_wrapper).each(function() {

					var selected_new = field_clear ? false : $(this).prop('defaultSelected');
					var trigger = $(this).prop('selected') !== selected_new;
					$(this).prop('selected', selected_new);
					if(trigger) { $(this).trigger(trigger_action); }
				});
				break;

			case 'checkbox' :
			case 'price_checkbox' :

				$('input[type="checkbox"]', obj_wrapper).each(function() {

					var checked_new = field_clear ? false : $(this).prop('defaultChecked');
					var trigger = $(this).prop('checked') !== checked_new;
					$(this).prop('checked', checked_new);
					if(trigger) { $(this).trigger(trigger_action); }
				});
				break;

			case 'radio' :
			case 'price_radio' :

				$('input[type="radio"]', obj_wrapper).each(function() {

					var checked_new = field_clear ? false : $(this).prop('defaultChecked');
					var trigger = $(this).prop('checked') !== checked_new;
					$(this).prop('checked', checked_new);
					if(trigger) { $(this).trigger(trigger_action); }
				});
				break;

			case 'textarea' :

				$('textarea', obj_wrapper).each(function() {

					var val_new = field_clear ? '' : $(this).prop('defaultValue');
					var trigger = $(this).val() !== val_new;
					$(this).val(val_new);
					if(typeof(ws_this.textarea_set_value) === 'function') { ws_this.textarea_set_value($(this), val_new); }
					if(trigger) { $(this).trigger('change'); }
				});
				break;

			case 'color' :

				$('input', obj_wrapper).each(function() {

					var val_new = field_clear ? '' : $(this).prop('defaultValue');
					var trigger = $(this).val() !== val_new;
					$(this).val(val_new);
					if($(this).hasClass('minicolors-input')) {
						$(this).minicolors('value', {color: val_new});
					}
					if(trigger) { $(this).trigger('change'); }
				});
				break;

			case 'hidden' :

				// Hidden fields don't have a wrapper so the obj_wrapper is the field. You cannot use the defaultValue property on hidden fields as it gets update when val() is used, so we use data-default-value attribute instead.
				var val_new = field_clear ? '' : obj_wrapper.attr('data-default-value');
				var trigger = obj_wrapper.val() !== val_new;
				obj_wrapper.val(val_new);
				if(trigger) { obj_wrapper.trigger(trigger_action); }
				break;

			case 'googlemap' :

				$('input', obj_wrapper).each(function() {

					var val_new = field_clear ? '' : $(this).attr('data-default-value');
					var trigger = $(this).val() !== val_new;
					$(this).val(val_new);
					if(trigger) { $(this).trigger(trigger_action); }
				});
				break;

			case 'file' :

				// Regular file uploads
				$('input[type="file"]', obj_wrapper).each(function() {

					var trigger = $(this).val() !== '';
					$(this).val('');
					if(trigger) { $(this).trigger(trigger_action); }
				});

				if(
					(typeof(ws_this.form_file_dropzonejs_populate) === 'function') &&
					(typeof(Dropzone) !== 'undefined')
				) {


					$('input[data-file-type="dropzonejs"]', obj_wrapper).each(function() {

						var val_old = $(this).val();

						ws_this.form_file_dropzonejs_populate($(this), field_clear);

						if($(this).val() !== val_old) { $(this).trigger(trigger_action); }
					});
				}

				break;

			default :

				$('input', obj_wrapper).each(function() {

					var val_new = field_clear ? '' : $(this).prop('defaultValue');
					var trigger = $(this).val() !== val_new;
					$(this).val(val_new);
					if(trigger) { $(this).trigger(trigger_action); }
				});
		}
	}

	// Form - Post
	$.WS_Form.prototype.form_post = function(post_mode, action_id) {

		if(typeof(post_mode) == 'undefined') { post_mode = 'save'; }
		if(typeof(action_id) == 'undefined') { action_id = 0; }

		// Determine if this is a submit
		var submit = (post_mode == 'submit');

		// Trigger post mode event
		this.trigger(post_mode);

		var ws_this = this;

		// Lock form
		this.form_post_lock();

		// Build form data
		this.form_add_hidden_input('wsf_form_id', this.form_id);
		this.form_add_hidden_input('wsf_hash', this.hash);
		if(ws_form_settings.wsf_nonce) {

			this.form_add_hidden_input(ws_form_settings.wsf_nonce_field_name, ws_form_settings.wsf_nonce);
		}

		// Tracking - Duration (If disabled, cookie and hidden field are not set to respect privacy settings)
		var duration_tracking = (this.get_object_meta_value(this.form, 'tracking_duration', '') == 'on');
		if(duration_tracking) {

			this.form_add_hidden_input('wsf_duration', Math.round((new Date().getTime() - this.date_start) / 1000));
		}

		// Repeatable section hidden field
		if(typeof(this.section_repeatable_hidden_field) === 'function') { this.section_repeatable_hidden_field(); }

		// Reset date start
		if(post_mode == 'submit') {

			this.date_start = false;

			if(duration_tracking) {

				this.cookie_set('date_start', false, false);
			}

			this.form_timer();
		}

		if((typeof(ws_form_settings.post_id) !== 'undefined') && (ws_form_settings.post_id > 0)) {

			this.form_add_hidden_input('wsf_post_id', ws_form_settings.post_id);
		}

		// Post mode
		this.form_add_hidden_input('wsf_post_mode', post_mode);

		// Work out which fields are hidden
		var hidden_array = $('[data-hidden],[data-hidden-section],[data-hidden-group]', ws_this.form_canvas_obj).map(function() {

			// Get name
			var name = $(this).attr('name');
			if(typeof(name) === 'undefined') {

				var name = $(this).attr('data-name');
				if(typeof(name) === 'undefined') {

					return '';
				}
			}

			// Strip brackets (For select, radio and checkboxes)
			name = name.replace('[]', '');

			return name;

		}).get();
		hidden_array = hidden_array.filter(function(value, index, self) { 

			return self.indexOf(value) === index;
		});
		var hidden = hidden_array.join();
		this.form_add_hidden_input('wsf_hidden', hidden);

		// Work out which required fields to bypass (because they are hidden) or no longer required because of conditional logic
		var bypass_required_array = $('[data-required-bypass],[data-required-bypass-section],[data-required-bypass-group],[data-conditional-logic-bypass]', this.form_canvas_obj).map(function() {

			// Get name
			var name = $(this).attr('name');

			// Strip brackets (For select, radio and checkboxes)
			name = name.replace('[]', '');

			return name;

		}).get();
		bypass_required_array = bypass_required_array.filter(function(value, index, self) { 

			return self.indexOf(value) === index;
		});
		var bypass_required = bypass_required_array.join();
		this.form_add_hidden_input('wsf_bypass_required', bypass_required);

		// Signatures
		if(typeof(this.signature_form_post) === 'function') { this.signature_form_post(); }

		// Clear action processing
		$('input[type="hidden"][name="wsf_actions_run[]"]').remove();

		// Action processing (Conditional)
		var actions_run = [];
		if(this.conditional_actions_changed) {

			if(!submit && (this.conditional_actions_run_save.length > 0)) { actions_run = this.conditional_actions_run_save; }
			if(submit && (this.conditional_actions_run_submit.length > 0)) { actions_run = this.conditional_actions_run_submit; }
			for(var actions_run_index in actions_run) {

				if(!actions_run.hasOwnProperty(actions_run_index)) { continue; }

				var action_run_id = actions_run[actions_run_index];
				if(actions_run.indexOf(action_run_id) == actions_run_index) { this.form_add_hidden_input('wsf_actions_run[]', action_run_id); }
			}
		}

		// POST form
		this.log(submit ? 'log_form_submit' : 'log_form_save');

		// Do not run AJAX
		if(
			submit &&
			(action_id == 0) &&
			(this.form_ajax === false)
		) {

			// We're done!
			this.form_hash_clear();
			this.trigger(post_mode + '-complete');
			this.trigger('complete');
			return;
		}

		// Trigger
		ws_this.trigger(post_mode + '-before-ajax');

		// Build form data
		var form_data = new FormData(this.form_obj[0]);

		// Action ID (Inject into form_data so that it doesn't stay on the form)
		if(action_id > 0) {

			form_data.append('wsf_action_id', action_id);
		}

		// Process international telephone inputs
		$('[data-intl-tel-input]').each(function() {

			// Get iti instance
			var iti = window.intlTelInputGlobals.getInstance($(this)[0]);

			// Get field ID
			var field_id = ws_this.get_field_id($(this));

			// Get field
			var field = ws_this.field_data_cache[field_id];

			// Get return format
			var return_format = ws_this.get_object_meta_value(field, 'intl_tel_input_format', '');

			// Get number
			switch(return_format) {

				case 'INTERNATIONAL' :
				case 'NATIONAL' :
				case 'E164' :
				case 'RFC3966' :

					// Return if intlTelInputUtils is not yet initialized on the page (prevents JS error if form submitted immediately)
					if(!intlTelInputUtils) { return; }

					var field_value = iti.getNumber(intlTelInputUtils.numberFormat[return_format]);

					break;

				default :

					return;
			}

			// Get field name
			var field_name = $(this).attr('name');

			// Override form data
			form_data.set(field_name, field_value);
		});

		// Call API
		this.api_call('submit', 'POST', form_data, function(response) {

			// Success

			// Reset reCAPTCHA
			if(typeof(ws_this.recaptcha_reset) === 'function') { ws_this.recaptcha_reset(); }

			// Reset hCaptcha
			if(typeof(ws_this.hcaptcha_reset) === 'function') { ws_this.hcaptcha_reset(); }

			// Reset turnstile
			if(typeof(ws_this.turnstile_reset) === 'function') { ws_this.turnstile_reset(); }

			// Check for validation errors
			var error_validation = (typeof(response.error_validation) !== 'undefined') && response.error_validation;

			// Check for errors
			var errors = (

				(typeof(response.data) !== 'undefined') &&
				(typeof(response.data.errors) !== 'undefined') &&
				response.data.errors.length
			);

			// If response is invalid or form is being saved, force unlock it
			var form_post_unlock_force = (

				(typeof(response.data) === 'undefined') ||
				(post_mode == 'save') ||
				error_validation ||
				errors
			);

			// Unlock form
			ws_this.form_post_unlock('progress', !form_post_unlock_force, form_post_unlock_force);

			// Trigger error event
			if(errors || error_validation) {

				// Trigger error
				ws_this.trigger(post_mode + '-error');
				ws_this.trigger('error');

			} else {

				// Trigger success
				ws_this.trigger(post_mode + '-success');
				ws_this.trigger('success');
			}

			// Check for form reload on submit
			if(
				(submit && !error_validation && !errors)
			) {

				// Clear hash
				ws_this.form_hash_clear();

				if(ws_this.get_object_meta_value(ws_this.form, 'submit_reload', true)) {

					// Reload
					ws_this.form_reload();
				}
			}

			// Show error messages
			if(errors && ws_this.get_object_meta_value(ws_this.form, 'submit_show_errors', true)) {

				for(var error_index in response.data.errors) {

					if(!response.data.errors.hasOwnProperty(error_index)) { continue; }

					var error_message = response.data.errors[error_index];
					ws_this.action_message(error_message);
				}
			}

			ws_this.trigger(post_mode + '-complete');
			ws_this.trigger('complete');

			return !errors;

		}, function(response) {

			// Error
			ws_this.form_post_unlock('progress', true, true);

			// Reset reCAPTCHA
			if(typeof(ws_this.recaptcha_reset) === 'function') { ws_this.recaptcha_reset(); }

			// Reset hCaptcha
			if(typeof(ws_this.hcaptcha_reset) === 'function') { ws_this.hcaptcha_reset(); }

			// Reset turnstile
			if(typeof(ws_this.turnstile_reset) === 'function') { ws_this.turnstile_reset(); }

			// Show error message
			if(typeof(response.error_message) !== 'undefined') {

				ws_this.action_message(response.error_message);
			}

			// Trigger post most complete event
			ws_this.trigger(post_mode + '-error');
			ws_this.trigger('error');

		}, (action_id > 0) || !submit);
	}

	// Form lock
	$.WS_Form.prototype.form_post_lock = function(cursor, force, ecommerce_calculate_disable) {

		if(typeof(cursor) === 'undefined') { cursor = 'progress'; }
		if(typeof(force) === 'undefined') { force = false; }
		if(typeof(ecommerce_calculate_disable) === 'undefined') { ecommerce_calculate_disable = false; }

		if(this.form_obj.hasClass('wsf-form-post-lock')) { return; }

		if(force || this.get_object_meta_value(this.form, 'submit_lock', false)) {

			// Stop further calculations
			if(ecommerce_calculate_disable) {

				this.form_ecommerce_calculate_enabled = false;
			}

			// Get submit buttons
			var submit_button_objs = $('button[type="submit"].wsf-button, input[type="submit"].wsf-button, button[data-action="wsf-save"].wsf-button, button[data-ecommerce-payment].wsf-button, [data-post-lock]', this.form_canvas_obj);

			// Check if already disabled
			submit_button_objs.each(function() {

				if(typeof($(this).attr('disabled')) !== 'undefined') {

					$(this).attr('data-form-lock-disabled-bypass', '');

				} else {

					$(this).attr('disabled', '');
				}
			})

			// Add locked class to form
			this.form_obj.addClass('wsf-form-post-lock' + (cursor ? ' wsf-form-post-lock-' + cursor : ''));

			// Lock form
			this.form_post_locked = true;

			// Trigger lock event
			this.trigger('lock');

			// Add to log
			this.log('log_submit_lock', '', 'duplicate-protect');
		}
	}

	// Form unlock
	$.WS_Form.prototype.form_post_unlock = function(cursor, timeout, force) {

		if(typeof(cursor) === 'undefined') { cursor = 'progress'; }
		if(typeof(timeout) === 'undefined') { timeout = true; }
		if(typeof(force) === 'undefined') { force = false; }

		if(!this.form_obj.hasClass('wsf-form-post-lock')) { return; }

		var ws_this = this;

		var unlock_fn = function() {

			// Re-enable cart calculations
			ws_this.form_ecommerce_calculate_enabled = true;

			// Remove locked class from form
			ws_this.form_obj.removeClass('wsf-form-post-lock' + (cursor ? ' wsf-form-post-lock-' + cursor : ''));

			// Get submit buttons
			var submit_button_objs = $('button[type="submit"].wsf-button, input[type="submit"].wsf-button, button[data-action="wsf-save"].wsf-button, button[data-ecommerce-payment].wsf-button, [data-post-lock]', ws_this.form_canvas_obj);

			// Enable submit buttons
			submit_button_objs.each(function() {

				if(typeof($(this).attr('data-form-lock-disabled-bypass')) !== 'undefined') {

					$(this).removeAttr('data-form-lock-disabled-bypass');

				} else {

					$(this).removeAttr('disabled');
				}
			});

			// Unlock form
			ws_this.form_post_locked = false;

			// Reset post upload progress indicators
			if(typeof(ws_this.form_progress_api_call_reset) === 'function') { ws_this.form_progress_api_call_reset(); }

			// Trigger unlock event
			ws_this.trigger('unlock');

			// Add to log
			ws_this.log('log_submit_unlock', '', 'duplicate-protect');
		}

		if(force || this.get_object_meta_value(this.form, 'submit_unlock', false)) {

			// Enable post buttons
			timeout ? setTimeout(function() { unlock_fn(); }, 1000) : unlock_fn();
		}
	}

	// API Call
	$.WS_Form.prototype.api_call = function(ajax_path, method, params, success_callback, error_callback, force_ajax_path) {

		// Defaults
		if(typeof(method) === 'undefined') { method = 'POST'; }
		if(!params) { params = new FormData(); }
		if(typeof(force_ajax_path) === 'undefined') { force_ajax_path = false; }

		var ws_this = this;

		// Debug
		if($.WS_Form.debug_rendered) {

			// Timer - Start
			var timer_start = new Date();
		}

		// Make AJAX request
		var url = force_ajax_path ? (ws_form_settings.url_ajax + ajax_path) : ((ajax_path == 'submit') ? this.form_obj.attr('action') : (ws_form_settings.url_ajax + ajax_path));

		// Check for custom action URL
		if(
			!force_ajax_path &&
			this.form_action_custom &&
			(ajax_path == 'submit')
		) {

			// Custom action submit
			this.form_obj.off('submit');
			this.form_obj.submit();
			return true;
		}

		// NONCE
		if(
			(
				(typeof(params.get) === 'undefined') || // Do it anyway for IE 11
				(params.get(ws_form_settings.wsf_nonce_field_name) === null)
			) &&
			(ws_form_settings.wsf_nonce)
		) {

			params.append(ws_form_settings.wsf_nonce_field_name, ws_form_settings.wsf_nonce);
		}

		// Convert FormData to object if making GET request (IE11 friendly code so not that elegant)
		if(method === 'GET') {

			var params_object = {};

			var form_data_entries = params.entries();
			var form_data_entry = form_data_entries.next();

			while (!form_data_entry.done) {

				var pair = form_data_entry.value;
				params_object[pair[0]] = pair[1];
				form_data_entry = form_data_entries.next();
			}

			params = params_object;
		}

		// Process validation focus
		this.action_js_process_validation_focus = true;

		// Call AJAX
		var ajax_request = {

			method: method,
			url: url,
			beforeSend: function(xhr) {

				// Nonce (X-WP-Nonce)
				if(ws_form_settings.x_wp_nonce) {

					xhr.setRequestHeader('X-WP-Nonce', ws_form_settings.x_wp_nonce);
				}
			},
			contentType: false,
			processData: (method === 'GET'),
 			statusCode: {

				// Success
				200: function(response) {

					// Handle hash response
					var hash_ok = ws_this.api_call_hash(response);

					// Debug
					if(hash_ok && $.WS_Form.debug_rendered) {

						// Timer - Duration
						var timer_duration = new Date() - timer_start;

						ws_this.debug_info('debug_info_submit_count', response.data.count);
						ws_this.debug_info('debug_info_submit_duration_user', ws_this.get_nice_duration(response.data.submit_duration_user));
						ws_this.debug_info('debug_info_submit_duration_client', timer_duration + ' ms');
						ws_this.debug_info('debug_info_submit_duration_server', response.data.submit_duration_server + ' ms');
					}

					// Check for action logs and errors (These are returned from the action system to tell the debugger to log the message)
					if($.WS_Form.debug_rendered) {

						if(typeof(response.data) !== 'undefined') {

							if(typeof(response.data.logs) === 'object') {

								for(var logs_index in response.data.logs) {

									if(!response.data.logs.hasOwnProperty(logs_index)) { continue; }

									ws_this.log('log_action', response.data.logs[logs_index], 'action');
								}
							}

							if(typeof(response.data.errors) === 'object') {

								for(var errors_index in response.data.errors) {

									if(!response.data.errors.hasOwnProperty(errors_index)) { continue; }

									// Get error message
									var error_message = response.data.errors[errors_index];

									// Check error message and log error
									if(error_message) {

										ws_this.error('error_action', error_message, 'action');

									} else {

										ws_this.error('error_action_no_message', '', 'action');
									}
								}
							}
						}
					}
					// Check for new nonce values
					if(typeof(response.x_wp_nonce) !== 'undefined') { ws_form_settings.x_wp_nonce = response.x_wp_nonce; }
					if(typeof(response.wsf_nonce) !== 'undefined') { ws_form_settings.wsf_nonce = response.wsf_nonce; }

					// Call success function
					var success_callback_result = (typeof(success_callback) === 'function') ? success_callback(response) : true;

					// Check for data to process
					if(
						(typeof(response.data) !== 'undefined') &&
						success_callback_result
					) {

						// Check for action_js (These are returned from the action system to tell the browser to do something)
						if(typeof(response.data.js) === 'object') { ws_this.action_js_init(response.data.js); }
					}
				},

				// Bad request
				400: function(response) {

					// Process error
					ws_this.api_call_error_handler(response, 400, url, error_callback);
				},

				// Unauthorized
				401: function(response) {

					// Process error
					ws_this.api_call_error_handler(response, 401, url, error_callback);
				},

				// Forbidden
				403: function(response) {

					// Process error
					ws_this.api_call_error_handler(response, 403, url, error_callback);
				},

				// Not found
				404: function(response) {

					// Process error
					ws_this.api_call_error_handler(response, 404, url, error_callback);
				},

				// Server error
				500: function(response) {

					// Process error
					ws_this.api_call_error_handler(response, 500, url, error_callback);
				}
			},

			complete: function() {

				this.api_call_handle = false;
			}
		};

		// Data
		if(params !== false) { ajax_request.data = params; }

		// Progress
		var progress_objs = $('[data-source="post_progress"]', this.form_canvas_obj);
		if(progress_objs.length) {

			ajax_request.xhr = function() {

				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(e) { ws_this.form_progress_api_call(progress_objs, e); }, false);
				xhr.addEventListener("progress", function(e) { ws_this.form_progress_api_call(progress_objs, e); }, false);
				return xhr;
			};
		}

		return $.ajax(ajax_request);
	};

	// API call - Process error
	$.WS_Form.prototype.api_call_error_handler = function(response, status, url, error_callback) {

		// Get response data
		var data = (typeof(response.responseJSON) !== 'undefined') ? response.responseJSON : false;

		// Process WS Form API error message
		if(data && data.error) {

			if(data.error_message) {

				this.error('error_api_call_' + status, data.error_message);

			} else {

				this.error('error_api_call_' + status, url);
			}

		} else {

			// Fallback
			this.error('error_api_call_' + status, url);
		}

		// Call error call back
		if(typeof(error_callback) === 'function') {

			// Run error callback
			error_callback(data);
		}
	}

	// API Call
	$.WS_Form.prototype.api_call_hash = function(response) {

		var hash_ok = true;
		if(typeof(response.hash) === 'undefined') { hash_ok = false; }
		if(hash_ok && (response.hash.length != 32)) { hash_ok = false; }
		if(hash_ok) {

			// Set hash
			this.hash_set(response.hash)
		}

		return hash_ok;
	}

	// Hash - Set
	$.WS_Form.prototype.hash_set = function(hash, token, cookie_set) {

		if(typeof(token) === 'undefined') { token = false; }
		if(typeof(cookie_set) === 'undefined') { cookie_set = false; }

		if(hash != this.hash) {

			// Set hash
			this.hash = hash;

			// Set hash cookie
			cookie_set = true;

			// Add to log
			this.log('log_hash_set', this.hash);

			// Debug
			if($.WS_Form.debug_rendered) { this.debug_info('debug_info_hash', this.hash, 'clear_hash'); }
		}

		if(token) {

			// Set token
			this.token = token;

			// Add to log
			this.log('log_token_set', this.token);
		}

		if(cookie_set) {

			var cookie_hash = this.get_object_value($.WS_Form.settings_plugin, 'cookie_hash');

			if(cookie_hash) {

				this.cookie_set('hash', this.hash);
			}
		}
	}

	// JS Actions - Init
	$.WS_Form.prototype.action_js_init = function(action_js) {

		// Trigger actions start event
		this.trigger('actions-start');

		this.action_js = action_js;

		this.action_js_process_next();
	};

	$.WS_Form.prototype.action_js_process_next = function() {

		if(this.action_js.length == 0) {

			// Trigger actions finish event
			this.trigger('actions-finish');

			return false;
		}

		var js_action = this.action_js.shift();

		var action = this.js_action_get_parameter(js_action, 'action');

		switch(action) {

			// Redirect
			case 'redirect' :

				var url = this.js_action_get_parameter(js_action, 'url');
				if(url !== false) { location.href = js_action['url']; }

				// Actions end at this point because of the redirect
				return true;

				break;

			// Message
			case 'message' :

				var message = this.js_action_get_parameter(js_action, 'message');
				var type = this.js_action_get_parameter(js_action, 'type');
				var method = this.js_action_get_parameter(js_action, 'method');
				var duration = this.js_action_get_parameter(js_action, 'duration');
				var form_hide = this.js_action_get_parameter(js_action, 'form_hide');
				var clear = this.js_action_get_parameter(js_action, 'clear');
				var scroll_top = this.js_action_get_parameter(js_action, 'scroll_top');
				var scroll_top_offset = this.js_action_get_parameter(js_action, 'scroll_top_offset');
				var scroll_top_duration = this.js_action_get_parameter(js_action, 'scroll_top_duration');
				var form_show = this.js_action_get_parameter(js_action, 'form_show');
				var message_hide = this.js_action_get_parameter(js_action, 'message_hide');

				this.action_message(message, type, method, duration, form_hide, clear, scroll_top, scroll_top_offset, scroll_top_duration, form_show, message_hide);

				break;
			// Conversion
			case 'conversion' :

				if(typeof(this.action_conversion) === 'function') {

					var type = this.js_action_get_parameter(js_action, 'type');
					var parse_values = this.js_action_get_parameter(js_action, 'parse_values');

					this.action_conversion(type, parse_values);
				}

				break;

			// JavaScript
			case 'javascript' :

				var javascript = this.js_action_get_parameter(js_action, 'javascript');

				this.action_javascript(javascript);

				break;
			// Field invalid feedback
			case 'field_invalid_feedback' :

				var field_id = parseInt(this.js_action_get_parameter(js_action, 'field_id'), 10);
				var section_repeatable_index = parseInt(this.js_action_get_parameter(js_action, 'section_repeatable_index'), 10);
				var section_repeatable_suffix = section_repeatable_index ? '-repeat-' + section_repeatable_index : '';
				var message = this.js_action_get_parameter(js_action, 'message');

				// Field object
				var field_obj = $('#' + this.form_id_prefix + 'field-' + field_id + section_repeatable_suffix, this.form_canvas_obj);

				// Set invalid feedback
				this.set_invalid_feedback(field_obj, message);

				// Log event
				this.log('error_invalid_feedback', field_id + ' (' + this.html_encode(message) + ')');

				var ws_this = this;

				// Reset if field modified
				field_obj.one('change input keyup paste', function() {

					// Reset invalid feedback
					ws_this.set_invalid_feedback($(this), '');
				});

				// Process focus?
				if(
					this.get_object_meta_value(this.form, 'invalid_field_focus', true) &&
					this.action_js_process_validation_focus
				) {

					// Get group index
					var group_index_focus = this.get_group_index(field_obj);

					// Focus object
					if(group_index_focus !== false) { 

						this.object_focus = field_obj;

					} else {

						field_obj.trigger('focus');
					}

					// Focus tab
					if(
						(typeof(this.form_tab_group_index_set) === 'function') &&
						(group_index_focus !== false)
					) {
						this.form_tab_group_index_set(group_index_focus);
					}

					// Prevent further focus
					this.action_js_process_validation_focus = false;
				}

				// Mark form as validated
				this.form_obj.addClass(this.class_validated);

				this.action_js_process_next();

				break;

			case 'trigger' :

				var event = this.js_action_get_parameter(js_action, 'event');
				var params = this.js_action_get_parameter(js_action, 'params');

				$(document).trigger(event, params);

				this.action_js_process_next();

				break;
		}
	}

	// JS Actions - Get js_action config parameter from AJAX return
	$.WS_Form.prototype.js_action_get_parameter = function(js_action_parameters, meta_key) {

		return typeof(js_action_parameters[meta_key]) !== 'undefined' ? js_action_parameters[meta_key] : false;
	}

	// JS Actions - Get framework config value
	$.WS_Form.prototype.get_framework_config_value = function(object, meta_key) {

		if(typeof(this.framework[object]) === 'undefined') {
			this.error('error_api_call_framework_invalid');
			return false;
		}
		if(typeof(this.framework[object]['public']) === 'undefined') {
			this.error('error_api_call_framework_invalid');
			return false;
		}
		if(typeof(this.framework[object]['public'][meta_key]) === 'undefined') { return false; }

		return this.framework[object]['public'][meta_key];
	}

	// JS Action - Message
	$.WS_Form.prototype.action_message = function(message, type, method, duration, form_hide, clear, scroll_top, scroll_top_offset, scroll_top_duration, form_show, message_hide) {

		// Check error message
		if(!message) { return; }

		// Error message setting defaults
		if(typeof(type) === 'undefined') { type = this.get_object_meta_value(this.form, 'error_type', 'danger'); }
		if(typeof(method) === 'undefined') { method = this.get_object_meta_value(this.form, 'error_method', 'after'); }
		if(typeof(duration) === 'undefined') { duration = parseInt(this.get_object_meta_value(this.form, 'error_duration', '4000'), 10); }
		if(typeof(form_hide) === 'undefined') { form_hide = (this.get_object_meta_value(this.form, 'error_form_hide', '') == 'on'); }
		if(typeof(clear) === 'undefined') { clear = (this.get_object_meta_value(this.form, 'error_clear', '') == 'on'); }
		if(typeof(scroll_top) === 'undefined') { scroll_top = (this.get_object_meta_value(this.form, 'error_scroll_top', '') == 'on'); }
		if(typeof(scroll_top_offset) === 'undefined') { scroll_top_offset = parseInt(this.get_object_meta_value(this.form, 'error_scroll_top_offset', '0'), 10); }
		scroll_top_offset = (scroll_top_offset == '') ? 0 : parseInt(scroll_top_offset, 10);
		if(typeof(scroll_top_duration) === 'undefined') { scroll_top_duration = parseInt(this.get_object_meta_value(this.form, 'error_scroll_top_duration', '400'), 10); }
		if(typeof(form_show) === 'undefined') { form_show = (this.get_object_meta_value(this.form, 'error_form_show', '') == 'on'); }
		if(typeof(message_hide) === 'undefined') { message_hide = (this.get_object_meta_value(this.form, 'error_message_hide', 'on') == 'on'); }

		var scroll_position = this.form_canvas_obj.offset().top - scroll_top_offset;

		// Parse duration
		duration = parseInt(duration, 10);
		if(duration < 0) { duration = 0; }

		// Get config
		var mask_wrapper = this.get_framework_config_value('message', 'mask_wrapper');
		var types = this.get_framework_config_value('message', 'types');

		var type = (typeof(types[type]) !== 'undefined') ? types[type] : false;
		var mask_wrapper_class = (typeof(type['mask_wrapper_class']) !== 'undefined') ? type['mask_wrapper_class'] : '';

		// Clear other messages
		if(clear) {

			$('[data-wsf-message][data-wsf-instance-id="' + this.form_instance_id + '"]').remove();
		}

		// Scroll top
		switch(scroll_top) {

			case 'instant' :
			case 'on' :			// Legacy

				$('html,body').scrollTop(scroll_position);

				break;

			// Smooth
			case 'smooth' :

				scroll_top_duration = (scroll_top_duration == '') ? 0 : parseInt(scroll_top_duration, 10);

				$('html,body').animate({

					scrollTop: scroll_position

				}, scroll_top_duration);

				break;
		}

		var mask_wrapper_values = {

			'message':				message,
			'mask_wrapper_class':	mask_wrapper_class 
		};

		var message_div = $('<div/>', { html: this.mask_parse(mask_wrapper, mask_wrapper_values) });
		message_div.attr('role', 'alert');
		message_div.attr('data-wsf-message', '');
		message_div.attr('data-wsf-instance-id', this.form_instance_id);

		// Hide form?
		if(form_hide) { this.form_obj.hide(); }

		// Render message
		switch(method) {

			// Before
			case 'before' :

				message_div.insertBefore(this.form_obj);
				break;

			// After
			case 'after' :

				message_div.insertAfter(this.form_obj);
				break;
		}

		// Process next action
		var ws_this = this;

		duration = parseInt(duration, 10);

		if(duration > 0) {

			setTimeout(function() {

				// Should this message be removed?
				if(message_hide) { message_div.remove(); }

				// Should the form be shown?
				if(form_show) { ws_this.form_canvas_obj.show(); }

				// Process next js_action
				ws_this.action_js_process_next();

			}, duration);

		} else {

			// Process next js_action
			ws_this.action_js_process_next();
		}
	}
	// JS Action - Run JavaScript
	$.WS_Form.prototype.action_javascript = function(action_javascript) {

		try {

			// Run JavaScript
			$.globalEval('(function($) {' + action_javascript + '})(jQuery);');

			// Log event
			this.log('log_javascript', 'action');

		} catch(e) {

			this.error('error_js', action_javascript);
		}

		// Process next js_action
		this.action_js_process_next();
	}
	// Text input and textarea character and word count
	$.WS_Form.prototype.form_character_word_count = function(obj) {

		var ws_this = this;
		if(typeof(obj) === 'undefined') { obj = this.form_canvas_obj; }

		// Run through each input that accepts text
		for(var field_id in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(field_id)) { continue; }

			var field = this.field_data_cache[field_id];

			// Process help?
			var help = this.get_object_meta_value(field, 'help', '', false, true);
			var process_help = (

				(help.indexOf('#character_') !== -1) ||
				(help.indexOf('#word_') !== -1)
			);

			// Process min or max?
			var process_min_max = (

				this.has_object_meta_key(field, 'min_length') ||
				this.has_object_meta_key(field, 'max_length') ||
				this.has_object_meta_key(field, 'min_length_words') ||
				this.has_object_meta_key(field, 'max_length_words')
			);

			if(process_min_max || process_help) {

				// Process count functionality on field
				var field_obj = $('#' + this.form_id_prefix + 'field-' + field_id, obj);
				if(!field_obj.length) { field_obj = $('[id^="' + this.form_id_prefix + 'field-' + field_id + '-"]:not([data-init-char-word-count]):not(iframe)', obj); }

				field_obj.each(function() {

					// Flag so it only initializes once
					$(this).attr('data-init-char-word-count', '');

					if(ws_this.form_character_word_count_process($(this))) {

						$(this).on('keyup change paste', function() { ws_this.form_character_word_count_process($(this)); });
					}
				});
			}
		}
	}

	// Text input and textarea character and word count - Process
	$.WS_Form.prototype.form_character_word_count_process = function(obj) {

		// Get minimum and maximum character count
		var field = this.get_field(obj);

		var min_length = this.get_object_meta_value(field, 'min_length', '');
		min_length = (parseInt(min_length, 10) > 0) ? parseInt(min_length, 10) : false;

		var max_length = this.get_object_meta_value(field, 'max_length', '');
		max_length = (parseInt(max_length, 10) > 0) ? parseInt(max_length, 10) : false;

		// Get minimum and maximum word length
		var min_length_words = this.get_object_meta_value(field, 'min_length_words', '');
		min_length_words = (parseInt(min_length_words, 10) > 0) ? parseInt(min_length_words, 10) : false;

		var max_length_words = this.get_object_meta_value(field, 'max_length_words', '');
		max_length_words = (parseInt(max_length_words, 10) > 0) ? parseInt(max_length_words, 10) : false;

		// Calculate sizes
		var val = obj.val();

		// Check value is a string
		if(typeof(val) !== 'string') { return; }

		var character_count = val.length;
		var character_remaining = (max_length !== false) ? max_length - character_count : false;
		if(character_remaining < 0) { character_remaining = 0; }

		var word_count = this.get_word_count(val);
		var word_remaining = (max_length_words !== false) ? max_length_words - word_count : false;
		if(word_remaining < 0) { word_remaining = 0; }

		// Check minimum and maximums counts
		var count_invalid = false;
		var count_invalid_message_array = [];

		if((min_length !== false) && (character_count < min_length)) {

			count_invalid_message_array.push(this.language('error_min_length', min_length));
			count_invalid = true;
		}
		if((max_length !== false) && (character_count > max_length)) {

			count_invalid_message_array.push(this.language('error_max_length', max_length));
			count_invalid = true;
		}
		if((min_length_words !== false) && (word_count < min_length_words)) {

			count_invalid_message_array.push(this.language('error_min_length_words', min_length_words));
			count_invalid = true;
		}
		if((max_length_words !== false) && (word_count > max_length_words)) {

			count_invalid_message_array.push(this.language('error_max_length_words', max_length_words));
			count_invalid = true;
		}

		// Check if required
		if(
			(typeof(obj.attr('required')) !== 'undefined') ||
			(val.length > 0)
		) {

			// Check if count_invalid
			if(count_invalid) {

				// Set invalid feedback
				this.set_invalid_feedback(obj, count_invalid_message_array.join(' / '));

			} else {

				// Reset invalid feedback
				this.set_invalid_feedback(obj, '');
			}

		} else {

			// Reset invalid feedback
			this.set_invalid_feedback(obj, '');
		}

		// Process help
		var help = this.get_object_meta_value(field, 'help', '', false, true);

		// If #character_ and #word_ not present, don't bother processing
		if(
			(help.indexOf('#character_') === -1) &&
			(help.indexOf('#word_') === -1)
		) {
			return true;
		}

		// Get language
		var character_singular = this.language('character_singular');
		var character_plural = this.language('character_plural');
		var word_singular = this.language('word_singular');
		var word_plural = this.language('word_plural');

		// Set mask values
		var mask_values_help = {

			// Characters
			'character_count':				character_count,
			'character_count_label':		(character_count == 1 ? character_singular : character_plural),
			'character_remaining':			(character_remaining !== false) ? character_remaining : '',
			'character_remaining_label':	(character_remaining == 1 ? character_singular : character_plural),
			'character_min':				(min_length !== false) ? min_length : '',
			'character_min_label':			(min_length !== false) ? (min_length == 1 ? character_singular : character_plural) : '',
			'character_max':				(max_length !== false) ? max_length : '',
			'character_max_label':			(max_length !== false) ? (max_length == 1 ? character_singular : character_plural) : '',

			// Words
			'word_count':			word_count,
			'word_count_label':		(word_count == 1 ? word_singular : word_plural),
			'word_remaining':		(word_remaining !== false) ? word_remaining : '',
			'word_remaining_label': (word_remaining == 1 ? word_singular : word_plural),
			'word_min':				(min_length_words !== false) ? min_length_words : '',
			'word_min_label':		(min_length_words !== false) ? (min_length_words == 1 ? word_singular : word_plural) : '',
			'word_max':				(max_length_words !== false) ? max_length_words : '',
			'word_max_label':		(max_length_words !== false) ? (max_length_words == 1 ? word_singular : word_plural) : ''
		};

		// Parse help mask
		var help_parsed = this.mask_parse(help, mask_values_help);

		// Update help HTML
		var help_obj = this.get_help_obj(obj);
		help_obj.html(help_parsed);

		return true;
	}

	// Get word count of a string
	$.WS_Form.prototype.get_word_count = function(input_string) {

		// Trim input string
		input_string = input_string.trim();

		// If string is empty, return 0
		if(input_string.length == 0) { return 0; }

		// Return word count
		return input_string.trim().replace(/\s+/gi, ' ').split(' ').length;
	}

	// Form - Statistics
	$.WS_Form.prototype.form_stat = function() {

		// Add view
		if(ws_form_settings.stat) { this.form_stat_add_view(); }
	}

	// Add view statistic
	$.WS_Form.prototype.form_stat_add_view = function() {

		// Call AJAX
		$.ajax({ method: 'POST', url: ws_form_settings.add_view_url, data: { wsffid: this.form_id } });
	}

	// Initialize forms function
	window.wsf_form_instances = [];

	window.wsf_form_init = function(force_reload, reset_events, container) {

		if(typeof(force_reload) === 'undefined') { force_reload = false; }
		if(typeof(reset_events) === 'undefined') { reset_events = false; }
		if(typeof(container) === 'undefined') {

			var forms = $('.wsf-form');

		} else {

			var forms = $('.wsf-form', container);
		}

		if(!$('.wsf-form').length) { return; }

		// Get highest instance ID
		var set_instance_id = 0;
		var instance_id_array = [];

		$('.wsf-form').each(function() {

			if(typeof($(this).attr('data-instance-id')) === 'undefined') { return; }

			// Get instance ID
			var instance_id_single = parseInt($(this).attr('data-instance-id'), 10);

			// Check for duplicate instance ID
			if(instance_id_array.indexOf(instance_id_single) !== -1) {

				// If duplicate, remove the data-instance-id so it is reset
				$(this).removeAttr('data-instance-id');

			} else {

				// Check if this is the highest instance ID
				if(instance_id_single > set_instance_id) { set_instance_id = instance_id_single; }
			}

			instance_id_array.push(instance_id_single);
		});

		// Increment to next instance ID
		set_instance_id++;

		// Render each form
		forms.each(function() {

			// Skip forms already initialized
			if(!force_reload && (typeof($(this).attr('data-wsf-rendered')) !== 'undefined')) { return; }

			// Reset events
			if(reset_events) { $(this).off(); }

			// Set instance ID
			if(typeof($(this).attr('data-instance-id')) === 'undefined') {

				// Set ID (Only if custom ID not set)
				if(typeof($(this).attr('data-wsf-custom-id')) === 'undefined') {

					$(this).attr('id', 'ws-form-' + set_instance_id);
				}

				// Set instance ID
				$(this).attr('data-instance-id', set_instance_id);

				set_instance_id++;
			}

			// Get attributes
			var id = $(this).attr('id');
			var form_id = $(this).attr('data-id');
			var instance_id = $(this).attr('data-instance-id');

			if(id && form_id && instance_id) {

				// Initiate new WS Form object
				var ws_form = new $.WS_Form();

				// Save to wsf_form_instances array
				window.wsf_form_instances[instance_id] = ws_form;

				// Render
				ws_form.render({

					'obj' :			'#' + id,
					'form_id':		form_id
				});
			}
		});
	}

	// On load
	$(function() { wsf_form_init(); });

})(jQuery);
