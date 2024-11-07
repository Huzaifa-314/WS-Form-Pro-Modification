<?php

	class WS_Form_Action_Custom_Endpoint extends WS_Form_Action {

		public $id = 'customendpoint';
		public $pro_required = true;
		public $label;
		public $label_action;
		public $events;
		public $multiple = true;
		public $configured = false;
		public $priority = 150;
		public $can_repost = true;
		public $form_add = false;

		// Config
		public $endpoint;
		public $request_method;
		public $content_type;

		public $authentication;
		public $username;
		public $password;

		public $file_to_url;
		public $array_to_delimited;

		public $field_mapping;
		public $custom_mapping;
		public $header_mapping;

		public function __construct() {

			// Set label
			$this->label = __('Custom Endpoint', 'ws-form');

			// Set label for actions pull down
			$this->label_action = __('Push to Custom Endpoint', 'ws-form');

			// Events
			$this->events = array('submit');

			// Register config filters
			add_filter('wsf_config_meta_keys', array($this, 'config_meta_keys'), 10, 2);

			// Register action
			parent::register($this);
		}

		// Post to endpoint
		public function post($form, &$submit, $config) {

			// Load configuration
			self::load_config($config);

			$data = array();

			// Get fields in single dimension array
			$fields = WS_Form_Common::get_fields_from_form($form);

			// Process field mapping
			foreach($this->field_mapping as $field_map) {

				// Get key
				$endpoint_key = $field_map['action_' . $this->id . '_endpoint_key'];
				if(empty($endpoint_key)) { continue; }
				$endpoint_key = WS_Form_Common::parse_variables_process($endpoint_key, $form, $submit, 'text/plain');

				// Get value
				$field_id = $field_map['ws_form_field'];
				if(empty($field_id)) { continue; }
				$endpoint_value = parent::get_submit_value($submit, WS_FORM_FIELD_PREFIX . $field_id, '', true);

				// Get field type
				if(!isset($fields[$field_id])) { continue; }
				$field = $fields[$field_id];

				// Process by field type
				switch($field->type) {

					case 'file' :
					case 'signature' :

						// Add URLs to file objects
						if(is_array($endpoint_value)) {

							$file_urls = array();

							foreach($endpoint_value as $file_object_index => $file_object) {

								if(
									isset($file_object['url']) ||
									!isset($file_object['name']) ||
									!isset($file_object['size']) ||
									!isset($file_object['type']) ||
									!isset($file_object['path'])

								) { continue; }

								// Get handler
								$handler = isset($file_object['handler']) ? $file_object['handler'] : 'wsform';

								// Get URL
								if(isset(WS_Form_File_Handler_WS_Form::$file_handlers[$handler])) {

									$file_url = WS_Form_File_Handler_WS_Form::$file_handlers[$handler]->get_url($file_object, $field_id, $file_object_index, $submit->hash);
									$endpoint_value[$file_object_index]['url'] = $file_url;
									$file_urls[] = $file_url;
								}
							}

							if($this->file_to_url) {

								$endpoint_value = implode(',', $file_urls);
							}
						}

						break;
				}

				// Array to delimited
				if(is_array($endpoint_value) && $this->array_to_delimited) {

					$endpoint_value = implode(',', $endpoint_value);
				}

				// Save field
				$data[$endpoint_key] = $endpoint_value;
			}

			// Process custom mapping
			foreach($this->custom_mapping as $custom_map) {

				// Get key
				$endpoint_key = $custom_map['action_' . $this->id . '_endpoint_key'];
				if(empty($endpoint_key)) { continue; }
				$endpoint_key = WS_Form_Common::parse_variables_process($endpoint_key, $form, $submit, 'text/plain');

				// Get value
				$endpoint_value = WS_Form_Common::parse_variables_process($custom_map['action_' . $this->id . '_endpoint_value'], $form, $submit, 'text/plain');

				// Array to delimited
				if(is_array($endpoint_value) && $this->array_to_delimited) {

					$endpoint_value = implode(',', $endpoint_value);
				}

				// Save field
				$data[$endpoint_key] = $endpoint_value;
			}

			// Encode if not GET method
			if(
				($this->content_type == 'application/json') &&
				($this->request_method != 'GET')
			) {

				$data = wp_json_encode($data);
				parent::success(sprintf(__('HTTP payload: <pre>%s</pre>', 'ws-form'), $data));
			} else {

				parent::success(sprintf(__('HTTP payload: <pre>%s</pre>', 'ws-form'), print_r($data, true)));
			}

			// Authentication
			$username = false;
			$password = false;
			if($this->authentication != '') {

				$username = $this->username;
				$password = $this->password;

				parent::success(sprintf(__('HTTP authentication: %s', 'ws-form'), $this->authentication));
			}

			// Process HTTP headers
			$http_headers = array();
			foreach($this->header_mapping as $header_map) {

				// Get header name
				$header_name = WS_Form_Common::parse_variables_process($header_map['action_' . $this->id . '_header_name'], $form, $submit, 'text/plain');
				if(empty($header_name)) { continue; }

				// Get header value
				$header_value = WS_Form_Common::parse_variables_process($header_map['action_' . $this->id . '_header_value'], $form, $submit, 'text/plain');

				// Save field
				$http_headers[$header_name] = $header_value;
			}
			if(count($http_headers) > 0) {

				parent::success(sprintf(__('HTTP headers: <pre>%s</pre>', 'ws-form'), print_r($http_headers, true)));
			}

			parent::success(sprintf(__('HTTP endpoint: %s', 'ws-form'), $this->endpoint));
			parent::success(sprintf(__('HTTP request method: %s', 'ws-form'), $this->request_method));
			parent::success(sprintf(__('HTTP content type: %s', 'ws-form'), $this->content_type));

			// Make API request
			$http_response = parent::api_call($this->endpoint, '', $this->request_method, $data, $http_headers, $this->authentication, $username, $password, $this->content_type, $this->content_type);
			parent::success(__('Pushed to custom endpoint' , 'ws-form'));

			if($http_response['error']) {

				// Errors
				parent::error($http_response['error_message']);

			} else {

				// Success
				$http_code = $http_response['http_code'];
				parent::success(sprintf(__('HTTP response code: %u', 'ws-form'), $http_code));
				parent::success(sprintf(__('HTTP response: <pre>%s</pre>', 'ws-form'), print_r($http_response, true)));
			}

			return true;
		}

		// Meta keys for this action
		public function config_meta_keys($meta_keys = array(), $form_id = 0) {

			// Build config_meta_keys
			$config_meta_keys = array(

				// Custom Endpoint
				'action_' . $this->id . '_endpoint'	=> array(

					'label'							=>	__('URL of Endpoint', 'ws-form'),
					'type'							=>	'text',
					'help'							=>	__('URL of endpoint to send data to.', 'ws-form'),
					'default'						=>	'https://'
				),

				// Request Method
				'action_' . $this->id . '_request_method'	=> array(

					'label'							=>	__('Request Method', 'ws-form'),
					'type'							=>	'select',
					'help'							=>	__('Select the HTTP request method to use.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'POST', 'text' => 'POST'),
						array('value' => 'GET', 'text' => 'GET'),
						array('value' => 'PUT', 'text' => 'PUT'),
						array('value' => 'DELETE', 'text' => 'DELETE'),
						array('value' => 'PATCH', 'text' => 'PATCH'),
					),
					'default'						=>	'POST'
				),

				// Content type
				'action_' . $this->id . '_content_type'	=> array(

					'label'						=>	__('Content Type', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the content type.', 'ws-form'),
					'options'					=>	array(

						array('value' => 'application/json', 'text' => 'JSON (application/json)'),
						array('value' => 'application/x-www-form-urlencoded', 'text' => sprintf('%s (application/x-www-form-urlencoded)', __('URL Encoded', 'ws-form'))),
					),
					'default'					=>	'application/json',
				),

				// Authentication
				'action_' . $this->id . '_authentication'	=> array(

					'label'						=>	__('Authentication', 'ws-form'),
					'type'						=>	'select',
					'help'						=>	__('Select the type of authentication.', 'ws-form'),
					'options'					=>	array(

						array('value' => '', 'text' => __('None', 'ws-form')),
						array('value' => 'basic', 'text' => __('Basic', 'ws-form')),
					),
					'default'					=>	'',
				),

				// Username
				'action_' . $this->id . '_username'	=> array(

					'label'							=>	__('Username', 'ws-form'),
					'type'							=>	'text',
					'help'							=>	__('Authentication username.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'action_' . $this->id . '_authentication',
							'meta_value'	=>	''
						)
					)
				),

				// Password
				'action_' . $this->id . '_password'	=> array(

					'label'							=>	__('Password', 'ws-form'),
					'type'							=>	'password',
					'help'							=>	__('Authentication password.', 'ws-form'),
					'condition'					=>	array(

						array(

							'logic'			=>	'!=',
							'meta_key'		=>	'action_' . $this->id . '_authentication',
							'meta_value'	=>	''
						)
					)
				),

				// File to URL
				'action_' . $this->id . '_file_to_url'	=> array(

					'label'						=>	__('Use URLs for File Fields', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, file and signature fields will be converted to URLs instead of file objects. Multiple files are comma separated.', 'ws-form'),
					'default'					=>	'',
				),

				// Array to delimited
				'action_' . $this->id . '_array_to_delimited'	=> array(

					'label'						=>	__('Convert Arrays to Delimited Text', 'ws-form'),
					'type'						=>	'checkbox',
					'help'						=>	__('If checked, fields that have multiple values (e.g. Checkboxes) will be converted to comma separated values.', 'ws-form'),
					'default'					=>	'',
				),

				// Field mapping
				'action_' . $this->id . '_field_mapping'	=> array(

					'label'						=>	__('Field Mapping', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	sprintf(

						/* translators: %s = WS Form */
						__('Map %s fields to endpoint keys.', 'ws-form'),

						WS_FORM_NAME_GENERIC
					),
					'meta_keys'					=>	array(

						'ws_form_field',
						'action_' . $this->id . '_endpoint_key'
					)
				),

				// Field meta mapping
				'action_' . $this->id . '_custom_mapping'	=> array(

					'label'						=>	__('Custom Mapping', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	__('Map custom values to endpoint keys.', 'ws-form'),
					'meta_keys'					=>	array(

						'action_' . $this->id . '_endpoint_key',
						'action_' . $this->id . '_endpoint_value'
					)
				),

				// key
				'action_' . $this->id . '_endpoint_key'	=> array(

					'label'						=>	__('Key', 'ws-form'),
					'type'						=>	'text'
				),

				// value
				'action_' . $this->id . '_endpoint_value'	=> array(

					'label'						=>	__('Value', 'ws-form'),
					'type'						=>	'text'
				),

				// Header mapping
				'action_' . $this->id . '_header_mapping'	=> array(

					'label'						=>	__('HTTP Header Name/Values', 'ws-form'),
					'type'						=>	'repeater',
					'help'						=>	__('Add HTTP Header name/values to your endpoint call.', 'ws-form'),
					'meta_keys'					=>	array(

						'action_' . $this->id . '_header_name',
						'action_' . $this->id . '_header_value'
					)
				),

				// Header name
				'action_' . $this->id . '_header_name'	=> array(

					'label'						=>	__('Name', 'ws-form'),
					'type'						=>	'text'
				),

				// Header value
				'action_' . $this->id . '_header_value'	=> array(

					'label'						=>	__('Value', 'ws-form'),
					'type'						=>	'text'
				),
			);

			// Merge
			$meta_keys = array_merge($meta_keys, $config_meta_keys);

			return $meta_keys;
		}

		// Process wp_error_process
		public function wp_error_process($post) {

			$error_messages = $post->get_error_messages();
			self::error_js($error_messages);
		}

		// Error
		public function error_js($error_messages) {

			if(!is_array($error_messages)) { $error_messages = array($error_messages); }

			foreach($error_messages as $error_message) {

				// Show the message
				parent::error($error_message, array(

					array(

						'action' => 'message',
						'message' => $error_message,
						'type' => 'danger',
						'method' => $this->message_method,
						'clear' => $this->message_clear,
						'scroll_top' => $this->message_scroll_top,
						'duration' => $this->message_duration,
						'form_hide' => $this->message_form_hide,
						'form_show' => $this->message_form_hide,
						'message_hide' => false
					)
				));
			}
		}

		// Load config for this action
		public function load_config($config = array()) {

			$this->endpoint = parent::get_config($config, 'action_' . $this->id . '_endpoint', '');

			$this->request_method = parent::get_config($config, 'action_' . $this->id . '_request_method');
			if(!in_array($this->request_method, array('POST', 'GET', 'PUT', 'DELETE', 'PATCH'))) { $this->request_method = 'POST'; }

			$this->content_type = parent::get_config($config, 'action_' . $this->id . '_content_type', '');

			$this->authentication = parent::get_config($config, 'action_' . $this->id . '_authentication', '');
			$this->username = parent::get_config($config, 'action_' . $this->id . '_username', '');
			$this->password = parent::get_config($config, 'action_' . $this->id . '_password', '');

			$this->file_to_url = parent::get_config($config, 'action_' . $this->id . '_file_to_url', '');
			$this->array_to_delimited = parent::get_config($config, 'action_' . $this->id . '_array_to_delimited', '');

			// Field mapping
			$this->field_mapping = parent::get_config($config, 'action_' . $this->id . '_field_mapping', array());
			if(!is_array($this->field_mapping)) { $this->field_mapping = array(); }

			// Custom mapping
			$this->custom_mapping = parent::get_config($config, 'action_' . $this->id . '_custom_mapping', array());
			if(!is_array($this->custom_mapping)) { $this->custom_mapping = array(); }

			// Header mapping
			$this->header_mapping = parent::get_config($config, 'action_' . $this->id . '_header_mapping', array());
			if(!is_array($this->header_mapping)) { $this->header_mapping = array(); }
		}


		// Get settings
		public function get_action_settings() {

			$settings = array(

				'meta_keys'		=> array(

					'action_' . $this->id . '_endpoint',
					'action_' . $this->id . '_request_method',
					'action_' . $this->id . '_content_type',
					'action_' . $this->id . '_authentication',
					'action_' . $this->id . '_username',
					'action_' . $this->id . '_password',
					'action_' . $this->id . '_file_to_url',
					'action_' . $this->id . '_array_to_delimited',
					'action_' . $this->id . '_field_mapping',
					'action_' . $this->id . '_custom_mapping',
					'action_' . $this->id . '_header_mapping',
				)
			);

			// Wrap settings so they will work with sidebar_html function in admin.js
			$settings = parent::get_settings_wrapper($settings);

			// Add labels
			$settings->label = $this->label;
			$settings->label_action = $this->label_action;

			// Add multiple
			$settings->multiple = $this->multiple;

			// Add events
			$settings->events = $this->events;

			// Add can_repost
			$settings->can_repost = $this->can_repost;

			// Apply filter
			$settings = apply_filters('wsf_action_' . $this->id . '_settings', $settings);

			return $settings;
		}
	}

	new WS_Form_Action_Custom_Endpoint();
