<?php

	abstract class WS_Form_File_Handler extends WS_Form_Core {

		// Variables global to this abstract class
		public static $file_handlers = array();
		private static $return_array = array();

		// Register data source
		public function register($object) {

			// Check if pro required for data source
			if(!WS_Form_Common::is_edition($this->pro_required ? 'pro' : 'basic')) { return false; }

			// Get data source ID
			$file_handler_id = $this->id;

			// Add action to actions array
			self::$file_handlers[$file_handler_id] = $object;

			// Add meta keys to file field type
			if(method_exists($object, 'get_file_handler_settings')) {

				$settings = $object->get_file_handler_settings();

				if(isset($settings['meta_keys'])) {

					add_filter('wsf_config_field_types', function($field_types) {

						$object = self::$file_handlers[$this->id];
						$settings = $object->get_file_handler_settings();
						$meta_keys = $settings['meta_keys'];

						// Locate file handler index in file field
						$file_handler_index = false;
						foreach($field_types['advanced']['types']['file']['fieldsets']['basic']['fieldsets'] as $index => $fieldset) {

							if(in_array('file_handler', $fieldset['meta_keys'])) {

								$file_handler_index = $index;
								break;
							}
						}
						if($file_handler_index !== false) {

							foreach($meta_keys as $meta_key) {

								$field_types['advanced']['types']['file']['fieldsets']['basic']['fieldsets'][$file_handler_index]['meta_keys'][] = $meta_key;
							}
						}

						// Locate file handler index in signature field
						$file_handler_index = false;
						foreach($field_types['advanced']['types']['signature']['fieldsets']['basic']['fieldsets'] as $index => $fieldset) {

							if(in_array('file_handler', $fieldset['meta_keys'])) {

								$file_handler_index = $index;
								break;
							}
						}
						if($file_handler_index !== false) {

							foreach($meta_keys as $meta_key) {

								$field_types['advanced']['types']['signature']['fieldsets']['basic']['fieldsets'][$file_handler_index]['meta_keys'][] = $meta_key;
							}
						}

						return $field_types;
					});
				}
			}
		}

		// Touch with file handler ID
		public function touch(&$file_objects) {

			foreach($file_objects as $file_object_key => $file_object) {

				$file_objects[$file_object_key]['handler'] = $this->id;
			}
		}

		// DropzoneJS - Upload
		public static function dropzonejs_upload($file_id, &$attachment_ids, $upload_path, $file_size, $file_min_size, $file_max_size) {

			global $wsf_upload_path;
			$wsf_upload_path = $upload_path;

			$upload_dir_func = function($uploads) {

				global $wsf_upload_path;

				$uploads['path'] = $uploads['basedir'] . '/' . $wsf_upload_path;
				$uploads['url'] = $uploads['baseurl'] . '/' . $wsf_upload_path;
				$uploads['subdir'] = '/' . $wsf_upload_path;

				return $uploads;
			};

			// Check minimum file size - Field level
			if(
				($file_min_size > 0) &&
				($file_size < $file_min_size)
			) {

				self::db_throw_error(sprintf(__('The uploaded file was too small (Minimum size: %s)', 'ws-form'), WS_Form_Common::get_file_size($file_min_size)));
				return;
			}

			// Check maximum file size - Field level
			if(
				($file_max_size > 0) &&
				($file_size > $file_max_size)
			) {

				self::db_throw_error(sprintf(__('The uploaded file was too large (Maximum size: %s)', 'ws-form'), WS_Form_Common::get_file_size($file_min_size)));
				return;
			}

			// Set upload directory
			add_filter('upload_dir', $upload_dir_func);

			// Prevent WS Form from creating multiple file sizes for scratch file
			add_filter('intermediate_image_sizes_advanced', array('WS_Form_File_Handler', 'intermediate_image_sizes_advanced'));

			// Process file with media_handle_upload
			$attachment_id = media_handle_upload($file_id, 0);

			// Remove filters
			remove_filter('upload_dir', $upload_dir_func);

			remove_filter('intermediate_image_sizes_advanced', array('WS_Form_File_Handler', 'intermediate_image_sizes_advanced'));

			// Error checking
			if(is_wp_error($attachment_id)) {

				$error_message = __('Error handling media upload', 'ws-form');

				if(
					isset($attachment_id->errors) &&
					isset($attachment_id->errors['upload_error']) &&
					isset($attachment_id->errors['upload_error'][0])
				) {

					$error_message = $attachment_id->errors['upload_error'][0];
				}

				parent::db_throw_error($error_message);
			}

			$attachment_ids[] = $attachment_id;
		}

		// DropzoneJS - Intermediate image sizing - Retain thumb only
		public static function intermediate_image_sizes_advanced($size) {

			// Get image size
			$image_size = apply_filters('wsf_dropzonejs_image_size', WS_FORM_DROPZONEJS_IMAGE_SIZE);

			return isset($size[$image_size]) ? array($image_size => $size[$image_size]) : array();
		}

		// DropzoneJS - Purge
		public static function dropzonejs_purge() {

			remove_action('pre_get_posts', 'WS_Form_File_Handler::dropzonejs_filter_attachments', 10);

			$cookie_timeout = WS_Form_Common::option_get('cookie_timeout', 60 * 60 * 24 * 28);

			$before = date('Y-m-d H:i:s', strtotime(sprintf('-%u seconds', $cookie_timeout)));

			$args = array(

				'post_type' => 'attachment',

				'posts_per_page' => -1,

				'meta_query' => array(

					array(

						'key'		=> '_wsf_attachment_scratch',
						'compare'	=> 'EXISTS',
					),
				),

				'date_query' => array(

					'column' => 'post_date_gmt',
					'before' => $before,
				),

				'post_parent' => 0,
			);

			$posts = get_posts($args);

			foreach($posts as $post) {

				if(!get_post_meta($post->ID, '_wsf_attachment_scratch', true)) { continue; }

				wp_delete_attachment($post->ID, true);
			}

			add_action('pre_get_posts', 'WS_Form_File_Handler::dropzonejs_filter_attachments', 10);
		}

		// DropzoneJS - Filter attachments
		public static function dropzonejs_filter_attachments($query) {

			if($query->get('post_type') !== 'attachment') { return; }

			$meta_query = $query->get('meta_query');

			if(!is_array($meta_query)) { $meta_query = array(); }

			if(isset($meta_query['relation']) && (strtolower($meta_query['relation']) === 'or')) {

				$meta_query = array($meta_query);
			}

			$meta_query[] = array(

				'key'     => '_wsf_attachment_scratch',
				'compare' => 'NOT EXISTS',
			);

			$query->set('meta_query', $meta_query);
		}
		// Get file object from URL
		public static function get_file_object_from_url($url) {

			$attachment_id = attachment_url_to_postid($url);
			if($attachment_id === 0) { return false; }

			return self::get_file_object_from_attachment_id($attachment_id);
		}

		// Get file object from attachment ID
		public static function get_file_object_from_attachment_id($attachment_id) {

			if(!$attachment_id) { return false; }

			// Get file path full
			$file_path_full = get_attached_file($attachment_id);
			if($file_path_full === false) { return false; }

			// Get file name
			$file_name = basename($file_path_full);			

			// Get file path
			$file_path = str_replace(wp_upload_dir()['basedir'] . '/', '', $file_path_full);

			// Get file size
			$file_size = 0;
			if(file_exists($file_path_full)) {

				$file_size = filesize($file_path_full);

			} else {

				return false;
			}

			// Get mime type
			$file_type = get_post_mime_type($attachment_id);

			// Get UUID
			$file_uuid = md5(get_the_guid($attachment_id));

			// Get image size
			$image_size = apply_filters('wsf_dropzonejs_image_size', WS_FORM_DROPZONEJS_IMAGE_SIZE);

			// Get file URL
			$file_url = wp_get_attachment_image_src($attachment_id, $image_size, true);
			if($file_url) {

				$file_url = $file_url[0];

			} else {

				$file_url = wp_get_attachment_thumb_url($attachment_id);

				if(!$file_url) { $file_url = ''; }
			}

			// Build file object
			$file_object = array(

				'name' => $file_name,
				'path' => $file_path,
				'url' => $file_url,
				'size' => $file_size,
				'type' => $file_type,
				'uuid' => $file_uuid,		// Used by DropzoneJS to provide a unique ID
				'attachment_id' => $attachment_id
			);

			return $file_object;
		}

		public function api_call($endpoint, $path = '', $method = 'GET', $body = null, $headers = array(), $authentication = 'basic', $username = false, $password = false, $accept = 'application/json', $content_type = 'application/json') {
			
			// Build query string
			$query_string = (($body !== null) && ($method == 'GET')) ? '?' . http_build_query($body) : '';

			// Filters
			$timeout = apply_filters('wsf_api_call_timeout', WS_FORM_API_CALL_TIMEOUT);
			$sslverify = apply_filters('wsf_api_call_verify_ssl',WS_FORM_API_CALL_VERIFY_SSL);

			// Headers
			if($accept !== false) { $headers['Accept'] = $accept; }
			if($content_type !== false) { $headers['Content-Type'] = $content_type; }
			if($username !== false) {

				switch($authentication) {

					case 'basic' :

						$headers['Authorization']  = 'Basic ' . base64_encode($username . ':' . $password);
						break;
				}
			}

			// Build args
			$args = array(

				'method'		=>	$method,
				'headers'		=>	$headers,
				'user-agent'	=>	'WSForm/' . WS_FORM_VERSION . ' (wsform.com)',
				'timeout'		=>	$timeout,
				'sslverify'		=>	$sslverify
			);

			// Add body
			if($method != 'GET') { $args['body'] = $body; }

			// URL
			$url = $endpoint . $path . $query_string;

			// Call using Wordpress wp_remote_get
			$wp_remote_request_response = wp_remote_request($url, $args);

			// Check for error
			if($api_response_error = is_wp_error($wp_remote_request_response)) {

				// Handle error
				$api_response_error_message = $wp_remote_request_response->get_error_message();
				$api_response_headers = array();
				$api_response_body = '';
				$api_response_http_code = 0;

			} else {

				// Handle response
				$api_response_error_message = '';
				$api_response_headers = wp_remote_retrieve_headers($wp_remote_request_response);
				$api_response_body = wp_remote_retrieve_body($wp_remote_request_response);
				$api_response_http_code = wp_remote_retrieve_response_code($wp_remote_request_response);
			}

			// Return response
			return array('error' => $api_response_error, 'error_message' => $api_response_error_message, 'response' => $api_response_body, 'http_code' => $api_response_http_code, 'headers' => $api_response_headers);
		}

		// Get value of an object, otherwise return false if not set
		public function get_object_value($field, $key) {

			return isset($field->{$key}) ? $field->{$key} : false;
		}
	}
