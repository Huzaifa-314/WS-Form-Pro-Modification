<?php

	/**
	 * Common functions used by this plugin
	 */
	class WS_Form_Common {

		// Cookie prefix
		const WS_FORM_COOKIE_PREFIX = 'ws_form_';

		// Admin messages
		public static $admin_messages = array();

		// IP lookup response
		public static $ip_lookup_response = false;

		// NONCE verified
		public static $nonce_verified = false;

		// Options cache (Don't use WordPress option caching as it is so slow)
		public static $options = array();

		// Groups cache
		public static $groups = false;

		// Sections cache
		public static $sections = false;

		// Fields cache
		public static $fields = false;

		// Field types cache
		public static $field_types = false;

		// Admin messages - Push
		public static function admin_message_push($message, $type = 'notice-success', $dismissible = true, $nag_notice = true) {

			self::$admin_messages[] = array(

				'message'		=>	$message,
				'type'			=>	$type,
				'dismissible'	=>	$dismissible,
				'nag_notice'	=>	$nag_notice
			);
		}

		// Admin messages - Render
		public static function admin_messages_render() {

			// Server side notices
			foreach(self::$admin_messages as $admin_message) {

				$message = $admin_message['message'];
				$type = isset($admin_message['type']) ? $admin_message['type'] : 'notice-success';
				$dismissible = isset($admin_message['dismissible']) ? $admin_message['dismissible'] : true;
				$nag_notice = isset($admin_message['nag_notice']) ? $admin_message['nag_notice'] : false;

				self::admin_message_render($message, $type, $dismissible, $nag_notice);
			}
		}

		// Admin messages - Render single
		public static function admin_message_render($message, $type = 'notice-success', $dismissible = true, $nag_notice = false, $class = '') {

			if(!(defined('DISABLE_NAG_NOTICES') && DISABLE_NAG_NOTICES && $nag_notice)) {

				// $message may contain HTML
				echo sprintf('<div class="notice %s"><p>%s</p></div>', esc_attr($type . ($dismissible ? ' is-dismissible' : '') . ($class ? ' '  . $class : '')), str_replace("\n", "<br />\n", $message));	// phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			}
		}

		// Admin messages - Get count
		public static function get_admin_message_count() {

			return count(self::$admin_messages);
		}

		// Wrapper classes
		public static function wrapper_classes() {

			$wrapper_classes_array = array('wrap');

			// Detect if this plugin is being hosted on wordpress.com
			if(
				isset($_SERVER) && // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				isset($_SERVER['HTTP_X_PRESSABLE_PROXY']) && // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				(isset($_SERVER['HTTP_X_PRESSABLE_PROXY']) == 'wordpress')// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitiz	ed
			) {

				$wrapper_classes_array[] = 'wsf-wpcom';
			}

			// Output classes
			esc_attr_e(implode(' ', $wrapper_classes_array));
		}

		// Get option name and options
		public static function get_options($key, $enable_cache) {

			// Default return values
			$option_name = WS_FORM_OPTION_NAME;
			$options = false;

			// Check for action related keys (Disabled by default)
			if(apply_filters('wsf_option_separate_action', WS_FORM_OPTION_SEPARATE_ACTION)) {

				if(strpos($key, 'action_') === 0) {

					$next_underscore = strpos($key, '_', 7);

					if($next_underscore !== false) {

						// Set key prefix
						$key_prefix = substr($key, 0, $next_underscore);

						// Set option name
						$option_name = sprintf('%s_%s', WS_FORM_OPTION_NAME, $key_prefix);

						// Get options
						$options = self::get_options_by_option_name($option_name, $key_prefix, $enable_cache);
					}
				}
			}

			// Check for CSS related keys (Enabled by default)
			if(apply_filters('wsf_option_separate_css', WS_FORM_OPTION_SEPARATE_CSS)) {

				if(strpos($key, 'css_') === 0) {

					// Set key prefix
					$key_prefix = 'css';

					// Set option name
					$option_name = sprintf('%s_%s', WS_FORM_OPTION_NAME, $key_prefix);

					// Get options
					$options = self::get_options_by_option_name($option_name, $key_prefix, $enable_cache);
				}
			}

			// Load options if they haven't been loaded already
			if($options === false) {

				// Check cache
				if($enable_cache && isset(self::$options[$option_name])) {

					$options = self::$options[$option_name];

				} else {

					$options = get_option($option_name, false);

					// Check options
					if(!is_array($options)) { $options = array(); }

					// Cache options
					self::$options[$option_name] = $options;
				}
			}

			// Return option name
			return array('name' => $option_name, 'options' => $options);
		}

		// Migrate check
		public static function get_options_by_option_name($option_name, $key_prefix, $enable_cache) {

			// Check cache
			if($enable_cache && isset(self::$options[$option_name])) {

				return self::$options[$option_name];

			} else {

				// Check if options have already been migrated
				$options = get_option($option_name, false);

				// If no options found, migrate any existing options to the new option name
				if($options === false) {

					$options = self::options_migrate($option_name, $key_prefix);
				}

				// Check options
				if(!is_array($options)) { $options = array(); }

				return $options;
			}
		}

		// Migrate 
		public static function options_migrate($option_name_new, $key_prefix) {

			// Get main options data
			$options = get_option(WS_FORM_OPTION_NAME, false);

			// Build old and new options array
			$options_old = array();
			$options_new = array();

			// If options exist, extract keys that start with key_prefix
			if(is_array($options)) {

				foreach($options as $key => $value) {

					if(strpos($key, $key_prefix) === 0) {

						$options_new[$key] = $value;

					} else {

						$options_old[$key] = $value;
					}
				}

				// Write new options
				if(update_option($option_name_new, $options_new, 'no')) {

					// Cache new options
					self::$options[$option_name_new] = $options_new;

					// Write old options
					update_option(WS_FORM_OPTION_NAME, $options_old, 'no');

					// Cache old options
					self::$options[WS_FORM_OPTION_NAME] = $options_old;
				}
			}

			return $options_new;
		}

		// Get plugin option key value
		public static function option_get($key, $default = false, $default_set = false, $enable_cache = true, $use_default_if_blank = false) {

			// Get option name
			$option_name_return = self::get_options($key, $enable_cache);
			$option_name = $option_name_return['name'];
			$options = $option_name_return['options'];

			// Return default
			$value = $default;

			// If key exists, return the value
			if(isset($options[$key])) {

				$value = $options[$key];

			} else {

				// Set value
				if($default_set) { self::option_set($key, $default); }
			}

			// If value is blank check to see if we should return the default value
			if(($value === '') && $use_default_if_blank) {

				$value = $default;
			}

			return apply_filters('wsf_option_get', $value, $key);
		}

		// Set plugin option key value
		public static function option_set($key, $value, $update = true) {

			// Get option name
			$option_name_return = self::get_options($key, false);
			$option_name = $option_name_return['name'];
			$options = $option_name_return['options'];

			// Set value
			if((isset($options[$key]) && $update) || (!isset($options[$key]))) {

				// Set key to value in options array
				$options[$key] = apply_filters('wsf_option_set', $value, $key);
			}

			// Cache options
			self::$options[$option_name] = $options;

			// Update WordPress option
			update_option($option_name, $options, 'no');
		}

		// Remove plugin option key value
		public static function option_remove($key) {

			// Get option name
			$option_name_return = self::get_options($key, false);
			$option_name = $option_name_return['name'];
			$options = $option_name_return['options'];

			// If key exists, unset it
			if(isset($options[$key])) {

				// Remove key
				unset($options[$key]);

				// Cache options
				self::$options[$option_name] = $options;

				// Update WordPress option
				update_option($option_name, $options, 'no');

				// Key found and removed
				return true;
			}

			// Did not find key
			return false;
		}

		// Force WS Form framework
		public static function option_get_framework_ws_form($value, $key) {

			switch($key) {

				case 'framework' : return 'ws-form';
				case 'css_compile' : return false;
				case 'css_inline' : return false;

				default : return $value;
			}
		}

		// Get admin URL
		public static function get_admin_url($page_slug = '', $item_id = false, $path_extra = '') {

			$page_path = 'admin.php';
			if($page_slug != '') { $page_path .= '?page=' . $page_slug; }
			if($item_id !== false) { $item_id = absint($item_id); $page_path .= '&id=' . $item_id; }
			if($path_extra) { $page_path .= '&' . $path_extra; }

			return admin_url($page_path);
		}

		// Get plugin website link
		public static function get_plugin_website_url($path = '', $medium = false) {

			return sprintf('https://wsform.com%s?utm_source=ws_form_pro%s', $path, (($medium !== false) ? '&utm_medium=' . $medium : ''));
		}

		// Get customize URL
		public static function get_customize_url($panel = 'ws_form', $preview_form_id = 0) {

			return sprintf('customize.php?return=%s&wsf_panel_open=%s%s', urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))), $panel, (($preview_form_id > 0) ? sprintf('&wsf_preview_form_id=%u', $preview_form_id) : ''));
		}

		// Get tooltip attributes
		public static function tooltip($title, $position = 'bottom-center') {

			$helper_icon_tooltip = self::option_get('helper_icon_tooltip', true);

			if($helper_icon_tooltip) {

				return sprintf(' data-wsf-tooltip="%s" title="%s"', esc_attr($position), esc_attr($title));

			} else {

				return sprintf(' title="%s"', esc_attr($title));
			}
		}

		// Echo tooltip attribut
		public static function tooltip_e($title, $position = 'bottom-center') {

			// Output is escaped in tooltip method
			echo self::tooltip($title, $position = 'bottom-center');	// phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		// Output settings attributes
		public static function attributes_e($attributes) {

			if(!is_array($attributes)) { return; }

			foreach($attributes as $key => $value) {

				echo ' ';
				esc_attr_e($key);
				echo '="';
				esc_attr_e($value);
				echo '"';
			}
		}

		// Sanitize IP addresses
		public static function sanitize_ip_address($ip) {

			return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
		}

		// Get query var (NONCE is not available)
		public static function get_query_var($var, $default = '', $parameters = false, $esc_sql = false, $strip_slashes = true, $request_method = false) {

			// REST parameters
			if($parameters !== false) {

				if(isset($parameters[$var])) {

					$return_value = $esc_sql ? esc_sql($parameters[$var]) : $parameters[$var];
					$return_value = self::mod_security_fix($return_value);
					return $strip_slashes ? stripslashes_deep($return_value) : $return_value;
				}
			}

			// Get request method
			$request_method = ($request_method === false) ? self::get_request_method() : $request_method;
			if(!$request_method) { return $default; }

			// Regular GET, POST, PUT handling
			switch($request_method) {

				case 'GET' :

					$post_vars = $_GET;	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					break;

				case 'POST' :

					$post_vars = $_POST;	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					break;

				case 'PUT' :

					// PUT method data is in php://input so parse that into $post_vars
					parse_str(file_get_contents('php://input'), $post_vars);
					$strip_slashes = false;

					break;

				default :

					return $default;
			}

			// DATA param (This overcomes standard 1000 POST parameter limitation in PHP)
			if(
				isset($post_vars['data'])
			) {

				$data = $strip_slashes ? stripslashes_deep($post_vars['data']) : $post_vars['data'];
				$data = self::mod_security_fix($data);

				$data_array = is_string($data) ? json_decode($data, true) : array();

				if(isset($data_array[$var])) { return $data_array[$var]; }
			}

			// Get return value
			$return_value = isset($post_vars[$var]) ? ($esc_sql ? esc_sql($post_vars[$var]) : $post_vars[$var]) : $default;
			$return_value = self::mod_security_fix($return_value);
			return $strip_slashes ? stripslashes_deep($return_value) : $return_value;
		}

		// Get request var 
		public static function get_query_var_nonce($var, $default = '', $parameters = false, $esc_sql = false, $strip_slashes = true, $request_method_required = false, $request_method = false) {

			// REST parameters
			if($parameters !== false) {

				if(isset($parameters[$var])) {

					$return_value = $esc_sql ? esc_sql($parameters[$var]) : $parameters[$var];
					$return_value = self::mod_security_fix($return_value);
					return $strip_slashes ? stripslashes_deep($return_value) : $return_value;
				}
			}

			// Get from standard _GET _POST arrays
			$request_method = ($request_method === false) ? self::get_request_method() : $request_method;
			if(!$request_method) { return $default; }
			if(
				($request_method_required !== false) &&
				($request_method_required !== $request_method)
			) {

				return $default;
			}

			// NONCE enabled
			$nonce_enabled = is_admin() || is_user_logged_in() || WS_Form_Common::option_get('security_nonce');

			// Check wp_verify_nonce exists
			if($nonce_enabled && !function_exists('wp_verify_nonce')) { self::error_nonce(); }

			// Regular GET, POST, PUT handling
			switch($request_method) {

				case 'GET' :

					// If value is not set, return the default value
					if(!isset($_GET)) { return $default; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					if(!isset($_GET[$var]) && !isset($_GET['data'])) { return $default; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					// NONCE
					if(
						$nonce_enabled &&
						!self::$nonce_verified &&
						(

							!isset($_GET[WS_FORM_POST_NONCE_FIELD_NAME]) ||	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							!wp_verify_nonce(wp_unslash($_GET[WS_FORM_POST_NONCE_FIELD_NAME]), WS_FORM_POST_NONCE_ACTION_NAME)	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						)
					) {

						self::error_nonce();

					} else {

						self::$nonce_verified = true;
					}

					$post_vars = $_GET;	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					break;

				case 'POST' :

					// If value is not set, return the default value
					if(!isset($_POST)) { return $default; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					if(!isset($_POST[$var]) && !isset($_POST['data'])) { return $default; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					// NONCE
					if(
						$nonce_enabled &&
						!self::$nonce_verified &&
						(
							!isset($_POST[WS_FORM_POST_NONCE_FIELD_NAME]) ||	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							!wp_verify_nonce(wp_unslash($_POST[WS_FORM_POST_NONCE_FIELD_NAME]), WS_FORM_POST_NONCE_ACTION_NAME)
						)
					) {
						self::error_nonce();

					} else {

						self::$nonce_verified = true;
					}

					$post_vars = $_POST;	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					break;

				case 'PUT' :

					// PUT method data is in php://input so parse that into $post_vars
					parse_str(file_get_contents('php://input'), $post_vars);

					// NONCE
					if(
						$nonce_enabled &&
						!self::$nonce_verified &&
						(

							!isset($post_vars[WS_FORM_POST_NONCE_FIELD_NAME]) ||
							!wp_verify_nonce(wp_unslash($post_vars[WS_FORM_POST_NONCE_FIELD_NAME]), WS_FORM_POST_NONCE_ACTION_NAME)
						)
					) {

						self::error_nonce();

					} else {

						self::$nonce_verified = true;
					}

					$strip_slashes = false;

					break;

				default :

					return $default;
			}

			// DATA param (This overcomes standard 1000 POST parameter limitation in PHP)
			if(
				isset($post_vars['data'])
			) {

				$data = $strip_slashes ? stripslashes_deep($post_vars['data']) : $post_vars['data'];
				$data = self::mod_security_fix($data);

				$data_array = is_string($data) ? json_decode($data) : array();

				if(isset($data_array->{$var})) { return $data_array->{$var}; }
			}

			// Get return value
			$return_value = isset($post_vars[$var]) ? ($esc_sql ? esc_sql($post_vars[$var]) : $post_vars[$var]) : $default;
			$return_value = self::mod_security_fix($return_value);
			return $strip_slashes ? stripslashes_deep($return_value) : $return_value;
		}

		// nonce error
		public static function error_nonce() {

			// Simulate WordPress error
			$code = 'rest_forbidden';
			$message = __('Sorry, you are not allowed to do that (WSF).', 'ws-form');
			$status = rest_authorization_required_code();

			// Build response
			$response = array(

				'code' => $code,
				'message' => $message,
				'data' => array(

					'status' => $status
				)
			);

			// Send response
			http_response_code($status);
			header('Content-Type: application/json');
			echo wp_json_encode($response);

			exit;
		}

		// mod_security fix
		public static function mod_security_fix($fix_this) {

			if(is_string($fix_this)) {

				$fix_this = str_replace('~WSF%23~', '#', $fix_this);
				$fix_this = str_replace('~WSF%60~', '<', $fix_this);
				$fix_this = str_replace('~WSF%62~', '>', $fix_this);
				$fix_this = str_replace('~WSFTCELES~', 'SELECT', $fix_this);
				$fix_this = str_replace('~WSFtceles~', 'select', $fix_this);
				$fix_this = str_replace('~WSFtceleS~', 'Select', $fix_this);
				$fix_this = str_replace('~WSFelyts~', 'style', $fix_this);
				$fix_this = str_replace('~WSFELYTS~', 'STYLE', $fix_this);
				$fix_this = str_replace('~WSFcrs~', 'src', $fix_this);
				$fix_this = str_replace('~WSFCRS~', 'SRC', $fix_this);
				$fix_this = str_replace('~WSFsnlmx~', 'xmlns', $fix_this);
				$fix_this = str_replace('~WSFSNLMX~', 'XMLNS', $fix_this);
				$fix_this = str_replace('~WSFid_tcejbo~', 'object_id', $fix_this);

				return $fix_this;
			}

			if(is_array($fix_this)) {

				foreach($fix_this as $key => $fix_this_single) {

					$fix_this[$key] = self::mod_security_fix($fix_this_single);
				}

				return $fix_this;
			}

			return $fix_this;
		}
		// Get IP lookup
		// Data requested from geoplugin.net that uses GeoLite data created by MaxMind, available from http://www.maxmind.com
		public static function get_ip_lookup($json_var_array) {

			if(!is_array($json_var_array)) { $json_var_array = array($json_var_array); }

			if(self::$ip_lookup_response === false) {

				// Get remote IP address (Sanitized with sanitize_ip_address below)
				$remote_addr = self::get_http_env_raw(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'));
				if($remote_addr == '') { return ''; }

				// Split IP (IP can be comma separated if proxy in use)
				$ip_array = explode(',', $remote_addr);
				$ip_lookup = false;

				foreach($ip_array as $ip) {

					// Trim
					$ip = trim($ip);

					// Validate IP
					if(self::sanitize_ip_address($ip) === false) { continue; }

					// Use valid IP found
					$ip_lookup = $ip;
					break;
				}

				if($ip_lookup !== false) {

					// Do IP Lookup
					$wp_remote_get_response = wp_remote_get(sprintf('http://www.geoplugin.net/php.gp?ip=%s', $ip_lookup));

					if(!is_wp_error($wp_remote_get_response)) {

						$ip_lookup_response = wp_remote_retrieve_body($wp_remote_get_response);

						if(is_serialized($ip_lookup_response)) {

							self::$ip_lookup_response = unserialize($ip_lookup_response);
						}
					}
				}
			}

			if(self::$ip_lookup_response !== false) {

				$return_array = array();

				foreach($json_var_array as $json_var) {

					if(isset(self::$ip_lookup_response[$json_var])) {

						$return_array[] = self::$ip_lookup_response[$json_var];
					}
				}

				return implode(',', $return_array);
			}

			return '';
		}

		// Get MySQL date
		public static function get_mysql_date($date = false) {

			$time = ($date === false) ? time() : strtotime($date);
			if($time === false) { return false; }
			$date = gmdate('Y-m-d H:i:s', $time);
			return $date;
		}

		// Get request method
		public static function get_request_method($valid_request_methods = false) {

			// Check for valid request methods
			if(!$valid_request_methods) { $valid_request_methods = ['GET', 'POST', 'PUT', 'DELETE']; }

			// Check to ensure we can determine request method
			if(!isset($_SERVER) || !isset($_SERVER["REQUEST_METHOD"])) { return false; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Read request method
			$request_method = strtoupper(wp_unslash($_SERVER["REQUEST_METHOD"]));

			// Ensure it is valid
			if(!in_array($request_method, $valid_request_methods)) { return false; }

			return ($request_method != '') ? $request_method : false;
		}

		// Get current user ID
		public static function get_user_id($exit_on_zero = true) {

			$user_id = get_current_user_id();
			if(($user_id == 0) && $exit_on_zero) { exit; }
			return($user_id);
		}

		// Get current URL
		public static function get_current_url() {

			return home_url(sanitize_url($_SERVER['REQUEST_URI']));
		}

		// Add query string to URL
		public static function add_query_string($url, $query_string) {

			$url_parsed = parse_url($url);
			if(!isset($url_parsed['path'])) { $url .= '/'; }
			$separator = isset($url_parsed['query']) ? '&' : '?';
			$url .= $separator . $query_string;

			return $url;
		}

		// Echo comment - CSS
		public static function comment_css($comment) {

			// Should CSS be commented?
			$comments_css = self::option_get('comments_css', true);

			if($comments_css) {

				// Echo comment
				$return_css = sprintf("/* %s */\n", $comment);

			} else {

				$return_css = '';
			}

			return $return_css;
		}

		// SVG Render
		public static function render_icon_16_svg($id) {

			$return_html = WS_Form_Config::get_icon_16_svg($id);

			if($return_html !== false) {

				// Static SVG outut, no user input
				echo $return_html;	// phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			}

			return $return_html;
		}

		// Check form status
		public static function check_form_status($status, $throw_error = true) {

			// Check status is valid
			$valid_statuses = explode(',', WS_FORM_STATUS_FORM);
			if(!in_array($status, $valid_statuses)) {

				if($throw_error) {

					self::throw_error(__('Attempt to set invalid status on form', 'ws-form'));

				} else {

					return false;
				}
			}

			return true;
		}

		// Check submit status
		public static function check_submit_status($status, $throw_error = true) {

			// Check status is valid
			$valid_statuses = explode(',', WS_FORM_STATUS_SUBMIT);
			if(!in_array($status, $valid_statuses)) {

				if($throw_error) {

					self::throw_error(__('Attempt to set invalid status on submit', 'ws-form'));

				} else {

					return false;
				}
			}

			return true;
		}

		// Get API base path
		public static function get_api_path($path = '', $query_string = false) {

			// Check permalinks
			$permalink_custom = (get_option('permalink_structure') != '');

			if($permalink_custom) {

				$api_path = rest_url() . WS_FORM_RESTFUL_NAMESPACE . '/' . $path;
				if($query_string !== false) { $api_path .= '?' . $query_string; }

			} else {

				$path = '/' . WS_FORM_RESTFUL_NAMESPACE . '/' . $path;
				$api_path = get_site_url() . '/?rest_route=' . rawurlencode($path);
				if($query_string !== false) { $api_path .= '&' . $query_string; }
			}

			return $api_path;
		}

		// Is debug enabled?
		public static function debug_enabled() {

			if(
				self::is_block_editor() ||
				self::is_customize_preview()

			) { return false; }

			$debug_enabled = false;

			switch(self::option_get('helper_debug', 'off')) {

				case 'administrator' : 	

					if(function_exists('wp_get_current_user')) {

						// Works better for multisite than checking roles. Roles are not available in WP_User on multisite
						$debug_enabled = current_user_can('activate_plugins');

					} else {

						$debug_enabled = false;
					}

					break;
	
				case 'on' :

					$debug_enabled = true;

					break;
			}

			$debug_enabled = apply_filters('wsf_debug_enabled', $debug_enabled);

			return $debug_enabled;
		}

		// Check to see if object should show
		public static function object_show($object, $object_prefix, $current_user, $user_roles) {

			// Object user status
			$object_user_status = WS_Form_Common::get_object_meta_value($object, $object_prefix . '_user_status', '');
			$object_user_roles = WS_Form_Common::get_object_meta_value($object, $object_prefix . '_user_roles', false);
			$object_user_capabilities = WS_Form_Common::get_object_meta_value($object, $object_prefix . '_user_capabilities', false);

			if($object_user_status) {

				$object_show = false;

				switch($object_user_status) {

					// Must be logged in
					case 'on' :

						if($current_user->ID > 0) { $object_show = true; }
						break;

					// Must be logged out
					case 'out' :

						if($current_user->ID == 0) { $object_show = true; }
						break;

					// Must have user role or capability
					case 'role_capability' :

						if(is_array($object_user_roles) && (count($object_user_roles) > 0)) {

							$user_role_ok = false;

							if(is_user_logged_in()) {

								foreach($object_user_roles as $object_user_role) {

									if(in_array($object_user_role, $user_roles)) {

										$user_role_ok = true;
									}
								}
							}

						} else {

							$user_role_ok = true;
						}

						if(is_array($object_user_capabilities) && (count($object_user_capabilities) > 0)) {

							$user_capability_ok = false;

							if(is_user_logged_in()) {

								foreach($object_user_capabilities as $object_user_capability) {

									if(WS_Form_Common::can_user($object_user_capability)) {

										$user_capability_ok = true;
									}
								}
							}

						} else {

							$user_capability_ok = true;
						}

						if($user_role_ok && $user_capability_ok) { $object_show = true; }

						break;
				}

			} else {

				return true;
			}

			return $object_show;
		}

		// Get all fields from form
		public static function get_fields_from_form($form_object, $no_cache = false, $filter_group_ids = false, $filter_section_ids = false) {

			// Retrieve from cache
			if(!$no_cache && (self::$fields !== false)) { return self::$fields; }

			// Get fields
			$fields = self::get_fields_from_form_group($form_object->groups, $filter_group_ids, $filter_section_ids);

			// Add to cache
			self::$fields = $fields;

			return $fields;
		}

		// Get all fields from form
		public static function get_field_types($no_cache = false) {

			// Retrieve from cache
			if(!$no_cache && (self::$field_types !== false)) { return self::$field_types; }

			// Get field types
			$field_types = WS_Form_Config::get_field_types_flat();

			// Add to cache
			self::$field_types = $field_types;

			return $field_types;
		}

		// Run through each group
		public static function get_fields_from_form_group($groups, $filter_group_ids, $filter_section_ids) {

			$fields = array();

			foreach($groups as $key => $group) {

				// Get group ID
				$group_id = $group->id;

				// Filter group IDs
				if(
					($filter_group_ids !== false) &&
					!in_array($group_id, $filter_group_ids)
				) {
					continue;
				}

				if(isset($groups[$key]->sections)) {

					$section_fields = self::get_fields_from_form_section($key, $group->sections, $filter_section_ids);

					$fields = $fields + $section_fields;
				}
			}

			return $fields;
		}

		// Run through each section
		public static function get_fields_from_form_section($group_key, $sections, $filter_section_ids) {

			$fields = array();

			foreach($sections as $section_key => $section) {

				// Get section ID
				$section_id = $section->id;

				// Filter section IDs
				if(
					($filter_section_ids !== false) &&
					!in_array($section_id, $filter_section_ids)
				) {
					continue;
				}

				// Check if repeatable
				$section_repeatable = isset($section->meta) && isset($section->meta->section_repeatable) && !empty($section->meta->section_repeatable);

				if(isset($sections[$section_key]->fields)) {

					$section_fields = array();

					foreach($section->fields as $field_key => $field) {

						$field->group_key = $group_key;
						$field->section_key = $section_key;
						$field->field_key = $field_key;

						$field->section_id = $section_id;
						$field->section_repeatable = $section_repeatable;

						$section_fields[$field->id] = $field;
					}

					$fields = $fields + $section_fields;
				}
			}

			return $fields;
		}

		// Get all sections from form
		public static function get_sections_from_form($form_object, $no_cache = false, $get_fields = true, $get_meta = true) {

			// Retrieve from cache
			if(!$no_cache && (self::$sections !== false)) { return self::$sections; }

			// Get sections
			$sections = self::get_sections_from_form_group($form_object->groups, $get_fields, $get_meta);

			// Add to cache
			self::$sections = $sections;

			return $sections;
		}

		// Run through each group
		public static function get_sections_from_form_group($groups, $get_fields = true, $get_meta = true) {

			$sections_return = array();

			foreach($groups as $key => $group) {

				if(isset($groups[$key]->sections)) {

					$section_fields = self::get_sections_from_form_section($group->sections, $get_fields, $get_meta);

					$sections_return = $sections_return + $section_fields;
				}
			}

			return $sections_return;
		}

		// Run through each section
		public static function get_sections_from_form_section($sections, $get_fields = true, $get_meta = true) {

			$sections_return = array();

			foreach($sections as $key => $section) {

				// Get section ID
				$section_id = $section->id;

				$sections_return[$section_id] = new stdClass();
				$sections_return[$section_id]->label = $section->label;
				$sections_return[$section_id]->fields = array();
				$sections_return[$section_id]->meta = new stdClass();

				// Check if repeatable
				$sections_return[$section_id]->repeatable = isset($section->meta) && isset($section->meta->section_repeatable) && !empty($section->meta->section_repeatable);

				if(isset($section->fields) && $get_fields) {

					$section_fields = array();

					foreach($section->fields as $field) {

						$section_fields[$field->id] = $field;
					}

					$sections_return[$section_id]->fields = $section_fields;
				}

				if(isset($section->meta) && $get_meta) {

					$sections_return[$section_id]->meta = $section->meta;
				}
			}

			return $sections_return;
		}

		// Get all groups from form
		public static function get_groups_from_form($form_object, $no_cache = false) {

			// Retrieve from cache
			if(!$no_cache && (self::$groups !== false)) { return self::$groups; }

			// Get groups
			$groups = array();

			foreach($form_object->groups as $key => $group) {

				$groups[$group->id] = new stdClass();
				$groups[$group->id]->label = $group->label;
			}

			// Add to cache
			self::$groups = $groups;

			return $groups;
		}

		// Mask parse
		public static function mask_parse($mask, $values, $prefix = '#', $single_parse = false) {

			if($mask == '') { return ''; }

			// Final sort
			uksort($values, function($variable_a, $variable_b) {

				if($variable_a === $variable_b) { return 0; }

				$variable_a_is_function = (strpos($variable_a, '(') !== false);
				$variable_b_is_function = (strpos($variable_b, '(') !== false);

				if($variable_a_is_function && $variable_b_is_function) {

					return $variable_a < $variable_b ? 1 : -1;
				}

				if(
					(!$variable_a_is_function && $variable_b_is_function) ||
					($variable_a_is_function && !$variable_b_is_function)
				) {

					return $variable_a_is_function < $variable_b_is_function ? 1 : -1;
				}

				return $variable_a < $variable_b ? 1 : -1;
			});

			foreach($values as $key => $value) {

				if($single_parse) {

					// Single parse
					$replace = '/' . preg_quote($prefix . $key, '/') . '/';
					$mask = preg_replace($replace, $value, $mask, 1);

				} else {

					// Multi parse (Default)
					$mask = str_replace($prefix . $key, $value, $mask);
				}
			}

			return $mask;
		}

		// Create shortcode
		public static function shortcode($form_id = false) {

			$form_id = absint($form_id);
			if($form_id === 0) { return ''; }

			return sprintf('[%s id="%u"]', WS_FORM_SHORTCODE, esc_attr($form_id));
		}

		// Check file upload capabilities
		public static function uploads_check() {

			// Create file warnings
			$files_warning = [];

			// Read ini settings
			if(!ini_get('upload_max_filesize')) { return(['max_upload_size' => 0, 'max_uploads' => 0, 'errors' => [__('Unable to read PHP upload_max_filesize setting', 'ws-form')]]); }
			$upload_max_filesize = self::ini_shorthand_notation_to_bytes(ini_get('upload_max_filesize'));

			if(!ini_get('post_max_size')) { return(['max_upload_size' => 0, 'max_uploads' => 0, 'errors' => [__('Unable to read PHP post_max_size setting', 'ws-form')]]); }
			$post_max_size = self::ini_shorthand_notation_to_bytes(ini_get('post_max_size'));

			if(!ini_get('memory_limit')) { return(['max_upload_size' => 0, 'max_uploads' => 0, 'errors' => [__('Unable to read PHP memory_limit setting', 'ws-form')]]); }
			$memory_limit = self::ini_shorthand_notation_to_bytes(ini_get('memory_limit'));

			// This limit was added in PHP 5.3.1
			if(ini_get('max_file_uploads')) {
				$max_file_uploads = absint(ini_get('max_file_uploads'));
			} else {
				$max_file_uploads = 20;
			}

			// Calculate recommended maximum upload size
			$max_upload_size = $upload_max_filesize;
			if($max_upload_size > $post_max_size) { $max_upload_size = $post_max_size; }
			if($max_upload_size > $memory_limit) { $max_upload_size = $memory_limit; }

			// Check ini settings
			if($post_max_size < $upload_max_filesize) { $files_warning[] = sprintf(__('Your PHP post_max_size setting (%s) is less than your max_upload_size setting (%s).', 'ws-form'), ini_get('memory_limit'), ini_get('post_max_size')); }
			if($memory_limit < $upload_max_filesize) { $files_warning[] = sprintf(__('Your PHP memory_limit setting (%s) is less than your max_upload_size setting (%s).', 'ws-form'), ini_get('memory_limit'), ini_get('post_max_size')); }
			if($memory_limit < $post_max_size) { $files_warning[] = sprintf(__('Your PHP memory_limit setting (%s) is less than your post_max_size setting (%s).', 'ws-form'), ini_get('memory_limit'), ini_get('post_max_size')); }

			// Check file permissions
			$upload_dir_create = self::upload_dir_create();
			if($upload_dir_create['error']) { $files_warning[] = $upload_dir_create['message']; }

			// Return result
			return ['max_upload_size' => $max_upload_size, 'max_uploads' => $max_file_uploads, 'errors' => $files_warning];
		}

		// Make sure upload folder can be created. Create it if it doesn't exist.
		public static function upload_dir_create($dir = '') {

			// Get base upload_dir
			$upload_dir = wp_upload_dir()['basedir'];

			// Check upload directory can be written to
			if(!is_writeable($upload_dir)) { return ['error' => true, 'message' => __('Your WordPress uploads directory cannot be written to.', 'ws-form')]; }

			// Get WS Form upload dir
			$upload_dir_path = WS_FORM_UPLOAD_DIR . (($dir != '') ? '/' . $dir : '');
			$upload_dir_ws_form = $upload_dir . '/' . $upload_dir_path;

			// Check to see if WS Forms upload folder exists
			if(!file_exists($upload_dir_ws_form)) {

				if(!wp_mkdir_p($upload_dir_ws_form)) { return ['error' => true, 'message' => sprintf(__('Unable to create upload folder for uploaded files (wp-content/uploads/%s).', 'ws-form'), $upload_dir_path)]; }
			}

			return ['error' => false, 'dir' => $upload_dir_ws_form, 'path' => $upload_dir_path];
		}

		// Check path is valid
		public static function check_path($path_base) {

			$illegal_paths = array('..', '~');

			foreach($illegal_paths as $illegal_path) {

				if(strpos($path_base, $illegal_path) !== false) { die(__('Invalid Path', 'ws-form')); }
			}

			$path = realpath($path_base);
			if(strpos($path, $path_base) !== 0 || strpos($path, $path_base) === false) { die(__('Invalid Path', 'ws-form')); } 

			return $path_base;			
		}

		// Convert PHP ini shorthand notation to bytes
		public static function ini_shorthand_notation_to_bytes($ini_value) {

			$ini_value  = trim($ini_value);
			$last = strtolower($ini_value[strlen($ini_value)-1]);
			$ini_value  = substr($ini_value, 0, -1); // necessary since PHP 7.1; otherwise optional
			$ini_value = absint($ini_value);

			switch($last) {

				case 'g':
					$ini_value *= 1024;

				case 'm':
					$ini_value *= 1024;

				case 'k':
					$ini_value *= 1024;
			}

			return $ini_value;
		}

		// Get HTTP environment variable (Accepts array for multiple HTTP environment variable checks)
		// Return from this function must be sanitized
		public static function get_http_env_raw($variable_array) {

			// Checks
			if(!isset($_SERVER)) { return ''; }	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if(!is_array($variable_array)) { $variable_array = array($variable_array); }
			$variable_array_index_last = count($variable_array) - 1;

			// Run through each variable
			foreach($variable_array as $variable_array_index => $variable) {

				if(isset($_SERVER[$variable])) {	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if($variable_array_index == $variable_array_index_last) {

						return wp_unslash($_SERVER[$variable]);

					} else {

						if(!empty($_SERVER[$variable])) return wp_unslash($_SERVER[$variable]);
					}
				}
			}

			return '';
		}

		// Get object variable
		public static function get_object_var($object, $var, $default_value = '') {

			// Check for meta data
			if(!isset($object->{$var})) { return $default_value; }

			return $object->{$var};
		}

		// Get object meta value
		public static function get_object_meta_value($object, $meta_key, $default_value = '') {

			// Check for meta data
			if(!isset($object->meta)) { return $default_value; }

			// Check for meta_key
			if(!isset($object->meta->{$meta_key})) { return $default_value; }

			return $object->meta->{$meta_key};
		}

		// Get array meta value
		public static function get_array_meta_value($array, $meta_key, $default_value = '') {

			// Check for meta data
			if(!isset($array['meta'])) { return $default_value; }

			// Check for meta_key
			if(!isset($array['meta']->{$meta_key})) { return $default_value; }

			return $array['meta']->{$meta_key};
		}

		// Extract numbers from string
		public static function get_tel($phone) {

			return preg_replace('/[^+\d]+/', '', $phone);
		}

		// Extract float with fraction from string
		public static function get_number($number_input, $default_value = 0, $process_currency = false, $decimals = false) {

			// Convert numbers to text
			if(is_numeric($number_input)) { $number_input = strval($number_input); }

			// Check input is a string
			if(!is_string($number_input)) { return 0; }

			// Trim input
			$number_input = trim($number_input);

			// Convert from current currency
			if($process_currency) {

				$currency = self::get_currency();

				// Filter characters required for parseFloat
				$decimal_separator = $currency['decimal_separator'];
				$thousand_separator = $currency['thousand_separator'];

				// Ensure the decimal separator setting is included in the regex (Add ,. too in case default value includes alternatives)
				$number_input = preg_replace('/[^0-9-' . $decimal_separator . ']/', '', $number_input);

				if($decimal_separator === $thousand_separator) {

					// Convert decimal separators to periods so parseFloat works
					if(substr($number_input, -3, 1) === $decimal_separator) {

						$decimal_index = (strlen($number_input) - 3);
						$number_input = substr($number_input, 0, $decimal_index) . '[dec]' . substr($number_input, $decimal_index + 1);
					}

					// Remove thousand separators
					$number_input = str_replace($thousand_separator, '', $number_input);

					// Replace [dec] back to decimal separator for parseFloat
					$number_input = str_replace('[dec]', '.', $number_input);

				} else {

					// Replace [dec] back to decimal separator for parseFloat
					$number_input = str_replace($decimal_separator, '.', $number_input);
				}
			}

			// parseFloat converts decimal separator to period to ensure that function works
			$number_output = (trim($number_input) === '') ? $default_value : (!is_numeric($number_input) ? $default_value : floatVal($number_input));

			// Round
			if($decimals !== false) { $number_output = round(parseFloat($number_output), $decimals); }

			return $number_output;
		}

		// Get array of MIME types
		public static function get_mime_array($value) {

			if(is_array($value)) { return $value; }

			$mime_array = explode(',', $value);
			$mime_array_return = array();

			foreach($mime_array as $mime) {

				$mime_split = $mime_array = explode('/', $mime);
				if(count($mime_split) !== 2) { continue; }
				if(strlen($mime_split[0]) == 0) { continue; }
				if(strlen($mime_split[1]) == 0) { continue; }
				$mime_array_return[] = strtolower(trim($mime));
			}

			return $mime_array_return;
		}

		// Add datestamp to filename
		public static function filename_datestamp($filename_prefix, $filename_suffix) {

			$filename = $filename_prefix . (WS_Form_Common::wp_version_at_least('5.3') ? current_datetime()->format('-Y-m-d-H-i-s') : current_time('-Y-m-d-H-i-s')) . '.' . $filename_suffix;
			return sanitize_file_name($filename);
		}

		// Output file download headers
		public static function file_download_headers($filename, $mime_type, $encoding = 'binary') {

			$filename = sanitize_file_name($filename);			// WordPress function

			// HTTP headers
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: ' . $mime_type);
			header('Content-Disposition: attachment; filename=' . $filename);
			header('Content-Transfer-Encoding: ' . $encoding);
		}

		public static function hex_to_hsl($hex) {

			$hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
			$rgb = array_map(function($part) {

				return hexdec($part) / 255;

			}, $hex);

			$max = max($rgb);
			$min = min($rgb);

			$l = ($max + $min) / 2;

			if ($max == $min) {

				$h = $s = 0;

			} else {

				$diff = $max - $min;
				$s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

				switch($max) {

					case $rgb[0]:
						$h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
						break;

					case $rgb[1]:
						$h = ($rgb[2] - $rgb[0]) / $diff + 2;
						break;

					case $rgb[2]:
						$h = ($rgb[0] - $rgb[1]) / $diff + 4;
						break;
				}

				$h /= 6;
			}

			return array($h, $s, $l);
		}

		public static function hex_to_rgba($color, $opacity = false) {
		 
			$default = 'rgb(0,0,0)';
		 
			if(empty($color))
		  	return $default; 

			if ($color[0] == '#' ) {
				$color = substr( $color, 1 );
			}
	 
			if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}
	 
			$rgb =  array_map('hexdec', $hex);
	 
			if ($opacity) {
				if (abs($opacity) > 1)
					$opacity = 1.0;
				$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
			} else {
				$output = 'rgb('.implode(",",$rgb).')';
			}
	 
			return $output;
		}

		public static function hsl_to_rgb($hsl) {

			list($h, $s, $l) = $hsl;

			$r; 
			$g; 
			$b;

			$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
			$x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
			$m = $l - ( $c / 2 );
			if ( $h < 60 ) {
				$r = $c;
				$g = $x;
				$b = 0;
			} else if ( $h < 120 ) {
				$r = $x;
				$g = $c;
				$b = 0;			
			} else if ( $h < 180 ) {
				$r = 0;
				$g = $c;
				$b = $x;					
			} else if ( $h < 240 ) {
				$r = 0;
				$g = $x;
				$b = $c;
			} else if ( $h < 300 ) {
				$r = $x;
				$g = 0;
				$b = $c;
			} else {
				$r = $c;
				$g = 0;
				$b = $x;
			}
			$r = ( $r + $m ) * 255;
			$g = ( $g + $m ) * 255;
			$b = ( $b + $m  ) * 255;

			return array( floor( $r ), floor( $g ), floor( $b ) );
		}

		public static function hsl_to_hex($hsl) {

			list($h, $s, $l) = $hsl;

			if ($s == 0) {

				$r = $g = $b = 1;

			} else {

				$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
				$p = 2 * $l - $q;

				$r = self::hue_to_rgb($p, $q, $h + 1/3);
				$g = self::hue_to_rgb($p, $q, $h);
				$b = self::hue_to_rgb($p, $q, $h - 1/3);
			}

			return self::rgb_to_hex($r) . self::rgb_to_hex($g) . self::rgb_to_hex($b);
		}

		public static function hue_to_rgb($p, $q, $t) {

			if ($t < 0) $t += 1;
			if ($t > 1) $t -= 1;
			if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
			if ($t < 1/2) return $q;
			if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

			return $p;
		}

		public static function rgb_to_hex($rgb) {

			return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
		}

		public static function hex_hsl_adjust($hex, $hPercent, $sPercent, $lPercent) {

			if($hex == '') { return ''; }

			// Check for hash
			$has_hash = (substr($hex, 0, 1) == '#');
			if($has_hash) { $hex = substr($hex, 1); }

			// Convert to HSL
			$hsl = self::hex_to_hsl($hex);

			$h = $hsl[0];
			$s = $hsl[1];
			$l = $hsl[2];

			// Adjust
			$h = $h + (($hPercent / 100) * $h);
			$s = $s + (($sPercent / 100) * $s);
			$l = $l + (($lPercent / 100) * $l);

			// Convert back to hex
			return ($has_hash ? '#' : '') . self::hsl_to_hex(array($h, $s, $l));
		}

		// Green (0) --> Lime Green (25) --> Yellow (50) --> Orange (75) --> Red (100)
		public static function get_green_to_red_rgb($value, $min = 0, $max = 100) {

			// Calculate ratio
			$ratio = $value / $max;
			if($ratio < 0) { $ratio = 0; }
			if($ratio > 1) { $ratio = 1; }

			// Red
			$r = ($ratio * 2) * 255;
			$r = ($r > 255) ? 255 : $r;

			// Green
			$g = (2 - ($ratio * 2)) * 255;
			$g = ($g > 255) ? 255 : $g;

			// Blue
			$b = 0;

			return "rgb($r,$g,$b)";
		}

		public static function hex_lighten_percentage($hex, $percentage) {

			$rgbhex = str_split(trim($hex, '# '), 2);
			$rgbdec = array_map('hexdec', $rgbhex);

			if(count($rgbdec) !== 3) { return $hex; }

			$hsv = self::rgb_to_hsv($rgbdec[0], $rgbdec[1], $rgbdec[2]);

			$hsv_s = $hsv['S'];
			$hsv['S'] = $hsv_s - ($hsv_s * ($percentage / 100));

			$hsv_v = $hsv['V'];
			$hsv_diff = (1 - $hsv_v);
			$hsv['V'] = $hsv_v + ($hsv_diff * ($percentage / 100));

			$rgblight = self::hsv_to_rgb($hsv['H'], $hsv['S'], $hsv['V']);
			$output = array_map('dechex', $rgblight);
			$output = array_map('self::zero_pad', $output); // gotta zero-pad single-digit hex

			return '#'.implode($output);
		}

		public static function hex_darken_percentage($hex, $percentage) {

			$rgbhex = str_split(trim($hex, '# '), 2);
			$rgbdec = array_map('hexdec', $rgbhex);

			if(count($rgbdec) !== 3) { return $hex; }

			$hsv = self::rgb_to_hsv($rgbdec[0], $rgbdec[1], $rgbdec[2]);

			$hsv_v = $hsv['V'];
			$hsv['V'] = ($hsv_v * ((100 - $percentage) / 100));

			$rgblight = self::hsv_to_rgb($hsv['H'], $hsv['S'], $hsv['V']);
			$output = array_map('dechex', $rgblight);
			$output = array_map('self::zero_pad', $output); // gotta zero-pad single-digit hex

			return '#'.implode($output);
		}

		public static function zero_pad($num) {
			$limit = 2;
			return (strlen($num) >= $limit) ? $num : self::zero_pad('0' . $num);
		}

		public static function rgb_to_hsv($R, $G, $B) {  // RGB Values:Number 0-255 

			$HSL = array(); 

			$var_R = ($R / 255); 
			$var_G = ($G / 255); 
			$var_B = ($B / 255); 

			$var_Min = min($var_R, $var_G, $var_B); 
			$var_Max = max($var_R, $var_G, $var_B); 
			$del_Max = $var_Max - $var_Min; 

			$V = $var_Max; 

			if ($del_Max == 0) { 

				$H = 0; 
				$S = 0; 

			} else { 

				$S = $del_Max / $var_Max; 

				$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
				$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
				$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 

				if	  ($var_R == $var_Max) $H = $del_B - $del_G; 
				else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B; 
				else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R; 

				if ($H<0) $H++; 
				if ($H>1) $H--; 
			}

			$HSL['H'] = $H;
			$HSL['S'] = $S;
			$HSL['V'] = $V;

			return $HSL; 
		} 

		public static function hsv_to_rgb($H, $S, $V) {  // HSV Values:Number 0-1 

			$RGB = array(); 

			if($S == 0) 
			{ 
				$R = $G = $B = $V * 255; 
			} 
			else 
			{ 
				$var_H = $H * 6; 
				$var_i = floor( $var_H ); 
				$var_1 = $V * ( 1 - $S ); 
				$var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) ); 
				$var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) ); 

				if	   ($var_i == 0) { $var_R = $V	 ; $var_G = $var_3  ; $var_B = $var_1 ; } 
				else if  ($var_i == 1) { $var_R = $var_2 ; $var_G = $V	  ; $var_B = $var_1 ; } 
				else if  ($var_i == 2) { $var_R = $var_1 ; $var_G = $V	  ; $var_B = $var_3 ; } 
				else if  ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2  ; $var_B = $V	 ; } 
				else if  ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1  ; $var_B = $V	 ; } 
				else				   { $var_R = $V	 ; $var_G = $var_1  ; $var_B = $var_2 ; } 

				$R = $var_R * 255; 
				$G = $var_G * 255; 
				$B = $var_B * 255; 
			} 

			$RGB['R'] = round($R);
			$RGB['G'] = round($G);
			$RGB['B'] = round($B);

			return $RGB; 
		} 

		// Parse WS Form variables
		public static function parse_variables_process($parse_string, $form = false, $submit = false, $content_type = 'text/html', $scope = false, $section_repeatable_index = false, $section_row_number = 1, $exclude_secure = false, $action_config = false, $depth = 1) {

			if(!is_string($parse_string)) { return $parse_string; }

			// Checks to speed up this function
			if(strpos($parse_string, '#') === false) { return $parse_string; }

			// Exclude secure on nested parses?
			$exclude_secure_nested_parse = true;

			// Get post
			$post = self::get_post_root();

			// Get user
			$user = self::get_user();

			// Initialize variables
			$variables = array();
			$variables_single_parse = array();

			// Parse type
			$lookups_contain_singles = false;

			// Check for too many iterations
			if($depth > 100) { return ''; }

			// Get parse variables config
			$parse_variables_config = WS_Form_Config::get_parse_variables();

			// Process each parse variable key
			foreach($parse_variables_config as $parse_variables_key => $parse_variables) {

				// Check for prefix (for performance)
				$ignore_prefix = (isset($parse_variables['ignore_prefix']) ? $parse_variables['ignore_prefix'] : false);
				if(!$ignore_prefix) {

					if(strpos($parse_string, '#' . $parse_variables_key) === false) { continue; }
				}

				foreach($parse_variables['variables'] as $parse_variable => $parse_variable_config) {

					// Skip variables that are not in scope
					if($scope !== false) {

						$parse_variable_scope = isset($parse_variable_config['scope']) ? $parse_variable_config['scope'] : array();

						if(is_array($parse_variable_scope)) {

							if(!in_array($scope, $parse_variable_scope)) { continue; }

						} else {

							continue;
						}
					}

					if(strpos($parse_string, '#' . $parse_variable) === false) { continue; }

					$parsed_variable = '';

					switch($parse_variable) {

						case 'email_tracking' :

							if(!isset($submit->meta)) { break; }

							// Tracking
							$tracking_array = WS_Form_Config::get_tracking(false);

							foreach($tracking_array as $meta_key => $tracking) {

								// Check meta_key is enabled
								if(($form === false) || !self::get_object_meta_value($form, $meta_key, false)) { continue; }

								$label = $tracking['label'];

								// Add to fields_all HTML
								switch($content_type) {

									case 'text/html' :

										$parsed_variable .= '<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 10px;"><strong>' . htmlentities($label) . '</strong><br />#' . $meta_key . '</p>';
										break;

									default :

										$parsed_variable .= $label . "\n#" . $meta_key . "\n\n";
										break;
								}
							}

							// Release memory
							$tracking_array = null;

							$parsed_variable = self::parse_variables_process($parsed_variable, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

							$variables[$parse_variable] = $parsed_variable;

							break;
						default :

							// Assign value
							$parse_variable_value = (isset($parse_variable_config['value'])) ? $parse_variable_config['value'] : false;
							$parse_variable_attributes = (isset($parse_variable_config['attributes'])) ? $parse_variable_config['attributes'] : false;

							// Single parse? (Used if different value returned each parse, e.g. random_number)
							$parse_variable_single_parse = isset($parse_variable_config['single_parse']) ? $parse_variable_config['single_parse'] : false;

							// If no attributes specified, then just set the value
							if(($parse_variable_attributes === false) && ($parse_variable_value !== false)) { $variables[$parse_variable] = $parse_variable_value; break; }

							// Get number of attributes required
							$variable_attribute_count = isset($parse_variable_config['attributes']) ? count($parse_variable_attributes) : 0;

							// Handle variables
							if($variable_attribute_count > 0) {

								// Do until no more found
								$variable_index_start = 0;
								do {

									$parsed_variable = '';

									// Find position of variable and brackets
									$variable_index_of = strpos($parse_string, '#' . $parse_variable, $variable_index_start);

									// No more instances of variable found
									if($variable_index_of === false) { continue; }

									// Find bracket positions
									$variable_index_of_bracket_start = false;
									$variable_index_of_bracket_finish = false;
									$parse_string_function = substr($parse_string, $variable_index_of + strlen('#' . $parse_variable));

									// Bracket should immediately follow the variable name
									if(substr($parse_string_function, 0, 1) == '(') {

										$variable_index_of_bracket_start = $variable_index_of + strlen('#' . $parse_variable);
										$variable_index_of_bracket_finish = self::closing_string_index($parse_string, ')', '(', $variable_index_of_bracket_start + 1);
									}

									// Check brackets found
									if(	($variable_index_of_bracket_start === false) ||
										($variable_index_of_bracket_finish === false) ) {

										// Shift index to look for next instance
										$variable_index_start = $variable_index_of + strlen('#' . $parse_variable);

										// Get full string to parse
										$parse_variable_full = '#' . $parse_variable;

										// No brackets found so set attributes as blank
										$variable_attribute_array = array();

									} else {

										// Shift index to look for next instance
										$variable_index_start = $variable_index_of_bracket_finish + 1;

										// Ensure bracket starts immediate after parse_variable
										if((($variable_index_of_bracket_start - $variable_index_of) - strlen('#' . $parse_variable)) !== 0) { continue; };

										// Get full string to parse
										$parse_variable_full = substr($parse_string, $variable_index_of, ($variable_index_of_bracket_finish + 1) - $variable_index_of);

										// Get attribute string
										$variable_attribute_string = substr($parse_string, $variable_index_of_bracket_start + 1, ($variable_index_of_bracket_finish - 1) - $variable_index_of_bracket_start);

										// Replace non standard double quotes
										$variable_attribute_string = str_replace('“', '"', $variable_attribute_string);
										$variable_attribute_string = str_replace('”', '"', $variable_attribute_string);

										// Get separator
										$separator = isset($parse_variable_config['attribute_separator']) ? $parse_variable_config['attribute_separator'] : ',';

										// Get attribute array
										$variable_attribute_array = str_getcsv($variable_attribute_string, $separator);

										// Trim and strip double quotes
										foreach($variable_attribute_array as $key => $e) {

											$e = preg_replace('/^"(.+(?="$))"$/', '', $e);
											$variable_attribute_array[$key] = $e;
										}
									}

									// Check each attribute
									foreach($parse_variable_attributes as $parse_variable_attributes_index => $parse_variable_attribute) {

										$parse_variable_attribute_id = $parse_variable_attribute['id'];

										// Was attribute provided for this index?
										$parse_variable_attribute_supplied = isset($variable_attribute_array[$parse_variable_attributes_index]);

										// Check required
										$parse_variable_attribute_required = (isset($parse_variable_attribute['required']) ? $parse_variable_attribute['required'] : true);
										if($parse_variable_attribute_required && !$parse_variable_attribute_supplied) {

											// Syntax error - Attribute count
											self::throw_error(sprintf(__('Syntax error, missing attribute: %s (Expected: %s)', 'ws-form'), '#' . $parse_variable, $parse_variable_attribute_id));
											continue;
										}

										// Check default
										$parse_variable_attribute_default = isset($parse_variable_attribute['default']) ? $parse_variable_attribute['default'] : false;

										if(($parse_variable_attribute_default !== false) && !$parse_variable_attribute_supplied) {

											$variable_attribute_array[$parse_variable_attributes_index] = $parse_variable_attribute_default;
										}

										// Check trim
										$parse_variable_attribute_trim = isset($parse_variable_attribute['trim']) ? $parse_variable_attribute['trim'] : true;
										if(
											$parse_variable_attribute_trim &&
											isset($variable_attribute_array[$parse_variable_attributes_index]) &&
											is_string($variable_attribute_array[$parse_variable_attributes_index])
										) {

											$variable_attribute_array[$parse_variable_attributes_index] = trim($variable_attribute_array[$parse_variable_attributes_index]);
										}

										// Check validity
										$parse_variable_attribute_valid = isset($parse_variable_attribute['valid']) ? $parse_variable_attribute['valid'] : false;
										if($parse_variable_attribute_valid !== false) {

											if(
												isset($variable_attribute_array[$parse_variable_attributes_index]) &&
												!in_array($variable_attribute_array[$parse_variable_attributes_index], $parse_variable_attribute_valid)
											) {

												// Syntax error - Attribute count
												self::throw_error(sprintf(__('Syntax error, invalid attribute: %s (Expected: %s)', 'ws-form'), '#' . $parse_variable, implode(', ', $parse_variable_attribute_valid)));
											}
										}
									}

									// Process variable
									switch($parse_variable) {

										case 'query_var' :
										case 'post_var' :

											$parsed_variable = WS_Form_Common::get_query_var($variable_attribute_array[0]);
											if($content_type == 'text/html') { $parsed_variable = htmlentities($parsed_variable); }
											break;

										case 'email_submission' :

											if(!isset($submit->meta)) { break; }

											$render_group_labels = $variable_attribute_array[0];
											$render_section_labels = $variable_attribute_array[1];
											$render_field_labels = $variable_attribute_array[2];
											$render_blank_fields = ($variable_attribute_array[3] == 'true');
											$render_static_fields = ($variable_attribute_array[4] == 'true');
											$hidden_array = explode(',', WS_Form_Common::get_query_var_nonce('wsf_hidden', ''));

											$value = self::parse_variables_fields_all((object) $form, $submit, $content_type, $render_group_labels, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config);

											$parsed_variable = self::parse_variables_process($value, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											break;
										case 'if' :

											$endif_position = self::closing_string_index($parse_string, '#endif', '#if', $variable_index_of_bracket_finish);

											if($endif_position === false) {

												self::throw_error(sprintf(__('Syntax error, missing #endif for #if(%s)', 'ws-form'), $variable_attribute_string));
											}

											// Valid operators
											$operators_valid = array('==', '!=', '>', '>=', '<=', '<', '^=', '$=', '!^=', '!$=', '*=', '!*=');

											// Valid logic previous
											$logic_previous_valid = array('&&', '||');

											// Run through each attribute and build boolean array
											$if_boolean_array = array();
											$if_boolean_array_index = 0;
											$if_part = 0;
											foreach($variable_attribute_array as $variable_attribute) {

												if($if_part === 0) {

													$if_boolean_array[$if_boolean_array_index] = array(

														'value_a' => '',
														'operator' => '!=',
														'value_b' => ''
													);
												}

												// Decode operators and logic
												if(($if_part == 1) || ($if_part == 3)) {

													// Convert HTML encoding caused by TinyMCE
													$variable_attribute = str_replace('&amp;', '&', $variable_attribute);
													$variable_attribute = str_replace('&lt;', '<', $variable_attribute);
													$variable_attribute = str_replace('&gt;', '>', $variable_attribute);
												}

												// Check for numeric values
												if(($if_part == 0) || ($if_part == 2)) {

													$variable_attribute = $variable_attribute_string = self::parse_variables_process($variable_attribute, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);
													if(is_numeric($variable_attribute)) { $variable_attribute = floatval($variable_attribute); }
												}

												// Short form #if
												if(
													($if_part == 1) &&
													in_array($variable_attribute, $logic_previous_valid)
												) {

													$if_part = 3;
												}

												// Process IF parts
												switch($if_part) {

													case 0 :
													case 2 :

														$if_part_key = ($if_part == 0) ? 'value_a' : 'value_b';
														$if_boolean_array[$if_boolean_array_index][$if_part_key] = $variable_attribute;
														$if_boolean_array[$if_boolean_array_index][$if_part_key . '_string'] = $variable_attribute_string;
														break;

													case 1 :

														if(!in_array($variable_attribute, $operators_valid)) {

															self::throw_error(sprintf(__('Syntax error, invalid operator %s in #if(%s).', 'ws-form'), $variable_attribute, $variable_attribute_string));
														}
														$if_boolean_array[$if_boolean_array_index]['operator'] = $variable_attribute;
														break;

													case 3 :

														if(!in_array($variable_attribute, $logic_previous_valid)) {

															self::throw_error(sprintf(__('Syntax error, invalid logic %s in #if(%s).', 'ws-form'), $variable_attribute, $variable_attribute_string));
														}
														$if_boolean_array[$if_boolean_array_index]['logic_next'] = $variable_attribute;
														break;
												}

												$if_part++;

												if($if_part > 3) {

													$if_part = 0;
													$if_boolean_array_index++;
												}
											}

											$result = false;

											$logic_next = '&&';

											foreach($if_boolean_array as $if_boolean_array_index => $if_boolean) {

												switch($if_boolean['operator']) {

													// Equals
													case '==' :

														$result_single = ($if_boolean['value_a'] == $if_boolean['value_b']);
														break;

													// Not equals
													case '!=' :

														$result_single = ($if_boolean['value_a'] != $if_boolean['value_b']);
														break;

													// Less than
													case '<' :

														$result_single = ($if_boolean['value_a'] < $if_boolean['value_b']);
														break;

													// Greater than
													case '>' :

														$result_single = ($if_boolean['value_a'] > $if_boolean['value_b']);
														break;

													// Less than or equal
													case '<=' :

														$result_single = ($if_boolean['value_a'] <= $if_boolean['value_b']);
														break;

													// Greater than or equal
													case '>=' :

														$result_single = ($if_boolean['value_a'] >= $if_boolean['value_b']);
														break;

													// Contains
													case '*=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) !== false) : false;
														break;

													// Does not contain
													case '!*=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) === false) : false;
														break;

													// Starts with
													case '^=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) === 0) : false;
														break;

													// Does not start with
													case '!^=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) > 0) : false;
														break;

													// Ends with
													case '$=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) === (strlen($if_boolean['value_a_string']) - strlen($if_boolean['value_b_string']))) : false;
														break;

													// Does not end with
													case '!$=' :

														$result_single = ($if_boolean['value_b_string'] !== '') ? (strpos($if_boolean['value_a_string'], $if_boolean['value_b_string']) !== (strlen($if_boolean['value_a']) - strlen($if_boolean['value_b_string']))) : false;
														break;
												}

												$result = ($if_boolean_array_index == 0) ? $result_single : (($logic_next == '&&') ? ($result && $result_single) : ($result || $result_single));

												$logic_next = isset($if_boolean['logic_next']) ? $if_boolean['logic_next'] : '&&';
											}

											$parsed_variable = false;

											if($result) {

												// Remove the endif
												$parse_string = substr($parse_string, 0, $endif_position) . substr($parse_string, $endif_position + 6);

												// Remove the if
												$parse_string = substr($parse_string, 0, $variable_index_of) . substr($parse_string, $variable_index_of_bracket_finish + 1);

											} else {

												// Remove from if to endif
												$parse_string = substr($parse_string, 0, $variable_index_of) . substr($parse_string, $endif_position + 6);
											}

											$variable_index_start = 0;

											break;

										case 'section_rows_start' :

											if($submit === false) { break; }

											if(!is_numeric($variable_attribute_array[0])) { break; }

											$section_rows_end_position = self::closing_string_index($parse_string, '#section_rows_end', '#section_rows_start', $variable_index_of_bracket_finish);

											if($section_rows_end_position === false) {

												self::throw_error(sprintf(__('Syntax error, missing #section_rows_end for #section_rows_start(%s)', 'ws-form'), $variable_attribute_string));
											}

											// Get section_id
											$section_id = absint($variable_attribute_array[0]);
											if($section_id === 0) { break; }

											// Get string to loop
											$parse_string_loop = substr($parse_string, $variable_index_of_bracket_finish + 1, $section_rows_end_position - ($variable_index_of_bracket_finish + 1));

											// Unserialize section_repeatable
											$section_repeatable = is_serialized($submit->section_repeatable) ? unserialize($submit->section_repeatable) : false;

											// Build section ID string
											$section_id_string = 'section_' . $section_id;
											$section_repeatable_array = (

												($section_repeatable !== false) &&
												isset($section_repeatable[$section_id_string]) &&
												isset($section_repeatable[$section_id_string]['index'])

											) ? $section_repeatable[$section_id_string]['index'] : [false];

											// Loop through section_repeatable_array
											$parsed_string = '';
											$section_row_number = 1;
											foreach($section_repeatable_array as $section_repeatable_array_index => $section_repeatable_index_row) {

												// Parse at same depth
												$parsed_string .= self::parse_variables_process($parse_string_loop, $form, $submit, $content_type, $scope, $section_repeatable_index_row, $section_row_number++, $exclude_secure_nested_parse, $action_config, $depth);
											}

											// Remove from section_rows_start to section_rows_end
											$parse_string = substr($parse_string, 0, $variable_index_of) . $parsed_string . substr($parse_string, $section_rows_end_position + 17);

											$variable_index_start = 0;

											break;

										case 'section_row_count' :

											if($submit === false) { break; }

											if(!is_numeric($variable_attribute_array[0])) { break; }

											// Get section_id
											$section_id = absint($variable_attribute_array[0]);
											if($section_id === 0) { break; }

											// Unserialize section_repeatable
											$section_repeatable = is_serialized($submit->section_repeatable) ? unserialize($submit->section_repeatable) : false;

											// Build section ID string
											$section_id_string = 'section_' . $section_id;

											// Get row count
											$parsed_variable = (

												($section_repeatable !== false) &&
												isset($section_repeatable[$section_id_string]) &&
												isset($section_repeatable[$section_id_string]['index']) &&
												is_array($section_repeatable[$section_id_string]['index'])

											) ? count($section_repeatable[$section_id_string]['index']) : 0;

											break;
										case 'tab_label' :

											if(!is_numeric($variable_attribute_array[0])) { break; }

											// Get group_id
											$group_id = absint($variable_attribute_array[0]);
											if($group_id === 0) { break; }

											// Get groups
											$groups = self::get_groups_from_form($form);

											if(
												isset($groups[$group_id]) &&
												isset($groups[$group_id]->label)
											) {

												$parsed_variable = self::parse_variables_process($groups[$group_id]->label, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											} else {

												self::throw_error(sprintf(__('Syntax error, invalid group ID in #group_label(%u)', 'ws-form'), $group_id));
											}

											break;

										case 'section_label' :

											if(!is_numeric($variable_attribute_array[0])) { break; }

											// Get section_id
											$section_id = absint($variable_attribute_array[0]);
											if($section_id === 0) { break; }

											// Get sections
											$sections = self::get_sections_from_form($form);

											if(
												isset($sections[$section_id]) &&
												isset($sections[$section_id]->label)
											) {

												$parsed_variable = self::parse_variables_process($sections[$section_id]->label, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											} else {

												self::throw_error(sprintf(__('Syntax error, invalid section ID in #section_label(%u)', 'ws-form'), $section_id));
											}

											break;

										case 'cookie_get' :

											// Get cookie value
											$parsed_variable = htmlentities(self::cookie_get_raw($variable_attribute_array[0]));

											break;

										case 'field_label' :

											if(!is_numeric($variable_attribute_array[0])) { break; }

											// Get field_id
											$field_id = absint($variable_attribute_array[0]);
											if($field_id === 0) { break; }

											// Get fields
											$fields = self::get_fields_from_form($form);

											if(
												isset($fields[$field_id]) &&
												isset($fields[$field_id]->label)
											) {

												$parsed_variable = self::parse_variables_process($fields[$field_id]->label, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											} else {

												self::throw_error(sprintf(__('Syntax error, invalid field ID in #field_label(%u)', 'ws-form'), $field_id));
											}

											break;

										case 'text' :

											$value = $variable_attribute_array[0];

											$parsed_variable = self::parse_variables_process($value, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											break;

										case 'field' :
										case 'field_float' :
										case 'field_date_offset' :
										case 'ecommerce_field_price' :

											if(!isset($submit->meta)) { break; }

											if(!is_numeric($variable_attribute_array[0])) { break; }

											$field_id = $variable_attribute_array[0];

											// Get fields
											$fields = self::get_fields_from_form($form);

											// Get field
											if(!isset($fields[$field_id])) { break; }
											$field = $fields[$field_id];

											// Get field type
											if(!isset($field->type)) { break; }
											$field_type = $field->type;

											// Get field types in single dimension array
											$field_types = self::get_field_types();

											// Get field config
											if(!isset($field_types[$field_type])) { break; }
											$field_type_config = $field_types[$field_type];

											// WPAutoP?
											$wpautop = self::field_wpautop($field, $field_type_config);
											$html_encode = !$wpautop;

											// Get meta key
											$meta_key = WS_FORM_FIELD_PREFIX . $field_id;
											if(
												($section_repeatable_index !== false) &&
												!empty($field->section_repeatable)
											) {

												$meta_key .= '_' . $section_repeatable_index;
											}

											if(isset($submit->meta[$meta_key])) {

												// Get value
												$meta = $submit->meta[$meta_key];

												// Get delimiter
												$delimiter = isset($variable_attribute_array[1]) ? $variable_attribute_array[1] : NULL;

												// Get column
												$column_override = isset($variable_attribute_array[2]) ? $variable_attribute_array[2] : false;

												$value = self::parse_variables_meta_value($form, $meta, $content_type, $html_encode, 'parse_variable', 0, $delimiter, false, false, false, $column_override);

											} else {

												// No submitted value, get static
												$value = '';

												// Check for static fields
												$field_static = isset($field_type_config['static']) ? $field_type_config['static'] : false;

												if($field_static) {

													if($field_static === true) {

														// If static set to true, we use the mask_field
														$value = isset($field_type_config['mask_field_static']) ? $field_type_config['mask_field_static'] : '';

													} else {

														// Get value
														$value = self::get_object_meta_value($field, $field_static, '');
													}
												}
											}

											$parsed_variable = self::parse_variables_process($value, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											switch($parse_variable) {

												case 'ecommerce_field_price' :

													$parsed_variable = self::get_price(self::get_number($parsed_variable, 0, true));
													break;

												case 'field_date_offset' :

													if($field_type !== 'datetime') { break; }

													// Get date/time type
													$input_type_datetime = self::get_object_meta_value($field, 'input_type_datetime', 'date');

													// Get input date
													$parsed_variable_date = self::get_date_by_type($parsed_variable, (object) $field);

													// Ensure parsed_variable_date is a date
													if(
														($parsed_variable_date !== false)
													) {

														// Check for format
														if(
															isset($variable_attribute_array[2]) &&
															($variable_attribute_array[2] != '')
														) {

															$format_date = $variable_attribute_array[2];

														} else {

															// Get date format
															$format_date = self::get_object_meta_value($field, 'format_date_input', get_option('date_format'));
														}

														if(empty($format_date)) { $format_date = get_option('date_format'); }

														// Check for offset
														$seconds_offset = intval(self::parse_variables_process($variable_attribute_array[1], $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1));

														// Process date
														$parsed_variable = date($format_date, strtotime($parsed_variable_date) + $seconds_offset);
													}

													break;

												case 'field_float' :

													$parsed_variable = self::get_number($parsed_variable, 0, true);
													break;
											}

											// WPAutoP
											if($wpautop) { $parsed_variable = wpautop($parsed_variable); }

											break;

										case 'post_meta' :

											if(is_null($post)) { break; }

											$post_meta = get_post_meta($post->ID);
											if($post_meta === false) { break; }

											$meta_key = $variable_attribute_array[0];

											if(!isset($post_meta[$meta_key])) { break; }
											if(!isset($post_meta[$meta_key][0])) { break; }

											$parsed_variable = $post_meta[$meta_key][0];
											if(is_array($parsed_variable)) { $parsed_variable = serialize($parsed_variable); }
											break;

										case 'user_meta' :

											// Check we have user data
											if(($user === false) || !$user->ID) { break; }

											$meta_key = $variable_attribute_array[0];

											$parsed_variable = get_user_meta($user->ID, $meta_key, true);
											if(is_array($parsed_variable)) { $parsed_variable = serialize($parsed_variable); }

											break;

										case 'ecommerce_price' :

											$value = $variable_attribute_array[0];

											$parsed_variable = self::parse_variables_process($value, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											$parsed_variable = self::get_price(self::get_number($parsed_variable, 0, true));

											break;

										case 'select_option_text' :
										case 'checkbox_label' :
										case 'radio_label' :

											if(!isset($submit->meta)) { break; }

											if(!is_numeric($variable_attribute_array[0])) { break; }

											$field_id = $variable_attribute_array[0];
											$datagrid_delimiter = isset($variable_attribute_array[1]) ? $variable_attribute_array[1] : NULL;

											// Get meta key
											$meta_key = WS_FORM_FIELD_PREFIX . $field_id;
											if($section_repeatable_index !== false) {

												$meta_key .= '_' . $section_repeatable_index;
											}

											if(!isset($submit->meta[$meta_key])) { break; }

											// Get value
											$meta = $submit->meta[$meta_key];
											$value = self::parse_variables_meta_value($form, $meta, $content_type, true, 'label', 0, $datagrid_delimiter);

											$parsed_variable = self::parse_variables_process($value, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											break;

										case 'acf_repeater_field' :

											if(is_null($post)) { break; }

											$parent_field = $variable_attribute_array[0];
											$sub_field = $variable_attribute_array[1];

											$parent_field_array = explode(',', $parent_field);
											foreach($parent_field_array as $key => $value) {

												$parent_field_array[$key] = trim($value);
											}

											$parsed_variable = WS_Form_ACF::acf_repeater_field_walker($parent_field_array, $sub_field, $post);

											break;

										// Date
										case 'post_date_custom' :
										case 'server_date_custom' :

											$seconds_offset = intval(isset($variable_attribute_array[1]) ? self::parse_variables_process($variable_attribute_array[1], $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1) : 0);

											$parsed_variable = date($variable_attribute_array[0], strtotime($parse_variable_value) + $seconds_offset);

											if($content_type == 'text/html') { $parsed_variable = htmlentities($parsed_variable); }

											break;

										// Random number
										case 'random_number' :

											$random_number_min = intval($variable_attribute_array[0]);
											$random_number_max = intval($variable_attribute_array[1]);
											$parsed_variable = rand($random_number_min, $random_number_max);
											break;

										// Random string
										case 'random_string' :

											$random_string_length = absint($variable_attribute_array[0]);
											$random_string_characters = $variable_attribute_array[1];
											$random_string_character_length = strlen($random_string_characters) - 1;
											$parsed_variable = '';
											for($random_string_index = 0; $random_string_index < $random_string_length; $random_string_index++) { $parsed_variable .= $random_string_characters[rand(0, $random_string_character_length)]; }
											break;

										// Date
										case 'blog_date_custom' :

											$seconds_offset = intval(isset($variable_attribute_array[1]) ? self::parse_variables_process($variable_attribute_array[1], $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1) : 0);

											$parsed_variable = date($variable_attribute_array[0], current_time('timestamp') + $seconds_offset);

											if($content_type == 'text/html') { $parsed_variable = htmlentities($parsed_variable); }
											break;

										// User
										case 'user_lost_password_url' :

											// Check we have user data
											if(($user === false) || !$user->ID) { break; }

											// Check we can produce a lost password URL
											if(!(

												isset($user->lost_password_key) && 
												($user->lost_password_key != '') && 
												isset($user->user_login) && 
												($user->user_login != '')

											)) { break; }

											// Get path
											$path = $variable_attribute_array[0];

											if($path !== '') {

												$parsed_variable = network_site_url(sprintf('%s?key=%s&login=%s', $path, rawurlencode($user->lost_password_key),rawurlencode($user->user_login)));

											} else {

												$parsed_variable = network_site_url(sprintf('wp-login.php?action=rp&key=%s&login=%s', rawurlencode($user->lost_password_key), rawurlencode($user->user_login)), 'login');
											}

											break;

										// Uppercase
										case 'upper' :
										case 'lower' :
										case 'ucwords' :
										case 'ucfirst' :
										case 'capitalize' :
										case 'sentence' :
										case 'wpautop' :
										case 'trim' :

											// Get input value
											$input_value = self::parse_variables_process($variable_attribute_array[0], $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);

											switch($parse_variable) {

												// Uppercase
												case 'upper' :

													$parsed_variable = strtoupper($input_value);
													break;

												// Lowercase
												case 'lower' :

													$parsed_variable = strtolower($input_value);
													break;

												// Uppercase words
												case 'ucwords' :

													$parsed_variable = ucwords($input_value);
													break;

												// Uppercase first
												case 'ucfirst' :

													$parsed_variable = ucfirst($input_value);
													break;

												// Capitalize
												case 'capitalize' :

													$parsed_variable = ucwords(strtolower($input_value));
													break;

												// Sentence
												case 'sentence' :

													$parsed_variable = ucfirst(strtolower($input_value));
													break;

												// WPAutoP
												case 'wpautop' :

													$parsed_variable = wpautop($input_value);
													break;

												// Trim
												case 'trim' :

													$parsed_variable = trim($input_value);
													break;
											}

											break;
									}

									// Assign value
									if($parsed_variable !== false) {

										if($parse_variable_single_parse) {

											$variables_single_parse[substr($parse_variable_full, 1)] = $parsed_variable;

										} else {

											$variables[substr($parse_variable_full, 1)] = $parsed_variable;
										}
									}

								} while ($variable_index_of !== false);

								// Secure variables
								if($exclude_secure) { $variables = self::parse_variables_exclude_secure($variables); }

								// Parse function
								$parse_string = self::mask_parse($parse_string, $variables);
							}
					}
				}
			}

			// Release memory
			$parse_variables_config = null;

			if($scope === false) {

				// Blog
				if(strpos($parse_string, 'blog') !== false) {

					$variables['blog_admin_email'] = get_bloginfo('admin_email');
				}

				// Seconds
				if(strpos($parse_string, 'seconds_epoch') !== false) {

					$variables['seconds_epoch'] = date('U');
				}

				// Form
				if(strpos($parse_string, 'form') !== false) {

					$variables['form_label'] = self::get_object_var($form, 'label', '');
					$variables['form_id'] = self::get_object_var($form, 'id', '');
					$variables['form_checksum'] = self::get_object_var($form, 'published_checksum', '');

					// These variables are only available on the public side
					$variables['form_obj_id'] = '';
					$variables['form_framework'] = '';
					$variables['form_instance_id'] = 0;
				}

				// Section
				if(strpos($parse_string, 'section') !== false) {

					$variables['section_row_index'] = $section_repeatable_index;
					$variables['section_row_number'] = $section_row_number;
				}

				// Post
				if(strpos($parse_string, 'post') !== false) {

					$post_not_null = !is_null($post);

					$variables['post_id'] = ($post_not_null ? $post->ID : '');
					$variables['post_type'] = ($post_not_null ? $post->post_type : '');
					$variables['post_title'] = ($post_not_null ? $post->post_title : '');
					$variables['post_name'] = ($post_not_null ? $post->post_name : '');
					$variables['post_content'] = ($post_not_null ? $post->post_content : '');
					$variables['post_excerpt'] = ($post_not_null ? $post->post_excerpt : '');
					$variables['post_status'] = ($post_not_null ? $post->post_status : '');
					$variables['post_url'] = ($post_not_null ? get_permalink($post->ID) : '');
					$variables['post_url_edit'] = ($post_not_null ? get_edit_post_link($post->ID) : '');
					$variables['post_date'] = ($post_not_null ? date(get_option('date_format'), strtotime($post->post_date)) : '');
					$variables['post_time'] = ($post_not_null ? date(get_option('time_format'), strtotime($post->post_date)) : '');
				}

				// User
				if(strpos($parse_string, 'user') !== false) {

					$user_id = (($user === false) ? 0 : $user->ID);

					$variables['user_id'] = $user_id;
					$variables['user_login'] = (($user_id > 0) ? $user->user_login : '');
					$variables['user_nicename'] = (($user_id > 0) ? $user->user_nicename : '');
					$variables['user_email'] = (($user_id > 0) ? $user->user_email : '');
					$variables['user_display_name'] = (($user_id > 0) ? $user->display_name : '');
					$variables['user_url'] = (($user_id > 0) ? $user->user_url : '');
					$variables['user_registered'] = (($user_id > 0) ? $user->user_registered : '');
					$variables['user_first_name'] = (($user_id > 0) ? get_user_meta($user_id, 'first_name', true) : '');
					$variables['user_last_name'] = (($user_id > 0) ? get_user_meta($user_id, 'last_name', true) : '');
					$variables['user_bio'] = (($user_id > 0) ? get_user_meta($user_id, 'description', true) : '');
					$variables['user_nickname'] = (($user_id > 0) ? get_user_meta($user_id, 'nickname', true) : '');
					$variables['user_admin_color'] = (($user_id > 0) ? get_user_meta($user_id, 'admin_color', true) : '');
					$variables['user_lost_password_key'] = (($user_id > 0) ? $user->lost_password_key : '');
				}

				// Author
				if(strpos($parse_string, 'author') !== false) {

					$post_author_id = !is_null($post) ? $post->post_author : 0;

					$variables['author_email'] = get_the_author_meta('user_email', $post_author_id);
				}

				// Submit
				if(strpos($parse_string, 'submit') !== false) {

					$submit_id = self::get_object_var($submit, 'id', '');
					$submit_hash = self::get_object_var($submit, 'hash', '');
					$submit_token = self::get_object_var($submit, 'token', '');

					$variables['submit_id'] = $submit_id;
					$variables['submit_user_id'] = self::get_object_var($submit, 'user_id', '');
					$variables['submit_hash'] = $submit_hash;
					$variables['submit_status'] = self::get_object_var($submit, 'status', '');
					$variables['submit_status_label'] = WS_Form_Submit::db_get_status_name(self::get_object_var($submit, 'status', ''));

					// Get form ID
					$form_id = absint(self::get_object_var($form, 'id', ''));

					// Build submit admin URL and link
					if($form_id && $submit_id) {

						$submit_admin_url = get_admin_url(null, 'admin.php?page=ws-form-submit&id=' . $form_id . '#' . $submit_id);

						$variables['submit_admin_url'] = $submit_admin_url;
						$variables['submit_admin_link'] = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $submit_admin_url);
					}

					// Build submit URL and link
					if($form_id && $submit_hash) {

						$referrer = wp_sanitize_redirect(WS_Form_Common::get_http_env_raw(array('HTTP_REFERER')));

						$wsf_hash = rawurlencode(json_encode(array(

							// Save single submit hash
							array('id' => $form_id, 'hash' => $submit_hash, 'token' => $submit_token)
						)));

						$submit_url = add_query_arg('wsf_hash', $wsf_hash, $referrer);

						$variables['submit_url'] = $submit_url;
						$variables['submit_url_hash'] = $wsf_hash;
						$variables['submit_link'] = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $submit_url);
					}

					// URL
					if(strpos($parse_string, 'url_login') !== false) { $variables['url_login'] = wp_login_url(); }
					if(strpos($parse_string, 'url_logout') !== false) { $variables['url_logout'] = wp_logout_url(); }
					if(strpos($parse_string, 'url_lost_password') !== false) { $variables['url_lost_password'] = wp_lostpassword_url(); }
					if(strpos($parse_string, 'url_register') !== false) { $variables['url_register'] = wp_registration_url(); }
				}

				// E-Mail
				if(strpos($parse_string, 'email') !== false) {

					$variables['email_promo'] = sprintf(

						/* translators: %s = WS Form */
						__('Powered by %s.', 'ws-form'),

						sprintf(($content_type == 'text/html') ? '<a href="%s" style="color: #999999; font-size: 12px; text-align: center; text-decoration: none;">WS Form</a>' : 'WS Form %s', self::get_plugin_website_url('', 'email_footer'))
					);
				}

				// E-mail - CSS
				if(strpos($parse_string, 'email_css') !== false) {

					$ws_form_css = new WS_Form_CSS();
					$css = $ws_form_css->get_email();
					$variables['email_css'] = $css;
				}

				// E-mail - E-Commerce
				if(strpos($parse_string, 'email_ecommerce') !== false) {

					$variables['email_ecommerce'] = self::parse_variables_ecommerce($submit);
				}

				// E-Commerce
				if(strpos($parse_string, 'ecommerce') !== false) {

					// Get e-commerce config
					$ecommerce_config = WS_Form_Config::get_ecommerce();

					foreach($ecommerce_config['cart_price_types'] as $meta_key => $cart_price_type) {

						$meta_key = 'ecommerce_cart_' . $meta_key;

						if(strpos($parse_string, $meta_key) !== false) {

							$value = isset($submit->meta[$meta_key]) ? $submit->meta[$meta_key] : false;

							if(($value !== '') && ($value !== false)) {

								$variables[$meta_key] = $value;
								$variables[$meta_key . '_currency'] = self::get_price($value);

							} else {

								$variables[$meta_key] = self::get_price(0);
							}
						}
					}

					foreach($ecommerce_config['meta_keys'] as $meta_key => $meta_key_config) {

						if(strpos($parse_string, $meta_key) !== false) {

							$value = isset($submit->meta[$meta_key]) ? $submit->meta[$meta_key] : false;

							if(($value !== '') && ($value !== false)) {

								// Lookup?
								if(
									(isset($meta_key_config['lookup'])) &&
									(isset($ecommerce_config[$meta_key_config['lookup']])) &&
									(isset($ecommerce_config[$meta_key_config['lookup']][$value]))
								) {

									$value = $ecommerce_config[$meta_key_config['lookup']][$value]['label'];

								}

								// Type
								if(isset($meta_key_config['type'])) {

									switch($meta_key_config['type']) {

										case 'price' : $variables[$meta_key . '_currency'] = self::get_price($value); break;
									}
								}

								$variables[$meta_key] = $value;
							}
						}
					}
				}

				// Tracking
				if((strpos($parse_string, 'tracking') !== false) && isset($submit->meta)) {

					$tracking_array = WS_Form_Config::get_tracking(false);

					foreach($tracking_array as $meta_key => $tracking) {

						// Check tracking meta_key is enabled
						if(($form === false) || !self::get_object_meta_value($form, $meta_key, false)) { continue; }

						// Check tracking meta_key exists in parse string
						if(strpos($parse_string, '#' . $meta_key) === false) { continue; }

						// Check meta exists in submit data
						if(!isset($submit->meta[$meta_key])) {

							$value = '';

						} else {

							// Get value
							$meta = array(

								'type'	=>	$tracking['type'],
								'value'	=>	$submit->meta[$meta_key]
							);

							// Get parsed version
							$value = self::parse_variables_meta_value($form, $meta, $content_type);
						}

						$variables[$meta_key] = $value;
					}
				}
			}

			// Variables filter
			$variables = apply_filters('wsf_parse_variables', $variables, $parse_string, $form, $submit, $content_type);

			// Secure variables
			if($exclude_secure) { $variables = self::parse_variables_exclude_secure($variables); }

			// Parse until no more changes made
			$parse_string_before = $parse_string;
			$parse_string = self::mask_parse($parse_string, $variables);
			$parse_string = self::mask_parse($parse_string, $variables_single_parse, '#', true);
			$parse_string = apply_filters('wsf_config_parse_string', $parse_string);

			if(
				($parse_string !== $parse_string_before) &&
				(strpos($parse_string, '#') !== false)
			) {

				$parse_string = self::parse_variables_process($parse_string, $form, $submit, $content_type, $scope, $section_repeatable_index, $section_row_number, $exclude_secure_nested_parse, $action_config, $depth + 1);
			}

			return $parse_string;
		}

		// Blank secure parse variables
		public static function parse_variables_exclude_secure($variables) {

			$parse_variables_secure = WS_Form_Config::get_parse_variables_secure();

			foreach($parse_variables_secure as $parse_variable_key) {

				if(isset($variables[$parse_variable_key])) {

					$variables[$parse_variable_key] = '&num;' . $parse_variable_key;
				}
			}

			return $variables;
		}

		// Find closing string
		public static function closing_string_index($parse_string, $closing_string, $opening_string, $index) {

			$depth = 1;

			while($depth > 0) {

				// Look for embedded if
				$opening_string_index = strpos($parse_string, $opening_string, $index);
				$closing_string_index = strpos($parse_string, $closing_string, $index);

				// Embedded opening string
				if(
					($opening_string_index !== false) &&
					($closing_string_index !== false) &&
					($opening_string_index < $closing_string_index) 
				) {
					$index = $opening_string_index + strlen($opening_string);
					$depth++;
					continue;
				}

				// Embedded closing string
				if(
					($closing_string_index !== false) &&
					($depth > 1)
				) {
					$index = $closing_string_index + strlen($closing_string);
					$depth--;
					continue;
				}

				// Associated closing string
				if(
					($closing_string_index !== false) &&
					($depth === 1)
				) {
					break;
				}

				break;
			}

			return $closing_string_index;
		}

		// Parse form data for use with parse_variables
		public static function parse_variables_meta_value($form, $meta, $content_type, $html_encode = true, $column = 'parse_variable', $column_id_default = 0, $datagrid_delimiter = NULL, $action_config = false, $field = false, $submit = false, $column_override = false) {

			$type = $meta['type'];
			$value = $meta['value'];

			if($value == '') { return ''; }

			// Content type override
			if($content_type == 'column_value') {

				$content_type = 'text/plain';
				$column = 'value';
			}

			// HTML encode values
			if($content_type == 'text/html' && !is_array($value)) {

				if($html_encode) { $value = htmlentities($value); }

				switch($type) {

					case 'url' :

						$value = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $value);
						break;

					case 'tel' :

						$value = sprintf('<a href="tel:%1$s">%1$s</a>', $value);
						break;

					case 'email' :

						if(filter_var($value, FILTER_VALIDATE_EMAIL)) {

							$value = sprintf('<a href="mailto:%1$s">%1$s</a>', $value);
						}
						break;

					case 'ip' :

						// Get lookup URL mask
						$ip_lookup_url_mask = self::option_get('ip_lookup_url_mask');
						if(empty($ip_lookup_url_mask)) { $value = htmlentities($value); break; }

						// Split IP (IP can be comma separated if proxy in use)
						$ip_array = explode(',', $value);
						$value_array = [];

						foreach($ip_array as $ip) {

							// Trim
							$ip = trim($ip);

							// Validate IP
							if(inet_pton($ip) === false) { continue; }

							// Get #value for mask
							$ip_lookup_url_mask_values = array('value' => $ip);

							// Build lookup URL
							$ip_lookup_url = self::mask_parse($ip_lookup_url_mask, $ip_lookup_url_mask_values);

							$value_array[] = '<a href="' . $ip_lookup_url . '" target="_blank">' . htmlentities($ip) . '</a>';
						}

						$value = implode('<br />', $value_array);

						break;

					case 'latlon' :

						if(preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $value) == 1) {

							// Get lookup URL mask
							$latlon_lookup_url_mask = self::option_get('latlon_lookup_url_mask');
							if(empty($latlon_lookup_url_mask)) { $value = htmlentities($value); break; }

							// Get #value for mask
							$latlon_lookup_url_mask_values = array('value' => $value);

							// Build lookup URL
							$latlon_lookup_url = self::mask_parse($latlon_lookup_url_mask, $latlon_lookup_url_mask_values);

							$value = '<a href="' . $latlon_lookup_url . '" target="_blank">' . htmlentities($value) . '</a>';

						} else {

							switch(absint($value)) {

								case 1 :

									$value = __('User denied the request for geo location', 'ws-form');
									break;

								case 2 :

									$value = __('Geo location information was unavailable', 'ws-form');
									break;

								case 3 :

									$value = __('The request to get user geo location timed out', 'ws-form');
									break;

								default :

									$value = '-';
							}
						}
						break;
				}
			}

			// Process by field type
			switch($type) {

				case 'file' :
				case 'signature' :

					$files = $meta['value'];

					if(!is_array($files)) { break; }

					// File links? (Email action settings)
					if(
						($action_config !== false) &&
						is_array($action_config) &&
						isset($action_config['meta']) && 
						isset($action_config['meta']['action_email_message_file_links'])
					) {

						$file_links = $action_config['meta']['action_email_message_file_links'];

					} else {

						$file_links = false;
					}

					// File embed? (Global settings)
					$file_embed = self::option_get('action_email_embed_images', true);

					$value_array = array();

					foreach($files as $file_object_index => $file_object) {

						if(!is_array($file_object)) { continue; }

 						// Get file handler
						$file_handler = isset($file_object['handler']) ? $file_object['handler'] : '';
 						if($file_handler == '') { $file_handler = 'wsform'; }
 						if(!isset(WS_Form_File_Handler::$file_handlers[$file_handler])) { continue; }
 						$file_handler = WS_Form_File_Handler::$file_handlers[$file_handler];

 						// Get field ID
 						$field_id = (($field !== false) && isset($field->id)) ? $field->id : false;

 						// Get value array
 						if(method_exists($file_handler, 'get_value_parse_variable')) {

 							// Get hash
 							$hash = (($submit !== false) && isset($submit->hash)) ? $submit->hash : '';

 							// Use file handler to get the value for parse variables
	 						$value_array[] = $file_handler->get_value_parse_variable($file_object, $field_id, $file_object_index, $hash, $file_links, $file_embed, $content_type);

	 					} else {

	 						// Fallback
							$file_size = WS_Form_Common::get_file_size($file_object['size']);
	 						$value_array[] = sprintf('%s (%s)', $file_object['name'], $file_size);
	 					}
					}
	
					$value = implode((($content_type == 'text/html') ? '<br />' : "\n"), $value_array);

					break;

				case 'datetime' :

					$fields = self::get_fields_from_form($form);

					if(!isset($fields[$meta['id']])) { break; }

					$field = $fields[$meta['id']];

					// If submit is read from database, it is split into MySQL and presentable formats
					if(is_array($value) && isset($value['mysql'])) { $value = $value['mysql']; }

					$value = self::get_date_by_type($value, (object) $field);

					break;

				case 'googlemap' :

					if(
						is_array($value) &&
						isset($value['lat']) &&
						isset($value['lng'])
					) {

						$value = sprintf('%.7f,%.7f', $value['lat'], $value['lng']);

						if($content_type == 'text/html') {

							// Get lookup URL mask
							$latlon_lookup_url_mask = self::option_get('latlon_lookup_url_mask');
							if(empty($latlon_lookup_url_mask)) { break; }

							// Get #value for mask
							$latlon_lookup_url_mask_values = array('value' => $value);

							// Build lookup URL
							$latlon_lookup_url = self::mask_parse($latlon_lookup_url_mask, $latlon_lookup_url_mask_values);

							$value = '<a href="' . $latlon_lookup_url . '" target="_blank">' . htmlentities($value) . '</a>';
						}

					} else {

						$value = '';
					}

					break;

				case 'price_select' :
				case 'select' :
				case 'price_checkbox' :
				case 'checkbox' :
				case 'price_radio' :
				case 'radio' :

					// If value_array is set, we'll use that in case we have repeaters
					if(isset($meta['value_array'])) {

						$value = $meta['value_array'];
					}

					$fields = self::get_fields_from_form($form);

					if(!isset($fields[$meta['id']])) { break; }

					$field = $fields[$meta['id']];

					if(is_null($datagrid_delimiter)) {

						$delimiter_text_plain = "\n";
						$delimiter_text_html = "<br />";

					} else {

						$delimiter_text_plain = $delimiter_text_html = $datagrid_delimiter;
					}

					// Build default value
					$default_value = is_array($value) ? (($content_type == 'text/html') ? implode($delimiter_text_html, $value) : implode($delimiter_text_plain, $value)) : $value;

					// Get data grid value
					$value = self::get_datagrid_value($field, $value, $content_type, $default_value, $column, $column_id_default, $datagrid_delimiter, $column_override);

					break;

				default :

					$value = is_array($value) ? (($content_type == 'text/html') ? implode("<br />", $value) : implode("\n", $value)) : $value;
			}

			return $value;
		}

		// #email_ecommerce
		public static function parse_variables_ecommerce($submit) {

			$ecommerce_html = '';

			$ecommerce_rows = array();

			// Get e-commerce config
			$ecommerce_config = WS_Form_Config::get_ecommerce();

			foreach($ecommerce_config['cart_price_types'] as $meta_key => $cart_price_type) {

				$meta_key = 'ecommerce_cart_' . $meta_key;

				$value = isset($submit->meta[$meta_key]) ? $submit->meta[$meta_key] : false;

				if(($value !== '') && ($value !== false)) {

					$priority = isset($cart_price_type['priority']) ? $cart_price_type['priority'] : 10000;

					$value = self::get_price($value);
					$ecommerce_rows[] = array('label' => $cart_price_type['label'], 'value' => $value, 'priority' => $priority);

				} else {

					continue;
				}
			}

			foreach($ecommerce_config['meta_keys'] as $meta_key => $meta_key_config) {

				$value = isset($submit->meta[$meta_key]) ? $submit->meta[$meta_key] : false;

				if(($value !== '') && ($value !== false)) {

					$label = $meta_key_config['label'];
					$priority = isset($meta_key_config['priority']) ? $meta_key_config['priority'] : 10000;

					// Lookup?
					if(
						(isset($meta_key_config['lookup'])) &&
						(isset($ecommerce_config[$meta_key_config['lookup']])) &&
						(isset($ecommerce_config[$meta_key_config['lookup']][$value]))
					) {

						$value = $ecommerce_config[$meta_key_config['lookup']][$value]['label'];
					}

					// Type
					if(isset($meta_key_config['type'])) {

						switch($meta_key_config['type']) {

							case 'price' : $value = self::get_price($value); break;
						}
					}

					// Push to e-commerce rows
					$ecommerce_rows[] = array('label' => $label, 'value' => $value, 'priority' => $priority);
				}
			}

			// Sort e-commerce rows by priority
			uasort($ecommerce_rows, function ($ecommerce_rows_1, $ecommerce_rows_2) {

				return $ecommerce_rows_1['priority'] < $ecommerce_rows_2['priority'] ? -1 : 1;
			});

			if(count($ecommerce_rows) > 0) {

				foreach($ecommerce_rows as $ecommerce_row) {

					$ecommerce_html .= '<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 10px;"><strong>' . htmlentities($ecommerce_row['label']) . '</strong><br />' . $ecommerce_row['value'] . '</p>';
				}
			}
			return $ecommerce_html;
		}
		// #email_submission
		public static function parse_variables_fields_all($form, $submit, $content_type, $render_group_labels, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config) {

			$fields_all = self::parse_variables_fields_all_group($form->groups, $form, $submit, $content_type, $render_group_labels, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config);

			return $fields_all;
		}

		// Run through each group
		public static function parse_variables_fields_all_group($groups, $form, $submit, $content_type, $render_group_labels, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config) {

			$groups_html = '';

			$group_count = count($groups);
			$group_label_join = '';

			foreach($groups as $key => $group) {

				if(isset($groups[$key]->sections)) {

					$sections_html = self::parse_variables_fields_all_section($group->sections, $form, $submit, $content_type, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config);

					// Should label be rendered?
					$render_label =	(
										(
											$render_group_labels == 'true'
										)
										||
										(
											($render_group_labels == 'auto') && 
											($sections_html != '')
										)
									) && self::get_object_meta_value($group, 'label_render');

					if(($group_count > 0) && $render_label) {

						switch($content_type) {

							case 'text/html' :

								$groups_html .= $group_label_join . ($render_label ? '<h2>' . htmlentities($group->label) . "</h2>\n" : '');
								$group_label_join = "<hr style=\"margin: 20px 0\" />\n";
								break;

							default :

								$groups_html .= $group_label_join . "** " . $group->label . " **\n\n";
								$group_label_join = "\n";
						}

					}

					$groups_html .= $sections_html;
				}
			}

			return $groups_html;
		}

		// Run through each section
		public static function parse_variables_fields_all_section($sections, $form, $submit, $content_type, $render_section_labels, $render_field_labels, $render_blank_fields, $render_static_fields, $hidden_array, $action_config) {

			$sections_html = '';

			$section_count = count($sections);
			$section_label_join = '';

			// Unserialize section_repeatable
			$section_repeatable = is_serialized($submit->section_repeatable) ? unserialize($submit->section_repeatable) : false;

			// Get field types in single dimension array
			$field_types = WS_Form_Config::get_field_types_flat();

			foreach($sections as $key => $section) {

				// Build section ID string
				$section_id_string = 'section_' . $section->id;
				$section_repeatable_array = (

					($section_repeatable !== false) &&
					isset($section_repeatable[$section_id_string]) &&
					isset($section_repeatable[$section_id_string]['index'])

				) ? $section_repeatable[$section_id_string]['index'] : [false];

				if(!isset($sections[$key]->fields)) { continue; }

				// Loop through section_repeatable_array
				foreach($section_repeatable_array as $section_repeatable_array_index => $section_repeatable_index) {

					$fields_html = '';

					// Check if repeatable
					$section_repeatable_html = '';
					$section_repeatable_suffix = '';

					// Repeatable, so render fieldset and set field_name suffix
					if($section_repeatable_index !== false) {

						// Repeatable section found
						$section_repeatable_index = absint($section_repeatable_index);
						if($section_repeatable_index === 0) { continue; }

						// Render fieldset
						$section_repeatable_html = '<h4>#' . (absint($section_repeatable_array_index) + 1) . "</h4>\n";

						// Set field_name suffix
						$section_repeatable_suffix = '_' . $section_repeatable_index;
					}

					// Process fields
					foreach($section->fields as $field) {

						$field_type = $field->type;
						$field_type_config = $field_types[$field_type];

						// Remove layout editor only fields
						$layout_editor_only = isset($field_type_config['layout_editor_only']) ? $field_type_config['layout_editor_only'] : false;
						if($layout_editor_only) { continue; }

						// Check for excluded fields
						$exclude_email = self::get_object_meta_value($field, 'exclude_email', false);
						if($exclude_email) { continue; }

						// Check for static fields
						$field_static = isset($field_type_config['static']) ? $field_type_config['static'] : false;

						if($render_static_fields && $field_static) {

							// do_shortcode (Only on static fields)
							$meta_do_shortcode = isset($field_type_config['meta_do_shortcode']) ? $field_type_config['meta_do_shortcode'] : false;
							if($meta_do_shortcode !== false) {

								if(!is_array($meta_do_shortcode)) { $meta_do_shortcode = array($meta_do_shortcode); }

								foreach($meta_do_shortcode as $meta_do_shortcode_meta_key) {

									// Check meta key exists
									if(
										!isset($field->meta) ||
										!isset($field->meta->{$meta_do_shortcode_meta_key})

									) { continue; }

									// Update form_object
									$field->meta->{$meta_do_shortcode_meta_key} = WS_Form_Common::do_shortcode($field->meta->{$meta_do_shortcode_meta_key});
								}
							}

							// Build field name
							$field_name = WS_FORM_FIELD_PREFIX . $field->id;
							if($section_repeatable_index !== false) {

								$field_name = sprintf('%s[%u]', $field_name, $section_repeatable_index);
							}

							// Bypass hidden static elements
							if(in_array($field_name, $hidden_array)) {

								continue;
							}

							if($field_static === true) {

								// If static set to true, we use the mask_field
								$mask_field = isset($field_type_config['mask_field_static']) ? $field_type_config['mask_field_static'] : '';

								if($mask_field == '') {

									$mask_field = isset($field_type_config['mask_field']) ? $field_type_config['mask_field'] : '';
								}

								$fields_html .= self::parse_variables_process($mask_field, $form, $submit, $content_type);

							} else {

								// Get value
								$value = self::get_object_meta_value($field, $field_static, '');

								// WPAutoP?
								$wpautop = self::field_wpautop($field, $field_type_config);
								if($wpautop) { $value = wpautop($value); }

								// Get meta value
								$fields_html .= self::parse_variables_process($value, $form, $submit, $content_type);
							}
							continue;
						}

						// Check to ensure this field is saved
						$submit_save = isset($field_type_config['submit_save']) ? $field_type_config['submit_save'] : false;
						if(!$submit_save) { continue; }

						// Get field label
						$label = $field->label;

						// Should label be rendered?
						$render_label =	(

							($render_field_labels == 'true')

							||

							(
								($render_field_labels == 'auto') &&
								self::get_object_meta_value($field, 'label_render')
							)
						);

						// Build field name
						$field_name = WS_FORM_FIELD_PREFIX . $field->id . $section_repeatable_suffix;

						// WPAutoP
						$wpautop = self::field_wpautop($field, $field_type_config);
						$html_encode = !$wpautop;

						if(isset($submit->meta[$field_name])) {

							// Get submit meta
							$meta = $submit->meta[$field_name];

							// Get field value
							$value = self::parse_variables_meta_value($form, $meta, $content_type, $html_encode, 'parse_variable', 0, NULL, $action_config, $field, $submit);

						} else {

							$value = '';
						}

						// No submit value found
						if($value == '') {

							if($render_blank_fields) {

								$value = '-';

							} else {

								continue;
							}
						}

						// WPAutoP?
						if($wpautop) { $value = wpautop($value); }

						// Add to fields_html HTML
						switch($content_type) {

							case 'text/html' :

								$fields_html .= '<p>' . ($render_label ? ('<strong>' . htmlentities($label) . '</strong><br />') : '') . $value . "</p>\n";
								break;

							default :

								$fields_html .= ($render_label ? ($label . "\n") : '') . $value . "\n\n";
								break;
						}
					}

					// Should label be rendered?
					$render_label =	(

						($render_section_labels == 'true')

						||

						(
							($render_section_labels == 'auto') &&
							($fields_html != '')
						)
					)
					&& self::get_object_meta_value($section, 'label_render')
					&& ($section_repeatable_array_index == 0);

					// Add section title if fields found
					if($render_label) {

						switch($content_type) {

							case 'text/html' :

								$sections_html .= $render_label ? '<h3>' . htmlentities($section->label) . "</h3>\n" : '';
								break;

							default :

								$sections_html .= $section_label_join . "* " . $section->label . " *\n\n";
								$section_label_join = "\n\n";
						}
					}

					// Add fields
					if($fields_html != '') { $sections_html .= $section_repeatable_html . $fields_html; }
				}
			}

			return $sections_html;
		}

		// WPAutoP
		public static function field_wpautop($field, $field_type_config) {

			// Meta wpautop
			$wpautop = isset($field_type_config['wpautop']) ? $field_type_config['wpautop'] : false;	
			$wpautop_conditions = isset($field_type_config['wpautop_conditions']) ? $field_type_config['wpautop_conditions'] : false;	

			if(
				($wpautop_conditions !== false) &&
				is_array($wpautop_conditions)
			) {

				$wpautop = false;

				foreach($wpautop_conditions as $meta_key => $meta_value) {

					if(self::get_object_meta_value($field, $meta_key, '') === $meta_value) {

						$wpautop = true;
					}
				}
			}

			return $wpautop;
		}

		// Get value label lookup
		public static function get_datagrid_value($field, $value_array, $content_type, $default_value, $column = 'parse_variable', $column_id_default = 0, $datagrid_delimiter = NULL, $column_override = false) {

			$return_array = array();

			if(!is_array($value_array)) { return $default_value; }

			// Get meta key prefix
			$meta_key_prefix = $field->type;

			// Apply fix to meta key prefix
			switch($meta_key_prefix) {

				case 'price_select' :

					$meta_key_prefix = 'select_price';
					break;

				case 'price_checkbox' :

					$meta_key_prefix = 'checkbox_price';
					break;

				case 'price_radio' :

					$meta_key_prefix = 'radio_price';
					break;
			}

			// Get data grid
			$datagrid = self::get_object_meta_value($field, sprintf('data_grid_%s', $meta_key_prefix), false);
			if($datagrid === false) { return $default_value; }

			// Get value mapping column ID
			$value_column_id = self::get_object_meta_value($field, sprintf('%s_field_value', $meta_key_prefix), 0);
			$value_column_id = absint($value_column_id);

			// Get column ID to return
			$return_column_id = self::get_object_meta_value($field, sprintf('%s_field_%s', $meta_key_prefix, $column), $column_id_default);
			$return_column_id = absint($return_column_id);

			// Preparsing columns
			$label_column_id = self::get_object_meta_value($field, sprintf('%s_field_label', $meta_key_prefix), 0);
			$label_column_id = absint($label_column_id);

			$price_column_id = self::get_object_meta_value($field, sprintf('%s_field_price', $meta_key_prefix), 0);
			$price_column_id = absint($price_column_id);

			$parse_variable_column_id = self::get_object_meta_value($field, sprintf('%s_field_parse_variable', $meta_key_prefix), 0);
			$parse_variable_column_id = absint($parse_variable_column_id);

			$wc_column_id = self::get_object_meta_value($field, sprintf('%s_field_wc', $meta_key_prefix), 0);
			$wc_column_id = absint($wc_column_id);

			// Get data grid columns
			if(!isset($datagrid->columns)) { return $default_value; }
			if(!is_array($datagrid->columns)) { return $default_value; }
			$columns = $datagrid->columns;

			// Get return_column_index from return_column_id
			$return_column_index = false;
			$value_column_index = false;
			$label_column_index = false;
			$price_column_index = false;
			$parse_variable_column_index = false;
			$wc_column_index = false;

			// If column_override specified
			if($column_override !== false) {

				$return_column_index = absint($column_override);
			}

			foreach($columns as $index => $column) {

				// Check
				if(!isset($column->id)) { continue; }

				// Set indexes if column ID found
				if($column_override === false) {

					if($column->id === $return_column_id) { $return_column_index = $index; }

				} else {

					if($column->label == $column_override) { $return_column_index = $index; }
				}

				if($column->id === $value_column_id) { $value_column_index = $index; }
				if($column->id === $label_column_id) { $label_column_index = $index; }
				if($column->id === $price_column_id) { $price_column_index = $index; }
				if($column->id === $parse_variable_column_id) { $parse_variable_column_index = $index; }
				if($column->id === $wc_column_id) { $wc_column_index = $index; }
			}

			// Check that we got indexes back
			if(
				($return_column_index === false) ||
				($value_column_index === false)
			) {
				return $default_value;
			}

			// Get data grid rows
			if(!isset($datagrid->groups)) { return $default_value; }
			if(!is_array($datagrid->groups)) { return $default_value; }
			$groups = $datagrid->groups;

			foreach($groups as $group) {

				if(!isset($group->rows)) { continue; }
				if(!is_array($group->rows)) { continue; }

				$rows = $group->rows;

				foreach($rows as $row) {

					// Get data
					if(!isset($row->data)) { continue; }
					if(!is_array($row->data)) { continue; }
					$data = $row->data;

					// Check value and return indexes exist
					if(!isset($data[$value_column_index])) { continue; }
					if(!isset($data[$return_column_index])) { continue; }

					// Pre-parsing
					$mask_values_row = array(

						'data_grid_row_price' => '',
						'data_grid_row_price_currency' => '',
						'data_grid_row_woocommerce_cart' => '',
						'data_grid_row_value' => $data[$value_column_index],
						'data_grid_row_action_variable' => '',
						'data_grid_row_label' => ''
					);

					// Label
					if(
						($label_column_index !== false) &&
						isset($data[$label_column_index])
					) {

						$mask_values_row['data_grid_row_label'] = $data[$label_column_index];
					}

					// Price
					if(
						($price_column_index !== false) &&
						isset($data[$price_column_index])
					) {

						$mask_values_row['data_grid_row_price_currency'] = self::get_price($data[$price_column_index]);
						$mask_values_row['data_grid_row_price'] = self::get_number($data[$price_column_index]);
					}
					// Parse Variable
					if(
						($parse_variable_column_index !== false) &&
						isset($data[$parse_variable_column_index])
					) {

						$mask_values_row['data_grid_row_action_variable'] = $data[$parse_variable_column_index];
					}

					// WooCommerce
					if(
						($wc_column_index !== false) &&
						isset($data[$wc_column_index])
					) {

						$mask_values_row['data_grid_row_woocommerce_cart'] = $data[$wc_column_index];
					}
					// Parse columns
					foreach($data as $column_index => $column) {

						$data[$column_index] = self::mask_parse($column, $mask_values_row);
					}

					// Check if value matches
					$value_array_index = array_search($data[$value_column_index], $value_array);

					if($value_array_index !== false) {

						unset($value_array[$value_array_index]);

						$return_array[] = $data[$return_column_index];
					}
				}
			}

			// Add any value_array elements that were not found
			$return_array = array_merge($return_array, $value_array);

			// Return unique values to avoid duplicates if there are duplicate values
			$return_array = array_unique($return_array);

			if(is_null($datagrid_delimiter)) {

				$delimiter_text_plain = "\n";
				$delimiter_text_html = "<br />";

			} else {

				$delimiter_text_plain = $delimiter_text_html = $datagrid_delimiter;
			}

			return (($content_type == 'text/html') ? implode($delimiter_text_html, $return_array) : implode($delimiter_text_plain, $return_array));
		}

		// Check if user can do a WordPress capability (current_user_can not available on public side)
		public static function can_user($capability) {

			if(!function_exists('wp_get_current_user')) {

				include(ABSPATH . "wp-includes/pluggable.php"); 
			}

			return current_user_can($capability);
		}

		// Loader
		public static function loader() {
?>
<!-- Loader -->
<div id="wsf-loader"></div>
<!-- /Loader -->
<?php
		}

		// Review
		public static function review() {

			// Review nag
			$review_nag = self::option_get('review_nag', false);
			if($review_nag) { return; }

			// Determine if review nag should be shown
			$install_timestamp = absint(self::option_get('install_timestamp', time(), true));
			$review_nag_show = (time() > ($install_timestamp + (WS_FORM_REVIEW_NAG_DURATION * 86400)));
			if(!$review_nag_show) { return; }

			// Show nag
			/* translators: %1$s! = Presentable plugin name, e.g. WS Form PRO */
			self::admin_message_render(sprintf(__('<p><strong>Thank you for using %1$s!</strong></p><p>We hope you have enjoyed using the plugin. Positive reviews from awesome users like you help others to feel confident about choosing %1$s too. If convenient, we would greatly appreciate you sharing your happy experiences with the WordPress community. Thank you in advance for helping us out!</p><p class="buttons"><a href="https://wordpress.org/support/plugin/ws-form/reviews/#new-post" class="button button-primary" onclick="wsf_review_nag_dismiss();" target="_blank">Leave a review</a> <a href="#" class="button" onclick="wsf_review_nag_dismiss();">No thanks</a></p>', 'ws-form'), WS_FORM_NAME_PRESENTABLE), 'notice-success', false, false, 'wsf-review');
?>
<script>

	function wsf_review_nag_dismiss() {

		(function($) {

			'use strict';

			// Hide nag
			$('.wsf-review').hide();

			// Call AJAX to prevent review nag appearing again
			$.ajax({ method: 'POST', url: '<?php esc_html_e(self::get_api_path('helper/review-nag/dismiss/')); ?>', data: { '_wpnonce': '<?php esc_attr_e(wp_create_nonce('wp_rest')); ?>' } });

		})(jQuery);
	}

</script>
<?php
		}

		// Check edition
		public static function is_edition($edition) {

			switch($edition) {

				case 'basic' :

					return true;

				case 'pro' :

					return true;

				default :

					return false;
			}
		}

		// Build data grid meta
		public static function build_data_grid_meta($meta_key, $group_name = false, $columns = false, $rows = false) {

			// Get base meta
			$meta_keys = WS_Form_Config::get_meta_keys_data_grids();

			if(
				!isset($meta_keys[$meta_key]) ||
				!isset($meta_keys[$meta_key]['default'])
			) {
				return false;
			}

			$meta = $meta_keys[$meta_key]['default'];

			if($group_name !== false) { $meta['groups'][0]['label'] = $group_name; }
			if($columns !== false) { $meta['columns'] = $columns; }
			if($rows !== false) { $meta['groups'][0]['rows'] = $rows; }

			return $meta;
		}

		// Get nice file size
		public static function get_file_size($bytes) {

			if($bytes >= 1048576) {

				$bytes = number_format($bytes / 1048576, 2) . ' MB';

			} elseif ($bytes >= 1024) {

				$bytes = number_format($bytes / 1024, 2) . ' KB';

			} elseif ($bytes > 1) {

				$bytes = $bytes . ' bytes';

			} elseif ($bytes == 1) {

				$bytes = $bytes . ' byte';

			} else {

				$bytes = '0 bytes';
			}

			$bytes = str_replace('.00', '', $bytes);

			return $bytes;
		}

		// PHP to MySQL Date Format
		public static function php_to_mysql_date_format($format_string) {

			// Cannot convert: N, z, t, L, o
			$php_to_mysql_date_format_character_array = array(

				'd' => '%d',
				'a' => '%p',
				'D' => '%a',
				'j' => '%e',
				'u' => '%f',
				'W' => '%u',
				'l' => '%W',
				'w' => '%w',
				'M' => '%b',
				'F' => '%M',
				'm' => '%m',
				'n' => '%c',
				'Y' => '%Y',
				'y' => '%y',
				'A' => '%p',
				'g' => '%l',
				'G' => '%k',
				'h' => '%h',
				'H' => '%H',
				'i' => '%i',
				's' => '%S',
			);

			foreach($php_to_mysql_date_format_character_array as $from => $to) {

				$format_string = str_replace($from, $to, $format_string);
			}

			return $format_string;
		}

		// Get m/d/Y formatted date
		public static function get_date_by_site($date) {

			$format_date = get_option('date_format');

			switch($format_date) {

				case 'd/m/Y' :

					$date = str_replace('/', '.', $date);
					break;
			}

			// Strip commas
			$date = str_replace(',', '', $date);

			return $date;
		}

		// Get nice date by type
		public static function get_date_by_type($date, $field_object, $format_output = false) {

			if(empty($date)) { return ''; }
			if(!is_string($date)) { return ''; }

			$format_date = self::get_object_meta_value($field_object, 'format_date', get_option('date_format'));
			if(empty($format_date)) { $format_date = get_option('date_format'); }
			$format_time = self::get_object_meta_value($field_object, 'format_time', get_option('time_format'));
			if(empty($format_time)) { $format_time = get_option('time_format'); }

			switch($format_date) {

				case 'd/m/Y' :

					// Convert / to - so that strtotime works with d/m/Y format
					$date = str_replace('/', '-', $date);
					break;

				case 'd.m.Y' :

					// Convert / to - so that strtotime works with d/m/Y format
					$date = str_replace('.', '-', $date);
					break;
			}

			if($format_output === false) {

				$input_type_datetime = self::get_object_meta_value($field_object, 'input_type_datetime', 'date');

				switch($input_type_datetime) {

					case 'date' :

						return date($format_date, strtotime($date));

					case 'month' :

						return date('F Y', strtotime($date));

					case 'time' :

						return date($format_time, strtotime($date));

					case 'week' :

						return __('Week', 'ws-form') . ' ' . date('W, Y', strtotime($date));

					default :

						return date($format_date . ' ' . $format_time, strtotime($date));
				}

			} else {

				return date($format_output, strtotime($date));
			}
		}

		// Throw error
		public static function throw_error($error) {
			
			throw new Exception($error);
		}

		// Get system report
		public static function get_system_report_html() {

			// Get system report
			$system_report = WS_Form_Config::get_system();

			// Build system report HTML
			$system_report_html = '<table class="wsf-table-system">';

			foreach($system_report as $group_id => $group) {

				$system_report_html .= '<tbody>';

				$system_report_html .= '<tr><th colspan="2"><h2>' . htmlentities($group['label']) . '</h2></th></tr>';

				foreach($group['variables'] as $item_id => $item) {

					// Valid
					// 0 = Ignore, 1 = Yes, 2 = No
					$valid = isset($item['valid']) ? ($item['valid'] ? 1 : 2) : 0;

					$system_report_html .= '<tr';

					switch($valid) {

						case 1 : $system_report_html .= ' class="wsf-system-valid"'; break;
						case 2 : $system_report_html .= ' class="wsf-system-invalid"'; break;
					}

					// Label
					$system_report_html .= '><td><b>' . htmlentities($item['label']);
					if(isset($item['min'])) { $system_report_html .= ' (Min: ' . $item['min'] . ')'; }
					$system_report_html .= '</b></td>';

					// Value
					$system_report_html .= '<td>';

					$value = isset($item['value']) ? $item['value'] : '-';
					$type = isset($item['type']) ? $item['type'] : 'text';

					switch($type) {

						case 'plugins' :

							if(is_array($value)) {

								$plugin_array = array();

								foreach($value as $plugin_path) {

									$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
									$plugin_array[] = sprintf('<a href="%s" target="_blank">%s</a> (%s)', $plugin_data['PluginURI'], $plugin_data['Name'], $plugin_data['Version']);
								}

								$value = implode('<br />', $plugin_array);
							}
							break;

						case 'theme' :

							if(is_object($value)) {

								$value = sprintf('<a href="%s" target="_blank">%s</a> (%s)', $value->get('ThemeURI'), $value->get('Name'), $value->get('Version'));
							}
							break;

						case 'boolean' :

							$value = $value ? __('Yes', 'ws-form') : __('No', 'ws-form');
							break;

						case 'url' :

							$value = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $value);
							break;

						case 'size' :

							$value = size_format($value);
							break;

						case 'edition' :

							switch($value) {

								case 'basic' : $value = 'Basic'; break;
								case 'pro' : $value = 'PRO'; break;
							}
							break;

						case 'date' :

							$value = ($value != '') ? date(get_option('date_format'), $value) : '-';
							break;
					}

					$system_report_html .= $value;

					// Suffix
					if(isset($item['suffix'])) { $system_report_html .= ' ' . $item['suffix']; }

					// Valid
					switch($valid) {

						case 1 : $system_report_html .= WS_Form_Config::get_icon_16_svg('check'); break;
						case 2 : $system_report_html .= WS_Form_Config::get_icon_16_svg('warning'); break;
					}

					$system_report_html .= '</td></tr>';
				}

				$system_report_html .= '</tbody>';
			}

			$system_report_html .= '</table>';

			return $system_report_html;
		}

		// Get a string formatted for SMTP email addresses
		public static function get_email_address($email, $name = '') {

			// Ensure email is valid
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { return false; }

			// Check length
			if(strlen($name) > 255) { $name = substr($name, 0, 255); }

			if(strpos($name, '"') !== false) {

				// Escape double quotes in name
				$name = str_replace('"', '\"', $name);

				// Wrap in double quotes
				$name = '"' . $name . '"';
			}

			// Return full email address
			return ($name != '') ? sprintf('%s <%s>', $name, $email) : $email;
		}

		// Get encryption status HTML
		public static function get_encryption_status_html() {

			$ws_form_encryption = new WS_Form_Encryption();
			if($ws_form_encryption->key_found === false) {			

				/* translators: %s = wp-config.php */
				$encryption_status_html = '<p>' . sprintf(__('Copy and paste the following code into your %s file after the Authentication Unique Keys and Salts section.', 'ws-form'), '<strong>wp-config.php</strong>') . '</p>';

				$encryption_key = htmlentities($ws_form_encryption->create_random_key());
				$encryption_status_html .= "<textarea class=\"wsf-field wsf-encryption-snippet\" rows=\"6\" readonly>/** Encryption Key For WS Form */\ndefine('WS_FORM_ENCRYPTION_KEY', '$encryption_key');</textarea>";

				$encryption_status_html .= sprintf('<p><strong style="color: #bb0000;">%s:</strong> %s</p>', __('Caution'), __('Once installed, do not change this key. Changing your encryption key at a later date will make it impossible to decrypt existing submission data. THIS KEY IS ONLY GENERATED ONCE. PLEASE ENSURE YOU DO NOT LOSE THIS KEY AND KEEP IT IN A SECURE PLACE. ENCRYPTED DATA AND KEYS CANNOT BE RECOVERED IF THE KEY IS LOST.'));

			} else {

				/* translators: %s = wp-config.php */
				$encryption_status_html = '<p>' . sprintf(__('Key successfully found in %s', 'ws-form'), '<strong>wp-config.php</strong>') . '</p>';

				if($ws_form_encryption->can_encrypt) {

					$encryption_status_html .= '<p><strong>' . __('Encrypting submission meta data.', 'ws-form') . '</strong></p>';

				} else {

					$encryption_status_html .= '<p><strong>' . __('Submission meta data is not being encrypted.', 'ws-form') . '</strong></p>';
				}
			}

			return $encryption_status_html;
		}

		// Get preview URL
		public static function get_preview_url($form_id, $skin_id = 'ws_form', $conversational = false) {

			return get_site_url(null, sprintf('/?wsf_preview%s_form_id=%u&wsf_skin_id=%s&wsf_rand=%s', ($conversational ? '_conversational' : ''), $form_id, $skin_id, wp_generate_password(12, false, false)));
		}

		// Get obscure license key
		public static function get_license_key_obscured() {

			// License key
			$license_key = WS_Form_Common::option_get('license_key', '');
			$license_key_length = strlen($license_key);
			$license_key_obscured = ($license_key_length > 6) ? (str_repeat('*', $license_key_length - 6) . substr($license_key, -6)) : '';

			return ($license_key != '') ? $license_key_obscured : __('Unlicensed', 'ws-form');
		}

		// Get currency
		public static function get_currency() {

			$return_array = array();

			$price_decimals = absint(self::option_get('price_decimals'));
			$currency_position = self::option_get('currency_position');

			// Get currency symbol
			$currencies = WS_Form_Config::get_currencies();
			$currency = self::option_get('currency', 'USD');
			$currency_found = isset($currencies[$currency]) && isset($currencies[$currency]['s']);
			$currency_symbol = $currency_found ? $currencies[$currency]['s'] : '$';

			$return_array['prefix'] = $currency_symbol;
			$return_array['suffix'] = '';

			switch($currency_position) {

				case 'right' :

					$return_array['prefix'] = '';
					$return_array['suffix'] = $currency_symbol;
					break;

				case 'left_space' :

					$return_array['prefix'] = $currency_symbol . ' ';
					break;

				case 'right_space' :

					$return_array['prefix'] = '';
					$return_array['suffix'] = ' ' . $currency_symbol;
					break;
			}

			// Price decimals
			$return_array['decimals'] = ($price_decimals < 0) ? 0 : $price_decimals;

			// Separators
			$return_array['decimal_separator'] = self::option_get('price_decimal_separator');
			$return_array['thousand_separator'] = self::option_get('price_thousand_separator');

			return $return_array;
		}

		public static function get_price($price_float, $currency = false, $currency_symbol_render = true) {

			if($currency === false) { $currency = self::get_currency(); }

			if(!is_numeric($price_float)) { $price_float = floatVal($price_float); }

			return ($currency_symbol_render ? $currency['prefix'] : '') . number_format($price_float, absint($currency['decimals']), $currency['decimal_separator'], $currency['thousand_separator']) . ($currency_symbol_render ? $currency['suffix'] : '');
		}

		public static function get_currency_default() {

			return apply_filters('wsf_currency_default', 'USD');
		}

		public static function get_admin_icon($color = '#a0a5aa', $base64 = true) {

			$svg = sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><path fill="%s" d="M0 0v400h400V0H0zm336.6 118.9c6.7-.1 13.4 5.4 13.4 13.6-.1 7.4-5.9 13.4-13.4 13.4-8.1 0-13.6-6.7-13.5-13.7 0-7.3 6.1-13.5 13.5-13.3zm-124.4 6.5c-12 48.8-24 97.6-36.1 146.3 0 .2-.2.2-.2.4-.8.1-6 .2-10.4.2h-1.9c-2.1 0-3.7-.1-4.1-.1-.2-.2-.3-.4-.3-.6-.1-.2-.1-.5-.2-.7-1.5-6.6-2.9-13.2-4.4-19.8-2.8-12.2-5.5-24.4-8.2-36.6-2.3-10.2-4.5-20.4-6.8-30.6-.9-4.2-1.9-8.3-2.8-12.5-.6-3-1.1-6.1-1.7-9.1-1-5.5-2-11.2-3-16.7-.1-.4-.2-.8-.3-1.5-.2.6-.3.8-.4 1.1-1.5 9-3.4 17.9-5.2 26.8-.9 4.7-2.1 9.5-3.2 14.2-2.8 12.3-5.7 24.6-8.5 36.9-3.6 15.8-7.3 31.6-11 47.5-.1.5-.3 1-.4 1.5-.2.1-.5.2-.6.2H86.7c-.6-.3-.6-.8-.7-1.2-1.2-4.8-2.3-9.6-3.5-14.5-3.6-15.4-7.4-30.9-11-46.4-3.9-16.4-7.8-32.7-11.7-49.1-2.8-11.5-5.5-22.9-8.1-34.3-.2-.7-.5-1.4-.4-2.1 1.2-.2 11.9-.3 14-.1.1.3.2.7.4 1.1 2.4 10 4.7 19.9 7 29.9 2.5 10.7 5 21.4 7.5 32 1.8 7.8 3.7 15.6 5.5 23.3 1.7 7.4 3.2 14.8 4.7 22.2 1.4 6.8 2.8 13.7 4.3 20.5.1.5.2.9.4 1.3.1.2.3.2.6.3.2-.6.3-1.1.4-1.7 1.8-13.1 4.4-25.9 7.4-38.8 4.3-18.6 8.7-37.2 13.1-55.8l7.8-33.3c.1-.5.2-.9.4-1.4 1.7-.1 3.3 0 5-.1h5c1.7 0 3.3-.1 5 .1.2.6.3 1.2.5 1.7 3.8 16.1 7.6 32.2 11.4 48.2 3 12.7 6 25.3 8.9 38 1.3 5.5 2.3 11.2 3.4 16.8 1.3 6.9 2.5 13.8 3.7 20.6.3 1.7.6 3.2.9 4.9.1.3.2.6.7.6l.3-1.2c.8-5.1 1.8-10 2.9-15 3.9-18.1 8.2-36.1 12.4-54.3 3.6-15.2 7.1-30.4 10.7-45.6 1-4.5 2.1-9 3.2-13.5.1-.3.2-.7.4-1.1 1.2-.2 2.4-.1 3.5-.1h6.8c1.2 0 2.4-.2 3.6.2-.8.5-.8.7-.9 1zm86.3 124.5c-3.6 11.5-11.3 19.1-22.8 22.8-3.2 1-6.6 1.7-10 2-5.2.6-10.4.5-15.7-.1-7-.7-13.8-2.7-20.2-5.9-1.2-.6-2.4-1.2-3.5-2.1.6-1.6 5.4-9.7 6.2-10.7.3.2.6.2.9.5 1.7 1.1 3.5 1.8 5.4 2.5 3.2 1.1 6.3 2.1 9.7 2.8 5.1.9 10.1 1.3 15.3.8 12.9-1.4 19.2-10 21.3-18 1.5-5.6 1.6-11.3.2-16.9-.9-3.9-2.8-7.3-5.4-10.2-1.8-2-3.8-3.8-5.9-5.4-4.2-3.3-8.7-6.2-13.3-8.9-4.7-2.8-9.3-5.5-13.8-8.6-2.9-2.1-5.9-4.1-8.4-6.6-3.7-3.6-6.7-7.7-8.9-12.4-1.8-3.9-2.8-8.1-3.2-12.3-.2-2.5-.2-5.1-.1-7.7.6-8.8 4.2-16.2 10.6-22.3 5.9-5.5 12.8-8.9 20.6-10.4 5.3-1 10.7-1.2 16.1-.7 7.4.6 14.5 2.5 21.1 5.9 1.3.7 2.6 1.4 3.9 2.2.3.2.6.5.9.6-.5 1.3-3.8 7.1-5.7 10.1-.2.2-.3.4-.6.6l-1.2-.6c-5.4-3.1-11.1-5.3-17.2-6.2-5.6-.9-11.2-.9-16.7.5-2.8.7-5.4 1.7-7.8 3.3-5.9 3.9-9.3 9.3-10.2 16.2-.6 4.5-.2 8.9 1.2 13.3 1.1 3.5 3 6.5 5.5 9 2.6 2.6 5.5 4.8 8.5 7 3.3 2.3 6.8 4.4 10.3 6.5 5.8 3.4 11.5 7 16.8 11 2.3 1.7 4.6 3.6 6.6 5.6 5.9 5.9 9.5 13 10.6 21.2 1.4 7.2 1.1 14.5-1.1 21.6zm38 26.4c-7.5.1-13.5-6.2-13.5-13.5 0-6.7 5.4-13.5 13.5-13.4 7.4 0 13.4 5.9 13.4 13.4.1 8-6.5 13.6-13.4 13.5zm-.1-64.8c-7.9-.1-13.4-6.6-13.4-13.4 0-7.7 6.4-13.7 13.5-13.5 6.4-.2 13.4 5.1 13.5 13.5 0 7.4-6.1 13.5-13.6 13.4z"></path></svg>', $color);

			return $base64 ? 'data:image/svg+xml;base64,' . base64_encode($svg) : $svg;
		}

		// Load content via AJAX
		public static function ajax_load($url, $id = 'wsf-settings-content') {

			// Build action product ID's
			$action_license_item_id_array = array();

			foreach(get_declared_classes() as $class){

				if(strpos($class, 'WS_Form_Action_') === false) { continue; }
				if(!is_subclass_of($class, 'WS_Form_Action')) { continue; }

				$action = New $class();

				if(method_exists($action, 'get_license_item_id')) {

					$action_license_item_id_array[] = $action->get_license_item_id();
				}
			}

			$action_license_item_ids = implode(',', $action_license_item_id_array);

			$url_variables = array(

				'locale' 					=> rawurlencode(get_locale()),
				'version'					=> rawurlencode(WS_FORM_VERSION),
				'action_license_item_ids'	=> rawurlencode($action_license_item_ids)
			);

			$url = self::mask_parse($url, $url_variables);

			echo '<div id="wsf-settings-content"><script>(function($) {\'use strict\';$(\'#' . esc_html($id) . '\').load(\'' .  esc_html($url) . '\', function(response, status, xhr) { if(status == \'error\') { $(\'#' . esc_html($id) . '\').html(\'' . sprintf(__('<a href="%s" target="_blank">%s</a>', 'ws-form'), esc_html(self::get_plugin_website_url('', 'settings')), sprintf(

					/* translators: %s = WS Form */
					__('Click here to learn more about %s', 'ws-form'),

					WS_FORM_NAME_GENERIC

				)) . '\'); }});})(jQuery);</script></div>';
		}

		// Get root post
		public static function get_post_root() {

			// Load post (This uses the post ID set before any of the page renders)
			$post = (isset($GLOBALS) && isset($GLOBALS['ws_form_post_root'])) ? $GLOBALS['ws_form_post_root'] : null;

			// Load post by query string (Used by actions when a form is submitted)
			if(is_null($post)) {

				$post_id = absint(self::get_query_var('wsf_post_id', 0));
				if($post_id == 0) { $post_id = absint(self::get_query_var('post_id', 0)); }
				$post = ($post_id > 0) ? get_post($post_id) : null;
				$GLOBALS['ws_form_post_root'] = $post;
			}

			return $post;
		}

		// Get user
		public static function get_user() {

			// Load user
			$user = (isset($GLOBALS) && isset($GLOBALS['ws_form_user'])) ? $GLOBALS['ws_form_user'] : false;

			// Load user by current_user
			if(
				($user === false) &&
				function_exists('wp_get_current_user')
			) {

				$user = wp_get_current_user();
			}

			return $user;
		}

		// Get editable user roles
		public function get_editable_roles() {

			global $wp_roles;

			$all_roles = $wp_roles->roles;
			$editable_roles = apply_filters('editable_roles', $all_roles);

			return $editable_roles;
		}

		// Is block editor on page?
		public static function is_block_editor() {

			if(!function_exists('get_current_screen')) { return false; }
			if(!is_object(get_current_screen())) { return false; }
			if(!method_exists(get_current_screen(), 'is_block_editor')) { return false; }

			return get_current_screen()->is_block_editor();
		}

		// Is this a REST request
		public static function is_rest_request() {

			return (defined('REST_REQUEST') && REST_REQUEST);
		}

		// Is this customize preview?
		public static function is_customize_preview() {

			return (self::get_query_var('customize_theme') != '');
		}

		// do_shortcode
		public static function do_shortcode($input) {

			// Get shortcode regex
			$shortcode_regex = get_shortcode_regex();

			// If there are shortcodes in the input
			if(
				preg_match_all('/'. $shortcode_regex .'/s', $input, $matches) &&
				array_key_exists(2, $matches) &&
				!in_array(WS_FORM_SHORTCODE, $matches[2])
			) {

				// Run do_shortcode
				$input = do_shortcode($input);
			}

			return $input;
		}

		// Convert a CSV file to a meta value containing a new data grid
		public static function csv_file_to_data_grid_meta_value($file, $meta_key, $meta_value) {

			// Ensure meta value is an object
			$meta_value = json_decode(json_encode($meta_value));

			// Auto detect line endings
			ini_set("auto_detect_line_endings", true);

			// Read file data
			$file_name = $file['name'];
			$file_type = $file['type'];
			$file_tmp_name = $file['tmp_name'];
			$file_error = $file['error'];
			$file_size = $file['size'];
			$file_string = isset($file['string']) ? $file['string'] : file_get_contents($file_tmp_name);

			// Error
			if($file_error !== 0) { self::throw_error(__('File upload error', 'ws-form') . ': ' . $file_error); }

			// Check file extension
			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			if($ext !== 'csv') { self::throw_error(__('Unsupported file extension', 'ws-form') . ': ' . $ext); }

			// Determine character encoding
			$first2 = substr($file_string, 0, 2);
			$first3 = substr($file_string, 0, 3);
			$first4 = substr($file_string, 0, 3);

			$char_encoding = false;
			if($first3 == WS_FORM_UTF8_BOM) $char_encoding =  'UTF-8';
			elseif($first4 == WS_FORM_UTF32_BIG_ENDIAN_BOM) $char_encoding =  'UTF-32BE';
			elseif($first4 == WS_FORM_UTF32_LITTLE_ENDIAN_BOM) $char_encoding =  'UTF-32LE';
			elseif($first2 == WS_FORM_UTF16_BIG_ENDIAN_BOM) $char_encoding =  'UTF-16BE';
			elseif($first2 == WS_FORM_UTF16_LITTLE_ENDIAN_BOM) $char_encoding =  'UTF-16LE';

			// Convert string
			if($char_encoding) {

				$file_string = mb_convert_encoding($file_string, 'UTF-8', $char_encoding);
			}

			// Load CSV data as file pointer
			$fp = fopen("php://temp", 'r+');
			fputs($fp, $file_string);
			rewind($fp);

			// Read header
			$columns = fgetcsv($fp);
			if($columns === false) { self::throw_error(__('Unable to read header row of file', 'ws-form')); }
			if(is_null($columns)) { self::throw_error(__('Unable to read header row of file', 'ws-form')); }
			if(count($columns) == 0) { self::throw_error(__('No columns to process', 'ws-form')); }

			// Set group_index to 0 (Select first tab)
			$meta_value->group_index = 0;

			// Build columns
			$columns_new = array();
			$column_key_id = -1;
			$column_key_wsf_id = -1;
			$column_key_wsf_default = -1;
			$column_key_wsf_required = -1;
			$column_key_wsf_disabled = -1;
			$column_key_wsf_hidden = -1;

			$column_index = 0;
			foreach($columns as $key => $column) {

				switch(strtolower($column)) {

					case 'wsf_id' :

						$column_key_wsf_id = $key;
						break;

					case 'wsf_default' :

						$column_key_wsf_default = $key;
						break;

					case 'wsf_required' :

						$column_key_wsf_required = $key;
						break;

					case 'wsf_disabled' :

						$column_key_wsf_disabled = $key;
						break;

					case 'wsf_hidden' :

						$column_key_wsf_hidden = $key;
						break;

					case 'id' :
						$column_key_id = $key;

					default:

						$columns_new[] = (object) array(

							'id' => $column_index,
							'label' => $column
						);
						$column_index++;
				}
			}
			$meta_value->columns = $columns_new;

			// Get default group configuration
			$meta_keys = WS_Form_Config::get_meta_keys(0, false);
			if(!isset($meta_keys[$meta_key])) { self::throw_error(__('Unknown meta key', 'ws-form') + ': ' + $meta_key); }
			if(!isset($meta_keys[$meta_key]['default'])) { self::throw_error(__('Default not found', 'ws-form') + ': ' + $meta_key); }
			if(!isset($meta_keys[$meta_key]['default']['groups'])) { self::throw_error(__('Groups not found', 'ws-form') + ': ' + $meta_key); }
			if(!isset($meta_keys[$meta_key]['default']['groups'][0])) { self::throw_error(__('Group[0] not found', 'ws-form') + ': ' + $meta_key); }

			$group = json_decode(json_encode($meta_keys[$meta_key]['default']['groups'][0]));

			// Re-process array to match required format for data grid
			$id_array = [];
			$key = 0;
			while($row = fgetcsv($fp)) {

				if(($row === false) || is_null($row)) { continue; }

				// UTF-8 encode the row
				$row_id = -1;
				$column_index = 0;
				$data = [];
				$default = '';
				$required = '';
				$disabled = '';
				$hidden = '';

				foreach($row as $column_key => $field) {

					if(($row === false) || is_null($field)) { continue 2; }

					$field_lower = strtolower($field);

					switch($column_key) {

						case $column_key_wsf_id:

							$row_id = absint($field_lower);
							if($row_id > 0) { $id = $row_id; }
							break;

						case $column_key_wsf_default:

							$default = ($field_lower != '') ? 'on' : '';
							break;

						case $column_key_wsf_required :

							$required = ($field_lower != '') ? 'on' : '';
							break;

						case $column_key_wsf_disabled :

							$disabled = ($field_lower != '') ? 'on' : '';
							break;

						case $column_key_wsf_hidden :

							$hidden = ($field_lower != '') ? 'on' : '';
							break;

						case $column_key_id :

							$row_id = is_numeric($field_lower) ? absint($field_lower) : -1;
							if($row_id > 0) { $id = $row_id; }

						default :

							$data[$column_index] = $field;
							$column_index++;
					}
				}

				// ID row not found
				if($row_id == -1) {

					$max_id = 0;
					foreach($id_array as $id) {

						if($id > $max_id) { $max_id = $id; }
					}
					$id = $max_id + 1;

					// Add ID to ID array
					$id_array[] = $id;
				}

				// Build row
				$array[$key] = (object) array(

					'id'		=> $id,
					'default'	=> $default,
					'disabled'	=> $disabled,
					'required'	=> $required,
					'hidden'	=> $hidden,
					'data'		=> $data
				);

				$key++;
			}

			// Build group label
			if(isset($file['group_label'])) {

				$group_label = $file['group_label'];

			} else {

				$group_label = strtolower($file_name);
				$group_label = str_replace('_', ' ', $group_label);
				$group_label = str_replace('-', ' ', $group_label);
				$group_label = str_replace('.csv', '', $group_label);
				$group_label = ucwords($group_label);
			}

			// Build group
			$group->label = $group_label;
			$group->page = 0;
			$group->rows = $array;

			// Add to meta value
			$meta_value->groups = array($group);
			$meta_value->group_index = 0;

			return $meta_value;
		}

		// Get key value array (Used by third party visual builders)
		public static function get_forms_array($placeholder = true) {

			// Build form list
			$ws_form_form = new WS_Form_Form();
			$forms = $ws_form_form->db_read_all('', "NOT (status = 'trash')", 'label ASC, id ASC', '', '', false);
			$form_array = $placeholder ? array('' => __('Select form...', 'ws-form')) : array();

			if($forms) {

				foreach($forms as $form) {

					/* translators: %s: Name of the form, %u: ID of the form */
					$form_array[$form['id']] = esc_html(sprintf(__('%s (ID: %u)', 'ws-form'), esc_html($form['label']), $form['id']));
				}
			}

			return $form_array;
		}

		// Check form ID
		public static function check_form_id($form_id) {

			if(
				(absint($form_id) === 0)
			) {
				self::throw_error(__('Invalid form ID', 'ws-form'));
				die();
			}

			return true;
		}

		// Check submit hash
		public static function check_submit_hash($hash) {

			if(
				($hash == '') ||
				!preg_match('/^[a-f0-9]{32}$/i', $hash)
			) {

				return false;
			}

			return true;
		}

		// Get cookie raw
		public static function cookie_get_raw($cookie_name, $default_value = '') {

			if(
				($cookie_name === '') ||
				!isset($_COOKIE) ||
				!isset($_COOKIE[$cookie_name])
			) {
				return $default_value;
			}

			return $_COOKIE[$cookie_name];
		}

		// Get form_object from POST $_FILE
		public static function get_form_object_from_post_file() {

			// Get files
			if(!isset($_FILES)) { self::throw_error(__('No files found', 'ws-form')); }
			if(!isset($_FILES['file'])) { self::throw_error(__('No files found', 'ws-form')); }

			// Run through files
			$file = $_FILES['file'];

			// Read file data
			$file_name = $file['name'];
			$file_type = $file['type'];
			$file_tmp_name = $file['tmp_name'];
			$file_error = $file['error'];
			$file_size = $file['size'];

			// Error checking
			if($file_error != 0) { self::throw_error(__('File upload error', 'ws-form') . ': ' . $file_error); }
			if($file_size == 0) { self::throw_error(__('File empty', 'ws-form')); }

			// Check file extension
			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			if($ext !== 'json') { self::throw_error(sprintf(__('Unsupported file extension: %s', 'ws-form') , $ext)); }

			// Check file format
			if(!file_exists($file_tmp_name)) { self::throw_error(__('Unable to read uploaded file', 'ws-form')); }
			$form_json = file_get_contents($file_tmp_name);

			// Get form object from JSON
			$form_object = self::get_form_object_from_json($form_json, true);

			return $form_object;
		}

		// Get form object from JSON and check JSON is valid for a form
		public static function get_form_object_from_json($form_json, $checksum_check = false) {

			// Check form JSON format
			$form_object = json_decode($form_json);
			if(is_null($form_object)) { self::throw_error(__('JSON corrupt', 'ws-form')); }
			if(!is_object($form_object)) { self::throw_error(__('JSON corrupt (Not object)', 'ws-form')); }

			// Checksum test
			if(
				$checksum_check &&
				!self::form_object_checksum_check($form_object)
			) {

				self::throw_error(__('JSON corrupt (Checksum error)', 'ws-form'));
			}

			// Check identifier
			if(
				!isset($form_object->identifier) ||
				($form_object->identifier !== WS_FORM_IDENTIFIER)
			) {

				self::throw_error(sprintf(

					/* translators: %s = WS Form */
					__('JSON corrupt (Not a %s JSON file)', 'ws-form'),

					WS_FORM_NAME_GENERIC
				));
			}

			// Check label
			if(!isset($form_object->label)) { self::throw_error(__('JSON corrupt (No label)', 'ws-form')); }

			// Check meta
			if(!isset($form_object->meta)) { self::throw_error(__('JSON corrupt (No meta data)', 'ws-form')); }

			return $form_object;
		}

		// Form object checksum check
		public static function form_object_checksum_check($form_object) {

			$return_value = false;

			$checksum = $form_object->checksum;
			$form_object_checksum_check = clone $form_object;
			unset($form_object_checksum_check->checksum);
			$checksum_file = md5(json_encode($form_object_checksum_check));
			if($checksum == $checksum_file) { $return_value = true; }
			$form_object_checksum_check = null;

			return $return_value;
		}

		// Get meta value array
		public static function get_meta_value_array($field_id, $fields, $field_types) {

			$meta_value_array = array();

			// Get field
			$field = $fields[$field_id];

			// Get field type config
			$field_type = $field->type;
			if(!isset($field_types[$field_type])) { return array(); }
			$field_type_config = $field_types[$field_type];

			// If has data grid data source
			if(
				isset($field_type_config['datagrid_column_value']) &&
				isset($field_type_config['data_source']) &&
				($field_type_config['data_source']['type'] == 'data_grid')
			) {

				// Get data source
				$data_source_id = $field_type_config['data_source']['id'];
				$data_source = WS_Form_Common::get_object_meta_value($field, $data_source_id, 0);

				// Get columns
				if(!isset($data_source->columns)) { return array(); }
				$columns = $data_source->columns;

				// Get data grid column ID
				$datagrid_column_value = $field_type_config['datagrid_column_value'];
				$datagrid_column_id = WS_Form_Common::get_object_meta_value($field, $datagrid_column_value, 0);

				// Get data grid column index
				$data_grid_column_index = false;
				foreach($columns as $column_index => $column) {

					if($column->id == $datagrid_column_id) {

						$data_grid_column_index = $column_index;
						break;
					}
				}
				if($data_grid_column_index === false) { return array(); }

				// Get data
				if(
					isset($data_source->groups) &&
					isset($data_source->groups[0]) &&
					isset($data_source->groups[0]->rows)
				) {

					foreach($data_source->groups[0]->rows as $row) {

						if(
							isset($row->data) &&
							isset($row->data[$data_grid_column_index])
						) {

							$meta_value_array[] = $row->data[$data_grid_column_index];
						}
					}
				}
			}

			return $meta_value_array;
		}

		// Inject array at index
		public static function array_inject_element($array_old, $element, $index) {

			$array_new = array_slice($array_old, 0, $index, true);
			$array_new[] = $element;
			$array_new = array_merge($array_new, array_slice($array_old, $index, NULL, true));

			return $array_new;
		}

		// Get uploads base directory and ensure paths are https (Known bug with wp_upload_dir)
		public static function get_upload_dir_base_url() {

			$upload_dir_base_url = wp_upload_dir()['baseurl'];
			if(is_ssl()) {

				$upload_dir_base_url = str_replace('http://', 'https://', $upload_dir_base_url);
			}

			return $upload_dir_base_url;
		}

		// File name parse
		public static function file_name_parse($form_object, $submit_object, $field, $file_name) {

			// Parse file_name
			$file_name_mask = WS_Form_Common::get_object_meta_value($field, 'file_name_mask', '');
			$file_name_mask = trim($file_name_mask);

			if($file_name_mask !== '') {

				// Split file name into parts
				$file_name_parts = pathinfo($file_name);

				// If split successful, parse file name
				if(is_array($file_name_parts)) {

					$file_name_values = array(

						'file_basename' => isset($file_name_parts['basename']) ? $file_name_parts['basename'] : '',
						'file_filename' => isset($file_name_parts['filename']) ? $file_name_parts['filename'] : '',
						'file_extension' => isset($file_name_parts['extension']) ? $file_name_parts['extension'] : ''
					);

					$file_name = WS_Form_Common::mask_parse($file_name_mask, $file_name_values);
				}

				// Regular parse
				$file_name = WS_Form_Common::parse_variables_process($file_name, $form_object, $submit_object, 'text/plain');
			}

			return $file_name;
		}

		// Wordress version compare
		public static function wp_version_at_least($ver) {

			global $wp_version;

			return (self::version_compare($wp_version, $ver) >= 0);
		}

		// Version compare
		public static function version_compare($ver_a, $ver_b) {

			$ver_a_parts = explode('-', $ver_a);
			$ver_b_parts = explode('-', $ver_b);

			$semver_a = array_shift($ver_a_parts);
			$semver_b = array_shift($ver_b_parts);
			$comparison = version_compare($semver_a, $semver_b);

			if(0 !== $comparison) {
				return $comparison;
			}

			$prerelease_a = array_shift($ver_a_parts);
			$prerelease_b = array_shift($ver_b_parts);
			$comparison = strcmp(is_string($prerelease_a) ? $prerelease_a : '', is_string($prerelease_b) ? $prerelease_b : '');

			if(0 !== $comparison) {
				return $comparison;
			}

			$rev_a = absint(array_shift($ver_a_parts));
			$rev_b = absint(array_shift($ver_b_parts));

			$src_a = 'src' === array_shift($ver_a_parts);
			$src_b = 'src' === array_shift($ver_b_parts);

			if($src_a xor $src_b) {
				return 0;
			}

			// Compare revision numbers.
			return $rev_b - $rev_a;
		}

		// Toolbar enabled
		public static function toolbar_enabled() {

			return (

				!WS_Form_Common::option_get('disable_toolbar_menu') &&
				WS_Form_Common::can_user('edit_form') &&
				WS_Form_Common::can_user('read_submission')
			);
		}

		// Toolbar CSS
		public static function toolbar_css() {

			if(!self::toolbar_enabled()) { return; }

			if(!is_admin_bar_showing()) { return; }
?>
<style>

	#wp-admin-bar-ws-form-node .ab-icon {
		
		width: 20px !important;
		height: 30px !important;
	}

	#wp-admin-bar-ws-form-node .ab-icon svg {

		margin-top: 2px;
		width: 20px;
	}

	#wp-admin-bar-ws-form-node:hover .ab-icon svg path,
	#wp-admin-bar-ws-form-node.hover .ab-icon svg path {

		fill: #00b9eb;
	}

	@media screen and ( max-width: 782px ) {

		#wpadminbar #wp-admin-bar-ws-form-node .ab-icon svg {

			margin: 9px 12px 0 12px;
			height: 28px !important;
			width: 28px !important;
			display: block;
		}

		#wpadminbar li#wp-admin-bar-ws-form-node {

			width: 52px;
			display: block;
		}
	}

</style>
<?php
		}
	}
