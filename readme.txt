=== WS Form PRO ===
Contributors: westguard
Requires at least: 5.2
Tested up to: 6.0.1
Stable tag: trunk
Requires PHP: 5.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WS Form PRO allows you to build faster, effective, user friendly WordPress forms. Build forms in a single click or use the unique drag and drop editor to create forms in seconds.

== Description ==

= Build Better WordPress Forms =

WS Form lets you create any form for your website in seconds using our unique drag and drop editor. Build everything from simple contact us forms to complex multi-page application forms.

== Installation ==

For help installing WS Form, please see our [Installation](https://wsform.com/knowledgebase/installation/?utm_source=wp_plugins&utm_medium=readme) knowledge base article.

== Changelog ==

= 1.9.4 - 10/17/2022 =
* Bug Fix: GeoIP lookup endpoint
* Bug Fix: Tab indexing on refresh

= 1.9.3 - 10/17/2022 =
* Bug Fix: Date variables without date fields

= 1.9.2 - 10/17/2022 =
* Added: #field with delimiter support for price select, price checkbox and price radio fields
* Added: Quality and price total auto map on clone
* Bug Fix: Tab indexing on clone

= 1.9.1 - 10/16/2022 =
* Added: Reviewed santitization, escaping and validation throughout
* Added: Improved code to make security reviews easier in future

= 1.9.0 - 10/14/2022 =
* Added: Dynamic enqueuing setting (Global settings)
* Added: Public JS optimized throughout to dramatically reduce file download size
* Added: Minimum password strength setting on password field
* Added: Suggest password setting on password field
* Added: Generate password option in conditional logic for password fields
* Bug Fix: Population of price checkbox fields

= 1.8.236 - 10/11/2022 =
* Added: Bricks element dynamic form ID setting

= 1.8.235 - 10/09/2022 =
* Added: Remove Arrows/Spinners option for number and quantity fields
* Bug Fix: Input mask component incorrectly setting decimal point if decimal places set to 0

= 1.8.234 - 10/07/2022 =
* Added: Page and Post ID options added to redirect action
* Added: Error handling improvements for captcha server-side validation
* Added: Conditional logic assessed when field set validation occurs
* Bug Fix: International Telephone Input initialization height

= 1.8.233 - 10/06/2022 =
* Added: Show invalid fields option for tab validation
* Added: wsf_submit_field_validate filtered by submit_save setting
* Added: Date validation support for min / max when set to EPOCH format, e.g. +1970-01-05
* Changed: wsf_form_get_form_label function now usable when not logged in as admin
* Bug Fix: Checkbox / Select field max setting was not assessed if invalid feedback was disable
* Bug Fix: Date population if min / max contained EPOCH format or variables

= 1.8.232 - 10/05/2022 =
* Bug Fix: Post Status data source column headings

= 1.8.231 - 10/04/2022 =
* Added: Cloudflare Turnstile captcha widget
* Added: Translatable 'week' in date/time field
* Added: Performance improvement on custom field date calculations
* Bug Fix: #calc using European price formats

= 1.8.230 - 10/04/2022 =
* Bug Fix: Meta box date processing for international formats

= 1.8.229 - 10/03/2022 =
* Added: Auto-focus on Select2 search when Select2 is opened
* Bug Fix: Placeholder on select field with cascading required data-placeholder attribute

* 1.8.228 - 10/01/2022 =
* Added: Note field type for adding notes in the layout editor
* Added: Cloud file handler settings added to signature field
* Added: Conditional logic select all / deselect all for select and checkbox fields
* Added: Spam Protection field group
* Added: Meta Box date field Timestamp / Save Format support
* Changed: Basic mode no longer renders different field types in admin sidebar settings

= 1.8.227 - 09/27/2022 =
* Added: Query string parameter setting in redirect action

= 1.8.226 - 09/26/2022 =
* Added: Ability to edit fields using data sources in submissions page
* Bug Fix: Form SVG generator for buttons and messages set to type none

= 1.8.225 - 09/21/2022 =
* Added: cookie_get("name") variable

= 1.8.224 - 09/18/2022 =
* Bug Fix: Left alignment on inside label if prefix is applied to a field

= 1.8.223 - 09/17/2022 =
* Added: Upgraded input-mask library to version 5.0.7 to fix JS error in Oxygen when editing a product template
* Changed: German translations improved with thanks to @maexx for his contribution
* Bug Fix: User management add-on threw an error when listing fields if an add-on was not configured
* Bug Fix: CSS class error when unable to write cached files in uploads folder

= 1.8.222 - 08/29/2022 =
* Added: #checkbox_count(id) variable. This returns the number of checkboxes checked on a checkbox field.
* Added: #select_count(id) variable. This returns the number of options selected on a select field.
* Added: Support for calculations with currencies that have blank decimal or thousand separators.
* Added: Error thrown if negative value occurs in cart detail fields without 'Allow Negative Value' checked.

= 1.8.221 - 08/24/2022 =
* Added: Performance improvement: wp_options data for CSS cache now split into separate record
* Added: Performance improvement: wp_options data now has autoload set to 'no'
* Added: Business related field mappings for Google Address field
* Added: Rows setting for text area configured to use Visual Editor
* Bug Fix: Removed duplicate field class on radio field group wrapper
* Bug Fix: Case sensitive issue with select field value checking in conditional logic

= 1.8.220 - 08/11/2022 =
* Added: Bi-directional population for all Meta Box file and image field types
* Added: 'Show All If No Results' setting in Search fields under Select2 --> AJAX
* Added: Population of select fields if using Select2 --> AJAX
* Bug Fix: Canonical URL fixed on conversation form pages

= 1.8.219 - 08/10/2022 =
* Added: Bricks Builder 1.4 / 1.5 support
* Bug Fix: Conditional logic now checks if field exists in DOM when checking values
* Bug Fix: Cached RTL skin and layout CSS was not loading if not rendered inline

= 1.8.218 - 08/07/2022 =
* Added: Support for #field column look ups for term, post and user data sources

= 1.8.217 - 08/06/2022 =
* Added: #trim variable
* Bug Fix: Corrected Meta Box data format for singular checkbox / select choices

= 1.8.216 - 08/05/2022 =
* Added: #post_status variable
* Bug Fix: get_number function with currency symbols containing the decimal separator character

= 1.8.215 - 07/26/2022 =
* Added: Improved support for Fancybox when form contains Visual Editor (TinyMCE)

= 1.8.214 - 07/19/2022 =
* Added: Additional date format support for submission date range searching

= 1.8.213 - 07/18/2022 =
* Added: #request_url variable that returns the page URL you are on, regardless of the template
* Changed: Form lists order changed to order by name and then ID
* Bug Fix: Submissions date range searching
* Bug Fix: 'Select value does not equal' was not appearing in conditional logic options for select fields

= 1.8.212 - 07/07/2022 =
* Added: $form_object attribute set on 'Get Data' for WordPress Hook data source
* Bug Fix: Disabled add view REST API requests in LITE edition
* Bug Fix: Vertical tab alignment on WooCommerce products

= 1.8.211 - 06/29/2022 =
* Added: Support for terms and term categories without labels
* Added: EDD licensing improvements
* Changed: Bricks element label changed to 'WS Form'

= 1.8.210 - 06/20/2022 =
* Added: Google Address component mapping now supports 'subpremise' (Used for unit # in Australia)

= 1.8.209 - 06/08/2022 =
* Added: Google Address result type options (All, Address, Businesses, Cities, Regions)

= 1.8.208 - 05/31/2022 =
* Added: Server-side limit checks on form submit
* Bug Fix: Run WordPress Hook message form scrolling configuration

= 1.8.207 - 05/27/2022 =
* Added: Required setting added to legal fields (User must scroll through entirety of legal text to validate field)

= 1.8.206 - 05/20/2022 =
* Bug Fix: Title attribute on spam indicator on submissions page

= 1.8.205 - 05/18/2022 =
* Added: Inline validation setting on fields and sections
* Added: Neighborhood and Formatted Address mapping to Google Address field
* Added: Formatted Address added to data transfer from Google Address to Google Map field
* Added: Additional admin CSS to override poorly targetted CSS from third party plugins
* Changed: Improved German translations (Thanks to @upt2 for their contribution)

= 1.8.204 - 05/14/2022 =
* Bug Fix: Cached CSS file build for fresh install on admin side

= 1.8.203 - 05/13/2022 =
* Changed: Gravity Forms migration updates
* Bug Fix: WordPress hook data source row ID across separate groups

= 1.8.202 - 05/12/2022 =
* Added: Ability to run server-side validation on form save

= 1.8.201 - 05/09/2022 =
* Added: After form lock, only buttons that were originally enabled are re-enabled

= 1.8.200 - 05/06/2022 =
* Added: Support for WPForms LITE migration with no submission data

= 1.8.199 - 05/03/2022 =
* Bug Fix: Server-side #blog_date_custom variable fix

= 1.8.198 - 05/02/2022 =
* Added: Support for select2 in Elementor pop-ups
* Added: #submit_url_hash variable which returns only the hash query variable part

= 1.8.197 - 04/26/2022 =
* Bug Fix: Blur / focus conditional logic events

= 1.8.196 - 04/25/2022 =
* Added: Improved behavior of marker on Google Maps. Drag and drop. No re-centering on map click.
* Changed: Updated Elementor widget to support latest class structure

= 1.8.195 - 04/18/2022 =
* Added: Support for #calc sums using #field with a column specified

= 1.8.194 - 04/12/2022 =
* Added: Revised date formats passed to JS Date function to overcome Safari bug
* Bug Fix: Stop compiled CSS files being enqueued in admin for visual builders

= 1.8.193 - 04/04/2022 =
* Bug Fix: Meta box taxonomy population for multiple values

= 1.8.192 - 03/29/2022 =
* Bug Fix: Conditional logic action enable / disable was not resetting on subsequent form submissions

= 1.8.191 - 03/25/2022 =
* Bug Fix: Fixed default data grid columns for field generated for Meta Box that utilize a data source

= 1.8.190 - 03/23/2022 =
* Changed: Removed color reference on 'Add-Ons' and 'Upgrade' menu items to better support admin themes
* Changed: Removed need for spacer image in standard HTML email template

= 1.8.189 - 03/14/2022 =
* Added: Support for #field with column attribute for values containing row parse values

= 1.8.188 - 03/09/2022 =
* Added: PATCH to Push to Custom endpoint action
* Bug Fix: Min / max on date / time field configured as type 'Time'
* Bug Fix: Column hiding on forms admin page

= 1.8.187 - 02/22/2022 =
* Added: reCAPTCHA V3 reset on form submit
* Added: Encoding of analytics functions to avoid conflicts with plugins containing poor regex functions intended for identifying cookie / analytics scripts
* Bug Fix: Server side column mapping with data grid parse variables

= 1.8.186 - 02/17/2022 =
* Bug Fix: #field iterator checking

= 1.8.185 - 02/15/2022 =
* Bug Fix: Trim and validity checking on multi parameter variables

= 1.8.184 - 02/13/2022 =
* Added: delimiter and column parameters added to #field variable for data grids. #field(id, delimiter, column). Column can be the column label or index (0 is first column)
* Added: Donation amount template added to section library

= 1.8.183 - 02/10/2022 =
* Added: Improved targeting of jQuery sortable 1.13.1 script override

= 1.8.182 - 02/07/2022 =
* Bug Fix: ACF date field population if value is empty

= 1.8.181 - 02/06/2022 =
* Added: Clear session ID with Conditional Logic (Form --> Clear session ID)
* Bug Fix: Typing enter in post editor label field cause Add Form modal to open. Not a WS Form bug but workaround implemented.
* Bug Fix: Media library was not opening with recent jQuery UI sortable fix.

= 1.8.180 - 02/01/2022 =
* Added: form_id and field_id parameters added to wsf_action_email_email_validate hook

= 1.8.179 - 02/01/2022 =
* Added: WooCommerce: Ability to save form as well as save & continue
* Added: WooCommerce: Form hash cleared when product added to cart
* Added: WooCommerce: Ability to run actions using conditional logic
* Added: Support for shortcodes in email templates (Shortcodes cannot be entered into fields for security reasons)
* Added: Ability to search for submissions by date range using site date format (Now supports d/m/Y)
* Added: New action: wsf_enqueue_public (Runs if WS Form enqueues have occurred)
* Added: Media library files embedded using URL instead of Base64 encoding for improved email support
* Bug Fix: Google event firing was occurring if parent checkbox was disabled.

= 1.8.178 - 02/01/2022 =
* Added: Placeholder on Google Address field
* Bug Fix: Conditional value setting with URL type fields

= 1.8.177 - 01/30/2022 =
* Added: Improved key obscuring in settings
* Added: Improved CSS / font loading for compatibility with WordPress.com
* Bug Fix: Submission read by hash now excludes trashed records

= 1.8.176 - 01/26/2022 =
* Added: Translatable string improvements throughout
* Bug Fix: XSS fix: Admin label encoding in form list
* Bug Fix: XSS fix: Text extraction from text area fields in submission viewer

= 1.8.175 - 01/26/2022 =
* Bug Fix: LiteSpeed fix

= 1.8.174 - 01/26/2022 =
* Added: #wpautop(string) variable

= 1.8.173 - 01/26/2022 =
* Bug Fix: Hidden static fields were not excluded from #email_submission variable

= 1.8.172 - 01/24/2022 =
* Added: wsf_tab_index_<form_id> query variable that allows linking directly to tabs on forms
* Added: Close icon on debug console (Debug console can be re-opened in Global Settings --> Advanced)
* Added: Debug console now remembers height when minimized or restored if page refreshed

= 1.8.171 - 01/22/2022 =
* Added: wsf_submit_meta_read($meta_value, $field_id) and wsf_submit_meta_update($meta_value, $field_id) filter hooks

= 1.8.170 - 01/22/2022 =
* Added: Virtual keyboard disable option for date / time fields (Improves usability of date / time picker on mobile)
* Added: Virtual keyboard setting for text and text area fields
* Added: Improved license and key obscuring for all add-ons

= 1.8.169 - 01/21/2022 =
* Bug Fix: Conditional logic applying values to hidden fields in repeatable sections

= 1.8.168 - 01/19/2022 =
* Added: Submissions export support for sites with permalinks configured as plain
* Bug Fix: Tab navigation if tabs hidden

= 1.8.167 - 01/19/2022 =
* Bug Fix: ACF population of values containing amphersand
* Bug Fix: ACF true/false field type value now set to 0 or 1

= 1.8.166 - 01/17/2022 =
* Added: Tranform setting on text based fields: Uppercase, Lowercase, Capitalize, Sentence
* Added: Variables: #upper(string), #lower(string), #ucfirst(string), #ucwords(string), #capitalize(string), #sentence(string)
* Bug Fix: Tab navigation if first tab hidden

= 1.8.165 - 01/16/2022 =
* Added: ARIA attributes added to progress field
* Bug Fix: aria-disabled attribute on repeatable section navigation

= 1.8.164 - 01/16/2022 =
* Added: Block pattern library
* Bug Fix: Tab rendering if first tab hidden

= 1.8.163 - 01/14/2022 =
* Added: Sidebar changes now auto save if you switch to a different sidebar, tab, section or field
* Added: Updated version numbers on external enqueues
* Added: Submission form selector now shows '0 records' instead of disabling the option

= 1.8.162 - 01/07/2022 =
* Bug Fix: Conversational field change events limited to current section to avoid form jumping if dynamic field changes in non-focussed sections occur

= 1.8.161 - 01/06/2022 =
* Added: Improved accessibility for repeatable section icons
* Added: WordPress 5.9 uses jQuery UI 1.13.0 which has a positioning bug when dragging fields to the form (https://github.com/jquery/jquery-ui/issues/2001). Version 1.12.1 of jQuery UI sortable is now bundled with WS Form to overcome this issue.

= 1.8.160 =
* Added: DropzoneJS thumbnail image size filter hook: wsf_dropzonejs_image_size($size). $size must exist in get_intermediate_image_sizes()
* Added: Improved language on welcome screen framework selection step
* Bug Fix: Conversational next field focussing

= 1.8.159 =
* Bug Fix: Color function for PHP 8.1

= 1.8.158 =
* Added: WordPress 5.9 / PHP 8.1 compatibility preparation
* Bug Fix: Conditional logic data grid column mapping

= 1.8.157 =
* Added: New 'Run WordPress Hook' filter returns can now trigger form errors. See: https://wsform.com/knowledgebase/run-wordpress-hook/
* Added: Additional no cache headers and Litespeed no cache directives for save, submit requests

= 1.8.156 =
* Bug Fix: Date >=, <= comparisons in conditional logic with EU date formats

= 1.8.155 =
* Added: Google Address map assignment initial zoom setting
* Added: Improvement to tab ARIA attributes

= 1.8.154 =
* Added: NONCEs on public forms where a user is not logged are now optional to allow for increased cache timeouts

= 1.8.153 =
* Added: Various accessibility improvements (ARIA tags and text contrast)
* Added: Improved min/max file size error messages
* Bug Fix: Repeater deduplication between fields

= 1.8.152 =
* Added: Improved reset and clear functionality for file fields

= 1.8.151 =
* Bug Fix: SVG URL encoding issue for select down arrows

= 1.8.150 =
* Added: Support for legacy WordPress date functions (WP < 5.3)
* Bug Fix: Conditional logic numeric checks fixed for EU number formats

= 1.8.149 =
* Bug Fix: Field change events now initialized before initial calculations fire

= 1.8.148 =
* Added: wp_mail errors now include PHPMailerException error message
* Added: Media (attachment) post type to Post data source. Added 'inherit' post status so that attachments can be listed.
* Added: Running the 'Save Submission' action via conditional logic now returns a submission hash
* Added: Improved translation strings. Moved translations out of constants.
* Added: Obscured license key in system report

= 1.8.147 =
* Added: Support for repeat label hiding on H1 - H6 as well as legend
* Added: Improved accessibility markup for repeatable section icons
* Bug Fix: Section delete variable clean up
* Bug Fix: Word and character count validation if help text empty

= 1.8.146 =
* Bug Fix: Database action auto archiving feature (cron action)

= 1.8.145 =
* Bug Fix: Welcome screen fixes for PC version of Chrome

= 1.8.144 =
* Bug Fix: Submission status filter links

= 1.8.143 =
* Bug Fix: Term lookup cache in post data source bug if term removed
* Bug Fix: WooCommerce price meta data now converted to floating point

= 1.8.142 =
* Bug Fix: Calculations from select fields were processed as currency
* Bug Fix: Rounding error in source field for calculations if commas used as decimal separator

= 1.8.141 =
* Bug Fix: Conversational URL link

= 1.8.140 =
* Added: Support for WooCommerce product meta data (Post management add-on)
* Bug Fix: Debug populate number fields with step of 'any'

= 1.8.139 =
* Added: New functionality ready for Amazon S3 integration for file fields
* Added: Form search
* Added: Third parameter to #field_date_offset variable to allow formatting of output
* Added: wsf_form_create_meta_data($meta_data) filter that allows meta data to be edited prior to form being created
* Added: Cost by area template

= 1.8.138 =
* Added: intl-tel-input support for phone field. https://intl-tel-input.com/. Check 'Enable' under 'International Telephone Input' in the basic tab to use it.

= 1.8.137 =
* Changed: Server and blog date formats with upgraded WordPress date/time methods

= 1.8.136 =
* Changed: Improved third party custom field plugin detection and add-on initialization

= 1.8.135 =
* Added: #field_date_offset(field_id, offset_in_seconds) variable. Returns a date field with an offset added or subtracted in seconds.
* Changed: Duration tracking is now switched off by default
* Bug Fix: PHP 8 fix in Divi module

= 1.8.134 =
* Bug Fix: Duration cookie no longer set if duration tracking disable

= 1.8.133 =
* Bug Fix: Table prefix on Post data source when retrieving terms

= 1.8.132 =
* Added: 'Use URLs for file fields' setting in 'Push to Custom Endpoint' action
* Added: reCaptcha V3 revisions
* Changed: Submission export temporary folder location

= 1.8.131 =
* Bug Fix: Double quote detection in variable attributes

= 1.8.130 =
* Added: New hCaptcha field
* Changed: Performance improvement on change triggering for cart detail fields

= 1.8.129 =
* Added: Migration from Gravity Forms 2.5 now supports column widths
* Added: Ability to use #field to fields outside a repeatable section in #section_rows_start ... end
* Bug Fix: Migrate tool submissions import meta data
* Bug Fix: Meta Box checkbox issue in repeaters

= 1.8.128 =
* Added: Form level Google Analytics events
* Added: gtm.formSubmit Google Tag Manager Form Submission trigger support
* Added: Change event fired when Google Address fields populated

= 1.8.127 =
* Added: Latitude and longitude mapping for the Google Address field
* Bug Fix: Parameter ordering issue for GA tracking objects

= 1.8.126 =
* Change: Improved detection of custom field plugins in multi-site environments
* Bug Fix: Google Analytics event firing now sets value to 'null' if empty
* Bug Fix: Meta box repeater (group) fields not recognized in field mapping

= 1.8.125 =
* Added: New Google Address field allows you to auto populate address fields from a Google Places Autocomplete API search

= 1.8.124 =
* Added: Performance improvements with #calc and other price related calculations

= 1.8.123 =
* Added: New filter wsf_action_email_email_validate($valid, $email) added for filtering unwanted email addresses

= 1.8.122 =
* Added: Updated option checking in Meta Box and ACF classes
* Bug Fix: Dedupe value scope processing issue

= 1.8.121 =
* Added: Increased length of form, tab, section and field labels to 1024 characters

= 1.8.120 =
* Bug Fix: Tab validation misreported valid email address if field was not required
* Bug Fix: User ID on form table when form cloned
* Bug Fix: Clicking a select field on a conversational form using PC / Chrome did not advance to next field

= 1.8.119 =
* Added: Toolset version check to ensure latest public functions are available

= 1.8.118 =
* Added: User ID set to current user ID when elements cloned in admin

= 1.8.117 =
* Added: Improved Elementor class declaration
* Added: Improved SVG background image encoding in skin CSS
* Bug Fix: HTML encoded labels for third party form selectors
* Bug Fix: File count conditional logic when using file fields in repeaters

= 1.8.116 =
* Bug Fix: WooCommerce class PHP 8 compatibility

= 1.8.115 =
* Added: Toolset integration support for Post Management (1.4.0) add-on
* Added: Toolset integration support for User Management (1.4.0) add-on
* Added: Toolset field option data source
* Bug Fix: Mouseover event on radio fields

= 1.8.114 =
* Added: Improved data sources so that column names retained but rows cleared on save

= 1.8.113 =
* Added: Ability to filter data source terms by those checked on a post

= 1.8.112 =
* Added: Support for conditional logic mouse events on select2 elements

= 1.8.111 =
* Added: Email field allow / deny values with wildcard (e.g. Allow *@wsform.com). Found in 'Advanced' tab.

= 1.8.110 =
* Added: Variable parsing on email header keys and values
* Bug Fix: Submission count recalculated when record status set to trash

= 1.8.109 =
* Added: Ability to use fields that return multiple values (e.g. checkboxes) as #field in email action 'To' setting
* Added: Ability to use variables with attributes embedded in #if statements
* Added: Improved z-indexing in admin
* Added: Improved time function handling per WordPress recommendations

= 1.8.108 =
* Added: Support for multiple textarea fields configured as type HTML
* Added: Reset data grids if a field is saved with a data source selected to minimize form data size
* Bug Fix: Cascade / AJAX select fields with no group data caused warning

= 1.8.107 =
* Added: URL element added to files in custom API endpoint action

= 1.8.106 =
* Bug Fix: Bulk Action Mark as Spam fixed

= 1.8.105 =
* Bug Fix: Encryption class includes for initial key generation

= 1.8.104 =
* Added: Conversion tracking now support variables
* Bug Fix: Improvement to encryption class includes
* Bug Fix: Elementor script and style enqueues

= 1.8.103 =
* Added: Ability to enable optgroup using the WordPress hook data source
* Added: CSS for tooltips in labels, e.g. <span data-wsf-tooltip="Tooltip">Icon</span>
* Added: Applied wpautop to message action content
* Added: Improved data picker styles
* Added: Booking template in section library
* Bug Fix: Password visibility toggle
* Bug Fix: Updated email regex pattern throughout to support new TLD's
* Bug Fix: Inline date picker

= 1.8.102 =
* Added: Post statuses data source
* Bug Fix: Lower track on range slider was not rendering if #value was missing from help setting

= 1.8.101 =
* Added: wsf_submit_clear_meta_filter_keys filter (Used for new feature in post management add-on version 1.3.2)
* Bug Fix: Taxonomy list in post data source

= 1.8.100 =
* Added: Support for hidden sections in conversational forms
* Bug Fix: Improved Meta Box field lookups to handle meta boxes assigned to more than one post type

= 1.8.99 =
* Added: Ability to self reference field when using #field in labels, placeholder and help
* Bug Fix: Bug fix on conditional logic setting value using #field in repeatable section

= 1.8.98 =
* Added: Performance improvement on clearing select fields with conditional logic
* Bug Fix: Save Submission field filtering
* Bug Fix: Select conditional logic on load with AJAX cascading
* Bug Fix: Repeatable sections in conversational forms now highlight all rows

= 1.8.97 =
* Added: Meter field type
* Added: New section templates
* Bug Fix: Various CSS fixes
* Bug Fix: Data grid enter key press fix

= 1.8.96 =
* Bug Fix: Elementor pop-ups

= 1.8.95 =
* Added: Support for forms in preview mode in Elementor
* Added: Row select value support for price select fields
* Bug Fix: Pods Yes/No field data grid rows

= 1.8.94 =
* Added: Improvements to functions.php for later PHP versions
* Changed: Behavior of rating field adjusted so that conditional logic does not fire on input

= 1.8.93 =
* Added: Cascading filter column comma separated value setting

= 1.8.92 =
* Added: LiteSpeed cache support
* Added: Notion field ID support in sidebar select fields

= 1.8.91 =
* Added: #seconds_epoch_midnight client-side variable returns seconds elapsed since Unix Epoch up until the closest previous midnight
* Bug Fix: Sidebar repeater select options were not disabling if value contained forward slashes in value
* Bug Fix: Select fields configured to use Select2, multiple and tagging enabled were not appearing in #field variables server side

= 1.8.90 =
* Added: Support for comma separated values in cascading fields
* Added: Hide Child Terms options in Terms data source (Only returns terms with a parent ID of 0)
* Added: Automatic column mapping on 'Get Data' for data sources
* Bug Fix: Data source save on sidebar conditional processing

= 1.8.89 =
* Added: Added checks to ensure Dropzone and Select2 JavaScript libraries are installed before initializing them

= 1.8.88 =
* Added: Improved check for Pods 2.8 Beta get_groups method

= 1.8.87 =
* Added: Additional support for Pods 2.8 Beta
* Bug Fix: Dynamic data source update when form submitted

= 1.8.86 =
* Added: WordPress Filter Hook data source

= 1.8.85 =
* Bug Fix: Improved client side HTML encoding

= 1.8.84 =
* Added: Pods integration support for Post Management (1.3.0) add-on
* Added: Pods integration support for User Management (1.3.0) add-on
* Added: Pods field option data source

= 1.8.83 =
* Added: wsf-steps-checks class for adding checks to styled tabs
* Added: Updated to GPLv3 license
* Bug Fix: Fixed invalid feedback reset on field focus

= 1.8.82 =
* Added: Improved performance on DropzoneJS uploads
* Bug Fix: Modal CSS duplication removed

= 1.8.81 =
* Added: 'Exclude From Cart Total' setting on Price, Price Select, Price Checkbox, Price Radio and Price Range fields types. This excludes those fields from the e-commerce calculations so that they can be used for other functionality such as #calc.
* Added: wsf-steps, wsf-vertical form wrapper classes for WS Form framework for tab styling
* Added: wsf-sticky form wrapper, section wrapper and field wrapper classes for WS Form framework
* Added: Tab accessibility improvements

= 1.8.80 =
* Bug Fix: select_option_text, checkbox_label and radio_label compatibility with repeatable sections

= 1.8.79 =
* Added: Placeholder option for Select2 with multiple selected
* Added: License key now obscured after it is entered
* Bug Fix: Select2 rendering

= 1.8.78 =
* Changed: Revised #post* variables for performance

= 1.8.77 =
* Added: Data export request containing registered user data

= 1.8.76 =
* Bug Fix: Field value form scope for #field
* Bug Fix: $submit_object on wsf_submit_field_validate
* Bug Fix: Data export request

= 1.8.75 =
* Added: Ability to hide reCAPTCHA in tabs, sections at field level
* Bug Fix: Tab Google Analytics event firing

= 1.8.74 =
* Bug Fix: Function compatibility issue with WooCommerce extension

= 1.8.73 =
* Added: Multiple choice on Select, Checkbox and Radio IF conditions (OR logic)
* Added: Round robin recipient logged in email action log on each submission

= 1.8.72 =
* Added: Option in email action to include links to file uploads and signatures
* Added: CSV export now includes full paths to file upoads and signatures
* Changed: E-Commerce status column in CSV export changed from 'Status' to 'Order Status' to avoid duplicate column name
* Changed: On submission view, signatures now displayed with preview and download icons to match file upload fields

= 1.8.71 =
* Added: New functions: wsf_form_get_form_object & wsf_form_get_form_label

= 1.8.70 =
* Bug Fix: ACF data source finding choices in grouped or repeater fields

= 1.8.69 =
* Added: WordPress 5.8 compatibility testing
* Added: PHP 8 compatibility testing
* Added: wp_upload_dir known WordPress bug fix

= 1.8.68 =
* Added: Submissions CSV export system now builds in chunks if records exceed 500 to avoid timeouts
* Added: Submissions CSV export system now zips CSV file if it exceeds 1/2 MB in size for convenience

= 1.8.67 =
* Added: Implemented fix for range slider browser bug

= 1.8.66 =
* Added: #calc / #text prioritizes attribute changes before default value changes
* Changed: #calc / #text numeric values changed to 12 decimal point precision

= 1.8.65 =
* Added: ACF fix on LITE

= 1.8.64 =
* Added: Meta Box integration support for Post Management (1.2.0) add-on
* Added; Meta Box integration support for User Management (1.2.0) add-on
* Added: Meta Box field option data source
* Added: Improved wpautop support throughout
* Added: New conditional logic for form submit and save success
* Bug Fix: Repeater column toggle in sidebar on row add
* Bug Fix: Wildcard file accept
* Bug Fix: Bootstrap 5 tabs
* Bug Fix: Checkbox fix in customize
* Bug Fix: Conversation form made PRO

= 1.8.63 =
* Added: Hierarchy support for terms data source (Display as Hierarchy setting)

= 1.8.62 =
* Added: #calc / #text support for min / max field attributes
* Added: Min / max support for price fields in conditional logic

= 1.8.61 =
* Bug Fix: Progress bar assigned to tabs

= 1.8.60 =
* Added: ACF field validation (filter: acf/validate_value) for post and user management actions
* Added: ACF oEmbed field type support
* Changed: Code optimization in public.js
* Bug Fix: Tab click events in conditional logic

= 1.8.59 =
* Bug Fix: Form preview rendering in Gutenberg
* Bug Fix: Field encoding post management populate values

= 1.8.58 =
* Changed: Currency symbols to match WooCommerce

= 1.8.57 =
* Bug Fix: Form submission limit

= 1.8.56 =
* Bug Fix: Message action delay

= 1.8.55 =
* Added: Change to placeholder CSS on inside field label positioning
* Changed: Bricks element updated due to function names changing in Bricks theme core

= 1.8.54 =
* Added: Hovering over tabs, sections and fields in the layout editor with the conditional logic sidebar open now highlights any conditional rows that contain those form elements

= 1.8.53 =
* Bug Fix: Required span on calculated labels
* Bug Fix: Calc firing on repeatable section row count changes

= 1.8.52 =
* Added: Error logging on invalid feedback client side action

= 1.8.51 =
* Added: Bootstrap 5 launch compatibility checks

= 1.8.50 =
* Bug Fix: Conversational style dequeuing

= 1.8.49 =
* Bug Fix: #email_tracking variable in email templates

= 1.8.48 =
* Added: Conversational forms (Beta)
* Changed: PRO licensing
* Bug Fix: Numeric value return on data sources pulling ACF meta data

= 1.8.47 =
* Bug Fix: Initial CSS build error

= 1.8.46 =
* Added: Server-side form validation hook (https://wsform.com/knowledgebase/custom-server-side-form-validation/)
* Added: Performance improvements with CSS engine
* Bug Fix: Form deletion

= 1.8.45 =
* Added: Date field: Disabled days
* Added: Date field: Disabled dates
* Added: Date field: Enabled dates

= 1.8.44 =
* Bug Fix: Visual editor CSS file path issue

= 1.8.43 =
* Bug Fix: x_wp_nonce JS error

= 1.8.42 =
* Added: RTL support for new compiled skin system

= 1.8.41 =
* Bug Fix: Redirect URL parsing now uses text/plain method

= 1.8.40 =
* Bug Fix: Select all fixed on data grids using dynamic data sources

= 1.8.39 =
* Added: Suggested knowledge base articles on support contact form
* Bug Fix: ACF numeric field value population in select, checkbox and radio fields
* Bug Fix: Min / max date validation

= 1.8.38 =
* Added: Improved default invalid feedback text by field type
* Added: Improved appearance of legal field for Foundation framework
* Bug Fix: Server date fix for certain JavaScript engines

= 1.8.37 =
* Added: Name to section library (first name, last name)
* Bug Fix: Applied pre_render filter to submit API call

= 1.8.36 =
* Added: Round robin support for the Send Email action
* Added: Box shadow customization
* Added: Updated version of date time picker JS

= 1.8.35 =
* Added: Support for #text and #calc containing #select_option_text
* Bug Fix: #text on date fields

= 1.8.34 =
* Changed: Improved and consolidated admin UI icons
* Bug Fix: 0 values to ACF fields

= 1.8.33 =
* Bug Fix: Minor bug fix for legacy PHP versions and autocomplete data
* Bug Fix: Validation of dates with period separators fixed for certain JavaScript engines

= 1.8.32 =
* Bug Fix: Dependencies fix

= 1.8.31 =
* Added: Improvement to Elementor pop-up handling
* Added: Calc enabled from HTML fields
* Added: Spacer height setting
* Added: Enqueued script dependency filters (for PayTrace)
* Added: CSS recompiled on upgrade
* Changed: Delete icon continuity throughout admin
* Bug Fix: Bricks preview now uses form preview
* Bug Fix: Select2 CSS

= 1.8.30 =
* Added: Enabled #text on server side to allow static fields to render correctly
* Bug Fix: Price range slider help now correctly formats prices using #ecommerce_price
* Bug Fix: Running actions with conditional logic no longer issues a session ID

= 1.8.29 =
* Added: Hide tabs option (Form --> Settings)
* Bug Fix: Elementor double initializing form events

= 1.8.28 =
* Bug Fix: Shortcode for Oxygen on public contained visual editor attribute

= 1.8.27 =
* Added: Bricks Element (https://bricksbuilder.io/)
* Changed: #pos to #positive, #neg to #negative for future compatibility

= 1.8.26 =
* Added: New variable: #min(value, value ...) - Returns the minimum of the supplied values
* Added: New variable: #max(value, value ...) - Returns the maximum of the supplied values
* Added: New variable: #neg(value) - Returns the 0 if positive, original value if negative
* Added: New variable: #pos(value) - Returns the 0 if negative, original value if positive

= 1.8.25 =
* Added: Improved preview template auto detection
* Added: Improved support for performance optimization plugins

= 1.8.24 =
* Added: Improved Gutenberg block rendering

= 1.8.23 =
* Added: Debug console now removes existing instances if wsf_form_init called

= 1.8.22 =
* Added: Ability to use step of 'any' on number fields
* Bug Fix: Elementor modals

= 1.8.21 =
* Added: WS Form Oxygen element
* Added: Improved JavaScript ws_form_init() function (Reinitializes all forms)
* Added: Improved Beaver Builder, Elementor form initialization
* Added: Improved Gutenberg block functionality
* Added: Note that field deduplication does not work with encryption
* Bug Fix: #calc on multiple select
* Bug Fix: Gutenberg block SVG

= 1.8.20 =
* Added: Client side #section_rows_start(section_id) / #section_rows_end

= 1.8.19 =
* Changed: Removed 'Read more' link from visual editor in admin
* Bug Fix: Visual editor link dialog flashed in Chrome if sidebar was minimized (Known Chrome issue)
* Bug Fix: Military format for time in date field

= 1.8.18 =
* Added: #text(field_id) variable (Allows you to duplicate field values in other fields)
* Added: User status options for tabs (Logged in, logged out, user role, user capability)
* Added: User status options for sections (Logged in, logged out, user role, user capability)
* Added: User logged in, logged out, role and capability conditional logic IF conditions
* Bug Fix: Encryption during WooCommerce checkout

= 1.8.17 =
* Added: Custom Email HTML template
* Added: Email Validation template
* Added: Spam level set to 0 on validated submissions
* Bug Fix: Validation of european date formats using periods

= 1.8.16 =
* Added: #section_rows_start(section_id) / #sections_row_end for email templates
* Added: Public config script optimizations
* Bug Fix: Media library uploads

= 1.8.15 =
* Bug Fix: Fixed hidden field validation for conditional logic validation

= 1.8.14 =
* Added: Improved checks on conditional logic object integrity
* Added: Implemented workaround for select2 not behaving properly with dynamically disabled options

= 1.8.13 =
* Bug Fix: Conditional logic icons if condition deleted

= 1.8.12 =
* Added: Improved signature validation
* Added: Server side #ecommerce_price function
* Added: Signature 'Clear' link text setting
* Added: Google Map 'Reset' link text setting
* Added: Password strength and visibility toggle text settings
* Added: Various Bootstrap and Foundation CSS improvements

= 1.8.11 =
* Added: WordPress 5.7 testing
* Added: Support for tab conditional logic in Bootstrap and Foundation

= 1.8.10 =
* Bug Fix: Conditional logic object ID lookups on form import
* Bug Fix: Next / previous buttons on forms with tab validation
* Bug Fix: Inside label positioning on e-commerce cart total field

= 1.8.9 =
* Added: Conditional logic icons on tabs, sections and fields
* Added: Highlight tabs, sections and fields when editing a condition

= 1.8.8 =
* Bug Fix: Email template parsing

= 1.8.7 =
* Bug Fix: Next / previous tab navigation on conditional logic disable
* Bug Fix: Parse variable processing fix (Server-side)

= 1.8.6 =
* Bug Fix: Media library upload with default file upload field

= 1.8.5 =
* Bug Fix: New form default settings

= 1.8.4 =
* Bug Fix: Required signatures on save

= 1.8.3 =
* Added: Password visibility toggle icon
* Added: Email validation via link in email
* Added: Validate before saving option on save field
* Bug Fix: Action format at on submission save

= 1.8.2 =
* Bug Fix: Tab next/previous buttons if tab hidden with conditional logic

= 1.8.1 =
* Bug Fix: Custom validity removal on hidden fields

= 1.8.0 =
* Added: Section Library
* Added: Section Library (Pre-built and 'My Sections' feature)
* Added: Section and tab import and export
* Added: Section and tab add to 'My Sections'
* Added: Improved tab management in layout editor (settings no longer obscure tab names)
* Added: Performance improvement: Compiled CSS global setting
* Added: Compliance with SCRIPT_DEBUG constant throughout (Minified CSS setting removed from Global Settings)
* Added: Compiled and inline CSS enabled by default for new users
* Added: Field and section search in Toolbox
* Added: Various layout editor UI improvement
* Added: DropzoneJS timeout setting
* Added: Improve statistics gathering methods and error checking
* Added: Latest version of DropzoneJS
* Changed: Removed prefix on form error alerts
* Bug Fix: Foundation 6+ Abide initialization on WooCommerce product pages
* Bug Fix: Data encryption learn more link
