(function($) {

	'use strict';

	// Debug size
	$.WS_Form.prototype.debug_size = function(difference_y, resize_on_complete, debug_height_force) {

		// Change height
		if(debug_height_force > 0) {

			this.debug_height = debug_height_force;

		} else {

			this.debug_height = this.debug_height_start + difference_y;
		}

		// If height < allowed minimum
		this.debug_height_min = $('#wsf-debug-nav-wrapper').outerHeight();
		if(this.debug_height < this.debug_height_min) { this.debug_height = this.debug_height_min; }

		// If height > allowed maximum
		var debug_height_max = ($(window).height() - 150);
		if(this.debug_height > debug_height_max) { this.debug_height = debug_height_max; }

		// Set height
		$('#wsf-debug').css({ height: this.debug_height });

		// Scroll
		this.debug_scroll_y = this.debug_scroll_y_start - (this.debug_height_start - this.debug_height);

		// If scroll < 0
		if(this.debug_scroll_y < 0) { this.debug_scroll_y = 0; }

		// Set scroll
		$(window).scrollTop(this.debug_scroll_y);

		if(resize_on_complete) {

			// Change body margin
			this.debug_margin_bottom = this.debug_margin_bottom_start + this.debug_height;

			// If margin < allowed minimum (i.e. minimum originally set on body)
			if(this.debug_margin_bottom < this.debug_margin_bottom_start) { this.debug_margin_bottom = this.debug_margin_bottom_start; }

			// Set margin
			$('body').css({ marginBottom: this.debug_margin_bottom + 'px' });

			// Set panel heights
			this.debug_panel_heights();
		}
	}

	// Debug
	$.WS_Form.prototype.debug = function() {

		var ws_this = this;

		// forEach support on NodeList
		if(window.NodeList && !NodeList.prototype.forEach) {

			NodeList.prototype.forEach = Array.prototype.forEach;
		}

		// Debug mutation observer
		var observer = new MutationObserver(function(mutations) {

			if(!mutations.length || (typeof(mutations) === 'undefined')) { return; }

			mutations.forEach(function(mutation) {

				if(!mutation.removedNodes.length || (typeof(mutation.removedNodes) === 'undefined')) { return; }

				mutation.removedNodes.forEach(function(node) {

					if(node === ws_this.form_canvas_obj[0]) {

						$('#wsf-debug-nav-' + ws_this.form_instance_id).remove();
						$('#wsf-debug-instance-' + ws_this.form_instance_id).remove();

						if($('#wsf-debug-nav li a').length) {

							$('#wsf-debug-nav li a').first().trigger('click');

						} else {

							$('#wsf-debug').remove();
							$.WS_Form.debug_rendered = false;
						}
					}
				});
			});
		});
		observer.observe(document.body, { subtree: true, childList: true });

		// Debug counters
		this.audit_count = [];
		this.audit_count['log'] = 0;
		this.audit_count['error'] = 0;

		// Debug variables
		var debug_height_default = 200;
		this.debug_margin_bottom_start = parseInt($('body').css('marginBottom'), 10);
		this.debug_scroll_y_start = $(window).scrollTop();
		this.debug_height_start = $('#wsf-debug').height();

		// Is debug enabled?
		if(!ws_form_settings.debug) { return false; }

		// Render debug core
		if(!$.WS_Form.debug_rendered) {

			// Debug height start (Read from cookie, or use default)
			this.debug_height_start = parseInt(this.cookie_get('debug_height', debug_height_default, false), 10);

			// Build debug HTML
			var debug_core_html = '<div id="wsf-debug" style="height:' + this.debug_height_start + 'px">';

			// Primary nav wrapper
			debug_core_html += '<div id="wsf-debug-nav-wrapper">';

			// Primary nav
			debug_core_html += '<ul id="wsf-debug-nav"></ul>';

			// Logo
			debug_core_html += '<a href="https://wsform.com?utm_source=ws_form_pro&utm_medium=debug" target="_blank" class="wsf-debug-logo" title="' + this.language('debug_logo') + '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1500 428"><path fill="#002D5D" d="M215.2 422.9l-44.3-198.4-1-4.6-9.3-51.8h-.6l-4.1 22.9-6.8 33.5-45.8 198.4H69.7L0 130.1h28.1L68 300.7l18.6 89.1h1.8c3.5-25.7 9.3-55.6 17.1-89.6l39.9-170H175l40.2 170.6c3.1 12.8 8.8 42.5 16.8 89.1h1.8a581 581 0 0 1 8.7-44.8c5.2-23.9 21.9-95.5 50.1-214.8h27.8l-72.1 292.8h-33.1zM495 349.5c0 24.7-7.1 44-21.3 57.9-14.2 13.9-34.7 20.9-61.5 20.9-14.6 0-27.4-1.7-38.4-5.1a99.8 99.8 0 0 1-25.7-11.3l12.3-21.3c8.3 5.1 5.9 3.6 16.6 7.4 12 4.2 24.3 6.1 36.9 6.1 16.5 0 29.6-4.9 39-14.8a54.9 54.9 0 0 0 14.2-39.7c0-13-3.4-23.9-10.2-32.8-6.8-8.9-19.8-19-38.9-30.4a264.6 264.6 0 0 1-44.8-30.2 84 84 0 0 1-18.6-25.4 78.2 78.2 0 0 1-6.6-33.5c0-21.1 7.8-38.5 23.3-52.2a86.3 86.3 0 0 1 59.4-20.6c25.8 0 45.2 6.7 62.6 17.8L481 163.6a96.9 96.9 0 0 0-51.4-14.8 56.7 56.7 0 0 0-39.6 13.4 45 45 0 0 0-14.8 35.2c0 13 3.3 23.8 10 32.5s20.9 19.3 42.6 31.7c21.3 12.8 35.9 23 43.7 30.6a75.6 75.6 0 0 1 23.5 57.3z"/><path fill="#A7A8AA" d="M643.8 152.8h-50.2V423h-27.8V152.8H525l.2-22.3h40.3l.3-25.5c0-37.2 3.6-60.9 13.4-77.2A56.7 56.7 0 0 1 630.5 0h28.9v23.6l-27.3.4c-13.9.2-20.1 4.5-25.1 9.7-4.9 5.2-7.5 11.5-9.9 23.2a256.1 256.1 0 0 0-3.5 48.6v24.6h50.2v22.7zM857.1 275.8c0 49.3-8.5 87-25.6 113.2a82 82 0 0 1-73.1 39.3 80.4 80.4 0 0 1-72-39.3c-16.7-26.2-25-63.9-25-113.2 0-100.9 32.7-151.4 98.1-151.4a81 81 0 0 1 71.8 39.7c17.2 26.4 25.8 63.7 25.8 111.7zm-166.4 0c0 42.3 5.5 74.2 16.6 95.8A54.5 54.5 0 0 0 759 404c45.9 0 68.9-42.7 68.9-128.2 0-84.7-23-127.1-68.9-127.1-24 0-41.4 10.6-52.2 31.8-10.7 21.3-16.1 53.1-16.1 95.3zM901.8 196.5c0-35.5 42.9-71.7 88.5-72 30.9-.3 42 8.6 53.2 13.7l-13.9 21.6a79 79 0 0 0-39.9-9.9 56 56 0 0 0-35.9 9.3c-9.7 6.4-20.4 12.9-23.6 40.8-2.2 19-.8 45.9-.8 67.8V423h-28.1M1047.6 191.4c5.6-48.2 49.8-67.2 80.6-67.2 17.7 0 39.6 6.4 50.2 14.5 9.5 7.2 14.7 13.4 20.3 32.2a60.4 60.4 0 0 1 25.1-31.3 75.3 75.3 0 0 1 43.7-14.9c24.2 0 48.4 7.5 62.9 28.5 11.6 16.7 16.8 41 16.8 78.4V423h-27.8V223.5c.7-56.9-14.3-75.2-52-75.2-18.7 0-32.2 4.7-42.2 21.9-9.8 17-14.3 47.9-14.3 81.3v171.4h-27.8V223.5c0-24.8-3.8-43.3-11.5-55.5s-26.7-18.6-42.8-18.6c-21.3 0-35.6 10.4-45.3 28-9.7 17.6-8.6 45.1-8.6 84.6v160.9h-28.1M1467.2 109h-2.1l.4-28.5-6.1-.1v-2l14.3.2v2l-6.1-.1-.4 28.5zM1487.1 109.3l-7.8-27.8h-.2l.1 5.5-.3 22.2h-2l.4-30.4h3l6.6 23.4c.6 2.1 1 3.8 1.1 5.3h.2c.2-1 .6-2.8 1.4-5.2l7.2-23.2h3.1l-.4 30.4h-2.1l.3-22 .3-5.7h-.2l-8.5 27.5h-2.2z"/><circle fill="#A7A8AA" cx="1412.6" cy="149.4" r="25.3"/><circle fill="#A7A8AA" cx="1412.6" cy="273" r="25.3"/><circle fill="#A7A8AA" cx="1412.6" cy="395" r="25.3"/></svg></a>';

			// Close
			debug_core_html += '<div class="wsf-debug-close" data-action="wsf-debug-close" title="' + this.language('debug_close') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path d="M11.5 10.1 9.4 8l2.1-2.1-1.4-1.4L8 6.6 5.9 4.5 4.5 5.9 6.6 8l-2.1 2.1 1.4 1.4L8 9.4l2.1 2.1 1.4-1.4z"/><path d="M15 1H1v14h14V1zm-1 13H2V2h12v12z"/></svg></div>';

			// Restore
			debug_core_html += '<div class="wsf-debug-restore" data-action="wsf-debug-restore" title="' + this.language('debug_restore') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path d="M12 7h-3v-3h-2v3h-3v2h3v3h2v-3h3z"></path><path d="M15 1h-14v14h14v-14zM14 14h-12v-12h12v12z"></path></svg></div>';

			// Minimize
			debug_core_html += '<div class="wsf-debug-minimize" data-action="wsf-debug-minimize" title="' + this.language('debug_minimize') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M4 7h8v2h-8v-2z"></path><path fill="#444" d="M15 1h-14v14h14v-14zM14 14h-12v-12h12v12z"></path></svg></div>';

			debug_core_html += '</div></div>';

			// Form debug
			$('body').append(debug_core_html);

			// Initial resize
			this.debug_size(0, false, 0);

			// Prevent selection in nav wrapper (Stops scrolling issues when cursor falls below bottom of page)
			$('#wsf-debug-nav-wrapper').on('selectstart dragstart', function(evt){ evt.preventDefault(); return false; });

			// Debug resize event - Mouse
			$('#wsf-debug-nav-wrapper').on('mousedown', function(e){
	
				// Initialize start points
				var debug_screen_y_start = e.screenY;
				ws_this.debug_height_start = $('#wsf-debug').height();
				ws_this.debug_scroll_y_start = $(window).scrollTop();

				$(document).on('mouseup', function(e) {

					ws_this.cookie_set('debug_height', $('#wsf-debug').height(), false, false);

					$(document).off('mouseup').off('mousemove');
				});

				$(document).on('mousemove', function(e) {

					var mouse_y_difference = (debug_screen_y_start - e.screenY);
					ws_this.debug_size(mouse_y_difference, true);
				});
			});

			// Mark debug as rendered
			$.WS_Form.debug_rendered = true;
		}

		// Remove existing instance
		if($('#wsf-debug-nav-' + this.form_instance_id).length) {

			$('#wsf-debug-nav-' + this.form_instance_id).remove();
		}
		if($('#wsf-debug-instance-' + this.form_instance_id).length) {

			$('#wsf-debug-instance-' + this.form_instance_id).remove();
		}

		// Debug - Tab
		var debug_instance_tab = '<li class="wsf-debug-nav" id="wsf-debug-nav-' + this.form_instance_id + '"><a href="#wsf-debug-instance-' + this.form_instance_id + '">' + this.form_instance_id + '. ' + this.html_encode(this.form.label) + '</a></li>';
		$('#wsf-debug-nav').append(debug_instance_tab);

		// Debug - Panel
		var debug_instance = '<div id="wsf-debug-instance-' + this.form_instance_id + '" class="wsf-debug-instance">';

		// Instance - Tabs
		debug_instance += '<ul class="wsf-debug-nav-sub">';
		debug_instance += '<li><a href="#wsf-debug-instance-' + this.form_instance_id + '-tool">' + this.language('debug_tools') + '</a></li>';
		debug_instance += '<li><a href="#wsf-debug-instance-' + this.form_instance_id + '-log">' + this.language('debug_log') + ' (<span>0</span>)</a></li>';
		debug_instance += '<li><a href="#wsf-debug-instance-' + this.form_instance_id + '-error">' + this.language('debug_error') + ' (<span>0</span>)</a></li>';
		debug_instance += '</ul>';

		// Instance - Panels

		// Instance - Panel - Tools
		debug_instance += '<div id="wsf-debug-instance-' + this.form_instance_id + '-tool">';

		// Instance - Panel - Tools - Helpers
		debug_instance += '<ul class="wsf-debug-instance-toolbar">';
		debug_instance += '<li><div data-action="wsf-populate" title="' + this.language('debug_tools_populate') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M16 4c0 0 0-1-1-2s-1.9-1-1.9-1l-1.1 1.1v-2.1h-12v16h12v-8l4-4zM6.3 11.4l-0.6-0.6 0.3-1.1 1.5 1.5-1.2 0.2zM7.2 9.5l-0.6-0.6 5.2-5.2c0.2 0.1 0.4 0.3 0.6 0.5zM14.1 2.5l-0.9 1c-0.2-0.2-0.4-0.3-0.6-0.5l0.9-0.9c0.1 0.1 0.3 0.2 0.6 0.4zM11 15h-10v-14h10v2.1l-5.9 5.9-1.1 4.1 4.1-1.1 2.9-3v6z"></path></svg> ' + this.language('debug_tools_populate') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-submit" title="' + this.language('debug_tools_submit') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M16 7.9l-6-4.9v3c-0.5 0-1.1 0-2 0-8 0-8 8-8 8s1-4 7.8-4c1.1 0 1.8 0 2.2 0v2.9l6-5z"></path></svg> ' + this.language('debug_tools_submit') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-populate-submit" title="' + this.language('debug_tools_populate_submit') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M16 4c0 0 0-1-1-2s-1.9-1-1.9-1l-1.1 1.1v-2.1h-12v16h12v-8l4-4zM6.3 11.4l-0.6-0.6 0.3-1.1 1.5 1.5-1.2 0.2zM7.2 9.5l-0.6-0.6 5.2-5.2c0.2 0.1 0.4 0.3 0.6 0.5zM14.1 2.5l-0.9 1c-0.2-0.2-0.4-0.3-0.6-0.5l0.9-0.9c0.1 0.1 0.3 0.2 0.6 0.4zM11 15h-10v-14h10v2.1l-5.9 5.9-1.1 4.1 4.1-1.1 2.9-3v6z"></path></svg> <svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M16 7.9l-6-4.9v3c-0.5 0-1.1 0-2 0-8 0-8 8-8 8s1-4 7.8-4c1.1 0 1.8 0 2.2 0v2.9l6-5z"></path></svg> ' + this.language('debug_tools_populate_submit') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-save" title="' + this.language('debug_tools_save') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path d="M15.791849,4.41655721 C15.6529844,4.08336982 15.4862083,3.8193958 15.2916665,3.625 L12.3749634,0.708260362 C12.1806771,0.513974022 11.916703,0.347234384 11.5833697,0.208260362 C11.2502188,0.0694322825 10.9445781,0 10.666849,0 L1.00003637,0 C0.722343724,0 0.486171803,0.0971614127 0.291703035,0.291630181 C0.0972342664,0.485989492 0.000109339408,0.722124927 0.000109339408,0.999963514 L0.000109339408,15.0002189 C0.000109339408,15.2781305 0.0972342664,15.5142659 0.291703035,15.7086617 C0.486171803,15.902948 0.722343724,16.0002189 1.00003637,16.0002189 L15.0002553,16.0002189 C15.2782033,16.0002189 15.5143023,15.902948 15.7086981,15.7086617 C15.9029844,15.5142659 16.0001093,15.2781305 16.0001093,15.0002189 L16.0001093,5.3334063 C16.0001093,5.05553123 15.9307135,4.75 15.791849,4.41655721 Z M6.66684898,1.66655721 C6.66684898,1.57629159 6.69986853,1.49832166 6.76587116,1.43220957 C6.83180082,1.36638938 6.90995318,1.3334063 7.0002188,1.3334063 L9.00032825,1.3334063 C9.09037496,1.3334063 9.16849083,1.3663164 9.23445698,1.43220957 C9.30060554,1.49832166 9.33358862,1.57629159 9.33358862,1.66655721 L9.33358862,4.99996351 C9.33358862,5.09037507 9.30038663,5.16845447 9.23445698,5.23445709 C9.16849083,5.30024081 9.09037496,5.33326036 9.00032825,5.33326036 L7.0002188,5.33326036 C6.90995318,5.33326036 6.83176433,5.30035026 6.76587116,5.23445709 C6.69986853,5.16834501 6.66684898,5.09037507 6.66684898,4.99996351 L6.66684898,1.66655721 Z M12.0003647,14.6669221 L4.00003637,14.6669221 L4.00003637,10.6667761 L12.0003647,10.6667761 L12.0003647,14.6669221 Z M14.6672503,14.6669221 L13.3336251,14.6669221 L13.3333697,14.6669221 L13.3333697,10.3334063 C13.3333697,10.0554947 13.2362083,9.81950525 13.0418125,9.62496351 C12.8474167,9.43056772 12.6112813,9.33329685 12.3336251,9.33329685 L3.66673952,9.33329685 C3.38893742,9.33329685 3.1527655,9.43056772 2.95829673,9.62496351 C2.76393742,9.81935931 2.66670303,10.0554947 2.66670303,10.3334063 L2.66670303,14.6669221 L1.33333322,14.6669221 L1.33333322,1.33326036 L2.66666655,1.33326036 L2.66666655,5.66670315 C2.66666655,5.94454174 2.76379148,6.18056772 2.95826024,6.37503649 C3.15272901,6.5693958 3.38890093,6.66666667 3.66670303,6.66666667 L9.66699492,6.66666667 C9.94465108,6.66666667 10.1810419,6.5693958 10.3751823,6.37503649 C10.5694687,6.18067717 10.666849,5.94454174 10.666849,5.66670315 L10.666849,1.33326036 C10.7709792,1.33326036 10.9063046,1.36792177 11.0731537,1.43735406 C11.2399663,1.50674985 11.3579611,1.57618214 11.4273933,1.64561442 L14.3547138,4.57286194 C14.4241096,4.64229422 14.4935784,4.76222271 14.5629742,4.93228255 C14.6326254,5.10248832 14.6672138,5.23620841 14.6672138,5.3334063 L14.6672138,14.6669221 L14.6672503,14.6669221 Z" fill="#444"></path></svg> ' + this.language('debug_tools_save') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-reload" title="' + this.language('debug_tools_reload') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M2.6 5.6c0.9-2.1 3-3.6 5.4-3.6 3 0 5.4 2.2 5.9 5h2c-0.5-3.9-3.8-7-7.9-7-3 0-5.6 1.6-6.9 4.1l-1.1-1.1v4h4l-1.4-1.4z"></path><path fill="#444" d="M16 9h-4.1l1.5 1.4c-0.9 2.1-3 3.6-5.5 3.6-2.9 0-5.4-2.2-5.9-5h-2c0.5 3.9 3.9 7 7.9 7 3 0 5.6-1.7 7-4.1l1.1 1.1v-4z"></path></svg> ' + this.language('debug_tools_reload') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-reset" title="' + this.language('debug_tools_form_reset') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M8 0c-3 0-5.6 1.6-6.9 4.1l-1.1-1.1v4h4l-1.5-1.5c1-2 3.1-3.5 5.5-3.5 3.3 0 6 2.7 6 6s-2.7 6-6 6c-1.8 0-3.4-0.8-4.5-2.1l-1.5 1.3c1.4 1.7 3.6 2.8 6 2.8 4.4 0 8-3.6 8-8s-3.6-8-8-8z"></path></svg> ' + this.language('debug_tools_form_reset') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-clear" title="' + this.language('debug_tools_form_clear') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M8.1 14l6.4-7.2c0.6-0.7 0.6-1.8-0.1-2.5l-2.7-2.7c-0.3-0.4-0.8-0.6-1.3-0.6h-1.8c-0.5 0-1 0.2-1.4 0.6l-6.7 7.6c-0.6 0.7-0.6 1.9 0.1 2.5l2.7 2.7c0.3 0.4 0.8 0.6 1.3 0.6h11.4v-1h-7.9zM6.8 13.9c0 0 0-0.1 0 0l-2.7-2.7c-0.4-0.4-0.4-0.9 0-1.3l3.4-3.9h-1l-3 3.3c-0.6 0.7-0.6 1.7 0.1 2.4l2.3 2.3h-1.3c-0.2 0-0.4-0.1-0.6-0.2l-2.8-2.8c-0.3-0.3-0.3-0.8 0-1.1l3.5-3.9h1.8l3.5-4h1l-3.5 4 3.1 3.7-3.5 4c-0.1 0.1-0.2 0.1-0.3 0.2z"></path></svg> ' + this.language('debug_tools_form_clear') + '</div></li>';
		debug_instance += '<li><div data-action="wsf-identify" title="' + this.language('debug_tools_identify') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M15.7 14.3l-4.2-4.2c-0.2-0.2-0.5-0.3-0.8-0.3 0.8-1 1.3-2.4 1.3-3.8 0-3.3-2.7-6-6-6s-6 2.7-6 6 2.7 6 6 6c1.4 0 2.8-0.5 3.8-1.4 0 0.3 0 0.6 0.3 0.8l4.2 4.2c0.2 0.2 0.5 0.3 0.7 0.3s0.5-0.1 0.7-0.3c0.4-0.3 0.4-0.9 0-1.3zM6 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z"></path></svg> ' + this.language('debug_tools_identify') + '</div></li>';

		// *** TO DO: Capability check ***
		debug_instance += '<li><a href="' + ws_form_settings.admin_url + 'admin.php?page=ws-form-edit&id=' + ws_this.form_id + '" target="_blank" title="' + this.language('debug_tools_edit') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path d="M16 9v-2l-1.7-0.6c-0.2-0.6-0.4-1.2-0.7-1.8l0.8-1.6-1.4-1.4-1.6 0.8c-0.5-0.3-1.1-0.6-1.8-0.7l-0.6-1.7h-2l-0.6 1.7c-0.6 0.2-1.2 0.4-1.7 0.7l-1.6-0.8-1.5 1.5 0.8 1.6c-0.3 0.5-0.5 1.1-0.7 1.7l-1.7 0.6v2l1.7 0.6c0.2 0.6 0.4 1.2 0.7 1.8l-0.8 1.6 1.4 1.4 1.6-0.8c0.5 0.3 1.1 0.6 1.8 0.7l0.6 1.7h2l0.6-1.7c0.6-0.2 1.2-0.4 1.8-0.7l1.6 0.8 1.4-1.4-0.8-1.6c0.3-0.5 0.6-1.1 0.7-1.8l1.7-0.6zM8 12c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="#444"></path><path d="M10.6 7.9c0 1.381-1.119 2.5-2.5 2.5s-2.5-1.119-2.5-2.5c0-1.381 1.119-2.5 2.5-2.5s2.5 1.119 2.5 2.5z" fill="#444"></path></svg> ' + this.language('debug_tools_edit') + '</a></li>';
		debug_instance += '<li><a href="' + ws_form_settings.admin_url + 'admin.php?page=ws-form-submit&id=' + ws_this.form_id + '" target="_blank" title="' + this.language('debug_tools_submissions') + '"><svg height="16" width="16" viewBox="0 0 16 16"><path fill="#444" d="M0 1v15h16v-15h-16zM5 15h-4v-2h4v2zM5 12h-4v-2h4v2zM5 9h-4v-2h4v2zM5 6h-4v-2h4v2zM10 15h-4v-2h4v2zM10 12h-4v-2h4v2zM10 9h-4v-2h4v2zM10 6h-4v-2h4v2zM15 15h-4v-2h4v2zM15 12h-4v-2h4v2zM15 9h-4v-2h4v2zM15 6h-4v-2h4v2z"></path></svg> ' + this.language('debug_tools_submissions') + '</a></li>';

		debug_instance += '</ul>';

		debug_instance += '<div class="wsf-debug-instance-panel">';

		// Instance - Panel - Tools - Info
		debug_instance += '<div class="wsf-debug-instance-info wsf-debug-table"><table><tbody></tbody></table></div>';

		debug_instance += '</div>';

		debug_instance += '</div>';

		// Instance - Panel - Log
		debug_instance += this.debug_audit_html('log');

		// Instance - Panel - Error
		debug_instance += this.debug_audit_html('error');

		debug_instance += '</div>';

		$('#wsf-debug').append(debug_instance);

		// Tools
		var form_obj = $('.wsf-form-canvas[data-instance-id="' + this.form_instance_id + '"]');
		var tools_obj = $('#wsf-debug-instance-' + this.form_instance_id + '-tool');
		var log_obj = $('#wsf-debug-instance-' + this.form_instance_id + '-log');
		var errors_obj = $('#wsf-debug-instance-' + this.form_instance_id + '-error');

		// Tools - Info
		this.debug_info('debug_info_label', this.form.label);
		this.debug_info('debug_info_id', this.form_id);
		this.debug_info('debug_info_instance', this.form_instance_id);
		this.debug_info('debug_info_hash', (this.hash == '' ? this.language('debug_hash_empty') : this.hash), ((this.hash == '') ? '' : 'clear_hash'));
		this.debug_info('debug_info_checksum', this.form.published_checksum, ((this.form.published_checksum != this.form.checksum) ? 'publish' : ''));
		this.debug_info('debug_info_framework', this.framework.name);
		this.debug_info('debug_info_duration', '-');
		this.debug_info('debug_info_submit_count', '-');
		this.debug_info('debug_info_submit_duration_user', '-');
		this.debug_info('debug_info_submit_duration_client', '-');
		this.debug_info('debug_info_submit_duration_server', '-');
//		this.debug_info('debug_info_anti_spam_score', '-');

		// Tabs - Sub
		var debug_instance_cookie_id = 'tab_index_' + this.form_instance_id;
		var debug_instance_tab_index = this.cookie_get(debug_instance_cookie_id, 0, false);

		var form_instance_id = this.form_instance_id;
		ws_this.tabs($('#wsf-debug-instance-' + this.form_instance_id + ' .wsf-debug-nav-sub'), {

			active: debug_instance_tab_index,
			activate: function(tab_index) {

				ws_this.cookie_set(debug_instance_cookie_id, tab_index, false, false);
			}
		});

		// Tools actions

		// Tools - Populate
		$('[data-action="wsf-populate"]', tools_obj).on('click', function() {

			ws_this.debug_populate(form_obj);
		});

		// Tools - Reset form
		$('[data-action="wsf-reset"]', tools_obj).on('click', function() {

			ws_this.debug_form_reset();
		});

		// Tools - Clear form
		$('[data-action="wsf-clear"]', tools_obj).on('click', function() {

			ws_this.debug_form_clear();
		});

		// Tools - Reload
		$('[data-action="wsf-reload"]', tools_obj).on('click', function() {

			ws_this.form_reload();
		});

		// Tools - Identify
		$('[data-action="wsf-identify"]', tools_obj).on('click', function() {

			form_obj.toggleClass('wsf-form-identify');
			if(form_obj.hasClass('wsf-form-identify')) {

				$('body, html').scrollTop(ws_this.form_obj.offset().top);
			}
		});

		// Tools - Save
		$('[data-action="wsf-save"]', tools_obj).on('click', function() {

			ws_this.form_post('save');
		});

		// Tools - Populate & Submit
		$('[data-action="wsf-populate-submit"]', tools_obj).on('click', function() {

			ws_this.debug_populate(form_obj);

			// Short pause to allow browser rendering to complete
			setTimeout(function() {

				form_obj.submit();

			}, 200);
		});

		// Tools - Submit
		$('[data-action="wsf-submit"]', tools_obj).on('click', function() {

			form_obj.submit();
		});

		// Log - Clear log
		$('[data-action="wsf-clear-log"]', log_obj).on('click', function() {

			ws_this.debug_audit_clear('log');
		});

		// Errors - Clear errors
		$('[data-action="wsf-clear-error"]', errors_obj).on('click', function() {

			ws_this.debug_audit_clear('error');
		});

		// All instances rendered
		var debug_tab_count = $('#wsf-debug-nav li').length;
		var form_count = $('.wsf-form-canvas[data-instance-id]').length;
		if(debug_tab_count == form_count) {

			// Read tab index from cookie
			var debug_tab_index = this.cookie_get('tab_index', 0, false);
			if(debug_tab_index > (form_count - 1)) { debug_tab_index = 0; }

			// Init (tabs)
			ws_this.tabs($('#wsf-debug-nav'), {

				active: debug_tab_index,
				activate: function(tab_index) {

					ws_this.cookie_set('tab_index', tab_index, false, false);
				}
			});

			// Final resize
			this.debug_size(0, true, 0);

			// Restore
			$('[data-action="wsf-debug-restore"]', $('#wsf-debug')).on('click', function() {

				// Calculate minimize height
				var header_height = $('#wsf-debug-nav-wrapper').outerHeight();
				header_height += $('.wsf-debug-nav-sub').first().outerHeight();
				var active_nav_primary = $($('#wsf-debug-nav .ui-tabs-active > a').attr('href'));
				var active_nav_secondary = $($('.ui-tabs-active > a', active_nav_primary).attr('href'));
				active_nav_secondary.find('.wsf-debug-instance-toolbar').each(function() { header_height += $(this).outerHeight(); });
				header_height += $('.wsf-debug-instance-toolbar').first().outerHeight();

				// Add info table height
				header_height += 220;

				// Set height
				ws_this.debug_size(0, true, header_height);

				// Save to cookie
				ws_this.cookie_set('debug_height', header_height, false, false);
			});

			// Minimize
			$('[data-action="wsf-debug-minimize"]', $('#wsf-debug')).on('click', function() {

				// Calculate minimize height
				var header_height = $('#wsf-debug-nav-wrapper').outerHeight();
				header_height += $('.wsf-debug-nav-sub').first().outerHeight();
				var active_nav_primary = $($('#wsf-debug-nav .ui-tabs-active > a').attr('href'));
				var active_nav_secondary = $($('.ui-tabs-active > a', active_nav_primary).attr('href'));
				active_nav_secondary.find('.wsf-debug-instance-toolbar').each(function() { header_height += $(this).outerHeight(); });

				// Set height
				ws_this.debug_size(0, true, header_height);

				// Save to cookie
				ws_this.cookie_set('debug_height', header_height, false, false);
			});

			// Close
			$('[data-action="wsf-debug-close"]', $('#wsf-debug')).on('click', function() {

				if(ws_form_settings.wsf_nonce) {

					if(confirm(ws_this.language('debug_close_confirm'))) {

						ws_this.debug_close();
					}

				} else {

					ws_this.debug_close();
				}
			});
		}

		// Expose error function
		this.form_canvas_obj[0].ws_form_log_error = function(language_id, variable, log_class) { ws_this.error(language_id, variable, log_class); }
	}

	// Debug - Close
	$.WS_Form.prototype.debug_close = function() {

		// Remove debug element
		$('#wsf-debug').remove();

		// If logged in, call API to disable 
		if(!ws_form_settings.wsf_nonce) { return; }

		// NONCE
		var data = {};
		data[ws_form_settings.wsf_nonce_field_name] = ws_form_settings.wsf_nonce;

		// Call AJAX
		var ajax_request = {

			method: 'POST',
			url: ws_form_settings.url_ajax + 'helper/debug/close/',
			beforeSend: function(xhr) {

				// Nonce (X-WP-Nonce)
				if(ws_form_settings.x_wp_nonce) {

					xhr.setRequestHeader('X-WP-Nonce', ws_form_settings.x_wp_nonce);
				}
			},
			data: data
		};

		return $.ajax(ajax_request);
	}

	// Debug - Panel heights
	$.WS_Form.prototype.debug_panel_heights = function() {

		// Set panel heights
		var ws_this = this;
		var header_height_base = $('#wsf-debug-nav-wrapper').outerHeight();
		header_height_base += $('.wsf-debug-nav-sub').first().outerHeight();

		$('#wsf-debug .wsf-debug-instance').each(function() {

			var header_height = header_height_base;
			$(this).find('.wsf-debug-instance-toolbar').each(function() { header_height += $(this).outerHeight(); });
			$(this).find('.wsf-debug-instance-panel').each(function() { $(this).height(ws_this.debug_height - header_height + 'px'); });
		});
	}

	// Debug - Tools - Info
	$.WS_Form.prototype.debug_info = function(header, data, action) {

		var tools_obj = $('#wsf-debug-instance-' + this.form_instance_id + '-tool');
		var ws_this = this;

		// Add action icon
		var action_html = '';
		if(typeof action !== 'undefined') {

			switch(action) {

				case 'clear_hash' :

					action_html = '<div class="wsf-debug-instance-info-action" data-action="wsf-clear-hash" title="' + this.language('debug_tools_clear_hash') + '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16"><path fill="#444" d="M8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM8 2c1.3 0 2.5 0.4 3.5 1.1l-8.4 8.4c-0.7-1-1.1-2.2-1.1-3.5 0-3.3 2.7-6 6-6zM8 14c-1.3 0-2.5-0.4-3.5-1.1l8.4-8.4c0.7 1 1.1 2.2 1.1 3.5 0 3.3-2.7 6-6 6z"></path></svg></div>';
					break;

				case 'publish' :

					action_html = '<div class="wsf-debug-instance-info-action wsf-debug-publish-pending">' + this.language('debug_tools_publish_pending') + '</div>';
					break;
			}
		}

		// Render data
		if($('[data-id="' + header + '"]', tools_obj).length) {

			// Existing info line
			$('[data-id="' + header + '"] td', tools_obj).html(this.html_encode(data) + action_html);
	
		} else {

			// New info line
			var row = '<tr data-id="' + header + '"><th>' + this.html_encode(this.language(header)) + '</th><td>' + this.html_encode(data) + action_html + '</td></tr>';
			$('tbody', tools_obj).append(row);
		}

		// Add action events
		if(typeof action !== 'undefined') {

			switch(action) {

				case 'clear_hash' :

					// Tools - Clear hash
					$('[data-action="wsf-clear-hash"]', tools_obj).on('click', function() {

						ws_this.debug_clear_hash();
					});

					break;
			}
		}
	}

	// Debug - Audit HTML
	$.WS_Form.prototype.debug_audit_html = function(type) {

		// Instance - Panel - Error
		var audit_html = '<div id="wsf-debug-instance-' + this.form_instance_id + '-' + type +'" class="wsf-debug-instance">';

		audit_html += '<ul class="wsf-debug-instance-toolbar">';
		audit_html += '<li><div data-action="wsf-clear-' + type + '" title="' + this.language('debug_tools_clear_' + type) + '"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16"><path fill="#444" d="M8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM8 2c1.3 0 2.5 0.4 3.5 1.1l-8.4 8.4c-0.7-1-1.1-2.2-1.1-3.5 0-3.3 2.7-6 6-6zM8 14c-1.3 0-2.5-0.4-3.5-1.1l8.4-8.4c0.7 1 1.1 2.2 1.1 3.5 0 3.3-2.7 6-6 6z"></path></svg></div></li>';
		audit_html += '</ul>';

		audit_html += '<div class="wsf-debug-instance-panel">';

		audit_html += '<table><thead><tr><th>Time</th><th>Event</th></tr></thead><tbody></tbody></table>';

		audit_html += '</div>';

		audit_html += '</div>';

		return audit_html;
	}

	// Debug - Audit add
	$.WS_Form.prototype.debug_audit_add = function(audit_type, audit_message, audit_class) {

		var ws_this = this;

		if(typeof(audit_class) === 'undefined') { audit_class = ''; }

		// Build HTML for row
		var audit_html = '<tr' + ((audit_class != '') ? (' class="wsf-audit-' + audit_class + '"') : '') + '><td>' + this.debug_get_date_time() + '</td><td>' + audit_message + "</td></tr>\n";

		// Add to array
		if(audit_type === 'log') { this.debug_log_array.unshift(audit_html); }
		if(audit_type === 'error') { this.debug_error_array.unshift(audit_html); }

		// Render after setTimeout
		if(!this.debug_timeout) {

			this.debug_timeout = setTimeout(function() {

				if(ws_this.debug_log_array.length) {

					var audit_tbody_obj = $('#wsf-debug-instance-' + ws_this.form_instance_id + '-log tbody');
					audit_tbody_obj.prepend(ws_this.debug_log_array.join(''));
					ws_this.debug_log_array = [];
					ws_this.debug_audit_tab('log');
				}

				if(ws_this.debug_error_array.length) {

					var audit_tbody_obj = $('#wsf-debug-instance-' + ws_this.form_instance_id + '-error tbody');
					audit_tbody_obj.prepend(ws_this.debug_error_array.join(''));
					ws_this.debug_error_array = [];
					ws_this.debug_audit_tab('error');
				}

				ws_this.debug_timeout = null;

			}, 500);
		}

		// Increment audit counter
		this.audit_count[audit_type]++;
	}

	// Debug - Audit clear
	$.WS_Form.prototype.debug_audit_clear = function(audit_type) {

		// Clear audit table
		$('#wsf-debug-instance-' + this.form_instance_id + '-' + audit_type + ' tbody tr').remove();

		// Reset audit counter
		this.audit_count[audit_type] = 0;

		// Update audit tab
		this.debug_audit_tab(audit_type);
	}

	// Debug - Audit tab
	$.WS_Form.prototype.debug_audit_tab = function(audit_type) {

		var audit_tab_obj = $('a[href="#wsf-debug-instance-' + this.form_instance_id + '-' + audit_type + '"]');
		if(this.audit_count[audit_type] == 0) { audit_tab_obj.removeClass('wsf-has-' + audit_type); }
		if(this.audit_count[audit_type] == 1) { audit_tab_obj.addClass('wsf-has-' + audit_type); }
		$('a[href="#wsf-debug-instance-' + this.form_instance_id + '-' + audit_type + '"] span').html(this.audit_count[audit_type]);
	}

	// Get date / time string
	$.WS_Form.prototype.debug_get_date_time = function() {

		var date = new Date();
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var seconds = date.getSeconds();
		var ampm = hours >= 12 ? 'pm' : 'am';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0' + minutes : minutes;
		seconds = seconds < 10 ? '0' + seconds : seconds;
		var strTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;

		return date.getMonth()+1 + "/" + date.getDate() + "/" + date.getFullYear() + "  " + strTime;
	}

	// Debug - Populate
	$.WS_Form.prototype.debug_populate = function() {

		var password = false;
		var ws_this = this;

		var native_date_format = !(

			// Use date/time picker
			($.WS_Form.settings_plugin.ui_datepicker == 'on') ||

			// If browser does not support native date/time picked, use datetimepicker
			(
				($.WS_Form.settings_plugin.ui_datepicker == 'native') &&
				!this.native_date
			)

		);

		// Tel prefixes
		var tel_area_codes = [403,907,205,256,334,251,870,501,479,480,623,928,602,520,628,341,764,925,909,562,661,657,510,650,949,760,415,951,752,831,209,669,408,559,626,442,530,916,707,627,714,310,323,213,424,747,818,858,935,619,805,369,720,303,970,719,203,959,475,860,202,302,689,407,239,836,727,321,754,954,352,863,904,386,561,772,786,305,861,941,813,850,478,770,470,404,706,678,912,229,671,808,515,319,563,641,712,208,217,282,872,312,773,464,708,815,224,847,618,309,331,630,765,574,260,219,317,812,913,785,316,620,327,502,859,606,270,504,985,225,318,337,774,508,781,339,857,617,978,351,413,443,410,280,249,969,240,301,207,383,517,546,810,278,313,586,248,734,269,906,989,616,231,679,947,612,320,651,763,952,218,507,636,660,975,816,314,557,573,417,670,601,662,228,406,336,252,984,919,980,910,828,704,701,402,308,603,908,848,732,551,201,862,973,609,856,505,957,702,775,315,518,716,585,646,347,718,212,516,917,845,631,607,914,216,330,234,567,419,380,440,740,614,283,513,937,918,580,405,503,971,541,814,717,570,358,878,835,484,610,445,267,215,724,412,939,787,401,843,864,803,605,423,865,931,615,901,731,254,325,713,940,817,430,903,806,737,512,361,210,936,409,979,972,469,214,682,832,281,830,956,432,915,435,801,385,434,804,757,703,571,540,276,381,236,802,509,360,564,206,425,253,715,920,414,262,608,353,420,304,307];
		var tel_area_code = tel_area_codes[Math.floor(Math.random() * tel_area_codes.length)];
		var tel_mid = this.debug_pad(this.debug_random_number(100, 999), 3);

		for(var key in this.field_data_cache) {

			if(!this.field_data_cache.hasOwnProperty(key)) { continue; }

			// Get field connfig
			var field = this.field_data_cache[key];

			// Bypass? (Because field has calculated value)
			if(this.debug_field_id_bypass.includes(field.id)) {

				continue;
			}

			var field_repeatable = (typeof(field.section_repeatable_section_id) !== 'undefined');
			var field_repeatable_suffix = field_repeatable ? '-repeat-' : '';
			var field_label = field.label;
			var field_name_source = ws_form_settings.field_prefix + field.id;
			var form_obj = $('.wsf-form-canvas[data-instance-id="' + this.form_instance_id + '"]');

			// Input mask
			var input_mask = this.get_object_meta_value(field, 'input_mask', '');

			// Find all instances of field
			var field_objs = $('[id^="' + this.form_id_prefix + 'field-wrapper-' + field.id + field_repeatable_suffix + '"]');
			field_objs.each(function() {

				// Bounds
				var min = ws_this.get_object_meta_value(field, 'min', '');
				var max = ws_this.get_object_meta_value(field, 'max', '');
				var step = ws_this.get_object_meta_value(field, 'step', '');
				var min_length = ws_this.get_object_meta_value(field, 'min_length', '');
				var max_length = ws_this.get_object_meta_value(field, 'max_length', '');

				// Check for repeatable index, if found, append array index to field_name
				var field_data_repeatable_index = $(this).attr('data-repeatable-index');
				var field_data_repeatable_index = (typeof(field_data_repeatable_index) !== 'undefined') ? field_data_repeatable_index : false;
				var field_name = field_name_source + ((field_data_repeatable_index !== false) ? '[' + field_data_repeatable_index + ']' : '');

				var populate_obj = $('[name="' + field_name + '"]:enabled', form_obj);
				var populate_array_obj = $('[name="' + field_name + '[]"]:enabled', form_obj);
				var populate_obj_required = $('[name="' + field_name + '"]:required', form_obj);
				var populate_array_obj_required = $('[name="' + field_name + '[]"]:required', form_obj);
				var populate_ecommerce_price = $('[name="' + field_name + '"][data-ecommerce-price]', form_obj).length;

				// Do not populate data-required-bypass fields
				var populate_obj_data_required_bypass = $('[name="' + field_name + '"][data-required-bypass],[name="' + field_name + '"][data-required-bypass-section],[name="' + field_name + '"][data-required-bypass-group]', form_obj);
				if(populate_obj_data_required_bypass.length) { return; }

				// Build populate value based on field type
				var populate = false;
				switch(field.type) {

					case 'text' :
					case 'hidden' :
					case 'search' :

						var field_label_lowercase = field_label.toLowerCase();
						var populate_override = false;

						// Username
						var username_labels = ['username', 'user name', 'user_name', 'login'];
						for(var username_label_index in username_labels) {

							if(!username_labels.hasOwnProperty(username_label_index)) { continue; }

							var username_label = username_labels[username_label_index];
							if(field_label_lowercase.indexOf(username_label) != -1) {

								populate_override = ws_this.debug_get_word() + (Math.floor(Math.random()*900000) + 100000);
								break;
							}
						}

						// Zip code
						var zip_code_labels = ['zip', 'zip code', 'zip_code', 'zipcode'];
						for(var zip_code_label_index in zip_code_labels) {

							if(!zip_code_labels.hasOwnProperty(zip_code_label_index)) { continue; }

							var zip_code_label = zip_code_labels[zip_code_label_index];
							if(field_label_lowercase.indexOf(zip_code_label) != -1) {

								populate_override = Math.floor(Math.random()*90000) + 10000;
								break;
							}
						}

						// IP address
						var ip_address_labels = ['ip address', 'ip_address', 'ipaddress', 'ipv4'];
						for(var ip_address_label_index in ip_address_labels) {

							if(!ip_address_labels.hasOwnProperty(ip_address_label_index)) { continue; }

							var ip_address_label = ip_address_labels[ip_address_label_index];
							if(field_label_lowercase.indexOf(ip_address_label) != -1) {

								populate_override = (Math.floor(Math.random() * 255) + 1)+"."+(Math.floor(Math.random() * 255) + 0)+"."+(Math.floor(Math.random() * 255) + 0)+"."+(Math.floor(Math.random() * 255) + 0);
								break;
							}
						}

						// Check for populate override
						if(populate_override !== false) { populate = populate_override; break; }

						// Other
						if(max_length == '') { max_length = 16; }
						max_length = parseInt(max_length, 10);
						if(max_length < 1) { max_length = 16; }
						if(max_length < min_length) { max_length = min_length; }
						populate = ws_this.debug_get_sentence(min_length, max_length, false);
						break;

					case 'textarea' :

						if(max_length == '') { max_length = 256; }
						max_length = parseInt(max_length, 10);
						if(max_length < 1) { max_length = 256; }
						if(max_length < min_length) { max_length = min_length; }
						populate = ws_this.debug_get_sentence(min_length, max_length);

						// Set if textarea is TinyMCE or HTML editor
						if(typeof(ws_this.textarea_set_value) === 'function') { ws_this.textarea_set_value(populate_obj, populate); }

						break;

					case 'password' :

						if(!max_length) { max_length = 16; }
						max_length = parseInt(max_length, 10);
						if(max_length < 1) { max_length = 16; }
						if(password === false) { password = ((typeof(ws_this.generate_password) === 'function') ? ws_this.generate_password(max_length) : ''); }
						populate = password;
						break;

					case 'email' :

						populate = ws_this.debug_get_word() + '.' + ws_this.debug_get_word() + '@' + ws_this.debug_get_word() + '.com';
						break;

					case 'rating' :

						var rating_max = parseInt(ws_this.get_object_meta_value(field, 'rating_max', '5'), 10);
						if(rating_max <= 1) { rating_max = 1; }

						min = 0;
						max = rating_max;
						step = 1;
						populate = ws_this.debug_random_number(min, max, step);
						break;

					case 'number' :
					case 'price_range' :
					case 'range' :
					case 'quantity' :

						if(min == '') { min = 0; }
						if(max == '') { max = 100; }
						if(step == '') { step = 1; }
						min = parseFloat(min);
						max = parseFloat(max);
						step = (step == 'any') ? 0.0001 : parseFloat(step);
						populate = ws_this.debug_random_number(min, max, step);
						break;

					case 'price' :
					case 'cart_price' :

						if(min == '') { min = 0; }
						if(max == '') { max = 100; }
						if(step == '') { step = populate_ecommerce_price ? 0.01 : 1; }
						min = parseFloat(min);
						max = parseFloat(max);
						step = (step == 'any') ? 0.01 : parseFloat(step);
						populate = ws_this.get_price(ws_this.debug_random_number(min, max, step));
						break;

					case 'url' :

						populate = 'http://www.' + ws_this.debug_get_word() + '.com';
						break;

					case 'tel' :

						// Check for input mask
						switch(input_mask) {

							case '999 999-9999' :

								populate = tel_area_code + ' ' + tel_mid + '-' + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
								break;

							case '+1 999 999-9999' :

								populate = '+1 ' + tel_area_code + ' ' + tel_mid + '-' + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
								break;

							case '9999999999' :

								populate = '' + tel_area_code + tel_mid + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
								break;

							case '+19999999999' :

								populate = '+1' + tel_area_code + tel_mid + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
								break;

							case '+1 (999) 999-9999' :

								populate = '(' + tel_area_code + ') ' + tel_mid + '-' + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
								break;

							default :

								populate = '+1 (' + tel_area_code + ') ' + tel_mid + '-' + ws_this.debug_pad(ws_this.debug_random_number(1000, 9999), 4);
						}

						break;

					case 'select' :
					case 'price_select' :

						var random_offset = (ws_this.get_object_meta_value(field, 'placeholder_row', '') != '') ? 1 : 0;
						var options = populate_array_obj.find('option:enabled');
						var random = random_offset + Math.floor((options.length - random_offset) * (Math.random() % 1));
						populate_array_obj.find('option:enabled').prop('selected',false).eq(random).prop('selected',true);
						populate_array_obj.trigger('change');

						break;

					case 'checkbox' :
					case 'price_checkbox' :

						populate_array_obj.each(function() {

							$(this).prop('checked', (Math.random() >= 0.5)).trigger('change');
						})
						populate_array_obj_required.prop('checked',true).trigger('change');

						break;

					case 'radio' :
					case 'price_radio' :

						var random = Math.floor(populate_array_obj.length * (Math.random() % 1));
						populate_array_obj.prop('checked',false).eq(random).prop('checked',true).trigger('change');

						break;

					case 'color' :

						var r = ws_this.debug_random_number(0, 255).toString(16);
						var g = ws_this.debug_random_number(0, 255).toString(16);
						var b = ws_this.debug_random_number(0, 255).toString(16);

						if(r.length < 2) { r = '0' + r; }
						if(g.length < 2) { g = '0' + g; }
						if(b.length < 2) { b = '0' + b; }

						populate = '#' + r + g + b;

						// Minicolors
						if(typeof(populate_obj.minicolors) === 'function') {

							populate_obj.minicolors('value', populate);
						}

						break;

					case 'datetime' :

						// Get min date
						var min_date = ((typeof(ws_this.parse_date_min_max) === 'function') ? ws_this.parse_date_min_max(populate_obj, 'min') : '');

						// Get max date
						var max_date = ((typeof(ws_this.parse_date_min_max) === 'function') ? ws_this.parse_date_min_max(populate_obj, 'max') : '');

						// Calculate random date
						var current_year = new Date().getFullYear();
						var min = (min_date == '') ? new Date(current_year - 50, 0, 1) : min_date;
						var max = (max_date == '') ? new Date(current_year + 50, 0, 1) : max_date;
						var populate_date = ws_this.debug_random_date(min, max);

						// Year
						var populate_year = populate_date.getFullYear();

						// Month
						var populate_month = populate_date.getMonth() + 1;
						if(populate_month < 10) populate_month = '0' + populate_month;

						// Day
						var populate_day = populate_date.getDate();
						if(populate_day < 10) populate_day = '0' + populate_day;

						// Hour
						var populate_hour = populate_date.getHours();
						if(populate_hour < 10) populate_hour = '0' + populate_hour;

						// Minute
						var populate_minute = populate_date.getMinutes();
						if(populate_minute < 10) populate_minute = '0' + populate_minute;

						// Week
						var populate_week = ws_this.debug_random_number(1, 52);
						if(populate_week < 10) populate_week = '0' + populate_week;

						// Get date format
						var date_format = populate_obj.attr('data-date-format');
						if(!date_format) { date_format = ws_form_settings.date_format; }

						// Get time format
						var time_format = populate_obj.attr('data-time-format');
						if(!time_format) { time_format = ws_form_settings.time_format; }

						// Create populate string
						var datetime_type = populate_obj.attr('data-date-type');

						switch(datetime_type) {

							case 'month' :

								if(native_date_format) {

									populate = populate_year + '-' + populate_month;

								} else {

									populate = ((typeof(ws_this.date_format) === 'function') ? ws_this.date_format(populate_date, 'F Y') : '');
								}
								break;

							case 'time' :

								if(native_date_format) {

									populate = populate_hour + ':' + populate_minute;

								} else {

									populate = ((typeof(ws_this.date_format) === 'function') ? ws_this.date_format(populate_date, time_format) : '');
								}
								break;

							case 'week' :

								if(native_date_format) {

									populate = populate_year + '-W' + populate_week;

								} else {

									populate = ((typeof(ws_this.date_format) === 'function') ? ws_this.date_format(populate_date, '\\W\\e\\e\\k W, Y') : '');
								}
								break;

							case 'date' :

								if(native_date_format) {

									populate = populate_date.toISOString().split('T')[0];

								} else {

									populate = ((typeof(ws_this.date_format) === 'function') ? ws_this.date_format(populate_date, date_format) : '');
								}
								break;

							default :

								if(native_date_format) {

									populate = populate_year + '-' + populate_month + '-' + populate_day + 'T' + populate_hour + ':' + populate_minute;
	
								} else {

									populate = ws_this.date_format(populate_date, date_format + ' ' + time_format);
								}
						}

						break;

					case 'signature' :

						var signature_populate_data = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzA0IiBoZWlnaHQ9IjEyNiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGRlZnM+PHBhdGggaWQ9ImEiIGQ9Ik0yOS4yNTkuMTk2djM4LjMzMkguNzA1Vi4xOTZ6Ii8+PC9kZWZzPjxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0iTTM0LjEyOCA2My4wMzZjLTIuMDk0IDEuNzIyLTQuMTQgMy41MDQtNi4yOSA1LjE1NS05Ljk1OCA3LjY0OC0xNy43NTIgMTYuODE3LTIyLjIzIDI4LjgyNi0yLjYyNiA3LjA0LTIuNjYgMTMuNjMxIDEuOTczIDE5LjMwMiA0LjY4MyA1LjczMSAxMS43NjIgNS44MTMgMTguMzM2IDQuNDA4IDguMTc3LTEuNzQ5IDE1Ljk2NS01LjAyNSAyMS40NS0xMS42OTEgNy44MDMtOS40ODUgMTUuNzI4LTE4Ljk1NCAyMi41NDctMjkuMTM1IDQuMDg4LTYuMTA2IDYuNDMyLTEzLjQ1NSA5LjA5MS0yMC40MjQgNS4zODctMTQuMTIgMTAuNDYtMjguMzU4IDE1LjYzOC00Mi41NTcuMjY4LS43MzMuMzE0LTEuNTQ2LjQyLTIuMDg4bC00My43ODcgNS43OSA2LjA1NiA2Ljc1OWMtNC44Mi40MDMtMTIuMzYyLTQuMzA0LTE1LjI5NC05LjgyNyAyLjUwNy0uNTk4IDQuNzU0LTEuNDAzIDcuMDYtMS42MzkgMTMuMTM0LTEuMzQ1IDI2LjMwOC0yLjM0OCAzOS40MTQtMy45MjEgNC42Ny0uNTYgOS41MzUtMS42MjggMTEuMTE0LTcuNTc1LjQ0LTEuNjU1IDIuNDgxLTMuMTY0IDQuMTQ1LTQuMTEuOTgtLjU1NiAyLjk5NS0uMzIgMy45MzMuMzguODgyLjY1OSAxLjQ4NyAyLjQ5NyAxLjI2NSAzLjYzLS4zIDEuNTM0LTEuMTc1IDMuNzAyLTIuMzc3IDQuMTkyLTcuNzI0IDMuMTQ4LTguNzkzIDEwLjgxOC0xMS40NzkgMTcuMDQ2LTYuODg4IDE1Ljk2OC0xMi45OCAzMi4yNzgtMTkuNTY3IDQ4LjM4LTYuNDA4IDE1LjY2Ni0xNS4yNyAyOS44MDItMjguNSA0MC41NjItOC45MDYgNy4yNDMtMTkuMTczIDExLjcwNy0zMS4xNjIgMTAuNTY3QzMuNzIyIDEyMy45MDktMS45MDYgMTEwLjU5LjU3NSAxMDEuNWMzLjYxNi0xMy4yNTYgMTEuMDA1LTIzLjk1MSAyMi4yMi0zMS45OTEgMi4yODgtMS42NDEgNC4zLTMuNjY1IDYuNDgtNS40NiAxLjE1LS45NDYgMi4zOTMtMS43NzkgMy41OTMtMi42NjNsMS4yNiAxLjY1TTEzNS4zMzggMjQuMTA2YzQuNDc0IDEuNTQ0IDQuNDM1IDMuODQzIDIuOSA3LjE3Ni0zLjYwNiA3Ljg0LTYuOTIgMTUuODE1LTEwLjI4NyAyMy43NjMtLjQ5NyAxLjE3Mi0uNjM3IDIuNDk0LTEuMDUyIDQuMTk3IDQuMjQtMy44NCA4LjA3OS03LjMxNCAxMS45MTctMTAuNzg4bDIuMzQzIDEuMTc3Yy0uNTYyIDMuMTg3LTEuMTAzIDYuMzc3LTEuNjkxIDkuNTU5LTEuMDA4IDUuNDYtMi45MTIgMTAuOTMtMi44MzUgMTYuMzcyLjA4NCA1Ljg2OSAzLjMxMiA3LjAwNCA5LjA3IDQuMTI1LTQuNTUtNS45NTIgMi43MTMtOS45MyAyLjU5OS0xNS4zNDYtLjA1My0yLjU1NC44MDUtNS4xOTMgMS42MTYtNy42NzQuNDY4LTEuNDM0IDEuNTE4LTIuNzgyIDIuNjE5LTMuODQ0LjM3My0uMzYxIDIuMzguMDk1IDIuNDc4LjQ3OC4zNjggMS40MzIuNTg1IDMuMDY2LjI0OSA0LjQ3OS0uNTI3IDIuMjItMS41NSA0LjMyLTEuNTYzIDcuMTUgNC44MjgtNC43NTggOS41NjYtOS42MTQgMTQuNTUtMTQuMjAyIDEuMTk3LTEuMTAzIDMuMjEzLTEuMzE2IDQuODUzLTEuOTM4LjI2NyAxLjcxOCAxLjE5IDMuNjU5LjY5NCA1LjExOS0zLjE5OCA5LjQyNS02LjY1NCAxOC43NjUtMTAuMTk2IDI4LjA2Ny0uMzUuOTE2LTEuODUzIDEuMzktMi44MjEgMi4wNy0uMzY1LTEuMDc0LTEuMjg1LTIuMzI4LTEuMDA2LTMuMTk0IDEuOTktNi4xNyA0LjIzNy0xMi4yNTQgNi4zNy0xOC4zNzYuNDItMS4yMDUuNzA2LTIuNDU2LjE1My00LjMwOGwtMTYuNjc3IDE3LjQ2Yy4yOS4zMTQuNTguNjI3Ljg3Mi45NGw0LjExNi0yLjU0MmMtMi4wNzMgNy40LTEyLjQ5OCAxMy4zOTctMTguMjcgMTAuODM1LTMuMjMxLTEuNDM2LTUuNDA4LTYuNzE1LTQuNzgyLTExLjQwMS4yODUtMi4xMzMuMzAzLTQuMzA0LjYzNS02LjQyOC4zMjItMi4wNi44OTItNC4wOCAxLjM1Mi02LjExNWwtMS41NDEtLjc4OGMtMi42OTQgNC4zNS01LjIyNSA4LjgxNS04LjE1MiAxMy4wMDItMS4zOTggMi0xLjM1NiA2Ljc2OC01LjMgNC45NzUtMy44LTEuNzI4LS42NTQtNS4xMDUuMTQtNy4zNTggNC4xODgtMTEuODcgOC43OS0yMy41OTIgMTMuMTM4LTM1LjQwNiAxLjMzNi0zLjYzIDIuMzE5LTcuMzkgMy41MDktMTEuMjM2TTIxMC41NiA2Ni43NzZsMS4yMy44NTRjMy4wOTctMi4zMzQgNi4xMy00Ljc2MiA5LjMwMy02Ljk4NiAxMi4wNC04LjQzNiAyMS4yMzMtMTkuNTI1IDI5LjQ0Ni0zMS41MTQgMi40NDgtMy41NzQuOTU1LTYuMjQ4LTIuNjI5LTcuODMzLTcuMzE5LTMuMjM2LTE0LjcxMi0yLjU5Mi0yMS42MTktLjUxMWwtMTUuNzMgNDUuOTltLTUuNjAyIDYuNjQ0YzQuODctMTUuODU3IDkuODc3LTMxLjY3IDE0Ljc5LTQ3LjUxMi4yNzItLjg3Ny40NC0yLjMxNi0uMDQzLTIuODE1LTQuNDY5LTQuNjE0LS4zMzctNS4zNiAyLjgzMy02LjQxNCA5Ljc1MS0zLjI0NyAxOS4zNC0yLjEzNiAyOC41OTMgMS43NTkgNS44NzQgMi40NzMgNy4yNjIgNi4wOCAzLjczNiAxMS40MjQtNC4xODUgNi4zNDMtOC44MDggMTIuNjE4LTE0LjI2MyAxNy44NTctMTEuMDA1IDEwLjU3Mi0yMi43IDIwLjQyMi0zNC4xMTIgMzAuNTdsLTEuNTkyLS4xODhjMC0xLjU2Ni0uMzgtMy4yNS4wNTgtNC42ODF6IiBmaWxsPSIjMDAwIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjc0IDQxLjI0MykiPjxtYXNrIGlkPSJiIiBmaWxsPSIjZmZmIj48dXNlIHhsaW5rOmhyZWY9IiNhIi8+PC9tYXNrPjxwYXRoIGQ9Ik05LjM1MiAxNC40NjZsMi40ODcgMi40M0wyMi40OCA2LjE4MmwtMi45NzUtMi45NjdMOS4zNTIgMTQuNDY2bTE5LjkwNyAxMi4xMmMtNS4yMiA1LjExLTEwLjU3NyAxMC4yLTE4LjA5OSAxMS44MjgtNC40MTkuOTU2LTkuNTYyLTQuMjkxLTEwLjI1Mi04Ljc4MUMtLjY0IDE5LjU2NSA2Ljk5IDE0LjI1NyAxMS44NTYgNy41MjNjMS4zNDQtMS44NiAzLjMyLTMuMjUgNC44ODItNC45NjYgMi43NzMtMy4wNDUgNS44ODQtMy4wMTEgOC44MzItLjY2MSAzLjI4IDIuNjEzLjc5IDUuNTI0LS44NCA3LjMzOS0zLjQwNiAzLjc5LTcuNDQgNy4wMjMtMTEuMjc4IDEwLjQxMy0xLjU5NiAxLjQxLTMuMjM0IDIuODQ3LTUuMDY1IDMuOS0zLjA0MyAxLjc1My0zLjQ4IDQuMjYtMi4zOTQgNy4xNzIgMS4yMjQgMy4yNzggNC4yMDYgNC4wMTUgNi45OTIgMy4wODcgMy4xNjYtMS4wNTQgNi4wNzItMi45NyA4Ljk3NS00LjcwNyAyLjExNC0xLjI2MiA0LjAzOC0yLjg0MSA2LjA0Ni00LjI3OGwxLjI1MyAxLjc2NCIgZmlsbD0iIzAwMCIgbWFzaz0idXJsKCNiKSIvPjwvZz48cGF0aCBkPSJNMTE1LjM0IDUyLjA2Yy01LjkyMyAxLjMxNC0xNS42MTcgMTMuOC0xNS4yNTMgMTkuMzk1LjI0MiAzLjczMiAyLjQ1MyAzLjQ0MyA0Ljc1MiAxLjgyNiA3LjI5Ni01LjEzMyAxMC4xNzQtMTIuNDM1IDEwLjUwMi0yMS4yMm00LjY2LjYwNGMtMi4yMjggMTAuNTY2LTYuMTcgMTguODMtMTQuMDgyIDI0LjUwNC0xLjc5IDEuMjgzLTUuNjUyIDEuODYxLTcuMTU0Ljc5MS0xLjc5LTEuMjc1LTIuOTI1LTQuNzMtMi43NzctNy4xNDMuMTgtMi45MjkgMS41ODctNi4wMTYgMy4xOTctOC41NzMgMi44OTQtNC41OTYgNi4wNTMtOS4xIDkuNjczLTEzLjEzIDEuNjU1LTEuODQyIDUuNzQtNC4wNDggNi44NDQtMy4zMDQgMi4yNCAxLjUxMiAzLjIgNC45MiA0LjI5OSA2Ljg1NU0yNzEuMzI5IDUyLjA5OWMtNi4wNyAxLjI1Ny0xNi4xODEgMTQuNTIzLTE1LjIxNCAxOS44MTIuNzE4IDMuOTI1IDMuMTkgMi41NiA1LjExNCAxLjEyNiA2Ljk1Mi01LjE4NCA5LjgzOS0xMi4zMzggMTAuMS0yMC45MzltLTIwLjc5MyAxOS40MjdjMS45MTMtMy43NzQgMy4yODctNy4yODUgNS4zMzYtMTAuMzQzIDMuMTE1LTQuNjUgNi4zNTMtOS4zMTIgMTAuMTgtMTMuMzY0IDMuOTU4LTQuMTkyIDcuMjA1LTMuMTIyIDguOTQ4IDIuMzg5LjQzNSAxLjM3Ni42NzcgMi45ODMuNDI2IDQuMzc5LTEuNjA3IDguOTM5LTUuNzA4IDE2LjYxNy0xMi45NCAyMi4xNDgtMS44OTIgMS40NS01LjQ5MSAyLjEzNC03LjYzNiAxLjM0Ni0xLjg0LS42NzctMi43MjItMy45NTktNC4zMTQtNi41NTUiIGZpbGw9IiMwMDAiLz48L2c+PC9zdmc+';

						// Find this signature and populate it
						if(ws_this.signatures.length > 0) {

							for(var signature_index in ws_this.signatures) {

								if(!ws_this.signatures.hasOwnProperty(signature_index)) { continue; }

								var signature = ws_this.signatures[signature_index];

								if(
									!signature.input.attr('disabled') &&
									(signature.name == field_name) 
								) {
									signature.signature.fromDataURL(signature_populate_data);
									signature.canvas.trigger('mousedown');
								}
							}
						}

						break;

					case 'googlemap' :

						var positions = [

							{'lat': 29.97916667,	'lng': 31.13416667},
							{'lat': 31.21388889,	'lng': 29.88555556},
							{'lat': 33.5355,		'lng': 44.2475},
							{'lat': 36.4511,		'lng': 28.2278},
							{'lat': 37.03777778,	'lng': 27.42416667},
							{'lat': 37.63777778,	'lng': 21.63},
							{'lat': 37.94972222,	'lng': 27.36388889},
							{'lat': 37.81972222,	'lng': -122.4786111},
							{'lat': 43.6425,		'lng': -78.61305556},
							{'lat': 51.0125,		'lng': 1.5041},
							{'lat': 51.65,			'lng': 3.72},
							{'lat': 9.08,			'lng': -79.68},
							{'lat': 40.74833333,	'lng': -73.98555556},
							{'lat': 31.178558,		'lng': 29.892954},
							{'lat': 40.67693,		'lng': 117.23193},
							{'lat': 41.008548,		'lng': 28.979938},
							{'lat': 51.17861111,	'lng': -1.826111111},
							{'lat': 43.72305556,	'lng': 10.39638889},
							{'lat': 41.89,			'lng': 12.49222222},
							{'lat': 32.08027778,	'lng': 118.73},
							{'lat': 20.68277778,	'lng': -88.56861111},
							{'lat': -12.83666667,	'lng': -72.54555556},
							{'lat': 41.89,			'lng': 12.49222222},
							{'lat': 29.97916667,	'lng': 31.13416667},
							{'lat': 30.32861111,	'lng': 35.44194444},
							{'lat': 27.175,			'lng': 78.04194444},
							{'lat': 40.67666667,	'lng': 117.2316667},
							{'lat': 19.49277778,	'lng': -102.2508333},
							{'lat': 36.1,			'lng': -112.1},
							{'lat': -21.20972222,	'lng': -43.15555556},
							{'lat': -16.07555556,	'lng': 25.85666667},
							{'lat': 27.98805556,	'lng': 86.92527778},
							{'lat': -17.71388889,	'lng': 147.7},
							{'lat': 36.01555556,	'lng': -114.7377778},
							{'lat': 9.08,			'lng': -79.68},
							{'lat': 40.70555556,	'lng': -73.99638889},
							{'lat': 56.43416667,	'lng': -2.387222222},
							{'lat': 51.48861111,	'lng': -0.005555555556},
							{'lat': 51.50722222,	'lng': -0.1058333333},
							{'lat': 41.27638889,	'lng': 95.86666667},
							{'lat': 20.68277778,	'lng': -88.56861111},
							{'lat': 25.7,			'lng': -171.7333333},
							{'lat': 36.1,			'lng': -112.1},
							{'lat': 89.9999,		'lng': 0},
							{'lat': 31.77666667,	'lng': 35.23416667},
							{'lat': 29.65777778,	'lng': 91.11694444},
							{'lat': 41.7258,		'lng': -49.9408},
							{'lat': 50.8227041,		'lng': -0.1375889},
							{'lat': 28.6050,		'lng': -80.6026}
						];

						var position = positions[Math.floor(Math.random() * positions.length)];
						position.zoom = 10;
						position.map_type_id = 'roadmap';

						populate = JSON.stringify(position);

						break;

					case 'googleaddress' :

						var addresses = [

							'1600 Pennsylvania Avenue Northwest',
							'11 Wall Street',
							'350 Fifth Avenue',
							'4059 Mount Lee Drive'
						];

						populate = addresses[Math.floor(Math.random() * addresses.length)];

						break;
				}

				if(populate !== false) {

					// Set value
					populate_obj.val(populate);

					// Trigger events
					var data_populate_event = populate_obj.attr('data-populate-event');
					var data_populate_event_array = data_populate_event ? data_populate_event.split(' ') : ['change'];

					for(var data_populate_event_array_index in data_populate_event_array) {

						if(!data_populate_event_array.hasOwnProperty(data_populate_event_array_index)) { continue; }

						var object_event_event = data_populate_event_array[data_populate_event_array_index];

						// Only run events that relate to a value being changed
						switch(object_event_event) {

							case 'change' :
							case 'input' :

								populate_obj.trigger(object_event_event);
								break;
						}
					}
				}
			});
		}

		// Run calculations
		if(typeof(this.form_calc) === 'function') { this.form_calc(); }
	}

	// Debug - Get word
	$.WS_Form.prototype.debug_get_word = function() {

		var words = $.WS_Form.debug.words;
		return words[Math.floor(Math.random()*words.length)];
	}

	// Debug - Get sentence
	$.WS_Form.prototype.debug_get_sentence = function(min_length, max_length, add_periods, sentence_length) {

		if(typeof add_periods === 'undefined') { add_periods = true; }
		if(typeof sentence_length === 'undefined') { sentence_length = 32; }

		// Build sentence
		var sentence = '';
		var sentence_start = true;
		var sentence_index = 0;
		var string_next = '';
		var sentence_index_total = 0;
		do {

			sentence += string_next;

			// Establish next word to add
			var word_next = this.debug_get_word();

			// Should we start a new sentence?
			if(sentence_index > sentence_length) { sentence_index = 0; }

			// Join
			var string_next = sentence_start ? '' : ((sentence_index == 0) ? '. ' : ' ');

			// Word
			string_next += (sentence_index == 0) ? this.debug_ucfirst(word_next) : word_next;

			// Add next string to sentence
			var sentence_index_inc_amount = string_next.length + ((sentence_index == 0) ? 2 : 1);
			sentence_index += sentence_index_inc_amount;
			sentence_index_total += sentence_index_inc_amount

			sentence_start = false;

		} while((sentence_index_total < max_length) || (sentence_index_total < min_length))
		sentence += string_next;

		// Truncate
		sentence = this.debug_truncate(sentence, max_length - (add_periods ? 1 : 0));

		// Add period to sentence
		if(add_periods) { sentence += '.'; }

		return sentence;
	}

	// Debug - Truncate a string
	$.WS_Form.prototype.debug_truncate = function(input_string, length) {

		return input_string.substring(0,length);
	}

	// Debug - Uppercase first letter
	$.WS_Form.prototype.debug_ucfirst = function(input_string) {

		return input_string.charAt(0).toUpperCase() + input_string.slice(1);
	}

	// Debug - Random number
	$.WS_Form.prototype.debug_random_number = function(min, max, step) {

		if(
			(typeof(step) === 'undefined') ||
			(step === 0)
		) {
			step = 1;
		}

		var inv = 1.0 / step;

		var return_value = Math.round((Math.random()*(max-min)+min) * inv) / inv;

		if(return_value > max) { return_value = max; }
		if(return_value < min) { return_value = min; }

		return return_value;
	}

	// Debug - Random date
	$.WS_Form.prototype.debug_random_date = function(start, end) {

		return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
	}

	// Debug - Pad
	$.WS_Form.prototype.debug_pad = function(num, size) {

		var s = num + '';
		while (s.length < size) s = '0' + s;
		return s;
	}

	// Debug - Reset
	$.WS_Form.prototype.debug_form_reset = function() {

		this.form_reset();
	}

	// Debug - Clear
	$.WS_Form.prototype.debug_form_clear = function() {

		this.form_clear();
	}

	// Hash - Clear
	$.WS_Form.prototype.debug_clear_hash = function() {

		this.form_hash_clear();
	}

})(jQuery);




































