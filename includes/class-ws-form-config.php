<?php

	/**
	 * Configuration settings
	 * Pro Version
	 */

	class WS_Form_Config {

		// Caches
		public static $meta_keys = array();
		public static $file_types = false;
		public static $settings_plugin = array();
		public static $frameworks = array();
		public static $parse_variables = array();
		public static $parse_variables_repairable = array();
		public static $parse_variables_secure = false;
		public static $tracking = array();
		public static $ecommerce = false;
		public static $data_sources = false;
		public static $field_types = array();
		public static $field_types_flat = array();

		// Get full public or admin config
		public static function get_config($parameters = false, $field_types = array(), $is_admin = null) {

			// Determine if this is an admin or public API request
			if($is_admin === null) {
				$is_admin = (WS_Form_Common::get_query_var('wsf_fia', 'false') == 'true');
			}
			$form_id = WS_Form_Common::get_query_var('form_id', 0);

			// Standard response
			$config = array();

			// Different for admin or public
			if($is_admin) {

				$config['meta_keys'] = self::get_meta_keys($form_id, false);
				$config['field_types'] = self::get_field_types(false);
				$config['file_types'] = self::get_file_types(false);
				$config['settings_plugin'] = self::get_settings_plugin(false);
				$config['settings_form'] = self::get_settings_form_admin();
				$config['frameworks'] = self::get_frameworks(false);
				$config['parse_variables'] = self::get_parse_variables(false);
				$config['parse_variable_help'] = self::get_parse_variable_help($form_id, false);
				$config['parse_variables_repairable'] = self::get_parse_variables_repairable(false);
				$config['calc'] = self::get_calc();
				$config['tracking'] = self::get_tracking(false);
				$config['ecommerce'] = self::get_ecommerce();
				$config['actions'] = WS_Form_Action::get_settings();
				$config['data_sources'] = WS_Form_Data_Source::get_settings();

				$ws_form_template = new WS_Form_Template();
				$ws_form_template->type = 'section';
				$config['templates_section'] = $ws_form_template->get_settings();

			} else {

				$config['meta_keys'] = self::get_meta_keys($form_id, true);
				$config['field_types'] = self::get_field_types_public($field_types);
				$config['settings_plugin'] = self::get_settings_plugin();
				$config['settings_form'] = self::get_settings_form_public();
				$config['frameworks'] = self::get_frameworks();
				$config['parse_variables'] = self::get_parse_variables();
				// Base 64 encoded function otherwise Google's tag assistant and other plugins think this is actual tagging javascript
				$config['analytics'] = base64_encode(json_encode(self::get_analytics()));
				$config['tracking'] = self::get_tracking();
				$config['ecommerce'] = self::get_ecommerce();

				// Debug
				if(WS_Form_Common::debug_enabled()) {

					$config['debug'] = self::get_debug();
				}
			}

			// Add generic settings (Shared between both admin and public, e.g. language)
			$config['settings_form'] = array_merge_recursive($config['settings_form'], self::get_settings_form(!$is_admin));

			return $config;
		}

		public static function get_settings_form_admin() {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_settings_form_admin();
		}

		public static function get_calc() {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_calc();
		}

		public static function get_parse_variable_help($form_id = 0, $public = true, $group = false, $group_first = false) {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_parse_variable_help($form_id, $public, $group, $group_first);
		}

		public static function get_system() {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_system();
		}

		public static function get_file_types() {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_file_types();
		}

		public static function get_patterns() {

			include_once 'config/class-ws-form-config-admin.php';
			$ws_form_config_admin = new WS_Form_Config_Admin();
			return $ws_form_config_admin->get_patterns();
		}

		public static function get_settings_conditional($public = true) {

			include_once 'config/class-ws-form-config-conditional.php';
			$ws_form_config_conditional = new WS_Form_Config_Conditional();
			return $ws_form_config_conditional->get_settings_conditional($public);
		}

		public static function get_settings_form_public() {

			include_once 'config/class-ws-form-config-public.php';
			$ws_form_config_public = new WS_Form_Config_Public();
			return $ws_form_config_public->get_settings_form_public();
		}

		public static function get_field_types_public($field_types_filter) {

			include_once 'config/class-ws-form-config-public.php';
			$ws_form_config_public = new WS_Form_Config_Public();
			return $ws_form_config_public->get_field_types_public($field_types_filter);
		}

		public static function get_logo_svg($color_1 = '#002d5d', $color_2 = '#a7a8aa') {

			include_once 'config/class-ws-form-config-svg.php';
			$ws_form_config_svg = new WS_Form_Config_SVG();
			return $ws_form_config_svg->get_logo_svg($color_1, $color_2);
		}

		public static function get_icon_24_svg($id = '') {

			include_once 'config/class-ws-form-config-svg.php';
			$ws_form_config_svg = new WS_Form_Config_SVG();
			return $ws_form_config_svg->get_icon_24_svg($id);
		}

		public static function get_icon_16_svg($id = '') {

			include_once 'config/class-ws-form-config-svg.php';
			$ws_form_config_svg = new WS_Form_Config_SVG();
			return $ws_form_config_svg->get_icon_16_svg($id);
		}

		public static function get_debug() {

			include_once 'config/class-ws-form-config-debug.php';
			$ws_form_config_debug = new WS_Form_Config_Debug();
			return $ws_form_config_debug->get_debug();
		}

		// Configuration - Field Types
		public static function get_field_types($public = true) {

			// Check cache
			if(isset(self::$field_types[$public])) { return self::$field_types[$public]; }

			$field_types = array(

				'basic' => array(

					'label'	=> __('Basic', 'ws-form'),
					'types' => array(

						'text' => array (

							'label'				=>	__('Text', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/text/',
							'label_default'		=>	__('Text', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('single line', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'		=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'cw==', 'cw!=', 'cw>', 'cw<', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'		=>	array('visibility', 'required', 'focus', 'value', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'		=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'	=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'readonly', 'required', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'placeholder', 'pattern', 'list', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_text', 'hidden_bypass', 'transform', 'inputmode'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value', 'placeholder', 'help_count_char_word', 'autocomplete_text', 'inputmode'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'readonly', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'pattern', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Transform', 'ws-form'),
											'meta_keys'	=>	array('transform')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Autocomplete
								'datalist'	=> array(

									'label'		=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'textarea' => array (

							'label'				=>	__('Text Area', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/textarea/',
							'label_default'		=>	__('Text Area', 'ws-form'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'wpautop_conditions'	=>	array('input_type_textarea' => 'tinymce'),
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'label_inside'		=>	true,
							'mappable'			=>	true,
							'keyword'			=>	__('paragraph visual editor tinymce codemirror', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'		=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'cw==', 'cw!=', 'cw>', 'cw<', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'		=>	array('visibility', 'required', 'focus', 'value_textarea', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'		=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Fields
							'mask_field'					=>	'#pre_label<textarea id="#id" name="#name"#attributes>#value</textarea>#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'readonly', 'required', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'placeholder', 'spellcheck', 'cols', 'rows', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'input_type_textarea', 'input_type_textarea_toolbar', 'hidden_bypass', 'autocomplete', 'transform', 'inputmode'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'input_type_textarea', 'input_type_textarea_toolbar', 'required', 'hidden', 'default_value_textarea', 'placeholder', 'help_count_char_word_with_default', 'autocomplete', 'inputmode'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'rows')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'cols', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Transform', 'ws-form'),
											'meta_keys'	=>	array('transform')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'number' => array (

							'label'				=>	__('Number', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/number/',
							'label_default'		=>	__('Number', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('digit', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'value_number', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'min', 'max', 'step', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-number',
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="number" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'min', 'max', 'step', 'disabled', 'readonly', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_number', 'hidden_bypass', 'number_no_spinner'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value_number', 'placeholder', 'help', 'autocomplete_number'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'number_no_spinner')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'min', 'max', 'step', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'		=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'tel' => array (

							'label'				=>	__('Phone', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/tel/',
							'label_default'		=>	__('Phone', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	false,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('telephone cell fax', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'value_tel', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-email-tel-url',
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="tel" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'readonly', 'min_length', 'max_length', 'pattern_tel', 'list', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'input_mask', 'custom_attributes', 'autocomplete_tel', 'hidden_bypass', 'intl_tel_input'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'required', 'hidden', 'default_value_tel', 'placeholder', 'help_count_char', 'autocomplete_tel'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('International Telephone Input', 'ws-form'),
											'meta_keys'	=>	array('intl_tel_input', 'intl_tel_input_allow_dropdown', 'intl_tel_input_auto_placeholder', 'intl_tel_input_national_mode', 'intl_tel_input_separate_dial_code', 'intl_tel_input_format', 'intl_tel_input_initial_country', 'intl_tel_input_only_countries', 'intl_tel_input_preferred_countries')
										),

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'		=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled','readonly', 'min_length', 'max_length', 'input_mask', 'pattern_tel', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Labels', 'ws-form'),
											'meta_keys'	=>	array('intl_tel_input_label_number', 'intl_tel_input_label_country_code', 'intl_tel_input_label_short', 'intl_tel_input_label_long')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'		=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'email' => array (

							'label'					=>	__('Email', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'				=>	'/knowledgebase/email/',
							'label_default'			=>	__('Email', 'ws-form'),
							'data_source'			=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'			=>	true,
							'submit_edit'			=>	true,
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	true,
							'value_out'				=>	true,
							'mappable'				=>	true,
							'label_inside'			=>	true,
							'progress'				=>	true,
							'conditional'			=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'regex_email', 'regex_email_not', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'value_email', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-email-tel-url',
							'events'				=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'			=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'		=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'						=>	'#pre_label<input type="email" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'				=>	array('class', 'multiple_email', 'min_length', 'max_length', 'pattern', 'list', 'disabled', 'readonly', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_email', 'hidden_bypass', 'transform'),
							'mask_field_label'					=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'		=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value_email', 'multiple_email', 'placeholder', 'help_count_char', 'autocomplete_email'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'min_length', 'max_length', 'pattern', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Transform', 'ws-form'),
											'meta_keys'	=>	array('transform')
										),

										array(
											'label'		=>	__('Allow or Deny', 'ws-form'),
											'meta_keys'	=> array('allow_deny', 'allow_deny_values', 'allow_deny_message')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'		=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'url' => array (

							'label'				=>	__('URL', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/url/',
							'label_default'		=>	__('URL', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('website', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'regex_url', 'regex_url_not', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'value_url', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-email-tel-url',
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="url" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'min_length', 'max_length', 'list', 'disabled', 'readonly', 'required', 'placeholder', 'pattern', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_url', 'hidden_bypass'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value_url', 'placeholder', 'help_count_char', 'autocomplete_url'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled','readonly', 'min_length', 'max_length', 'pattern', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'			=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						)
					)
				),

				'choice' => array(

					'label'	=> __('Choice', 'ws-form'),
					'types' => array(

						'select' => array (

							'label'				=>	__('Select', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/select/',
							'label_default'		=>	__('Select', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_select'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'submit_array'		=>	true,
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('dropdown', 'ws-form'),
							'invalid_feedback'	=>	__('Please select a valid #label_lowercase.', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'data_grid_fields'			=>	'data_grid_select',
								'option_text'				=>	'select_field_label',
								'logics_enabled'			=>	array('selected', 'selected_not', 'selected_any', 'selected_any_not', 'rs==', 'rs!=', 'rs>', 'rs<', 'selected_value_equals', 'selected_value_equals_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'			=>	array('visibility', 'required', 'focus', 'value_row_select', 'value_row_deselect', 'value_row_select_value', 'value_row_deselect_value', 'value_row_select_all', 'value_row_deselect_all', 'value_row_disabled', 'value_row_not_disabled', 'value_row_class_add', 'value_row_class_remove', 'value', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'select_min', 'select_max', 'reset', 'value_row_reset'),
								'condition_event'			=>	'change'
							),
							'events'	=>	array(

								'event'						=>	'change',
								'event_action'				=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'					=>	'<optgroup label="#group_label"#disabled>#group</optgroup>',
							'mask_group_label'				=>	'#group_label',

							// Rows
							'mask_row'						=>	'<option id="#row_id" data-id="#data_id" value="#select_field_value"#attributes>#select_field_label</option>',
							'mask_row_placeholder'			=>	'<option data-id="0" value="" data-placeholder>#value</option>',
							'mask_row_attributes'			=>	array('default', 'disabled'),
							'mask_row_lookups'				=>	array('select_field_value', 'select_field_label', 'select_field_parse_variable', 'select_cascade_field_filter'),
							'datagrid_column_value'			=>	'select_field_value',
							'mask_row_default' 				=>	' selected',

							// Fields
							'mask_field'					=>	'#pre_label<select id="#id" name="#name"#attributes>#datalist</select>#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'size', 'multiple', 'required', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'dedupe_value_scope', 'select_cascade_ajax', 'hidden_bypass', 'autocomplete', 'select2'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=> array('label_render', 'required', 'hidden', 'multiple', 'size', 'placeholder_row', 'help', 'autocomplete'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Select2', 'ws-form'),
											'meta_keys'	=>	array('select2_intro', 'select2', 'select2_ajax', 'select2_no_match', 'select2_tags')
										),
										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'select_min', 'select_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),
										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),
										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Options
								'options'	=> array(

									'label'			=>	__('Options', 'ws-form'),
									'meta_keys'		=> array('data_grid_select', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('select_field_label', 'select_field_value', 'select_field_parse_variable')
										),
										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('select_cascade', 'select_cascade_field_filter', 'select_cascade_field_filter_comma', 'select_cascade_field_id', 'select_cascade_no_match', 'select_cascade_option_text_no_rows', 'select_cascade_ajax', 'select_cascade_ajax_option_text_loading')
										)
									)
								)
							)
						),

						'checkbox' => array (

							'label'				=>	__('Checkbox', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/checkbox/',
							'label_default'		=>	__('Checkbox', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_checkbox'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'submit_array'		=>	true,
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'keyword'			=>	__('buttons toggle switches colors images', 'ws-form'),
							'invalid_feedback'	=>	__('This checkbox is required.', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'data_grid_fields'		=>	'data_grid_checkbox',
								'option_text'			=>	'checkbox_field_label',
								'logics_enabled'		=>	array('checked', 'checked_not', 'checked_any', 'checked_any_not', 'rc==', 'rc!=', 'rc>', 'rc<', 'checked_value_equals', 'checked_value_equals', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'		=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper', 'value_row_check', 'value_row_uncheck', 'value_row_check_value','value_row_uncheck_value', 'value_row_check_all', 'value_row_uncheck_all', 'value_row_focus', 'value_row_required', 'value_row_not_required', 'value_row_disabled', 'value_row_not_disabled', 'value_row_visible', 'value_row_not_visible', 'value_row_class_add', 'value_row_class_remove', 'value_row_set_custom_validity', 'checkbox_min', 'checkbox_max', 'reset', 'clear'),
								'condition_event'		=>	'change',
								'condition_event_row'	=>	true
							),
							'events'	=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group_wrapper'		=>	'<div#attributes>#group</div>',
							'mask_group_label'			=>	'<legend>#group_label</legend>',

							// Rows
							'mask_row'					=>	'<div#attributes>#row_label</div>',
							'mask_row_attributes'		=>	array('class'),
							'mask_row_label'			=>	'<label id="#label_row_id" for="#row_id"#attributes>#row_field#checkbox_field_label#required</label>#invalid_feedback',
							'mask_row_label_attributes'	=>	array('class'),
							'mask_row_field'			=>	'<input type="checkbox" id="#row_id" name="#name" value="#checkbox_field_value"#attributes />',
							'mask_row_field_attributes'	=>	array('class', 'default', 'disabled', 'required', 'aria_labelledby', 'dedupe_value_scope', 'hidden_bypass'),
							'mask_row_lookups'			=>	array('checkbox_field_value', 'checkbox_field_label', 'checkbox_field_parse_variable', 'checkbox_cascade_field_filter'),
							'datagrid_column_value'		=>	'checkbox_field_value',
							'mask_row_default' 			=>	' checked',

							// Fields
							'mask_field'					=>	'#pre_label#datalist#post_label#invalid_feedback#help',
							'mask_field_label'				=>	'<label id="#label_id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),
//							'mask_field_label_hide_group'	=>	true,

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render_off', 'hidden', 'select_all', 'select_all_label', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Layout', 'ws-form'),
											'meta_keys'	=>	array('orientation', 'orientation_breakpoint_sizes')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('checkbox_min', 'checkbox_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),
										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),
										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Checkboxes
								'checkboxes' 	=> array(

									'label'		=>	__('Checkboxes', 'ws-form'),
									'meta_keys'	=> array('data_grid_checkbox', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('checkbox_field_label', 'checkbox_field_value', 'checkbox_field_parse_variable')
										),
										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('checkbox_cascade', 'checkbox_cascade_field_filter', 'checkbox_cascade_field_filter_comma', 'checkbox_cascade_field_id', 'checkbox_cascade_no_match')
										)
									)
								)
							)
						),

						'radio' => array (

							'label'				=>	__('Radio', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/radio/',
							'label_default'		=>	__('Radio', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_radio'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'submit_array'		=>	true,
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'keyword'			=>	__('buttons toggle switches colors images', 'ws-form'),
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'progress'			=>	true,
							'conditional'		=>	array(

								'data_grid_fields'		=>	'data_grid_radio',
								'option_text'			=>	'radio_field_label',
								'logics_enabled'		=>	array('checked', 'checked_not', 'checked_any', 'checked_any_not', 'checked_value_equals', 'checked_value_equals', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'		=>	array('visibility', 'required', 'class_add_wrapper', 'class_remove_wrapper', 'value_row_check', 'value_row_uncheck', 'value_row_check_value','value_row_uncheck_value', 'value_row_focus', 'value_row_disabled', 'value_row_not_disabled', 'value_row_visible', 'value_row_not_visible', 'value_row_class_add', 'value_row_class_remove', 'set_custom_validity', 'reset', 'clear'),
								'condition_event'		=>	'change',
								'condition_event_row'	=>	true
							),
							'events'	=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group_wrapper'		=>	'<div#attributes role="radiogroup">#group</div>',
							'mask_group_label'			=>	'<legend>#group_label</legend>',

							// Rows
							'mask_row'					=>	'<div#attributes>#row_label</div>',
							'mask_row_attributes'		=>	array('class'),
							'mask_row_label'			=>	'<label id="#label_row_id" for="#row_id" data-label-required-id="#label_id"#attributes>#row_field#radio_field_label</label>#invalid_feedback',
							'mask_row_label_attributes'	=>	array('class'),
							'mask_row_field'			=>	'<input type="radio" id="#row_id" name="#name" value="#radio_field_value"#attributes />',
							'mask_row_field_attributes'	=>	array('class', 'default', 'disabled', 'required_row', 'aria_labelledby', 'hidden', 'dedupe_value_scope', 'hidden_bypass'),
							'mask_row_lookups'			=>	array('radio_field_value', 'radio_field_label', 'radio_field_parse_variable', 'radio_cascade_field_filter'),
							'datagrid_column_value'		=>	'radio_field_value',
							'mask_row_default' 			=>	' checked',

							// Fields
							'mask_field'					=>	'#pre_label#datalist#post_label#help',
							'mask_field_attributes'			=>	array('required_attribute_no'),
							'mask_field_label'				=>	'<label id="#label_id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),
//							'mask_field_label_hide_group'	=>	true,

							'invalid_feedback_last_row'		=> true,

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'required_attribute_no', 'hidden', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Layout', 'ws-form'),
											'meta_keys'	=>	array('orientation', 'orientation_breakpoint_sizes')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),
										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),
										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Radios
								'radios'	=> array(

									'label'		=>	__('Radios', 'ws-form'),
									'meta_keys'	=> array('data_grid_radio', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('radio_field_label', 'radio_field_value', 'radio_field_parse_variable')
										),
										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('radio_cascade', 'radio_cascade_field_filter', 'radio_cascade_field_filter_comma', 'radio_cascade_field_id', 'radio_cascade_no_match')
										)
									)
								)
							)
						),

						'datetime' => array (

							'label'				=>	__('Date/Time', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/datetime/',
							'label_default'		=>	__('Date/Time', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'keyword'			=>	__('week month', 'ws-form'),
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('d==', 'd!=', 'd<', 'd>', 'd<=', 'd>=', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'value_datetime', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'compatibility_id'	=>	'input-datetime',
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'	=>	array('datalist_field_value', 'datalist_field_text'),

							// Fields
							'mask_field'					=>	'#pre_label<input id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('input_type_datetime', 'format_date', 'format_time', 'dow_start', 'class', 'disabled', 'required', 'readonly', 'min_date', 'max_date', 'inline', 'year_start', 'year_end', 'time_step', 'step', 'input_mask', 'pattern_date', 'list', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_datetime', 'hidden_bypass', 'inputmode_none'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'inline', 'input_type_datetime', 'format_date', 'format_time', 'default_value_datetime', 'help', 'autocomplete_datetime', 'inputmode_none'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'dow_start')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'min_date', 'max_date', 'year_start', 'year_end', 'disabled_week_days', 'disabled_dates', 'enabled_dates', 'time_step', 'step', 'input_mask', 'pattern_date', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Autocomplete
								'datalist'	=> array(

									'label'		=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'range' => array (

							'label'				=>	__('Range Slider', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/range/',
							'label_default'		=>	__('Range Slider', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'	=>	array('visibility', 'focus', 'value_range', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'min', 'max', 'step', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-range',
							'events'						=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid
							'trigger'				=> 'input',

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option value="#datalist_field_value" style="--wsf-position-tick-mark: #datalist_field_value_percentage%;" data-label="#datalist_field_text"></option>',
							'mask_row_lookups'	=>	array('datalist_field_value', 'datalist_field_text'),

							// Fields
							'mask_field'					=>	'#pre_label<input type="range" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'min_range', 'max_range', 'step', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'class_fill_lower_track', 'hidden_bypass', 'autocomplete_range'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'hidden', 'default_value_range', 'help_range', 'autocomplete_range'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'class_fill_lower_track')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'min_range', 'max_range', 'step', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Tick Marks
								'tickmarks'	=> array(

									'label'		=>	__('Tick Marks', 'ws-form'),
									'meta_keys'	=>	array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'color' => array (

							'label'				=>	__('Color', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/color/',
							'label_default'		=>	__('Color', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('c==', 'c!=', 'ch<', 'ch>', 'cs<', 'cs>', 'cl<', 'cl>', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'focus', 'value_color', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'compatibility_id'	=>	'input-color',
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option>#datalist_field_value</option>',
							'mask_row_lookups'	=>	array('datalist_field_value'),

							// Fields
							'mask_field'					=>	'#pre_label<input type="#color_type" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'required', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_color', 'hidden_bypass', 'transform'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value_color', 'help', 'autocomplete_color'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Transform', 'ws-form'),
											'meta_keys'	=>	array('transform')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'		=>	array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_value')
										)
									)
								)
							)
						),

						'rating' => array (

							'label'				=>	__('Rating', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/rating/',
							'label_default'		=>	__('Rating', 'ws-form'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'keyword'			=>	__('score review star', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'	=>	array('visibility', 'value_rating', 'class_add_wrapper', 'class_remove_wrapper', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'trigger'			=> 'input',

							'mask_field'					=>	'#pre_label<input data-rating type="number" id="#id" name="#name" value="#value"#attributes style="display:none;" />#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'required', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'rating_color_off', 'rating_color_on', 'hidden_bypass', 'readonly'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value_number', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'horizontal_align', 'rating_icon', 'rating_icon_html', 'rating_size', 'rating_color_off', 'rating_color_on')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('readonly', 'rating_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),										

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'			=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						)
					)
				),

				'advanced' => array(

					'label'	=> __('Advanced', 'ws-form'),
					'types' => array(

						'file' => array (

							'label'							=>	__('File Upload', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'						=>	'/knowledgebase/file/',
							'label_default'					=>	__('File Upload', 'ws-form'),
//							'label_position_force'			=>	'top',	// Prevent formatting issues with different label positioning. The label is the button.
							'submit_save'					=>	true,
							'submit_edit'					=>	false,
							'submit_array'					=>	true,
							'calc_in'						=>	false,
							'calc_out'						=>	false,
							'text_in'						=>	false,
							'text_out'						=>	true,
							'value_out'						=>	false,
							'mappable'						=>	true,
							'progress'						=>	true,
							'invalid_feedback'				=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'keyword'						=>	__('dropzonejs images gallery', 'ws-form'),
							'conditional'					=>	array(

								'logics_enabled'	=>	array('f==', 'f!=', 'f<', 'f>', 'file', 'file_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'click', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset_file', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'mdn-html_elements_input_input-file',
							'events'						=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Fields
							'mask_field'					=>	'#pre_label<input type="file" id="#id" name="#name"#attributes />#post_label#invalid_feedback#help',
							'mask_field_dropzonejs'			=>	'#pre_label<input type="text" id="#id" name="#name" value="#value" style="display:none;"#attributes /><div id="#id-dropzonejs" class="dropzone needsclick"><div class="dz-message">#placeholder</div><div id="#id-dropzonejs-previews"></div></div>#post_label#invalid_feedback#help',

							'mask_field_attributes'			=>	array('class', 'multiple_file', 'directory', 'disabled', 'accept', 'required', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'file_type', 'file_preview', 'hidden_bypass', 'file_capture'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class', 'file_button_label'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'file_type', 'required', 'hidden', 'multiple_file', 'directory', 'placeholder_dropzonejs', 'file_capture', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('File Handler', 'ws-form'),
											'meta_keys'	=>	array('file_handler')	// , 'file_name_mask'
										),

										array(
											'label'		=>	__('Preview', 'ws-form'),
											'meta_keys'	=> array('file_preview', 'file_preview_orientation', 'file_preview_width', 'file_preview_orientation_breakpoint_sizes')
										),

										array(
											'label'		=>	__('Image Optimization', 'ws-form'),
											'meta_keys'	=> array('file_image_max_width', 'file_image_max_height', 'file_image_crop', 'file_image_compression', 'file_image_mime')
										),

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'file_min', 'file_max', 'file_min_size', 'file_max_size', 'file_timeout', 'accept', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'hidden' => array (

							'label'						=>	__('Hidden', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'					=>	'/knowledgebase/hidden/',
							'label_default'				=>	__('Hidden', 'ws-form'),
							'mask_field'				=>	'<input type="hidden" id="#id" name="#name" value="#value" data-default-value="#value" data-id-hidden="#field_id"#attributes />',
							'mask_field_attributes'		=>	array('autocomplete'),
							'submit_save'				=>	true,
							'submit_edit'				=>	true,
							'submit_edit_type'			=>	'text',
							'calc_in'					=>	true,
							'calc_out'					=>	true,
							'text_in'					=>	true,
							'text_out'					=>	true,
							'value_out'					=>	true,
							'mappable'					=>	true,
							'progress'					=>	false,
							'template_svg_exclude'		=>	true,
							'conditional'				=>	array(

								'logics_enabled'		=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', '<', '>', '<=', '>=', 'blank', 'blank_not', 'regex', 'regex_not', 'field_match', 'field_match_not'),
								'actions_enabled'		=>	array('value', 'reset', 'clear'),
								'condition_event'		=>	'change'
							),
							'compatibility_id'	=>	'mdn-html_elements_input_input-hidden',
							'events'						=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'event_validate_bypass'		=>	true,	// This field can never be invalid
							'mask_wrappers_drop'		=>	true,

							'fieldsets'					=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('default_value', 'autocomplete'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email_on')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=> array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										)
									)
								)
							)
						),

						'signature' => array (

							'label'						=>	__('Signature', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'					=>	'/knowledgebase/signature/',
							'label_default'				=>	__('Signature', 'ws-form'),
							'mask_field'				=>	'#pre_label<input type="text" id="#id" name="#name" value="#value"#attributes style="display:none;" /><canvas tabindex="-1"></canvas>#post_label#invalid_feedback#help',
							'mask_field_attributes'		=>	array('class', 'signature_mime', 'signature_dot_size', 'signature_pen_color', 'signature_background_color', 'signature_height', 'signature_crop', 'required', 'disabled', 'custom_attributes', 'hidden_bypass'),
							'mask_field_label'			=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'=>	array('class'),
							'mask_help_append'			=>	'#help_append_separator<a href="#" data-action="wsf-signature-clear">#text_clear</a>',
							'mask_help_append_separator'=>	'<br />',
							'submit_save'				=>	true,
							'submit_edit'				=>	false,
							'calc_in'					=>	false,
							'calc_out'					=>	true,
							'text_in'					=>	false,
							'text_out'					=>	true,
							'value_out'					=>	false,
							'mappable'					=>	true,
							'label_inside'				=>	true,
							'progress'					=>	true,
							'invalid_feedback'			=>	__('This signature is required.', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'		=>	array('signature', 'signature_not', 'validate', 'validate_not'),
								'actions_enabled'		=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper', 'required_signature', 'disabled', 'reset_signature'),
								'condition_event'		=>	'change'
							),
							'events'					=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required_on', 'hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('File Handler', 'ws-form'),
											'meta_keys'	=>	array('file_handler')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'signature_mime', 'signature_pen_color', 'signature_background_color', 'signature_dot_size', 'signature_height', 'signature_crop',)
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'			=>	__('Labels', 'ws-form'),
											'meta_keys'	=>	array('text_clear')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'progress' => array (

							'label'				=>	__('Progress', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/progress/',
							'label_default'		=>	__('Progress', 'ws-form'),
							'submit_save'		=>	false,
							'submit_edit'		=>	false,
							'progress'			=>	false,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	false,
							'mappable'			=>	false,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change'),
								'actions_enabled'	=>	array('visibility', 'value', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'max', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'compatibility_id'				=>	'progress',
							'mask_field'					=>	'#pre_label<progress data-progress-bar data-progress-bar-value id="#id" name="#name" value="#value" role="progressbar"#attributes /></progress>#post_label#help',
							'mask_field_attributes'			=>	array('class', 'progress_source', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'max_progress'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'hidden', 'default_value_progress', 'progress_source', 'help_progress'),

									'fieldsets'	=>	array(

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('max_progress', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'meter' => array (

							'label'				=>	__('Meter', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/meter/',
							'label_default'		=>	__('Meter', 'ws-form'),
							'submit_save'		=>	false,
							'submit_edit'		=>	false,
							'progress'			=>	false,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	false,
							'mappable'			=>	false,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change'),
								'actions_enabled'	=>	array('visibility', 'value', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'min', 'max', 'low', 'high', 'optimum', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'compatibility_id'				=>	'meter',
							'mask_field'					=>	'#pre_label<meter id="#id" name="#name" value="#value"#attributes /></meter>#post_label#help',
							'mask_field_attributes'			=>	array('class', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'min_meter', 'max_meter', 'low', 'high', 'optimum'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'hidden', 'default_value_meter', 'help_meter'),

									'fieldsets'	=>	array(

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('min_meter', 'max_meter', 'low', 'high', 'optimum', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'password' => array (

							'label'				=>	__('Password', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/password/',
							'label_default'		=>	__('Password', 'ws-form'),
							'submit_save'		=>	false,
							'submit_edit'		=>	false,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	false,
							'text_out'			=>	false,
							'value_out'			=>	false,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('password_generate', 'visibility', 'required', 'focus', 'value', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'mdn-html_elements_input_input-password',
							'events'				=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Fields
							'mask_field'					=>	'#pre_label<input type="password" id="#id" name="#name" value="#value"#attributes />#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'autocomplete_password', 'required', 'readonly', 'min_length', 'max_length', 'placeholder', 'input_mask', 'pattern', 'aria_describedby', 'aria_labelledby', 'aria_label', 'password_strength_meter', 'password_visibility_toggle', 'password_generate', 'password_strength_invalid', 'custom_attributes'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'required_on', 'hidden', 'default_value', 'placeholder', 'help_count_char', 'autocomplete_password'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Features', 'ws-form'),
											'meta_keys'	=>	array('password_strength_invalid', 'password_strength_meter', 'password_visibility_toggle', 'password_generate')
										),

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'readonly', 'min_length', 'max_length', 'input_mask', 'pattern', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Labels', 'ws-form'),
											'meta_keys'	=>	array('text_password_strength_short', 'text_password_strength_bad', 'text_password_strength_good', 'text_password_strength_strong', 'text_password_visibility_toggle_off', 'text_password_visibility_toggle_on', 'text_password_generate', 'text_password_strength_invalid')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'googlemap' => array (

							'label'				=>	__('Google Map', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/google-map/',
							'label_default'		=>	__('Google Map', 'ws-form'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_array'		=>	false,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a location.', 'ws-form'),
							'keyword'			=>	__('location place address latitude longitude', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'change'),
								'actions_enabled'	=>	array('visibility', 'required', 'value', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'trigger'			=> 'change',

							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" value="#value" style="display:none;" data-google-map#attributes /><div id="#id-map"></div>#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'required', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'google_map_lat', 'google_map_lng', 'google_map_zoom', 'google_map_type', 'google_map_control_type', 'google_map_control_full_screen', 'google_map_control_street_view', 'google_map_control_zoom', 'google_map_height', 'google_map_search_field_id', 'google_map_marker_icon_title', 'google_map_marker_icon_url', 'hidden_bypass'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),
							'mask_help_append'				=>	'#help_append_separator<a href="#" data-action="wsf-google-map-clear">#text_reset</a>',
							'mask_help_append_separator'	=>	'<br />',

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Map', 'ws-form'),
											'meta_keys'	=>	array('google_map_not_enabled', 'google_map_lat', 'google_map_lng', 'google_map_zoom', 'google_map_type', 'google_map_search_field_id')
										),

										array(
											'label'		=>	__('Controls', 'ws-form'),
											'meta_keys'	=>	array('google_map_control_type', 'google_map_control_full_screen', 'google_map_control_street_view', 'google_map_control_zoom')
										),

										array(
											'label'		=>	__('Marker', 'ws-form'),
											'meta_keys'	=>	array('google_map_marker_icon_title', 'google_map_marker_icon_url')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'google_map_height', 'google_map_style')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'			=>	__('Labels', 'ws-form'),
											'meta_keys'	=>	array('text_reset')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'			=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'googleaddress' => array (

							'label'				=>	__('Google Address', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/google-address/',
							'icon'				=>	'googlemap',
							'label_default'		=>	__('Address Line 1', 'ws-form'),
							'label_inside'		=>	true,
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'submit_array'		=>	false,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please enter an address.', 'ws-form'),
							'keyword'			=>	__('location place address', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('equals', 'equals_not', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'change'),
								'actions_enabled'	=>	array('visibility', 'required', 'value', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'	=>	'change'
							),
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'trigger'			=> 'change',

							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" value="#value" data-google-address#attributes />#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'required', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'hidden_bypass', 'placeholder'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'placeholder_googleaddress', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Address Fields', 'ws-form'),
											'meta_keys'	=>	array('google_address_field_mapping')
										),

										array(
											'label'		=>	__('Google Map', 'ws-form'),
											'meta_keys'	=>	array('google_address_map', 'google_address_map_zoom')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'			=>	__('Labels', 'ws-form'),
											'meta_keys'	=>	array('text_reset')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('google_address_restriction_business', 'google_address_restriction_country', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'			=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'search' => array (

							'label'				=>	__('Search', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/search/',
							'label_default'		=>	__('Search', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	true,
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'value_out'			=>	false,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'progress'			=>	true,
							'conditional'		=>	array(

								'logics_enabled'		=>	array('equals', 'equals_not', 'contains', 'contains_not', 'starts', 'starts_not', 'ends', 'ends_not', 'blank', 'blank_not', 'cc==', 'cc!=', 'cc>', 'cc<', 'cw==', 'cw!=', 'cw>', 'cw<', 'regex', 'regex_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'		=>	array('visibility', 'required', 'focus', 'value', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'reset', 'clear'),
								'condition_event'		=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'keyup',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'	=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="search" id="#id" name="#name" value="#value"#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'readonly', 'required', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'placeholder', 'pattern', 'list', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'autocomplete_search', 'hidden_bypass', 'transform'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'default_value', 'placeholder', 'help_count_char_word', 'autocomplete_search'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'readonly', 'min_length', 'max_length', 'min_length_words', 'max_length_words', 'input_mask', 'pattern', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Transform', 'ws-form'),
											'meta_keys'	=>	array('transform')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe', 'dedupe_period', 'dedupe_message')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Autocomplete
								'datalist'	=> array(

									'label'		=>	__('Datalist', 'ws-form'),
									'meta_keys'	=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'legal' => array (

							'label'					=>	__('Legal', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'				=>	'/knowledgebase/legal/',
							'label_default'			=>	__('Legal', 'ws-form'),

							// Fields
							'mask_field'			=>	'#pre_label<div data-wsf-legal#attributes>#value</div><input data-wsf-legal-input type="text" id="#id" name="#name" style="display:none" />#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'legal_source', 'legal_termageddon_key', 'legal_termageddon_hide_title', 'legal_style_height', 'custom_attributes'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'calc_in'				=>	false,
							'calc_out'				=>	false,
							'text_in'				=>	false,
							'text_out'				=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'meta_wpautop'			=>	'text_editor',
							'keyword'				=>	__('terms conditions privacy policy', 'ws-form'),
							'conditional'			=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper')
							),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render_off', 'required', 'hidden', 'legal_source', 'legal_termageddon_intro', 'legal_termageddon_key', 'legal_termageddon_hide_title', 'legal_text_editor', 'help')
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'legal_style_height', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback_legal')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),										

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)

									)
								)
							)
						)
					)
				),

				'spam' => array(

					'label' => __('Spam Protection', 'ws-form'),
					'types' => array(

						'recaptcha' => array (

							'label'							=>	__('reCAPTCHA', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'						=>	'/knowledgebase/recaptcha/',
							'label_default'					=>	__('reCAPTCHA', 'ws-form'),
							'mask_field'					=>	'<div id="#id" name="#name" style="border: none; padding: 0" required data-recaptcha#attributes></div>#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'recaptcha_site_key', 'recaptcha_recaptcha_type', 'recaptcha_badge', 'recaptcha_type', 'recaptcha_theme', 'recaptcha_size', 'recaptcha_language', 'recaptcha_action'),
							'submit_save'					=>	false,
							'submit_edit'					=>	false,
							'calc_in'						=>	false,
							'calc_out'						=>	false,
							'text_in'						=>	false,
							'text_out'						=>	false,
							'value_out'						=>	false,
							'mappable'						=>	false,
							'progress'						=>	false,
							'invalid_feedback'				=>	__('Please complete the reCAPTCHA.', 'ws-form'),
							'keyword'						=>	__('google spam', 'ws-form'),
							'multiple'						=>	false,
							'conditional'					=>	array(

								'logics_enabled'	=>	array('recaptcha', 'recaptcha_not'),
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper'),
								'condition_event'	=> 'recaptcha'
							),
							'events'						=>	array(

								'event'				=>	'mousedown touchstart',
								'event_action'		=>	__('Field', 'ws-form')
							),

							'fieldsets'						=> array(

								// Tab: Basic
								'basic'		=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'recaptcha_recaptcha_type', 'recaptcha_site_key', 'recaptcha_secret_key', 'recaptcha_badge', 'recaptcha_type', 'recaptcha_theme', 'recaptcha_size', 'recaptcha_action', 'help'),
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),										

										array(
											'label'		=>	__('Localization', 'ws-form'),
											'meta_keys'	=>	array('recaptcha_language')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'hcaptcha' => array (

							'label'							=>	__('hCaptcha', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'						=>	'/knowledgebase/hcaptcha/',
							'label_default'					=>	__('hCaptcha', 'ws-form'),
							'mask_field'					=>	'<div id="#id" name="#name" style="border: none; padding: 0" required data-hcaptcha#attributes></div>#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'hcaptcha_site_key', 'hcaptcha_type', 'hcaptcha_theme', 'hcaptcha_size', 'hcaptcha_language'),
							'submit_save'					=>	false,
							'submit_edit'					=>	false,
							'calc_in'						=>	false,
							'calc_out'						=>	false,
							'text_in'						=>	false,
							'text_out'						=>	false,
							'value_out'						=>	false,
							'mappable'						=>	false,
							'progress'						=>	false,
							'invalid_feedback'				=>	__('Please complete the hCaptcha.', 'ws-form'),
							'keyword'						=>	__('spam', 'ws-form'),
							'multiple'						=>	false,
							'conditional'					=>	array(

								'logics_enabled'	=>	array('hcaptcha', 'hcaptcha_not'),
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper'),
								'condition_event'	=> 'hcaptcha'
							),
							'events'						=>	array(

								'event'				=>	'mousedown touchstart',
								'event_action'		=>	__('Field', 'ws-form')
							),

							'fieldsets'						=> array(

								// Tab: Basic
								'basic'		=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'hcaptcha_type', 'hcaptcha_site_key', 'hcaptcha_secret_key', 'hcaptcha_theme', 'hcaptcha_size', 'help'),
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),										

										array(
											'label'		=>	__('Localization', 'ws-form'),
											'meta_keys'	=>	array('hcaptcha_language')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'turnstile' => array (

							'label'							=>	__('Turnstile', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'						=>	'/knowledgebase/turnstile/',
							'label_default'					=>	__('Turnstile', 'ws-form'),
							'mask_field'					=>	'<div id="#id" name="#name" style="border: none; padding: 0" required data-turnstile#attributes></div>#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'turnstile_site_key', 'turnstile_theme'),
							'submit_save'					=>	false,
							'submit_edit'					=>	false,
							'calc_in'						=>	false,
							'calc_out'						=>	false,
							'text_in'						=>	false,
							'text_out'						=>	false,
							'value_out'						=>	false,
							'mappable'						=>	false,
							'progress'						=>	false,
							'invalid_feedback'				=>	__('Please complete the captcha.', 'ws-form'),
							'keyword'						=>	__('spam captcha', 'ws-form'),
							'multiple'						=>	false,
							'conditional'					=>	array(

								'logics_enabled'	=>	array('turnstile', 'turnstile_not'),
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper'),
								'condition_event'	=> 'turnstile'
							),
							'events'						=>	array(

								'event'				=>	'mousedown touchstart',
								'event_action'		=>	__('Field', 'ws-form')
							),

							'fieldsets'						=> array(

								// Tab: Basic
								'basic'		=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'turnstile_site_key', 'turnstile_secret_key', 'turnstile_theme', 'help'),
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'			=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						)
					)
				),

				'content' => array(

					'label'	=> __('Content', 'ws-form'),
					'types' => array(

						'texteditor' => array (

							'label'					=>	__('Text Editor', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'				=>	'/knowledgebase/texteditor/',
							'label_default'			=>	__('Text Editor', 'ws-form'),
							'label_position_force'	=>	'top',	// Prevent formatting issues with different label positioning. The label is the button.
							'mask_field'			=>	'<div data-text-editor data-static data-name="#name"#attributes>#value</div>',
							'mask_preview'			=>	'#text_editor',
							'meta_wpautop'			=>	'text_editor',
							'wpautop'				=>	true,
							'meta_do_shortcode'		=>	'text_editor',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'static'				=>	'text_editor',
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'keyword'				=>	__('visual tinymce', 'ws-form'),
							'progress'				=>	false,
							'conditional'			=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'text_editor', 'html', 'class_add_wrapper', 'class_remove_wrapper')
							),

							'fieldsets'				=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'text_editor'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email_on')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),										

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'html' => array (

							'label'					=>	__('HTML', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'				=>	'/knowledgebase/html/',
							'label_default'			=>	__('HTML', 'ws-form'),
							'label_position_force'	=>	'top',	// Prevent formatting issues with different label positioning.
							'mask_field'			=>	'<div data-html data-static data-name="#name"#attributes>#value</div>',
							'meta_do_shortcode'		=>	'html_editor',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'static'				=>	'html_editor',
							'calc_in'				=>	true,
							'calc_out'				=>	true,
							'text_in'				=>	true,
							'text_out'				=>	true,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'keyword'				=>	__('codemirror shortcode javascript js embed tag', 'ws-form'),
							'conditional'			=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'		=>	array('visibility', 'html', 'class_add_wrapper', 'class_remove_wrapper')
							),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'html_editor'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email_on')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)

									)
								)
							)
						),

						'divider' => array (

							'label'					=>	__('Divider', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'				=>	'/knowledgebase/divider/',
							'label_default'			=>	__('Divider', 'ws-form'),
							'mask_field'			=>	'<hr data-static data-name="#name"#attributes />',
							'mask_field_static'		=>	'<hr />',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'calc_in'				=>	false,
							'calc_out'				=>	false,
							'text_in'				=>	false,
							'text_out'				=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'static'				=>	true,
							'keyword'				=>	__('hr', 'ws-form'),
							'progress'				=>	false,
							'conditional'			=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper')
							),
							'label_disabled'			=>	true,

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email_on')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'spacer' => array (

							'label'				=>	__('Spacer', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'			=>	'/knowledgebase/spacer/',
							'label_default'		=>	__('Spacer', 'ws-form'),
							'mask_field'		=>	'<div#attributes></div>',
							'mask_field_attributes' => array('spacer_style_height'),
							'submit_save'		=>	false,
							'submit_edit'		=>	false,
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	false,
							'text_out'			=>	false,
							'value_out'			=>	false,
							'mappable'			=>	false,
							'progress'			=>	false,
							'conditional'		=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper')
							),
							'label_disabled'	=>	true,

							'fieldsets'			=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden')
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('spacer_style_height')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'message' => array (

							'label'					=>	__('Message', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'				=>	'/knowledgebase/message/',
							'icon'					=>	'info-circle',
							'label_default'			=>	__('Message', 'ws-form'),
							'label_position_force'	=>	'top',	// Prevent formatting issues with different label positioning. The label is the button.
							'mask_field'			=>	'<div data-text-editor data-static data-name="#name"#attributes>#value</div>',
							'mask_field_attributes'	=>	array('class', 'custom_attributes'),
							'mask_preview'			=>	'#text_editor',
							'meta_wpautop'			=>	'text_editor',
							'wpautop'				=>	true,
							'meta_do_shortcode'		=>	'text_editor',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'static'				=>	'text_editor',
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'keyword'				=>	__('alert success information warning danger', 'ws-form'),
							'conditional'			=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'text_editor', 'html', 'class_add_wrapper', 'class_remove_wrapper')
							),
							'fieldsets'				=>	array(

								// Tab: Basic
								'basic'	=>	array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'class_field_message_type', 'text_editor'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email_on')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'note' => array (

							'label'					=>	__('Note', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'				=>	'/knowledgebase/note/',
							'label_default'			=>	__('Note', 'ws-form'),
							'admin_hide_id'			=>	true,
							'mask_field'			=>	'',
							'mask_field_attributes'	=>	array(),
							'mask_preview'			=>	'#text_editor',
							'meta_wpautop'			=>	'text_editor',
							'wpautop'				=>	true,
							'meta_do_shortcode'		=>	'text_editor',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'static'				=>	'text_editor',
							'calc_in'				=>	false,
							'calc_out'				=>	false,
							'text_in'				=>	false,
							'text_out'				=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'mask_wrappers_drop'	=>	true,
							'layout_editor_only'	=>	true,
							'template_svg_exclude'	=>	true,
							'keyword'				=>	__('comment help', 'ws-form'),
							'conditional'			=>	array(

								'exclude_condition'		=>	true,
								'exclude_then'			=>	true,
								'exclude_else'			=>	true
							),
							'fieldsets'				=>	array(

								// Tab: Note
								'note'	=>	array(

									'label'		=>	__('Note', 'ws-form'),
									'meta_keys'	=>	array('text_editor_note')
								)
							)
						)
					)
				),

				'buttons' => array(

					'label'	=> __('Buttons', 'ws-form'),
					'types' => array(

						'submit' => array (

							'label'							=>	__('Submit', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'						=>	'/knowledgebase/submit/',
							'label_default'					=>	__('Submit', 'ws-form'),
							'label_position_force'			=>	'top',
							'mask_field'					=>	'<button type="submit" id="#id" name="#name"#attributes>#label</button>#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'				=>	'#label',
							'submit_save'					=>	false,
							'submit_edit'					=>	false,
							'calc_in'						=>	true,
							'calc_out'						=>	false,
							'text_in'						=>	true,
							'text_out'						=>	false,
							'value_out'						=>	false,
							'mappable'						=>	false,
							'progress'						=>	false,
							'conditional'					=>	array(

								'logics_enabled'		=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'		=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'		=>	'click',
							),
							'events'	=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type_primary', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'save' => array (

							'label'					=>	__('Save', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'				=>	'/knowledgebase/save/',
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	false,
							'label_default'			=>	__('Save', 'ws-form'),
							'label_position_force'	=>	'top',
							'mask_field'			=>	'<button type="button" id="#id" name="#name" data-action="wsf-save"#attributes>#label</button>#help',
							'mask_field_attributes'	=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'		=>	'#label',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'keyword'				=>	__('continue', 'ws-form'),
							'conditional'			=>	array(

								'logics_enabled'	=>	array('click', 'hidden', 'mouseover', 'mouseout', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'	=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'validate_form', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type_success', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'reset' => array (

							'label'							=>	__('Reset', 'ws-form'),
							'pro_required'					=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'						=>	'/knowledgebase/reset/',
							'calc_in'						=>	true,
							'calc_out'						=>	false,
							'text_in'						=>	true,
							'text_out'						=>	false,
							'label_default'					=>	__('Reset', 'ws-form'),
							'label_position_force'			=>	'top',
							'mask_field'					=>	'<button type="reset" id="#id" name="#name" data-action="wsf-reset"#attributes>#label</button>#help',
							'mask_field_attributes'			=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'				=>	'#label',
							'submit_save'					=>	false,
							'submit_edit'					=>	false,
							'value_out'						=>	false,
							'mappable'						=>	false,
							'progress'						=>	false,
							'conditional'					=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'	=>	array(

								'event'						=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'				=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'clear' => array (

							'label'					=>	__('Clear', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'				=>	'/knowledgebase/clear/',
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	false,
							'label_default'			=>	__('Clear', 'ws-form'),
							'label_position_force'	=>	'top',
							'mask_field'			=>	'<button type="button" id="#id" name="#name" data-action="wsf-clear"#attributes>#label</button>#help',
							'mask_field_attributes'	=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'		=>	'#label',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'progress'				=>	false,
							'conditional'			=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'	=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'tab_previous' => array (

							'label'						=>	__('Previous Tab', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'					=>	'/knowledgebase/tab_previous/',
							'icon'						=>	'previous',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Previous', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name" data-action="wsf-tab_previous"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'keyword'					=>	__('back', 'ws-form'),
							'progress'					=>	false,
							'conditional'				=>	array(

								'logics_enabled'			=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'			=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'			=>	'click',
							),
							'events'	=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid
							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'			=>	__('Scroll', 'ws-form'),
											'meta_keys'	=>	array('scroll_to_top', 'scroll_to_top_offset', 'scroll_to_top_duration')
										),

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'tab_next' => array (

							'label'					=>	__('Next Tab', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('basic'),
							'kb_url'				=>	'/knowledgebase/tab_next/',
							'icon'					=>	'next',
							'calc_in'				=>	true,
							'calc_out'				=>	false,
							'text_in'				=>	true,
							'text_out'				=>	false,
							'label_default'			=>	__('Next', 'ws-form'),
							'label_position_force'	=>	'top',
							'mask_field'			=>	'<button type="button" id="#id" name="#name" data-action="wsf-tab_next"#attributes>#label</button>#help',
							'mask_field_attributes'	=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'		=>	'#label',
							'submit_save'			=>	false,
							'submit_edit'			=>	false,
							'value_out'				=>	false,
							'mappable'				=>	false,
							'keyword'				=>	__('continue forward', 'ws-form'),
							'progress'				=>	false,
							'conditional'			=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'	=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid
							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'			=>	__('Scroll', 'ws-form'),
											'meta_keys'	=>	array('scroll_to_top', 'scroll_to_top_offset', 'scroll_to_top_duration')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'				=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'button' => array (

							'label'						=>	__('Custom', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'					=>	'/knowledgebase/button/',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Button', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'progress'					=>	false,
							'conditional'				=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'					=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'				=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						)
					)
				),

				'section' => array(

					'label'	=> __('Repeatable Sections', 'ws-form'),
					'types' => array(

						'section_add' => array (

							'label'						=>	__('Add', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'icon'						=>	'plus',
							'kb_url'					=>	'/knowledgebase/section_add/',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Add', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name" data-action="wsf-section-add-button"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'section_repeatable_section_id'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'progress'					=>	false,
							'keyword'					=>	__('button', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'					=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'				=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'section_repeatable_section_id', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'section_delete' => array (

							'label'						=>	__('Remove', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'icon'						=>	'minus',
							'kb_url'					=>	'/knowledgebase/section_delete/',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Remove', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name" data-action="wsf-section-delete-button"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'section_repeatable_section_id'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'progress'					=>	false,
							'keyword'					=>	__('button', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'					=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'				=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'section_repeatable_section_id', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type_danger', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'section_up' => array (

							'label'						=>	__('Move Up', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'icon'						=>	'up',
							'kb_url'					=>	'/knowledgebase/section_move_up/',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Move Up', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name" data-action="wsf-section-move-up-button"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'progress'					=>	false,
							'keyword'					=>	__('button', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'					=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'				=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),


						'section_down' => array (

							'label'						=>	__('Move Down', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'icon'						=>	'down',
							'kb_url'					=>	'/knowledgebase/section_move_down/',
							'calc_in'					=>	true,
							'calc_out'					=>	false,
							'text_in'					=>	true,
							'text_out'					=>	false,
							'label_default'				=>	__('Move Down', 'ws-form'),
							'label_position_force'		=>	'top',
							'mask_field'				=>	'<button type="button" id="#id" name="#name" data-action="wsf-section-move-down-button"#attributes>#label</button>#help',
							'mask_field_attributes'		=>	array('class', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes'),
							'mask_field_label'			=>	'#label',
							'submit_save'				=>	false,
							'submit_edit'				=>	false,
							'value_out'					=>	false,
							'mappable'					=>	false,
							'progress'					=>	false,
							'keyword'					=>	__('button', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'	=>	array('click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur'),
								'actions_enabled'	=>	array('visibility', 'focus', 'blur', 'button_html', 'click', 'disabled', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'click',
							),
							'events'					=>	array(

								'event'				=>	'click',
								'event_action'		=>	__('Button', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							'fieldsets'				=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'help'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'class_field_button_type', 'class_field_full_button_remove')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('disabled', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),

						'section_icons' => array (

							'label'				=>	__('Icons', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'kb_url'			=>	'/knowledgebase/section_icons/',
							'icon'				=>	'section-icons',
							'calc_in'			=>	false,
							'calc_out'			=>	false,
							'text_in'			=>	false,
							'text_out'			=>	false,
							'label_default'		=>	__('Icons', 'ws-form'),
							'submit_save'		=>	false,
							'submit_edit'		=>	false,
							'value_out'			=>	false,
							'mappable'			=>	false,
							'progress'			=>	false,
							'keyword'			=>	__('add remove move up down drag reset clear', 'ws-form'),
							'conditional'		=>	array(

								'exclude_condition'	=>	true,
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper')
							),

							'mask_field'					=>	'<div data-section-icons#attributes></div>',
							'mask_field_attributes'			=>	array('class', 'section_repeatable_section_id'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('hidden', 'section_repeatable_section_id'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Icons', 'ws-form'),
											'meta_keys'	=>	array('section_icons')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('class_single_vertical_align_bottom', 'horizontal_align', 'section_icons_style', 'section_icons_size', 'section_icons_color_on', 'section_icons_color_off', 'section_icons_html_add', 'section_icons_html_delete', 'section_icons_html_move_up', 'section_icons_html_move_down', 'section_icons_html_drag', 'section_icons_html_reset', 'section_icons_html_clear')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=>	array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						),
					)
				),

				'ecommerce' => array(

					'label'	=> __('E-Commerce', 'ws-form'),
					'types' => array(

						'price' => array (

							'label'				=>	__('Price', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'text',
							'kb_url'			=>	'/knowledgebase/price/',
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'label_default'		=>	__('Price', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'ecommerce_price'	=>	true,
							'progress'			=>	true,
							'keyword'			=>	__('money currency ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'focus', 'blur', 'value_number', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'ecommerce_price_min', 'ecommerce_price_max', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" value="#value" data-ecommerce-price#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'disabled', 'readonly', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'ecommerce_price_negative', 'ecommerce_price_min', 'ecommerce_price_max', 'text_align_right', 'custom_attributes', 'ecommerce_calculation_persist', 'hidden_bypass', 'autocomplete_price', 'exclude_cart_total'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'hidden', 'text_align_right', 'default_value', 'ecommerce_price_negative', 'placeholder', 'help', 'autocomplete_price'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email', 'exclude_cart_total')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'ecommerce_price_min', 'ecommerce_price_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'		=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'price_select' => array (

							'label'				=>	__('Price Select', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'select',
							'kb_url'			=>	'/knowledgebase/price_select/',
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'label_default'		=>	__('Price Select', 'ws-form'),
							'label_inside'		=>	true,
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_select_price'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'submit_array'		=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'ecommerce_price'	=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please select a valid #label_lowercase.', 'ws-form'),
							'keyword'			=>	__('money currency ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'data_grid_fields'			=>	'data_grid_select_price',
								'option_text'				=>	'select_price_field_label',
								'logics_enabled'			=>	array('selected', 'selected_not', 'selected_any', 'selected_any_not', 'rs==', 'rs!=', 'rs>', 'rs<', 'selected_value_equals', 'selected_value_equals_not', 'focus', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'			=>	array('visibility', 'required', 'focus', 'blur', 'value_row_select', 'value_row_deselect', 'value_row_select_value', 'value_row_deselect_value', 'value_row_select_all', 'value_row_deselect_all', 'value_row_disabled', 'value_row_not_disabled', 'value_row_class_add', 'value_row_class_remove', 'value', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'select_min', 'select_max', 'reset', 'value_row_reset'),
								'condition_event'			=>	'change'
							),

							'events'	=>	array(

								'event'						=>	'change',
								'event_action'				=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'					=>	'<optgroup label="#group_label"#disabled>#group</optgroup>',
							'mask_group_label'				=>	'#group_label',

							// Rows
							'mask_row'						=>	'<option id="#row_id" data-id="#data_id" data-price="#row_price" value="#row_value"#attributes>#select_price_field_label</option>',
							'mask_row_value'				=>	'#select_price_field_value_html',
							'mask_row_price'				=>	'#select_price_field_price_html',
							'mask_row_placeholder'			=>	'<option data-id="0" value="" data-placeholder>#value</option>',
							'mask_row_attributes'			=>	array('default', 'disabled'),
							'mask_row_lookups'				=>	array('select_price_field_value', 'select_price_field_label', 'select_price_field_price', 'select_price_field_parse_variable', 'price_select_cascade_field_filter'),
							'datagrid_column_value'			=>	'select_price_field_value',
							'mask_row_default' 				=>	' selected',

							// Fields
							'mask_field'					=>	'#pre_label<select id="#id" name="#name" data-ecommerce-price#attributes>#datalist</select>#post_label#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'size', 'multiple', 'required', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'dedupe_value_scope', 'price_select_cascade_ajax', 'ecommerce_calculation_persist', 'hidden_bypass', 'select2', 'autocomplete_price', 'exclude_cart_total'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=> array('label_render', 'required', 'hidden', 'multiple', 'size', 'placeholder_row', 'help', 'autocomplete_price'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Select2', 'ws-form'),
											'meta_keys'	=>	array('select2_intro', 'select2', 'select2_ajax', 'select2_no_match', 'select2_tags')
										),

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email', 'exclude_cart_total')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'select_min', 'select_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Options
								'options'	=> array(

									'label'			=>	__('Options', 'ws-form'),
									'meta_keys'		=> array('data_grid_select_price', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('select_price_field_label', 'select_price_field_value', 'select_price_field_price', 'select_price_field_parse_variable')
										),

										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('price_select_cascade', 'price_select_cascade_field_filter', 'price_select_cascade_field_filter_comma', 'price_select_cascade_field_id', 'price_select_cascade_option_text_no_rows', 'price_select_cascade_no_match', 'price_select_cascade_ajax', 'price_select_cascade_ajax_option_text_loading')
										)
									)
								)
							)
						),

						'price_checkbox' => array (

							'label'				=>	__('Price Checkbox', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'checkbox',
							'kb_url'			=>	'/knowledgebase/price_checkbox/',
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'label_default'		=>	__('Price Checkbox', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_checkbox_price'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'submit_array'		=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'ecommerce_price'	=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('This checkbox is required.', 'ws-form'),
							'keyword'			=>	__('buttons toggle switches colors images products money currency ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'data_grid_fields'		=>	'data_grid_checkbox_price',
								'option_text'			=>	'checkbox_price_field_label',
								'logics_enabled'		=>	array('checked', 'checked_not', 'checked_any', 'checked_any_not', 'rc==', 'rc!=', 'rc>', 'rc<', 'checked_value_equals', 'checked_value_equals', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'		=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper', 'value_row_check', 'value_row_uncheck', 'value_row_check_value','value_row_uncheck_value', 'value_row_check_all', 'value_row_uncheck_all', 'value_row_focus', 'value_row_required', 'value_row_not_required', 'value_row_disabled', 'value_row_not_disabled', 'value_row_visible', 'value_row_not_visible', 'value_row_class_add', 'value_row_class_remove', 'value_row_set_custom_validity', 'checkbox_min', 'checkbox_max', 'reset', 'clear'),
								'condition_event'		=>	'change',
								'condition_event_row'	=>	true
							),

							'events'		=>	array(

								'event'					=>	'change',
								'event_action'			=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group_wrapper'		=>	'<div#attributes>#group</div>',
							'mask_group_label'			=>	'<legend>#group_label</legend>',

							// Rows
							'mask_row'					=>	'<div#attributes>#row_label</div>',
							'mask_row_attributes'		=>	array('class'),
							'mask_row_label'			=>	'<label id="#label_row_id" for="#row_id"#attributes>#row_field#checkbox_price_field_label#required</label>#invalid_feedback',
							'mask_row_label_attributes'	=>	array('class'),
							'mask_row_field'			=>	'<input type="checkbox" id="#row_id" name="#name" data-price="#row_price" value="#row_value" data-ecommerce-price#attributes />',
							'mask_row_value'			=>	'#checkbox_price_field_value_html',
							'mask_row_price'			=>	'#checkbox_price_field_price_html',
							'mask_row_field_attributes'	=>	array('class', 'default', 'disabled', 'required', 'aria_labelledby', 'dedupe_value_scope', 'ecommerce_calculation_persist', 'hidden_bypass', 'exclude_cart_total'),
							'mask_row_lookups'			=>	array('checkbox_price_field_value', 'checkbox_price_field_label', 'checkbox_price_field_price', 'checkbox_price_field_parse_variable', 'price_checkbox_cascade_field_filter'),
							'datagrid_column_value'		=>	'checkbox_price_field_value',
							'mask_row_default' 			=>	' checked',

							// Fields
							'mask_field'				=>	'#pre_label#datalist#post_label#invalid_feedback#help',
							'mask_field_label'				=>	'<label id="#label_id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),
//							'mask_field_label_hide_group'	=>	true,

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render_off', 'hidden', 'select_all', 'select_all_label', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Layout', 'ws-form'),
											'meta_keys'	=>	array('orientation',
												'orientation_breakpoint_sizes'
											)
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email', 'exclude_cart_total')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('checkbox_min', 'checkbox_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Checkboxes
								'checkboxes' 	=> array(

									'label'			=>	__('Checkboxes', 'ws-form'),
									'meta_keys'		=> array('data_grid_checkbox_price', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('checkbox_price_field_label', 'checkbox_price_field_value', 'checkbox_price_field_price', 'checkbox_price_field_parse_variable')
										),

										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('price_checkbox_cascade', 'price_checkbox_cascade_field_filter', 'price_checkbox_cascade_field_filter_comma', 'price_checkbox_cascade_field_id', 'price_checkbox_cascade_no_match')
										)
									)
								)
							)
						),

						'price_radio' => array (

							'label'				=>	__('Price Radio', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'radio',
							'kb_url'			=>	'/knowledgebase/price_radio/',
							'calc_in'			=>	false,
							'calc_out'			=>	true,
							'text_in'			=>	false,
							'text_out'			=>	true,
							'label_default'		=>	__('Price Radio', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_radio_price'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'submit_array'		=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'ecommerce_price'	=>	true,
							'progress'			=>	true,
							'invalid_feedback'	=>	__('Please choose a valid #label_lowercase.', 'ws-form'),
							'keyword'			=>	__('buttons toggle switches colors images products money currency ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'data_grid_fields'			=>	'data_grid_radio_price',
								'option_text'				=>	'radio_price_field_label',
								'logics_enabled'			=>	array('checked', 'checked_not', 'checked_any', 'checked_any_not', 'checked_value_equals', 'checked_value_equals', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'			=>	array('visibility', 'required', 'class_add_wrapper', 'class_remove_wrapper', 'value_row_check', 'value_row_uncheck', 'value_row_check_value','value_row_uncheck_value', 'value_row_focus', 'value_row_disabled', 'value_row_not_disabled', 'value_row_visible', 'value_row_not_visible', 'value_row_class_add', 'value_row_class_remove', 'set_custom_validity', 'reset', 'clear'),
								'condition_event'			=>	'change',
								'condition_event_row'		=>	true
							),

							'events'	=>	array(

								'event'						=>	'change',
								'event_action'				=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'					=>	'<fieldset#disabled>#group_label#group</fieldset>',
							'mask_group_wrapper'			=>	'<div#attributes>#group</div>',
							'mask_group_label'				=>	'<legend>#group_label</legend>',

							// Rows
							'mask_row'						=>	'<div#attributes>#row_label</div>',
							'mask_row_attributes'			=>	array('class'),
							'mask_row_label'				=>	'<label id="#label_row_id" for="#row_id" data-label-required-id="#label_id"#attributes>#row_field#radio_price_field_label</label>#invalid_feedback',
							'mask_row_label_attributes'		=>	array('class'),
							'mask_row_field'				=>	'<input type="radio" id="#row_id" name="#name" data-price="#row_price" value="#row_value" data-ecommerce-price#attributes />',
							'mask_row_value'				=>	'#radio_price_field_value_html',
							'mask_row_price'				=>	'#radio_price_field_price_html',
							'mask_row_field_attributes'		=>	array('class', 'default', 'disabled', 'required_row', 'aria_labelledby', 'dedupe_value_scope', 'ecommerce_calculation_persist', 'hidden_bypass', 'exclude_cart_total'),
							'mask_row_lookups'				=>	array('radio_price_field_value', 'radio_price_field_label', 'radio_price_field_price', 'radio_price_field_parse_variable', 'radio_price_cascade_field_filter', 'price_radio_cascade_field_filter'),
							'datagrid_column_value'			=>	'radio_price_field_value',
							'mask_row_default' 				=>	' checked',

							// Fields
							'mask_field'					=>	'#pre_label#datalist#post_label#help',
							'mask_field_attributes'			=>	array('required_attribute_no'),
							'mask_field_label'				=>	'<label id="#label_id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),
//							'mask_field_label_hide_group'	=>	true,

							'invalid_feedback_last_row'		=> true,

							'fieldsets'						=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required_attribute_no', 'hidden', 'help'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Layout', 'ws-form'),
											'meta_keys'	=>	array('orientation',
												'orientation_breakpoint_sizes'
											)
										),

										array(
											'label'			=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email', 'exclude_cart_total')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'	=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'			=>	__('Classes', 'ws-form'),
											'meta_keys'		=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Duplication', 'ws-form'),
											'meta_keys'	=>	array('dedupe_value_scope')
										),

										array(
											'label'			=>	__('Validation', 'ws-form'),
											'meta_keys'		=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'			=>	__('Breakpoints', 'ws-form'),
											'meta_keys'		=> array('breakpoint_sizes'),
											'class'			=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Radios
								'radios'	=> array(

									'label'			=>	__('Radios', 'ws-form'),
									'meta_keys'		=> array('data_grid_radio_price', 'data_grid_rows_randomize'),
									'fieldsets' => array(

										array(
											'label'		=>	__('Column Mapping', 'ws-form'),
											'meta_keys'	=> array('radio_price_field_label', 'radio_price_field_value', 'radio_price_field_price', 'radio_price_field_parse_variable')
										),

										array(
											'label'		=>	__('Cascading', 'ws-form'),
											'meta_keys'	=> array('price_radio_cascade', 'price_radio_cascade_field_filter', 'price_radio_cascade_field_filter_comma', 'price_radio_cascade_field_id', 'price_radio_cascade_no_match')
										)
									)
								)
							)
						),

						'price_range' => array (

							'label'				=>	__('Price Range', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'range',
							'kb_url'			=>	'/knowledgebase/price_range/',
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'label_default'		=>	__('Price Range', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'ecommerce_price'	=>	true,
							'progress'			=>	true,
							'keyword'			=>	__('slider money currency ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input'),
								'actions_enabled'	=>	array('visibility', 'focus', 'blur', 'value_range', 'disabled', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'min', 'max', 'step', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-range',
							'events'						=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid
							'trigger'				=> 'input',

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'			=>	'<option value="#datalist_field_value" style="--wsf-position-tick-mark: #datalist_field_value_percentage%;" data-label="#datalist_field_text"></option>',
							'mask_row_lookups'	=>	array('datalist_field_value', 'datalist_field_text'),

							// Fields
							'mask_field'					=>	'#pre_label<input type="range" id="#id" name="#name" value="#value" data-ecommerce-price#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_submit'				=>	'<input type="text" id="#id" name="#name" value="#value" data-ecommerce-price#attributes />#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'min_range', 'max_range', 'step', 'disabled', 'aria_describedby', 'aria_labelledby', 'aria_label', 'custom_attributes', 'class_fill_lower_track', 'ecommerce_calculation_persist', 'hidden_bypass', 'autocomplete_range', 'exclude_cart_total'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'hidden', 'default_value_price_range', 'help_price_range', 'autocomplete_range'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email', 'exclude_cart_total')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'		=>	__('Advanced', 'ws-form'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'class_fill_lower_track')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'min_range', 'max_range', 'step', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Tab: Tick Marks
								'tickmarks'	=> array(

									'label'		=>	__('Tick Marks', 'ws-form'),
									'meta_keys'	=>	array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'quantity' => array (

							'label'				=>	__('Quantity', 'ws-form'),
							'pro_required'		=>	!WS_Form_Common::is_edition('pro'),
							'icon'				=>	'quantity',
							'kb_url'			=>	'/knowledgebase/quantity/',
							'calc_in'			=>	true,
							'calc_out'			=>	true,
							'text_in'			=>	true,
							'text_out'			=>	true,
							'label_default'		=>	__('Quantity', 'ws-form'),
							'data_source'		=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'		=>	true,
							'submit_edit'		=>	false,
							'submit_edit_ecommerce'	=>	true,
							'value_out'			=>	true,
							'mappable'			=>	true,
							'label_inside'		=>	true,
							'ecommerce_quantity'=>	true,
							'progress'			=>	true,
							'keyword'			=>	__('number digit ecommerce', 'ws-form'),
							'conditional'		=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'blank', 'blank_not', 'field_match', 'field_match_not', 'validate', 'validate_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change', 'input', 'change_input', 'keyup', 'keydown'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'blur', 'value_number', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field', 'min_int', 'max_int', 'step_int', 'reset', 'clear'),
								'condition_event'	=>	'change input'
							),
							'compatibility_id'	=>	'input-number',
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'					=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'				=> true,

							// Rows
							'mask_row'						=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'				=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'			=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="number" id="#id" name="#name" value="#value" data-ecommerce-quantity#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'disabled', 'readonly', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'ecommerce_quantity_min', 'max', 'step', 'ecommerce_field_id', 'text_align_center', 'custom_attributes', 'hidden_bypass', 'autocomplete_quantity', 'number_no_spinner'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'text_align_center', 'ecommerce_field_id', 'ecommerce_quantity_default_value', 'placeholder', 'help', 'autocomplete_quantity'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align', 'number_no_spinner')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly', 'ecommerce_quantity_min', 'max', 'step', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'		=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'price_subtotal' => array (

							'label'						=>	__('Price Subtotal', 'ws-form'),
							'pro_required'				=>	!WS_Form_Common::is_edition('pro'),
							'icon'						=>	'calculator',
							'kb_url'					=>	'/knowledgebase/price_subtotal/',
							'calc_in'					=>	false,
							'calc_out'					=>	true,
							'text_in'					=>	false,
							'text_out'					=>	true,
							'label_default'				=>	__('Price Subtotal', 'ws-form'),
							'submit_save'				=>	true,
							'submit_edit'				=>	false,
							'submit_edit_ecommerce'		=>	true,
							'value_out'					=>	true,
							'mappable'					=>	true,
							'label_inside'				=>	true,
							'ecommerce_price_subtotal'	=>	true,
							'progress'					=>	false,
							'keyword'					=>	__('money currency ecommerce', 'ws-form'),
							'conditional'				=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change'),
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							// Fields
							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" data-ecommerce-price-subtotal readonly placeholder#attributes />#post_label',
							'mask_field_submit'				=>	'<input type="text" id="#id" name="#name" value="#value"data-ecommerce-price-subtotal #attributes />',
							'mask_field_attributes'			=>	array('class', 'aria_describedby', 'aria_labelledby', 'aria_label', 'ecommerce_field_id', 'text_align_right', 'custom_attributes', 'ecommerce_price_negative', 'hidden_bypass'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'hidden', 'text_align_right', 'ecommerce_field_id', 'ecommerce_price_negative'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)

									)
								)
							)
						),

						'cart_price' => array (

							'label'					=>	__('Cart Detail', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'icon'					=>	'price',
							'kb_url'				=>	'/knowledgebase/cart_price/',
							'calc_in'				=>	true,
							'calc_out'				=>	true,
							'text_in'				=>	true,
							'text_out'				=>	true,
							'label_default'			=>	__('Cart Detail', 'ws-form'),
							'data_source'			=>	array('type' => 'data_grid', 'id' => 'data_grid_datalist'),
							'submit_save'			=>	true,
							'submit_edit'			=>	false,
							'submit_edit_ecommerce'	=>	true,
							'value_out'				=>	true,
							'mappable'				=>	true,
							'label_inside'			=>	true,
							'ecommerce_cart_price'	=>	true,
							'progress'				=>	true,
							'keyword'				=>	__('discount gift wrap handeling fee insurance shipping discount subtotal tax money currency ecommerce', 'ws-form'),


							'conditional'			=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'blank', 'blank_not', 'field_match', 'field_match_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change'),
								'actions_enabled'	=>	array('visibility', 'required', 'focus', 'blur', 'value_number', 'disabled', 'readonly', 'set_custom_validity', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change input',
								'event_action'		=>	__('Field', 'ws-form')
							),

							// Groups
							'mask_group'		=>	"\n\n<datalist id=\"#group_id\">#group</datalist>",
							'mask_group_always'	=> true,

							// Rows
							'mask_row'				=>	'<option value="#datalist_field_value">#datalist_field_text</option>',
							'mask_row_lookups'		=>	array('datalist_field_value', 'datalist_field_text'),
							'datagrid_column_value'	=>	'datalist_field_value',

							// Fields
							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" value="#value" data-ecommerce-cart-price#attributes />#post_label#datalist#invalid_feedback#help',
							'mask_field_attributes'			=>	array('class', 'list', 'disabled', 'readonly_on', 'required', 'placeholder', 'aria_describedby', 'aria_labelledby', 'aria_label', 'ecommerce_price_negative', 'ecommerce_price_min', 'ecommerce_price_max', 'ecommerce_cart_price_type', 'text_align_right', 'custom_attributes', 'ecommerce_calculation_persist', 'hidden_bypass', 'autocomplete_price'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'		=>	__('Basic', 'ws-form'),
									'meta_keys'	=>	array('label_render', 'required', 'hidden', 'text_align_right', 'ecommerce_cart_price_type', 'default_value', 'ecommerce_price_negative', 'placeholder', 'help', 'autocomplete_price'),

									'fieldsets'	=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass', 'ecommerce_calculation_persist')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=> array('disabled', 'readonly_on', 'ecommerce_price_min', 'ecommerce_price_max', 'field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Validation', 'ws-form'),
											'meta_keys'	=>	array('invalid_feedback_render', 'validate_inline', 'invalid_feedback')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								),

								// Datalist
								'datalist'	=> array(

									'label'			=>	__('Datalist', 'ws-form'),
									'meta_keys'		=> array('data_grid_datalist'),
									'fieldsets' => array(

										array(
											'label' => __('Column Mapping', 'ws-form'),
											'meta_keys' => array('datalist_field_text', 'datalist_field_value')
										)
									)
								)
							)
						),

						'cart_total' => array (

							'label'					=>	__('Cart Total', 'ws-form'),
							'pro_required'			=>	!WS_Form_Common::is_edition('pro'),
							'icon'					=>	'calculator',
							'kb_url'				=>	'/knowledgebase/cart_total/',
							'calc_in'				=>	false,
							'calc_out'				=>	true,
							'text_in'				=>	false,
							'text_out'				=>	true,
							'label_default'			=>	__('Cart Total', 'ws-form'),
							'submit_save'			=>	true,
							'submit_edit'			=>	false,
							'submit_edit_ecommerce'	=>	true,
							'value_out'				=>	true,
							'mappable'				=>	true,
							'label_inside'			=>	true,
							'ecommerce_cart_total'	=>	true,
							'progress'				=>	false,
							'keyword'				=>	__('money currency ecommerce', 'ws-form'),
							'conditional'			=>	array(

								'logics_enabled'	=>	array('==', '!=', '<', '>', '<=', '>=', 'field_match', 'field_match_not', 'click', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'touchstart', 'touchend', 'touchmove', 'touchcancel', 'focus', 'blur', 'change'),
								'actions_enabled'	=>	array('visibility', 'class_add_wrapper', 'class_remove_wrapper', 'class_add_field', 'class_remove_field'),
								'condition_event'	=>	'change input'
							),
							'events'			=>	array(

								'event'				=>	'change',
								'event_action'		=>	__('Field', 'ws-form')
							),
							'event_validate_bypass'	=> true,	// This field can never be invalid

							// Fields (Blank placeholder is required for inside label positioning)
							'mask_field'					=>	'#pre_label<input type="text" id="#id" name="#name" data-ecommerce-cart-total readonly placeholder#attributes />#post_label',
							'mask_field_submit'				=>	'<input type="text" id="#id" name="#name" value="#value" data-ecommerce-cart-total#attributes />',
							'mask_field_attributes'			=>	array('class', 'aria_describedby', 'aria_labelledby', 'aria_label', 'text_align_right', 'custom_attributes', 'ecommerce_price_negative', 'hidden_bypass'),
							'mask_field_label'				=>	'<label id="#label_id" for="#id"#attributes>#label</label>',
							'mask_field_label_attributes'	=>	array('class'),

							'fieldsets'	=> array(

								// Tab: Basic
								'basic'	=> array(

									'label'			=>	__('Basic', 'ws-form'),
									'meta_keys'		=>	array('label_render', 'hidden', 'text_align_right', 'ecommerce_price_negative'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Prefix / Suffix', 'ws-form'),
											'meta_keys'	=>	array('prepend', 'append')
										),

										array(
											'label'		=>	__('Accessibility', 'ws-form'),
											'meta_keys'	=>	array('aria_label')
										),

										array(
											'label'		=>	__('Exclusions', 'ws-form'),
											'meta_keys'	=>	array('exclude_email')
										),

										array(
											'label'		=>	__('Hidden Behavior', 'ws-form'),
											'meta_keys'	=>	array('hidden_bypass')
										)
									)
								),

								// Tab: Advanced
								'advanced'	=>	array(

									'label'			=>	__('Advanced', 'ws-form'),

									'fieldsets'		=>	array(

										array(
											'label'		=>	__('Style', 'ws-form'),
											'meta_keys'	=>	array('label_position', 'label_column_width', 'class_single_vertical_align')
										),

										array(
											'label'		=>	__('Classes', 'ws-form'),
											'meta_keys'	=> array('class_field_wrapper', 'class_field')
										),

										array(
											'label'		=>	__('Restrictions', 'ws-form'),
											'meta_keys'	=>	array('field_user_status', 'field_user_roles', 'field_user_capabilities')
										),

										array(
											'label'		=>	__('Custom Attributes', 'ws-form'),
											'meta_keys'	=>	array('custom_attributes')
										),

										array(
											'label'		=>	__('Breakpoints', 'ws-form'),
											'meta_keys'	=> array('breakpoint_sizes'),
											'class'		=>	array('wsf-fieldset-panel')
										)
									)
								)
							)
						)
					)
				)
			);

			// Apply filter
			$field_types = apply_filters('wsf_config_field_types', $field_types);

			// Add icons and compatibility links
			if(!$public) {

				foreach($field_types as $group_key => $group) {

					$types = $group['types'];

					foreach($types as $field_key => $field_type) {

						// Set icons (If not already an SVG)
						$field_icon = isset($field_type['icon']) ? $field_type['icon'] : $field_key;
						if(strpos($field_icon, '<svg') === false) {

							$field_types[$group_key]['types'][$field_key]['icon'] = self::get_icon_16_svg($field_icon);
						}

						// Set compatibility
						if(isset($field_type['compatibility_id'])) {

							$field_types[$group_key]['types'][$field_key]['compatibility_url'] = str_replace('#compatibility_id', $field_type['compatibility_id'], WS_FORM_COMPATIBILITY_MASK);
							unset($field_types[$group_key]['types'][$field_key]['compatibility_id']);
						}
					}
				}
			}

			// Cache
			self::$field_types[$public] = $field_types;

			return $field_types;
		}

		// Configuration - Field types (Single dimension array)
		public static function get_field_types_flat($public = true) {

			// Check cache
			if(isset(self::$field_types_flat[$public])) { return self::$field_types_flat[$public]; }

			$field_types = array();
			$field_types_config = self::get_field_types($public);

			foreach($field_types_config as $group) {

				$types = $group['types'];

				foreach($types as $key => $field_type) {

					$field_types[$key] = $field_type;
				}
			}

			// Cache
			self::$field_types_flat[$public] = $field_types;

			return $field_types;
		}

		// Configuration - Skins
		public static function get_skins() {

			$skins = array(

				'ws_form'			=>	array(

					'label'				=>	WS_FORM_NAME_GENERIC,

					'setting_id_prefix'	=>	'',

					'defaults'			=>	array(

						// Colors
						'color_default'					=> '#000000',
						'color_default_inverted' 		=> '#ffffff',
						'color_default_light' 			=> '#767676',
						'color_default_lighter' 		=> '#ceced2',
						'color_default_lightest' 		=> '#efeff4',
						'color_primary'					=> '#205493',
						'color_secondary'				=> '#5b616b',
						'color_success'					=> '#2e8540',
						'color_information'				=> '#02bfe7',
						'color_warning'					=> '#fdb81e',
						'color_danger'					=> '#bb0000',
						'color_form_background'			=> '',

						// Typography
						'font_family'					=> 'inherit',
						'font_size' 					=> 14,
						'font_size_large'				=> 18,
						'font_size_small'				=> 12,
						'font_weight'					=> 'inherit',
						'line_height'					=> 1.4,

						// Border
						'border'						=> true,
						'border_width'					=> 1,
						'border_style'					=> 'solid',
						'border_radius'					=> 4,

						// Box shadow
						'box_shadow'					=> true,
						'box_shadow_width' 				=> 4,
						'box_shadow_color_opacity'		=> 0.25,

						// Transition
						'transition'					=> true,
						'transition_speed'				=> 200,
						'transition_timing_function'	=> 'ease-in-out',

						// Advanced
						'grid_gutter'					=> 20,
						'spacing'						=> 10,
						'spacing_small'					=> 5,
						'label_position_inside_mode'	=> 'move',
						'label_column_inside_scale'		=> 0.9
					)
				)

	//_conversational
				,'ws_form_conv'	=>	array(

					'label'				=>	sprintf(__('%s - Conversational', 'ws-form'), WS_FORM_NAME_GENERIC
				),

					'conversational'	=>	true,

					'setting_id_prefix'	=>	'conv',

					'defaults'			=>	array(

						// Colors
						'color_default'					=> '#000000',
						'color_default_inverted' 		=> '#ffffff',
						'color_default_light' 			=> '#767676',
						'color_default_lighter' 		=> '#ceced2',
						'color_default_lightest' 		=> '#efeff4',
						'color_primary'					=> '#205493',
						'color_secondary'				=> '#5b616b',
						'color_success'					=> '#2e8540',
						'color_information'				=> '#02bfe7',
						'color_warning'					=> '#fdb81e',
						'color_danger'					=> '#bb0000',
						'color_form_background'			=> '#ffffff',

						// Typography
						'font_family'					=> '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
						'font_size' 					=> 22,
						'font_size_large'				=> 26,
						'font_size_small'				=> 18,
						'font_weight'					=> 'normal',
						'line_height'					=> 1.4,

						// Border
						'border'						=> true,
						'border_width'					=> 2,
						'border_style'					=> 'solid',
						'border_radius'					=> 4,

						// Box shadow
						'box_shadow'					=> true,
						'box_shadow_width' 				=> 4,
						'box_shadow_color_opacity'		=> 0.25,

						// Transition
						'transition'					=> true,
						'transition_speed'				=> 200,
						'transition_timing_function'	=> 'ease-in-out',

						// Advanced
						'grid_gutter'					=> 40,
						'spacing'						=> 20,
						'spacing_small'					=> 10,
						'label_position_inside_mode'	=> 'move',
						'label_column_inside_scale'		=> 0.9,

						// Conversational
						'conversational_max_width'					=> '800px',
						'conversational_color_background'			=> '#efeff4',
						'conversational_color_background_nav'		=> '#585858',
						'conversational_color_foreground_nav'		=> '#ffffff',
						'conversational_opacity_section_inactive'	=> '0.25'
					)
				)
			);

			foreach($skins as $skin_id => $skin) {

				$defaults = $skins[$skin_id]['defaults'];

				$skins[$skin_id]['defaults']['label_column_inside_offset'] = -(round(($defaults['font_size'] * $defaults['line_height']) / 2) + 10 - $defaults['border_width']);
			}

			// Apply filter
			$skins = apply_filters('wsf_config_skins', $skins);

			return $skins;
		}

		// Configuration - Customize
		public static function get_customize() {

			$customize	=	array(

				'colors'	=>	array(

					'heading'	=>	__('Colors', 'ws-form'),

					'fields'	=>	array(

						'color_default'	=> array(

							'label'			=>	__('Default', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Labels and field values.', 'ws-form')
						),

						'color_default_inverted'	=> array(

							'label'			=>	__('Inverted', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Field backgrounds and button text.', 'ws-form')
						),

						'color_default_light'	=> array(

							'label'			=>	__('Light', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Placeholders, help text, and disabled field values.', 'ws-form')
						),

						'color_default_lighter'	=> array(

							'label'			=>	__('Lighter', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Field borders and buttons.', 'ws-form')
						),

						'color_default_lightest'	=> array(

							'label'			=>	__('Lightest', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Range slider backgrounds, progress bar backgrounds, and disabled field backgrounds.', 'ws-form')
						),

						'color_primary'	=> array(

							'label'			=>	__('Primary', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Checkboxes, radios, range sliders, progress bars, and submit buttons.')
						),

						'color_secondary'	=> array(

							'label'			=>	__('Secondary', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Secondary elements such as a reset button.', 'ws-form')
						),

						'color_success'	=> array(

							'label'			=>	__('Success', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Completed progress bars, save buttons, and success messages.')
						),

						'color_information'	=> array(

							'label'			=>	__('Information', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Information messages.', 'ws-form')
						),

						'color_warning'	=> array(

							'label'			=>	__('Warning', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Warning messages.', 'ws-form')
						),

						'color_danger'	=> array(

							'label'			=>	__('Danger', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Required field labels, invalid field borders, invalid feedback, remove repeatable section buttons, and danger messages.')
						),

						'color_form_background'	=> array(

							'label'			=>	__('Form Background', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Leave blank for none.', 'ws-form')
						)
					)
				),

				'typography'	=>	array(

					'heading'		=>	__('Typography', 'ws-form'),

					'fields'		=>	array(

						'font_family'	=> array(

							'label'			=>	__('Font Family', 'ws-form'),
							'type'			=>	'text',
							'description'	=>	__('Font family used throughout the form.', 'ws-form')
						),

						'font_size'	=> array(

							'label'			=>	__('Font Size', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Regular font size used on the form.', 'ws-form')
						),

						'font_size_large'	=> array(

							'label'			=>	__('Font Size Large', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Font size used for section labels and fieldset legends.', 'ws-form')
						),

						'font_size_small'	=> array(

							'label'			=>	__('Font Size Small', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Font size used for help text and invalid feedback text.', 'ws-form')
						),

						'font_weight'	=>	array(

							'label'			=>	__('Font Weight', 'ws-form'),
							'type'			=>	'select',
							'choices'		=>	array(

								'inherit'	=>	__('Inherit', 'ws-form'),
								'normal'	=>	__('Normal', 'ws-form'),
								'bold'		=>	__('Bold', 'ws-form'),
								'100'		=>	'100',
								'200'		=>	'200',
								'300'		=>	'300',
								'400'		=>	'400 (' . __('Normal', 'ws-form') . ')',
								'500'		=>	'500',
								'600'		=>	'600',
								'700'		=>	'700 (' . __('Bold', 'ws-form') . ')',
								'800'		=>	'800',
								'900'		=>	'900'
							),
							'description'	=>	__('Font weight used throughout the form.', 'ws-form')
						),


						'line_height'	=> array(

							'label'			=>	__('Line Height', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Line height used throughout form.', 'ws-form')
						)
					)
				),

				'borders'	=>	array(

					'heading'		=>	__('Borders', 'ws-form'),

					'fields'		=>	array(

						'border'	=>	array(

							'label'			=>	__('Enabled', 'ws-form'),
							'type'			=>	'checkbox',
							'description'	=>	__('When checked, borders will be shown.', 'ws-form')
							),

						'border_width'	=> array(

							'label'			=>	__('Width', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Specify the width of borders used through the form. For example, borders around form fields.', 'ws-form')
						),

						'border_style'	=>	array(

							'label'			=>	__('Style', 'ws-form'),
							'type'			=>	'select',
							'choices'		=>	array(

								'dashed'	=>	__('Dashed', 'ws-form'),
								'dotted'	=>	__('Dotted', 'ws-form'),
								'double'	=>	__('Double', 'ws-form'),
								'groove'	=>	__('Groove', 'ws-form'),
								'inset'		=>	__('Inset', 'ws-form'),
								'outset'	=>	__('Outset', 'ws-form'),
								'ridge'		=>	__('Ridge', 'ws-form'),
								'solid'		=>	__('Solid', 'ws-form')
							),
							'description'	=>	__('Border style used throughout the form.', 'ws-form')
						),

						'border_radius'	=> array(

							'label'			=>	__('Radius', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Border radius used throughout the form.', 'ws-form')
						)
					)
				),

				'box_shadows'	=>	array(

					'heading'		=>	__('Box Shadows', 'ws-form'),

					'fields'		=>	array(

						'box_shadow'	=>	array(

							'label'			=>	__('Enabled', 'ws-form'),
							'type'			=>	'checkbox',
							'description'	=>	__('When checked, box shadows will be shown.', 'ws-form')
							),

						'box_shadow_width'	=> array(

							'label'			=>	__('Width', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Specify the width of box shadows used through the form. For example, box shadows around focused form fields.', 'ws-form')
						),

						'box_shadow_color_opacity'	=> array(

							'label'			=>	__('Opacity', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Specify the opacity of box shadows used through the form. (e.g. 0 is fully transparent and 1 is fully opaque)', 'ws-form')
						)
					)
				),

				'transitions'	=>	array(

					'heading'	=>	__('Transitions', 'ws-form'),

					'fields'	=>	array(

						'transition'	=>	array(

							'label'			=>	__('Enabled', 'ws-form'),
							'type'			=>	'checkbox',
							'description'	=>	__('When checked, transitions will be used on the form.', 'ws-form')
						),

						'transition_speed'	=> array(

							'label'			=>	__('Speed', 'ws-form'),
							'type'			=>	'number',
							'help'			=>	__('Value in milliseconds.', 'ws-form'),
							'description'	=>	__('Transition speed in milliseconds.', 'ws-form')
						),

						'transition_timing_function'	=>	array(

							'label'			=>	__('Timing Function', 'ws-form'),
							'type'			=>	'select',
							'choices'		=>	array(

								'ease'			=>	__('Ease', 'ws-form'),
								'ease-in'		=>	__('Ease In', 'ws-form'),
								'ease-in-out'	=>	__('Ease In Out', 'ws-form'),
								'ease-out'		=>	__('Ease Out', 'ws-form'),
								'linear'		=>	__('Linear', 'ws-form'),
								'step-end'		=>	__('Step End', 'ws-form'),
								'step-start'	=>	__('Step Start', 'ws-form')
							),
							'description'	=>	__('Speed curve of the transition effect.', 'ws-form')
						)
					)
				),

				'advanced'	=>	array(

					'heading'	=>	__('Advanced', 'ws-form'),

					'fields'	=>	array(

						'grid_gutter'	=> array(

							'label'			=>	__('Grid Gutter', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Sets the distance between form elements.', 'ws-form')
						),

						'spacing'	=> array(

							'label'			=>	__('Spacing', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Spacing used for section legends, checkboxes, and radios', 'ws-form')
						),

						'spacing_small'	=> array(

							'label'			=>	__('Spacing Small', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Spacing used for field labels, help text, invalid feedback, ratings, and section icons.', 'ws-form')
						),

						'label_position_inside_mode'	=>	array(

							'label'			=>	__('Inside Label Behavior', 'ws-form'),
							'type'			=>	'select',
							'choices'		=>	array(

								'move'			=>	__('Move', 'ws-form'),
								'hide'			=>	__('Hide', 'ws-form')
							),
							'description'	=>	__('Select the behavior of the label if content is present in a field.', 'ws-form')
						),

						'label_column_inside_offset'	=>	array(

							'label'			=>	__('Inside Label Vertical Offset', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('How many pixels to move the label vertically if content is present in a field.', 'ws-form')
						),

						'label_column_inside_scale'	=>	array(

							'label'			=>	__('Inside Label Scale', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('What factor to scale the label by if content is present in a field.', 'ws-form')
						)
					)
				),

				'conversational'	=>	array(

					'heading'	=>	__('Conversational', 'ws-form'),

					'skin_ids'	=>	array('ws_form_conv'),

					'fields'	=>	array(

						'conversational_max_width'	=> array(

							'label'			=>	__('Form Max Width', 'ws-form'),
							'type'			=>	'text',
							'description'	=>	__('Sets the max width of the conversational form.', 'ws-form')
						),

						'conversational_color_background'	=> array(

							'label'			=>	__('Background Color', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Leave blank for none.', 'ws-form')
						),

						'conversational_color_background_nav'	=> array(

							'label'			=>	__('Navigation Background Color', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Leave blank for none.', 'ws-form')
						),

						'conversational_color_foreground_nav'	=> array(

							'label'			=>	__('Navigation Foreground Color', 'ws-form'),
							'type'			=>	'color',
							'description'	=>	__('Leave blank for none.', 'ws-form')
						),

						'conversational_opacity_section_inactive'	=> array(

							'label'			=>	__('Inactive Section Opacity', 'ws-form'),
							'type'			=>	'number',
							'description'	=>	__('Leave blank for none.', 'ws-form')
						)
					)
				)
			);

			// Apply filter
			$customize = apply_filters('wsf_config_customize', $customize);

			return $customize;
		}

		// Configuration - Options
		public static function get_options($process_options = true) {

			$options = array(

				// Basic
				'basic'		=> array(

					'label'		=>	__('Basic', 'ws-form'),
					'groups'	=>	array(

						'preview'	=>	array(

							'heading'		=>	__('Preview', 'ws-form'),
							'fields'	=>	array(

								'helper_live_preview'	=>	array(

									'label'		=>	__('Live', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('Update the form preview window automatically.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/previewing-forms/'), __('Learn more', 'ws-form')),
									'default'	=>	true
								),

								'preview_template'	=> array(

									'label'				=>	__('Template', 'ws-form'),
									'type'				=>	'select',
									'help'				=>	__('Page template used for previewing forms.', 'ws-form'),
									'options'			=>	array(),	// Populated below
									'default'			=>	''
								),
								'helper_debug'	=> array(

									'label'		=>	__('Debug Console', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('Choose when to show the debug console.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/debug-console/'), __('Learn more', 'ws-form')),
									'default'	=>	'',
									'options'	=>	array(

										'off'				=>	array('text' => __('Off', 'ws-form')),
										'administrator'		=>	array('text' => __('Administrators only', 'ws-form')),
										'on'				=>	array('text' => __('Show always'), 'ws-form')
									),
									'mode'	=>	array(

										'basic'		=>	'off',
										'advanced'	=>	'administrator'
									)
								)
							)
						),

						'layout_editor'	=>	array(

							'heading'	=>	__('Layout Editor', 'ws-form'),
							'fields'	=>	array(

								'mode'	=> array(

									'label'		=>	__('Mode', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	__('Advanced mode allows variables and calculations to be used in field settings.', 'ws-form'),
									'default'	=>	'basic',
									'options'	=>	array(

										'basic'		=>	array('text' => __('Basic', 'ws-form')),
										'advanced'	=>	array('text' => __('Advanced', 'ws-form'))
									)
								),

								'helper_columns'	=>	array(

									'label'		=>	__('Column Guidelines', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	__('Show column guidelines when editing forms?', 'ws-form'),
									'options'	=>	array(

										'off'		=>	array('text' => __('Off', 'ws-form')),
										'resize'	=>	array('text' => __('On resize', 'ws-form')),
										'on'		=>	array('text' => __('Always on', 'ws-form')),
									),
									'default'	=>	'resize'
								),

								'helper_breakpoint_width'	=>	array(

									'label'		=>	__('Breakpoint Widths', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Resize the width of the form to the selected breakpoint.', 'ws-form'),
									'default'	=>	true
								),

								'helper_compatibility' => array(

									'label'		=>	__('HTML Compatibility Helpers', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Show HTML compatibility helper links (Data from', 'ws-form') . ' <a href="' . WS_FORM_COMPATIBILITY_URL . '" target="_blank">' . WS_FORM_COMPATIBILITY_NAME . '</a>).',
									'default'	=>	false,
									'mode'		=>	array(

										'basic'		=>	false,
										'advanced'	=>	true
									)
								),

								'helper_icon_tooltip' => array(

									'label'		=>	__('Icon Tooltips', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Show icon tooltips.'),
									'default'	=>	true
								),

								'helper_field_help' => array(

									'label'		=>	__('Sidebar Help Text', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Show help text in sidebar.'),
									'default'	=>	true
								),

								'helper_section_id'	=> array(

									'label'		=>	__('Section IDs', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Show IDs on sections.', 'ws-form'),
									'default'	=>	true,
									'mode'		=>	array(

										'basic'		=>	false,
										'advanced'	=>	true
									)
								),

								'helper_field_id'	=> array(

									'label'		=>	__('Field IDs', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Show IDs on fields. Useful for #field(nnn) variables.', 'ws-form'),
									'default'	=>	true
								)
							)
						),

						'statistics'	=>	array(

							'heading'	=>	__('Statistics', 'ws-form'),
							'fields'	=>	array(

								'disable_form_stats'			=>	array(

									'label'		=>	__('Disable', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false,
									'help'		=>	sprintf('%s <a href="%s" target="_blank">%s</a>', sprintf(

										/* translators: %s = WS Form */
										__('If checked, %s will stop gathering statistical data about forms.', 'ws-form'),

										WS_FORM_NAME_GENERIC

									), WS_Form_Common::get_plugin_website_url('/knowledgebase/statistics/'), __('Learn more', 'ws-form')),
								),

								'add_view_method'	=>	array(

									'label'		=>	__('Method', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	sprintf('%s <a href="%s" target="_blank">%s</a>', sprintf(

										/* translators: %s = WS Form */
										__('Select how %s should gather form statistics.', 'ws-form'),

										WS_FORM_NAME_GENERIC

									), WS_Form_Common::get_plugin_website_url('/knowledgebase/global-settings/'), __('Learn more', 'ws-form')),
									'default'	=>	'',
									'options'	=>	array()
								)
							)
						),
						'admin'	=>	array(

							'heading'	=>	__('Administration', 'ws-form'),
							'fields'	=>	array(

								'disable_count_submit_unread'	=>	array(

									'label'		=>	__('Disable Unread Submission Bubbles', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false
								),

								'disable_toolbar_menu'			=>	array(

									'label'		=>	__('Disable Toolbar Menu', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false,
									'help'		=>	sprintf(

										/* translators: %s = WS Form */
										__('If checked, the %s toolbar menu will not be shown.', 'ws-form'),

										WS_FORM_NAME_GENERIC
									)
								)
							)
						)
					)
				),

				// Advanced
				'advanced'	=> array(

					'label'		=>	__('Advanced', 'ws-form'),
					'groups'	=>	array(

						'markup'	=>	array(

							'heading'		=>	__('Markup', 'ws-form'),
							'fields'	=>	array(

								'framework'	=> array(

									'label'				=>	__('Framework', 'ws-form'),
									'type'				=>	'select',
									'help'				=>	__('Framework used for rendering the front-end HTML.', 'ws-form'),
									'options'			=>	array(),	// Populated below
									'default'			=>	WS_FORM_DEFAULT_FRAMEWORK,
									'button'			=>	'wsf-framework-detect',
									'public'			=>	true,
									'data_change'		=>	'reload' 				// Reload settings on change
								),

								'framework_column_count'	=> array(

									'label'		=>	__('Column Count', 'ws-form'),
									'type'		=>	'select_number',
									'default'	=>	12,
									'minimum'	=>	1,
									'maximum'	=>	24,
									'public'	=>	true,
									'absint'	=>	true,
									'help'		=>	__('We recommend leaving this setting at 12.', 'ws-form')
								),

								'comments_html'	=>	array(

									'label'		=>	__('HTML Comments', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should HTML include comments?', 'ws-form'),
									'default'	=>	false,
									'public'	=>	true
								),

								'comments_css'	=>	array(

									'label'		=>	__('CSS Comments', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should CSS include comments?', 'ws-form'),
									'default'	=>	false,
									'public'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),

								'css_layout'	=>	array(

									'label'		=>	__('Framework CSS', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should the framework CSS be rendered?', 'ws-form'),
									'default'	=>	true,
									'public'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),

								'css_skin'	=>	array(

									'label'		=>	__('Skin CSS', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	sprintf(__('Should the skin CSS be rendered? <a href="%s">Click here</a> to customize the skin.', 'ws-form'), admin_url('customize.php?return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dws-form-settings%26tab%3Dappearance')),
									'default'	=>	true,
									'public'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),
							)
						),

						'performance'	=>	array(

							'heading'		=>	__('Performance', 'ws-form'),
							'condition'	=>	array('framework' => 'ws-form'),
							'fields'	=>	array(

								'css_compile'	=>	array(

									'label'		=>	__('Compile CSS', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should CSS be precompiled? (Recommended)', 'ws-form'),
									'default'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),

								'css_inline'	=>	array(

									'label'		=>	__('Inline CSS', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should CSS be rendered inline? (Recommended)', 'ws-form'),
									'default'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),

								'css_cache_duration'	=>	array(

									'label'		=>	__('CSS Cache Duration', 'ws-form'),
									'type'		=>	'number',
									'help'		=>	__('Expires header duration in seconds for CSS.', 'ws-form'),
									'default'	=>	WS_FORM_CSS_CACHE_DURATION_DEFAULT,
									'public'	=>	true,
									'condition'	=>	array('framework' => 'ws-form')
								),

								'enqueue_dynamic'	=>	array(

									'label'		=>	__('Dynamic Enqueuing', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('Should WS Form dynamically enqueue form components? (Recommended)', 'ws-form'),
									'default'	=>	true
								)
							)
						),

						'api_keys'	=>	array(

							'heading'	=>	__('API Keys', 'ws-form'),
							'fields'	=>	array(

								'api_key_google_map'	=>	array(

									'label'		=>	__('Google', 'ws-form'),
									'type'		=>	'text',
									'help'		=>	__('Enter your Google API key.', 'ws-form'),
									'default'	=>	'',
									'help'		=>	sprintf('%s <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">%s</a>', __('Need an API key?', 'ws-form'), __('Learn more', 'ws-form')),
									'public'	=>	true
								)
							)
						),

						'javascript'	=>	array(

							'heading'	=>	__('JavaScript', 'ws-form'),
							'fields'	=>	array(

								'jquery_footer'	=>	array(

									'label'		=>	__('Enqueue in Footer', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('If checked, scripts will be enqueued in the footer.', 'ws-form'),
									'default'	=>	''
								),

								'jquery_source'	=>	array(

									'label'		=>	__('jQuery Source', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	__('Where should external libraries load from? Use \'Local\' if you are using optimization plugins.', 'ws-form'),
									'default'	=>	'local',
									'public'	=>	true,
									'options'	=>	array(

										'local'		=>	array('text' => __('Local', 'ws-form')),
										'cdn'		=>	array('text' => __('CDN', 'ws-form'))
									)
								),

								'ui_datepicker'	=>	array(

									'label'		=>	__('jQuery Date/Time Picker', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	__('When should date fields use a jQuery Date/Time Picker component?', 'ws-form'),
									'default'	=>	'on',
									'public'	=>	true,
									'options'	=>	array(

										'on'		=>	array('text' => __('Always', 'ws-form')),
										'native'	=>	array('text' => __('If native not available', 'ws-form')),
										'off'		=>	array('text' => __('Never', 'ws-form'))
									)
								),

								'ui_color'	=>	array(

									'label'		=>	__('jQuery Color Picker', 'ws-form'),
									'type'		=>	'select',
									'help'		=>	__('When should color fields use a jQuery Color picker component?', 'ws-form'),
									'default'	=>	'on',
									'public'	=>	true,
									'options'	=>	array(

										'on'		=>	array('text' => __('Always', 'ws-form')),
										'native'	=>	array('text' => __('If native not available', 'ws-form')),
										'off'		=>	array('text' => __('Never', 'ws-form'))
									)
								),
							)
						),
						'upload'	=>	array(

							'heading'	=>	__('File Uploads', 'ws-form'),
							'fields'	=>	array(

								'max_upload_size'	=>	array(

									'label'		=>	__('Maximum Filesize (Bytes)', 'ws-form'),
									'type'		=>	'number',
									'default'	=>	'#max_upload_size',
									'minimum'	=>	0,
									'maximum'	=>	'#max_upload_size',
									'button'	=>	'wsf-max-upload-size'
								),

								'max_uploads'	=>	array(

									'label'		=>	__('Maximum Files', 'ws-form'),
									'type'		=>	'number',
									'default'	=>	'#max_uploads',
									'minimum'	=>	0,
									'maximum'	=>	'#max_uploads',
									'button'	=>	'wsf-max-uploads'
								)
							)
						),
						'cookie'	=>	array(

							'heading'	=>	__('Cookies', 'ws-form'),
							'fields'	=>	array(

								'cookie_timeout'	=>	array(

									'label'		=>	__('Cookie Timeout (Seconds)', 'ws-form'),
									'type'		=>	'number',
									'help'		=>	__('Duration in seconds cookies are valid for.', 'ws-form'),
									'default'	=>	60 * 60 * 24 * 28,	// 28 day
									'public'	=>	true
								),

								'cookie_prefix'	=>	array(

									'label'		=>	__('Cookie Prefix', 'ws-form'),
									'type'		=>	'text',
									'help'		=>	__('We recommend leaving this value as it is.', 'ws-form'),
									'default'	=>	WS_FORM_IDENTIFIER,
									'public'	=>	true
								),

								'cookie_hash'	=>	array(

									'label'		=>	__('Enable Save Cookie', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('If checked a cookie will be set when a form save button is clicked to later recall the form content.', 'ws-form'),
									'default'	=>	true,
									'public'	=>	true
								)
							)
						),

						'security'	=>	array(

							'heading'	=>	__('Security', 'ws-form'),
							'fields'	=>	array(

								'security_nonce'	=>	array(

									'label'		=>	__('Enable NONCE', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	sprintf('%s <a href="https://codex.wordpress.org/WordPress_Nonces" target="_blank">%s</a><br />%s', __('Add a NONCE to all form submissions.', 'ws-form'), __('Learn more', 'ws-form'), __('If enabled we recommend keeping overall page caching to less than 10 hours.<br />NONCEs are always used on forms if a user is logged in.', 'ws-form')),
									'default'	=>	''
								)
							)
						),

						'lookup'	=>	array(

							'heading'	=>	__('Tracking Lookups', 'ws-form'),
							'fields'	=>	array(

								'ip_lookup_url_mask' => array(

									'label'		=>	__('IP Lookup URL Mask', 'ws-form'),
									'type'		=>	'text',
									'default'	=>	'https://whatismyipaddress.com/ip/#value',
									'help'		=>	__('#value will be replaced with the tracking IP address.', 'ws-form')
								),

								'latlon_lookup_url_mask' => array(

									'label'		=>	__('Geolocation Lookup URL Mask', 'ws-form'),
									'type'		=>	'text',
									'default'	=>	'https://www.google.com/maps/search/?api=1&query=#value',
									'help'		=>	__('#value will be replaced with latitude,longitude.', 'ws-form')
								)
							)
						)
					)
				),

				// E-Commerce
				'ecommerce'	=> array(

					'label'		=>	__('E-Commerce', 'ws-form'),
					'groups'	=>	array(

						'price'	=>	array(

							'heading'	=>	__('Prices', 'ws-form'),
							'fields'	=>	array(

								'currency'	=> array(

									'label'		=>	__('Currency', 'ws-form'),
									'type'		=>	'select',
									'default'	=>	WS_Form_Common::get_currency_default(),
									'options'	=>	array(),
									'public'	=>	true
								),

								'currency_position'	=> array(

									'label'		=>	__('Currency Position', 'ws-form'),
									'type'		=>	'select',
									'default'	=>	'left',
									'options'	=>	array(
										'left'			=>	array('text' => __('Left', 'ws-form')),
										'right'			=>	array('text' => __('Right', 'ws-form')),
										'left_space'	=>	array('text' => __('Left with space', 'ws-form')),
										'right_space'	=>	array('text' => __('Right with space', 'ws-form'))
									),
									'public'	=>	true
								),

								'price_thousand_separator'	=> array(

									'label'		=>	__('Thousand Separator', 'ws-form'),
									'type'		=>	'text',
									'default'	=>	',',
									'public'	=>	true
								),

								'price_decimal_separator'	=> array(

									'label'		=>	__('Decimal Separator', 'ws-form'),
									'type'		=>	'text',
									'default'	=>	'.',
									'public'	=>	true
								),

								'price_decimals'	=> array(

									'label'		=>	__('Number Of Decimals', 'ws-form'),
									'type'		=>	'number',
									'default'	=>	'2',
									'public'	=>	true
								)
							)
						),

						'submission'	=>	array(

							'heading'	=>	__('Submissions', 'ws-form'),
							'fields'	=>	array(

								'submit_edit_ecommerce'	=>	array(

									'label'		=>	__('Allow Price Field Edits', 'ws-form'),
									'type'		=>	'checkbox',
									'help'		=>	__('If checked, prices can be edited in submissions. Note that changes to prices will not recalculate values in the rest of the submission.', 'ws-form'),
									'default'	=>	''
								)
							)
						)
					)
				),
				// System
				'system'	=> array(

					'label'		=>	__('System', 'ws-form'),
					'fields'	=>	array(

						'system' => array(

							'label'		=>	__('System Report', 'ws-form'),
							'type'		=>	'static'
						),

						'setup'	=> array(

							'type'		=>	'hidden',
							'default'	=>	false
						)
					)
				),
				// License
				'license'	=> array(

					'label'		=>	__('License', 'ws-form'),
					'fields'	=>	array(

						'version'	=>	array(

							'label'		=>	__('Version', 'ws-form'),
							'type'		=>	'static'
						),

						'license_key'	=>	array(

							'label'		=>	__('License Key', 'ws-form'),
							'type'		=>	'license',

							'help'		=>	sprintf('%s <a href="%s" target="_blank">%s</a>', 

								sprintf(

									/* translators: %1$s = Presentable name (e.g. WS Form PRO) */
									__('Enter your %1$s license key here. If you have a Freelance or Agency license, please enter your %1$s key instead.', 'ws-form'),
									WS_FORM_NAME_PRESENTABLE
								),

								WS_Form_Common::get_plugin_website_url('/knowledgebase/licensing/'), __('Learn more', 'ws-form')
							),
							'button'	=>	'wsf-license'
						),

						'license_status'	=>	array(

							'label'		=>	__('License Status', 'ws-form'),
							'type'		=>	'static'
						)
					)
				),
				// Data
				'data'	=> array(

					'label'		=>	__('Data', 'ws-form'),
					'groups'	=>	array(

						'form'	=>	array(

							'heading'	=>	__('Forms', 'ws-form'),
							'fields'	=>	array(

								'form_stat_reset' => array(

									'label'		=>	__('Reset Statistics', 'ws-form'),
									'type'		=>	'select',
									'save'		=>	false,
									'button'	=>	'wsf-form-stat-reset'
								)
							)
						),

						'encryption'	=>	array(

							'heading'	=>	__('Encryption', 'ws-form'),
							'fields'	=>	array(

								'encryption_enabled' => array(

									'label'		=>	__('Enable Data Encryption', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false,
									'help'		=>	sprintf(__('<a href="%s" target="_blank">%s</a>', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/data-encryption/'), __('Learn more', 'ws-form'))
								),

								'encryption_status' => array(

									'label'		=>	__('Encryption Status', 'ws-form'),
									'type'		=>	'static'
								)
							)
						),
						'uninstall'	=>	array(

							'heading'	=>	__('Uninstall', 'ws-form'),
							'fields'	=>	array(

								'uninstall_options' => array(

									'label'		=>	__('Delete Plugin Settings on Uninstall', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false,
									'help'		=>	sprintf('<p><strong style="color: #bb0000;">%s:</strong> %s</p>', __('Caution', 'ws-form'), __('If you enable this setting and uninstall the plugin this data cannot be recovered.'))
								),

								'uninstall_database' => array(

									'label'		=>	__('Delete Database Tables on Uninstall', 'ws-form'),
									'type'		=>	'checkbox',
									'default'	=>	false,
									'help'		=>	sprintf('<p><strong style="color: #bb0000;">%s:</strong> %s</p>', __('Caution', 'ws-form'), __('If you enable this setting and uninstall the plugin this data cannot be recovered.'))
								)
							)
						)
					)
				)
			);

			// Don't run the rest of this function to improve client side performance
			if(!$process_options) {

				// Apply filter
				$options = apply_filters('wsf_config_options', $options);

				return $options;
			}

			// Frameworks
			$frameworks = self::get_frameworks(false);
			foreach($frameworks['types'] as $key => $framework) {

				$name = $framework['name'];
				$options['advanced']['groups']['markup']['fields']['framework']['options'][$key] = array('text' => $name);
			}

			// Templates
			$options['basic']['groups']['preview']['fields']['preview_template']['options'][''] = array('text' => __('Automatic', 'ws-form'));

			// Custom page templates
			$page_templates = array();
			$templates_path = get_template_directory();
			$templates = wp_get_theme()->get_page_templates();
			$templates['page.php'] = 'Page';
			$templates['singular.php'] = 'Singular';
			$templates['index.php'] = 'Index';
			$templates['front-page.php'] = 'Front Page';
			$templates['single-post.php'] = 'Single Post';
			$templates['single.php'] = 'Single';
			$templates['home.php'] = 'Home';

			foreach($templates as $template_file => $template_title) {

				// Build template path
				$template_file_full = $templates_path . '/' . $template_file;

				// Skip files that don't exist
				if(!file_exists($template_file_full)) { continue; }

				$page_templates[$template_file] = $template_title . ' (' . $template_file . ')';
			}

			asort($page_templates);

			foreach($page_templates as $template_file => $template_title) {

				$options['basic']['groups']['preview']['fields']['preview_template']['options'][$template_file] = array('text' => $template_title);
			}

			// Fallback
			$options['basic']['groups']['preview']['fields']['preview_template']['options']['fallback'] = array('text' => __('Blank Page', 'ws-form'));

			// Currencies
			$currencies = self::get_currencies();
			foreach($currencies as $code => $currency) {

				$options['ecommerce']['groups']['price']['fields']['currency']['options'][$code] = array('text' => $currency['n'] . ' (' . $currency['s'] . ')');
			}

			// Forms
			$options['data']['groups']['form']['fields']['form_stat_reset']['options'][''] = array('text' => __('Select...', 'ws-form'));

			$ws_form_form = New WS_Form_Form();
			$forms = $ws_form_form->db_read_all('', "NOT (status = 'trash')", 'label ASC', '', '', false);

			if($forms) {

				foreach($forms as $form) {

					if($form['count_stat_view'] > 0) {

						$options['data']['groups']['form']['fields']['form_stat_reset']['options'][$form['id']] = array('text' => esc_html(sprintf(__('%s (ID: %u)', 'ws-form'), $form['label'], $form['id'])));
					}
				}
			}

			// Add view method
			$options['basic']['groups']['statistics']['fields']['add_view_method']['options'][''] = array('text' => __('AJAX', 'ws-form'));

			// Check to see if PHP script is working
			$ws_form_form_stat = new WS_Form_Form_Stat();
			$add_view_php_valid = $ws_form_form_stat->add_view_php_valid();

			$options['basic']['groups']['statistics']['fields']['add_view_method']['options']['php'] = array('text' => sprintf('%s%s', __('AJAX Low Resource', 'ws-form'), ($add_view_php_valid['error'] ? sprintf(' (%s: %s)', __('Error', 'ws-form'), $add_view_php_valid['error_message']) : '')), 'disabled' => $add_view_php_valid['error']);

			$options['basic']['groups']['statistics']['fields']['add_view_method']['options']['server'] = array('text' => __('Server Side', 'ws-form'));

			// Apply filter
			$options = apply_filters('wsf_config_options', $options);

			return $options;
		}

		// Configuration - Settings (Shared with admin and public)
		public static function get_settings_form($public = true) {

			// Check if debug is enabled
			$debug = WS_Form_Common::debug_enabled();
			$settings_form = array(

				// Language
				'language'	=> array(

					// Errors
					'error_attributes'					=>	__('No attributes specified.', 'ws-form'),
					'error_attributes_obj'				=>	__('No attributes object specified.', 'ws-form'),
					'error_attributes_form_id'			=>	__('No attributes form ID specified.', 'ws-form'),
					'error_form_id'						=>	__('Form ID not specified.', 'ws-form'),

					/* translators: %s = WS Form */
					'error_pro_required'				=>	sprintf(

						/* translators: %s = WS Form */
						__('%s PRO required.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),

					// Errors - API calls
					'error_api_call_400'				=>	__('400 Bad request response from server: %s', 'ws-form'),
					'error_api_call_401'				=>	sprintf('%s <a href="%s" target="_blank">%s</a>.', __('401 Unauthorized response from server.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/401-unauthorized/', 'api_call'), __('Click here', 'ws-form')),
					'error_api_call_403'				=>	sprintf('%s <a href="%s" target="_blank">%s</a>.', __('403 Forbidden response from server.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/403-forbidden/', 'api_call'), __('Click here', 'ws-form')),
					'error_api_call_404'				=>	__('404 Not found response from server: %s', 'ws-form'),
					'error_api_call_500'				=>	__('500 Server error response from server: %s', 'ws-form'),

					// Error message
					'dismiss'							=>  __('Dismiss', 'ws-form'),

					// Comments
					'comment_group_tabs'				=>	__('Tabs', 'ws-form'),
					'comment_groups'					=>	__('Tabs Content', 'ws-form'),
					'comment_group'						=>	__('Tab', 'ws-form'),
					'comment_sections'					=>	__('Sections', 'ws-form'),
					'comment_section'					=>	__('Section', 'ws-form'),
					'comment_fields'					=>	__('Fields', 'ws-form'),
					'comment_field'						=>	__('Field', 'ws-form'),

					// Word and character counts
					'character_singular'				=>	__('character', 'ws-form'),
					'character_plural'					=>	__('characters', 'ws-form'),
					'word_singular'						=>	__('word', 'ws-form'),
					'word_plural'						=>	__('words', 'ws-form'),

					// Date
					'week'								=>	__('Week', 'ws-form'),

					// Select all
					'select_all_label'					=>	__('Select All', 'ws-form'),
					// Section icons
					'section_icon_add'					=>  __('Add', 'ws-form'),
					'section_icon_delete'				=>  __('Remove', 'ws-form'),
					'section_icon_move-up'				=>  __('Move Up', 'ws-form'),
					'section_icon_move-down'			=>  __('Move Down', 'ws-form'),
					'section_icon_drag'					=>  __('Drag', 'ws-form'),
					'section_icon_reset'				=>  __('Reset', 'ws-form'),
					'section_icon_clear'				=>  __('Clear', 'ws-form'),

					// Parse variables
					'error_parse_variable_syntax_error_brackets'			=>	__('Syntax error, missing brackets: %s', 'ws-form'),
					'error_parse_variable_syntax_error_bracket_closing'		=>	__('Syntax error, missing closing bracket: %s', 'ws-form'),
					'error_parse_variable_syntax_error_attribute'			=>	__('Syntax error, missing attribute: %s', 'ws-form'),
					'error_parse_variable_syntax_error_attribute_invalid'	=>	__('Syntax error, invalid attribute: %s', 'ws-form'),
					'error_parse_variable_syntax_error_depth'				=>	__('Syntax error, too many iterations', 'ws-form'),
					'error_parse_variable_syntax_error_field_id'			=>	__('Syntax error, invalid field ID: %s', 'ws-form'),
					'error_parse_variable_syntax_error_section_id'			=>	__('Syntax error, invalid section ID: %s', 'ws-form'),
					'error_parse_variable_syntax_error_group_id'			=>	__('Syntax error, invalid tab ID: %s', 'ws-form'),
					'error_parse_variable_syntax_error_self_ref'			=>	__('Syntax error, fields cannot contain references to themselves: %s', 'ws-form'),
					'error_parse_variable_syntax_error_calc_in'				=>	__('Syntax error, #calc cannot be added to this field: %s', 'ws-form'),
					'error_parse_variable_syntax_error_calc_out'			=>	__('Syntax error, #calc cannot be retrieved from this field: %s', 'ws-form'),
					'error_parse_variable_syntax_error_text_in'				=>	__('Syntax error, #text cannot be added to this field: %s', 'ws-form'),
					'error_parse_variable_syntax_error_text_out'			=>	__('Syntax error, #text cannot be retrieved from this field: %s', 'ws-form'),
					'error_parse_variable_syntax_error_endif'				=>	__('Syntax error, missing #endif in #if(%s)', 'ws-form'),
					'error_parse_variable_syntax_error_operator'			=>	__('Syntax error, invalid operator in #if(%s)', 'ws-form'),
					'error_parse_variable_syntax_error_logic'				=>	__('Syntax error, invalid logic in #if(%s)', 'ws-form'),
					'error_parse_variable_syntax_error_field_date_offset'	=>	__('Syntax error, field ID %s is not a date field', 'ws-form'),

					// E-Commerce
					'error_ecommerce_negative_value'	=>	__('Negative value detected in field ID %s. If you intend to allow a negative value check the \'Allow Negative Value\' setting.', 'ws-form'),

					// Cascading
					'cascade_option_text_loading'		=>	__('Loading...', 'ws-form'),
					'cascade_option_text_no_rows'		=>	__('Select...', 'ws-form'),

					// DropzoneJS
					'dropzonejs_default_message'		=>	__('Drop files here to upload.', 'ws-form'),
					'dropzonejs_remove'					=>	__('Remove', 'ws-form'),
				)
			);

			// Conditional
			if(!$public || $debug) {

				// Additional language strings for admin or public debug feature
				$language_extra = array(

					'error_conditional_if'				=>	__('Condition [if] not found', 'ws-form'),
					'error_conditional_then'			=>	__('Condition [then] not found', 'ws-form'),
					'error_conditional_else'			=>	__('Condition [else] not found', 'ws-form'),
					'error_conditional_settings'		=>	__('Conditional settings not found', 'ws-form'),
					'error_conditional_data_grid'		=>	__('Condition field data not found', 'ws-form'),
					'error_conditional_object'			=>	__('Condition object not found', 'ws-form'),
					'error_conditional_object_id'		=>	__('Condition object ID not found', 'ws-form'),
					'error_conditional_logic'			=>	__('Condition logic not found: %s', 'ws-form'),
					'error_conditional_logic_previous'	=>	__('Condition logic previous not found: %s', 'ws-form'),
					'error_conditional_logic_previous_group'	=>	__('Group logic previous not found', 'ws-form'),
				);

				// Add to language array
				foreach($language_extra as $key => $value) {

					$settings_form['language'][$key] = $value;
				}
			}
			// Apply filter
			$settings_form = apply_filters('wsf_config_settings_form', $settings_form);

			return $settings_form;
		}

		// Get plug-in settings
		public static function get_settings_plugin($public = true) {

			// Check cache
			if(isset(self::$settings_plugin[$public])) { return self::$settings_plugin[$public]; }

			$settings_plugin = [];

			// Plugin options
			$options = self::get_options(false);

			// Set up options with default values
			foreach($options as $tab => $data) {

				if(isset($data['fields'])) {

					self::get_settings_plugin_process($data['fields'], $public, $settings_plugin);
				}

				if(isset($data['groups'])) {

					$groups = $data['groups'];

					foreach($groups as $group) {

						self::get_settings_plugin_process($group['fields'], $public, $settings_plugin);
					}
				}
			}

			// Apply filter
			$settings_plugin = apply_filters('wsf_config_settings_plugin', $settings_plugin);

			// Cache
			self::$settings_plugin[$public] = $settings_plugin;

			return $settings_plugin;
		}

		// Get plug-in settings process
		public static function get_settings_plugin_process($fields, $public, &$settings_plugin) {

			foreach($fields as $field => $attributes) {

				// Skip field if public only?
				$field_skip = false;
				if($public) {

					$field_skip = !isset($attributes['public']) || !$attributes['public'];
				}
				if($field_skip) { continue; }

				// Get default value (if available)
				if(isset($attributes['default'])) { $default_value = $attributes['default']; } else { $default_value = ''; }

				// Get option value
				$settings_plugin[$field] = WS_Form_Common::option_get($field, $default_value);
			}
		}

		// Configuration - Meta Keys
		public static function get_meta_keys($form_id = 0, $public = false) {

			// Check cache
			if(isset(self::$meta_keys[$public])) { return self::$meta_keys[$public]; }

			$label_position = array(

				array('value' => 'top', 'text' => __('Top', 'ws-form')),
				array('value' => 'right', 'text' => __('Right', 'ws-form')),
				array('value' => 'bottom', 'text' => __('Bottom', 'ws-form')),
				array('value' => 'left', 'text' => __('Left', 'ws-form')),
				array('value' => 'inside', 'text' => __('Inside', 'ws-form'))
			);

			$button_types = array(

				array('value' => '', 			'text' => __('Default', 'ws-form')),
				array('value' => 'primary', 	'text' => __('Primary', 'ws-form')),
				array('value' => 'secondary', 	'text' => __('Secondary', 'ws-form')),
				array('value' => 'success', 	'text' => __('Success', 'ws-form')),
				array('value' => 'information', 'text' => __('Information', 'ws-form')),
				array('value' => 'warning', 	'text' => __('Warning', 'ws-form')),
				array('value' => 'danger', 		'text' => __('Danger', 'ws-form')),
				array('value' => 'none', 		'text' => __('None', 'ws-form'))
			);

			$message_types = array(

				array('value' => 'success', 	'text' => __('Success', 'ws-form')),
				array('value' => 'information', 'text' => __('Information', 'ws-form')),
				array('value' => 'warning', 	'text' => __('Warning', 'ws-form')),
				array('value' => 'danger', 		'text' => __('Danger', 'ws-form')),
				array('value' => 'none', 		'text' => __('None', 'ws-form'))
			);

			$vertical_align = array(

				array('value' => '', 'text' => __('Top', 'ws-form')),
				array('value' => 'middle', 'text' => __('Middle', 'ws-form')),
				array('value' => 'bottom', 'text' => __('Bottom', 'ws-form'))
			);

			$autocomplete_options = array(

				array('value' => 'on'),
				array('value' => 'off'),
				array('value' => 'name', 'control_group' => 'text'),
				array('value' => 'honorific-prefix', 'control_group' => 'text'),
				array('value' => 'given-name', 'control_group' => 'text'),
				array('value' => 'additional-name', 'control_group' => 'text'),
				array('value' => 'family-name', 'control_group' => 'text'),
				array('value' => 'honorific-suffix', 'control_group' => 'text'),
				array('value' => 'nickname', 'control_group' => 'text'),
				array('value' => 'organization-title', 'control_group' => 'text'),
				array('value' => 'username', 'control_group' => 'username'),
				array('value' => 'new-password', 'control_group' => 'password'),
				array('value' => 'current-password', 'control_group' => 'password'),
				array('value' => 'one-time-code', 'control_group' => 'password'),
				array('value' => 'organization', 'control_group' => 'text'),
				array('value' => 'street-address', 'control_group' => 'multiline'),
				array('value' => 'address-line1', 'control_group' => 'text'),
				array('value' => 'address-line2', 'control_group' => 'text'),
				array('value' => 'address-line3', 'control_group' => 'text'),
				array('value' => 'address-level4', 'control_group' => 'text'),
				array('value' => 'address-level3', 'control_group' => 'text'),
				array('value' => 'address-level2', 'control_group' => 'text'),
				array('value' => 'address-level1', 'control_group' => 'text'),
				array('value' => 'country', 'control_group' => 'text'),
				array('value' => 'country-name', 'control_group' => 'text'),
				array('value' => 'postal-code', 'control_group' => 'text'),
				array('value' => 'cc-name', 'control_group' => 'text'),
				array('value' => 'cc-given-name', 'control_group' => 'text'),
				array('value' => 'cc-additional-name', 'control_group' => 'text'),
				array('value' => 'cc-family-name', 'control_group' => 'text'),
				array('value' => 'cc-number', 'control_group' => 'text'),
				array('value' => 'cc-exp', 'control_group' => 'month'),
				array('value' => 'cc-exp-month', 'control_group' => 'numeric'),
				array('value' => 'cc-exp-year', 'control_group' => 'numeric'),
				array('value' => 'cc-csc', 'control_group' => 'text'),
				array('value' => 'cc-type', 'control_group' => 'text'),
				array('value' => 'transaction-currency', 'control_group' => 'text'),
				array('value' => 'transaction-amount', 'control_group' => 'numeric'),
				array('value' => 'language', 'control_group' => 'text'),
				array('value' => 'bday', 'control_group' => 'date'),
				array('value' => 'bday-day', 'control_group' => 'numeric'),
				array('value' => 'bday-month', 'control_group' => 'numeric'),
				array('value' => 'bday-year', 'control_group' => 'numeric'),
				array('value' => 'sex', 'control_group' => 'text'),
				array('value' => 'url', 'control_group' => 'url'),
				array('value' => 'photo', 'control_group' => 'url'),
				array('value' => 'tel', 'control_group' => 'tel'),
				array('value' => 'tel-country-code', 'control_group' => 'text'),
				array('value' => 'tel-national', 'control_group' => 'text'),
				array('value' => 'tel-area-code', 'control_group' => 'text'),
				array('value' => 'tel-local', 'control_group' => 'text'),
				array('value' => 'tel-local-prefix', 'control_group' => 'text'),
				array('value' => 'tel-local-suffix', 'control_group' => 'text'),
				array('value' => 'tel-extension', 'control_group' => 'text'),
				array('value' => 'email', 'control_group' => 'username'),
				array('value' => 'impp', 'control_group' => 'url')
			);

			$autocomplete_control_groups = array(

				// Control group: All
				'autocomplete' => array(),

				// Control group: Text
				'autocomplete_text' => array('control_group_exclude' => array('multiline')),

				// Control group: Search
				'autocomplete_search' => array('control_group_exclude' => array('multiline')),

				// Control group: Password
				'autocomplete_password' => array('control_group_include' => array('password'), 'default' => 'new-password'),

				// Control group: URL
				'autocomplete_url' => array('control_group_include' => array('url')),

				// Control group: Email
				'autocomplete_email' => array('control_group_include' => array('username')),

				// Control group: Tel
				'autocomplete_tel' => array('control_group_include' => array('tel')),

				// Control group: Number
				'autocomplete_number' => array('control_group_include' => array('numeric')),

				// Control group: Date / Time
				'autocomplete_datetime' => array('control_group_include' => array('date', 'month'), 'default' => 'off'),

				// Control group: Price
				'autocomplete_price' => array('control_group_include' => array()),

				// Control group: Quantity
				'autocomplete_quantity' => array('control_group_include' => array()),

				// Control group: Range
				'autocomplete_range' => array('control_group_include' => array()),

				// Control group: Color
				'autocomplete_color' => array('control_group_include' => array())
			);

			foreach($autocomplete_control_groups as $id => $autocomplete_control_group) {

				$$id = array();

				$control_group_exclude = isset($autocomplete_control_group['control_group_exclude']) ? $autocomplete_control_group['control_group_exclude'] : false;
				$control_group_include = isset($autocomplete_control_group['control_group_include']) ? $autocomplete_control_group['control_group_include'] : false;

				foreach($autocomplete_options as $autocomplete_option) {

					$control_group = isset($autocomplete_option['control_group']) ? $autocomplete_option['control_group'] : false;

					if($control_group !== false) {

						// If control group is excluded, skip this option
						if(
							($control_group_exclude !== false) &&
							in_array($control_group, $control_group_exclude)
						) {
							continue;
						}

						// If control group is included, do not skip this option
						if(
							($control_group_include !== false) &&
							!in_array($control_group, $control_group_include)
						) {
							continue;
						}
					}

					array_push($$id, array('value' => $autocomplete_option['value'], 'text' => $autocomplete_option['value']));
				}
			}

			$meta_keys = array(

				// Forms

				// Should tabs be remembered?
				'cookie_tab_index' => array(

					'label'		=>	__('Remember Last Tab Clicked', 'ws-form'),
					'type'		=>	'checkbox',
					'help'		=>	__('Should the last tab clicked be remembered?', 'ws-form'),
					'default'	=>	true
				),

				'tab_validation' => array(

					'label'		=>	__('Tab Validation', 'ws-form'),
					'type'		=>	'checkbox',
					'help'		=>	__('Prevent the user from advancing to the next tab until the current tab is validated.', 'ws-form'),
					'default'	=>	false
				),

				'tab_validation_show' => array(

					'label'		=>	__('Show Invalid Fields', 'ws-form'),
					'type'		=>	'checkbox',
					'help'		=>	__('If a tab contains invalid fields and the user attempts to progress to the next tab, show invalid feedback.', 'ws-form'),
					'default'	=>	false,
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'tab_validation',
							'meta_value'	=>	'on'
						)
					)
				),

				'tabs_hide' => array(

					'label'		=>	__('Hide Tabs', 'ws-form'),
					'type'		=>	'checkbox',
					'help'		=>	__('Hide the tabs but retain tab functionality.', 'ws-form'),
					'default'	=>	false
				),

				// Add HTML to required labels
				'label_required' =>	array(

					'label'			=>	__("Show Required HTML", 'ws-form'),
					'type'			=>	'checkbox',
					'default'		=>	true,
					'help'			=>	__("Should the required HTML (e.g. '*') be added to labels if a field is required?", 'ws-form')
				),

				// Add HTML to required labels
				'label_mask_required' => array(

					'label'			=>	__("Custom Required HTML", 'ws-form'),
					'type'			=>	'text',
					'default'		=>	'',
					'help'			=>	__('Example: &apos; &lt;small&gt;Required&lt;/small&gt;&apos;.', 'ws-form'),
					'select_list'				=>	array(

						array('text' => sprintf('&lt;small&gt;%s&lt;/small&gt;', __('Required', 'ws-form')), 'value' => sprintf(' <small>%s</small>', __('Required', 'ws-form')))
					),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'label_required',
							'meta_value'	=>	'on'
						)
					)
				),

				// Hidden
				'hidden' =>	array(

					'label'						=>	__('Hidden', 'ws-form'),
					'mask'						=>	'data-hidden',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				'hidden_section' => array(

					'label'						=>	__('Hidden', 'ws-form'),
					'mask'						=>	'data-hidden',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// Fields
				// reCAPTCHA
				'recaptcha' => array(

					'label'						=>	__('reCAPTCHA', 'ws-form'),
					'type'						=>	'recaptcha',
					'dummy'						=>	true
				),

				// hCaptcha
				'hcaptcha' => array(

					'label'						=>	__('hCaptcha', 'ws-form'),
					'type'						=>	'hcaptcha',
					'dummy'						=>	true
				),

				// Turnstile
				'turnstile' => array(

					'label'						=>	__('Turnstile', 'ws-form'),
					'type'						=>	'turnstile',
					'dummy'						=>	true
				),

				// Breakpoint sizes grid
				'breakpoint_sizes' => array(

					'label'						=>	__('Breakpoint Sizes', 'ws-form'),
					'type'						=>	'breakpoint_sizes',
					'dummy'						=>	true,
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'invisible'
						)
					)
				),

				// Spam Protection - WS Form
				'antispam' => array(

					'label'						=>	__('Enabled', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('WS Form Anti-Spam System.', 'ws-form'),
				),

				// Spam Protection - Honeypot
				'honeypot' => array(

					'label'						=>	__('Enabled', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Adds a hidden field to fool spammers.', 'ws-form'),
				),

				// Spam Protection - Threshold
				'spam_threshold' => array(

					'label'						=>	__('Spam Threshold', 'ws-form'),
					'type'						=>	'range',
					'default'					=>	50,
					'min'						=>	0,
					'max'						=>	100,
					'help'						=>	__('If your form is configured to check for spam (e.g. Human Presence, Akismet or reCAPTCHA), each submission will be given a score between 0 (Not spam) and 100 (Blatant spam). Use this setting to determine the minimum score that will move a submission into the spam folder.', 'ws-form'),
				),

				// Duplicate Protection - Lock submit
				'submit_lock' => array(

					'label'						=>	__('Lock Save &amp; Submit Buttons', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('Lock save and submit buttons when form is saved or submitted so that they cannot be double clicked.', 'ws-form')
				),

				// Duplicate Protection - Lock submit
				'submit_unlock' => array(

					'label'						=>	__('Unlock Save &amp; Submit Buttons', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('Unlock save and submit buttons after form is saved or submitted.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'submit_lock',
							'meta_value'		=>	'on'
						)
					)
				),

				// Legal - Source
				'legal_source' => array(

					'label'						=>	__('Source', 'ws-form'),
					'type'						=>	'select',
					'mask'						=>	'data-wsf-legal-source="#value"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'termageddon',
					'options'					=>	array(

						array('value' => 'termageddon', 'text' => __('Termageddon', 'ws-form')),
						array('value' => '', 'text' => __('Own Copy', 'ws-form'))
					)
				),

				// Legal - Termageddon - Key
				'legal_termageddon_intro' => array(

					'type'						=>	'html',
					'html'						=>	sprintf('<a href="https://app.termageddon.com?fp_ref=westguard" target="_blank"><img src="%s/includes/third-party/termageddon/images/logo.gif" width="150" height="22" alt="Termageddon" title="Termageddon" /></a><div class="wsf-helper">%s</div>',

						WS_FORM_PLUGIN_DIR_URL,

						sprintf(

							/* translators: %s = WS Form */
							__('Termageddon is a third party service that generates policies for U.S. websites and apps and updates them whenever the laws change. %s has no control over and accepts no liability in respect of this service and content.', 'ws-form'),

							WS_FORM_NAME_GENERIC
						)
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'legal_source',
							'meta_value'		=>	'termageddon'
						)
					)
				),

				// Legal - Termageddon - Key
				'legal_termageddon_key' => array(

					'label'						=>	__('Key', 'ws-form'),
					'type'						=>	'text',
					'mask'						=>	'data-wsf-termageddon-key="#value"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'',
					'help'						=>	__('Need a key? <a href="https://app.termageddon.com?fp_ref=westguard" target="_blank">Register</a>'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'legal_source',
							'meta_value'		=>	'termageddon'
						)
					)
				),

				// Legal - Termageddon - Hide title
				'legal_termageddon_hide_title' => array(

					'label'						=>	__('Hide Title', 'ws-form'),
					'type'						=>	'checkbox',
					'mask'						=>	'data-wsf-termageddon-extra="no-title=true"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'legal_source',
							'meta_value'		=>	'termageddon'
						)
					)
				),

				// Legal - Own copy
				'legal_text_editor'	 => array(

					'label'						=>	__('Legal Copy', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Enter the legal copy you would like to display.', 'ws-form'),
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'legal_source',
							'meta_value'		=>	''
						)
					),
					'key'						=>	'text_editor'
				),

				// Legal - Style - Height
				'legal_style_height' => array(

					'label'						=>	__('Height (pixels)', 'ws-form'),
					'type'						=>	'number',
					'mask'						=>	'style="height:#valuepx;overflow-y:scroll;"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'200',
					'help'						=>	__('Setting this to blank will remove the height restriction.', 'ws-form')
				),

				// Analytics - Google
				'analytics_google' => array(

					'label'						=>	__('Google Analytics Event Tracking', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				// Analytics - Google - Form events
				'analytics_google_event_form' => array(

					'label'						=>	__('Fire Form Events', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'analytics_google',
							'meta_value'	=>	'on'
						)
					),
					'indent'					=>	true
				),

				// Analytics - Google - Tab events
				'analytics_google_event_tab' => array(

					'label'						=>	__('Fire Tab Events', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'analytics_google',
							'meta_value'	=>	'on'
						)
					),
					'indent'					=>	true
				),

				// Analytics - Google - Field events
				'analytics_google_event_field' => array(

					'label'						=>	__('Fire Field Events', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'analytics_google',
							'meta_value'	=>	'on'
						)
					),
					'indent'					=>	true
				),

				// Tracking - Remote IP address
				'tracking_remote_ip' => array(

					'label'						=>	__('Remote IP Address', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Users remote IP address.', 'ws-form')
				),

				// Tracking - Geo Location
				'tracking_geo_location' => array(

					'label'						=>	__('Geographical Location (Browser)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Latitude & longitude (User may be prompted to grant you permissions to this information).', 'ws-form')
				),

				// Tracking - Referrer
				'tracking_referrer' => array(

					'label'						=>	__('Referrer', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Referring page.', 'ws-form')
				),

				// Tracking - OS
				'tracking_os' => array(

					'label'						=>	__('Operating System', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Users operating system.', 'ws-form')
				),

				// Tracking - Agent
				'tracking_agent' => array(

					'label'						=>	__('Agent', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Users web browser type.', 'ws-form')
				),

				// Tracking - Hostname
				'tracking_host' => array(

					'label'						=>	__('Hostname', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Server hostname.', 'ws-form')
				),

				// Tracking - Pathname
				'tracking_pathname' => array(

					'label'						=>	__('Pathname', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Pathname of the URL.', 'ws-form')
				),

				// Tracking - Query String
				'tracking_query_string' => array(

					'label'						=>	__('Query String', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Query string of the URL.', 'ws-form')
				),

				// Tracking - UTM - Campaign source
				'tracking_utm_source' => array(

					'label'						=>	__('UTM Source', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Campaign source (e.g. website name).', 'ws-form')
				),

				// Tracking - UTM - Campaign medium
				'tracking_utm_medium' => array(

					'label'						=>	__('UTM Medium', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Campaign medium (e.g. email).', 'ws-form')
				),

				// Tracking - UTM - Campaign name
				'tracking_utm_campaign' => array(

					'label'						=>	__('UTM Campaign', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Campaign name.', 'ws-form')
				),

				// Tracking - UTM - Campaign term
				'tracking_utm_term' => array(

					'label'						=>	__('UTM Term', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Campaign term (e.g. keyword).', 'ws-form')
				),

				// Tracking - UTM - Campaign content
				'tracking_utm_content' => array(

					'label'						=>	__('UTM Content', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Campaign content (e.g. text link).', 'ws-form')
				),

				// Tracking - IP Lookup - City
				'tracking_ip_lookup_city' => array(

					'label'						=>	__('City (By IP)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Attempt to get the city from the user\'s IP address.', 'ws-form')
				),

				// Tracking - IP Lookup - Region
				'tracking_ip_lookup_region' => array(

					'label'						=>	__('Region (By IP)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Attempt to get the region from the user\'s IP address.', 'ws-form')
				),

				// Tracking - IP Lookup - Country
				'tracking_ip_lookup_country' => array(

					'label'						=>	__('Country (By IP)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Attempt to get the country from the user\'s IP address.', 'ws-form')
				),

				// Tracking - IP Lookup - Latitude / Longitude
				'tracking_ip_lookup_latlon' => array(

					'label'						=>	__('Geographical Location (By IP)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Attempt to get the latitude and longitude from the user\'s IP address.', 'ws-form')
				),

				// Tracking - IP Lookup - Country
				'tracking_ip_lookup_time_zone' => array(

					'label'						=>	__('Time Zone (By IP)', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Attempt to get the time zone from the user\'s IP address.', 'ws-form')
				),

				// Tracking - Duration
				'tracking_duration' => array(

					'label'						=>	__('Duration', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Track the time it takes for users to complete a form.', 'ws-form')
				),

				// Conversational
				'conversational' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('If checked, this form will be made available in a conversational format.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/conversational-forms/'), __('Learn more', 'ws-form'))
				),

				// Conversational - Slug
				'conversational_slug' => array(

					'label'						=>	__('URL Slug', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	'wsf-conversational-form-#form_id',
					'help'						=>	__('The last part of the URL.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Preview
				'conversational_preview' => array(

					'label'						=>	__('Preview', 'ws-form'),
					'type'						=>	'conversational_preview',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Customize
				'conversational_customize' => array(

					'label'						=>	__('Customize', 'ws-form'),
					'type'						=>	'conversational_customize',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - View
				'conversational_view' => array(

					'type'						=>	'conversational_view',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Navigation - Show
				'conversational_nav' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('Show the navigation bar at the bottom of the page.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Navigation - Progress Help 
				'conversational_nav_progress_help' => array(

					'label'						=>	__('Progress Help', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'#progress_percent',
					'help'						=>	__('Help text to show alongside the navigation progress bar. You can use #progress_percent to inject the current progress percentage.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational_nav',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Scroll - Duration
				'conversational_scroll_duration' => array(

					'label'						=>	__('Scroll Duration (ms)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'300',
					'help'						=>	__('Duration in milliseconds for scrolling between sections.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),

				// Conversational - Full Height Section
				'conversational_full_height_section' => array(

					'label'						=>	__('Full Height (Conversational)', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, this section will appear full height on a conversation form.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'type'				=>	'object_meta_value_form',
							'meta_key'			=>	'conversational',
							'meta_value'		=>	'on'
						)
					)
				),
				// Spacer - Style - Height
				'spacer_style_height' => array(

					'label'						=>	__('Height (pixels)', 'ws-form'),
					'type'						=>	'number',
					'mask'						=>	'style="width:100%;height:#valuepx;"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'60',
					'help'						=>	__('If blank, spacer will have no height.', 'ws-form')
				),

				// Focus on invalid fields
				'invalid_field_focus' => array(

					'label'						=>	__('Focus Invalid Fields', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('On form submit, should the first invalid field be focussed on?', 'ws-form')
				),
				// Submission limit
				'submit_limit' => array(

					'label'						=>	__('Limit by Submission Count', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Limit number of submissions for this form.', 'ws-form')
				),

				'submit_limit_count' => array(

					'label'						=>	__('Maximum Count', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	1,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'submit_limit',
							'meta_value'		=>	'on'
						)
					)
				),

				'submit_limit_period' => array(

					'label'						=>	__('Duration', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('All Time', 'ws-form')),
						array('value' => 'hour', 'text' => __('Per Hour', 'ws-form')),
						array('value' => 'day', 'text' => __('Per Day', 'ws-form')),
						array('value' => 'week', 'text' => __('Per Week', 'ws-form')),
						array('value' => 'month', 'text' => __('Per Month', 'ws-form')),
						array('value' => 'year', 'text' => __('Per Year', 'ws-form'))
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'submit_limit',
							'meta_value'		=>	'on'
						)
					)
				),

				'submit_limit_message' => array(

					'label'						=>	__('Limit Reached Message', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Enter the message you would like to show if the submisson limit is reached. Leave blank to hide form.', 'ws-form'),
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'submit_limit',
							'meta_value'		=>	'on'
						)
					)
				),

				'submit_limit_message_type' => array(

					'label'						=>	__('Message Style', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'success', 'text' => __('Success', 'ws-form')),
						array('value' => 'information', 'text' => __('Information', 'ws-form')),
						array('value' => 'warning', 'text' => __('Warning', 'ws-form')),
						array('value' => 'danger', 'text' => __('Danger', 'ws-form'))
					),
					'default'					=>	'information',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'submit_limit',
							'meta_value'		=>	'on'
						)
					)
				),

				// Form scheduling
				'schedule_start' => array(

					'label'						=>	__('Schedule Start', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Schedule a start date/time for the form.', 'ws-form')
				),

				'schedule_start_datetime' => array(

					'label'						=>	__('Start Date/Time', 'ws-form'),
					'type'						=>	'datetime',
					'default'					=>	'',
					'help'						=>	__('Date/time form is scheduled to start.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_start',
							'meta_value'		=>	'on'
						)
					)
				),

				'schedule_start_message' => array(

					'label'						=>	__('Before Message', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Message shown before the form start date/time. Leave blank to hide form.', 'ws-form'),
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_start',
							'meta_value'		=>	'on'
						)
					)
				),

				'schedule_start_message_type' => array(

					'label'						=>	__('Before Message Style', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'success', 'text' => __('Success', 'ws-form')),
						array('value' => 'information', 'text' => __('Information', 'ws-form')),
						array('value' => 'warning', 'text' => __('Warning', 'ws-form')),
						array('value' => 'danger', 'text' => __('Danger', 'ws-form'))
					),
					'default'					=>	'information',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_start',
							'meta_value'		=>	'on'
						)
					)
				),

				'schedule_end' => array(

					'label'						=>	__('Schedule End', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Schedule an end date/time for the form.', 'ws-form')
				),

				'schedule_end_datetime' => array(

					'label'						=>	__('End Date/Time', 'ws-form'),
					'type'						=>	'datetime',
					'default'					=>	'',
					'help'						=>	__('Date/time form is scheduled to end.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_end',
							'meta_value'		=>	'on'
						)
					)
				),

				'schedule_end_message' => array(

					'label'						=>	__('After Message', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Message shown after the form end date/time. Leave blank to hide form.', 'ws-form'),
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_end',
							'meta_value'		=>	'on'
						)
					)
				),

				'schedule_end_message_type' => array(

					'label'						=>	__('After Message Style', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'success', 'text' => __('Success', 'ws-form')),
						array('value' => 'information', 'text' => __('Information', 'ws-form')),
						array('value' => 'warning', 'text' => __('Warning', 'ws-form')),
						array('value' => 'danger', 'text' => __('Danger', 'ws-form'))
					),
					'default'					=>	'information',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'schedule_end',
							'meta_value'		=>	'on'
						)
					)
				),

				// User limits
				'user_limit_logged_in' => array(

					'label'						=>	__('User Status', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Any', 'ws-form')),
						array('value' => 'on', 'text' => __('Is Logged In', 'ws-form')),
						array('value' => 'out', 'text' => __('Is Logged Out', 'ws-form')),
						array('value' => 'role_capability', 'text' => __('Has User Role or Capability', 'ws-form'))
					),
					'help'						=>	__('Only show the form under certain user conditions.', 'ws-form')
				),

				'user_limit_logged_in_message' => array(

					'label'						=>	__('Message', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Message shown if the user does not meet the user status condition. Leave blank to hide form.', 'ws-form'),
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'user_limit_logged_in',
							'meta_value'		=>	''
						)
					)
				),

				'user_limit_logged_in_message_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'success', 'text' => __('Success', 'ws-form')),
						array('value' => 'information', 'text' => __('Information', 'ws-form')),
						array('value' => 'warning', 'text' => __('Warning', 'ws-form')),
						array('value' => 'danger', 'text' => __('Danger', 'ws-form'))
					),
					'default'					=>	'danger',
					'help'						=>	__('Style of message to use', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'user_limit_logged_in',
							'meta_value'		=>	''
						)
					)
				),
				// Submit on enter
				'submit_on_enter' => array(

					'label'						=>	__('Enable Form Submit On Enter', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Allow the form to be submitted if someone types Enter/Return. Not advised for e-commerce forms.', 'ws-form')
				),

				// Reload on submit
				'submit_reload' => array(

					'label'						=>	__('Reset Form After Submit', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('Should the form be reset to its default state after it is submitted?', 'ws-form')
				),

				// Form action
				'form_action' => array(

					'label'						=>	__('Custom Form Action', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',

					/* translators: %s = WS Form */
					'help'						=>	sprintf(__('Enter a custom action for this form. Leave blank to use %s (Recommended).', 'ws-form'), 'ws-form')
				),

				// Show errors on submit
				'submit_show_errors' => array(

					'label'						=>	__('Show Server Side Error Messages', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',

					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If a server side error occurs when a form is submitted, should %s show those as form error messages?', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				// Error - Type
				'error_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Style of message to use', 'ws-form'),
					'options'					=>	array(

						array('value' => 'success', 'text' => __('Success', 'ws-form')),
						array('value' => 'information', 'text' => __('Information', 'ws-form')),
						array('value' => 'warning', 'text' => __('Warning', 'ws-form')),
						array('value' => 'danger', 'text' => __('Danger', 'ws-form')),
						array('value' => 'none', 'text' => __('None', 'ws-form'))
					),
					'default'					=>	'danger'
				),

				// Error - Method
				'error_method' => array(

					'label'						=>	__('Position', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Where should the message be added?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'before', 'text' => __('Before Form', 'ws-form')),
						array('value' => 'after', 'text' => __('After Form', 'ws-form'))
					),
					'default'					=>	'after'
				),

				// Error - Form - Clear other messages
				'error_clear' => array(

					'label'						=>	__('Clear Other Messages', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Clear any other messages when shown?', 'ws-form'),
					'default'					=>	'on'
				),

				// Error - Form - Scroll to top
				'error_scroll_top' => array(

					'label'						=>	__('Scroll To Top', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'instant', 'text' => __('Instant', 'ws-form')),
						array('value' => 'smooth', 'text' => __('Smooth', 'ws-form'))
					)
				),

				// Error - Scroll Top - Offset
				'error_scroll_top_offset' => array(

					'label'						=>	__('Scroll Offset (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'0',
					'help'						=>	__('Number of pixels to offset the final scroll position by. Useful for sticky headers, e.g. if your header is 100 pixels tall, enter 100 into this setting.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'error_scroll_top',
							'meta_value'		=>	''
						)
					)
				),

				// Error - Scroll Top - Duration
				'error_scroll_top_duration'	=> array(

					'label'						=>	__('Scroll Duration (ms)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'400',
					'help'						=>	__('Duration of the smooth scroll in ms.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'error_scroll_top',
							'meta_value'		=>	'smooth'
						)
					)
				),

				// Error - Form - Hide
				'error_form_hide' => array(

					'label'						=>	__('Hide Form When Shown', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Hide form when message shown?', 'ws-form'),
					'default'					=>	''
				),

				// Duration
				'error_duration' => array(

					'label'						=>	__('Show Duration (ms)', 'ws-form'),
					'type'						=>	'number',
					'help'						=>	__('Duration in milliseconds to show message.', 'ws-form'),
					'default'					=>	''
				),

				// Error - Message - Hide
				'error_message_hide' => array(

					'label'						=>	__('Hide Message After Duration', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Hide message after show duration finishes?', 'ws-form'),
					'default'					=>	'on',
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'error_duration',
							'meta_value'	=>	''
						)
					)
				),

				// Error - Form - Show
				'error_form_show' => array(

					'label'						=>	__('Show Form After Duration', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Show form after duration finishes?', 'ws-form'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'error_form_hide',
							'meta_value'	=>	'on',
							'logic_previous'	=>	'&&'
						),

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'error_duration',
							'meta_value'	=>	'',
							'logic_previous'	=>	'&&'
						)
					)
				),

				// Render label checkbox (On by default)
				'label_render' => array(

					'label'						=>	__('Show Label', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on'
				),

				// Render label checkbox (Off by default)
				'label_render_off' => array(

					'label'						=>	__('Show Label', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'key'						=>	'label_render'
				),

				// Label position (Form)
				'label_position_form' => array(

					'label'						=>	__('Default Label Position', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the default position of field labels.', 'ws-form'),
					'options'					=>	$label_position,
					'options_framework_filter'	=>	'label_positions',
					'default'					=>	'top'
				),

				// Label position
				'label_position' => array(

					'label'						=>	__('Label Position', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the position of the field label.', 'ws-form'),
					'options'					=>	$label_position,
					'options_default'			=>	'label_position_form',
					'options_framework_filter'	=>	'label_positions',
					'default'					=>	'default',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'label_render',
							'meta_value'		=>	'on'
						)
					)
				),

				// Label column width
				'label_column_width_form' => array(

					'label'						=>	__('Default Label Width (Columns)', 'ws-form'),
					'type'						=>	'select_number',
					'default'					=>	3,
					'minimum'					=>	1,
					'maximum'					=>	'framework_column_count',
					'help'						=>	__('Column width of labels if positioned left or right.', 'ws-form')
				),

				// Label column width
				'label_column_width' => array(

					'label'						=>	__('Label Width (Columns)', 'ws-form'),
					'type'						=>	'select_number',
					'options_default'			=>	'label_column_width_form',
					'default'					=>	'default',
					'minimum'					=>	1,
					'maximum'					=>	'framework_column_count',
					'help'						=>	__('Column width of label.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'label_position',
							'meta_value'		=>	'left'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'label_position',
							'meta_value'		=>	'right',
							'logic_previous'	=>	'||'
						)
					)
				),

				// reCAPTCHA - Site key
				'recaptcha_site_key' => array(

					'label'						=>	__('Site Key', 'ws-form'),
					'mask'						=>	'data-site-key="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'default_on_clone'			=>	true,
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('reCAPTCHA site key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/recaptcha/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// reCAPTCHA - Secret key
				'recaptcha_secret_key' => array(

					'label'						=>	__('Secret Key', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('reCAPTCHA secret key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/recaptcha/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'default_on_clone'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// reCAPTCHA - reCAPTCHA type
				'recaptcha_recaptcha_type' => array(

					'label'						=>	__('reCAPTCHA Type', 'ws-form'),
					'mask'						=>	'data-recaptcha-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the reCAPTCHA version your site key relates to.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'v2_default', 'text' => __('Version 2 - Default', 'ws-form')),
						array('value' => 'v2_invisible', 'text' => __('Version 2 - Invisible', 'ws-form')),
						array('value' => 'v3_default', 'text' => __('Version 3', 'ws-form')),
					),
					'default'					=>	'v2_default'
				),

				// reCAPTCHA - Badge
				'recaptcha_badge' => array(

					'label'						=>	__('Badge Position', 'ws-form'),
					'mask'						=>	'data-badge="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Position of the reCAPTCHA badge (Invisible only).', 'ws-form'),
					'options'					=>	array(

						array('value' => 'bottomright', 'text' => __('Bottom Right', 'ws-form')),
						array('value' => 'bottomleft', 'text' => __('Bottom Left', 'ws-form')),
						array('value' => 'inline', 'text' => __('Inline', 'ws-form'))
					),
					'default'					=>	'bottomright',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v2_invisible'
						)
					)
				),

				// reCAPTCHA - Type
				'recaptcha_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'data-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Image or audio?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'image', 'text' => __('Image', 'ws-form')),
						array('value' => 'audio', 'text' => __('Audio', 'ws-form')),
					),
					'default'					=>	'image',
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v3_default'
						)
					)
				),

				// reCAPTCHA - Theme
				'recaptcha_theme' => array(

					'label'						=>	__('Theme', 'ws-form'),
					'mask'						=>	'data-theme="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Light or dark theme?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'light', 'text' => __('Light', 'ws-form')),
						array('value' => 'dark', 'text' => __('Dark', 'ws-form')),
					),
					'default'					=>	'light',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v2_default'
						)
					)
				),

				// reCAPTCHA - Size
				'recaptcha_size' => array(

					'label'						=>	__('Size', 'ws-form'),
					'mask'						=>	'data-size="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Normal or compact size?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'normal', 'text' => __('Normal', 'ws-form')),
						array('value' => 'compact', 'text' => __('Compact', 'ws-form')),
					),
					'default'					=>	'normal',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v2_default'
						)
					)
				),

				// reCAPTCHA - Language (Language Culture Name)
				'recaptcha_language' => array(

					'label'						=>	__('Language', 'ws-form'),
					'mask'						=>	'data-language="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Force the reCAPTCHA to render in a specific language?', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => 'Auto Detect'),
						array('value' => 'ar', 'text' => 'Arabic'),
						array('value' => 'af', 'text' => 'Afrikaans'),
						array('value' => 'am', 'text' => 'Amharic'),
						array('value' => 'hy', 'text' => 'Armenian'),
						array('value' => 'az', 'text' => 'Azerbaijani'),
						array('value' => 'eu', 'text' => 'Basque'),
						array('value' => 'bn', 'text' => 'Bengali'),
						array('value' => 'bg', 'text' => 'Bulgarian'),
						array('value' => 'ca', 'text' => 'Catalan'),
						array('value' => 'zh-HK', 'text' => 'Chinese (Hong Kong)'),
						array('value' => 'zh-CN', 'text' => 'Chinese (Simplified)'),
						array('value' => 'zh-TW', 'text' => 'Chinese (Traditional)'),
						array('value' => 'hr', 'text' => 'Croatian'),
						array('value' => 'cs', 'text' => 'Czech'),
						array('value' => 'da', 'text' => 'Danish'),
						array('value' => 'nl', 'text' => 'Dutch'),
						array('value' => 'en-GB', 'text' => 'English (UK)'),
						array('value' => 'en', 'text' => 'English (US)'),
						array('value' => 'et', 'text' => 'Estonian'),
						array('value' => 'fil', 'text' => 'Filipino'),
						array('value' => 'fi', 'text' => 'Finnish'),
						array('value' => 'fr', 'text' => 'French'),
						array('value' => 'fr-CA', 'text' => 'French (Canadian)'),
						array('value' => 'gl', 'text' => 'Galician'),
						array('value' => 'ka', 'text' => 'Georgian'),
						array('value' => 'de', 'text' => 'German'),
						array('value' => 'de-AT', 'text' => 'German (Austria)'),
						array('value' => 'de-CH', 'text' => 'German (Switzerland)'),
						array('value' => 'el', 'text' => 'Greek'),
						array('value' => 'gu', 'text' => 'Gujarati'),
						array('value' => 'iw', 'text' => 'Hebrew'),
						array('value' => 'hi', 'text' => 'Hindi'),
						array('value' => 'hu', 'text' => 'Hungarain'),
						array('value' => 'is', 'text' => 'Icelandic'),
						array('value' => 'id', 'text' => 'Indonesian'),
						array('value' => 'it', 'text' => 'Italian'),
						array('value' => 'ja', 'text' => 'Japanese'),
						array('value' => 'kn', 'text' => 'Kannada'),
						array('value' => 'ko', 'text' => 'Korean'),
						array('value' => 'lo', 'text' => 'Laothian'),
						array('value' => 'lv', 'text' => 'Latvian'),
						array('value' => 'lt', 'text' => 'Lithuanian'),
						array('value' => 'ms', 'text' => 'Malay'),
						array('value' => 'ml', 'text' => 'Malayalam'),
						array('value' => 'mr', 'text' => 'Marathi'),
						array('value' => 'mn', 'text' => 'Mongolian'),
						array('value' => 'no', 'text' => 'Norwegian'),
						array('value' => 'fa', 'text' => 'Persian'),
						array('value' => 'pl', 'text' => 'Polish'),
						array('value' => 'pt', 'text' => 'Portuguese'),
						array('value' => 'pt-BR', 'text' => 'Portuguese (Brazil)'),
						array('value' => 'pt-PT', 'text' => 'Portuguese (Portugal)'),
						array('value' => 'ro', 'text' => 'Romanian'),
						array('value' => 'ru', 'text' => 'Russian'),
						array('value' => 'sr', 'text' => 'Serbian'),
						array('value' => 'si', 'text' => 'Sinhalese'),
						array('value' => 'sk', 'text' => 'Slovak'),
						array('value' => 'sl', 'text' => 'Slovenian'),
						array('value' => 'es', 'text' => 'Spanish'),
						array('value' => 'es-419', 'text' => 'Spanish (Latin America)'),
						array('value' => 'sw', 'text' => 'Swahili'),
						array('value' => 'sv', 'text' => 'Swedish'),
						array('value' => 'ta', 'text' => 'Tamil'),
						array('value' => 'te', 'text' => 'Telugu'),
						array('value' => 'th', 'text' => 'Thai'),
						array('value' => 'tr', 'text' => 'Turkish'),
						array('value' => 'uk', 'text' => 'Ukrainian'),
						array('value' => 'ur', 'text' => 'Urdu'),
						array('value' => 'vi', 'text' => 'Vietnamese'),
						array('value' => 'zu', 'text' => 'Zul')
					),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v3_default'
						)
					)
				),

				// reCAPTCHA - Action
				'recaptcha_action' => array(

					'label'						=>	__('Action', 'ws-form'),
					'mask'						=>	'data-recaptcha-action="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Actions run on form load. Actions may only contain alphanumeric characters and slashes, and must not be user-specific.', 'ws-form'),
					'default'					=>	'ws_form/#form_id/load',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'recaptcha_recaptcha_type',
							'meta_value'	=>	'v3_default'
						)
					)
				),

				// hCaptcha - Site key
				'hcaptcha_site_key' => array(

					'label'						=>	__('Site Key', 'ws-form'),
					'mask'						=>	'data-site-key="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'default_on_clone'			=>	true,
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('hCaptcha site key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/hcaptcha/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// hCaptcha - Secret key
				'hcaptcha_secret_key' => array(

					'label'						=>	__('Secret Key', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'default_on_clone'			=>	true,
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('hCaptcha secret key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/hcaptcha/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// hCaptcha - hCaptcha type
				'hcaptcha_type' => array(

					'label'						=>	__('hCaptcha Type', 'ws-form'),
					'mask'						=>	'data-hcaptcha-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the hCaptcha version your site key relates to.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'default', 'text' => __('Default', 'ws-form')),
						array('value' => 'invisible', 'text' => __('Invisible', 'ws-form'))
					),
					'default'					=>	'default'
				),

				// hCaptcha - Theme
				'hcaptcha_theme' => array(

					'label'						=>	__('Theme', 'ws-form'),
					'mask'						=>	'data-theme="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Light or dark theme?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'light', 'text' => __('Light', 'ws-form')),
						array('value' => 'dark', 'text' => __('Dark', 'ws-form')),
					),
					'default'					=>	'light',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'hcaptcha_type',
							'meta_value'	=>	'default'
						)
					)
				),

				// hCaptcha - Size
				'hcaptcha_size' => array(

					'label'						=>	__('Size', 'ws-form'),
					'mask'						=>	'data-size="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Normal or compact size?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'normal', 'text' => __('Normal', 'ws-form')),
						array('value' => 'compact', 'text' => __('Compact', 'ws-form')),
					),
					'default'					=>	'normal',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'hcaptcha_type',
							'meta_value'	=>	'default'
						)
					)
				),

				// hCaptcha - Language (Language Culture Name)
				'hcaptcha_language' => array(

					'label'						=>	__('Language', 'ws-form'),
					'mask'						=>	'data-language="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Force the hCaptcha to render in a specific language?', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => 'Auto Detect'),
						array('value' => 'ar', 'text' => 'Arabic'),
						array('value' => 'af', 'text' => 'Afrikaans'),
						array('value' => 'am', 'text' => 'Amharic'),
						array('value' => 'hy', 'text' => 'Armenian'),
						array('value' => 'az', 'text' => 'Azerbaijani'),
						array('value' => 'eu', 'text' => 'Basque'),
						array('value' => 'bn', 'text' => 'Bengali'),
						array('value' => 'bg', 'text' => 'Bulgarian'),
						array('value' => 'ca', 'text' => 'Catalan'),
						array('value' => 'zh-HK', 'text' => 'Chinese (Hong Kong)'),
						array('value' => 'zh-CN', 'text' => 'Chinese (Simplified)'),
						array('value' => 'zh-TW', 'text' => 'Chinese (Traditional)'),
						array('value' => 'hr', 'text' => 'Croatian'),
						array('value' => 'cs', 'text' => 'Czech'),
						array('value' => 'da', 'text' => 'Danish'),
						array('value' => 'nl', 'text' => 'Dutch'),
						array('value' => 'en-GB', 'text' => 'English (UK)'),
						array('value' => 'en', 'text' => 'English (US)'),
						array('value' => 'et', 'text' => 'Estonian'),
						array('value' => 'fil', 'text' => 'Filipino'),
						array('value' => 'fi', 'text' => 'Finnish'),
						array('value' => 'fr', 'text' => 'French'),
						array('value' => 'fr-CA', 'text' => 'French (Canadian)'),
						array('value' => 'gl', 'text' => 'Galician'),
						array('value' => 'ka', 'text' => 'Georgian'),
						array('value' => 'de', 'text' => 'German'),
						array('value' => 'de-AT', 'text' => 'German (Austria)'),
						array('value' => 'de-CH', 'text' => 'German (Switzerland)'),
						array('value' => 'el', 'text' => 'Greek'),
						array('value' => 'gu', 'text' => 'Gujarati'),
						array('value' => 'iw', 'text' => 'Hebrew'),
						array('value' => 'hi', 'text' => 'Hindi'),
						array('value' => 'hu', 'text' => 'Hungarain'),
						array('value' => 'is', 'text' => 'Icelandic'),
						array('value' => 'id', 'text' => 'Indonesian'),
						array('value' => 'it', 'text' => 'Italian'),
						array('value' => 'ja', 'text' => 'Japanese'),
						array('value' => 'kn', 'text' => 'Kannada'),
						array('value' => 'ko', 'text' => 'Korean'),
						array('value' => 'lo', 'text' => 'Laothian'),
						array('value' => 'lv', 'text' => 'Latvian'),
						array('value' => 'lt', 'text' => 'Lithuanian'),
						array('value' => 'ms', 'text' => 'Malay'),
						array('value' => 'ml', 'text' => 'Malayalam'),
						array('value' => 'mr', 'text' => 'Marathi'),
						array('value' => 'mn', 'text' => 'Mongolian'),
						array('value' => 'no', 'text' => 'Norwegian'),
						array('value' => 'fa', 'text' => 'Persian'),
						array('value' => 'pl', 'text' => 'Polish'),
						array('value' => 'pt', 'text' => 'Portuguese'),
						array('value' => 'pt-BR', 'text' => 'Portuguese (Brazil)'),
						array('value' => 'pt-PT', 'text' => 'Portuguese (Portugal)'),
						array('value' => 'ro', 'text' => 'Romanian'),
						array('value' => 'ru', 'text' => 'Russian'),
						array('value' => 'sr', 'text' => 'Serbian'),
						array('value' => 'si', 'text' => 'Sinhalese'),
						array('value' => 'sk', 'text' => 'Slovak'),
						array('value' => 'sl', 'text' => 'Slovenian'),
						array('value' => 'es', 'text' => 'Spanish'),
						array('value' => 'es-419', 'text' => 'Spanish (Latin America)'),
						array('value' => 'sw', 'text' => 'Swahili'),
						array('value' => 'sv', 'text' => 'Swedish'),
						array('value' => 'ta', 'text' => 'Tamil'),
						array('value' => 'te', 'text' => 'Telugu'),
						array('value' => 'th', 'text' => 'Thai'),
						array('value' => 'tr', 'text' => 'Turkish'),
						array('value' => 'uk', 'text' => 'Ukrainian'),
						array('value' => 'ur', 'text' => 'Urdu'),
						array('value' => 'vi', 'text' => 'Vietnamese'),
						array('value' => 'zu', 'text' => 'Zul')
					),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'hcaptcha_type',
							'meta_value'	=>	'v3_default'
						)
					)
				),

				// Turnstile - Site key
				'turnstile_site_key' => array(

					'label'						=>	__('Site Key', 'ws-form'),
					'mask'						=>	'data-site-key="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'default_on_clone'			=>	true,
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('Turnstile site key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/turnstile/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// Turnstile - Secret key
				'turnstile_secret_key' => array(

					'label'						=>	__('Secret Key', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'default_on_clone'			=>	true,
					'help'						=>	sprintf('%s <a href="%s" target="_blank">%s</a>', __('Turnstile secret key.', 'ws-form'), WS_Form_Common::get_plugin_website_url('/knowledgebase/turnstile/'), __('Learn more', 'ws-form')),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				// Turnstile - Theme
				'turnstile_theme' => array(

					'label'						=>	__('Theme', 'ws-form'),
					'mask'						=>	'data-theme="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Auto, light or dark theme?', 'ws-form'),
					'options'					=>	array(

						array('value' => 'auto', 'text' => __('Auto', 'ws-form')),
						array('value' => 'light', 'text' => __('Light', 'ws-form')),
						array('value' => 'dark', 'text' => __('Dark', 'ws-form')),
					),
					'default'					=>	'auto'
				),
				// Signature - Dot Size
				'signature_dot_size' => array(

					'label'						=>	__('Pen Size', 'ws-form'),
					'mask'						=>	'data-dot-size="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Radius of a single dot.', 'ws-form'),
					'default'					=>	'2'
				),

				// Signature - Pen Color
				'signature_pen_color' => array(

					'label'						=>	__('Pen Color', 'ws-form'),
					'mask'						=>	'data-pen-color="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'help'						=>	__('Color used to draw the lines.', 'ws-form'),
					'default'					=>	WS_Form_Common::option_get('skin_color_default')
				),

				// Signature - Background Color
				'signature_background_color' => array(

					'label'						=>	__('Background Color', 'ws-form'),
					'mask'						=>	'data-background-color="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'help'						=>	__('Color used for background (JPG only).', 'ws-form'),
					'default'					=>	WS_Form_Common::option_get('skin_color_default_inverted'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'signature_mime',
							'meta_value'	=>	'image/jpeg'
						)
					)
				),

				// Signature - Type
				'signature_mime' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'data-mime="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Output format of signature image.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('PNG (Transparent)', 'ws-form')),
						array('value' => 'image/jpeg', 'text' => __('JPG', 'ws-form')),
						array('value' => 'image/svg+xml', 'text' => __('SVG', 'ws-form')),
					),
					'default'					=>	''
				),

				// Signature - Height
				'signature_height' => array(

					'label'						=>	__('Height', 'ws-form'),
					'mask'						=>	'data-height="#value"',
					'mask_disregard_on_empty'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Height of signature canvas.', 'ws-form'),
					'default'					=>	'76px'
				),

				// Signature - Crop
				'signature_crop' => array(

					'label'						=>	__('Crop', 'ws-form'),
					'mask'						=>	'data-crop',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'help'						=>	__('Should the signature be cropped prior to submitting it?', 'ws-form'),
					'default'					=>	'on'
				),

				// Input Type - Date/Time
				'input_type_datetime' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'type="#datetime_type" data-date-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Type of date to display.', 'ws-form'),
					'data_change'				=>	array('event' => 'change', 'action' => 'reload'),

					'options'					=>	array(

						array('value' => 'date', 'text' => __('Date', 'ws-form')),
						array('value' => 'time', 'text' => __('Time', 'ws-form')),
						array('value' => 'datetime-local', 'text' => __('Date/Time', 'ws-form')),
						array('value' => 'week', 'text' => __('Week', 'ws-form')),
						array('value' => 'month', 'text' => __('Month', 'ws-form')),
					),
					'default'					=>	'date',
					'compatibility_id'			=> 'input-datetime'
				),

				// Date format
				'format_date' => array(

					'label'						=>	__('Date Format', 'ws-form'),
					'mask'						=>	'data-date-format="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __(sprintf('Default (%s)', date_i18n(get_option('date_format'))), 'ws-form'))
					),
					'default'					=>	'',
					'help'						=>	__('Format used for selected date.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				// Time format
				'format_time' => array(

					'label'						=>	__('Time Format', 'ws-form'),
					'mask'						=>	'data-time-format="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '', 'text' => __(sprintf('Default (%s)', date_i18n(get_option('time_format'))), 'ws-form'))
					),
					'default'					=>	'',
					'help'						=>	__('Format used for selected time.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'time'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				// Date - Day of week start
				'dow_start' => array(

					'label'						=>	__('First Day of the Week', 'ws-form'),
					'mask'						=>	'data-dow-start="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Choose which day to start the date picker with.', 'ws-form'),
					'options'					=>	array(

						array('value' => '0', 'text' => __('Sunday', 'ws-form')),
						array('value' => '1', 'text' => __('Monday', 'ws-form')),
						array('value' => '2', 'text' => __('Tuesday', 'ws-form')),
						array('value' => '3', 'text' => __('Wednesday', 'ws-form')),
						array('value' => '4', 'text' => __('Thursday', 'ws-form')),
						array('value' => '5', 'text' => __('Friday', 'ws-form')),
						array('value' => '6', 'text' => __('Saturday', 'ws-form')),
					),
					'default'					=> '1'
				),

				// Input Type - Text Area
				'input_type_textarea' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'data-textarea-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Type of text editor to display. If a user has visual editor or syntax highlighting disabled, those editors will not render.', 'ws-form'),
					'data_change'				=>	array('event' => 'change', 'action' => 'reload'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Default', 'ws-form'))
					),
					'default'					=> ''
				),

				// Input Type - Text Area - Toolbar
				'input_type_textarea_toolbar' => array(

					'label'						=>	__('Visual Editor Toolbar', 'ws-form'),
					'mask'						=>	'data-textarea-toolbar="#value"',
					'type'						=>	'select',
					'help'						=>	__('Type of text editor to display.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'full', 'text' => __('Full', 'ws-form')),
						array('value' => 'compact', 'text' => __('Compact', 'ws-form'))
					),
					'default'					=> '',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'input_type_textarea',
							'meta_value'	=>	'tinymce'
						)
					),
				),

				// Progress Data Source
				'progress_source' => array(

					'label'						=>	__('Source', 'ws-form'),
					'mask'						=>	'data-source="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Source of progress data.', 'ws-form'),

					'options'					=>	array(

						array('value' => '', 'text' => __('No source', 'ws-form')),
						array('value' => 'form_progress', 'text' => __('Form Progress', 'ws-form')),
						array('value' => 'tab_progress', 'text' => __('Tab Progress', 'ws-form')),
						array('value' => 'post_progress', 'text' => __('Upload Progress', 'ws-form')),
					),
					'default'					=>	'form_progress'
				),
				'class_field_full_button_remove' => array(

					'label'						=>	__('Remove Full Width Class', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				'class_field_message_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'information',
					'options'					=>	$message_types,
					'help'						=>	__('Style of message to use', 'ws-form')
				),

				'class_field_button_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'default',
					'options'					=>	$button_types,
					'fallback'					=>	'default'
				),

				'class_field_button_type_primary' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'primary',
					'options'					=>	$button_types,
					'key'						=>	'class_field_button_type',
					'fallback'					=>	'primary'
				),

				'class_field_button_type_danger' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'danger',
					'options'					=>	$button_types,
					'key'						=>	'class_field_button_type',
					'fallback'					=>	'danger'
				),

				'class_field_button_type_success' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'success',
					'options'					=>	$button_types,
					'key'						=>	'class_field_button_type',
					'fallback'					=>	'success'
				),

				'class_fill_lower_track' => array(

					'label'						=>	__('Fill Lower Track', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'mask'						=>	'data-fill-lower-track',
					'mask_disregard_on_empty'	=>	true,

					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('%s skin only.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
				),

				'class_single_vertical_align' => array(

					'label'						=>	__('Vertical Alignment', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	$vertical_align
				),

				'class_single_vertical_align_bottom' => array(

					'label'						=>	__('Vertical Alignment', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'bottom',
					'options'					=>	$vertical_align,
					'key'						=>	'class_single_vertical_align',
					'fallback'					=>	''
				),

				// Sets default value attribute (unless saved value exists)
				'default_value' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default value entered in field.', 'ws-form'),
					'select_list'				=>	true,
					'calc'						=>	true
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_number' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default number entered in field.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'calc'						=>	true,
					'calc_for_type'				=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_range' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default value of range slider.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'calc'						=>	true,
					'calc_for_type'				=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_price_range' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'0',
					'help'						=>	__('Default value of price range slider.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'calc'						=>	true,
					'calc_for_type'				=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_color' => array(

					'label'						=>	__('Default Color', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'#000000',
					'help'						=>	__('Default color selected in field.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_datetime' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default date entered in field. If using the jQuery date/time picker (default) then match the chosen date/time format. If using the native browser date/time picker use yyyy-mm-dd format.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_email' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default email entered in field.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_tel' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default phone number entered in field.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_url' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default URL entered in field.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_textarea' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'textarea',
					'default'					=>	'',
					'help'						=>	__('Default value entered in field', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'calc'						=>	true
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_progress' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default value of progress bar.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'calc'						=>	true,
					'calc_for_type'				=>	'text',
					'compatibility_id'			=>	'mdn-html_elements_progress_value'
				),

				// Sets default value attribute (unless saved value exists)
				'default_value_meter' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Default value of meter.', 'ws-form'),
					'key'						=>	'default_value',
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'calc'						=>	true,
					'calc_for_type'				=>	'text',
					'compatibility_id'			=>	'mdn-html_elements_meter_value'
				),

				// Number - No spinner
				'number_no_spinner' => array(

					'label'						=>	__('Remove Arrows/Spinners', 'ws-form'),
					'mask'						=>	'data-wsf-no-spinner',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				// International telephone input
				'intl_tel_input' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'mask'						=>	'data-intl-tel-input',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked the phone field will have an international telephone input added to it.', 'ws-form')
				),

				// International telephone input - Allow dropdown
				'intl_tel_input_allow_dropdown' => array(

					'label'						=>	__('Allow Dropdown', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('If not checked, there is no dropdown arrow, and the selected flag is not clickable.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Show placeholder number
				'intl_tel_input_auto_placeholder' => array(

					'label'						=>	__('Show Placeholder Number', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('If checked, an example placeholder number will be shown. Only shown if placeholder setting is blank.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - National mode
				'intl_tel_input_national_mode' => array(

					'label'						=>	__('National mode', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('If checked, allow users to enter national numbers and not have to think about international dial codes.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Separate dial code
				'intl_tel_input_separate_dial_code' => array(

					'label'						=>	__('Separate Dial Code', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked, display the country dial code next to the selected flag so it is not part of the typed number.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Return format
				'intl_tel_input_format' => array(

					'label'						=>	__('Return Format', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('No Formatting', 'ws-form')),
						array('value' => 'NATIONAL', 'text' => __('National', 'ws-form')),
						array('value' => 'INTERNATIONAL', 'text' => __('International', 'ws-form')),
						array('value' => 'E164', 'text' => __('E164', 'ws-form')),
						array('value' => 'RFC3966', 'text' => __('RFC3966', 'ws-form'))
					),
					'help'						=>	__('Choose which format the phone number will be returned as.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Initial country
				'intl_tel_input_initial_country' => array(

					'label'						=>	__('Initial country', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Not set', 'ws-form')),
						array('value' => 'auto', 'text' => __('Auto (IP Lookup)', 'ws-form'))
					),
					'help'						=>	__('Set the initial country selection.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Counties
				'intl_tel_input_only_countries' => array(

					'label'						=>	__('Countries', 'ws-form'),
					'type'						=>	'repeater',
					'meta_keys'					=>	array(

						'country_alpha_2'
					),
					'meta_keys_unique'			=>	array(

						'country_alpha_2'
					),
					'help'						=>	__('Limit list to these countries.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Counties
				'intl_tel_input_preferred_countries' => array(

					'label'						=>	__('Preferred Countries', 'ws-form'),
					'type'						=>	'repeater',
					'meta_keys'					=>	array(

						'country_alpha_2'
					),
					'meta_keys_unique'			=>	array(

						'country_alpha_2'
					),
					'help'						=>	__('Preferred countries shown at the top of the list.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Invalid label: Invalid number
				'intl_tel_input_label_number' => array(

					'label'						=>	__('Invalid number', 'ws-form'),
					'type'						=>	'test',
					'default'					=>	__('Invalid number', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Invalid label: Invalid country code
				'intl_tel_input_label_country_code' => array(

					'label'						=>	__('Invalid country code', 'ws-form'),
					'type'						=>	'test',
					'default'					=>	__('Invalid country code', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Invalid label: Too short
				'intl_tel_input_label_short' => array(

					'label'						=>	__('Too short', 'ws-form'),
					'type'						=>	'test',
					'default'					=>	__('Too short', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// International telephone input - Invalid label: Too long
				'intl_tel_input_label_long' => array(

					'label'						=>	__('Too long', 'ws-form'),
					'type'						=>	'test',
					'default'					=>	__('Too long', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'intl_tel_input',
							'meta_value'	=>	'on'
						)
					)
				),

				// Autocapitalize
				'autocapitalize' => array(

					'label'						=>	__('Autocapitalize', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => 'off', 'text' => __('Off', 'ws-form')),
						array('value' => '', 'text' => __('On', 'ws-form')),
						array('value' => 'sentences', 'text' => __('Sentences', 'ws-form')),
						array('value' => 'words', 'text' => __('Words', 'ws-form')),
						array('value' => 'characters', 'text' => __('Characters', 'ws-form'))
					),
					'help'						=>	__('Whether and how text input is automatically capitalized as it is entered/edited by the user.', 'ws-form'),
					'compatibility_id'			=>	'mdn-html_global_attributes_autocapitalize'
				),

				// Autocapitalize Off
				'autocapitalize_off' => array(

					'label'						=>	__('Autocapitalize', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Off', 'ws-form')),
						array('value' => 'on', 'text' => __('On', 'ws-form')),
						array('value' => 'sentences', 'text' => __('Sentences', 'ws-form')),
						array('value' => 'words', 'text' => __('Words', 'ws-form')),
						array('value' => 'characters', 'text' => __('Characters', 'ws-form'))
					),
					'key'						=>	'autocapitalize',
					'help'						=>	__('Whether and how text input is automatically capitalized as it is entered/edited by the user.', 'ws-form'),
					'compatibility_id'			=>	'mdn-html_global_attributes_autocapitalize'
				),

				// Orientation
				'orientation' => array(

					'label'						=>	__('Orientation', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Vertical', 'ws-form')),
						array('value' => 'horizontal', 'text' => __('Horizontal', 'ws-form')),
						array('value' => 'grid', 'text' => __('Grid', 'ws-form'))
					),
					'key_legacy'				=>	'class_inline'
				),

				// Orientation
				'file_preview_orientation' => array(

					'label'						=>	__('Orientation', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'horizontal',
					'options'					=>	array(

						array('value' => '', 'text' => __('Vertical', 'ws-form')),
						array('value' => 'horizontal', 'text' => __('Horizontal', 'ws-form')),
						array('value' => 'grid', 'text' => __('Grid', 'ws-form'))
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'file_preview',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	'dropzonejs'
						)
					),
					'key'						=>	'orientation'
				),

				// Orientation sizes grid
				'orientation_breakpoint_sizes' => array(

					'label'						=>	__('Grid Breakpoint Sizes', 'ws-form'),
					'type'						=>	'orientation_breakpoint_sizes',
					'dummy'						=>	true,
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'orientation',
							'meta_value'	=>	'grid'
						)
					)
				),

				// Orientation sizes grid
				'file_preview_orientation_breakpoint_sizes' => array(

					'label'						=>	__('Grid Breakpoint Sizes', 'ws-form'),
					'type'						=>	'orientation_breakpoint_sizes',
					'dummy'						=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'file_preview',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	'dropzonejs'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'==',
							'meta_key'			=>	'orientation',
							'meta_value'		=>	'grid'
						)
					),
					'key'						=>	'orientation_breakpoint_sizes'
				),

				// Form label mask (Allows user to define custom mask)
				'label_mask_form' => array(

					'label'						=>	__('Form', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Example: &apos;&lt;h2&gt;#label&lt;/h2&gt;&apos;.', 'ws-form'),
					'placeholder'				=>	'&lt;h2&gt;#label&lt;/h2&gt'
				),

				// Group label mask (Allows user to define custom mask)
				'label_mask_group' => array(

					'label'						=>	__('Tab', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Example: &apos;&lt;h3&gt;#label&lt;/h3&gt;&apos;.', 'ws-form'),
					'placeholder'				=>	'&lt;h3&gt;#label&lt;/h3&gt'
				),

				// Section label mask (Allows user to define custom mask)
				'label_mask_section' => array(

					'label'						=>	__('Section', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Example: &apos;&lt;legend&gt;#label&lt;/legend&gt;&apos;.', 'ws-form'),
					'placeholder'				=>	'&lt;legend&gt;#label&lt;/legend&gt;'
				),

				// Wrapper classes
				'class_form_wrapper' => array(

					'label'						=>	__('Form Wrapper', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				'class_tabs_wrapper' => array(

					'label'						=>	__('Tabs Wrapper', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				'class_group_wrapper' => array(

					'label'						=>	__('Tab Content Wrapper', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				'class_section_wrapper' => array(

					'label'						=>	__('Section Wrapper', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				'class_field_wrapper' => array(

					'label'						=>	__('Field Wrapper', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				// Classes
				'class_field' => array(

					'label'						=>	__('Field', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Separate each class with spaces.', 'ws-form')
				),

				'contact_first_name' => array(

					'label'						=>	__('First Name', 'ws-form'),
					'type'						=>	'text',
					'required'					=>	true
				),

				'contact_last_name' => array(

					'label'						=>	__('Last Name', 'ws-form'),
					'type'						=>	'text',
					'required'					=>	true
				),

				'contact_email' => array(

					'label'						=>	__('Email', 'ws-form'),
					'type'						=>	'email',
					'required'					=>	true
				),

				'contact_push_form' => array(

					'label'						=>	__('Attach form (Recommended)', 'ws-form'),
					'type'						=>	'checkbox'
				),

				'contact_push_system' => array(

					'label'						=>	sprintf('<a href="%s" target="_blank">%s</a> (%s).', WS_Form_Common::get_admin_url('ws-form-settings', false, 'tab=system'), __('Attach system info', 'ws-form'), __('Recommended', 'ws-form')),
					'type'						=>	'checkbox'
				),

				'contact_inquiry' => array(

					'label'						=>	__('Inquiry', 'ws-form'),
					'type'						=>	'textarea',
					'required'					=>	true
				),

				'contact_support_search_results' => array(

					'type'						=>	'html',
					'html'						=>	''
				),

				'contact_gdpr' => array(

					'label'						=>	sprintf(

						/* translators: %s = WS Form */
						__('I consent to having %s store my submitted information so they can respond to my inquiry.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'type'						=>	'checkbox',
					'required'					=>	true
				),

				'contact_submit' => array(

					'label'						=>	__('Request Support', 'ws-form'),
					'type'						=>	'button',
					'data-action'				=>	'wsf-contact-us',
					'class_field'				=>	'wsf-button-primary'
				),

				'help' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field.', 'ws-form'),
					'select_list'				=>	true
				),

				'help_progress' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. You can use #progress_percent to inject the current progress percentage.', 'ws-form'),
					'default'					=>	'#progress_percent',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_meter' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. You can use #value to inject the current meter value.', 'ws-form'),
					'default'					=>	'#value',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_range' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. You can use #value to inject the current range value.', 'ws-form'),
					'default'					=>	'#value',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_price_range' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. You can use #value to inject the current range value.', 'ws-form'),
					'default'					=>	'#ecommerce_price(#value)',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_count_char' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. Use #character_count to inject the current character count.', 'ws-form'),
					'default'					=>	'',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_count_char_word' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. Use #character_count or #word_count to inject the current character or word count.', 'ws-form'),
					'default'					=>	'',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'help_count_char_word_with_default' => array(

					'label'						=>	__('Help Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Help text to show alongside this field. Use #character_count or #word_count to inject the current character or word count.', 'ws-form'),
					'default'					=>	'#character_count #character_count_label / #word_count #word_count_label',
					'key'						=>	'help',
					'select_list'				=>	true
				),

				'inputmode' => array(

					'label'						=>	__('Virtual Keyboard', 'ws-form'),
					'mask'						=>	'inputmode="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('This setting hints to the browser which type of virtual keyboard to use for mobile devices.', 'ws-form'),
					'default'					=>	'',
					'compatibility_id'			=>	'inputmode',
					'options'					=>	array(

						array('value' => '', 'text' => __('Default', 'ws-form')),
						array('value' => 'decimal', 'text' => __('Decimal', 'ws-form')),
						array('value' => 'email', 'text' => __('Email', 'ws-form')),
						array('value' => 'text', 'text' => __('Text', 'ws-form')),
						array('value' => 'tel', 'text' => __('Telephone', 'ws-form')),
						array('value' => 'search', 'text' => __('Search', 'ws-form')),
						array('value' => 'url', 'text' => __('URL', 'ws-form')),
						array('value' => 'none', 'text' => __('No Virtual Keyboard', 'ws-form'))
					)
				),

				'inputmode_none' => array(

					'label'						=>	__('Disable Virtual Keyboard', 'ws-form'),
					'mask'						=>	'inputmode="none"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'help'						=>	__('If checked the virtual keyboard will be disabled on mobile devices.', 'ws-form'),
					'default'					=>	'',
					'compatibility_id'			=>	'inputmode'
				),

				'html_editor' => array(

					'label'						=>	__('HTML', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'html_editor',
					'default'					=>	'',
					'help'						=>	__('Enter raw HTML to be output at this point on the form.', 'ws-form'),
					'select_list'				=>	true,
					'calc'						=>	true
				),

				'shortcode' => array(

					'label'						=>	__('Shortcode', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Enter the shortcode to insert.', 'ws-form'),
					'select_list'				=>	true
				),

				'validate_form' => array(

					'label'						=>	__('Validate Before Saving', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, the form must validate before it will be saved.', 'ws-form'),
					'default'					=>	''
				),

				'text_clear' => array(

					'label'						=>	__('Clear', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Clear', 'ws-form')
				),

				'text_reset' => array(

					'label'						=>	__('Reset', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Reset', 'ws-form')
				),

				'text_password_strength_short' => array(

					'label'						=>	__('Very Weak', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Very Weak', 'ws-form')
				),

				'text_password_strength_bad' => array(

					'label'						=>	__('Weak', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Weak', 'ws-form')
				),

				'text_password_strength_good' => array(

					'label'						=>	__('Medium', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Medium', 'ws-form')
				),

				'text_password_strength_strong' => array(

					'label'						=>	__('Strong', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Strong', 'ws-form')
				),

				'text_password_visibility_toggle_off' => array(

					'label'						=>	__('Show password', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Show password', 'ws-form')
				),

				'text_password_visibility_toggle_on' => array(

					'label'						=>	__('Hide password', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Hide password', 'ws-form')
				),

				'text_password_generate' => array(

					'label'						=>	__('Suggest password', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Suggest password', 'ws-form')
				),

				'text_password_strength_invalid' => array(

					'label'						=>	__('Strength invalid', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Please choose a stronger password.', 'ws-form')
				),

				'invalid_feedback_render' => array(

					'label'						=>	__('Show Invalid Feedback', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Show invalid feedback text?', 'ws-form'),
					'default'					=>	'on'
				),

				'invalid_feedback' => array(

					'label'						=>	__('Invalid Feedback Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Text to show if this field is incorrectly completed.', 'ws-form'),
					'mask_placeholder'			=>	__('Please provide a valid #label_lowercase.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'invalid_feedback_render',
							'meta_value'	=>	'on'
						)
					),
					'variables'					=> true
				),

				'invalid_feedback_legal' => array(

					'label'						=>	__('Invalid Feedback Text', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Text to show if this field is incorrectly completed.', 'ws-form'),
					'mask_placeholder'			=>	__('Please provide a valid #label_lowercase.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'invalid_feedback_render',
							'meta_value'	=>	'on'
						)
					),
					'variables'					=>	true,
					'default'					=>	__('Please read the entire legal agreement.', 'ws-form'),
					'key'						=>	'invalid_feedback'
				),

				'validate_inline' => array(

					'label'						=>	__('Inline Validation', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, field is validated immediately rather than waiting for the form to be submitted.', 'ws-form'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'			=>	'==',
							'meta_key'		=>	'invalid_feedback_render',
							'meta_value'	=>	'on'
						)
					)
				),

				'text_editor' => array(

					'label'						=>	__('Content', 'ws-form'),
					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Enter paragraphs of text.', 'ws-form'),
					'select_list'				=>	true,
					'calc'						=>	true
				),

				'text_editor_note' => array(

					'label'						=>	__('Note', 'ws-form'),
					'type'						=>	'text_editor',
					'default'					=>	'',
					'help'						=>	__('Enter a note about your form. This is only shown in the layout editor.', 'ws-form'),
					'key'						=>	'text_editor'
				),

				'required_message' => array(

					'label'						=>	__('Required Message', 'ws-form'),
					'type'						=>	'required_message',
					'help'						=>	__('Enter a custom message to show if this field is not completed.', 'ws-form'),
					'select_list'				=>	true
				),

				// Accept
				'accept' => array(

					'label'						=>	__('Accepted File Type(s)', 'ws-form'),
					'mask'						=>	'accept="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	'',
					'help'						=>	__('Specify the accepted mime types or file extensions separated by commas.', 'ws-form'),
					'placeholder'				=>	__('e.g. application/pdf,image/jpeg or .jpg or image/*', 'ws-form'),
					'compatibility_id'			=>	'input-file-accept',
					'select_list'				=>	array()
				),

				// Field - HTML 5 attributes
				'cols' => array(

					'label'						=>	__('Columns', 'ws-form'),
					'mask'						=>	'cols="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Number of columns.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_textarea',
							'meta_value'		=>	''
						)
					)
				),

				'disabled' => array(

					'label'						=>	__('Disabled', 'ws-form'),
					'mask'						=>	'disabled aria-disabled="true"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'required',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'readonly',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'section_repeatable' => array(

					'label'						=>	__('Enabled', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'fields_toggle'				=>	array(

						array(

							'type'				=> 'section_icons',
							'width_factor'		=> 0.25
						)
					),
					'fields_ignore'				=>	array(

						'section_add',
						'section_delete',
						'section_icons'
					)
				),

				'section_repeat_label' => array(

					'label'						=>	__('Repeat Label', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'label_render',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					),
				),

				'section_repeat_min' => array(

					'label'						=>	__('Minimum Row Count', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	1,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					)
				),

				'section_repeat_max' => array(

					'label'						=>	__('Maximum Row Count', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	1,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					)
				),

				'section_repeat_default' => array(

					'label'						=>	__('Default Row Count', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	1,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					)
				),

				// Section icons - Style
				'section_icons_style' => array(

					'label'						=>	__('Icon', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'circle',
					'help'						=>	__('Select the style of the icons.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'circle', 'text' => __('Circle', 'ws-form')),
						array('value' => 'circle-solid', 'text' => __('Circle - Solid', 'ws-form')),
						array('value' => 'square', 'text' => __('Square', 'ws-form')),
						array('value' => 'square-solid', 'text' => __('Square - Solid', 'ws-form')),
						array('value' => 'text', 'text' => __('Text', 'ws-form')),
						array('value' => 'custom', 'text' => __('Custom HTML', 'ws-form'))
					)
				),

				// Section icons
				'section_icons' => array(

					'type'						=>	'repeater',
					'help'						=>	__('Select the icons to show.', 'ws-form'),
					'meta_keys'					=>	array(

						'section_icons_type',
						'section_icons_label'
					),
					'meta_keys_unique'			=>	array(
						'section_icons_type'
					),
					'default'					=>	array(

						(object) array(

							'section_icons_type' => 'add',
							'section_icons_label' => __('Add row', 'ws-form')
						),

						(object) array(

							'section_icons_type' => 'delete',
							'section_icons_label' => __('Remove row', 'ws-form')
						),

						(object) array(

							'section_icons_type' => 'move-up',
							'section_icons_label' => __('Move row up', 'ws-form')
						),

						(object) array(

							'section_icons_type' => 'move-down',
							'section_icons_label' => __('Move row down', 'ws-form')
						)
					)
				),

				// Section icons - Types
				'section_icons_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'help'						=>	__('Select the style of the add icon.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'add', 'text' => __('Add', 'ws-form')),
						array('value' => 'delete', 'text' => __('Remove', 'ws-form')),
						array('value' => 'move-up', 'text' => __('Move Up', 'ws-form')),
						array('value' => 'move-down', 'text' => __('Move Down', 'ws-form')),
						array('value' => 'drag', 'text' => __('Drag', 'ws-form')),
						array('value' => 'reset', 'text' => __('Reset', 'ws-form')),
						array('value' => 'clear', 'text' => __('Clear', 'ws-form'))
					),
					'options_blank'					=>	__('Select...', 'ws-form'),
				),

				// Section icons - Label
				'section_icons_label' => array(

					'label'						=>	__('ARIA Label', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	''
				),

				// Section icons - Size
				'section_icons_size' => array(

					'label'						=>	__('Size (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	24,
					'min'						=>	1,
					'help'						=>	__('Size of section icons in pixels.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'text'
						)
					)
				),

				// Section icons - Color - Off
				'section_icons_color_off' => array(

					'label'						=>	__('Disabled Color', 'ws-form'),
					'mask'						=>	'data-rating-color-off="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'default'					=>	WS_Form_Common::option_get('skin_color_default_lighter'),
					'help'						=>	__('Color of section icons when disabled.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - Color - On
				'section_icons_color_on' => array(

					'label'						=>	__('Active Color', 'ws-form'),
					'mask'						=>	'data-rating-color-on="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'default'					=>	WS_Form_Common::option_get('skin_color_default'),
					'help'						=>	__('Color of section icons when active.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Add
				'section_icons_html_add' => array(

					'label'						=>	__('Add Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Add">Add</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Delete
				'section_icons_html_delete' => array(

					'label'						=>	__('Remove Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Remove">Remove</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Move Up
				'section_icons_html_move_up' => array(

					'label'						=>	__('Move Up Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Move Up">Move Up</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Move Down
				'section_icons_html_move_down' => array(

					'label'						=>	__('Move Down Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Move Down">Move Down</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Drag
				'section_icons_html_drag' => array(

					'label'						=>	__('Drag Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Drag">Drag</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Reset
				'section_icons_html_reset' => array(

					'label'						=>	__('Reset Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="Reset">Reset</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Section icons - HTML - Clear
				'section_icons_html_clear' => array(

					'label'						=>	__('Clear Icon HTML', 'ws-form'),
					'type'						=>	'html_editor',
					'default'					=>	'<span title="clear">Clear</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_icons_style',
							'meta_value'		=>	'custom'
						)
					)
				),

				'section_repeatable_section_id' => array(

					'label'						=>	__('Repeatable Section', 'ws-form'),
					'mask'						=>	'data-repeatable-section-id="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'options'					=>	'sections',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'section_filter_attribute'	=>	'section_repeatable',
					'help'						=>	__('Select the repeatabled section this field is assigned to.', 'ws-form'),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'default'					=>	'#section_id'
				),

				// Horizontal Align
				'horizontal_align' => array(

					'label'						=>	__('Horizontal Alignment', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'flex-start',
					'options'					=>	array(

						array('value' => 'flex-start', 'text' => __('Left', 'ws-form')),
						array('value' => 'center', 'text' => __('Center', 'ws-form')),
						array('value' => 'flex-end', 'text' => __('Right', 'ws-form')),
						array('value' => 'space-around', 'text' => __('Space Around', 'ws-form')),
						array('value' => 'space-between', 'text' => __('Space Between', 'ws-form')),
						array('value' => 'space-evenly', 'text' => __('Space Evenly', 'ws-form'))
					)
				),

				'section_repeatable_delimiter_section' => array(

					'label'						=>	__('Row Delimiter', 'ws-form'),
					'type'						=>	'text',
					'help'						=>	__('String used to delimit rows in combined field values.', 'ws-form'),
					'default'					=>	WS_FORM_SECTION_REPEATABLE_DELIMITER_SECTION,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					),
					'placeholder'				=>	WS_FORM_SECTION_REPEATABLE_DELIMITER_SECTION
				),

				'section_repeatable_delimiter_row' => array(

					'label'						=>	__('Item Delimiter', 'ws-form'),
					'type'						=>	'text',
					'help'						=>	__('String used to delimit items (e.g. Checkboxes) in combined field values.', 'ws-form'),
					'default'					=>	WS_FORM_SECTION_REPEATABLE_DELIMITER_ROW,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_repeatable',
							'meta_value'		=>	'on'
						)
					),
					'placeholder'				=>	WS_FORM_SECTION_REPEATABLE_DELIMITER_ROW

				),
				'disabled_section' => array(

					'label'						=>	__('Disabled', 'ws-form'),
					'mask'						=>	'disabled',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'compatibility_id'			=>	'fieldset-disabled'
				),

				'text_align' => array(

					'label'						=>	__('Text Align', 'ws-form'),
					'mask'						=>	'style="text-align: #value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the alignment of text in the field.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Not Set', 'ws-form')),
						array('value' => 'left', 'text' => __('Left', 'ws-form')),
						array('value' => 'right', 'text' => __('Right', 'ws-form')),
						array('value' => 'center', 'text' => __('Center', 'ws-form')),
						array('value' => 'justify', 'text' => __('Justify', 'ws-form')),
						array('value' => 'inherit', 'text' => __('Inherit', 'ws-form')),
					),
					'default'					=>	'',
					'key'						=>	'text_align'
				),

				'text_align_right' => array(

					'label'						=>	__('Text Align', 'ws-form'),
					'mask'						=>	'style="text-align: #value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the alignment of text in the field.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Not Set', 'ws-form')),
						array('value' => 'left', 'text' => __('Left', 'ws-form')),
						array('value' => 'right', 'text' => __('Right', 'ws-form')),
						array('value' => 'center', 'text' => __('Center', 'ws-form')),
						array('value' => 'justify', 'text' => __('Justify', 'ws-form')),
						array('value' => 'inherit', 'text' => __('Inherit', 'ws-form')),
					),
					'default'					=>	'right',
					'key'						=>	'text_align'
				),

				'text_align_center' => array(

					'label'						=>	__('Text Align', 'ws-form'),
					'mask'						=>	'style="text-align: #value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the alignment of text in the field.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Not Set', 'ws-form')),
						array('value' => 'left', 'text' => __('Left', 'ws-form')),
						array('value' => 'right', 'text' => __('Right', 'ws-form')),
						array('value' => 'center', 'text' => __('Center', 'ws-form')),
						array('value' => 'justify', 'text' => __('Justify', 'ws-form')),
						array('value' => 'inherit', 'text' => __('Inherit', 'ws-form')),
					),
					'default'					=>	'center',
					'key'						=>	'text_align'
				),

				'inline' => array(

					'label'						=>	__('Inline', 'ws-form'),
					'mask'						=>	'data-inline',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				'password_strength_meter' => array(

					'label'						=>	__('Password Strength Meter', 'ws-form'),
					'type'						=>	'checkbox',
					'mask'						=>	'data-password-strength-meter',
					'mask_disregard_on_empty'	=>	true,
					'help'						=>	__('Show the WordPress password strength meter?', 'ws-form'),
					'default'					=>	'on',
				),

				'password_visibility_toggle' => array(

					'label'						=>	__('Password Visibility Toggle', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Show the password visibility toggle icon?', 'ws-form'),
					'default'					=>	'',
				),

				'password_generate' => array(

					'label'						=>	__('Suggest Password', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('Show the suggest password icon?', 'ws-form'),
					'default'					=>	'',
				),

				'password_strength_invalid' => array(

					'label'						=>	__('Minimum Password Strength ', 'ws-form'),
					'type'						=>	'select',
					'mask'						=>	'data-password-strength-invalid="#value"',
					'mask_disregard_on_empty'	=>	true,
					'help'						=>	__('Choose the minimum required password strength.', 'ws-form'),
					'default'					=>	'0',
					'options'					=>	array(

						array('value' => '4', 'text' => __('Strong', 'ws-form')),
						array('value' => '3', 'text' => __('Medium', 'ws-form')),
						array('value' => '2', 'text' => __('Weak', 'ws-form')),
						array('value' => '1', 'text' => __('Very Weak', 'ws-form')),
						array('value' => '0', 'text' => __('None', 'ws-form'))
					)
				),

				'hidden_bypass' => array(

					'label'						=>	__('Always Include in Actions', 'ws-form'),
					'mask'						=>	'data-hidden-bypass',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',

					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will always include this field in actions if it is hidden.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'ecommerce_calculation_persist' => array(

					'label'						=>	__('Always Include in Cart Total', 'ws-form'),
					'mask'						=>	'data-ecommerce-persist',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',

					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will include this field in the cart total calculation if it is hidden.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'exclude_cart_total',
							'meta_value'		=>	'on'
						)
					)
				),

				'ecommerce_price_negative' => array(

					'label'						=>	__('Allow Negative Value', 'ws-form'),
					'mask'						=>	'data-ecommerce-negative',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				'ecommerce_price_min' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'data-ecommerce-min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Minimum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'field_part'				=>	'field_ecommerce_price_min'
				),

				'ecommerce_price_max' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'data-ecommerce-max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Maximum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'field_part'				=>	'field_ecommerce_price_max'

				),

				'ecommerce_quantity_min' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'default'					=>	0,
					'type'						=>	'text',
					'help'						=>	__('Minimum value this field can have.', 'ws-form'),
					'field_part'				=>	'field_min'

				),

				'ecommerce_field_id' => array(

					'label'						=>	__('Related Price Field', 'ws-form'),
					'mask'						=>	'data-ecommerce-field-id="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_attribute'	=>	array('ecommerce_price'),
					'help'						=>	__('Price field that this field relates to.', 'ws-form'),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),

				'ecommerce_quantity_default_value' => array(

					'label'						=>	__('Default Value', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'1',
					'help'						=>	__('Default quantity value.', 'ws-form'),
					'select_list'				=>	true,
					'key'						=>	'default_value'
				),

				// Price type
				'ecommerce_cart_price_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'data-ecommerce-cart-price-#value',
					'type'						=>	'select',
					'help'						=>	__('Select the type of cart detail.', 'ws-form'),
					'options'					=>	'ecommerce_cart_price_type',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'required_setting'			=>	true,
					'data_change'				=>	array('event' => 'change', 'action' => 'update')
				),
				'max_length' => array(

					'label'						=>	__('Maximum Characters', 'ws-form'),
					'mask'						=>	'maxlength="#value"',
					'mask_disregard_on_empty'	=>	true,
					'min'						=>	0,
					'type'						=>	'number',
					'default'					=>	'',
					'help'						=>	__('Maximum length for this field in characters.', 'ws-form'),
					'compatibility_id'			=>	'maxlength',
					'field_part'				=>	'field_maxlength'
				),

				'min_length' => array(

					'label'						=>	__('Minimum Characters', 'ws-form'),
					'mask'						=>	'minlength="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'number',
					'min'						=>	0,
					'default'					=>	'',
					'help'						=>	__('Minimum length for this field in characters.', 'ws-form'),
					'compatibility_id'			=>	'input-minlength',
					'field_part'				=>	'field_minlength'
				),

				'max_length_words' => array(

					'label'						=>	__('Maximum Words', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	0,
					'default'					=>	'',
					'help'						=>	__('Maximum words allowed in this field.', 'ws-form')
				),

				'min_length_words' => array(

					'label'						=>	__('Minimum Words', 'ws-form'),
					'min'						=>	0,
					'type'						=>	'number',
					'default'					=>	'',
					'help'						=>	__('Minimum words allowed in this field.', 'ws-form')
				),

				'min' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Minimum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'field_part'				=>	'field_min'
				),

				'max' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Maximum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'field_part'				=>	'field_max'
				),

				'min_range' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Minimum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'placeholder'				=>	'0',
					'key'						=>	'min',
					'field_part'				=>	'field_min'
				),

				'max_range' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Maximum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'placeholder'				=>	'100',
					'key'						=>	'max',
					'field_part'				=>	'field_max'
				),

				'max_progress' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Maximum value this field can have.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'placeholder'				=>	'100',
					'key'						=>	'max',
					'field_part'				=>	'field_max',
					'compatibility_id'			=>	'mdn-html_elements_progress_max'
				),

				'min_meter' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Minimum value possible on the meter. This can be any negative or positive number.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'placeholder'				=>	'0',
					'key'						=>	'min',
					'field_part'				=>	'field_min',
					'compatibility_id'			=>	'mdn-html_elements_meter_min'
				),

				'max_meter' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Maximum value possible on the meter. This can be any negative or positive number.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'placeholder'				=>	'1',
					'key'						=>	'max',
					'field_part'				=>	'field_max',
					'compatibility_id'			=>	'mdn-html_elements_meter_max'
				),

				'low' => array(

					'label'						=>	__('Low', 'ws-form'),
					'mask'						=>	'low="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Lowest value across the range defined by the meter. The value must be higher than min and lower than high.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'compatibility_id'			=>	'mdn-html_elements_meter_low'
				),

				'high' => array(

					'label'						=>	__('High', 'ws-form'),
					'mask'						=>	'high="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Highest value across the range defined by the meter. The value must be lower than max and higher than low.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'compatibility_id'			=>	'mdn-html_elements_meter_high'
				),

				'optimum' => array(

					'label'						=>	__('Optimum', 'ws-form'),
					'mask'						=>	'optimum="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'help'						=>	__('Indicates the optimum value and must be within the range of min and max values. When used with the low and high attribute, it indicates the preferred zone for a given range.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'text',
					'compatibility_id'			=>	'mdn-html_elements_meter_optimum'
				),

				'min_date' => array(

					'label'						=>	__('Minimum', 'ws-form'),
					'mask'						=>	'min="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'datetime_min_max_advanced',
					'help'						=>	__('Minimum date/time that can be chosen.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'datetime_min_max_advanced',
					'field_part'				=>	'field_min'
				),

				'max_date' => array(

					'label'						=>	__('Maximum', 'ws-form'),
					'mask'						=>	'max="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'datetime_min_max_advanced',
					'help'						=>	__('Maximum date/time that can be chosen.', 'ws-form'),
					'select_list'				=>	true,
					'select_list_for_type'		=>	'datetime_min_max_advanced',
					'field_part'				=>	'field_max'
				),

				'year_start' => array(

					'label'						=>	__('Start Year', 'ws-form'),
					'mask'						=>	'data-year-start="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Defaults to 1950', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'year_end' => array(

					'label'						=>	__('End Year', 'ws-form'),
					'mask'						=>	'data-year-end="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Defaults to 2050', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'time_step' => array(

					'label'						=>	__('Time Step', 'ws-form'),
					'mask'						=>	'data-time-step="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Time step in minutes. Defaults to 15 minutes.', 'ws-form'),
					'placeholder'				=>	'15',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'time'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'disabled_week_days' => array(

					'label'						=>	__('Disabled Week Days', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	__('Choose which days to disable.', 'ws-form'),
					'meta_keys_unique'			=>	array(

						'disabled_week_days_day'
					),
					'meta_keys'					=>	array(

						'disabled_week_days_day'
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'disabled_week_days_day' => array(

					'label'						=>	__('Day', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => '0', 'text' => __('Sunday', 'ws-form')),
						array('value' => '1', 'text' => __('Monday', 'ws-form')),
						array('value' => '2', 'text' => __('Tuesday', 'ws-form')),
						array('value' => '3', 'text' => __('Wednesday', 'ws-form')),
						array('value' => '4', 'text' => __('Thursday', 'ws-form')),
						array('value' => '5', 'text' => __('Friday', 'ws-form')),
						array('value' => '6', 'text' => __('Saturday', 'ws-form')),
					),
					'options_blank'				=>	__('Select...', 'ws-form')
				),

				'disabled_dates' => array(

					'label'						=>	__('Disabled Dates', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	__('Choose which dates to disable.', 'ws-form'),
					'meta_keys'					=>	array(

						'disabled_dates_date'
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'disabled_dates_date' => array(

					'label'						=>	__('Date', 'ws-form'),
					'type'						=>	'text',
					'placeholder'				=>	'yyyy-mm-dd'
				),

				'enabled_dates' => array(

					'label'						=>	__('Enabled Dates', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	__('Choose which dates to enable.', 'ws-form'),
					'meta_keys'					=>	array(

						'enabled_dates_date'
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'date'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'input_type_datetime',
							'meta_value'		=>	'datetime-local'
						)
					)
				),

				'enabled_dates_date' => array(

					'label'						=>	__('Date', 'ws-form'),
					'type'						=>	'text',
					'placeholder'				=>	'yyyy-mm-dd'
				),

				'multiple' => array(

					'label'						=>	__('Multiple', 'ws-form'),
					'mask'						=>	'multiple',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, multiple options can be selected at once.', 'ws-form'),
					'default'					=>	''
				),

				'multiple_email' => array(

					'label'						=>	__('Multiple', 'ws-form'),
					'mask'						=>	'multiple',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked, multiple email addresses can be entered.', 'ws-form'),
				),
				'select2' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'mask'						=>	'data-wsf-select2',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	''
				),

				'select2_ajax' => array(

					'label'						=>	__('Use AJAX', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, the options are retrieved dynamically using AJAX. Only options matching the keyword entered by the user will be shown. This can improve performance with larger datasets.', 'ws-form'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select2',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'!=',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'!=',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select2_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, show all results if no options match the keyword entered.', 'ws-form'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select2',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'==',
							'meta_key'			=>	'select2_ajax',
							'meta_value'		=>	'on'
						)
					)
				),

				'select2_tags' => array(

					'label'						=>	__('Enable Tagging', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, Select2 will dynamically create new options from text input by the user in the search box.', 'ws-form'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select2',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'==',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						)
					)
				),

				'select2_intro' => array(

					'type'						=>	'html',
					'html'						=>	__('Enabling <a href="https://select2.org/" target="_blank">Select2</a> adds support for searching as well as pill boxes if multiple is enabled.', 'ws-form')
				),

				'multiple_file' => array(

					'label'						=>	__('Multiple', 'ws-form'),
					'mask'						=>	'multiple',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked, multiple files can be selected in the file picker.', 'ws-form'),
					'compatibility_id'			=>	'input-file-multiple',
				),

				'file_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'mask'						=>	'data-file-type="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Type of file selector to use.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Default', 'ws-form')),
						array('value' => 'dropzonejs', 'text' => __('DropzoneJS', 'ws-form')),
					),
					'default'					=> '',
					'key'						=> 'sub_type'
				),

				'file_handler' => array(

					'label'						=>	__('Save To', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the final destination for this file when the form is submitted.', 'ws-form'),
					'options'					=>	array(),
					'default'					=> 'wsform'
				),

				'file_name_mask' => array(

					'label'						=>	__('Filename', 'ws-form'),
					'type'						=>	'text',
					'placeholder'				=>	'#file_name',
					'help'						=>	__('Use this setting to change the filename. You can use:<ul><li>#file_basename (Original filename)</li><li>#file_filename (Without extension)</li><li>#file_extension (e.g. jpg)</li></ul>You can also use WS Form variables.', 'ws-form'),
					'default'					=> ''
				),

				'file_min'	=> array(

					'label'						=>	__('Minimum Files', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	1,
					'help'						=>	__('Specify the minimum number of files that should be uploaded.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						)
					)
				),

				'file_max'	=> array(

					'label'						=>	__('Maximum Files', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	1,
					'help'						=>	__('Specify the maximum number of files that can be uploaded.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						)
					)
				),

				'file_min_size' => array(

					'label'						=>	__('Minimum File Size (MB)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0,
					'help'						=>	__('Leave blank for none.', 'ws-form')
				),

				'file_max_size' => array(

					'label'						=>	__('Maximum File Size (MB)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0,
					'help'						=>	__('Leave blank to use server configuration.', 'ws-form')
				),

				'file_timeout' => array(

					'label'						=>	__('Timeout (ms)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'placeholder'				=>	'30000',
					'min'						=>	0,
					'help'						=>	__('The timeout for the XHR requests in milliseconds. Leave blank for 30 seconds.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	'dropzonejs'
						)
					)
				),

				'file_preview' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',

					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will show a preview of the file(s).', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),

					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	''
						)
					)
				),

				'file_preview_width' => array(

					'label'						=>	__('Width', 'ws-form'),
					'type'						=>	'text',
					'help'						=>	__('Set the width of each file preview.', 'ws-form'),
					'default'					=>	'150px',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'file_preview',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	'dropzonejs'
						),

						array(

							'logic_previous'	=>	'&&',
							'logic'				=>	'==',
							'meta_key'			=>	'orientation',
							'meta_value'		=>	'horizontal'
						),


					)
				),

				'file_image_max_width' => array(

					'label'						=>	__('Max Width (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	1,
					'help'						=>	__('Enter the maximum width in pixels the saved file should be. Leave blank for no change.', 'ws-form')
				),

				'file_image_max_height' => array(

					'label'						=>	__('Max Height (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	1,
					'help'						=>	__('Enter the maximum height in pixels the saved file should be. Leave blank for no change.', 'ws-form')
				),

				'file_image_crop' => array(

					'label'						=>	__('Crop', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, images will be cropped to the maximum dimensions above using center positions.', 'ws-form'),
					'default'					=>	''
				),

				'file_image_compression' => array(

					'label'						=>	__('Quality', 'ws-form'),
					'type'						=>	'number',
					'min'						=>	1,
					'max'						=>	100,
					'help'						=>	__('Sets image compression quality on a 1-100 scale. Leave blank for no change.', 'ws-form')
				),

				'file_image_mime' => array(

					'label'						=>	__('File Format', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the file format image uploads should be saved as.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Same as original', 'ws-form')),
						array('value' => 'image/jpeg', 'text' => __('JPG', 'ws-form')),
						array('value' => 'image/png', 'text' => __('PNG', 'ws-form')),
						array('value' => 'image/gif', 'text' => __('GIF', 'ws-form'))
					)
				),

				'file_capture' => array(

					'label'						=>	__('Capture', 'ws-form'),
					'mask'						=>	'capture="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Select the preferred capture media (where supported).', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('Off', 'ws-form')),
						array('value' => 'capture', 'text' => __('On', 'ws-form')),
						array('value' => 'user', 'text' => __('User facing camera', 'ws-form')),
						array('value' => 'environment', 'text' => __('Environment facing camera', 'ws-form'))
					)
				),

				'directory' => array(

					'label'						=>	__('Directory', 'ws-form'),
					'mask'						=>	'webkitdirectory mozdirectory',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Allow entire directory with file contents (and any subdirectories) to be selected.', 'ws-form'),
					'compatibility_id'			=>	'input-file-directory',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	''
						)
					)
				),
				'input_mask' => array(

					'label'						=>	__('Input Mask', 'ws-form'),
					'mask'						=>	'data-inputmask="\'mask\': \'#value\'"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Input mask for the field, e.g. (999) 999-9999', 'ws-form'),
					'select_list'				=>	array(

						array('text' => __('US/Canadian Phone Number', 'ws-form'), 'value' => '(999) 999-9999'),
						array('text' => __('US/Canadian Phone Number (International)', 'ws-form'), 'value' => '+1 (999) 999-9999'),
						array('text' => __('US Zip Code', 'ws-form'), 'value' => '99999'),
						array('text' => __('US Zip Code +4', 'ws-form'), 'value' => '99999[-9999]'),
						array('text' => __('Canadian Post Code', 'ws-form'), 'value' => 'A9A-9A9'),
						array('text' => __('Short Date', 'ws-form'), 'value' => '99/99/9999'),
						array('text' => __('Social Security Number', 'ws-form'), 'value' => '999-99-9999')
					)
				),

				'group_user_status' => array(

					'label'						=>	__('User Status', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Any', 'ws-form')),
						array('value' => 'on', 'text' => __('Is Logged In', 'ws-form')),
						array('value' => 'out', 'text' => __('Is Logged Out', 'ws-form')),
						array('value' => 'role_capability', 'text' => __('Has User Role or Capability', 'ws-form'))
					),
					'help'						=>	__('Only show the tab under certain user conditions.', 'ws-form')
				),

				'section_user_status' => array(

					'label'						=>	__('User Status', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Any', 'ws-form')),
						array('value' => 'on', 'text' => __('Is Logged In', 'ws-form')),
						array('value' => 'out', 'text' => __('Is Logged Out', 'ws-form')),
						array('value' => 'role_capability', 'text' => __('Has User Role or Capability', 'ws-form'))
					),
					'help'						=>	__('Only show the section under certain user conditions.', 'ws-form')
				),

				'field_user_status' => array(

					'label'						=>	__('User Status', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('Any', 'ws-form')),
						array('value' => 'on', 'text' => __('Is Logged In', 'ws-form')),
						array('value' => 'out', 'text' => __('Is Logged Out', 'ws-form')),
						array('value' => 'role_capability', 'text' => __('Has User Role or Capability', 'ws-form'))
					),
					'help'						=>	__('Only show the field under certain user conditions.', 'ws-form')
				),

				'form_user_roles' => array(

					'label'						=>	__('User Role', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this form if logged in user has one of these roles.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'user_limit_logged_in',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'group_user_roles' => array(

					'label'						=>	__('User Role', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this tab if logged in user has one of these roles.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'group_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'section_user_roles' => array(

					'label'						=>	__('User Role', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this section if logged in user has one of these roles.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'field_user_roles' => array(

					'label'						=>	__('User Role', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this field if logged in user has one of these roles.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'field_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'form_user_capabilities' => array(

					'label'						=>	__('User Capability', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this form if logged in user has one of these capabilities.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'user_limit_logged_in',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'group_user_capabilities' => array(

					'label'						=>	__('User Capability', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this tab if logged in user has one of these capabilities.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'group_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'section_user_capabilities' => array(

					'label'						=>	__('User Capability', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this section if logged in user has one of these capabilities.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'section_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'field_user_capabilities' => array(

					'label'						=>	__('User Capability', 'ws-form'),
					'type'						=>	'select',
					'select2'					=>	true,
					'multiple'					=>	true,
					'placeholder'				=>	__('Select...'),
					'help'						=>	__('Only show this field if logged in user has one of these capabilities.', 'ws-form'),
					'options'					=>	array(),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'field_user_status',
							'meta_value'		=>	'role_capability'
						)
					)
				),

				'pattern' => array(

					'label'						=>	__('Pattern', 'ws-form'),
					'mask'						=>	'pattern="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Regular expression value is checked against.', 'ws-form'),
					'select_list'				=>	array(

						array('text' => __('Alpha', 'ws-form'), 'value' => '^[a-zA-Z]+$'),
						array('text' => __('Alphanumeric', 'ws-form'), 'value' => '^[a-zA-Z0-9]+$'),
						array('text' => __('Color', 'ws-form'), 'value' => '^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$'),
						array('text' => __('Country Code (2 Character)', 'ws-form'), 'value' => '[A-Za-z]{2}'),
						array('text' => __('Country Code (3 Character)', 'ws-form'), 'value' => '[A-Za-z]{3}'),
						array('text' => __('Date (mm/dd)', 'ws-form'), 'value' => '(0[1-9]|1[012]).(0[1-9]|1[0-9]|2[0-9]|3[01])'),
						array('text' => __('Date (dd/mm)', 'ws-form'), 'value' => '(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012])'),
						array('text' => __('Date (mm.dd.yyyy)', 'ws-form'), 'value' => '(0[1-9]|1[012]).(0[1-9]|1[0-9]|2[0-9]|3[01]).[0-9]{4}'),
						array('text' => __('Date (dd.mm.yyyy)', 'ws-form'), 'value' => '(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}'),
						array('text' => __('Date (yyyy-mm-dd)', 'ws-form'), 'value' => '(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))'),
						array('text' => __('Date (mm/dd/yyyy)', 'ws-form'), 'value' => '(?:(?:0[1-9]|1[0-2])[\/\\-. ]?(?:0[1-9]|[12][0-9])|(?:(?:0[13-9]|1[0-2])[\/\\-. ]?30)|(?:(?:0[13578]|1[02])[\/\\-. ]?31))[\/\\-. ]?(?:19|20)[0-9]{2}'),
						array('text' => __('Date (dd/mm/yyyy)', 'ws-form'), 'value' => '^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$'),
						array('text' => __('Email', 'ws-form'), 'value' => '[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$'),
						array('text' => __('IP (Version 4)', 'ws-form'), 'value' => '^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?).){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$'),
						array('text' => __('IP (Version 6)', 'ws-form'), 'value' => '((^|:)([0-9a-fA-F]{0,4})){1,8}$'),
						array('text' => __('ISBN', 'ws-form'), 'value' => '(?:(?=.{17}$)97[89][ -](?:[0-9]+[ -]){2}[0-9]+[ -][0-9]|97[89][0-9]{10}|(?=.{13}$)(?:[0-9]+[ -]){2}[0-9]+[ -][0-9Xx]|[0-9]{9}[0-9Xx])'),
						array('text' => __('Latitude or Longitude', 'ws-form'), 'value' => '-?\d{1,3}\.\d+'),
						array('text' => __('MD5 Hash', 'ws-form'), 'value' => '[0-9a-fA-F]{32}'),
						array('text' => __('Numeric', 'ws-form'), 'value' => '^[0-9]+$'),
						array('text' => __('Password (Numeric, lower, upper)', 'ws-form'), 'value' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$'),
						array('text' => __('Password (Numeric, lower, upper, min 8)', 'ws-form'), 'value' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}'),
						array('text' => __('Phone - General', 'ws-form'), 'value' => '[0-9+()-. ]+'),
						array('text' => __('Phone - UK', 'ws-form'), 'value' => '^\s*\(?(020[7,8]{1}\)?[ ]?[1-9]{1}[0-9{2}[ ]?[0-9]{4})|(0[1-8]{1}[0-9]{3}\)?[ ]?[1-9]{1}[0-9]{2}[ ]?[0-9]{3})\s*$'),
						array('text' => __('Phone - US: 123-456-7890', 'ws-form'), 'value' => '\d{3}[\-]\d{3}[\-]\d{4}'),
						array('text' => __('Phone - US: (123)456-7890', 'ws-form'), 'value' => '\([0-9]{3}\)[0-9]{3}-[0-9]{4}'),
						array('text' => __('Phone - US: (123) 456-7890', 'ws-form'), 'value' => '\([0-9]{3}\) [0-9]{3}-[0-9]{4}'),
						array('text' => __('Phone - US: Flexible', 'ws-form'), 'value' => '(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}'),
						array('text' => __('Postal Code (UK)', 'ws-form'), 'value' => '[A-Za-z]{1,2}[0-9Rr][0-9A-Za-z]? [0-9][ABD-HJLNP-UW-Zabd-hjlnp-uw-z]{2}'),
						array('text' => __('Price (1.23)', 'ws-form'), 'value' => '\d+(\.\d{2})?'),
						array('text' => __('Slug', 'ws-form'), 'value' => '^[a-z0-9-]+$'),
						array('text' => __('Time (hh:mm:ss)', 'ws-form'), 'value' => '(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}'),
						array('text' => __('URL', 'ws-form'), 'value' => 'https?://.+'),
						array('text' => __('Zip Code', 'ws-form'), 'value' => '(\d{5}([\-]\d{4})?)')						
					),
					'compatibility_id'			=>	'input-pattern'
				),

				'pattern_tel' => array(

					'label'						=>	__('Pattern', 'ws-form'),
					'mask'						=>	'pattern="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Regular expression value is checked against.', 'ws-form'),
					'select_list'				=>	array(

						array('text' => __('Phone - General', 'ws-form'), 'value' => '[0-9+()-. ]+'),
						array('text' => __('Phone - UK', 'ws-form'), 'value' => '^\s*\(?(020[7,8]{1}\)?[ ]?[1-9]{1}[0-9{2}[ ]?[0-9]{4})|(0[1-8]{1}[0-9]{3}\)?[ ]?[1-9]{1}[0-9]{2}[ ]?[0-9]{3})\s*$'),
						array('text' => __('Phone - US: 123-456-7890', 'ws-form'), 'value' => '\d{3}[\-]\d{3}[\-]\d{4}'),
						array('text' => __('Phone - US: (123)456-7890', 'ws-form'), 'value' => '\([0-9]{3}\)[0-9]{3}-[0-9]{4}'),
						array('text' => __('Phone - US: (123) 456-7890', 'ws-form'), 'value' => '\([0-9]{3}\) [0-9]{3}-[0-9]{4}'),
						array('text' => __('Phone - US: Flexible', 'ws-form'), 'value' => '(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}')						
					),
					'compatibility_id'			=>	'input-pattern'
				),

				'pattern_date' => array(

					'label'						=>	__('Pattern', 'ws-form'),
					'mask'						=>	'pattern="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Regular expression value is checked against.', 'ws-form'),
					'select_list'				=>	array(

						array('text' => __('mm.dd.yyyy', 'ws-form'), 'value' => '(0[1-9]|1[012]).(0[1-9]|1[0-9]|2[0-9]|3[01]).[0-9]{4}'),
						array('text' => __('dd.mm.yyyy', 'ws-form'), 'value' => '(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}'),
						array('text' => __('mm/dd/yyyy', 'ws-form'), 'value' => '(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d'),
						array('text' => __('dd/mm/yyyy', 'ws-form'), 'value' => '(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d'),
						array('text' => __('yyyy-mm-dd', 'ws-form'), 'value' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])'),
						array('text' => __('hh:mm:ss', 'ws-form'), 'value' => '(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}'),
						array('text' => __('yyyy-mm-ddThh:mm:ssZ', 'ws-form'), 'value' => '/([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/')						
					),
					'compatibility_id'			=>	'input-pattern'
				),

				'placeholder' => array(

					'label'						=>	__('Placeholder', 'ws-form'),
					'mask'						=>	'placeholder="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'help'						=>	__('Short hint that describes the expected value of the input field.', 'ws-form'),
					'compatibility_id'			=>	'input-placeholder',
					'select_list'				=>	true,
					'field_part'				=>	'field_placeholder'
				),

				'placeholder_dropzonejs' => array(

					'label'						=>	__('Placeholder', 'ws-form'),
					'type'						=>	'text',
					'help'						=>	__('The text used before any files are dropped.', 'ws-form'),
					'default'					=>	'',
					'placeholder'				=>	__('Drop files here to upload.', 'ws-form'),
					'select_list'				=>	true,
					'key'						=>	'placeholder',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'sub_type',
							'meta_value'		=>	'dropzonejs'
						)
					)
				),

				'placeholder_googleaddress' => array(

					'label'						=>	__('Placeholder', 'ws-form'),
					'type'						=>	'text',
					'help'						=>	__('Short hint that describes the expected value of the input field.', 'ws-form'),
					'default'					=>	'',
					'placeholder'				=>	__('Enter a location', 'ws-form'),
					'key'						=>	'placeholder'
				),

				'placeholder_row' => array(

					'label'						=>	__('First Row Placeholder (Blank for none)', 'ws-form'),
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'default'					=>	__('Select...', 'ws-form'),
					'help'						=>	__('First value in the select pulldown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						),

						array(

							'logic_previous'	=>	'||',
							'logic'				=>	'==',
							'meta_key'			=>	'select2',
							'meta_value'		=>	'on'
						)
					)
				),

				'readonly' => array(

					'label'						=>	__('Read Only', 'ws-form'),
					'mask'						=>	'readonly aria-readonly="true"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'required',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'disabled',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					),
					'compatibility_id'			=>	'readonly-attr'
				),

				'readonly_on' => array(

					'label'						=>	__('Read Only', 'ws-form'),
					'mask'						=>	'readonly aria-readonly="true"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'required',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'disabled',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					),
					'compatibility_id'			=>	'readonly-attr',
					'key'						=>	'readonly'
				),

				'scroll_to_top' => array(

					'label'						=>	__('Scroll To Top', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'instant', 'text' => __('Instant', 'ws-form')),
						array('value' => 'smooth', 'text' => __('Smooth', 'ws-form'))
					)
				),

				'scroll_to_top_offset' => array(

					'label'						=>	__('Offset (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'0',
					'help'						=>	__('Number of pixels to offset the final scroll position by. Useful for sticky headers, e.g. if your header is 100 pixels tall, enter 100 into this setting.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'scroll_to_top',
							'meta_value'		=>	''
						)
					)
				),

				'scroll_to_top_duration'	=> array(

					'label'						=>	__('Duration (ms)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'400',
					'help'						=>	__('Duration of the smooth scroll in ms.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'scroll_to_top',
							'meta_value'		=>	'smooth'
						)
					)
				),

				'required' => array(

					'label'						=>	__('Required', 'ws-form'),
					'mask'						=>	'required data-required aria-required="true"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'compatibility_id'			=>	'form-validation',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'disabled',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'readonly',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'required_on' => array(

					'label'						=>	__('Required', 'ws-form'),
					'mask'						=>	'required data-required aria-required="true"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'compatibility_id'			=>	'form-validation',
					'key'						=>	'required',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'disabled',
							'meta_value'	=>	'on'
						),

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'readonly',
							'meta_value'	=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'required_attribute_no' => array(

					'label'						=>	__('Required', 'ws-form'),
					'mask'						=>	'',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'compatibility_id'			=>	'form-validation',
					'data_change'				=>	array('event' => 'change', 'action' => 'update'),
					'key'						=>	'required'
				),

				'required_row' => array(

					'mask'						=>	'required data-required aria-required="true"',
					'mask_disregard_on_empty'	=>	true
				),

				'rows' => array(

					'label'						=>	__('Rows', 'ws-form'),
					'mask'						=>	'rows="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	true,
					'type'						=>	'number',
					'help'						=>	__('Number of rows.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'input_type_textarea',
							'meta_value'		=>	'html'
						)
					)
				),

				'size' => array(

					'label'						=>	__('Size', 'ws-form'),
					'mask'						=>	'size="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	true,
					'type'						=>	'number',
					'attributes'				=>	array('min' => 0),
					'help'						=>	__('The number of visible options.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'select2',
							'meta_value'		=>	'on'
						)
					),
				),

				'select_all' => array(

					'label'						=>	__('Enable Select All', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Show a \'Select All\' option above the first row.', 'ws-form')
				),

				'select_all_label' => array(

					'label'						=>	__('Select All Label', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Select All', 'ws-form'),
					'help'						=>	__('Enter custom label for \'Select All\' row.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_all',
							'meta_value'		=>	'on'
						)
					),
				),

				'spellcheck' => array(

					'label'						=>	__('Spell Check', 'ws-form'),
					'mask'						=>	'spellcheck="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'select',
					'help'						=>	__('Spelling and grammar checking.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 		'text' => __('Browser default', 'ws-form')),
						array('value' => 'true', 	'text' => __('Enabled', 'ws-form')),
						array('value' => 'false', 	'text' => __('Disabled', 'ws-form'))
					),
					'compatibility_id'			=>	'spellcheck-attribute'
				),

				'step' => array(

					'label'						=>	__('Step', 'ws-form'),
					'mask'						=>	'step="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
					'type'						=>	'text',
					'placeholder'				=>	'1',
					'help'						=>	__("Increment/decrement by this value. Use 'any' to allow any number of decimals.", 'ws-form')
				),

				// Fields - Sidebars
				'field_select' => array(

					'type'					=>	'field_select'
				),

				'section_select' => array(

					'type'					=>	'section_select'
				),

				'form_history' => array(

					'type'					=>	'form_history'
				),

				'knowledgebase' => array(

					'type'					=>	'knowledgebase'
				),

				'contact' => array(

					'type'					=>	'contact'
				),

				'ws_form_field' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form')
				),

				'ws_form_field_no_file' => array(

					'label'							=>	__('Form Field', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	'fields',
					'options_blank'					=>	__('Select...', 'ws-form'),
					'fields_filter_type_exclude'	=>	array('file', 'signature'),
					'key'							=>	'ws_form_field'
				),

				'ws_form_field_choice' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'checkbox', 'radio'),
					'key'						=>	'ws_form_field'
				),

				'ws_form_field_file' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('signature', 'file'),
					'key'						=>	'ws_form_field'
				),

				'ws_form_field_save' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_attribute'	=>	array('submit_save'),
					'key'						=>	'ws_form_field'
				),

				'ws_form_field_edit' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_attribute'	=>	array('submit_edit'),
					'key'						=>	'ws_form_field'
				),

				'ws_form_field_ecommerce_price_cart' => array(

					'label'						=>	__('Form Field', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_attribute'	=>	array('ecommerce_cart_price')
				),

				// Fields - Data grids
				'conditional' => array(

					'label'					=>	__('Conditions', 'ws-form'),
					'type'					=>	'data_grid',
					'type_sub'				=>	'conditional',	// Sub type
					'read_only_header'		=>	true,
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'max_columns'			=>	1,		// Maximum number of columns
					'groups_label'			=>	false,	// Is the group label feature enabled?
					'groups_label_render'	=>	false,	// Is the group label render feature enabled?
					'groups_auto_group'		=>	false,	// Is auto group feature enabled?
					'groups_disabled'		=>	false,	// Is the group disabled attribute?
					'groups_group'			=>	false,	// Is the group mask supported?
					'field_wrapper'			=>	false,
					'upload_download'		=>	false,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Condition', 'ws-form')),
							array('id' => 1, 'label' => __('Data', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Conditions', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
								)
							)
						)
					)
				),

				'action'	=>	array(

					'label'					=>	__('Actions', 'ws-form'),
					'type'					=>	'data_grid',
					'type_sub'				=>	'action',	// Sub type
					'read_only_header'		=>	true,
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'max_columns'			=>	1,		// Maximum number of columns
					'groups_label'			=>	false,	// Is the group label feature enabled?
					'groups_label_render'	=>	false,	// Is the group label render feature enabled?
					'groups_auto_group'		=>	false,	// Is auto group feature enabled?
					'groups_disabled'		=>	false,	// Is the group disabled attribute?
					'groups_group'			=>	false,	// Is the group mask supported?
					'field_wrapper'			=>	false,
					'upload_download'		=>	false,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Action', 'ws-form')),
							array('id' => 1, 'label' => __('Data', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Actions', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
								)
							)
						)
					)
				),

				'data_source_id' => array(

					'label'						=>	__('Data Source', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'data_source',
					'class_wrapper'				=>	'wsf-field-wrapper-header'
				),

				'data_source_recurrence' => array(

					'label'						=>	__('Update Frequency', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'hourly',
					'options'					=>	array(),
					'help'						=>	__('This setting only applies to published forms. Previews show data in real-time.')
				),

				'data_source_get' => array(

					'label'						=>	__('Get Data', 'ws-form'),
					'type'						=>	'button'
				),



				'datalist_field_value' => array(

					'label'						=>	__('Values', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_datalist',
					'default'					=>	0,
					'html_encode'				=>	true
				),

				'datalist_field_text' => array(

					'label'						=>	__('Labels', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_datalist',
					'default'					=>	1
				),



				'select_field_label' => array(

					'label'						=>	__('Labels', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the option labels.', 'ws-form')
				),

				'select_field_value' => array(

					'label'						=>	__('Values', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the option values. These values should be unique.', 'ws-form')
				),

				'select_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'select_min' => array(

					'label'						=>	__('Minimum Selected', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_max' => array(

					'label'						=>	__('Maximum Selected', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'multiple',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'select_cascade_field_id' => array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade_field_filter_comma'	=> array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked and the filter value does not match any data in your filter column, all options will be shown.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade_option_text_no_rows' => array(

					'label'						=>	__('No Results Placeholder', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Select...'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'select_cascade_no_match',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'select_cascade_ajax' => array(

					'label'						=>	__('Use AJAX', 'ws-form'),
					'type'						=>	'checkbox',
					'mask'						=>	'data-cascade-ajax',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked %s will retrieve data using AJAX. This can improve performance with larger datasets.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'select_cascade_ajax_option_text_loading' => array(

					'label'						=>	__('Loading Placeholder', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Loading...'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'select_cascade_ajax',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'checkbox_field_label' => array(

					'label'						=>	__('Labels', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the checkbox labels.', 'ws-form')
				),

				'checkbox_field_value' => array(

					'label'						=>	__('Values', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the checkbox values. These values should be unique.', 'ws-form')
				),

				'checkbox_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'checkbox_min' => array(

					'label'						=>	__('Minimum Checked', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0
				),

				'checkbox_max' => array(

					'label'						=>	__('Maximum Checked', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	'',
					'min'						=>	0,
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'select_all',
							'meta_value'		=>	'on'
						)
					)
				),

				'checkbox_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'checkbox_cascade_field_id' => array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'checkbox_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'checkbox_cascade_field_filter_comma' => array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'checkbox_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked and the filter value does not match any data in your filter column, all options will be shown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'radio_field_label' => array(

					'label'						=>	__('Labels', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the radio labels.', 'ws-form')

				),

				'radio_field_value' => array(

					'label'						=>	__('Values', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the radio values. These values should be unique.', 'ws-form')
				),

				'radio_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'radio_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'radio_cascade_field_id' => array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'radio_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'radio_cascade_field_filter_comma' => array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'radio_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked and the filter value does not match any data in your filter column, all radios will be shown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'data_grid_rows_randomize' => array(

					'label'						=>	__('Randomize Rows', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'data_source_term_hierarchy',
							'meta_value'		=>	'on'
						)
					)
				),
				'select_price_field_label' => array(

					'label'						=>	__('Label', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the option labels.', 'ws-form')
				),

				'select_price_field_value'		=> array(

					'label'						=>	__('Value', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select_price',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the option values. These values should be unique.', 'ws-form')
				),

				'select_price_field_price' => array(

					'label'						=>	__('Price', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select_price',
					'default'					=>	1,
					'html_encode'				=>	true,
					'price'						=>	true,
					'help'						=>	__('Choose which column to use for the price.', 'ws-form')

				),

				'select_price_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'price_select_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'price_select_cascade_field_id' => array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_select_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_select_price',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_select_cascade_field_filter_comma' => array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_select_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked and the filter value does not match any data in your filter column, all options will be shown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_select_cascade_option_text_no_rows' => array(

					'label'						=>	__('No Results Placeholder', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Select...'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade_no_match',
							'meta_value'		=>	'',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'price_select_cascade_ajax' => array(

					'label'						=>	__('Use AJAX', 'ws-form'),
					'type'						=>	'checkbox',
					'mask'						=>	'data-cascade-ajax',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked %s will retrieve data using AJAX. This can improve performance with larger datasets.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_select_cascade_ajax_option_text_loading' => array(

					'label'						=>	__('Loading Placeholder', 'ws-form'),
					'type'						=>	'text',
					'default'					=>	'',
					'placeholder'				=>	__('Loading...'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade',
							'meta_value'		=>	'on'
						),

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_select_cascade_ajax',
							'meta_value'		=>	'on',
							'logic_previous'	=>	'&&'
						)
					)
				),

				'checkbox_price_field_label' => array(

					'label'						=>	__('Label', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the checkbox labels.', 'ws-form')
				),

				'checkbox_price_field_value' => array(

					'label'						=>	__('Value', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox_price',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the checkbox values. These values should be unique.', 'ws-form')
				),

				'checkbox_price_field_price' => array(

					'label'						=>	__('Price', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox_price',
					'default'					=>	1,
					'html_encode'				=>	true,
					'price'						=>	true,
					'help'						=>	__('Choose which column to use for the price.', 'ws-form')

				),

				'checkbox_price_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'price_checkbox_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'price_checkbox_cascade_field_id'		=> array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_checkbox_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_checkbox',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_checkbox_cascade_field_filter_comma' => array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_checkbox_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked and the filter value does not match any data in your filter column, all options will be shown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_checkbox_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'radio_price_field_label' => array(

					'label'						=>	__('Label', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for the radio labels.', 'ws-form')
				),

				'radio_price_field_value' => array(

					'label'						=>	__('Value', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio_price',
					'default'					=>	0,
					'html_encode'				=>	true,
					'help'						=>	__('Choose which column to use for the radio values. These values should be unique.', 'ws-form')
				),

				'radio_price_field_price' => array(

					'label'						=>	__('Price', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio_price',
					'default'					=>	1,
					'html_encode'				=>	true,
					'price'						=>	true,
					'help'						=>	__('Choose which column to use for the price.', 'ws-form')
				),

				'radio_price_field_parse_variable' => array(

					'label'						=>	__('Action Variables', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio_price',
					'default'					=>	0,
					'help'						=>	__('Choose which column to use for variables in actions (e.g. #field or #email_submission in email or message actions).', 'ws-form')
				),

				'price_radio_cascade' => array(

					'label'						=>	__('Enable', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Filter this data grid using a value from another field.', 'ws-form')
				),

				'price_radio_cascade_field_id' => array(

					'label'						=>	__('Filter Value', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	'fields',
					'options_blank'				=>	__('Select...', 'ws-form'),
					'fields_filter_type'		=>	array('select', 'price_select', 'checkbox', 'price_checkbox', 'radio', 'price_radio', 'range', 'price_range', 'text', 'number', 'rating'),
					'help'						=>	__('Select the field to use as the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_radio_cascade_field_filter' => array(

					'label'						=>	__('Filter Column', 'ws-form'),
					'type'						=>	'data_grid_field',
					'data_grid'					=>	'data_grid_radio_price',
					'default'					=>	0,
					'help'						=>	__('Select the column to filter with the filter value.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_radio_cascade_field_filter_comma' => array(

					'label'						=>	__('Filter Column - Comma Separate', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will search comma separated values individually.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),

				'price_radio_cascade_no_match' => array(

					'label'						=>	__('Show All If No Results', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked and the filter value does not match any data in your filter column, all radios will be shown.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'price_radio_cascade',
							'meta_value'		=>	'on'
						)
					)
				),
				// Email
				'exclude_email' => array(

					'label'						=>	__('Exclude From Emails', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('If checked, this field will not appear in emails containing the #email_submission variable.', 'ws-form')
				),

				'exclude_email_on' => array(

					'label'						=>	__('Exclude From Emails', 'ws-form'),
					'type'						=>	'checkbox',
					'default'					=>	'on',
					'help'						=>	__('If checked, this field will not appear in emails containing the #email_submission variable.', 'ws-form'),
					'key'						=>	'exclude_email'
				),

				// Exclude from cart total
				'exclude_cart_total' => array(

					'label'						=>	__('Exclude From Cart Total', 'ws-form'),
					'type'						=>	'checkbox',
					'mask'						=>	'data-wsf-exclude-cart-total',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	'',
					'help'						=>	__('If checked, this field will be excluded from the form cart total calculation.', 'ws-form')
				),

				// Custom attributes
				'custom_attributes' => array(

					'type'						=>	'repeater',
					'help'						=>	__('Add additional attributes to this field type.', 'ws-form'),
					'meta_keys'					=>	array(

						'custom_attribute_name',
						'custom_attribute_value'
					)
				),

				// Custom attributes - Name
				'custom_attribute_name' => array(

					'label'							=>	__('Name', 'ws-form'),
					'type'							=>	'text'
				),

				// Custom attributes - Value
				'custom_attribute_value' => array(

					'label'							=>	__('Value', 'ws-form'),
					'type'							=>	'text'
				),
				// Rating - Size
				'rating_max' => array(

					'label'						=>	__('Maximum Rating', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	5,
					'min'						=>	1
				),

				// Rating - Icon
				'rating_icon' => array(

					'label'						=>	__('Icon', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => 'check', 	'text' => __('Check', 'ws-form')),
						array('value' => 'circle', 	'text' => __('Circle', 'ws-form')),
						array('value' => 'flag', 	'text' => __('Flag', 'ws-form')),
						array('value' => 'heart', 	'text' => __('Heart', 'ws-form')),
						array('value' => 'smiley', 	'text' => __('Smiley', 'ws-form')),
						array('value' => 'square', 	'text' => __('Square', 'ws-form')),
						array('value' => 'star', 	'text' => __('Star', 'ws-form')),
						array('value' => 'thumb', 	'text' => __('Thumbs Up', 'ws-form')),
						array('value' => 'custom', 	'text' => __('Custom HTML', 'ws-form'))
					),
					'default'					=>	'star'
				),

				// Rating - Icon - HTML
				'rating_icon_html' => array(

					'label'						=>	__('HTML', 'ws-form'),
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'html_editor',
					'default'					=>	'<span>*</span>',
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'rating_icon',
							'meta_value'		=>	'custom'
						)
					),
					'help'						=>	__('Custom rating icon HTML.', 'ws-form')
				),

				// Rating - Size
				'rating_size' => array(

					'label'						=>	__('Size (Pixels)', 'ws-form'),
					'type'						=>	'number',
					'default'					=>	24,
					'min'						=>	1,
					'help'						=>	__('Size of unselected rating icons in pixels.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'rating_icon',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Rating - Color - Off
				'rating_color_off' => array(

					'label'						=>	__('Unselected Color', 'ws-form'),
					'mask'						=>	'data-rating-color-off="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'default'					=>	WS_Form_Common::option_get('skin_color_default_lighter'),
					'help'						=>	__('Color of unselected rating icons.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'rating_icon',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Rating - Color - On
				'rating_color_on' => array(

					'label'						=>	__('Selected Color', 'ws-form'),
					'mask'						=>	'data-rating-color-on="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'color',
					'default'					=>	WS_Form_Common::option_get('skin_color_warning'),
					'help'						=>	__('Color of selected rating icons.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'rating_icon',
							'meta_value'		=>	'custom'
						)
					)
				),

				// Google map - Latitude
				'google_map_lat' => array(

					'label'						=>	__('Latitude', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'',
					'help'						=>	__('Enter the initial latitude for the map.', 'ws-form')
				),

				// Google map - Longitude
				'google_map_lng' => array(

					'label'						=>	__('Longitude', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'',
					'help'						=>	__('Enter the initial longitude for the map.', 'ws-form')
				),

				// Google map - Zoom
				'google_map_zoom' => array(

					'label'						=>	__('Zoom', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'14',
					'placeholder'				=>	'14',
					'help'						=>	sprintf('%s <a href="https://developers.google.com/maps/documentation/javascript/overview#zoom-levels" target="_blank">%s</a>', __('Enter the initial zoom for the map (1 = World, 20 = Building).', 'ws-form'), __('Learn more', 'ws-form'))
				),

				// Google map - Type
				'google_map_type' => array(

					'label'						=>	__('Type', 'ws-form'),
					'type'						=>	'select',
					'options'					=>	array(

						array('value' => 'roadmap', 'text' => __('Road Map', 'ws-form')),
						array('value' => 'satellite', 'text' => __('Satellite', 'ws-form')),
						array('value' => 'hybrid', 'text' => __('Hybrid', 'ws-form')),
						array('value' => 'terrain', 'text' => __('Terrain', 'ws-form'))
					),
					'default'					=>	'roadmap',
					'help'						=>	__('Choose the type of map to show.', 'ws-form')
				),

				// Google map - Height
				'google_map_height' => array(

					'label'						=>	__('Height', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'56.25%',
					'placeholder'				=>	'56.25%',
					'help'						=>	__('Enter the height of the map. You can enter a percentage (e.g. 56.25% for 16:9 or 100% for square) or pixels (e.g. 400px).', 'ws-form')
				),

				// Google map - Style
				'google_map_style' => array(

					'label'						=>	__('Style', 'ws-form'),
					'type'						=>	'html_editor',
					'select_list'				=>	true,
					'mode'						=>	'javascript',
					'help'						=>	__('Apply customized styling through an embedded JSON style declaration. Enter your JavaScript style array here.', 'ws-form')
				),

				// Google map - Search field
				'google_map_search_field_id' => array(

					'label'							=>	__('Search field', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	'fields',
					'options_blank'					=>	__('Select...', 'ws-form'),
					'fields_filter_type'			=>	array('text', 'textarea', 'select', 'hidden', 'search'),
					'help'							=>	__('Choose a field to act as an address search input.', 'ws-form')
				),

				// Google map - Control - Type
				'google_map_control_type' => array(

					'label'							=>	__('Map Type', 'ws-form'),
					'type'							=>	'checkbox',
					'default'						=>	'on'
				),

				// Google map - Control - Full screen
				'google_map_control_full_screen' => array(

					'label'							=>	__('Full Screen', 'ws-form'),
					'type'							=>	'checkbox',
					'default'						=>	'on'
				),

				// Google map - Control - Street view
				'google_map_control_street_view' => array(

					'label'							=>	__('Street View', 'ws-form'),
					'type'							=>	'checkbox',
					'default'						=>	'on'
				),

				// Google map - Control - Zoom
				'google_map_control_zoom' => array(

					'label'							=>	__('Zoom', 'ws-form'),
					'type'							=>	'checkbox',
					'default'						=>	'on'
				),

				// Google map - Marker - Icon - Title
				'google_map_marker_icon_title' => array(

					'label'						=>	__('Icon Title', 'ws-form'),
					'mask'						=>	'data-google-map-marker-icon-title="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	__('Selected location', 'ws-form'),
					'help'						=>	__('Enter the title of the marker.', 'ws-form')
				),

				// Google map - Marker - Icon - URL
				'google_map_marker_icon_url' => array(

					'label'						=>	__('Icon URL', 'ws-form'),
					'mask'						=>	'data-google-map-marker-icon-url="#value"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'',
					'help'						=>	__('Enter the URL to an icon.', 'ws-form')
				),

				// Google map - Notice
				'google_map_not_enabled' => array(

					'type'						=>	'html',
					'html'						=>	sprintf(__('To use Google Maps on your form, you need to enter your Google API Key <a href="%s">here</a>', 'ws-form'), WS_Form_Common::get_admin_url('ws-form-settings', false, 'tab=advanced')),
					'option_check'				=>	'api_key_google_map'
				),

				// Google address - Field mapping
				'google_address_field_mapping' => array(

					'label'							=>	__('Field Mapping', 'ws-form'),
					'type'							=>	'repeater',
					'meta_keys'					=>	array(

						'google_address_component',
						'ws_form_field_google_address'
					),
					'meta_keys_unique'			=>	array(

						'ws_form_field'
					),
					'default'					=> array(

						array(
							'google_address_component' => 'street_full_long',
							'ws_form_field' => ''
						),

						array(
							'google_address_component' => 'locality_long',
							'ws_form_field' => ''
						),

						array(
							'google_address_component' => 'aal1_short',
							'ws_form_field' => ''
						),

						array(
							'google_address_component' => 'postal_code_full_long',
							'ws_form_field' => ''
						),

						array(
							'google_address_component' => 'country_short',
							'ws_form_field' => ''
						)
					),
					'help'							=>	__('Map Google Places address components to your fields. Note that some countries may not return all components.', 'ws-form')
				),

				// Google address - Form field
				'ws_form_field_google_address' => array(

					'label'							=>	__('Form Field', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	'fields',
					'options_blank'					=>	__('Select...', 'ws-form'),
					'fields_filter_type'			=>	array('googleaddress', 'text', 'textarea', 'select'),
					'fields_filter_include_self'	=>	true,
					'key'							=>	'ws_form_field'
				),

				// Google address - Administrative area level 1 (State)
				'google_address_component' => array(

					'label'							=>	__('Component', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	array(

						array('value' => 'aal1_short', 'text' => __('AAL1 (State) - Short', 'ws-form')),
						array('value' => 'aal1_long', 'text' => __('AAL1 (State) - Long', 'ws-form')),
						array('value' => 'aal2_short', 'text' => __('AAL2 (County) - Short', 'ws-form')),
						array('value' => 'aal2_long', 'text' => __('AAL2 (County)- Long', 'ws-form')),
						array('value' => 'country_short', 'text' => __('Country - Short', 'ws-form')),
						array('value' => 'country_long', 'text' => __('Country - Long', 'ws-form')),
						array('value' => 'locality_short', 'text' => __('Locality (City) - Short', 'ws-form')),
						array('value' => 'locality_long', 'text' => __('Locality (City) - Long', 'ws-form')),
						array('value' => 'neighborhood_short', 'text' => __('Neighborhood - Short', 'ws-form')),
						array('value' => 'neighborhood_long', 'text' => __('Neighborhood - Long', 'ws-form')),
						array('value' => 'postal_code_short', 'text' => __('Postal Code (Zip) - Short', 'ws-form')),
						array('value' => 'postal_code_long', 'text' => __('Postal Code (Zip) - Long', 'ws-form')),
						array('value' => 'postal_code_suffix_short', 'text' => __('Postal Code (Zip) Suffix - Short', 'ws-form')),
						array('value' => 'postal_code_suffix_long', 'text' => __('Postal Code (Zip) Suffix - Long', 'ws-form')),
						array('value' => 'postal_code_full_short', 'text' => __('Postal Code (Zip) Full - Short', 'ws-form')),
						array('value' => 'postal_code_full_long', 'text' => __('Postal Code (Zip) Full - Long', 'ws-form')),
						array('value' => 'route_short', 'text' => __('Route (Street) - Short', 'ws-form')),
						array('value' => 'route_long', 'text' => __('Route (Street) - Long', 'ws-form')),
						array('value' => 'street_full_short', 'text' => __('Street full - Short', 'ws-form')),
						array('value' => 'street_full_long', 'text' => __('Street full - Long', 'ws-form')),
						array('value' => 'street_number_short', 'text' => __('Street number - Short', 'ws-form')),
						array('value' => 'street_number_long', 'text' => __('Street number - Long', 'ws-form')),
						array('value' => 'sublocality_short', 'text' => __('Sublocality - Short', 'ws-form')),
						array('value' => 'sublocality_long', 'text' => __('Sublocality - Long', 'ws-form')),
						array('value' => 'subpremise_short', 'text' => __('Subpremise - Short', 'ws-form')),
						array('value' => 'subpremise_long', 'text' => __('Subpremise - Long', 'ws-form')),
						array('value' => 'lat', 'text' => __('Latitude', 'ws-form')),
						array('value' => 'lng', 'text' => __('Longitude', 'ws-form')),
						array('value' => 'lat_lng', 'text' => __('Latitude,Longitude', 'ws-form')),
						array('value' => 'formatted_address', 'text' => __('Formatted Address', 'ws-form')),
						array('value' => 'formatted_phone_number', 'text' => __('Formatted Phone Number', 'ws-form')),
						array('value' => 'international_phone_number', 'text' => __('International Phone Number', 'ws-form')),
						array('value' => 'name', 'text' => __('Name', 'ws-form')),
						array('value' => 'rating', 'text' => __('Rating', 'ws-form')),
						array('value' => 'url', 'text' => __('URL', 'ws-form')),
						array('value' => 'vicinity', 'text' => __('Vicinity', 'ws-form')),
						array('value' => 'website', 'text' => __('Website', 'ws-form')),
						array('value' => 'place_id', 'text' => __('Place ID', 'ws-form'))
					),
					'options_blank'					=>	__('Select...', 'ws-form'),
					'fields_filter_type'			=>	array('text', 'textarea', 'select', 'hidden')
				),

				// Google address - Map
				'google_address_map' => array(

					'label'							=>	__('Google Map', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	'fields',
					'options_blank'					=>	__('Select...', 'ws-form'),
					'fields_filter_type'			=>	array('googlemap'),
					'help'							=>	__('Choose the Google Map field to center with the address.', 'ws-form')
				),

				// Google address - Map - Zoom
				'google_address_map_zoom' => array(

					'label'						=>	__('Google Map Zoom', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'default'					=>	'14',
					'help'						=>	sprintf('%s <a href="https://developers.google.com/maps/documentation/javascript/overview#zoom-levels" target="_blank">%s</a>', __('Enter the zoom for the map (1 = World, 20 = Building). Leave blank for no change.', 'ws-form'), __('Learn more', 'ws-form'))
				),

				// Google address - Restrict by country
				'google_address_restriction_country' => array(

					'label'						=>	__('Country Restrictions', 'ws-form'),
					'type'						=>	'repeater',
					'meta_keys'					=>	array(

						'country_alpha_2'
					)
				),

				// Google address - Restrict to type
				'google_address_restriction_business' => array(

					'label'						=>	__('Result Type', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('All', 'ws-form')),
						array('value' => 'address', 'text' => __('Addresses', 'ws-form')),
						array('value' => 'establishment', 'text' => __('Businesses', 'ws-form')),
						array('value' => '(cities)', 'text' => __('Cities', 'ws-form')),
						array('value' => '(regions)', 'text' => __('Regions', 'ws-form'))
					),
					'help'						=>	__('If selected, the Google Place Autocomplete service will only return results of this type.', 'ws-form')
				),
				'country_alpha_2'	=> array(

					'label'							=>	__('Country', 'ws-form'),
					'type'							=>	'select',
					'options'						=>	array(),
					'options_blank'					=>	__('Select...', 'ws-form')
				),

				'prepend' => array(

					'label'						=>	__('Prefix', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_textarea',
							'meta_value'		=>	''
						)
					)
				),

				'append' => array(

					'label'						=>	__('Suffix', 'ws-form'),
					'type'						=>	'text',
					'select_list'				=>	true,
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'input_type_textarea',
							'meta_value'		=>	''
						)
					)
				),

				// Allow or Deny
				'allow_deny' => array(

					'label'						=>	__('Method', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('None')),
						array('value' => 'allow', 'text' => __('Allow')),
						array('value' => 'deny', 'text' => __('Deny'))
					),
					'help'						=>	__('Allow or deny email addresses in this field. Use * as a wildcard, e.g. *@wsform.com')
				),

				'allow_deny_values'	=> array(

					'type'						=>	'repeater',
					'meta_keys'					=>	array(

						'allow_deny_value'
					),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'allow_deny',
							'meta_value'		=>	''
						)
					)
				),

				'allow_deny_value' => array(

					'label'							=>	__('Email Address', 'ws-form'),
					'type'							=>	'text'
				),

				'allow_deny_message' => array(

					'label'						=>	__('Message', 'ws-form'),
					'placeholder'				=>	__('The email address entered is not allowed.', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Enter a message to be shown if the email address entered is not allowed.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'!=',
							'meta_key'			=>	'allow_deny',
							'meta_value'		=>	''
						)
					)
				),

				// Transform
				'transform' => array(

					'label'						=>	__('Transform', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'mask'						=>	'data-wsf-transform="#value"',
					'mask_disregard_on_empty'	=>	true,
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'uc', 'text' => __('Uppercase', 'ws-form')),
						array('value' => 'lc', 'text' => __('Lowercase', 'ws-form')),
						array('value' => 'capitalize', 'text' => __('Capitalize', 'ws-form')),
						array('value' => 'sentence', 'text' => __('Sentence', 'ws-form'))
					),
					'help'						=>	__('Transform the field input.', 'ws-form')
				),

				// Deduplication
				'dedupe' => array(

					'label'						=>	__('No Submission Duplicates', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('If checked, %s will check for duplicates in existing submissions. This feature is not available if you are encrypting submission data.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'dedupe_period' => array(

					'label'						=>	__('Within', 'ws-form'),
					'type'						=>	'select',
					'default'					=>	'',
					'options'					=>	array(

						array('value' => '', 'text' => __('All Time', 'ws-form')),
						array('value' => 'hour', 'text' => __('Past Hour', 'ws-form')),
						array('value' => 'day', 'text' => __('Past Day', 'ws-form')),
						array('value' => 'day_current', 'text' => __('Current Day', 'ws-form')),
						array('value' => 'week', 'text' => __('Past Week', 'ws-form')),
						array('value' => 'month', 'text' => __('Past Month', 'ws-form')),
						array('value' => 'year', 'text' => __('Past Year', 'ws-form'))
					),
					'help'						=>	__('Choose a period in which to check for duplicates.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'dedupe',
							'meta_value'		=>	'on'
						)
					)
				),

				'dedupe_message' => array(

					'label'						=>	__('Message', 'ws-form'),
					'placeholder'				=>	__('The value entered for #label_lowercase has already been used.', 'ws-form'),
					'type'						=>	'textarea',
					'help'						=>	__('Enter a message to be shown if a duplicate value is entered for this field. Leave blank for the default message.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'				=>	'==',
							'meta_key'			=>	'dedupe',
							'meta_value'		=>	'on'
						)
					)
				),

				'dedupe_value_scope' => array(

					'label'						=>	__('No Duplicates in Repeatable Sections', 'ws-form'),
					'mask'						=>	'data-value-scope="repeatable-section"',
					'mask_disregard_on_empty'	=>	true,
					'type'						=>	'checkbox',
					'default'					=>	'',
					'help'						=>	__('Disable values already chosen in repeatable sections.', 'ws-form')
				),

				// Hidden (Never rendered but either have default values or are special attributes)
				'breakpoint' => array(

					'default'					=>	25
				),

				'tab_index' => array(

					'default'					=>	0
				),

				'sub_type' => array(

					'type'					=>	'text',
					'default'				=>	''
				),

				'list' => array(

					'mask'						=>	'list="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_disregard_on_zero'	=>	false,
				),

				'aria_label' => array(

					'label'						=>	__('ARIA Label', 'ws-form'),
					'mask'						=>	'aria-label="#value"',
					'mask_disregard_on_empty'	=>	true,
					'mask_placeholder'			=>	'#label',
					'compatibility_id'			=>	'wai-aria',
					'select_list'				=>	true
				),

				'aria_labelledby' => array(

					'mask'						=>	'aria-labelledby="#value"',
					'mask_disregard_on_empty'	=>	true
				),

				'aria_describedby' => array(

					'mask'						=>	'aria-describedby="#value"',
					'mask_disregard_on_empty'	=>	true
				),

				'class' => array(

					'mask'						=>	'class="#value"',
					'mask_disregard_on_empty'	=>	true,
				),

				'default' => array(

					'mask'						=>	'#value',
					'mask_disregard_on_empty'	=>	true,
				)
			);

			// Add data grid meta keys
			$meta_keys = array_merge($meta_keys, self::get_meta_keys_data_grids());

			// Autocomplete
			foreach($autocomplete_control_groups as $id => $autocomplete_control_group) {

				// Autocomplete - Hidden
				$meta_keys[$id] = array(

					'label'						=>	__('Auto Complete', 'ws-form'),
					'type'						=>	'select',
					'mask'						=>	'autocomplete="#value"',
					'mask_disregard_on_empty'	=>	true,
					'default'					=>	isset($autocomplete_control_group['default']) ? $autocomplete_control_group['default'] : '',
					'options'					=>	$$id,
					'options_blank'				=>	__('Select...', 'ws-form'),
					'help'						=>	__('Informs the browsers how to autocomplete this field.', 'ws-form'),
					'compatibility_id'			=>	'mdn-html_global_attributes_autocomplete',
					'key'						=>	'autocomplete'
				);
			}

			// File handlers
			foreach(WS_Form_File_Handler::$file_handlers as $file_handler) {

				$meta_keys['file_handler']['options'][] = array('value' => $file_handler->id, 'text' => $file_handler->label);
			}

			// Text editor types
			global $wp_version;
			if(WS_Form_Common::version_compare($wp_version, '4.8') >= 0) {
				$meta_keys['input_type_textarea']['options'][] = array('value' => 'tinymce', 'text' => __('Visual Editor', 'ws-form'));
			}
			if(WS_Form_Common::version_compare($wp_version, '4.9') >= 0) {
				$meta_keys['input_type_textarea']['options'][] = array('value' => 'html', 'text' => __('HTML Editor', 'ws-form'));
			}

			// Add mime types to accept
			$file_types = self::get_file_types();
			$mime_select_list = array();
			foreach($file_types as $mime_type => $file_type) {

				if($mime_type == 'default') { continue; }
				$mime_select_list[] = array('text' => $mime_type, 'value' => $mime_type);
			}
			usort($mime_select_list, function($a, $b) { if ($a['text'] == $b['text']) { return 0; } return ($a['text'] < $b['text']) ? -1 : 1; });
			$meta_keys['accept']['select_list'] = $mime_select_list;

			// Date format
			$date_formats = array_unique(apply_filters('date_formats', array('F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y')));
			foreach($date_formats as $date_format) {

				$meta_keys['format_date']['options'][] = array('value' => esc_attr($date_format), 'text' => date_i18n($date_format));	
			}

			// Time format
			$time_formats = array_unique(apply_filters( 'time_formats', array('g:i a', 'g:i A', 'H:i')));
			foreach($time_formats as $time_format) {

				$meta_keys['format_time']['options'][] = array('value' => esc_attr($time_format), 'text' => date_i18n($time_format));	
			}
			// User roles
			$capabilities = array();
			if (!function_exists('get_editable_roles')) {

				require_once(ABSPATH . '/wp-admin/includes/user.php');
			}
			$roles = get_editable_roles();
			uasort($roles, function($role_a, $role_b) {

				return ($role_a['name'] == $role_b['name']) ? 0 : (($role_a['name'] < $role_b['name']) ? -1 : 1);
			});
			foreach ($roles as $role => $role_config) {

				$meta_keys['form_user_roles']['options'][] = array('value' => esc_attr($role), 'text' => esc_html(translate_user_role($role_config['name'])));
				$meta_keys['group_user_roles']['options'][] = array('value' => esc_attr($role), 'text' => esc_html(translate_user_role($role_config['name'])));
				$meta_keys['section_user_roles']['options'][] = array('value' => esc_attr($role), 'text' => esc_html(translate_user_role($role_config['name'])));
				$meta_keys['field_user_roles']['options'][] = array('value' => esc_attr($role), 'text' => esc_html(translate_user_role($role_config['name'])));

				$capabilities = array_merge($capabilities, array_keys($role_config['capabilities']));
			}

			// User capabilities
			$capabilities = array_unique($capabilities);
			sort($capabilities);
			foreach ($capabilities as $capability) {

				$meta_keys['form_user_capabilities']['options'][] = array('value' => esc_attr($capability), 'text' => esc_html($capability));
				$meta_keys['group_user_capabilities']['options'][] = array('value' => esc_attr($capability), 'text' => esc_html($capability));
				$meta_keys['section_user_capabilities']['options'][] = array('value' => esc_attr($capability), 'text' => esc_html($capability));
				$meta_keys['field_user_capabilities']['options'][] = array('value' => esc_attr($capability), 'text' => esc_html($capability));
			}

			// Data source update frequencies

			// Add real-time
			$meta_keys['data_source_recurrence']['options'][] = array('value' => 'wsf_realtime', 'text' => __('Real-Time'));

			// Get registered schedules
			$schedules = wp_get_schedules();

			// Order by interval
			uasort($schedules, function ($schedule_1, $schedule_2) {
				if ($schedule_1['interval'] == $schedule_2['interval']) return 0;
				return $schedule_1['interval'] < $schedule_2['interval'] ? -1 : 1;
			});

			// IDs to include (also includes any schedule ID's beginning with wsf_)
			$wordpress_schedule_ids = array('hourly', 'twicedaily', 'daily', 'weekly');

			// Process schedules
			foreach($schedules as $schedule_id => $schedule_config) {

				if(
					!in_array($schedule_id, $wordpress_schedule_ids) &&
					(strpos($schedule_id, WS_FORM_DATA_SOURCE_SCHEDULE_ID_PREFIX) === false)
				) {
					continue;
				}

				$meta_keys['data_source_recurrence']['options'][] = array('value' => esc_attr($schedule_id), 'text' => esc_html($schedule_config['display']));
			}

			// Process countries alpha 2
			if(!$public) {

				$countries_alpha_2 = self::get_countries_alpha_2();

				foreach($countries_alpha_2 as $value => $text) {

					$meta_keys['country_alpha_2']['options'][] = array('value' => esc_attr($value), 'text' => esc_html($text));
					$meta_keys['intl_tel_input_initial_country']['options'][] = array('value' => esc_attr($value), 'text' => esc_html($text));
				}
			}

			// Apply filter
			$meta_keys = apply_filters('wsf_config_meta_keys', $meta_keys, $form_id);

			// Public parsing (To cut down on only output needed to render form
			if($public) {

				// Remove protected meta keys
				$meta_keys_protected = array_fill_keys(WS_Form_Config::get_meta_keys_protected(), null);

				foreach(array_intersect_key($meta_keys, $meta_keys_protected) as $key => $meta_key) {

					unset($meta_keys[$key]);
				}

				// Remove meta keys that don't contain any meta data we can use publicly
				$public_attributes_public = array('key' => 'k', 'mask' => 'm', 'mask_disregard_on_empty' => 'e', 'mask_disregard_on_zero' => 'z', 'mask_placeholder' => 'p', 'html_encode' => 'h', 'price' => 'pr', 'default' => 'd', 'field_part' => 'c');

				foreach($meta_keys as $key => $meta_key) {

					$meta_key_keep = false;

					foreach($public_attributes_public as $attribute => $attribute_public) {

						if(isset($meta_keys[$key][$attribute])) {

							$meta_key_keep = true;
							break;
						}
					}

					if(!$meta_key_keep) { unset($meta_keys[$key]); }
				}

				$meta_keys_new = array();

				foreach($meta_keys as $key => $meta_key) {

					$meta_key_source = $meta_keys[$key];
					$meta_key_new = array();

					foreach($public_attributes_public as $attribute => $attribute_public) {

						if(isset($meta_key_source[$attribute])) {

							unset($meta_key_new[$attribute]);
							$meta_key_new[$attribute_public] = $meta_key_source[$attribute];
						}
					}

					$meta_keys_new[$key] = $meta_key_new;
				}

				$meta_keys = $meta_keys_new;
			}

			// Parse compatibility meta_keys
			if(!$public) {

				foreach($meta_keys as $key => $meta_key) {

					if(isset($meta_key['compatibility_id'])) {

						$meta_keys[$key]['compatibility_url'] = str_replace('#compatibility_id', $meta_key['compatibility_id'], WS_FORM_COMPATIBILITY_MASK);
						unset($meta_keys[$key]['compatibility_id']);
					}
				}
			}

			// Cache
			self::$meta_keys[$public] = $meta_keys;

			return $meta_keys;
		}

		// Configuration - Meta Keys - Protected
		public static function get_meta_keys_protected() {

			return apply_filters('wsf_config_meta_keys_protected', array(

				// reCAPTCHA
				'recaptcha_secret_key',

				// hCAPTCHA
				'hcaptcha_secret_key',

				// Turnstile
				'turnstile_secret_key'
			));
		}

		// Configuration - Meta Keys - Data Grids
		public static function get_meta_keys_data_grids() {

			$meta_keys = array(

				'data_grid_datalist' => array(

					'label'					=>	__('Datalist', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	false,	// Is the default attribute supported on rows?
					'row_disabled'			=>	false,	// Is the disabled attribute supported on rows?
					'row_required'			=>	false,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'groups_label'			=>	false,	// Is the group label feature enabled?
					'groups_label_render'	=>	false,	// Is the group label render feature enabled?
					'groups_auto_group'		=>	false,	// Is auto group feature enabled?
					'groups_disabled'		=>	false,	// Is the disabled attribute supported on groups?
					'groups_group'			=>	false,	// Can user add groups?
					'mask_group'			=>	false,	// Is the group mask supported?
					'field_wrapper'			=>	false,
					'upload_download'		=>	true,
					'compatibility_id'		=>	'datalist',

					'meta_key_value'		=>	'datalist_field_value',
					'meta_key_label'		=>	'datalist_field_text',
					'data_source'			=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Value', 'ws-form')),
							array('id' => 1, 'label' => __('Label', 'ws-form'))
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Values', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array()
							)
						)
					)
				),

				'data_grid_select' => array(

					'label'					=>	__('Options', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	false,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	false,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Optgroup', 'ws-form'),

					'field_wrapper'			=>	false,
					'meta_key_value'			=>	'select_field_value',
					'meta_key_label'			=>	'select_field_label',
					'meta_key_parse_variable'	=>	'select_field_parse_variable',
					'data_source'			=>	true,

					'upload_download'		=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Options', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Option 1', 'ws-form'))
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Option 2', 'ws-form'))
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'disabled'	=> '',
										'required'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Option 3', 'ws-form'))
									)
								)
							)
						)
					)
				),

				'data_grid_checkbox' => array(

					'label'					=>	__('Checkboxes', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	true,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'row_default_multiple'	=>	true,	// Can multiple defaults be selected?
					'row_required_multiple'	=>	true,	// Can multiple requires be selected?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	true,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Fieldset', 'ws-form'),

					'field_wrapper'				=>	false,
					'upload_download'			=>	true,
					'meta_key_value'			=>	'checkbox_field_value',
					'meta_key_label'			=>	'checkbox_field_label',
					'meta_key_parse_variable'	=>	'checkbox_field_parse_variable',
					'data_source'				=>	true,
					'insert_image'				=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form'))
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Checkboxes', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',
								'label_render'	=> 'on',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(

									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Checkbox 1', 'ws-form'))
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Checkbox 2', 'ws-form'))
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Checkbox 3', 'ws-form'))
									)
								)
							)
						)
					)
				),

				'data_grid_radio' =>	array(

					'label'					=>	__('Radios', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	false,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'row_default_multiple'	=>	false,	// Can multiple defaults be selected?
					'row_required_multiple'	=>	false,	// Can multiple requires be selected?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	true,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Fieldset', 'ws-form'),

					'field_wrapper'			=>	false,
					'upload_download'		=>	true,
					'meta_key_value'			=>	'radio_field_value',
					'meta_key_label'			=>	'radio_field_label',
					'meta_key_parse_variable'	=>	'radio_field_parse_variable',
					'data_source'			=>	true,
					'insert_image'				=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form'))
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Radios', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',
								'label_render'	=> 'on',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(

									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Radio 1', 'ws-form'))
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Radio 2', 'ws-form'))
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Radio 3', 'ws-form'))
									)
								)
							)
						)
					)
				)
				,'data_grid_select_price' => array(

					'label'					=>	__('Options', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	false,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	false,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Optgroup', 'ws-form'),

					'field_wrapper'			=>	false,
					'upload_download'		=>	true,
					'meta_key_price'			=>	'select_price_field_price',
					'meta_key_value'			=>	'select_price_field_value',
					'meta_key_label'			=>	'select_price_field_label',
					'meta_key_parse_variable'	=>	'select_price_field_parse_variable',
					'data_source'			=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form')),
							array('id' => 1, 'label' => __('Price', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Options', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 1', 'ws-form'), '1')
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 2', 'ws-form'), '2')
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'disabled'	=> '',
										'required'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 3', 'ws-form'), '3')
									)
								)
							)
						)
					)
				),

				'data_grid_checkbox_price' => array(

					'label'					=>	__('Checkboxes', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	true,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'row_default_multiple'	=>	true,	// Can multiple defaults be selected?
					'row_required_multiple'	=>	true,	// Can multiple requires be selected?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	true,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Fieldset', 'ws-form'),

					'field_wrapper'				=>	false,
					'upload_download'			=>	true,
					'meta_key_price'			=>	'checkbox_price_field_price',
					'meta_key_value'			=>	'checkbox_price_field_value',
					'meta_key_label'			=>	'checkbox_price_field_label',
					'meta_key_parse_variable'	=>	'checkbox_price_field_parse_variable',
					'data_source'				=>	true,
					'insert_image'				=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form')),
							array('id' => 1, 'label' => __('Price', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Checkboxes', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',
								'label_render'	=> 'on',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 1', 'ws-form'), '1')
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 2', 'ws-form'), '2')
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'disabled'	=> '',
										'required'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 3', 'ws-form'), '3')
									)
								)
							)
						)
					)
				),

				'data_grid_radio_price'	=> array(

					'label'					=>	__('Radios', 'ws-form'),
					'type'					=>	'data_grid',
					'row_default'			=>	true,	// Is the default attribute supported on rows?
					'row_disabled'			=>	true,	// Is the disabled attribute supported on rows?
					'row_required'			=>	false,	// Is the required attribute supported on rows?
					'row_hidden'			=>	true,	// Is the hidden supported on rows?
					'row_default_multiple'	=>	false,	// Can multiple defaults be selected?
					'row_required_multiple'	=>	false,	// Can multiple requires be selected?
					'groups_label'			=>	true,	// Is the group label feature enabled?
					'groups_label_label'	=>	__('Label', 'ws-form'),
					'groups_label_render'	=>	true,	// Is the group label render feature enabled?
					'groups_label_render_label'	=>	__('Show Label', 'ws-form'),
					'groups_auto_group'		=>	true,	// Is auto group feature enabled?
					'groups_disabled'		=>	true,	// Is the group disabled attribute?
					'groups_group'			=>	true,	// Is the group mask supported?
					'groups_group_label'	=>	__('Wrap In Fieldset', 'ws-form'),

					'field_wrapper'				=>	false,
					'upload_download'			=>	true,
					'meta_key_price'			=>	'radio_price_field_price',
					'meta_key_value'			=>	'radio_price_field_value',
					'meta_key_label'			=>	'radio_price_field_label',
					'meta_key_parse_variable'	=>	'radio_price_field_parse_variable',
					'data_source'				=>	true,
					'insert_image'				=>	true,

					'default'			=>	array(

						// Config
						'rows_per_page'		=>	10,
						'group_index'		=>	0,
						'default'			=>	array(),

						// Columns
						'columns' => array(

							array('id' => 0, 'label' => __('Label', 'ws-form')),
							array('id' => 1, 'label' => __('Price', 'ws-form')),
						),

						// Group
						'groups' => array(

							array(

								'label' 		=> __('Radios', 'ws-form'),
								'page'			=> 0,
								'disabled'		=> '',
								'mask_group'	=> '',
								'label_render'	=> 'on',

								// Rows (Only injected for a new data grid, blank for new groups)
								'rows' 		=> array(
									array(

										'id'		=> 1,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 1', 'ws-form'), '1')
									),
									array(

										'id'		=> 2,
										'default'	=> '',
										'required'	=> '',
										'disabled'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 2', 'ws-form'), '2')
									),
									array(

										'id'		=> 3,
										'default'	=> '',
										'disabled'	=> '',
										'required'	=> '',
										'hidden'	=> '',
										'data'		=> array(__('Product 3', 'ws-form'), '3')
									)
								)
							)
						)
					)
				)
			);

			return $meta_keys;
		}

		// Configuration - Frameworks
		public static function get_frameworks($public = true) {

			// Check cache
			if(isset(self::$frameworks[$public])) { return self::$frameworks[$public]; }

			$frameworks = array(

				'types' => array(

					'ws-form' => array('name' => WS_FORM_NAME_GENERIC),
//					'automaticcss1' => array('name' => 'Automatic.css 1.x'),
					'bootstrap3' => array('name' => 'Bootstrap 3.x'),
					'bootstrap4' => array('name' => 'Bootstrap 4.0'),
					'bootstrap41' => array('name' => 'Bootstrap 4.1-4.6'),
					'bootstrap5' => array('name' => 'Bootstrap 5+'),
					'foundation5' => array('name' => 'Foundation 5.x'),
					'foundation6' => array('name' => 'Foundation 6.0-6.3.1'),
					'foundation64' => array('name' => 'Foundation 6.4+')
				)
			);

			// Load current framework
			$framework = WS_Form_Common::option_get('framework', 'ws-form');

			// Get file path and class name
			switch($framework) {

/*				case 'automaticcss1' :

					$framework_class_suffix = 'Automatic_CSS_1';
					break;
*/
				case 'bootstrap3' :

					$framework_class_suffix = 'Bootstrap_3';
					break;

				case 'bootstrap4' :

					$framework_class_suffix = 'Bootstrap_4';
					break;

				case 'bootstrap41' :

					$framework_class_suffix = 'Bootstrap_4_1';
					break;

				case 'bootstrap5' :

					$framework_class_suffix = 'Bootstrap_5';
					break;

				case 'foundation5' :

					$framework_class_suffix = 'Foundation_5';
					break;

				case 'foundation6' :

					$framework_class_suffix = 'Foundation_6';
					break;

				case 'foundation64' :

					$framework_class_suffix = 'Foundation_64';
					break;

				default :

					$framework = 'ws-form';
					$framework_class_suffix = 'WS_Form';
			}

			// Get framework include file name
			$framework_include_file_name = sprintf('frameworks/class-ws-form-framework-%s.php', $framework);

			// Get framework class name
			$framework_class_name = sprintf('WS_Form_Config_Framework_%s', $framework_class_suffix);

			// Admin icons
			if(!$public) {

				$frameworks['icons'] = array(

					'25'	=>	self::get_icon_24_svg('bp-25'),
					'50'	=>	self::get_icon_24_svg('bp-50'),
					'75'	=>	self::get_icon_24_svg('bp-75'),
					'100'	=>	self::get_icon_24_svg('bp-100'),
					'125'	=>	self::get_icon_24_svg('bp-125'),
					'150'	=>	self::get_icon_24_svg('bp-150')
				);

				// Include WS Form framework regardless
				include_once sprintf('frameworks/class-ws-form-framework-ws-form.php', $framework);
				$ws_form_config_framework_ws_form = new WS_Form_Config_Framework_WS_Form();
				$frameworks['types']['ws-form'] = $ws_form_config_framework_ws_form->get_framework_config();

				// Include current framework
				if($framework !== 'ws-form') {

					include_once $framework_include_file_name;
					$ws_form_config_framework = new $framework_class_name();
					$frameworks['types'][$framework] = $ws_form_config_framework->get_framework_config();
				}

			} else {

				// Include current framework
				include_once $framework_include_file_name;
				$ws_form_config_framework = new $framework_class_name();
				$frameworks['types'][$framework] = $ws_form_config_framework->get_framework_config();

				// Run through framework and remove references to admin
				foreach($frameworks['types'][$framework] as $meta_key => $meta_value) {

					if(is_array($meta_value) && isset($meta_value['admin'])) {

						unset($frameworks['types'][$framework][$meta_key]['admin']);
					}
				}
			}

			// Apply filter
			$frameworks = apply_filters('wsf_config_frameworks', $frameworks, $framework, $public);

			// Public filter
			if($public) {

				// Remove unused frameworks (in case the wsf_config_frameworks filter sets any array elements)
				foreach(array_keys($frameworks['types']) as $type) {

					if($type !== $framework) {

						unset($frameworks['types'][$type]);
					}
				}
			}

			// Cache
			self::$frameworks[$public] = $frameworks;

			return $frameworks;
		}

		// Get analytics
		public static function get_analytics() {

			$analytics = array(

				'google'	=>	array(

					'label'	=>	__('Google Analytics', 'ws-form'),

					'functions'	=> array(

						'gtag'	=> array(

							'label'		=>	'gtag',
							'log_found'	=>	'log_analytics_google_loaded_gtag_js',
							'analytics_event_function' => "gtag('event','#action',{'event_category': '#category', 'event_label': '#label', 'value': #value});"
						),

						'ga'	=> array(

							'label'		=>	'analytics',
							'log_found'	=>	'log_analytics_google_loaded_analytics_js',
							'analytics_event_function' => "var wsf_ga_trackers = ga.getAll(); for(var wsf_ga_tracker_index in wsf_ga_trackers) { if(!wsf_ga_trackers.hasOwnProperty(wsf_ga_tracker_index)) { continue; } wsf_ga_trackers[wsf_ga_tracker_index].send('event', '#category', '#action', '#label', #value); }",
						),

						'_gaq'	=> array(

							'label'		=>	'ga',
							'log_found'	=>	'log_analytics_google_loaded_ga_js',
							'analytics_event_function' => "_gaq.push(['_trackEvent', '#category', '#action', '#label', #value]);"
						),
					)
				),

				'facebook_standard'	=>	array(

					'label'	=>	__('Facebook (Standard)', 'ws-form'),

					'functions'	=> array(

						'fbq'	=> array(

							'label'		=>	'fbevents',
							'log_found'	=>	'log_analytics_facebook_loaded_fbevents_js',
							'analytics_event_function' => "fbq('track','#event'#params);"
						)
					)
				),

				'facebook_custom'	=>	array(

					'label'	=>	__('Facebook (Custom)', 'ws-form'),

					'functions'	=> array(

						'fbq'	=> array(

							'label'		=>	'fbevents',
							'log_found'	=>	'log_analytics_facebook_loaded_fbevents_js',
							'analytics_event_function' => "fbq('trackCustom','#event'#params);"
						)
					)
				),

				'linkedin'	=>	array(

					'label'	=>	__('LinkedIn (Insight Tag)', 'ws-form'),

					'functions'	=> array(

						'js'	=> array(

							'label'		=>	'insight',
							'log_found'	=>	'log_analytics_linkedin_loaded_insight_js',
							'analytics_event_function' => "if(_linkedin_partner_id){var wsf_linkedin_img = document.createElement('img');wsf_linkedin_img.setAttribute('width', 1);wsf_linkedin_img.setAttribute('height', 1);wsf_linkedin_img.setAttribute('style', 'display:none;');wsf_linkedin_img.setAttribute('src', 'https://px.ads.linkedin.com/collect/?pid=' + _linkedin_partner_id + '&conversionId=#conversion_id&fmt=gif');document.body.appendChild(wsf_linkedin_img);}"
   						)
					)
				)
			);

			// Apply filter
			$analytics = apply_filters('wsf_config_analytics', $analytics);

			return $analytics;
		}

		// Get tracking
		public static function get_tracking($public = true) {

			// Check cache
			if(isset(self::$tracking[$public])) { return self::$tracking[$public]; }

			$tracking = array(

				'tracking_remote_ip'	=>	array(

					'label'				=>	__('Remote IP Address', 'ws-form'),
					'server_source'		=>	'http_env',
					'server_http_env'	=>	array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'),
					'type'				=>	'ip',
					'description'		=>	__('Stores the website visitors remote IP address, e.g. 123.45.56.789', 'ws-form')
				),

				'tracking_geo_location'	=>	array(

					'label'				=>	__('Location (By browser)', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_geo_location',
					'client_source'		=>	'geo_location',
					'type'				=>	'latlon',
					'description'		=>	__('If a website visitors device supports geo location (GPS) this option will prompt and request permission for that data and store the latitude and longitude to a submission.', 'ws-form')
				),

				'tracking_ip_lookup_latlon'	=>	array(

					'label'				=>	__('Location (By IP)', 'ws-form'),
					'server_source'		=>	'ip_lookup',
					'server_json_var'	=>	array('geoplugin_latitude', 'geoplugin_longitude'),
					'type'				=>	'latlon',
					'description'		=>	__('This will obtain an approximate latitude and longitude of a website visitor by their IP address.', 'ws-form')
				),

				'tracking_referrer'	=>	array(

					'label'				=>	__('Referrer', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_referrer',
					'client_source'		=>	'referrer',
					'type'				=>	'url',
					'description'		=>	__('Stores the web page address a website visitor was on prior to completing the submitted form.', 'ws-form')
				),

				'tracking_os'	=>	array(

					'label'				=>	__('Operating System', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_os',
					'client_source'		=>	'os',
					'type'				=>	'text',
					'description'		=>	__('Stores the website visitors operating system.', 'ws-form')
				),

				'tracking_agent'		=>	array(

					'label'				=>	__('Agent', 'ws-form'),
					'server_source'		=>	'http_env',
					'server_http_env'	=>	array('HTTP_USER_AGENT'),
					'type'				=>	'text',
					'description'		=>	__('Stores the website visitors agent (browser type).', 'ws-form')
				),

				'tracking_host'	=>	array(

					'label'				=>	__('Hostname', 'ws-form'),
					'server_source'		=>	'http_env',
					'server_http_env'	=>	array('HTTP_HOST', 'SERVER_NAME'),
					'client_source'		=>	'pathname',
					'type'				=>	'text',
					'description'		=>	__('Stores the server hostname.', 'ws-form')

				),

				'tracking_pathname'	=>	array(

					'label'				=>	__('Pathname', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_pathname',
					'client_source'		=>	'pathname',
					'type'				=>	'text',
					'description'		=>	__('Pathname of the URL.', 'ws-form')

				),

				'tracking_query_string'	=>	array(

					'label'				=>	__('Query String', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_query_string',
					'client_source'		=>	'query_string',
					'type'				=>	'text',
					'description'		=>	__('Query string of the URL.', 'ws-form')

				),

				'tracking_ip_lookup_city'	=>	array(

					'label'				=>	__('City (By IP)', 'ws-form'),
					'server_source'		=>	'ip_lookup',
					'server_json_var'	=>	'geoplugin_city',
					'type'				=>	'text',
					'description'		=>	sprintf(

						/* translators: %s = WS Form */
						__('When enabled, %s will perform an IP lookup and obtain the city located closest to their approximate location.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'tracking_ip_lookup_region'	=>	array(

					'label'				=>	__('Region (By IP)', 'ws-form'),
					'server_source'		=>	'ip_lookup',
					'server_json_var'	=>	'geoplugin_region',
					'type'				=>	'text',
					'description'		=>	sprintf(

						/* translators: %s = WS Form */
						__('When enabled, %s will perform an IP lookup and obtain the region located closest to their approximate location.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'tracking_ip_lookup_country'	=>	array(

					'label'				=>	__('Country (By IP)', 'ws-form'),
					'server_source'		=>	'ip_lookup',
					'server_json_var'	=>	'geoplugin_countryName',
					'type'				=>	'text',
					'description'		=>	sprintf(

						/* translators: %s = WS Form */
						__('When enabled, %s will perform an IP lookup and obtain the country located closest to their approximate location.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'tracking_ip_lookup_time_zone'	=>	array(

					'label'				=>	__('Time Zone (By IP)', 'ws-form'),
					'server_source'		=>	'ip_lookup',
					'server_json_var'	=>	'geoplugin_timezone',
					'type'				=>	'text',
					'description'		=>	sprintf(

						/* translators: %s = WS Form */
						__('When enabled, %s will perform an IP lookup and obtain the time zone closest to their approximate location.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					)
				),

				'tracking_utm_source'	=>	array(

					'label'				=>	__('UTM Source', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_utm_source',
					'client_source'		=>	'query_var',
					'client_query_var'	=>	'utm_source',
					'type'				=>	'text',
					'description'		=>	__('This can be used to store the UTM (Urchin Tracking Module) source parameter.', 'ws-form')
				),

				'tracking_utm_medium'	=>	array(

					'label'				=>	__('UTM Medium', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_utm_medium',
					'client_source'		=>	'query_var',
					'client_query_var'	=>	'utm_medium',
					'type'				=>	'text',
					'description'		=>	__('This can be used to store the UTM (Urchin Tracking Module) medium parameter.', 'ws-form')
				),

				'tracking_utm_campaign'	=>	array(

					'label'				=>	__('UTM Campaign', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_utm_campaign',
					'client_source'		=>	'query_var',
					'client_query_var'	=>	'utm_campaign',
					'type'				=>	'text',
					'description'		=>	__('This can be used to store the UTM (Urchin Tracking Module) campaign parameter.', 'ws-form')
				),

				'tracking_utm_term'	=>	array(

					'label'				=>	__('UTM Term', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_utm_term',
					'client_source'		=>	'query_var',
					'client_query_var'	=>	'utm_term',
					'type'				=>	'text',
					'description'		=>	__('This can be used to store the UTM (Urchin Tracking Module) term parameter.', 'ws-form')
				),

				'tracking_utm_content'	=>	array(

					'label'				=>	__('UTM Content', 'ws-form'),
					'server_source'		=>	'query_var',
					'server_query_var'	=>	'wsf_utm_content',
					'client_source'		=>	'query_var',
					'client_query_var'	=>	'utm_content',
					'type'				=>	'text',
					'description'		=>	__('This can be used to store the UTM (Urchin Tracking Module) content parameter.', 'ws-form')
				)
			);

			// Apply filter
			$tracking = apply_filters('wsf_config_tracking', $tracking);

			// Public filtering
			if($public) {

				foreach($tracking as $key => $tracking_config) {

					if(!isset($tracking_config['client_source'])) {

						unset($tracking[$key]);

					} else {

						unset($tracking[$key]['label']);
						unset($tracking[$key]['description']);
						unset($tracking[$key]['type']);
					}
				}
			}

			// Cache
			self::$tracking[$public] = $tracking;

			return $tracking;
		}

		// Parse variables
		public static function get_parse_variables_repairable($public = false) {

			// Check cache
			if(isset(self::$parse_variables_repairable[$public])) { return self::$parse_variables_repairable[$public]; }

			$parse_variables = self::get_parse_variables($public);

			$parse_variables_repairable = array();

			foreach($parse_variables as $parse_variable_group => $parse_variable_group_config) {

				foreach($parse_variable_group_config['variables'] as $parse_variable => $parse_variable_config) {

					if(
						!isset($parse_variable_config['repair_group']) ||
						!isset($parse_variable_config['attributes'])

					) { continue; }

					$repair_group = $parse_variable_config['repair_group'];

					foreach($parse_variable_config['attributes'] as $attribute_config) {

						if(
							isset($attribute_config['id']) &&
							$attribute_config['id'] == 'id'
						) {

							$parse_variables_repairable[$repair_group][] = $parse_variable;
						}
					}
				}
			}

			// Cache
			self::$parse_variables_repairable[$public] = $parse_variables_repairable;

			return $parse_variables_repairable;
		}

		// Parse variable
		public static function get_parse_variables_secure() {

			// Check cache
			if(self::$parse_variables_secure !== false) { return self::$parse_variables_secure; }

			$parse_variables_secure = array();

			// Get admin variables
			$parse_variables_config = self::get_parse_variables();

			foreach($parse_variables_config as $parse_variable_group_id => $parse_variable_group) {

				foreach($parse_variable_group['variables'] as $parse_variable_key => $parse_variables_config) {

					if(
						isset($parse_variables_config['secure']) &&
						$parse_variables_config['secure']
					) {
						$parse_variables_secure[] = $parse_variable_key;
					}
				}
			}

			// Store to cache
			self::$parse_variables_secure = $parse_variables_secure;

			return $parse_variables_secure;
		}

		// Parse variables
		public static function get_parse_variables($public = true) {

			// Check cache
			if(isset(self::$parse_variables[$public])) {

				return self::get_parse_variables_return(self::$parse_variables[$public], $public);
			}

			// Get email logo
			$email_logo = '';
			$action_email_logo = absint(WS_Form_Common::option_get('action_email_logo'));
			$action_email_logo_size = WS_Form_Common::option_get('action_email_logo_size');
			if($action_email_logo_size == '') { $action_email_logo_size = 'full'; }
			if($action_email_logo > 0) {

				$email_logo = wp_get_attachment_image($action_email_logo, $action_email_logo_size);
			}

			// Get currency symbol
			$currencies = self::get_currencies();
			$currency = WS_Form_Common::option_get('currency', WS_Form_Common::get_currency_default());
			$currency_found = isset($currencies[$currency]) && isset($currencies[$currency]['s']);
			$currency_symbol = $currency_found ? $currencies[$currency]['s'] : '$';
			// Parse variables
			$parse_variables = array(

				// Blog
				'blog'	=>	array(

					'label'		=> __('Blog', 'ws-form'),

					'variables'	=> array(

						'blog_url'			=> array('label' => __('URL', 'ws-form'), 'value' => get_bloginfo('url')),
						'blog_name'			=> array('label' => __('Name', 'ws-form'), 'value' => get_bloginfo('name')),
						'blog_language'		=> array('label' => __('Language', 'ws-form'), 'value' => get_bloginfo('language')),
						'blog_charset'		=> array('label' => __('Character Set', 'ws-form'), 'value' => get_bloginfo('charset')),
						'blog_admin_email'	=> array('label' => __('Admin Email', 'ws-form'), 'secure' => true),

						'blog_time' => array(

							'label' => __('Current Time', 'ws-form'),

							'value' => WS_Form_Common::wp_version_at_least('5.3') ? current_datetime()->format(get_option('time_format')) : date(get_option('time_format'), current_time('timestamp')),

							'description' => __('Returns the blog time in the format configured in WordPress.', 'ws-form'),

							'secure' => true
						),

						'blog_date_custom' => array(

							'label' => __('Custom Date', 'ws-form'),

							'value' => WS_Form_Common::wp_version_at_least('5.3') ? current_datetime()->format('Y-m-d') . 'T' . current_datetime()->format('H:i:s') : date('Y-m-d', current_time('timestamp')) . 'T' . date('H:i:s', current_time('timestamp')),

							'attributes' => array(

								array('id' => 'format', 'required' => false, 'default' => 'm/d/Y H:i:s'),
								array('id' => 'seconds_offset', 'required' => false, 'default' => '0')
							),

							'kb_slug' => 'date-formats',

							'description' => __('Returns the blog date and time in a specified format (PHP date format).', 'ws-form'),

							'secure' => true
						),

						'blog_date' => array(

							'label' => __('Current Date', 'ws-form'),

							'value' => WS_Form_Common::wp_version_at_least('5.3') ? current_datetime()->format(get_option('date_format')) : date(get_option('date_format'), current_time('timestamp')),

							'description' => __('Returns the blog date in the format configured in WordPress.', 'ws-form'),

							'secure' => true
						),
					)
				),

				// Client
				'client'	=>	array(

					'label'		=>__('Client', 'ws-form'),

					'variables'	=> array(

						'client_time' => array('label' => __('Current Time', 'ws-form'), 'limit' => __('in client-side', 'ws-form'), 'description' => __('Returns the users web browser local time in the format configured in WordPress.', 'ws-form')),

						'client_date_custom' => array(

							'label' => __('Custom Date', 'ws-form'),

							'attributes' => array(

								array('id' => 'format', 'required' => false, 'default' => 'm/d/Y H:i:s'),
								array('id' => 'seconds_offset', 'required' => false, 'default' => '0')
							),

							'kb_slug' => 'date-formats',

							'limit' => __('in client-side', 'ws-form'),

							'description' => __('Returns the users web browser local date and time in a specified format (PHP date format).', 'ws-form')
						),

						'client_date' => array('label' => __('Current Date', 'ws-form'), 'limit' => __('in client-side', 'ws-form'), 'description' => __('Returns the users web browser local date in the format configured in WordPress.', 'ws-form')),
					)
 				),

				// Server
				'server'	=>	array(

					'label'		=>__('Server', 'ws-form'),

					'variables'	=> array(

						'server_time' => array('label' => __('Current Time', 'ws-form'), 'value' => date(get_option('time_format')), 'description' => __('Returns the server time in the format configured in WordPress.', 'ws-form'), 'secure' => true),

						'server_date_custom' => array(

							'label' => __('Custom Date', 'ws-form'),

							'value' => date('Y-m-d') . 'T' . date('H:i:s'),

							'attributes' => array(

								array('id' => 'format', 'required' => false, 'default' => 'm/d/Y H:i:s'),
								array('id' => 'seconds_offset', 'required' => false, 'default' => '0')
							),

							'kb_slug' => 'date-formats',

							'description' => __('Returns the server date and time in a specified format (PHP date format).', 'ws-form'),

							'secure' => true
						),

						'server_date' => array('label' => __('Current Date', 'ws-form'), 'value' => date(get_option('date_format')), 'description' => __('Returns the server date in the format configured in WordPress.', 'ws-form'), 'secure' => true)
					)
 				),

				// Form
				'form' 		=> array(

					'label'		=> __('Form', 'ws-form'),

					'variables'	=> array(

						'form_obj_id'		=>	array('label' => __('DOM Selector ID', 'ws-form')),
						'form_label'		=>	array('label' => __('Label', 'ws-form')),
						'form_instance_id'	=>	array('label' => __('Instance ID', 'ws-form')),
						'form_id'			=>	array('label' => __('ID', 'ws-form')),
						'form_framework'	=>	array('label' => __('Framework', 'ws-form')),
						'form_checksum'		=>	array('label' => __('Checksum', 'ws-form'), 'secure' => true),
					)
				),

				// Tab
				'tab' 	=> array(

					'label'		=> __('Tab', 'ws-form'),

					'variables'	=> array(

						'tab_label' =>	array(

							'label' => __('Tab Label', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Returns the tab label by ID.', 'ws-form')
						)
					)
				),

				// Submit
				'submit' 		=> array(

					'label'		=> __('Submission', 'ws-form'),

					'variables'	=> array(

						'submit_id'				=>	array('label' => __('ID', 'ws-form'), 'description' => __('Returns the numeric ID of the submission.', 'ws-form'), 'secure' => true),
						'submit_hash'			=>	array('label' => __('Hash', 'ws-form'), 'description' => __('Returns the anonymized hash ID of the submission.', 'ws-form'), 'secure' => true),
						'submit_user_id'		=>	array('label' => __('User ID', 'ws-form'), 'description' => __('Returns the ID of the user who completed the form.', 'ws-form'), 'secure' => true),
						'submit_admin_url'		=>	array('label' => __('Admin URL', 'ws-form'), 'description' => __('URL to submission in WordPress admin.', 'ws-form'), 'secure' => true),
						'submit_admin_link'		=>	array('label' => __('Admin Link', 'ws-form'), 'description' => __('Link to submission in WordPress admin.', 'ws-form'), 'secure' => true),
						'submit_url'			=>	array('label' => __('URL', 'ws-form'), 'description' => __('URL to recall form with submission loaded. Used in conjunction with the \'Save\' button.', 'ws-form'), 'secure' => true),
						'submit_link'			=>	array('label' => __('Link', 'ws-form'), 'description' => __('Link to recall form with submission loaded. Used in conjunction with the \'Save\' button.', 'ws-form'), 'secure' => true),
						'submit_status'			=>	array('label' => __('Status', 'ws-form'), 'description' => __('draft = In Progress, publish = Submitted, error = Error, spam = Spam, trash = Trash.', 'ws-form'), 'secure' => true),
						'submit_status_label'	=>	array('label' => __('Status Label', 'ws-form'), 'description' => __('Returns a nice version of the submission status.', 'ws-form'), 'secure' => true)
					)
				),

				// Skin
				'skin'			=> array(

					'label'		=> __('Skin', 'ws-form'),

					'variables' => array(

						// Color
						'skin_color_default'		=>	array('label' => __('Color - Default', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_default')),
						'skin_color_default_inverted'		=>	array('label' => __('Color - Default (Inverted)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_default_inverted')),
						'skin_color_default_light'		=>	array('label' => __('Color - Default (Light)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_default_light')),
						'skin_color_default_lighter'		=>	array('label' => __('Color - Default (Lighter)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_default_lighter')),
						'skin_color_default_lightest'		=>	array('label' => __('Color - Default (Lightest)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_default_lightest')),
						'skin_color_primary'		=>	array('label' => __('Color - Primary', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_primary')),
						'skin_color_secondary'		=>	array('label' => __('Color - Secondary', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_secondary')),
						'skin_color_success'		=>	array('label' => __('Color - Success', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_success')),
						'skin_color_information'		=>	array('label' => __('Color - Information', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_information')),
						'skin_color_warning'		=>	array('label' => __('Color - Warning', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_warning')),
						'skin_color_danger'		=>	array('label' => __('Color - Danger', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_color_danger')),

						// Font
						'skin_font_family'		=>	array('label' => __('Font - Family', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_font_family')),
						'skin_font_size'		=>	array('label' => __('Font - Size', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_font_size')),
						'skin_font_size_large'		=>	array('label' => __('Font - Size (Large)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_font_size_large')),
						'skin_font_size_small'		=>	array('label' => __('Font - Size (Small)', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_font_size_small')),
						'skin_font_weight'		=>	array('label' => __('Font - Weight', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_font_weight')),
						'skin_line_height'		=>	array('label' => __('Line Height', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_line_height')),

						// Border
						'skin_border_width'		=>	array('label' => __('Border - Width', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_border_width')),
						'skin_border_style'		=>	array('label' => __('Border - Style', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_border_style')),
						'skin_border_radius'		=>	array('label' => __('Border - Radius', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_border_radius')),

						// Box Shadow
						'skin_box_shadow_width'		=>	array('label' => __('Box Shadow - Width', 'ws-form'), 'kb_slug' => 'customize-appearance', 'value' => WS_Form_Common::option_get('skin_box_shadow_width'))
					)
				),
				// Progress
				'progress' 		=> array(

					'label'		=> __('Progress', 'ws-form'),

					'variables'	=> array(

						'progress'						=>	array('label' => __('Number (0 to 100)', 'ws-form'), 'limit' => __('in the Help setting for Progress fields', 'ws-form'), 'kb_slug' => 'progress'),
						'progress_percent'				=>	array('label' => __('Percent (0% to 100%)', 'ws-form'), 'limit' => __('in the Help setting for Progress fields', 'ws-form'), 'kb_slug' => 'progress'),
						'progress_remaining'			=>	array('label' => __('Number Remaining (100 to 0)', 'ws-form'), 'limit' => __('in the Help setting for Progress fields', 'ws-form'), 'kb_slug' => 'progress'),
						'progress_remaining_percent'	=>	array('label' => __('Percent Remaining (100% to 0%)', 'ws-form'), 'limit' => __('in the Help setting for Progress fields', 'ws-form'), 'kb_slug' => 'progress')
					)
				),

				// E-Commerce
				'ecommerce' 	=> array(

					'label'		=> __('E-Commerce', 'ws-form'),

					'variables'	=> array(

						'ecommerce_currency_symbol'		=>	array(

							'label' => __('Currency Symbol', 'ws-form'),

							'value' => $currency_symbol,

							'description' => __('Use this variable to show the current currency symbol.', 'ws-form')
						),

						'ecommerce_field_price'			=>	array(

							'label' => __('Field Value as Price', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
							),

							'description' => __('Use this variable to insert the value of a price field on your form. For example: <code>#field(123)</code> where \'123\' is the field ID shown in the layout editor. This variable will neatly format a currency value according to your E-Commerce settings. An example output might be: $123.00', 'ws-form'),

							'repair_group' => 'field'
						),

						'ecommerce_price'			=>	array(

							'label' => __('Value as Price', 'ws-form'),

							'attributes' => array(

								array('id' => 'number'),
							),

							'description' => __('Convert the number input to a price that matches the configured e-commerce currency settings. An example output might be: $123.00', 'ws-form')
						)
					)
				),
				// Section Rows
				'section_rows' 	=> array(

					'label'		=> __('Section Rows', 'ws-form'),

					'variables'	=> array(

						'section_rows_start' =>	array(

							'label' => __('Start Rows Start', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Define the start point for looping through repeatable section rows.', 'ws-form'),

							'repair_group' => 'section'
						),

						'section_rows_end'			=>	array(

							'label' => __('Section Rows End', 'ws-form'),

							'description' => __('Define the end point for looping through repeatable section rows.', 'ws-form')
						)
					),

					'priority' => 125
				),

				// Section
				'section' 	=> array(

					'label'		=> __('Section', 'ws-form'),

					'variables'	=> array(

						'section_row_count'	=>	array(

							'label' => __('Section Row Count', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
							),

							'description' => __('This variable returns the total number of rows in a repeatable section.', 'ws-form'),

							'repair_group' => 'section'
						),

						'section_row_number' => array(

							'label' => __('Section Row Number', 'ws-form'),

							'description' => __('This variable returns the row number in a repeatable section.', 'ws-form')
						),

						'section_row_index' => array(

							'label' => __('Section Row Index', 'ws-form'),

							'description' => __('This variable returns the row index in a repeatable section.', 'ws-form')
						),

						'section_label' =>	array(

							'label' => __('Section Label', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Returns the section label by ID.', 'ws-form'),

							'repair_group' => 'section'
						)
					)
				),

				// Time
				'seconds' 	=> array(

					'label'		=> __('Seconds', 'ws-form'),

					'variables'	=> array(

						'seconds_epoch_midnight' => array('label' => __('Seconds since Epoch at midnight', 'ws-form'), 'description' => __('Returns the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT) to the closest previous midnight.', 'ws-form'), 'limit' => __('in client-side', 'ws-form')),

						'seconds_epoch' => array('label' => __('Seconds since Epoch', 'ws-form'), 'description' => __('Returns the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).', 'ws-form')),

						'seconds_minute' => array('label' => __('Seconds in a minute', 'ws-form'), 'value' => '60', 'description' => __('Returns the number of seconds in a minute.', 'ws-form')),

						'seconds_hour' => array('label' => __('Seconds in an hour', 'ws-form'), 'value' => '3600', 'description' => __('Returns the number of seconds in an hour.', 'ws-form')),

						'seconds_day' => array('label' => __('Seconds in a day', 'ws-form'), 'value' => '86400', 'description' => __('Returns the number of seconds in a day.', 'ws-form')),

						'seconds_week' => array('label' => __('Seconds in a week', 'ws-form'), 'value' => '604800', 'description' => __('Returns the number of seconds in a week.', 'ws-form')),

						'seconds_year' => array('label' => __('Seconds in a year', 'ws-form'), 'value' => '31536000', 'description' => __('Returns the number of seconds in a common year.', 'ws-form'))
					)
				),

				// Cookies
				'cookie' 	=> array(

					'label'		=> __('Cookies', 'ws-form'),

					'variables'	=> array(

						'cookie_get'	=>	array(

							'label' => __('Get Cookie', 'ws-form'),

							'attributes' => array(

								array('id' => 'name'),
							),

							'description' => __('Returns the value of a cookie by name.', 'ws-form')
						)
					)
				),
				// Calculated
				'calc' 	=> array(

					'label'		=> __('Calculation', 'ws-form'),

					'variables'	=> array(

						'calc'			=>	array(

							'label' => __('Calculation', 'ws-form'),

							'attributes' => array(

								array('id' => 'calculation', 'required' => false),
							),

							'description' => __('Calculated value.', 'ws-form')
						)
					),

					'priority' => 100
				),

				// Text
				'text' 	=> array(

					'label'		=> __('Text', 'ws-form'),

					'variables'	=> array(

						'text'			=>	array(

							'label' => __('Text', 'ws-form'),

							'attributes' => array(

								array('id' => 'text', 'required' => false),
							),

							'description' => __('Keep the content of this variable updated.', 'ws-form')
						)
					),

					'priority' => 100
				),

				// Conditional
				'conditional' 	=> array(

					'label'		=> __('Conditional', 'ws-form'),

					'variables'	=> array(

						'if'			=>	array(

							'label' => __('Start of IF condition.', 'ws-form'),

							'attributes' => array(

								array('id' => 'condition', 'recurring' => true, 'required' => false)
							),

							'attribute_separator'	=>	' '
						),

						'endif'			=>	array(

							'label' => __('End of IF condition.', 'ws-form')
						)
					),

					'ignore_prefix' => true,

					'priority' => 50
				),
				// Math
				'math' 	=> array(

					'label'		=> __('Math', 'ws-form'),

					'variables'	=> array(

						'abs'			=>	array(

							'label' => __('Absolute', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
							),

							'description' => __('Returns the absolute value of a number.', 'ws-form')
						),

						'ceil'			=>	array(

							'label' => __('Ceiling', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
							),

							'description' => __('Rounds a number up to the next largest whole number.', 'ws-form')
						),

						'cos'			=>	array(

							'label' => __('Cosine', 'ws-form'),

							'attributes' => array(

								array('id' => 'radians', 'required' => false),
							),

							'description' => __('Returns the cosine of a radian number.', 'ws-form')
						),

						'exp'			=>	array(

							'label' => __("Euler's", 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
							),

							'description' => __('Returns E to the power of a number.', 'ws-form')
						),

						'floor'			=>	array(

							'label' => __("Floor", 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
							),

							'description' => __('Returns the largest integer value that is less than or equal to a number.', 'ws-form')
						),

						'log'			=>	array(

							'label' => __('Logarithm', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
							),

							'description' => __('Returns the natural logarithm of a number.', 'ws-form')
						),

						'round'			=>	array(

							'label' => __('Round', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false),
								array('id' => 'decimals', 'required' => false)
							),

							'description' => __('Returns the rounded value of a number.', 'ws-form')
						),

						'sin'			=>	array(

							'label' => __('Sine', 'ws-form'),

							'attributes' => array(

								array('id' => 'radians', 'required' => false)
							),

							'description' => __('Returns the sine of a radian number.', 'ws-form')
						),

						'sqrt'			=>	array(

							'label' => __('Square Root', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'required' => false)
							),

							'description' => __('Returns the square root of the number.', 'ws-form')
						),

						'tan'			=>	array(

							'label' => __('Tangent', 'ws-form'),

							'attributes' => array(

								array('id' => 'radians', 'required' => false)
							),

							'description' => __('Returns the tangent of a radian number.', 'ws-form')
						),

						'avg'			=>	array(

							'label' => __('Average', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'recurring' => true)
							),

							'description' => __('Returns the average of all the input numbers.', 'ws-form')
						),

						'pi'			=>	array(

							'label' => __('PI', 'ws-form'),

							'value' => M_PI,

							'description' => __('Returns an approximate value of PI.', 'ws-form')
						),

						'min'			=>	array(

							'label' => __('Minimum', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'recurring' => true)
							),

							'description' => __('Returns the lowest value of the supplied numbers.', 'ws-form')
						),

						'max'			=>	array(

							'label' => __('Maximum', 'ws-form'),

							'attributes' => array(

								array('id' => 'number', 'recurring' => true)
							),

							'description' => __('Returns the maxiumum value of the supplied numbers.', 'ws-form')
						),

						'negative'			=>	array(

							'label' => __('Negative', 'ws-form'),

							'attributes' => array(

								array('id' => 'number')
							),

							'description' => __('Returns 0 if positive, or original value if negative.', 'ws-form')
						),

						'positive'			=>	array(

							'label' => __('Positive', 'ws-form'),

							'attributes' => array(

								array('id' => 'number')
							),

							'description' => __('Returns 0 if negative, or original value if positive.', 'ws-form')
						),

						'pow'			=>	array(

							'label' => __('Base to the Exponent Power', 'ws-form'),

							'attributes' => array(

								array('id' => 'base'),
								array('id' => 'exponent')
							),

							'description' => __('Returns the base to the exponent power.', 'ws-form')
						),

						'avg'			=>	array(

							'label' => __('Average', 'ws-form'),

							'attributes' => array(

								array('id' => 'number')
							),

							'description' => __('Returns the average of all the input numbers.', 'ws-form')
						)
					),

					'ignore_prefix' => true,

					'priority' => 50
				),

				// String
				'string' 	=> array(

					'label'		=> __('String', 'ws-form'),

					'variables'	=> array(

						'lower'	=>	array(

							'label' => __('Lowercase', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the lowercase version of the input string.', 'ws-form')
						),

						'upper'	=>	array(

							'label' => __('Uppercase', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the uppercase version of the input string.', 'ws-form')
						),

						'ucwords'	=>	array(

							'label' => __('Uppercase words', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the uppercase words version of the input string.', 'ws-form')
						),

						'ucfirst'	=>	array(

							'label' => __('Uppercase first letter', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the uppercase first letter version of the input string.', 'ws-form')
						),

						'capitalize'	=>	array(

							'label' => __('Capitalize a string', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the capitalized version of an input string.', 'ws-form')
						),

						'sentence'	=>	array(

							'label' => __('Sentence case a string', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the sentence cased version of an input string.', 'ws-form')
						),

						'wpautop'	=>	array(

							'label' => __('Apply wpautop to a string', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the string with wpautop applied to it.', 'ws-form')
						),

						'trim'	=>	array(

							'label' => __('Trim a string', 'ws-form'),

							'attributes' => array(

								array('id' => 'string', 'required' => false),
							),

							'description' => __('Returns the trimmed string.', 'ws-form')
						)
					),

					'ignore_prefix' => true,

					'priority' => 50
				),

				// Field
				'field' 	=> array(

					'label'		=> __('Field', 'ws-form'),

					'variables'	=> array(

						'field_label' =>	array(

							'label' => __('Field Label', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Returns the field label by ID.', 'ws-form'),

							'repair_group' => 'field'
						),

						'field_float'			=>	array(

							'label' => __('Field Value as Floating Point Number', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
							),

							'description' => __('Use this variable to insert the value of a field on your form as a floating point number. For example: <code>#field(123)</code> where \'123\' is the field ID shown in the layout editor. This can be used to convert prices to floating point numbers. An example output might be: 123.45', 'ws-form'),

							'repair_group' => 'field'
						),

						'field_date_offset' => array(

							'label' => __('Field Date Adjusted by Offset in Seconds', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
								array('id' => 'seconds_offset', 'required' => false, 'default' => '0'),
								array('id' => 'format', 'required' => false, 'default' => get_option('date_format'))
							),

							'limit' => __('in client-side', 'ws-form'),

							'description' => __('Return a date adjusted by an offset in seconds.', 'ws-form'),

							'repair_group' => 'field'
						),

						'field'			=>	array(

							'label' => __('Field Value', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
								array('id' => 'delimiter', 'required' => false),
								array('id' => 'column', 'required' => false)
							),

							'description' => __('Use this variable to insert the value of a field on your form. For example: <code>#field(123)</code> where \'123\' is the field ID shown in the layout editor. If delimiter specified, fields with multiple values (e.g. checkboxes) will be separated by the specified delimiter. If column is specified it will return the value found in that data grid column. The value of column can be the column label or index (starting with 0).', 'ws-form'),

							'repair_group' => 'field'
						)
					)
				),

				// Data grid rows
				'data_grid_row'	=> array(

					'label'		=> __('Data Grid Rows', 'ws-form'),

					'variables'	=> array(

						'data_grid_row_value'	=>	array(

							'label' => __('Value Column', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the value column.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						),

						'data_grid_row_label'	=>	array(

							'label' => __('Label Column', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the label column.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						),

						'data_grid_row_action_variable'	=>	array(

							'label' => __('Action Variable Column', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the action variable column.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						),

						'data_grid_row_price'	=>	array(

							'label' => __('Price Column', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the price column.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						),

						'data_grid_row_price_currency'	=>	array(

							'label' => __('Price Column (With Currency)', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the price column formatted using the currency settings.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						),

						'data_grid_row_wocommerce_Cart'	=>	array(

							'label' => __('WooCommerce Cart Column', 'ws-form'),

							'description' => __('Use this variable within a data grid row to insert the text found in the WooCommerce cart column.', 'ws-form'),

							'limit' => __('within a data grid row', 'ws-form')
						)
					)
				),

				// Select option text
				'select' 	=> array(

					'label'		=> __('Select', 'ws-form'),

					'variables'	=> array(

						'select_count'	=>	array(

							'label' => __('Select Count', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Use this variable to return the number of options that have been selected for a field. For example: <code>#select_count(123)</code> where \'123\' is the field ID shown in the layout editor.', 'ws-form'),

							'repair_group' => 'field',

							'limit' => __('in client-side', 'ws-form')
						),

						'select_option_text'			=>	array(

							'label' => __('Select Option Text', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
								array('id' => 'delimiter', 'required' => false, 'trim' => false)
							),

							'description' => __('Use this variable to insert the selected option text of a select field on your form. For example: <code>#select_option_text(123)</code> where \'123\' is the field ID shown in the layout editor.', 'ws-form'),

							'repair_group' => 'field',

							'limit' => __('in client-side', 'ws-form')
						)
					)
				),

				// Checkboxes
				'checkbox' 	=> array(

					'label'		=> __('Checkbox', 'ws-form'),

					'variables'	=> array(

						'checkbox_count'	=>	array(

							'label' => __('Checkbox Count', 'ws-form'),

							'attributes' => array(

								array('id' => 'id')
							),

							'description' => __('Use this variable to return the number of checkboxes that have been checked for a field. For example: <code>#checkbox_count(123)</code> where \'123\' is the field ID shown in the layout editor.', 'ws-form'),

							'repair_group' => 'field',

							'limit' => __('in client-side', 'ws-form')
						),

						'checkbox_label'	=>	array(

							'label' => __('Checkbox Label', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
								array('id' => 'delimiter', 'required' => false, 'trim' => false)
							),

							'description' => __('Use this variable to insert the label of a checkbox field on your form. For example: <code>#checkbox_label(123)</code> where \'123\' is the field ID shown in the layout editor.', 'ws-form'),

							'repair_group' => 'field',

							'limit' => __('in client-side', 'ws-form')
						)
					)
				),

				// Radio label
				'radio' 	=> array(

					'label'		=> __('Radio', 'ws-form'),

					'variables'	=> array(

						'radio_label'	=>	array(

							'label' => __('Radio Label', 'ws-form'),

							'attributes' => array(

								array('id' => 'id'),
								array('id' => 'delimiter', 'required' => false, 'trim' => false)
							),

							'description' => __('Use this variable to insert the label of a radio field on your form. For example: <code>#radio_label(123)</code> where \'123\' is the field ID shown in the layout editor.', 'ws-form'),

							'repair_group' => 'field',

							'limit' => __('in client-side', 'ws-form')
						)
					)
				),

				// Email
				'email' 	=> array(

					'label'		=> __('Email', 'ws-form'),

					'variables'	=> array(

						'email_subject'			=>	array('label' => __('Subject', 'ws-form'), 'limit' => __('in the Send Email action', 'ws-form'), 'kb_slug' => 'send-email'),
						'email_content_type'	=>	array('label' => __('Content type', 'ws-form'), 'limit' => __('in the Send Email action', 'ws-form'), 'kb_slug' => 'send-email'),
						'email_charset'			=>	array('label' => __('Character set', 'ws-form'), 'limit' => __('in the Send Email action', 'ws-form'), 'kb_slug' => 'send-email'),
						'email_submission'		=>	array(

							'label' => __('Submitted Fields', 'ws-form'),

							'attributes' => array(

								array('id' => 'tab_labels', 'required' => false, 'default' => WS_Form_Common::option_get('action_email_group_labels', 'auto'), 'valid' => array('true', 'false', 'auto')),
								array('id' => 'section_labels', 'required' => false, 'default' => WS_Form_Common::option_get('action_email_section_labels', 'auto'), 'valid' => array('true', 'false', 'auto')),
								array('id' => 'field_labels', 'required' => false, 'default' => WS_Form_Common::option_get('action_email_field_labels', 'true'), 'valid' => array('true', 'false', 'auto')),
								array('id' => 'blank_fields', 'required' => false, 'default' => (WS_Form_Common::option_get('action_email_exclude_empty') ? 'false' : 'true'), 'valid' => array('true', 'false')),
								array('id' => 'static_fields', 'required' => false, 'default' => (WS_Form_Common::option_get('action_email_static_fields') ? 'true' : 'false'), 'valid' => array('true', 'false')),
							),

							'kb_slug' => 'send-email',

							'limit' => __('in the Send Email action', 'ws-form'),

							'description' => __('This variable outputs a list of the fields captured during a submission. You can either use: <code>#email_submission</code> or provide additional parameters to toggle tab labels, section labels, blank fields and static fields (such as text or HTML areas of your form). Specify \'true\' or \'false\' for each parameter, for example: <code>#email_submission(true, true, false, true, true)</code>', 'ws-form')
						),
						'email_ecommerce'		=>	array(

							'label' => __('E-Commerce Values', 'ws-form'),

							'kb_slug' => 'introduction-e-commerce',

							'limit' => __('in the Send Email action', 'ws-form'),

							'description' => __('This variable outputs a list of the e-commerce transaction details such as total, transaction ID and status fields.', 'ws-form'),

							'secure' => true
						),
						'email_tracking'		=>	array('label' => __('Tracking data', 'ws-form'), 'limit' => __('in the Send Email action', 'ws-form'), 'kb_slug' => 'send-email', 'secure' => true),
						'email_logo'			=>	array('label' => __('Logo', 'ws-form'), 'value' => $email_logo, 'limit' => __('in the Send Email action', 'ws-form'), 'kb_slug' => 'send-email'),
						'email_pixel'			=>	array('label' => __('Pixel'), 'value' => '<img src="' . WS_FORM_PLUGIN_DIR_URL . 'public/images/email/p.gif" width="100%" height="5" />', 'description' => __('Outputs a transparent gif. We use this to avoid Mac Mail going into dark mode when viewing emails.', 'ws-form'))
					)
				),

				// Query variable
				'query' 	=> array(

					'label'		=> __('Query Variable (GET request)', 'ws-form'),

					'variables'	=> array(

						'query_var'		=>	array(

							'label' => __('Query Variable', 'ws-form'),

							'attributes' => array(

								array('id' => 'variable')
							),

							'secure' => true
						)
					)
				),

				// Post variable
				'post_var' 	=> array(

					'label'		=> __('Post Variable (POST request)', 'ws-form'),

					'variables'	=> array(

						'post_var'	=>	array(

							'label' => __('Post Variable', 'ws-form'),

							'attributes' => array(

								array('id' => 'variable')
							),

							'secure' => true
						)
					)
				),

				// Random Numbers
				'random_number' 	=> array(

					'label'		=> __('Random Numbers', 'ws-form'),

					'variables'	=> array(

						'random_number'	=>	array(

							'label' => __('Random Number', 'ws-form'),

							'attributes' => array(

								array('id' => 'min', 'required' => false, 'default' => 0),
								array('id' => 'max', 'required' => false, 'default' => 100)
							),

							'description' => __('Outputs an integer between the specified minimum and maximum attributes. This function does not generate cryptographically secure values, and should not be used for cryptographic purposes.', 'ws-form'),

							'single_parse' => true
						)
					)
				),

				// Random Strings
				'random_string' 	=> array(

					'label'		=> __('Random Strings', 'ws-form'),

					'variables'	=> array(

						'random_string'	=>	array(

							'label' => __('Random String', 'ws-form'),

							'attributes' => array(

								array('id' => 'length', 'required' => false, 'default' => 32),
								array('id' => 'characters', 'required' => false, 'default' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
							),

							'description' => __('Outputs a string of random characters. Use the length attribute to control how long the string is and use the characters attribute to control which characters are randomly selected. This function does not generate cryptographically secure values, and should not be used for cryptographic purposes.', 'ws-form'),

							'single_parse' => true
						)
					)
				),

				// Character
				'character'	=> array(

					'label'		=> __('Character', 'ws-form'),

					'variables' => array(

						'character_count'	=>	array(

							'label'	=> __('Count', 'ws-form'),
							'description' => __('The total character count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_count_label'	=>	array(

							'label'	=> __('Count Label', 'ws-form'),
							'description' => __("Shows 'character' or 'characters' depending on the character count.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_remaining'	=>	array(

							'label'	=> __('Count Remaining', 'ws-form'),
							'description' => __('If you set a maximum character length for a field, this will show the total remaining character count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_remaining_label'	=>	array(

							'label'	=> __('Count Remaining Label', 'ws-form'),
							'description' => __('If you set a maximum character length for a field, this will show the total remaining character count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_min'	=>	array(

							'label'	=> __('Minimum', 'ws-form'),
							'description' => __('Shows the minimum character length that you set for a field.'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_min_label'	=>	array(

							'label'	=> __('Minimum Label', 'ws-form'),
							'description' => __("Shows 'character' or 'characters' depending on the minimum character length.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_max'	=>	array(

							'label'	=> __('Maximum', 'ws-form'),
							'description' => __('Shows the maximum character length that you set for a field.'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'character_max_label'	=>	array(

							'label'	=> __('Maximum Label', 'ws-form'),
							'description' => __("Shows 'character' or 'characters' depending on the maximum character length.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						)
					)
				),

				// Word
				'word'	=> array(

					'label'		=> __('Word', 'ws-form'),

					'variables' => array(

						'word_count'	=>	array(

							'label'	=> __('Count', 'ws-form'),
							'description' => __('The total word count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_count_label'	=>	array(

							'label'	=> __('Count Label', 'ws-form'),
							'description' => __("Shows 'word' or 'words' depending on the word count.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_remaining'	=>	array(

							'label'	=> __('Count Remaining', 'ws-form'),
							'description' => __('If you set a maximum word length for a field, this will show the total remaining word count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_remaining_label'	=>	array(

							'label'	=> __('Count Remaining Label', 'ws-form'),
							'description' => __('If you set a maximum word length for a field, this will show the total remaining word count.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_min'	=>	array(

							'label'	=> __('Minimum', 'ws-form'),
							'description' => __('Shows the minimum word length that you set for a field.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_min_label'	=>	array(

							'label'	=> __('Minimum Label', 'ws-form'),
							'description' => __("Shows 'word' or 'words' depending on the minimum word length.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_max'	=>	array(

							'label'	=> __('Maximum', 'ws-form'),
							'description' => __('Shows the maximum word length that you set for a field.', 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						),

						'word_max_label'	=>	array(

							'label'	=> __('Maximum Label', 'ws-form'),
							'description' => __("Shows 'word' or 'words' depending on the maximum word length.", 'ws-form'),
							'limit'	=> __('in the Help setting for text based Fields', 'ws-form'),
							'kb_slug' => 'word-and-character-count'
						)
					)
				)
			);

			// Post
			$post = WS_Form_Common::get_post_root();

			$post_not_null = !is_null($post);

			$parse_variables['post'] = array(

				'label'		=> __('Post', 'ws-form'),

				'variables'	=> array(

					'post_url_edit'		=>	array('label' => __('Admin URL', 'ws-form'), 'value' => $post_not_null ? get_edit_post_link($post->ID) : '', 'secure' => true),
					'post_url'			=>	array('label' => __('Public URL', 'ws-form'), 'value' => $post_not_null ? get_permalink($post->ID) : ''),
					'post_type'			=>	array('label' => __('Type', 'ws-form'), 'value' => $post_not_null ? $post->post_type : ''),
					'post_title'		=>	array('label' => __('Title', 'ws-form'), 'value' => $post_not_null ? $post->post_title : ''),
					'post_time'			=>	array('label' => __('Time', 'ws-form'), 'value' => $post_not_null ? date(get_option('time_format'), strtotime($post->post_date)) : '', 'secure' => true),
					'post_status'		=>	array('label' => __('Status', 'ws-form'), 'value' => $post_not_null ? $post->post_status : ''),
					'post_name'			=>	array('label' => __('Slug', 'ws-form'), 'value' => $post_not_null ? $post->post_name : ''),
					'post_id'			=>	array('label' => __('ID', 'ws-form'), 'value' => $post_not_null ? $post->ID : '', 'secure' => true),

					'post_date_custom'	=>	array(

						'label' => __('Post Custom Date', 'ws-form'),

						'value' => $post_not_null ? date('c', strtotime($post->post_date)) : '',

						'attributes' => array(

							array('id' => 'format', 'required' => false, 'default' => 'F j, Y, g:i a'),
							array('id' => 'seconds_offset', 'required' => false, 'default' => '0')
						),

						'kb_slug' => 'date-formats',

						'secure' => true
					),

					'post_date'			=>	array('label' => __('Date', 'ws-form'), 'value' => !is_null($post) ? date(get_option('date_format'), strtotime($post->post_date)) : '', 'secure' => true),

					'post_meta'			=>	array(

						'label' => __('Meta Value', 'ws-form'),

						'attributes' => array(

							array('id' => 'key')
						),

						'description' => __('Returns the post meta value for the key specified.', 'ws-form'),

						'scope' => array('form_parse'),

						'secure' => true
					),

					// Server side only
					'post_content'		=>	array('label' => __('Content', 'ws-form'), 'value' => ''),
					'post_excerpt'		=>	array('label' => __('Excerpt', 'ws-form'), 'value' => '')
				)
			);

			// Request
			$parse_variables['request'] = array(

				'label'		=> __('Request', 'ws-form'),

				'variables'	=> array(

					'request_url' 	=>	array('label' => __('Request URL', 'ws-form'), 'value' => WS_Form_Common::get_current_url())
				)
			);

			// Author
			$post_author_id = !is_null($post) ? $post->post_author : 0;
			$parse_variables['author'] = array(

				'label'		=> __('Author', 'ws-form'),

				'variables'	=> array(

					'author_id'				=>	array('label' => __('ID', 'ws-form'), 'value' => $post_author_id, 'secure' => true),
					'author_display_name'	=>	array('label' => __('Display Name', 'ws-form'), 'value' => get_the_author_meta('display_name', $post_author_id), 'secure' => true),
					'author_first_name'		=>	array('label' => __('First Name', 'ws-form'), 'value' => get_the_author_meta('first_name', $post_author_id), 'secure' => true),
					'author_last_name'		=>	array('label' => __('Last Name', 'ws-form'), 'value' => get_the_author_meta('last_name', $post_author_id), 'secure' => true),
					'author_nickname'		=>	array('label' => __('Nickname', 'ws-form'), 'value' => get_the_author_meta('nickname', $post_author_id), 'secure' => true),
					'author_email'			=>	array('label' => __('Email', 'ws-form'), 'secure' => true),
				)
			);

			// URL
			$parse_variables['url'] = array(

				'label'		=> __('URL', 'ws-form'),

				'variables'	=> array(

					'url_login'			=>	array('label' => __('Login', 'ws-form'), 'secure' => true),
					'url_logout'		=>	array('label' => __('Logout', 'ws-form'), 'secure' => true),
					'url_lost_password'	=>	array('label' => __('Lost Password', 'ws-form'), 'secure' => true),
					'url_register'		=>	array('label' => __('Register', 'ws-form'), 'secure' => true),
				)
			);

			// ACF
			if(class_exists('acf')) { 

				$parse_variables['acf'] =  array(

					'label'		=> __('ACF', 'ws-form'),

					'variables'	=> array(

						'acf_repeater_field'	=>	array(

							'label' => __('Repeater Field', 'ws-form'),

							'attributes' => array(

								array('id' => 'parent_field'),
								array('id' => 'sub_field'),
							),

							'description' => __('Used to obtain an ACF repeater field. You can separate parent_fields with commas to access deep variables.', 'ws-form'),

							'scope' => array('form_parse'),

							'secure' => true
						),
					)
				);
			}

			if(!$public) {

				// Tracking
				$tracking_array = self::get_tracking($public);
				$parse_variables['tracking'] = array(

					'label'		=> __('Tracking', 'ws-form'),
					'variables'	=> array()
				);

				foreach($tracking_array as $meta_key => $tracking) {

					$parse_variables['tracking']['variables'][$meta_key] = array('label' => $tracking['label'], 'description' => $tracking['description']);
				}
			}

			// Get e-commerce config
			$ecommerce_config = self::get_ecommerce();

			foreach($ecommerce_config['cart_price_types'] as $meta_key => $cart_price_type) {

				$parse_variables['ecommerce']['variables']['ecommerce_cart_' . $meta_key . '_span'] = array(

					'label' 		=> sprintf('%s (%s)', $cart_price_type['label'], __('Span', 'ws-form')),
					'value' 		=> sprintf('<span data-ecommerce-cart-price-%s>#ecommerce_cart_%1$s</span>', $meta_key),
					'description' 	=> __('Excludes currency symbol. This variable outputs a span that can be used in Text Editor or HTML fields.', 'ws-form')
				);
				$parse_variables['ecommerce']['variables']['ecommerce_cart_' . $meta_key . '_span_currency'] = array(

					'label' 		=> sprintf('%s (%s)', $cart_price_type['label'], __('Span Currency', 'ws-form')),
					'value' 		=> sprintf('<span data-ecommerce-cart-price-%1$s data-ecommerce-price-currency>#ecommerce_cart_%1$s_currency</span>', $meta_key),
					'description' 	=> __('Includes currency symbol. This variable outputs a span that can be used in Text Editor or HTML fields.', 'ws-form')
				);
				$parse_variables['ecommerce']['variables']['ecommerce_cart_' . $meta_key] = array(

					'label' 		=> $cart_price_type['label'],
					'description' 	=> __('Excludes currency symbol. Use this in conditional logic or email templates.', 'ws-form')
				);
				$parse_variables['ecommerce']['variables']['ecommerce_cart_' . $meta_key . '_currency'] = array(

					'label' 		=> sprintf('%s (%s)', $cart_price_type['label'], __('Currency', 'ws-form')),
					'description' 	=> __('Includes currency symbol. Use this in conditional logic or email templates.', 'ws-form')
				);
			}

			foreach($ecommerce_config['meta_keys'] as $meta_key => $meta_key_config) {

				$type = isset($meta_key_config['type']) ? $meta_key_config['type'] : false;

				if($type == 'price') {

					$parse_variables['ecommerce']['variables'][$meta_key . '_span'] = array(

						'label' 		=> sprintf('%s (%s)', $meta_key_config['label'], __('Span', 'ws-form')),
						'value' 		=> sprintf('<span data-%1$s>%1$s</span>', str_replace('_', '-', $meta_key)),
						'description' 	=> __('Excludes currency symbol. This variable outputs a span that can be used in Text Editor or HTML fields.', 'ws-form')
					);
					$parse_variables['ecommerce']['variables'][$meta_key . '_span_currency'] = array(

						'label' 		=> sprintf('%s (%s)', $meta_key_config['label'], __('Span Currency', 'ws-form')),
						'value' 		=> sprintf('<span data-%1$s data-ecommerce-price-currency>%1$s_currency</span>', str_replace('_', '-', $meta_key)),
						'description'	=> __('Includes currency symbol. This variable outputs a span that can be used in Text Editor or HTML fields.', 'ws-form')
					);
					$parse_variables['ecommerce']['variables'][$meta_key . '_currency'] = array(

						'label'			=> sprintf('%s (%s)', $meta_key_config['label'], __('Currency', 'ws-form')),
						'description' 	=> __('Includes currency symbol. Use this in conditional logic or email templates.', 'ws-form')
					);
				}

				$parse_variables['ecommerce']['variables'][$meta_key] = array(

					'label' 		=> $meta_key_config['label'],
					'description' 	=> __('Excludes currency symbol. Use this in conditional logic or email templates.', 'ws-form')
				);
			}
			// User
			$user = WS_Form_Common::get_user();

			$user_id = (($user === false) ? 0 : $user->ID);

			$parse_variables['user'] = array(

				'label'		=> __('User', 'ws-form'),

				'variables'	=> array(

					'user_id' 			=>	array('label' => __('ID', 'ws-form'), 'value' => $user_id, 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_login' 		=>	array('label' => __('Login', 'ws-form'), 'value' => ($user_id > 0) ? $user->user_login : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_nicename' 	=>	array('label' => __('Nice Name', 'ws-form'), 'value' => ($user_id > 0) ? $user->user_nicename : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_email' 		=>	array('label' => __('Email', 'ws-form'), 'value' => ($user_id > 0) ? $user->user_email : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_display_name' =>	array('label' => __('Display Name', 'ws-form'), 'value' => ($user_id > 0) ? $user->display_name : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_url' 			=>	array('label' => __('URL', 'ws-form'), 'value' => ($user_id > 0) ? $user->user_url : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_registered' 	=>	array('label' => __('Registration Date', 'ws-form'), 'value' => ($user_id > 0) ? $user->user_registered : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_first_name'	=>	array('label' => __('First Name', 'ws-form'), 'value' => ($user_id > 0) ? get_user_meta($user_id, 'first_name', true) : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_last_name'	=>	array('label' => __('Last Name', 'ws-form'), 'value' => ($user_id > 0) ? get_user_meta($user_id, 'last_name', true) : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_bio'			=>	array('label' => __('Bio', 'ws-form'), 'value' => ($user_id > 0) ? get_user_meta($user_id, 'description', true) : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_nickname' 	=>	array('label' => __('Nickname', 'ws-form'), 'value' => ($user_id > 0) ? get_user_meta($user_id, 'nickname', true) : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_admin_color' 	=>	array('label' => __('Admin Color', 'ws-form'), 'value' => ($user_id > 0) ? get_user_meta($user_id, 'admin_color', true) : '', 'limit' => __('if a user is currently signed in', 'ws-form')),
					'user_lost_password_key' => array('label' => __('Lost Password Key', 'ws-form'), 'value' => ($user_id > 0) ? $user->lost_password_key : '', 'limit' => __('if a user is currently signed in', 'ws-form'), 'secure' => true),
					'user_lost_password_url' => array(

						'label'			=> __('Lost Password URL', 'ws-form'),

						'attributes'	=> array(

							array('id' => 'path', 'required' => false, 'default' => '')
						),

						'limit' => __('if a user is currently signed in', 'ws-form'),

						'secure' => true
					),
					'user_meta'			=>	array(

						'label' => __('Meta Value', 'ws-form'),

						'attributes' => array(

							array('id' => 'key')
						),

						'description' => __('Returns the user meta value for the key specified.', 'ws-form'),

						'scope' => array('form_parse'),

						'secure' => true
					)
				)
			);

			// Search
			$parse_variables['search'] = array(

				'label'		=> __('Search', 'ws-form'),

				'variables'	=> array(

					'search_query' => array('label' => __('Query', 'ws-form'), 'value' => get_search_query())
				)
			);

			// Cache
			self::$parse_variables[$public] = $parse_variables;

			return self::get_parse_variables_return($parse_variables, $public);
		}

		// Return parse variables
		public static function get_parse_variables_return($parse_variables, $public) {

			// Apply filter
			$parse_variables = apply_filters('wsf_config_parse_variables', $parse_variables);

			// Public - Optimize
			if($public) {

				$parameters_exclude = array('label', 'description', 'limit', 'kb_slug');

				foreach($parse_variables as $variable_group => $variable_group_config) {

					foreach($variable_group_config['variables'] as $variable => $variable_config) {

						unset($parse_variables[$variable_group]['label']);

						foreach($parameters_exclude as $parameter_exclude) {

							if(isset($parse_variables[$variable_group]['variables'][$variable][$parameter_exclude])) {

								unset($parse_variables[$variable_group]['variables'][$variable][$parameter_exclude]);
							}
						}
					}
				}
			}

			return $parse_variables;
		}

		// JavaScript
		public static function get_external() {

			// CDN or local source?
			$jquery_source = WS_Form_Common::option_get('jquery_source', 'local');
			$local = ($jquery_source == 'local');
			// Minified scripts?
			$min = SCRIPT_DEBUG ? '' : '.min';

			// Third party script paths (Local and included with WS Form)
			$select2_js_local = sprintf('%sshared/js/external/select2.full%s.js?ver=4.0.5', WS_FORM_PLUGIN_DIR_URL, $min);
			$select2_css_local = sprintf('%sshared/css/external/select2.min.css?ver=4.0.5', WS_FORM_PLUGIN_DIR_URL, $min);
			$inputmask_js_local = sprintf('%spublic/js/external/jquery.inputmask%s.js?ver=5.0.7', WS_FORM_PLUGIN_DIR_URL, $min);
			$intl_tel_input_js_local = sprintf('%spublic/js/external/intlTelInput%s.js?ver=17.0.13', WS_FORM_PLUGIN_DIR_URL, $min);
			$intl_tel_input_css_local = sprintf('%spublic/css/external/intlTelInput%s.css?ver=17.0.13', WS_FORM_PLUGIN_DIR_URL, $min);

			$signature_pad_js_local = sprintf('%spublic/js/external/signature_pad%s.js?ver=2.3.2', WS_FORM_PLUGIN_DIR_URL, $min);
			$datetimepicker_js_local = sprintf('%spublic/js/external/jquery.datetimepicker.full%s.js?ver=2.5.21', WS_FORM_PLUGIN_DIR_URL, $min);
			$datetimepicker_css_local = sprintf('%spublic/css/external/jquery.datetimepicker.min.css?ver=2.5.21', WS_FORM_PLUGIN_DIR_URL, $min);
			$minicolors_js_local = sprintf('%spublic/js/external/jquery.minicolors%s.js?ver=2.3.2', WS_FORM_PLUGIN_DIR_URL, $min);
			$minicolors_css_local = sprintf('%spublic/css/external/jquery.minicolors.min.css?ver=2.3.2', WS_FORM_PLUGIN_DIR_URL, $min);
			$dropzonejs_js_local = sprintf('%spublic/js/external/dropzone%s.js?ver=5.7.6', WS_FORM_PLUGIN_DIR_URL, $min);
			$zxcvbn_local = sprintf('%spublic/js/wp/zxcvbn.min.js', WS_FORM_PLUGIN_DIR_URL);
			$password_strength_meter_local = sprintf('%spublic/js/wp/password-strength-meter.min.js', WS_FORM_PLUGIN_DIR_URL);

			// Script paths (CDN)
			$signature_pad_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad%s.js', $min);
			$datetimepicker_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.21/build/jquery.datetimepicker.full%s.js', $min);
			$datetimepicker_css_cdn = (SCRIPT_DEBUG ? 'https://cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.21/jquery.datetimepicker.css' : 'https://cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.21/build/jquery.datetimepicker.min.css');
			$minicolors_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/@claviska/jquery-minicolors@2.3.2/jquery.minicolors%s.js', $min);
			$minicolors_css_cdn = sprintf('https://cdn.jsdelivr.net/npm/@claviska/jquery-minicolors@2.3.2/jquery.minicolors%s.css', $min);
			$dropzonejs_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/dropzone@5.7.6/dist/dropzone%s.js', $min);
			$select2_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/select2@4.0.5/dist/js/select2.full%s.js', $min);
			$select2_css_cdn = sprintf('https://cdn.jsdelivr.net/npm/select2@4.0.5/dist/css/select2%s.css', $min);
			$inputmask_js_cdn = sprintf('https://cdn.jsdelivr.net/gh/RobinHerbots/jquery.inputmask@5.0.7/dist/jquery.inputmask%s.js', $min);
			$intl_tel_input_js_cdn = sprintf('https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.13/build/js/intlTelInput%s.js', $min);
			$intl_tel_input_css_cdn = sprintf('https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.13/build/css/intlTelInput%s.css', $min);
			$external = array(

				// Select2
				'select2_js' => ($local ? $select2_js_local : $select2_js_cdn),
				'select2_css' => ($local ? $select2_css_local : $select2_css_cdn),

				// Input mask bundle
				'inputmask_js' => ($local ? $inputmask_js_local : $inputmask_js_cdn),

				// International Telephone Input
				'intl_tel_input_js' => ($local ? $intl_tel_input_js_local : $intl_tel_input_js_cdn),
				'intl_tel_input_css' => ($local ? $intl_tel_input_css_local : $intl_tel_input_css_cdn),

				// Signature Pad
				'signature_pad_js' => ($local ? $signature_pad_js_local : $signature_pad_js_cdn),

				// Date Time Picker
				'datetimepicker_js' => ($local ? $datetimepicker_js_local : $datetimepicker_js_cdn),
				'datetimepicker_css' => ($local ? $datetimepicker_css_local : $datetimepicker_css_cdn),

				// MiniColors
				'minicolors_js' => ($local ? $minicolors_js_local : $minicolors_js_cdn),
				'minicolors_css' => ($local ? $minicolors_css_local : $minicolors_css_cdn),

				// DropzoneJS
				'dropzonejs_js' => ($local ? $dropzonejs_js_local : $dropzonejs_js_cdn),

				// Password Strength Meter (WordPress admin file)
				'zxcvbn'					=> $zxcvbn_local,
				'password_strength_meter'	=> $password_strength_meter_local
			);

			// Apply filter
			$external = apply_filters('wsf_config_external', $external);

			return $external;
		}

		public static function get_countries_alpha_2($options = false) {

			$countries_alpha_2 = array(

				'AF' => 'Afghanistan',
				'AX' => 'Aland Islands',
				'AL' => 'Albania',
				'DZ' => 'Algeria',
				'AS' => 'American Samoa',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AQ' => 'Antarctica',
				'AG' => 'Antigua And Barbuda',
				'AR' => 'Argentina',
				'AM' => 'Armenia',
				'AW' => 'Aruba',
				'AU' => 'Australia',
				'AT' => 'Austria',
				'AZ' => 'Azerbaijan',
				'BS' => 'Bahamas',
				'BH' => 'Bahrain',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BY' => 'Belarus',
				'BE' => 'Belgium',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Bolivia',
				'BA' => 'Bosnia And Herzegovina',
				'BW' => 'Botswana',
				'BV' => 'Bouvet Island',
				'BR' => 'Brazil',
				'IO' => 'British Indian Ocean Territory',
				'BN' => 'Brunei Darussalam',
				'BG' => 'Bulgaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'KH' => 'Cambodia',
				'CM' => 'Cameroon',
				'CA' => 'Canada',
				'CV' => 'Cape Verde',
				'KY' => 'Cayman Islands',
				'CF' => 'Central African Republic',
				'TD' => 'Chad',
				'CL' => 'Chile',
				'CN' => 'China',
				'CX' => 'Christmas Island',
				'CC' => 'Cocos (Keeling) Islands',
				'CO' => 'Colombia',
				'KM' => 'Comoros',
				'CG' => 'Congo',
				'CD' => 'Congo, Democratic Republic',
				'CK' => 'Cook Islands',
				'CR' => 'Costa Rica',
				'CI' => 'Cote D\'Ivoire',
				'HR' => 'Croatia',
				'CU' => 'Cuba',
				'CY' => 'Cyprus',
				'CZ' => 'Czech Republic',
				'DK' => 'Denmark',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominican Republic',
				'EC' => 'Ecuador',
				'EG' => 'Egypt',
				'SV' => 'El Salvador',
				'GQ' => 'Equatorial Guinea',
				'ER' => 'Eritrea',
				'EE' => 'Estonia',
				'ET' => 'Ethiopia',
				'FK' => 'Falkland Islands (Malvinas)',
				'FO' => 'Faroe Islands',
				'FJ' => 'Fiji',
				'FI' => 'Finland',
				'FR' => 'France',
				'GF' => 'French Guiana',
				'PF' => 'French Polynesia',
				'TF' => 'French Southern Territories',
				'GA' => 'Gabon',
				'GM' => 'Gambia',
				'GE' => 'Georgia',
				'DE' => 'Germany',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GR' => 'Greece',
				'GL' => 'Greenland',
				'GD' => 'Grenada',
				'GP' => 'Guadeloupe',
				'GU' => 'Guam',
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'HM' => 'Heard Island & Mcdonald Islands',
				'VA' => 'Holy See (Vatican City State)',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HU' => 'Hungary',
				'IS' => 'Iceland',
				'IN' => 'India',
				'ID' => 'Indonesia',
				'IR' => 'Iran, Islamic Republic Of',
				'IQ' => 'Iraq',
				'IE' => 'Ireland',
				'IM' => 'Isle Of Man',
				'IL' => 'Israel',
				'IT' => 'Italy',
				'JM' => 'Jamaica',
				'JP' => 'Japan',
				'JE' => 'Jersey',
				'JO' => 'Jordan',
				'KZ' => 'Kazakhstan',
				'KE' => 'Kenya',
				'KI' => 'Kiribati',
				'KR' => 'Korea',
				'KP' => 'North Korea',
				'KW' => 'Kuwait',
				'KG' => 'Kyrgyzstan',
				'LA' => 'Lao People\'s Democratic Republic',
				'LV' => 'Latvia',
				'LB' => 'Lebanon',
				'LS' => 'Lesotho',
				'LR' => 'Liberia',
				'LY' => 'Libyan Arab Jamahiriya',
				'LI' => 'Liechtenstein',
				'LT' => 'Lithuania',
				'LU' => 'Luxembourg',
				'MO' => 'Macao',
				'MK' => 'Macedonia',
				'MG' => 'Madagascar',
				'MW' => 'Malawi',
				'MY' => 'Malaysia',
				'MV' => 'Maldives',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MH' => 'Marshall Islands',
				'MQ' => 'Martinique',
				'MR' => 'Mauritania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mexico',
				'FM' => 'Micronesia, Federated States Of',
				'MD' => 'Moldova',
				'MC' => 'Monaco',
				'MN' => 'Mongolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MA' => 'Morocco',
				'MZ' => 'Mozambique',
				'MM' => 'Myanmar',
				'NA' => 'Namibia',
				'NR' => 'Nauru',
				'NP' => 'Nepal',
				'NL' => 'Netherlands',
				'AN' => 'Netherlands Antilles',
				'NC' => 'New Caledonia',
				'NZ' => 'New Zealand',
				'NI' => 'Nicaragua',
				'NE' => 'Niger',
				'NG' => 'Nigeria',
				'NU' => 'Niue',
				'NF' => 'Norfolk Island',
				'MP' => 'Northern Mariana Islands',
				'NO' => 'Norway',
				'OM' => 'Oman',
				'PK' => 'Pakistan',
				'PW' => 'Palau',
				'PS' => 'Palestinian Territory, Occupied',
				'PA' => 'Panama',
				'PG' => 'Papua New Guinea',
				'PY' => 'Paraguay',
				'PE' => 'Peru',
				'PH' => 'Philippines',
				'PN' => 'Pitcairn',
				'PL' => 'Poland',
				'PT' => 'Portugal',
				'PR' => 'Puerto Rico',
				'QA' => 'Qatar',
				'RE' => 'Reunion',
				'RO' => 'Romania',
				'RU' => 'Russian Federation',
				'RW' => 'Rwanda',
				'BL' => 'Saint Barthelemy',
				'SH' => 'Saint Helena',
				'KN' => 'Saint Kitts And Nevis',
				'LC' => 'Saint Lucia',
				'MF' => 'Saint Martin',
				'PM' => 'Saint Pierre And Miquelon',
				'VC' => 'Saint Vincent And Grenadines',
				'WS' => 'Samoa',
				'SM' => 'San Marino',
				'ST' => 'Sao Tome And Principe',
				'SA' => 'Saudi Arabia',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SC' => 'Seychelles',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapore',
				'SK' => 'Slovakia',
				'SI' => 'Slovenia',
				'SB' => 'Solomon Islands',
				'SO' => 'Somalia',
				'ZA' => 'South Africa',
				'GS' => 'South Georgia And Sandwich Isl.',
				'ES' => 'Spain',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudan',
				'SR' => 'Suriname',
				'SJ' => 'Svalbard And Jan Mayen',
				'SZ' => 'Swaziland',
				'SE' => 'Sweden',
				'CH' => 'Switzerland',
				'SY' => 'Syrian Arab Republic',
				'TW' => 'Taiwan',
				'TJ' => 'Tajikistan',
				'TZ' => 'Tanzania',
				'TH' => 'Thailand',
				'TL' => 'Timor-Leste',
				'TG' => 'Togo',
				'TK' => 'Tokelau',
				'TO' => 'Tonga',
				'TT' => 'Trinidad And Tobago',
				'TN' => 'Tunisia',
				'TR' => 'Turkey',
				'TM' => 'Turkmenistan',
				'TC' => 'Turks And Caicos Islands',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraine',
				'AE' => 'United Arab Emirates',
				'GB' => 'United Kingdom',
				'US' => 'United States',
				'UM' => 'United States Outlying Islands',
				'UY' => 'Uruguay',
				'UZ' => 'Uzbekistan',
				'VU' => 'Vanuatu',
				'VE' => 'Venezuela',
				'VN' => 'Vietnam',
				'VG' => 'Virgin Islands, British',
				'VI' => 'Virgin Islands, U.S.',
				'WF' => 'Wallis And Futuna',
				'EH' => 'Western Sahara',
				'YE' => 'Yemen',
				'ZM' => 'Zambia',
				'ZW' => 'Zimbabwe'
			);

			// Apply filter
			$countries_alpha_2 = apply_filters('wsf_config_countries_alpha_2', $countries_alpha_2);

			// Return as options
			if($options) {

				foreach($countries_alpha_2 as $value => $text) {


				}
			}

			return $countries_alpha_2;
		}

		public static function get_currencies() {

			$currencies = array(

				'AED' => array('s' => 'د.إ','n' => 'United Arab Emirates dirham'),
				'AFN' => array('s' => '؋','n' => 'Afghan afghani'),
				'ALL' => array('s' => 'L','n' => 'Albanian lek'),
				'AMD' => array('s' => 'AMD','n' => 'Armenian dram'),
				'ANG' => array('s' => 'ƒ','n' => 'Netherlands Antillean guilder'),
				'AOA' => array('s' => 'Kz','n' => 'Angolan kwanza'),
				'ARS' => array('s' => '$','n' => 'Argentine peso'),
				'AUD' => array('s' => '$','n' => 'Australian dollar'),
				'AWG' => array('s' => 'Afl.','n' => 'Aruban florin'),
				'AZN' => array('s' => 'AZN','n' => 'Azerbaijani manat'),
				'BAM' => array('s' => 'KM','n' => 'Bosnia and Herzegovina convertible mark'),
				'BBD' => array('s' => '$','n' => 'Barbadian dollar'),
				'BDT' => array('s' => '৳ ','n' => 'Bangladeshi taka'),
				'BGN' => array('s' => 'лв.','n' => 'Bulgarian lev'),
				'BHD' => array('s' => '.د.ب','n' => 'Bahraini dinar'),
				'BIF' => array('s' => 'Fr','n' => 'Burundian franc'),
				'BMD' => array('s' => '$','n' => 'Bermudian dollar'),
				'BND' => array('s' => '$','n' => 'Brunei dollar'),
				'BOB' => array('s' => 'Bs.','n' => 'Bolivian boliviano'),
				'BRL' => array('s' => 'R$','n' => 'Brazilian real'),
				'BSD' => array('s' => '$','n' => 'Bahamian dollar'),
				'BTC' => array('s' => '฿','n' => 'Bitcoin'),
				'BTN' => array('s' => 'Nu.','n' => 'Bhutanese ngultrum'),
				'BWP' => array('s' => 'P','n' => 'Botswana pula'),
				'BYR' => array('s' => 'Br','n' => 'Belarusian ruble (old)'),
				'BYN' => array('s' => 'Br','n' => 'Belarusian ruble'),
				'BZD' => array('s' => '$','n' => 'Belize dollar'),
				'CAD' => array('s' => '$','n' => 'Canadian dollar'),
				'CDF' => array('s' => 'Fr','n' => 'Congolese franc'),
				'CHF' => array('s' => 'CHF','n' => 'Swiss franc'),
				'CLP' => array('s' => '$','n' => 'Chilean peso'),
				'CNY' => array('s' => '¥','n' => 'Chinese yuan'),
				'COP' => array('s' => '$','n' => 'Colombian peso'),
				'CRC' => array('s' => '₡','n' => 'Costa Rican colón'),
				'CUC' => array('s' => '$','n' => 'Cuban convertible peso'),
				'CUP' => array('s' => '$','n' => 'Cuban peso'),
				'CVE' => array('s' => '$','n' => 'Cape Verdean escudo'),
				'CZK' => array('s' => 'Kč','n' => 'Czech koruna'),
				'DJF' => array('s' => 'Fr','n' => 'Djiboutian franc'),
				'DKK' => array('s' => 'DKK','n' => 'Danish krone'),
				'DOP' => array('s' => 'RD$','n' => 'Dominican peso'),
				'DZD' => array('s' => 'د.ج','n' => 'Algerian dinar'),
				'EGP' => array('s' => 'EGP','n' => 'Egyptian pound'),
				'ERN' => array('s' => 'Nfk','n' => 'Eritrean nakfa'),
				'ETB' => array('s' => 'Br','n' => 'Ethiopian birr'),
				'EUR' => array('s' => '€','n' => 'Euro'),
				'FJD' => array('s' => '$','n' => 'Fijian dollar'),
				'FKP' => array('s' => '£','n' => 'Falkland Islands pound'),
				'GBP' => array('s' => '£','n' => 'Pound sterling'),
				'GEL' => array('s' => '₾','n' => 'Georgian lari'),
				'GGP' => array('s' => '£','n' => 'Guernsey pound'),
				'GHS' => array('s' => '₵','n' => 'Ghana cedi'),
				'GIP' => array('s' => '£','n' => 'Gibraltar pound'),
				'GMD' => array('s' => 'D','n' => 'Gambian dalasi'),
				'GNF' => array('s' => 'Fr','n' => 'Guinean franc'),
				'GTQ' => array('s' => 'Q','n' => 'Guatemalan quetzal'),
				'GYD' => array('s' => '$','n' => 'Guyanese dollar'),
				'HKD' => array('s' => '$','n' => 'Hong Kong dollar'),
				'HNL' => array('s' => 'L','n' => 'Honduran lempira'),
				'HRK' => array('s' => 'kn','n' => 'Croatian kuna'),
				'HTG' => array('s' => 'G','n' => 'Haitian gourde'),
				'HUF' => array('s' => 'Ft','n' => 'Hungarian forint'),
				'IDR' => array('s' => 'Rp','n' => 'Indonesian rupiah'),
				'ILS' => array('s' => '₪','n' => 'Israeli new shekel'),
				'IMP' => array('s' => '£','n' => 'Manx pound'),
				'INR' => array('s' => '₹','n' => 'Indian rupee'),
				'IQD' => array('s' => 'ع.د','n' => 'Iraqi dinar'),
				'IRR' => array('s' => '﷼','n' => 'Iranian rial'),
				'IRT' => array('s' => 'تومان','n' => 'Iranian toman'),
				'ISK' => array('s' => 'kr.','n' => 'Icelandic króna'),
				'JEP' => array('s' => '£','n' => 'Jersey pound'),
				'JMD' => array('s' => '$','n' => 'Jamaican dollar'),
				'JOD' => array('s' => 'د.ا','n' => 'Jordanian dinar'),
				'JPY' => array('s' => '¥','n' => 'Japanese yen'),
				'KES' => array('s' => 'KSh','n' => 'Kenyan shilling'),
				'KGS' => array('s' => 'сом','n' => 'Kyrgyzstani som'),
				'KHR' => array('s' => '៛','n' => 'Cambodian riel'),
				'KMF' => array('s' => 'Fr','n' => 'Comorian franc'),
				'KPW' => array('s' => '₩','n' => 'North Korean won'),
				'KRW' => array('s' => '₩','n' => 'South Korean won'),
				'KWD' => array('s' => 'د.ك','n' => 'Kuwaiti dinar'),
				'KYD' => array('s' => '$','n' => 'Cayman Islands dollar'),
				'KZT' => array('s' => '₸','n' => 'Kazakhstani tenge'),
				'LAK' => array('s' => '₭','n' => 'Lao kip'),
				'LBP' => array('s' => 'ل.ل','n' => 'Lebanese pound'),
				'LKR' => array('s' => 'රු','n' => 'Sri Lankan rupee'),
				'LRD' => array('s' => '$','n' => 'Liberian dollar'),
				'LSL' => array('s' => 'L','n' => 'Lesotho loti'),
				'LYD' => array('s' => 'ل.د','n' => 'Libyan dinar'),
				'MAD' => array('s' => 'د.م.','n' => 'Moroccan dirham'),
				'MDL' => array('s' => 'MDL','n' => 'Moldovan leu'),
				'MGA' => array('s' => 'Ar','n' => 'Malagasy ariary'),
				'MKD' => array('s' => 'ден','n' => 'Macedonian denar'),
				'MMK' => array('s' => 'Ks','n' => 'Burmese kyat'),
				'MNT' => array('s' => '₮','n' => 'Mongolian tögrög'),
				'MOP' => array('s' => 'P','n' => 'Macanese pataca'),
				'MRU' => array('s' => 'UM','n' => 'Mauritanian ouguiya'),
				'MUR' => array('s' => '₨','n' => 'Mauritian rupee'),
				'MVR' => array('s' => '.ރ','n' => 'Maldivian rufiyaa'),
				'MWK' => array('s' => 'MK','n' => 'Malawian kwacha'),
				'MXN' => array('s' => '$','n' => 'Mexican peso'),
				'MYR' => array('s' => 'RM','n' => 'Malaysian ringgit'),
				'MZN' => array('s' => 'MT','n' => 'Mozambican metical'),
				'NAD' => array('s' => 'N$','n' => 'Namibian dollar'),
				'NGN' => array('s' => '₦','n' => 'Nigerian naira'),
				'NIO' => array('s' => 'C$','n' => 'Nicaraguan córdoba'),
				'NOK' => array('s' => 'kr','n' => 'Norwegian krone'),
				'NPR' => array('s' => '₨','n' => 'Nepalese rupee'),
				'NZD' => array('s' => '$','n' => 'New Zealand dollar'),
				'OMR' => array('s' => 'ر.ع.','n' => 'Omani rial'),
				'PAB' => array('s' => 'B/.','n' => 'Panamanian balboa'),
				'PEN' => array('s' => 'S/','n' => 'Sol'),
				'PGK' => array('s' => 'K','n' => 'Papua New Guinean kina'),
				'PHP' => array('s' => '₱','n' => 'Philippine peso'),
				'PKR' => array('s' => '₨','n' => 'Pakistani rupee'),
				'PLN' => array('s' => 'zł','n' => 'Polish złoty'),
				'PRB' => array('s' => 'р.','n' => 'Transnistrian ruble'),
				'PYG' => array('s' => '₲','n' => 'Paraguayan guaraní'),
				'QAR' => array('s' => 'ر.ق','n' => 'Qatari riyal'),
				'RON' => array('s' => 'lei','n' => 'Romanian leu'),
				'RSD' => array('s' => 'рсд','n' => 'Serbian dinar'),
				'RUB' => array('s' => '₽','n' => 'Russian ruble'),
				'RWF' => array('s' => 'Fr','n' => 'Rwandan franc'),
				'SAR' => array('s' => 'ر.س','n' => 'Saudi riyal'),
				'SBD' => array('s' => '$','n' => 'Solomon Islands dollar'),
				'SCR' => array('s' => '₨','n' => 'Seychellois rupee'),
				'SDG' => array('s' => 'ج.س.','n' => 'Sudanese pound'),
				'SEK' => array('s' => 'kr','n' => 'Swedish krona'),
				'SGD' => array('s' => '$','n' => 'Singapore dollar'),
				'SHP' => array('s' => '£','n' => 'Saint Helena pound'),
				'SLL' => array('s' => 'Le','n' => 'Sierra Leonean leone'),
				'SOS' => array('s' => 'Sh','n' => 'Somali shilling'),
				'SRD' => array('s' => '$','n' => 'Surinamese dollar'),
				'SSP' => array('s' => '£','n' => 'South Sudanese pound'),
				'STN' => array('s' => 'Db','n' => 'São Tomé and Príncipe dobra'),
				'SYP' => array('s' => 'ل.س','n' => 'Syrian pound'),
				'SZL' => array('s' => 'L','n' => 'Swazi lilangeni'),
				'THB' => array('s' => '฿','n' => 'Thai baht'),
				'TJS' => array('s' => 'ЅМ','n' => 'Tajikistani somoni'),
				'TMT' => array('s' => 'm','n' => 'Turkmenistan manat'),
				'TND' => array('s' => 'د.ت','n' => 'Tunisian dinar'),
				'TOP' => array('s' => 'T$','n' => 'Tongan paʻanga'),
				'TRY' => array('s' => '₺','n' => 'Turkish lira'),
				'TTD' => array('s' => '$','n' => 'Trinidad and Tobago dollar'),
				'TWD' => array('s' => 'NT$','n' => 'New Taiwan dollar'),
				'TZS' => array('s' => 'Sh','n' => 'Tanzanian shilling'),
				'UAH' => array('s' => '₴','n' => 'Ukrainian hryvnia'),
				'UGX' => array('s' => 'UGX','n' => 'Ugandan shilling'),
				'USD' => array('s' => '$','n' => 'United States (US) dollar'),
				'UYU' => array('s' => '$','n' => 'Uruguayan peso'),
				'UZS' => array('s' => 'UZS','n' => 'Uzbekistani som'),
				'VEF' => array('s' => 'Bs F','n' => 'Venezuelan bolívar'),
				'VES' => array('s' => 'Bs.S','n' => 'Bolívar soberano'),
				'VND' => array('s' => '₫','n' => 'Vietnamese đồng'),
				'VUV' => array('s' => 'Vt','n' => 'Vanuatu vatu'),
				'WST' => array('s' => 'T','n' => 'Samoan tālā'),
				'XAF' => array('s' => 'CFA','n' => 'Central African CFA franc'),
				'XCD' => array('s' => '$','n' => 'East Caribbean dollar'),
				'XOF' => array('s' => 'CFA','n' => 'West African CFA franc'),
				'XPF' => array('s' => 'Fr','n' => 'CFP franc'),
				'YER' => array('s' => '﷼','n' => 'Yemeni rial'),
				'ZAR' => array('s' => 'R','n' => 'South African rand'),
				'ZMW' => array('s' => 'ZK','n' => 'Zambian kwacha')
			);

			// Apply filter
			$currencies = apply_filters('wsf_config_currencies', $currencies);

			return $currencies;
		}

		public static function get_ecommerce() {

			// Check cache
			if(self::$ecommerce !== false) { return self::$ecommerce; }			

			$ecommerce = array(

				'cart_price_types' => array(

					'subtotal' 			=> array('label' => __('Subtotal', 'ws-form'), 'priority' => 10, 'multiple' => false, 'render' => true),
					'shipping' 			=> array('label' => __('Shipping', 'ws-form'), 'priority' => 20),
					'discount'			=> array('label' => __('Discount', 'ws-form'), 'priority' => 30),
					'handling_fee'		=> array('label' => __('Handling Fee', 'ws-form'), 'priority' => 40),
					'shipping_discount'	=> array('label' => __('Shipping Discount', 'ws-form'), 'priority' => 50),
					'insurance'			=> array('label' => __('Insurance', 'ws-form'), 'priority' => 60),
					'gift_wrap'			=> array('label' => __('Gift Wrap', 'ws-form'), 'priority' => 70),
					'other'				=> array('label' => __('Other', 'ws-form'), 'priority' => 80),
					'tax'				=> array('label' => __('Tax', 'ws-form'), 'priority' => 100)
				),

				'status' => array(

					'new'				=> array('label' =>	__('New', 'ws-form')),
					'pending_payment'	=> array('label' =>	__('Pending Payment', 'ws-form')),
					'processing'		=> array('label' =>	__('Processing', 'ws-form')),
					'active'			=> array('label' =>	__('Active', 'ws-form')),
					'cancelled'			=> array('label' =>	__('Cancelled', 'ws-form')),
					'authorized'		=> array('label' =>	__('Authorized', 'ws-form')),
					'completed'			=> array('label' =>	__('Completed', 'ws-form')),
					'failed'			=> array('label' =>	__('Failed', 'ws-form')),
					'refunded'			=> array('label' =>	__('Refunded', 'ws-form')),
					'voided'			=> array('label' =>	__('Voided', 'ws-form'))
				),

				'meta_keys' => array(

					'ecommerce_cart_total'		=> array('label' =>	__('Total', 'ws-form'), 'type' => 'price', 'priority' => 200),
					'ecommerce_status'			=> array('label' =>	__('Order Status', 'ws-form'), 'lookup' => 'status', 'priority' => 5),
					'ecommerce_transaction_id'	=> array('label' =>	__('Transaction ID', 'ws-form'), 'priority' => 1010),
					'ecommerce_payment_method'	=> array('label' =>	__('Payment Method', 'ws-form'), 'priority' => 1020)
				)
			);

			// Apply filter
			$ecommerce = apply_filters('wsf_config_ecommerce', $ecommerce);

			// Cache
			self::$ecommerce = $ecommerce;

			return $ecommerce;
		}
	}