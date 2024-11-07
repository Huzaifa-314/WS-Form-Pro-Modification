(function($) {

	'use strict';

	// Adds signature elements
	$.WS_Form.prototype.form_signature = function() {

		var ws_this = this;

		// Get signature objects
		var signature_objects = $('[data-type="signature"]:not([data-init-signature])', this.form_canvas_obj);
		if(
			!signature_objects.length ||
			(typeof(SignaturePad) === 'undefined')
		) {
			return false;
		}

		// Process each signature
		signature_objects.each(function() {

			// Flag so it only initializes once
			$(this).attr('data-init-signature', '');

			// Input
			var input = $('input', $(this));

			// Canvas
			var canvas = $('canvas', $(this));
			var canvas_element = canvas[0];

			// Name
			var name = input.attr('name');

			// Height
			var height = input.attr('data-height');
			canvas.attr('style', 'height: ' + height + '; padding: 0; width: 100%;');

			// ID
			var id = $(this).attr('data-id');

			// Field
			var field = ws_this.field_data_cache[id];

			// Size
			var dot_size = input.attr('data-dot-size');

			// Crop
			var crop = (input.attr('data-crop') !== undefined);

			// Pen color
			var pen_color = input.attr('data-pen-color');

			// Type
			var mime = (input.attr('data-mime') !== undefined) ? input.attr('data-mime') : 'image/png';

			// Build config
			var config = {

				maxWidth: dot_size,
				penColor: pen_color,
			}

			var signature = {signature: false, canvas: canvas, canvas_element: canvas_element, input: input, name: name, mime: mime, config: config, crop: crop};

			// Background color (JPG only)
			if(mime == 'image/jpeg') {

				var background_color = (canvas.attr('data-background-color') !== undefined) ? canvas.attr('data-background-color') : '#ffffff';
				config.backgroundColor = background_color;
				signature.background_color = background_color;
			}

			// Add to signatures array
			ws_this.signatures.push(signature)

			// Get current framework
			var framework_groups = ws_this.framework['groups']['public'];
			var class_active = typeof(framework_groups['class_active']) ? framework_groups['class_active'] : false;

			// Momentarily show group if currently hidden so that width is calculated correctly
			var group_obj = $(signature.canvas_element, ws_this.form_canvas_obj).closest('[id^="' + ws_this.form_id_prefix + 'group-"]');
			var group_visible = group_obj.is(':visible');

			if(!group_visible) {

				if(class_active) {

					group_obj.addClass(class_active);

				} else {

					group_obj.show();
				}
			}

			// Init signature pad
			var signature_pad = new SignaturePad(signature.canvas_element, signature.config);

			// Add to signatures object
			signature.signature = signature_pad;

			// Add signature to signatures_by_name array
			ws_this.signatures_by_name[signature.name] = signature;

			// Clear event
			signature.canvas.parent().find('[data-action="wsf-signature-clear"]').on('click', function(e) {

				e.preventDefault();

				signature.signature.clear();

				// Clear validation input
				signature.input.val('').trigger('change');
			});

			// Mouse down / touch start event
			signature.canvas.on('mousedown touchstart', function() {

				// Set validation input
				signature.input.val('1').trigger('change');
			});

			$(document).on('keydown', function(e) {

				if(signature.canvas.is(':focus') && (e.keyCode == 27)) {

					signature.signature.clear();

					// Clear validation input
					signature.input.val('').trigger('change');
				}
			});

			// Window resize
			$(window).on('resize', function() {

				ws_this.signature_redraw(signature.signature, signature.canvas_element);
			});

			// Initial redraw
			ws_this.signature_redraw(signature.signature, signature.canvas_element, true);

			// Hide group if it was hidden originally
			if(!group_visible) {

				if(class_active) {

					group_obj.removeClass(class_active);

				} else {

					group_obj.hide();
				}
			}

			// Fire real time form validation
			ws_this.form_validate_real_time_process(false);
		});
	}

	// Adds signature elements
	$.WS_Form.prototype.signature_redraw = function(signature, canvas_element, clear) {

		if(typeof(clear) === 'undefined') { clear = false; }

		// If the signature is empty, we'll clear it instead of drawing from data otherwise signature thinks it is not empty
		if(!clear && signature.isEmpty()) { clear = true; }

		// Remember canvas
		if(!clear) {

			var canvas_data = signature.toDataURL();
		}

		// Resize
		var ratio = Math.max(window.devicePixelRatio || 1, 1);
		canvas_element.width = canvas_element.offsetWidth * ratio;
		canvas_element.height = canvas_element.offsetHeight * ratio;
		canvas_element.getContext("2d").scale(ratio, ratio);

		// Redraw canvas using original data
		if(clear) {

			signature.clear();

		} else {

			signature.fromDataURL(canvas_data);
		}
	}

	// Signatures - Redraw on tab, section or field changes
	$.WS_Form.prototype.signatures_redraw = function(tab_index, section_id, field_id) {

		if(typeof(section_id) === 'undefined') { section_id = false; }
		if(typeof(field_id) === 'undefined') { field_id = false; }

		for(var signature_index in this.signatures) {

			if(!this.signatures.hasOwnProperty(signature_index)) { continue; }

			var signature = this.signatures[signature_index];

			// Skip if not yet initialized
			if(signature.signature === false) { continue; }

			var signature_canvas_element = signature.canvas_element;

			var signature_tab_index = this.get_group_index($(signature_canvas_element, this.form_canvas_obj));
			var signature_section_id = this.get_section_id($(signature_canvas_element, this.form_canvas_obj));
			var signature_field_id = this.get_field_id($(signature_canvas_element, this.form_canvas_obj));

			if(
				((tab_index !== false) && (signature_tab_index == tab_index)) ||
				((section_id !== false) && (section_id == signature_section_id)) ||
				(field_id == signature_field_id)
			) {
				this.signature_redraw(signature.signature, signature_canvas_element);
			}
		}
	}

	// Signature - Clear all signatures
	$.WS_Form.prototype.signatures_clear = function() {

		for(var signature_index in this.signatures) {

			if(!this.signatures.hasOwnProperty(signature_index)) { continue; }

			var signature = this.signatures[signature_index];

			signature.signature.clear();

			signature.input.val('').trigger('change');
		}
	}

	// Signature - Clear by name
	$.WS_Form.prototype.signature_clear_by_name = function(name) {

		var signature = (typeof(this.signatures_by_name[name]) !== 'undefined') ? this.signatures_by_name[name] : false;
		if(signature !== false) {

			signature.signature.clear();

			signature.input.val('').trigger('change');
		}
	}

	// Signature - Get response by name
	$.WS_Form.prototype.signature_get_response_by_name = function(name) {

		var signature = (typeof(this.signatures_by_name[name]) !== 'undefined') ? this.signatures_by_name[name] : false;
		return (signature !== false) ? (signature.input.val() !== '') : false;
	}

	// Signature - Form post
	$.WS_Form.prototype.signature_form_post = function() {

		// Signatures
		for(var signature_index in this.signatures) {

			if(!this.signatures.hasOwnProperty(signature_index)) { continue; }

			var signature = this.signatures[signature_index];

			// Check for blank signature
			if(
				signature.signature.isEmpty() ||
				(signature.canvas_element.width == 0) ||
				(signature.canvas_element.height == 0)
			) {

				// Add signature image to form data
				$('[name="' + signature.name + '"]', this.form_canvas_obj).val('');
				continue;
			}

			// Crop signature?
			if(signature.crop) {

				if(signature.mime == 'image/jpeg') {

					var rgb = this.hex_to_rgb(signature.background_color);
				}

	            var signature_cropped			= document.createElement('canvas'),
	                signature_cropped_context	= signature_cropped.getContext('2d');

	                signature_cropped.width 	= signature.canvas_element.width;
	                signature_cropped.height	= signature.canvas_element.height;
	                signature_cropped_context.drawImage(signature.canvas_element, 0, 0);

	            var w         = signature_cropped.width,
	                h         = signature_cropped.height,
	                pix       = {x:[], y:[]},
	                imageData = signature_cropped_context.getImageData(0,0,signature_cropped.width,signature_cropped.height),
	                x, y, index;

	            for (y = 0; y < h; y++) {

	                for (x = 0; x < w; x++) {

	                    index = (y * w + x) * 4;

	                    if(signature.mime == 'image/jpeg') {

	                	// Use background color
		                    if(!(
		                	(imageData.data[index] == rgb.r) &&
		                	(imageData.data[index+1] == rgb.g) &&
		                	(imageData.data[index+2] == rgb.b)
		                    )) {

		                        pix.x.push(x);
		                        pix.y.push(y);
		                    }

	                    } else {

	                	// Use alpha channel
		                    if (imageData.data[index+3] > 0) {

		                        pix.x.push(x);
		                        pix.y.push(y);
		                    }
		                }
	                }
	            }

	            pix.x.sort(function(a,b){return a-b});
	            pix.y.sort(function(a,b){return a-b});
	            var n = pix.x.length-1;

	            w = pix.x[n] - pix.x[0];
	            h = pix.y[n] - pix.y[0];

	            if(
	        	(w > 0) &&
	        	(h > 0)
				) {

		            var cut = signature_cropped_context.getImageData(pix.x[0], pix.y[0], w, h);

		            signature_cropped.width = w;
		            signature_cropped.height = h;
		            signature_cropped_context.putImageData(cut, 0, 0);

					var signature_data = signature_cropped.toDataURL(signature.mime);

				} else {

					var signature_data = signature.signature.toDataURL(signature.mime);
				}

			} else {

				var signature_data = signature.signature.toDataURL(signature.mime);
			}

			// Add signature image to form data
			$('[name="' + signature.name + '"]', this.form_canvas_obj).val(signature_data);
		}
	}

})(jQuery);
