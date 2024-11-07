(function($) {

	'use strict';

	// Form analytics
	$.WS_Form.prototype.form_analytics = function() {

		var ws_this = this;

		// Google Analytics
		var analytics_google = this.get_object_meta_value(this.form, 'analytics_google', false);

		// Check to see if Google Analytics is installed
		if(analytics_google || this.action_ga) { this.form_analytics_google(); }
	}

	// Form analytics - Google
	$.WS_Form.prototype.form_analytics_google = function(total_ms_start) {

		var ws_this = this;
		var analytics_google_functions = $.WS_Form.analytics.google.functions;

		// Timeout check
		if(typeof(total_ms_start) === 'undefined') { total_ms_start = new Date().getTime(); }
		if((new Date().getTime() - total_ms_start) > this.timeout_analytics_google) {

			this.error('error_timeout_analytics_google', '', 'analytics');
			return false;
		}

		// Run through Google functions
		var analytics_google_function_found = false;
		for(var analytics_google_function in analytics_google_functions) {

			if(!analytics_google_functions.hasOwnProperty(analytics_google_function)) { continue; }

			var analytics_google_function_config = analytics_google_functions[analytics_google_function];

			// Check to see if the window function is available
			if(window[analytics_google_function]) {

				// Found
				analytics_google_function_found = true;

				// Log
				this.log(analytics_google_function_config.log_found, '', 'analytics');

				// Get Google configuration
				var analytics_enabled = this.get_object_meta_value(this.form, 'analytics_google', false);
				var process_forms = analytics_enabled && this.get_object_meta_value(this.form, 'analytics_google_event_form', true);
				var process_tabs = analytics_enabled && this.get_object_meta_value(this.form, 'analytics_google_event_tab', true);
				var process_fields = analytics_enabled && this.get_object_meta_value(this.form, 'analytics_google_event_field', true);

				// Process
				this.form_analytics_process('google', analytics_google_function, process_fields, process_tabs, process_forms);

				// Save which function should be used for future event firing
				this.analytics_function['google'] = analytics_google_function;

				break;
			}
		}

		// Not found, retry
		if(!analytics_google_function_found) {

			setTimeout(function() { ws_this.form_analytics_google(total_ms_start); }, this.timeout_interval);
		}
	}

	// Form analytics - Process
	$.WS_Form.prototype.form_analytics_process = function(type, type_function, process_fields, process_tabs, process_forms) {

		// Forms
		if(process_forms) { this.form_analytics_process_forms(type, type_function); }

		// Tabs
		if(process_tabs && (Object.keys(this.form.groups).length > 0)) { this.form_analytics_process_tabs(type, type_function); }

		// Fields
		if(process_fields) { this.form_analytics_process_fields(type, type_function); }
	}

	// Form analytics - Process - Forms
	$.WS_Form.prototype.form_analytics_process_forms = function(type, type_function) {

		var ws_this = this;

		// Run through all tabs and set up analytics events for this type and function
		var analytics_function = $.WS_Form.analytics[type].functions[type_function];
		var analytics_label = $.WS_Form.analytics[type].label;
		var analytics_function_label = analytics_function.label;

		// Set up on click event for each tab
		$(document).on('wsf-submit wsf-save', function(event, form_object, form_id, instance_id, form_obj, form_canvas_obj) {

			// Check event
			if(
				(form_obj !== ws_this.form_obj)

			) { return; }

			// Parse values
			var parse_values = {

				'category' :	ws_this.js_string_encode(ws_this.language('analytics_category', ws_this.form.label)),	// Category
				'action' : 		(event.type === 'wsf-submit') ? 'Submit' : 'Save',										// Action
				'label' :		'',																						// Label
				'value' : 		'null'																					// Value
			};

			// Fire event
			ws_this.form_analytics_event_fire(

				type,
				type_function,
				parse_values
			);

			// Fire at a data layer level (Tag Manager form submitted trigger)
			if(
				(event.type === 'wsf-submit') &&
				(typeof(window.dataLayer) !== 'undefined') &&
				(typeof(window.dataLayer.push) === 'function')
			) {

				// Set up data layer args
				var data_layer_args = {

					'event' : 					'gtm.formSubmit',
					'gtm.element' : 			ws_this.form_obj[0],
					'wsf.form.id' : 			ws_this.js_string_encode(ws_this.form_id),
					'wsf.form.instance_id' : 	ws_this.js_string_encode(ws_this.form_instance_id),
					'wsf.form.label' : 			ws_this.js_string_encode(ws_this.form.label),
				};

				// Add gtm.elementId
				if(typeof(ws_this.form_obj.attr('id')) !== 'undefined') {

					data_layer_args['gtm.elementId'] = ws_this.form_obj.attr('id');
				}

				// Add gtm.elementClasses
				if(typeof(ws_this.form_obj.attr('class')) !== 'undefined') {

					data_layer_args['gtm.elementClasses'] = ws_this.form_obj.attr('class');
				}

				// Add gtm.elementUrl
				if(typeof(ws_this.form_obj.attr('action')) !== 'undefined') {

					data_layer_args['gtm.elementUrl'] = ws_this.form_obj.attr('action');
				}

				// Push args to data layer
				window.dataLayer.push(data_layer_args);

				// Log event
				ws_this.log('log_analytics_event_fired', 'dataLayer gtm.formSubmit event', 'analytics');
			}
		});

		// Log event
		ws_this.log('log_analytics_event_form', analytics_label + ' (' + analytics_function_label + ')', 'analytics');
	}

	// Form analytics - Process - Tabs
	$.WS_Form.prototype.form_analytics_process_tabs = function(type, type_function) {

		var ws_this = this;

		// Run through all tabs and set up analytics events for this type and function
		var analytics_function = $.WS_Form.analytics[type].functions[type_function];
		var analytics_label = $.WS_Form.analytics[type].label;
		var analytics_function_label = analytics_function.label;

		// Get selector href
		var selector_href = (typeof(this.framework.tabs.public.selector_href) !== 'undefined') ? this.framework.tabs.public.selector_href : 'href';

		// Set up on click event for each tab
		$('[' + selector_href + '^="#' + this.form_id_prefix + 'group-"]:not([data-init-analytics-tab])', this.form_canvas_obj).each(function() {

			$(this).attr('data-init-analytics-tab', '');

			$(this).on('wsf-click', function () {

				var group_index = $(this).parent().index();

				if($(this).attr('data-analytics-event-fired') === undefined) {

					var group = ws_this.form.groups[group_index];

					// Parse values
					var parse_values = {

						'category' :	ws_this.js_string_encode(ws_this.language('analytics_category', ws_this.form.label)),	// Category
						'action' : 		'Tab',																					// Action
						'label' :		ws_this.js_string_encode(group.label),													// Label
						'value' : 		'null'																					// Value
					};

					// Fire event
					ws_this.form_analytics_event_fire(

						type,
						type_function,
						parse_values
					);

					$(this).attr('data-analytics-event-fired', 'true');
				}
			});
		});

		// Log event
		ws_this.log('log_analytics_event_tab', analytics_label + ' (' + analytics_function_label + ')', 'analytics');
	}

	// Form analytics - Process - Fields
	$.WS_Form.prototype.form_analytics_process_fields = function(type, type_function) {

		var ws_this = this;

		// Run through all fields and set up analytics events for this type and function
		var analytics_function = $.WS_Form.analytics[type].functions[type_function];
		var analytics_label = $.WS_Form.analytics[type].label;
		var analytics_function_label = analytics_function.label;

		for(var field_index in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(field_index)) { continue; }

			var field_type = this.field_data_cache[field_index].type;
			var field_type_config = $.WS_Form.field_type_cache[field_type];

			// Get events
			if(typeof(field_type_config.events) === 'undefined') { continue; }
			var analytics_event = field_type_config.events.event;

			// Get field ID
			var field_id = this.field_data_cache[field_index].id;

			// Check to see if this field is submitted as an array
			var submit_array = (typeof(field_type_config.submit_array) !== 'undefined') ? field_type_config.submit_array : false;

			// Check to see if field is in a repeatable section
			var field_wrapper = $('[data-type][data-id="' + field_id + '"]', this.form_canvas_obj);

			// Run through each wrapper found (there might be repeatables)
			field_wrapper.each(function() {

				var section_repeatable_index = ws_this.get_section_repeatable_index($(this));
				var section_repeatable_suffix = (section_repeatable_index > 0) ? '[' + section_repeatable_index + ']' : '';

				if(submit_array) {

					var field_obj = $('[name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '[]"]:not([data-init-analytics-field])', ws_this.form_canvas_obj);

				} else {

					var field_obj = $('[name="' + ws_form_settings.field_prefix + field_id + section_repeatable_suffix + '"]:not([data-init-analytics-field])', ws_this.form_canvas_obj);
				}

				if(field_obj.length) {

					// Flag so it only initializes once
					field_obj.attr('data-init-analytics-field', '');

					// Create event
					field_obj.on(analytics_event, function() {

						if($(this).attr('data-analytics-event-fired') === undefined) {

							var field = ws_this.get_field($(this));
							var analytics_event_action = $.WS_Form.field_type_cache[field.type].events.event_action;

							// Parse values
							var parse_values = {

								'category' : 	ws_this.js_string_encode(ws_this.language('analytics_category', ws_this.form.label)),	// Category						// Category
								'action' :		analytics_event_action,																	// Action
								'label' :		ws_this.js_string_encode(field.label),													// Label
								'value' : 		'null'																					// Value
							}

							// Fire event
							ws_this.form_analytics_event_fire(

								type,
								type_function,
								parse_values
							);

							$(this).attr('data-analytics-event-fired', 'true');
						}
					});
				}
			});
		}

		// Log event
		ws_this.log('log_analytics_event_field', analytics_label + ' (' + analytics_function_label + ')', 'analytics');
	}

	// Form analytics - Fire event
	$.WS_Form.prototype.form_analytics_event_fire = function(type, type_function, parse_values) {

		// Run through all fields and set up analytics events for this type and function
		var analytics_function = $.WS_Form.analytics[type].functions[type_function];
		var analytics_label = $.WS_Form.analytics[type].label;
		var analytics_function_label = $.WS_Form.analytics[type].functions[type_function].label;

		// Parse event field args
		var analytics_event_function = analytics_function.analytics_event_function;
		var analytics_event_function_parsed = this.mask_parse(analytics_event_function, parse_values);

		// Call function
		if((type_function == 'js') || (typeof(window[type_function]) === 'function')) {

			$.globalEval('(function($) {' + analytics_event_function_parsed + '})(jQuery);');

			// Log event
			this.log('log_analytics_event_fired', analytics_label + ' (' + analytics_function_label + ') ' + analytics_event_function_parsed, 'analytics');

		} else {

			// Log error
			this.error('log_analytics_event_failed', analytics_label + ' (' + analytics_function_label + ') ' + analytics_event_function_parsed, 'analytics');
		}
	}

	// JS Action - Conversion
	$.WS_Form.prototype.action_conversion = function(type, parse_values) {

		// Check analytics type exists in config
		if(typeof(type) === 'undefined') { return false; }
		if(typeof($.WS_Form.analytics[type]) === 'undefined') { return false; }
		if(typeof($.WS_Form.analytics[type].functions) === 'undefined') { return false; }

		// Get type_function
		if(typeof(this.analytics_function[type]) !== 'undefined') {

			var type_function = this.analytics_function[type];

		} else {

			// Get first function
			var type_function = Object.keys($.WS_Form.analytics[type].functions)[0];
		}

		// Fire event
		if(typeof(this.form_analytics_event_fire) === 'function') {

			this.form_analytics_event_fire(

				type,
				type_function,
				parse_values
			);
		}

		// Process next js_action
		this.action_js_process_next();
	}
})(jQuery);
