(function($) {

	'use strict';

	// Google Maps API
	$.WS_Form.prototype.form_google_api_init = function() {

		// Should header script be loaded
		if(!$('#wsf-google-map-script-head').length) {

			var google_map_script_head = '<script id="wsf-google-map-script-head">';
			google_map_script_head += 'var wsf_google_maps_loaded = false;';
			google_map_script_head += 'function wsf_google_map_onload() {';
			google_map_script_head += 'wsf_google_maps_loaded = true;';
			google_map_script_head += '}';
			google_map_script_head += '</script>';

			$('head').append(google_map_script_head);
		}

		// Should Google Maps script be called?
		if(!(window.google && window.google.maps) && !$('#wsf-google-map-script-body').length) {

			// Get Google Maps JS API key
			var api_key_google_map = $.WS_Form.settings_plugin.api_key_google_map;
			var google_map_script_body = '<script id="wsf-google-map-script-body" src="https://maps.googleapis.com/maps/api/js?key=' + api_key_google_map + '&callback=wsf_google_map_onload&libraries=places&v=weekly" async defer></script>';
			$('body').append(google_map_script_body);
		}
	}

	// Google Map
	$.WS_Form.prototype.form_google_map = function() {

		var ws_this = this;

		// Get Google Map objects
		var google_map_objects = $('[data-google-map]:not([data-init-google-map])', this.form_canvas_obj);
		var google_map_objects_count = google_map_objects.length;
		if(!google_map_objects_count) { return false;}

		// Google API Init
		this.form_google_api_init();

		// Reset Google Maps arrays
		this.google_maps = [];

		google_map_objects.each(function() {

			$(this).attr('data-init-google-map', '');

			// Get field ID
			var field_id = ws_this.get_field_id($(this));

			// Get field object
			var field = ws_this.get_field($(this));

			// Build google_map object
			var google_map = {};

			// ID
			google_map.id = $(this).attr('id');

			// Map ID
			google_map.id_map = google_map.id + '-map';

			// $(this)
			google_map.obj = $(this);

			// Map obj
			google_map.obj_map = $('#' + google_map.id_map, ws_this.form_canvas_obj);

			// Height
			google_map.height = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_height', '')).output;
			if(google_map.height) {

				google_map.obj_map.css({

					'height': 0,
					'overflow': 'hidden',
					'padding-bottom': google_map.height,
					'position': 'relative'
				});
			}

			// Latitude
			google_map.lat = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_lat', '')).output;

			// Longitude
			google_map.lng = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_lng', '')).output;

			// Zoom
			google_map.zoom = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_zoom', '')).output;
			if(google_map.zoom) {

				google_map.zoom = parseInt(google_map.zoom, 10);

			} else {

				google_map.zoom = 14;
			}

			// Type
			google_map.type = ws_this.get_object_meta_value(field, 'google_map_type', '');
			if(!google_map.type) {

				google_map.type = 'roadmap';
			}

			// Search field ID
			google_map.search_field_id = parseInt(ws_this.get_object_meta_value(field, 'google_map_search_field_id', 0), 10);

			// Marker - Title
			google_map.marker_icon_title = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_marker_icon_title', '')).output;

			// Marker - Icon
			google_map.marker_icon_url = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_marker_icon_url', '')).output;

			// Style
			google_map.style = ws_this.parse_variables_process(ws_this.get_object_meta_value(field, 'google_map_style', '')).output;

			// Controls
			google_map.control_type = (ws_this.get_object_meta_value(field, 'google_map_control_type', 'on') !== '');
			google_map.control_full_screen = (ws_this.get_object_meta_value(field, 'google_map_control_full_screen', 'on') !== '');
			google_map.control_street_view = (ws_this.get_object_meta_value(field, 'google_map_control_street_view', 'on') !== '');
			google_map.control_zoom = (ws_this.get_object_meta_value(field, 'google_map_control_zoom', 'on') !== '');

			// Add to google_map array
			ws_this.google_maps[field_id] = google_map;

			ws_this.google_map_process(google_map);
		});
	}

	// Wait until Google Maps loaded, then process
	$.WS_Form.prototype.google_map_process = function(google_map, total_ms_start) {

		var ws_this = this;

		// Reposition flag
		google_map.reposition = true;

		// Timeout check
		if(typeof(total_ms_start) === 'undefined') { total_ms_start = new Date().getTime(); }
		if((new Date().getTime() - total_ms_start) > this.timeout_google_maps) {

			this.error('error_timeout_google_maps');
			return false;
		}

		if(window.google && window.google.maps) {

			wsf_google_maps_loaded = true;
		}

		// Check to see if Google Maps loaded
		if(wsf_google_maps_loaded) {

			// Save default value
			google_map.obj.attr('data-default-value', google_map.obj.val());

			// Geocoder
			google_map.geocoder = new google.maps.Geocoder();

			// Height
			if(google_map.height) {

				google_map.obj_map.css({

					'height': 0,
					'overflow': 'hidden',
					'padding-bottom': google_map.height,
					'position': 'relative'
				});
			}

			// Run geolocator
			google_map.geolocate_process = function(position) {

				// Geocoder
				google_map.geocoder.geocode({ location: position }, function(results, status) {

					if(
						(status === 'OK') &&
						results[0]
					) {

						if(google_map.search_field_obj) {

							google_map.search_field_obj.val(results[0].formatted_address);
						}

						google_map.marker_set_position(position, results[0]);

					} else {

						if(google_map.search_field_obj) {

							google_map.search_field_obj.val('');
						}

						google_map.marker_set_position(position);
					}
				});
			}

			// Set field value
			google_map.set_field_value = function(field_value_obj) {

				// Build latitude,longitude string
				var field_value = JSON.stringify(field_value_obj);

				// Set hidden value
				var trigger = (google_map.obj.val() !== field_value);
				google_map.obj.val(field_value);
				if(trigger) { google_map.obj.trigger('change'); }
			}

			// Marker position function
			google_map.marker_set_position = function(position, place) {

				// Get latitude and longitude
				var latitude = position.lat();
				var longitude = position.lng();

				var field_value_obj = {

					'lat': latitude,
					'lng': longitude,
					'zoom' : google_map.map.getZoom(),
					'map_type_id' : google_map.map.getMapTypeId(),
				}

				if(typeof(place) !== 'undefined') {

					// Place ID
					field_value_obj.place_id = (typeof(place.place_id) ? place.place_id : '');

					// Address
					field_value_obj.address = (typeof(place.formatted_address) ? place.formatted_address : '');

					// Name
					field_value_obj.name = (typeof(place.name) ? place.name : '');

					if(typeof(place.address_components) !== 'undefined') {

						place.address_components.forEach(function(address_component) {

							var types = address_component.types;

							// City
							if(types.indexOf('locality') !== -1) { field_value_obj.city = address_component.long_name; }

							// State
							if(types.indexOf('administrative_area_level_1') !== -1) { field_value_obj.city = address_component.long_name; }

							// Country Short / Long
							if(types.indexOf('country') !== -1) {

								field_value_obj.country = address_component.long_name;
								field_value_obj.country_short = address_component.short_name;
							}
						});
					}
				}

				google_map.set_field_value(field_value_obj);
			}

			// On change event
			google_map.obj.on('change', function() {

				var marker_visible = false;

				var field_value = $(this).val();

				try {

					// Check for regular format
					var field_value_obj = JSON.parse(field_value);

				} catch (e) {

					// Support for comma separated values
					var field_value_array = field_value.split(',');

					if(field_value_array.length === 2) {

						var field_value_obj = {

							lat: field_value_array[0],
							lng: field_value_array[1]
						};

						if(typeof(field_value_array[2]) !== 'undefined') {

							field_value_obj.zoom = field_value_array[2]
						}

						if(typeof(field_value_array[3]) !== 'undefined') {

							field_value_obj.map_type_id = field_value_array[3]
						}

						$(this).val(JSON.stringify(field_value_obj));

						google_map.geolocate_process(new google.maps.LatLng(parseFloat(field_value_obj.lat), parseFloat(field_value_obj.lng)));

					} else {

						var field_value_obj = false;
					}
				}

				if(field_value_obj !== false) {

					var position = new google.maps.LatLng(parseFloat(field_value_obj.lat), parseFloat(field_value_obj.lng));
					marker_visible = true;

					// Populate address
					if(
						google_map.search_field_obj &&
						field_value_obj.address &&
						(field_value_obj.address != google_map.search_field_obj.val())
					) {

						google_map.search_field_obj.val(field_value_obj.address);
					}

					// Set zoom
					if(
						field_value_obj.zoom &&
						(field_value_obj.zoom != google_map.map.getZoom())
					) {

						google_map.map.setZoom(field_value_obj.zoom);
					}

					// Set map type ID
					if(
						field_value_obj.map_type_id &&
						(field_value_obj.map_type_id != google_map.map.getMapTypeId())
					) {

						google_map.map.setMapTypeId(field_value_obj.map_type_id);
					}

				} else {

					if(google_map.lat && google_map.lng) {

						var position = new google.maps.LatLng(parseFloat(google_map.lat), parseFloat(google_map.lng));

					} else {

						var position = new google.maps.LatLng(29.95, -90.08);	// Fallback to New Orleans, LA
					}

					// Reset zoom
					google_map.map.setZoom(google_map.zoom);

					// Reset type
					google_map.map.setMapTypeId(google_map.type);
				}

				// Move the marker
				google_map.marker.setPosition(position);
				google_map.marker.setVisible(marker_visible);

				// Move map
				if(google_map.reposition) {

					google_map.map.setCenter(position);
				}
			});

			// Default options
			var options = {

				id: google_map.id_map,
				zoom: google_map.zoom,
				mapTypeId: google_map.type,
				clickableIcons: false,
				gestureHandling: 'cooperative',
				mapTypeControl: google_map.control_type,
				fullscreenControl: google_map.control_full_screen,
				streetViewControl: google_map.control_street_view,
				zoomControl: google_map.control_zoom
			};

			if(google_map.style) {

				try {

					options.styles = JSON.parse(google_map.style);

				} catch(e) {

					this.error('error_google_map_style_js');
				}
			}

			// Create map
			var map = new google.maps.Map(google_map.obj_map[0], options);
			google_map.map = map;

			// On zoom changed event
			google_map.map.addListener('zoom_changed', function() {

				var field_value = google_map.obj.val();
				if(field_value == '') { return; }

				try {

					// Check for regular format
					var field_value_obj = JSON.parse(field_value);

				} catch (e) { return; }

				if(typeof(field_value_obj) === 'object') {

					field_value_obj.zoom = google_map.map.getZoom();
					google_map.set_field_value(field_value_obj);
				}
			});

			// On map type change
			google_map.map.addListener('maptypeid_changed', function() {

				var field_value = google_map.obj.val();
				if(field_value == '') { return; }

				try {

					// Check for regular format
					var field_value_obj = JSON.parse(field_value);

				} catch (e) { return; }

				if(typeof(field_value_obj) === 'object') {

					field_value_obj.map_type_id = google_map.map.getMapTypeId();
					google_map.set_field_value(field_value_obj);
				}
			});


			// Map click event handler
			google_map.map.addListener('click', function(e) {

				google_map.reposition = false;

				google_map.marker_set_position(e.latLng);

				google_map.geolocate_process(e.latLng);
			});

			// Build map marker options
			var options_marker = {

				map: map,
				visible: false,
				draggable: true
			};

			// Marker - Icon
			if(google_map.marker_icon_url) {

				if(typeof(options_marker.icon) === 'undefined') { options_marker.icon = {}; }
				options_marker.icon.url = google_map.marker_icon_url;
			}

			// Marker - Title
			if(google_map.marker_icon_title) {

				options_marker.title = google_map.marker_icon_title;
			}

			// Add marker
			google_map.marker = new google.maps.Marker(options_marker);

			// Marker events
			google_map.marker.addListener('dragend', function() {

				google_map.reposition = false;

				var latitude = this.getPosition().lat();
				var longitude = this.getPosition().lng();

				google_map.geolocate_process(new google.maps.LatLng(latitude, longitude));
			})

			// Clear event
			google_map.obj.parent().find('[data-action="wsf-google-map-clear"]').on('click', function(e) {

				e.preventDefault();

				google_map.obj.val('').trigger('change');
			});

			// Search field ID
			if(google_map.search_field_id) {

				// Get section repeatable index
				var section_repeatable_suffix = ws_this.get_section_repeatable_suffix(google_map.obj);

				// Get search input
				var field_id = parseInt(google_map.search_field_id, 10);
				var search_field_obj = $('[id^="' + ws_this.form_id_prefix + 'field-' + field_id.toString() + section_repeatable_suffix + '"]', ws_this.form_canvas_obj);
				if(section_repeatable_suffix && !search_field_obj.length) {

					// Check outside of repeater
					var search_field_obj = $('[id^="' + ws_this.form_id_prefix + 'field-' + field_id.toString() + '"]', ws_this.form_canvas_obj);
				}
				if(search_field_obj.length) {

					google_map.search_field_obj = search_field_obj;

					// Set up search box
					var search_field_element = search_field_obj[0];
					google_map.search_box = new google.maps.places.SearchBox(search_field_element);

					google_map.map.addListener('bounds_changed', function() {

						google_map.search_box.setBounds(google_map.map.getBounds());
					});

					google_map.search_box.addListener('places_changed', function() {

						google_map.reposition = true;

						var places = google_map.search_box.getPlaces();

						if(places.length == 0) { return; }

						places.forEach(function(place) {

							if(!place.geometry) { return; }

							google_map.marker_set_position(place.geometry.location, place);

							if(place.geometry.viewport) {

								google_map.map.fitBounds(place.geometry.viewport);
							}
						});
					});
				}
			}

			// Initial change
			google_map.obj.trigger('change');

		} else {

			setTimeout(function() { ws_this.google_map_process(google_map, total_ms_start); }, this.timeout_interval);
		}
	}

	// Google Place Search
	$.WS_Form.prototype.form_google_address = function() {

		var ws_this = this;

		// Get Google Address field objects
		var google_address_objects = $('[data-google-address]:not([data-init-google-address])', this.form_canvas_obj);
		var google_address_objects_count = google_address_objects.length;
		if(!google_address_objects_count) { return false;}

		// Google API Init
		this.form_google_api_init();

		// Run through each autocomplete object
		google_address_objects.each(function() {

			$(this).attr('data-init-google-address', '');

			// Build google_address object
			var google_address = {};

			// Field
			var field = ws_this.get_field($(this));

			// $(this)
			google_address.obj = this;

			// Field ID
			google_address.field_id = ws_this.get_field_id($(this));

			// Field mapping
			google_address.field_mapping = ws_this.get_object_meta_value(field, 'google_address_field_mapping', []);

			// Google map - Field ID
			google_address.map_field_id = ws_this.get_object_meta_value(field, 'google_address_map', '');

			// Google map - Zoom
			google_address.map_zoom = parseInt(ws_this.get_object_meta_value(field, 'google_address_map_zoom', '14'), 10);

			// Country restrictions
			var restriction_countries = ws_this.get_object_meta_value(field, 'google_address_restriction_country', []);

			google_address.restriction_country = [];

			if(
				(typeof(restriction_countries) === 'object') &&
				restriction_countries.length
			) {

				for(var restriction_country_index in restriction_countries) {

					if(!restriction_countries.hasOwnProperty(restriction_country_index)) { continue; }

					var restriction_country = restriction_countries[restriction_country_index];

					if(restriction_country.country_alpha_2) {

						google_address.restriction_country.push(restriction_country.country_alpha_2);
					}
				}
			}

			// Business restriction
			google_address.restriction_business = ws_this.get_object_meta_value(field, 'google_address_restriction_business', '');

			// Process
			ws_this.google_address_process(google_address);
		});
	}

	// Google Place Search - Get address field object
	$.WS_Form.prototype.google_address_get_field_obj = function(obj, field_id) {

		// Get section repeatable index
		var section_repeatable_suffix = this.get_section_repeatable_suffix(obj);

		// Get search input
		var field_obj = $('[id^="' + this.form_id_prefix + 'field-' + field_id + section_repeatable_suffix + '"]', this.form_canvas_obj);
		if(section_repeatable_suffix && !field_obj.length) {

			// Check outside of repeater
			var field_obj = $('[id^="' + this.form_id_prefix + 'field-' + field_id + '"]', this.form_canvas_obj);
		}

		return field_obj.length ? field_obj.first() : false;
	}

	// Google Place Search - Process
	$.WS_Form.prototype.google_address_process = function(google_address, total_ms_start) {

		var ws_this = this;

		// Timeout check
		if(typeof(total_ms_start) === 'undefined') { total_ms_start = new Date().getTime(); }
		if((new Date().getTime() - total_ms_start) > this.timeout_google_maps) {

			this.error('error_timeout_google_maps');
			return false;
		}

		if(window.google && window.google.maps) {

			wsf_google_maps_loaded = true;
		}

		// Check to see if Google Maps loaded
		if(wsf_google_maps_loaded) {

			// Arguments
			var args = {

				fields: [

					'address_components',
					'business_status',
					'formatted_address',
					'formatted_phone_number',
					'geometry',
					'international_phone_number',
					'name',
					'place_id',
					'rating',
					'url',
					'vicinity',
					'website'
				]
			};

			// Result type
			switch(google_address.restriction_business) {

				case 'on' : // Legacy
				case 'establishment' :

					args.types = ['establishment'];
					break;

				case 'address' :
				case '(cities)' :
				case '(regions)' :

					args.types = [google_address.restriction_business];
					break;
			}

			// Country restriction
			if(
				(typeof(google_address.restriction_country) === 'object') &&
				google_address.restriction_country.length
			) {

				args.componentRestrictions = { country: google_address.restriction_country };
			}

			// Build autocomplete object
			var autocomplete = new google.maps.places.Autocomplete(google_address.obj, args);

			autocomplete.google_address = google_address;

			autocomplete.addListener('place_changed', function() {

				// Get place
				var place = this.getPlace();

				// Address components
				var components = [];
				components['street_full_short'] = '';
				components['street_full_long'] = '';
				components['postal_code_full_short'] = '';
				components['postal_code_full_long'] = '';

				for(var address_component_index in place.address_components) {

					if(!place.address_components.hasOwnProperty(address_component_index)) { continue; }

					var component = place.address_components[address_component_index];

					var component_type = component.types[0];

					switch (component_type) {

						case 'street_number' :

							components['street_number_short'] = component.short_name;
							components['street_number_long'] = component.long_name;
							components['street_full_short'] = component.short_name + ' ' + components['street_full_short'];
							components['street_full_long'] = component.long_name + ' ' + components['street_full_long'];
							break;

						case 'route' :

							components['route_short'] = component.short_name;
							components['route_long'] = component.long_name;
							components['street_full_short'] += component.short_name;
							components['street_full_long'] += component.long_name;
							break;

						case 'locality' :
						case 'postal_town' :

							components['locality_short'] = component.short_name;
							components['locality_long'] = component.long_name;
							break;

						case 'sublocality' :

							components['sublocality_short'] = component.short_name;
							components['sublocality_long'] = component.long_name;
							break;

						case 'subpremise' :

							components['subpremise_short'] = component.short_name;
							components['subpremise_long'] = component.long_name;
							break;

						case 'neighborhood' :

							components['neighborhood_short'] = component.short_name;
							components['neighborhood_long'] = component.long_name;
							break;

						case 'administrative_area_level_1' :
						
							components['aal1_short'] = component.short_name;
							components['aal1_long'] = component.long_name;
							break;

						case 'administrative_area_level_2' :
						
							components['aal2_short'] = component.short_name;
							components['aal2_long'] = component.long_name;
							break;

						case 'postal_code' :

							components['postal_code_short'] = component.short_name;
							components['postal_code_long'] = component.long_name;
							components['postal_code_full_short'] = component.short_name + components['postal_code_full_short'];
							components['postal_code_full_long'] = component.long_name + components['postal_code_full_long'];
							break;

						case 'postal_code_suffix' :

							components['postal_code_suffix_short'] = component.short_name;
							components['postal_code_suffix_long'] = component.long_name;
							components['postal_code_full_short'] = components['postal_code_full_short'] + '-' + component.short_name;
							components['postal_code_full_long'] = components['postal_code_full_long'] + '-' + component.long_name;
							break;

						case 'country' :

							components['country_short'] = component.short_name;
							components['country_long'] = component.long_name;
							break;
					}
				}

				// Geometry
				if(
					place.geometry &&
					place.geometry.location
				) {
					var location = place.geometry.location;
					components['lat'] = location.lat();
					components['lng'] = location.lng();
					components['lat_lng'] = location.lat() + ',' + location.lng();
				}

				// String components
				var components_string = [

					'business_status',
					'formatted_address',
					'formatted_phone_number',
					'international_phone_number',
					'name',
					'place_id',
					'rating',
					'url',
					'vicinity',
					'website'
				];

				for(var components_string_index in components_string) {

					if(!components_string.hasOwnProperty(components_string_index)) { continue; }

					var component_string = components_string[components_string_index];

					components[component_string] = place[component_string] ? place[component_string] : '';
				}

				// Field mapping
				if(
					(typeof(google_address.field_mapping) === 'object') &&
					google_address.field_mapping.length
				) {

					for(var field_mapping_index in google_address.field_mapping) {

						if(!google_address.field_mapping.hasOwnProperty(field_mapping_index)) { continue; }

						// Get field mapping
						var field_mapping = google_address.field_mapping[field_mapping_index];

						// Get component
						var google_address_component = (typeof(field_mapping.google_address_component) !== 'undefined') ? field_mapping.google_address_component : '';
						if(google_address_component == '') { continue; }
						if(typeof(components[google_address_component]) === 'undefined') { components[google_address_component] = ''; }

						// Get field ID
						var field_id = parseInt((typeof(field_mapping.ws_form_field) !== 'undefined') ? field_mapping.ws_form_field : '', 10);
						if(field_id == 0) { continue; }

						// Get field object
						var field_obj = ws_this.google_address_get_field_obj($(google_address.obj), field_id);
						if(!field_obj) { continue; }

						// Set value
						var value = components[google_address_component];
						var trigger = (field_obj.val() !== value);
						field_obj.val(value);
						if(trigger) { field_obj.trigger('change'); }
					}
				}

				// Set Google Map
				if(
					(google_address.map_field_id != '') &&
					(typeof(ws_this.google_maps[google_address.map_field_id]) !== 'undefined') &&
					place.geometry &&
					place.geometry.location
				) {

					// Get Google Map
					var google_map = ws_this.google_maps[google_address.map_field_id];

					// Set position
					google_map.marker_set_position(place.geometry.location, place);

					// Set viewport
					if(place.geometry.viewport) {

						google_map.map.fitBounds(place.geometry.viewport);
					}

					// Get Google Map - Zoom
					if(google_address.map_zoom > 0) {

						google_map.map.setZoom(google_address.map_zoom);
					}
				}
			});

		} else {

			setTimeout(function() { ws_this.google_address_process(google_address, total_ms_start); }, this.timeout_interval);
		}
	}


})(jQuery);
