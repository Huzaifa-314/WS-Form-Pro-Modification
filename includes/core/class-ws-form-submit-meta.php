<?php

	class WS_Form_Submit_Meta extends WS_Form_Core {

		public $id;
		public $parent_id;

		public $meta_key;
		public $meta_value;
		public $field_id;

		public $table_name;

		private $encryption_excluded_meta_keys = array('source_form_id', 'source_id');
		const DB_INSERT = 'meta_key,meta_value,field_id,parent_id';
		const DB_SELECT = 'meta_key,meta_value,section_id,field_id,repeatable_index';

		public function __construct() {

			global $wpdb;

			$this->id = 0;
			$this->parent_id = 0;
			$this->encryption_excluded_meta_keys = array('source_form_id', 'source_id');
		}

		// Get table name
		public function get_table_name() {

			global $wpdb;

			return $wpdb->prefix . WS_FORM_DB_TABLE_PREFIX . 'submit_meta';
		}

		// Read all meta data
		public function db_read_all($bypass_user_capability_check = false, $encrypted = false) {

			// User capability check
			if(!$bypass_user_capability_check && !WS_Form_Common::can_user('read_submission')) { return false; }

			$return_array = array();

			if(absint($this->parent_id) === 0) { parent::db_throw_error(__('Parent ID not set')); }

			global $wpdb;

			$sql = sprintf("SELECT %s FROM %s WHERE parent_id = %u;", self::DB_SELECT, self::get_table_name(), esc_sql($this->parent_id));
			$meta_array = $wpdb->get_results($sql, 'ARRAY_A');

			if($meta_array) {

				$return_array = $meta_array;

				// Decrypt
				if($encrypted) {

					$ws_form_encryption = new WS_Form_Encryption();

					foreach($return_array as $key => $value) {

						if(!isset($value['meta_value'])) { continue; }
						if(in_array($value['meta_key'], $this->encryption_excluded_meta_keys)) { continue; }

						$return_array[$key]['meta_value'] = $ws_form_encryption->decrypt($value['meta_value']);
					}
				}

				// Apply filters
				foreach($return_array as $key => $value) {

					if(!isset($value['meta_key'])) { continue; }
					if(!isset($value['meta_value'])) { continue; }
					if(!isset($value['field_id'])) { continue; }

					// Get field ID
					$field_id = absint($value['field_id']);
					if($field_id > 0) {

						// Apply filter
						$return_array[$key]['meta_value'] = apply_filters('wsf_submit_meta_read', $return_array[$key]['meta_value'], $field_id);
					}
				}
			}

			return $return_array;
		}

		// Find submit record by meta_key value
		public function db_read_parent_id_by_meta($meta_key, $meta_value, $form_id, $bypass_user_capability_check = false) {

			// Create submit object to get table name
			$ws_form_submit = new WS_Form_Submit();

			// User capability check
			if(!$bypass_user_capability_check && !WS_Form_Common::can_user('read_submission')) { return false; }

			global $wpdb;

			$sql = sprintf('SELECT %1$s.parent_id FROM %1$s RIGHT JOIN %2$s ON %1$s.parent_id = %2$s.id WHERE %1$s.meta_key = \'%3$s\' AND %1$s.meta_value = \'%4$s\' AND %2$s.form_id = %5$u LIMIT 1;', self::get_table_name(), $ws_form_submit->table_name, esc_sql($meta_key), esc_sql($meta_value), $form_id);
			$parent_id = $wpdb->get_var($sql);

			return !is_null($parent_id) ? $parent_id : false;
		}

		// Delete
		public function db_delete() {

			// User capability check
			if(!WS_Form_Common::can_user('edit_submission')) { return false; }

			global $wpdb;

			// Read meta value to determine if this is file type (file or signature)
			$sql = sprintf("SELECT meta_value FROM %s WHERE id = %u;", self::get_table_name(), $this->id);
			$meta_value = $wpdb->get_var($sql);
			if(is_null($meta_value)) { parent::db_wpdb_handle_error(__('Unable to read file meta data', 'ws-form')); }

			// Delete file
			self::db_delete_file($meta_value);

			// Delete meta
			$sql = sprintf("DELETE FROM %s WHERE id = %u;", self::get_table_name(), $this->id);
			if($wpdb->query($sql) === false) { parent::db_wpdb_handle_error(__('Error deleting submit meta', 'ws-form')); }
		}

		// Delete all meta in submit
		public function db_delete_by_submit() {

			// User capability check
			if(!WS_Form_Common::can_user('edit_submission')) { return false; }

			global $wpdb;

			// Read meta values to determine if any of them are a file type (file or signature)
			$sql = sprintf("SELECT meta_value FROM %s WHERE parent_id = %u;", self::get_table_name(), $this->parent_id);
			$metas = $wpdb->get_results($sql, 'ARRAY_A');
			if(is_null($metas)) { return false; }

			// Delete all files
			foreach($metas as $meta) {

				self::db_delete_file($meta['meta_value']);
			}

			// Delete submit meta
			$sql = sprintf("DELETE FROM %s WHERE parent_id = %u;", self::get_table_name(), $this->parent_id);
			if($wpdb->query($sql) === false) { parent::db_wpdb_handle_error(__('Error deleting all submit meta', 'ws-form')); }
		}

		// Delete file associated with meta_value
		public function db_delete_file($meta_value) {

			if(empty($meta_value)) { return false; }

			// Check to see if meta value is serialized data
			if(!is_serialized($meta_value)) { return false; }

			// Unserialize to get array of files
			$file_objects = unserialize($meta_value);

			// Check file objects
			if(!is_array($file_objects)) { return false; }
			if(count($file_objects) == 0) { return false; }

			// Run through each file
			foreach($file_objects as $file_object) {

				// Check file object
				if(
					!isset($file_object['name']) ||
					!isset($file_object['size']) ||
					!isset($file_object['type']) ||
					!isset($file_object['path'])

				) { continue; }

				// Field file handler type
				$field_file_handler = isset($file_object['handler']) ? $file_object['handler'] : 'wsform';

				// Check file handler is installed
				if(!isset(WS_Form_File_Handler_WS_Form::$file_handlers[$field_file_handler])) { continue; }

				// Delete file
				WS_Form_File_Handler_WS_Form::$file_handlers[$field_file_handler]->delete($file_object);
			}
		}

		// Add meta data from object (Meta data is stored as an object by default to allow for JSON transfer)
		public function db_update_from_object($meta_data_object, $submit_encrypted = false) {

			// No capabilities required, this is a public method

			self::db_update_from_array((array)$meta_data_object, $submit_encrypted);
		}

		// Add meta data from array
		public function db_update_from_array($meta_data_array, $submit_encrypted = false) {

			// No capabilities required, this is a public method

			if(absint($this->parent_id) === 0) { parent::db_throw_error(__('Parent ID not set', 'ws-form')); }
			if(!is_array($meta_data_array)) { return true; }							// Empty data
			if(count($meta_data_array) === 0) { return true; }							// Empty data

			// Encryption
			if($submit_encrypted) {

				$ws_form_encryption = new WS_Form_Encryption();
			}
			foreach($meta_data_array as $meta_key => $meta_value) {

				$is_repeatable = false;

				if(isset($meta_value['db_ignore'])) { continue; }

				if(is_array($meta_value) && isset($meta_value['id'])) {

					// Build meta data (field)
					$field_id = $meta_value['id'];
					$db_data = array('parent_id' => $this->parent_id, 'field_id' => $field_id, 'meta_value' => $meta_value['value'], 'meta_key' => $meta_key);

					// Repeatable index
					if(
						isset($meta_value['section_id']) &&
						isset($meta_value['repeatable_index']) &&
						($meta_value['repeatable_index'] !== false)
					) {

						$db_data['section_id'] = absint($meta_value['section_id']);
						$db_data['repeatable_index'] = absint($meta_value['repeatable_index']);
						if($db_data['repeatable_index'] > 0) { $is_repeatable = true; }
					}

				} else {

					// Build meta data (meta_key)
					$field_id = 0;
					$db_data = array('parent_id' => $this->parent_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value);
				}

				// Serialize arrays
				if(is_array($db_data['meta_value'])) { $db_data['meta_value'] = serialize($db_data['meta_value']); }

				// Encryption
				if($submit_encrypted) {

					if(!in_array($db_data['meta_key'], $this->encryption_excluded_meta_keys)) {

						if(is_int($db_data['meta_value'])) {

							$db_data['meta_value'] = strval($db_data['meta_value']);
						}

						$db_data['meta_value'] = $ws_form_encryption->encrypt($db_data['meta_value']);
					}
				}
				global $wpdb;

				// Get ID of existing meta record
				if($is_repeatable) {

					$sql = sprintf("SELECT id FROM %s WHERE parent_id = %u AND meta_key = '%s' AND section_id = %u AND repeatable_index = %u LIMIT 1", self::get_table_name(), $this->parent_id, esc_sql($meta_key), $db_data['section_id'], $db_data['repeatable_index']);
				} else {

					$sql = sprintf("SELECT id FROM %s WHERE parent_id = %u AND meta_key = '%s' LIMIT 1", self::get_table_name(), $this->parent_id, esc_sql($meta_key));
				}
				$id = $wpdb->get_var($sql);
				if($id) { $db_data['id'] = $id; }

				// WordPress hook
				if($field_id > 0) {

					$db_data['meta_value'] = apply_filters('wsf_submit_meta_update', $db_data['meta_value'], $field_id);
				}

				// Replace
				$replace_count = $wpdb->replace(self::get_table_name(), $db_data);
				if($replace_count === false) {

					parent::db_throw_error(__('Unable to replace meta data', 'ws-form'));
				}
			}

			return true;
		}

		// Process meta data
		public function process($ws_form_submit, &$meta) {

			foreach($meta as $meta_key => $meta_value) {

				if(!is_array($meta_value)) { continue; }
				if(!isset($meta_value['id'])) { continue; }
				if(!isset($meta_value['value'])) { continue; }

				$field_id = $meta_value['id'];
				$meta_value = $meta_value['value'];
				$section_repeatable_index = isset($meta_value['repeatable_index']) ? $meta_value['repeatable_index'] : false;
				if(is_array($meta_value)) { continue; }

				// Check for base64_to_file
				if(strpos($meta_value, 'base64_to_file,') === 0) {

					$field_value = substr($meta_value, 15);

					$field = (object) array(

						'id' => $field_id,
						'file_handler' => 'wsform'
					);

					$file_objects = self::process_signature($field, $section_repeatable_index, $field_value, $ws_form_submit);

					if($file_objects !== false) {

						$meta[$meta_key]['value'] = $file_objects;

					} else {

						$meta[$meta_key]['value'] = '';
					}
				}

				// Check for upload_url_to_file
				if(strpos($meta_value, 'upload_url_to_file,') === 0) {

					// Get upload URL
					$field_value = substr($meta_value, 19);

					$field_value_array = explode(',', $field_value);

					// Reset meta value in case the following functions fail
					$meta[$meta_key]['value'] = '';

					$file_objects = array();

					// Extract file information
					$upload_dir = wp_upload_dir()['basedir'];
					$upload_url = WS_Form_Common::get_upload_dir_base_url();
					$upload_url_parsed = parse_url($upload_url);
					if($upload_url_parsed != false) {

						$upload_path = $upload_url_parsed['path'] . '/';

						foreach($field_value_array as $field_value) {

							if(strpos($field_value, $upload_path) === 0) {

								$file_path = substr($field_value, strlen($upload_path));
								$file_name = basename($file_path);

								// Get file size
								$file_size = filesize($upload_dir . '/' . $file_path);
								if($file_size === false) { $file_size = 0; }

								// Get file type
								if(function_exists('mime_content_type')) {

									$file_type = mime_content_type($upload_dir . '/' . $file_path);
									if($file_type === false) { $file_type = ''; }

								} else {

									$file_type = '';
								}

								// Check file object
								$file_object = array();
								$file_object['name'] = $file_name;
								$file_object['hash'] = md5($ws_form_submit->id . '_' . $ws_form_submit->form_id . '_' . $file_name);
								$file_object['type'] = $file_type;
								$file_object['size'] = $file_size;
								$file_object['path'] = $file_path;

								$file_objects[] = $file_object;
							}
						}
					}

					// Push to file objects array
					$meta[$meta_key]['value'] = $file_objects;
				}

				// Check for filename_to_file
				if(strpos($meta_value, 'filename_to_file,') === 0) {

					// Get file path
					$file_name = substr($meta_value, 17);

					// Check file name
					if(strpos($file_name, '\\') !== false) { parent::db_throw_error(__('Illegal file name', 'ws-form')); }
					if(strpos($file_name, '/') !== false) { parent::db_throw_error(__('Illegal file name', 'ws-form')); }

					// Reset meta value in case the following functions fail
					$meta[$meta_key]['value'] = '';

					$file_objects = array();

					// Build file upload path
					$file_upload_path = $ws_form_submit->form_id . '/' . $ws_form_submit->hash . '/' . $field_id;

					// Apply filter
					$file_upload_path = apply_filters('wsf_file_upload_path', $file_upload_path);
					$file_upload_path = apply_filters('wsf_file_upload_path_file', $file_upload_path);

					$upload_dir = WS_Form_Common::upload_dir_create($file_upload_path);
					if($upload_dir['error']) { parent::db_throw_error($upload_dir['error']); }
					$file_upload_dir = $upload_dir['dir'];

					// Get file type
					if(function_exists('mime_content_type')) {

						$file_type = mime_content_type($file_name);
						if($file_type === false) { $file_type = ''; }

					} else {

						$file_type = '';
					}

					// Get file size
					$file_size = filesize($file_name);
					if($file_size === false) { $file_size = 0; }

					// Copy uploaded file to WordPress uploads folder
					$file_name_hash = md5($file_upload_dir . '/' . $file_name);
					$move_uploaded_file_destination =  $file_upload_dir . '/' . $file_name_hash;
					copy($file_name, $move_uploaded_file_destination);

					// Check file object
					$file_object = array();
					$file_object['name'] = $file_name;
					$file_object['hash'] = $file_name_hash;
					$file_object['type'] = $file_type;
					$file_object['size'] = $file_size;
					$file_object['path'] = $upload_dir['path'] . '/' . $file_name_hash;		// Store this in case we change the path structure in future

					// Push to file objects array
					$file_objects[] = $file_object;

					// Push to file objects array
					$meta[$meta_key]['value'] = $file_objects;
				}
			}
		}

		// Process signature
		public function process_signature($field, $section_repeatable_index, $field_value, $ws_form_submit) {

			if($field_value == '') {

				return false;
			}

			// Split field value
			$field_value = str_replace(' ', '+', $field_value);	// Required for canvas.toDataURL() data

			// Split the field_value by comma
			$field_value_array = explode(',', $field_value);
			if(count($field_value_array) != 2) { return false; }

			// Get file type
			$field_type_array = explode(':', $field_value_array[0]);
			if(count($field_type_array) != 2) { return false; }
			$field_type_array = explode(';', $field_type_array[1]);
			if(count($field_type_array) != 2) { return false; }
			$file_type = $field_type_array[0];
			if(strtolower($field_type_array[1]) != 'base64') { parent::db_throw_error(__('Unknown signature format', 'ws-form')); }

			// Get file data
			$file_data = base64_decode($field_value_array[1]);
			if($file_data === false) { parent::db_throw_error(__('Invalid signature base64 data', 'ws-form')); }

			// Create file name based upon file type
			switch($file_type) {

				case 'image/jpeg' :

					$file_name_extension = 'jpg';
					break;

				case 'image/svg+xml' :

					$file_name_extension = 'svg';
					break;

				case 'image/png' :

					$file_name_extension = 'png';
					break;

				default :

					parent::db_throw_error(__('Invalid signature file format', 'ws-form'));
			}
			$file_name = 'signature.' . $file_name_extension;

			// Check hash
			if(!WS_Form_Common::check_submit_hash($ws_form_submit->hash)) {

				parent::db_throw_error(__('Invalid hash ID (process_signature)', 'ws-form'));
				die();
			}

			// Build temporary path
			$temp_path = get_temp_dir() . 'ws-form-signature-' . $ws_form_submit->hash;

			// Open the output file for writing
			$file_pointer = fopen($temp_path, 'wb'); 

			// Write data to temporary file
			fwrite($file_pointer, $file_data);

			// Get file size
			$file_stat = fstat($file_pointer);
			$file_size = (isset($file_stat['size'])) ? $file_stat['size'] : 0;

			// Clean up the file resource
			fclose($file_pointer); 

			// Check file format (Security check to make sure something nasty wasn't uploaded)
			if(function_exists('mime_content_type')) {

				$file_type_check = mime_content_type($temp_path);
				if(!in_array($file_type_check, array('image/jpeg', 'image/svg+xml', 'text/plain', 'image/png'))) {		// text/plain = SVG

					unlink($temp_path);
					parent::db_throw_error(__('Invalid signature file format', 'ws-form'));
				}
			}

			// Check file object
			$file_object = array();
			$file_object['name'] = $file_name;
			$file_object['type'] = $file_type;
			$file_object['size'] = $file_size;
			$file_object['path'] = $temp_path;

			$file_objects = array($file_object);

			// Field file handler type
	 		$file_handler = WS_Form_Common::get_object_meta_value($field, 'file_handler', 'wsform');
	 		if($file_handler == '') { $file_handler = 'wsform'; }

			// Run file handlers
			$file_objects = apply_filters('wsf_file_handler_' . $file_handler, $file_objects, $ws_form_submit, $field, $section_repeatable_index);

			return $file_objects;
		}

		// Duplicate check
		public function db_dupe_check($form_id, $field_id, $value, $period = false) {

			global $wpdb;

			$ws_form_submit = new WS_Form_Submit();
			$table_name_submit = $ws_form_submit->table_name;

			// Duplication period
			if($period !== false) {

				switch($period) {

					case 'hour' :
					case 'day' :
					case 'week' :
					case 'month' :
					case 'year' :

						$period_sql = sprintf(" AND %s.date_added > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 %s)", $table_name_submit, strtoupper($period));
						break;

					case 'day_current' :

						// Get server time zone
						$server_time_zone = @date_default_timezone_get();

						// Get blog time zone
						$blog_time_zone = get_option('timezone_string');

						// Set time zone to blog
						date_default_timezone_set($blog_time_zone);

						// Get EPOCH for midnight
						$server_midnight_time = strtotime('midnight');

						// Restore timezone
						date_default_timezone_set($server_time_zone);

						$period_sql = sprintf(" AND %s.date_added > '%s'", $table_name_submit, date('Y-m-d H:i:s', $server_midnight_time));

						break;

					default :

						$period_sql = '';
				}
			}

			// Check for a duplicate record
			$sql = sprintf('SELECT %1$s.id FROM %2$s LEFT JOIN %1$s ON %2$s.id = %1$s.parent_id WHERE %2$s.form_id = %3$u AND %1$s.field_id = %4$u AND %1$s.meta_value = \'%5$s\'%6$s LIMIT 1;', self::get_table_name(), $table_name_submit, esc_sql($form_id), esc_sql($field_id), esc_sql($value), $period_sql);
			$submit_meta_id = $wpdb->get_var($sql);

			return !is_null($submit_meta_id);
		}
	}