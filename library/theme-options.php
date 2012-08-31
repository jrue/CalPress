<?php
/** 
 * CalPress Theme Options
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package WordPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */

/**
 * Properly enqueue styles and scripts for our theme options page in the admin.
 *
 * @since CalPress 2.0
 */
function calpress_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'calpress-theme-options', THEMEURI . '/css/theme-options.css', false, '20120614' );
	wp_enqueue_script( 'calpress-theme-options', THEMEURI . '/js/theme-options.js', array( 'farbtastic' ), '20120614' );
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script('jquery-ui-sortable');
}

/**
 * Register the form setting for our calpress_options array.
 *
 * This call to register_setting() registers a validation callback, twentyeleven_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * We also use this function to add our theme option if it doesn't already exist.
 *
 * @since CalPress 2.0
 */

function calpress_theme_options_init() {
	
	// If we have no options in the database, let's add them now.
	if ( false === calpress_get_theme_options() )
		add_option( 'calpress_theme_options', calpress_get_default_theme_options() );

	register_setting(
		'calpress_options',  // Options group, see settings_fields() call in calpress_theme_options_render_page()
		'calpress_theme_options', // Database option, see calpress_get_theme_options()
		'calpress_theme_options_validate' // The sanitization callback, see calpress_theme_options_validate()
	);
	
	add_settings_section(
		'look_and_feel', // Unique identifier for the settings section
		__('Look and Feel Settings', 'calpress'), // Section title
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see calpress_theme_options_add_page()
	);
	add_settings_field(
		'front_page_layout',
		__('Front Page Layout', 'calpress'),
		'calpress_settings_front_layout',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'big_news',
		__('Big News Callout', 'calpress'),
		'calpress_settings_big_news',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'category_block',
		__('Category blocks to display on front page', 'calpress'),
		'calpress_settings_category_block',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'omit_category_block',
		__('Omit category blocks', 'calpress'),
		'calpress_settings_category_block_omit',
		'theme_options',
		'look_and_feel'
	);

	add_settings_field(
		'featured_stories',
		__('Featured Stories', 'calpress'),
		'calpress_settings_featured_stories',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'theme_color',
		__('Color Bar at Top of Page', 'calpress'),
		'calpress_settings_color_bar',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'more_text',
		__('Continue Reading Text', 'calpress'),
		'calpress_settings_more_text',
		'theme_options',
		'look_and_feel'
	);
	add_settings_field(
		'comment_count',
		__('Show Comment Count?', 'calpress'),
		'calpress_settings_comment_count',
		'theme_options',
		'look_and_feel'
	);
	
	add_settings_field(
		'twitter_handle',
		__('This site\'s Twitter Handle', 'calpress'),
		'calpress_settings_twitter_handle',
		'theme_options',
		'look_and_feel'
	);
	
	add_settings_field(
		'share_code',
		__('Social Media Share Code', 'calpress'),
		'calpress_settings_share_code',
		'theme_options',
		'look_and_feel'
	);
	
	// Register our settings field group
	add_settings_section(
		'general', // Unique identifier for the settings section
		__('General Settings', 'calpress'), // Section title
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see calpress_theme_options_add_page()
	);
	// Register our individual settings fields
	add_settings_field(
		'front_category',
		__( 'Front Page Category', 'calpress' ), 
		'calpress_settings_front_page_category', // Function that renders the settings field
		'theme_options', // Menu slug, used to uniquely identify the page; see twentyeleven_theme_options_add_page()
		'general' // Settings section. Same as the first argument in the add_settings_section()
	);
	add_settings_field(
		'featured_category',
		__( 'Featured Story Category', 'calpress' ), 
		'calpress_settings_featured_story_category', 
		'theme_options', 
		'general' 
	);
	
	add_settings_field(
		'comment_policy',
		__( 'Comment Policy', 'calpress' ), 
		'calpress_settings_comment_policy', 
		'theme_options', 
		'general' 
	);
	
	add_settings_field(
		'show_author_profile_on_posts',
		__( 'Show author profile on posts?', 'calpress' ), 
		'calpress_settings_show_author_profile_on_posts', 
		'theme_options', 
		'general' 
	);
	
	add_settings_field(
		'insert_image_into_post',
		__( 'Remove "Insert Image Into Post"?', 'calpress' ), 
		'calpress_settings_insert_image_into_post', 
		'theme_options', 
		'general' 
	);
	
	add_settings_section(
		'legacy_options', // Unique identifier for the settings section
		__('Legacy Options', 'calpress'), // Section title
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see calpress_theme_options_add_page()
	);
	
	add_settings_field(
		'legacy_calpress',
		__( 'Support Legacy CalPress?', 'calpress'),
		'calpress_settings_support_legacy',
		'theme_options',
		'legacy_options'
	);
	add_settings_field(
		'legacy_video',
		__( 'Legacy Video Location', 'calpress'),
		'calpress_settings_legacy_video',
		'theme_options',
		'legacy_options'
	);
	add_settings_field(
		'legacy_soundslides',
		__( 'Legacy SoundSlides Location', 'calpress'),
		'calpress_settings_legacy_soundslides',
		'theme_options',
		'legacy_options'
	);
	add_settings_field(
		'jw_player',
		__( 'JW Player Location', 'calpress'),
		'calpress_settings_legacy_jw_player',
		'theme_options',
		'legacy_options'
	);
	add_settings_field(
		'jw_theme',
		__( 'JW Theme Location', 'calpress'),
		'calpress_settings_legacy_jw_theme',
		'theme_options',
		'legacy_options'
	);
	add_settings_section(
		'advanced', // Unique identifier for the settings section
		__('Site Administration Settings', 'calpress'), // Section title
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see calpress_theme_options_add_page()
	);
	add_settings_field(
		'google_verification',
		__('Google Webmaster Verification', 'calpress'),
		'calpress_settings_google_verification',
		'theme_options',
		'advanced' 
	);
	add_settings_field(
		'google_analytics',
		__('Google Analytics Code', 'calpress'),
		'calpress_settings_google_analytics',
		'theme_options',
		'advanced' 
	);
	
	add_settings_field(
		'extra_js_head',
		__('Extra JavaScript Code in &lt;head&gt;', 'calpress'),
		'calpress_settings_extra_javascript',
		'theme_options',
		'advanced' 
	);
	add_settings_field(
		'extra_css_head',
		__('Custom CSS styles', 'calpress'),
		'calpress_settings_extra_css',
		'theme_options',
		'advanced' 
	);
	/*
	add_settings_section(
		'facebook', // Unique identifier for the settings section
		__('Facebook Integration', 'calpress'), // Section title
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see calpress_theme_options_add_page()
	);
	add_settings_field(
		'facebook_id',
		__('Facebook App ID/API Key', 'calpress'),
		'calpress_settings_facebook_id',
		'theme_options',
		'facebook' 
	);
	add_settings_field(
		'facebook_secret',
		__('Facebook Application Secret', 'calpress'),
		'calpress_settings_facebook_secret',
		'theme_options',
		'facebook' 
	);
	add_settings_field(
		'facebook_button_layout',
		__('Facebook Button Layout', 'calpress'),
		'calpress_settings_facebook_layout',
		'theme_options',
		'facebook' 
	);
	add_settings_field(
		'facebook_button_action',
		__('Facebook Button Vocabulary', 'calpress'),
		'calpress_settings_facebook_action',
		'theme_options',
		'facebook' 
	);
	add_settings_field(
		'facebook_send_button',
		__('Facebook Send Button?', 'calpress'),
		'calpress_settings_facebook_send',
		'theme_options',
		'facebook' 
	);
	*/
}
	
add_action( 'admin_init', 'calpress_theme_options_init' );

/**
 * Change the capability required to save the 'calpress_options' options group.
 *
 * @see calpress_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see calpress_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * By default, the options groups for all registered settings require the manage_options capability.
 * This filter is required to change our theme options page to edit_theme_options instead.
 * By default, only administrators have either of these capabilities, but the desire here is
 * to allow for finer-grained control for roles and users.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function calpress_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_calpress_options', 'calpress_option_page_capability' );

/**
 * Add theme options page to the admin menu under Appearance, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Twenty Eleven 1.0
 */
function calpress_theme_options_add_page() {
	
	//add_menu_page( 'CalPress Options', 'CalPress Options', 'edit_theme_options', 'calpress_menu', 'calpress_menu', '', 95 );
	
	$theme_page = add_menu_page(
		__( 'CalPress Options', 'calpress' ),   // Name of page
		__( 'CalPress Options', 'calpress' ),   // Label in menu
		'edit_theme_options',                // Capability required
		'theme_options',                     // Menu slug, used to uniquely identify the page
		'calpress_theme_options_render_page', // Function that renders the options page
		'',
		95
	);
	if ( ! $theme_page )
		return;
	add_action( 'load-' . $theme_page, 'calpress_theme_options_help' );
	add_action( 'admin_print_styles-' . $theme_page, 'calpress_admin_enqueue_scripts' );
}
add_action( 'admin_menu', 'calpress_theme_options_add_page' );

function calpress_theme_options_help() {
	$help = '<p>' . __( 'These are some basic options you can set after you activate CalPress. These should really only be set once to personalize the site. Other options for adjusting the front page will be found under the Calpress menu item on the left.', 'calpress' ) . '</p>' .
			'<ol>' .
				'<li>' . __( '<strong>Front Category</strong>: This options sets which category of posts should be displayed on the front page of the site. This allows for granular control over which articles appear on the front page (versus articles that are meant specifically for other sections of the site, or aren\'t deserving of front page exposure.)', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Featured Category</strong>: To give even greater control, we\'ve developed a featured category which will specify articles that will be highlighted prominently on the front page. These are typically \'above the fold\' articles that should be front and center. Choose a category for these posts.', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Support Legacy CalPress</strong>: If you have been using an earlier version of CalPress, you should turn this on so that earlier custom fields are properly detected. If you\'re staring over, or have never use CalPress before, leave this off.', 'calpress') . '</li>' .
				'<li>' . __( '<strong>Theme Color</strong>: The color of the bar that runs along the top, as well as other small embellishments around the site.', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Google Verification</strong>: This is used for Google Webmaster tools. Enter in the verification ID here to claim your site and receive errors and better understand how Google indexes your site in search results. Visit <a href="www.google.com/webmasters/tools/">Google Webmaster Tools</a> to register your site and find more information.', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Google Analytics</strong>: Enter in your Google Analytics tracking ID here to see metrics and information about visitors to your site. If you use a different service, like Omniture, then use the extra javascript option to paste in the embed code.', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Extra JavaScript Code</strong>: If you need to add some javascript code to the &lt;head&gt; of all pages on your site, paste the code in this field option. Make sure to include &lt;script&gt; tags around embed code!', 'calpress' ) . '</li>' .
				'<li>' . __( '<strong>Custom CSS Styles</strong>: Add CSS styles to show up in the &lt;head&gt;. Do not include &lt;style&gt; or &lt;link&gt; tags, add style selectors and properties directly.', 'calpress' ) . '</li>' .
		'</ol>' .
			'<p>' . __( 'Remember to click "Save Changes" to save any changes you have made to the theme options.', 'calpress' ) . '</p>';

	$sidebar = '<p><strong>' . __( 'For more information:', 'calpress' ) . '</strong></p>' .
		'<p>' . __( '<a href="http://codex.wordpress.org/Appearance_Theme_Options_Screen" target="_blank">Documentation on Theme Options</a>', 'calpress' ) . '</p>' .
		'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'calpress' ) . '</p>';

	$screen = get_current_screen();

	if ( method_exists( $screen, 'add_help_tab' ) ) {
		// WordPress 3.3
		$screen->add_help_tab( array(
			'title' => __( 'Overview', 'calpress' ),
			'id' => 'theme-options-help',
			'content' => $help,
			)
		);

		$screen->set_help_sidebar( $sidebar );
	} else {
		// WordPress 3.2
		add_contextual_help( $screen, $help . $sidebar );
	}
}

/**
 * Returns the default options for CalPress.
 *
 * @since CalPress 2.0
 */
function calpress_get_default_theme_options() {
	
	//See if there are values set in the database from a legacy version of calpress
	$legacy_video = get_option('cp_video_location', '');
	$legacy_soundslides = get_option('cp_soundslides_location', '');
	$legacy_jw_player = get_option('cp_jw_player', '');
	$legacy_jw_theme = get_option('cp_jw_theme', '');
	$legacy_google_analytics = get_option('cp_ga', '');
	$legacy_front_category = get_option('cp_front_category', '');
	$legacy_featured_category = get_option('cp_front_featured_category', '');
	
	$default_theme_options = array(
		'front_category' => $legacy_front_category,
		'featured_category' => $legacy_featured_category,
		'legacy_calpress' => 'no',
		'legacy_soundslides' => $legacy_soundslides,
		'legacy_video' => $legacy_video,
		'jw_player' => $legacy_jw_player,
		'jw_theme' => $legacy_jw_theme,
		'comment_policy' => '',
		'show_author_profile_on_posts' => "false",
		'insert_image_into_post' => "true",
		'front_page_layout' => 'index',
		'big_news' => array('', '', '', ''),
		'featured_stories' => array('', '', ''),
		'category_block' => array(),
		'comment_count' => "true",
		'omit_category_block'=> '',
		'more_text' => ' [...]',
		'share_code' => '',
		'twitter_handle' => '',
		'theme_color' => '000000',
		'google_verification' => '',
		'google_analytics' => $legacy_google_analytics,
		'extra_js_head' => '',
		'extra_css_head' => '',
		'facebook_id' => '',
		'facebook_secret' => '',
		'facebook_button_layout' => '',
		'facebook_button_action' => '',
		'facebook_send_button' => '',
	);

	return apply_filters( 'calpress_default_theme_options', $default_theme_options );
}

/**
 * Returns the options array for CalPress. Set's defaults if not previously set.
 *
 * @since CalPress 2.0
 */
function calpress_get_theme_options() {
	return get_option( 'calpress_theme_options', calpress_get_default_theme_options() );
}

/**
 * Renders the front page category selection input
 *
 * @since CalPress 2.0
 */
function calpress_settings_front_page_category() {
	$options = calpress_get_theme_options();
	$categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC')); ?>
	
	<select name="calpress_theme_options[front_category]" id="calpress_theme_options[front_category]">
		<option <?php selected($options['front_category'], ''); ?> value="">All Categories</option>
		
<?php foreach($categories as $category): ?>
		<option value="<?php echo $category->term_id; ?>" <?php selected($options['front_category'], $category->term_id); ?>><?php echo $category->name; ?></option>
<?php endforeach; ?>

	</select>
	<p class="description">This is the category of articles you want to appear on the front page. Any articles (posts) that are not in this category will <strong>not</strong> appear on the front page. They will appear in other pages, such as category and archive pages.</p>
<?php
}

/**
 * Renders the featured page category selection input
 *
 * @since CalPress 2.0
 */
function calpress_settings_featured_story_category() {
	$options = calpress_get_theme_options();
	$categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC')); ?>
	
	<select name="calpress_theme_options[featured_category]" id="calpress_theme_options[featured_category]">
		<option <?php selected($options['featured_category'], ''); ?> value=""></option>
		
<?php foreach($categories as $category): ?>
		<option value="<?php echo $category->term_id; ?>" <?php selected($options['featured_category'], $category->term_id); ?>><?php echo $category->name; ?></option>
<?php endforeach; ?>

	</select>
	<p class="description">This is the category of articles that you want to appear above the fold. These are articles that will stand out on the front page, and will be given more weight.</p>
<?php
}

/**
 * Renders the multiple category picker form input in theme options
 *
 * @since CalPress 2.0
 */
function calpress_settings_category_block(){
	$options = calpress_get_theme_options();
	$categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC')); ?>

	<script type="text/javascript">
	
		function calpress_populate_category_block_field(val){
			jQuery(val).attr('selected', true); 
			jQuery(val).removeAttr('onclick');
			jQuery(val).attr("onclick", "calpress_put_element_back(this)");
			jQuery(val).attr("selected", "selected");
			jQuery(val).appendTo('#calpress_cat_block_picker');
		}
		function calpress_put_element_back(val){
			jQuery('#calpress_cat_block_picker option').attr('selected', true)
			jQuery('#calpress_cat_block_picker').blur();
			jQuery(val).attr('selected', false);
			jQuery(val).removeAttr('onclick');
			jQuery(val).attr("onclick","calpress_populate_category_block_field(this)");
			jQuery(val).insertBefore('#category_block_chooser option:first-child');

		}
	</script>
	<select id="category_block_chooser" multiple="multiple" size="6" style="width:200px; float:left;">
	<?php foreach($categories as $category): if(!in_array($category->cat_ID, (array) $options['category_block'])): ?>
	
		<option value="<?php echo $category->cat_ID ?>" onclick="calpress_populate_category_block_field(this); "><?php echo $category->name; ?></option>
	
	<?php endif; endforeach; $counter = 0; ?>
	</select>
	<select name="calpress_theme_options[category_block][]" id="calpress_cat_block_picker" multiple="multiple" size="6" style="width:200px; float:left;" class="clearfix">
	<?php if($options['category_block']) foreach($options['category_block'] as $option): ?>

		<option value="<?php echo $option; ?>" onclick="calpress_put_element_back(this)" selected="selected"><?php echo get_cat_name($option); ?></option>
		
	<?php endforeach; ?>
	</select>
	<div class="clear"></div>
	<p class="description">From the available categories on the <strong>left</strong>, choose which to show on the front page. Chosen categories will be on the right side. (Seven categories max)</p>
<?php
}

/**
 * Renders the a text field for omitting certain categories from the front page category blocks.
 *
 * @since CalPress 2.0
 */
function calpress_settings_category_block_omit(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[omit_category_block]" id="calpress_theme_options[omit_category_block]" type="text" value="<?php echo esc_attr($options['omit_category_block']); ?>" class="regular-text">
	<p class="description">Enter a <strong>comma separated</strong> list of category IDs to omit from the category blocks on the front page.</p>
<?php	
}

/**
 * Renders the a text fields for creating a breaking news callout.
 *
 * @since CalPress 2.0
 */
function calpress_settings_big_news(){
	$options = calpress_get_theme_options(); 
	
	$classred[0] = $options['big_news'][0] != '' ? ' style="background-color:#cdffc3;"' : '';
	$classred[1] = $options['big_news'][1] != '' ? ' style="background-color:#cdffc3;"' : '';
	$classred[2] = $options['big_news'][2] != '' ? ' style="background-color:#cdffc3;"' : '';
	$classred[3] = $options['big_news'][3] != '' ? ' style="background-color:#cdffc3;"' : '';	
	
	?>
	<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;"><label for="calpress_theme_options[big_news][0]">*News topic: </label></td>
			<td style="height:33px; padding:0; margin:0;"><input name="calpress_theme_options[big_news][0]" id="calpress_theme_options[big_news][0]" type="text" value="<?php echo esc_attr($options['big_news'][0]); ?>" class="regular-text"<?php echo $classred[0]; ?></td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;"><label for="calpress_theme_options[big_news][1]">*News Headline: </label></td>
			<td style="height:33px; padding:0; margin:0;"><input name="calpress_theme_options[big_news][1]" id="calpress_theme_options[big_news][0]" type="text" value="<?php echo esc_attr($options['big_news'][1]); ?>" class="regular-text"<?php echo $classred[1]; ?>></td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;"><label for="calpress_theme_options[big_news][2]">Link (optional): </label></td>
			<td style="height:33px; padding:0; margin:0;"><input name="calpress_theme_options[big_news][2]" id="calpress_theme_options[big_news][0]" type="text" value="<?php echo esc_attr($options['big_news'][2]); ?>" class="regular-text"<?php echo $classred[2]; ?>></td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;"><label for="calpress_theme_options[big_news][3]">Description (optional): </label></td>
			<td style="height:33px; padding:0; margin:0;"><input name="calpress_theme_options[big_news][3]" id="calpress_theme_options[big_news][0]" type="text" value="<?php echo esc_attr($options['big_news'][3]); ?>" class="regular-text"<?php echo $classred[3]; ?>></td>
		</tr>
		</tbody>
	</table>
	<p class="description">For a big news story, you can fill out the above forms to add a callout at the top of the page. The first two are required. To remove, simply blank out the fields and save this page. (When filled out, they will appear green to let you know they're activated.)</p>
<?php
}

/**
 * Renders the form of whether to disable the "insert image into post" option
 *
 * @since CalPress 2.0
 */
function calpress_settings_featured_stories(){
	$options = calpress_get_theme_options(); 
	wp_parse_args($options['featured_stories'], array(array('',''), array('',''), array('',''), array('',''), array('','')));
	
	?>
	<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
		<tr>
			<td>Label for story:</td>
			<td>Numerical story id:</td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;">
				<input name="calpress_theme_options[featured_stories][0][0]" id="calpress_theme_options[featured_stories][0][0]" type="text" value="<?php echo esc_attr($options['featured_stories'][0][0]); ?>" class="regular-text">
			</td>
			<td style="height:33px; padding:0; margin:0; width:100px;">
				<input name="calpress_theme_options[featured_stories][0][1]" id="calpress_theme_options[featured_stories][0][1]" type="text" value="<?php echo esc_attr($options['featured_stories'][0][1]); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;">
				<input name="calpress_theme_options[featured_stories][1][0]" id="calpress_theme_options[featured_stories][1][0]" type="text" value="<?php echo esc_attr($options['featured_stories'][1][0]); ?>" class="regular-text">
			</td>
			<td style="height:33px; padding:0; margin:0; width:100px;">
				<input name="calpress_theme_options[featured_stories][1][1]" id="calpress_theme_options[featured_stories][1][1]" type="text" value="<?php echo esc_attr($options['featured_stories'][1][1]); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;">
				<input name="calpress_theme_options[featured_stories][2][0]" id="calpress_theme_options[featured_stories][2][0]" type="text" value="<?php echo esc_attr($options['featured_stories'][2][0]); ?>" class="regular-text">
			</td>
			<td style="height:33px; padding:0; margin:0; width:100px;">
				<input name="calpress_theme_options[featured_stories][2][1]" id="calpress_theme_options[featured_stories][2][1]" type="text" value="<?php echo esc_attr($options['featured_stories'][2][1]); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;">
				<input name="calpress_theme_options[featured_stories][3][0]" id="calpress_theme_options[featured_stories][3][0]" type="text" value="<?php echo esc_attr($options['featured_stories'][3][0]); ?>" class="regular-text">
			</td>
			<td style="height:33px; padding:0; margin:0; width:100px;">
				<input name="calpress_theme_options[featured_stories][3][1]" id="calpress_theme_options[featured_stories][3][1]" type="text" value="<?php echo esc_attr($options['featured_stories'][3][1]); ?>" class="regular-text">
			</td>
		</tr>
		<tr>
			<td style="height:33px; padding:0 5px; margin:0;">
				<input name="calpress_theme_options[featured_stories][4][0]" id="calpress_theme_options[featured_stories][4][0]" type="text" value="<?php echo esc_attr($options['featured_stories'][4][0]); ?>" class="regular-text">
			</td>
			<td style="height:33px; padding:0; margin:0; width:100px;">
				<input name="calpress_theme_options[featured_stories][4][1]" id="calpress_theme_options[featured_stories][4][1]" type="text" value="<?php echo esc_attr($options['featured_stories'][4][1]); ?>" class="regular-text">
			</td>
		</tr>
		</tbody>
	</table>
	<p class="description">Enter in a label and story ID to featured individual stories on the front page.</p>
<?php	
}

/**
 * Renders the form that allows user to specify social media share buttons
 *
 * @since CalPress 2.0
 */
function calpress_settings_share_code(){
	$options = calpress_get_theme_options(); 
	?>
	<textarea name="calpress_theme_options[share_code]" id="calpress_theme_options[share_code]" rows="3" cols="50" class="large-text code" style="font-family: 'Courier New', Courier, monospace; font-size: 13px; color:#202020;"><?php echo esc_textarea($options['share_code']); ?></textarea>
	<p class="description">Paste in embed code for share buttons above articles.</p>
<?php
}

/**
 * Renders the form for user to enter their twitter handle
 *
 * @since CalPress 2.0
 */
function calpress_settings_twitter_handle(){
	$options = calpress_get_theme_options(); 
	?>
	<input name="calpress_theme_options[twitter_handle]" id="calpress_theme_options[twitter_handle]" type="text" value="<?php echo esc_attr($options['twitter_handle']); ?>" class="regular-text">
	<p class="description">Enter the main twitter handle for this website. Don't include the <code>@</code> symbol.</p>
<?php
}

/**
 * Renders the form of whether to disable the "insert image into post" option
 *
 * @since CalPress 2.0
 */
function calpress_settings_insert_image_into_post(){
	$options = calpress_get_theme_options();
	?>
	<select name="calpress_theme_options[insert_image_into_post]" id="calpress_theme_options[insert_image_into_post]">
		<option <?php if($options['insert_image_into_post'] == "true") echo "selected=\"selected\""; ?> value="true">Yes</option>
		<option <?php if($options['insert_image_into_post'] == "false") echo "selected=\"selected\""; ?> value="false">No</option>
	</select>
	<p class="description">This will disable the ability to insert images into a post body, forcing user to only use inline or lead art options.</p>
<?php
}

/**
 * Renders the form of whether to show author profile below posts
 *
 * @since CalPress 2.0
 */
function calpress_settings_show_author_profile_on_posts(){
	$options = calpress_get_theme_options();
	?>
	<select name="calpress_theme_options[show_author_profile_on_posts]" id="calpress_theme_options[show_author_profile_on_posts]">
		<option <?php if($options['show_author_profile_on_posts'] == "true") echo "selected=\"selected\""; ?> value="true">Yes</option>
		<option <?php if($options['show_author_profile_on_posts'] == "false") echo "selected=\"selected\""; ?> value="false">No</option>
	</select>
	<p class="description">Display short author profile below each article they write?</p>
<?php
}

/**
 * Renders the form for user to fill out comment policy
 *
 * @since CalPress 2.0
 */
function calpress_settings_comment_policy(){
	$options = calpress_get_theme_options();
	?>
	<textarea name="calpress_theme_options[comment_policy]" id="calpress_theme_options[comment_policy]" rows="4" cols="30" class="large-text"><?php echo esc_textarea($options['comment_policy']); ?></textarea>
	<p class="description">This message will be presented above the comment form. Please describe a brief one-paragraph comment policy for your site.</p>
<?php
}

/**
 * Renders the question whether this theme should support earlier versions of CalPress
 *
 * @since CalPress 2.0
 */
function calpress_settings_support_legacy(){
	$options = calpress_get_theme_options();
	?>
	<select name="calpress_theme_options[legacy_calpress]" id="calpress_theme_options[legacy_calpress]">
		<option <?php if($options['legacy_calpress'] == "true") echo "selected=\"selected\""; ?> value="true">Yes</option>
		<option <?php if($options['legacy_calpress'] == "false") echo "selected=\"selected\""; ?> value="false">No</option>
	</select>
	<p class="description">Select yes if you have used a previous version of CalPress and wish to support older features such as custom fields on older posts. This theme will automatically retrieve values from the database.</p>
<?php
}

/**
 * Renders the question of whether to show the comment count on article on the front page.
 *
 * @since CalPress 2.0
 */
function calpress_settings_comment_count(){
	$options = calpress_get_theme_options(); ?>
	<select name="calpress_theme_options[comment_count]" id="calpress_theme_options[comment_count]">
		<option <?php if($options['comment_count'] == "true") echo "selected=\"selected\""; ?> value="true">Yes</option>
		<option <?php if($options['comment_count'] == "false") echo "selected=\"selected\""; ?> value="false">No</option>
	</select>
	<p class="description">Select <strong>yes</strong> if you would like to show the number of comments for each article on the front page.</p>
<?php	
}

/**
 * Renders the question of how the front page should be presented
 *
 * @since CalPress 2.0
 */
function calpress_settings_front_layout(){
	$options = calpress_get_theme_options();
	$post_formats = calpress_return_all_featured_post_formats();
	?>
	<select name="calpress_theme_options[front_page_layout]" id="calpress_theme_options[front_page_layout]">
<?php 	foreach($post_formats as $key => $post_format): ?>

		<option <?php if($options['front_page_layout'] == $key) echo 'selected="selected"'; ?> value="<?php echo $key; ?>"><?php echo $post_format; ?></option>
<?php 	endforeach; ?>
	</select>
	<p class="description">Choose the layout for the front page.</p>
<?php	
}

/**
 * Renders the question about legacy video
 *
 * @since CalPress 2.0
 */
function calpress_settings_legacy_video(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[legacy_video]" id="calpress_theme_options[legacy_video]" type="text" value="<?php echo esc_url($options['legacy_video']); ?>" class="regular-text">
	<p class="description">Enter the URL where your videos are hosted if you are using legacy video options.</p>
<?php
}

/**
 * Renders the question about legacy SoundSlides location
 *
 * @since CalPress 2.0
 */
function calpress_settings_legacy_soundslides(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[legacy_soundslides]" id="calpress_theme_options[legacy_soundslides]" type="text" value="<?php echo esc_url($options['legacy_soundslides']); ?>" class="regular-text">
	<p class="description">Path to root soundslides directory. CalPress will look for SoundsSlides in this folder + plus /YYYY/MM/ of the post added.</p>
<?php
}

/**
 * Renders the question about legacy JW Player location
 *
 * @since CalPress 2.0
 */
function calpress_settings_legacy_jw_player(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[jw_player]" id="calpress_theme_options[jw_player]" type="text" value="<?php echo esc_url($options['jw_player']); ?>" class="regular-text">
	<p class="description">JW Player used to display legacy videos. You can get <a href="http://www.longtailvideo.com/">JW Player here</a>.</p>
<?php
}

/**
 * Renders the question about legacy JW Player Theme location
 *
 * @since CalPress 2.0
 */
function calpress_settings_legacy_jw_theme(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[jw_theme]" id="calpress_theme_options[jw_theme]" type="text" value="<?php echo esc_url($options['jw_theme']); ?>" class="regular-text">
	<p class="description">Location of the theme to use for JW Player.</p>
<?php
}

/**
 * Renders the question about how the more text should display
 *
 * @since CalPress 2.0
 */
function calpress_settings_more_text(){
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[more_text]" id="calpress_theme_options[more_text]" type="text" value="<?php echo esc_attr($options['more_text']); ?>" class="regular-text">
	<p class="description">Enter the way you want text cut-offs should appear on the front page when excerpts are too long.</p>
<?php	
}

/**
 * Renders the field to specify the color bar at the top of the page.
 *
 * @since CalPress 2.0
 */
function calpress_settings_color_bar() {
	$options = calpress_get_theme_options();
	?>
	<input type="text" name="calpress_theme_options[theme_color]" id="link-color" value="<?php echo esc_attr( $options['theme_color'] ); ?>" />
	<a href="#" class="pickcolor hide-if-no-js" id="link-color-example"></a>
	<input type="button" class="pickcolor button hide-if-no-js" value="<?php esc_attr_e( 'Select a Color', 'calpress' ); ?>" />
	<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
	<br />
	<span><?php printf( __( 'Default color: %s', 'calpress' ), '<span id="default-color">#000000</span>' ); ?></span>
	<?php
}

/**
 * Renders text field for entering Google Verification code.
 *
 * @since CalPress 2.0
 */
function calpress_settings_google_verification() {
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[google_verification]" id="calpress_theme_options[google_verification]" type="text" value="<?php echo $options['google_verification']; ?>" class="regular-text code">
	<p class="description">(optional) Enter in your <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=35179">Google Verification</a> code from Google Webmaster Tools.</p>
<?php
}

/**
 * Renders text field for entering Google Analytics code.
 *
 * @since CalPress 2.0
 */
function calpress_settings_google_analytics() {
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[google_analytics]" id="calpress_theme_options[google_analytics]" type="text" value="<?php echo esc_attr($options['google_analytics']); ?>" class="regular-text code">
	<p class="description">(optional) Enter in your Google Analytics account code. It <strong>must</strong> be formatted like <code>UA-XXXXXXX-X</code> with more or fewer digits.</p>
<?php
}

/**
 * Renders text field for entering extra javascript for the document head.
 *
 * @since CalPress 2.0
 */
function calpress_settings_extra_javascript() {
	$options = calpress_get_theme_options();
	?>
	<textarea name="calpress_theme_options[extra_js_head]" id="calpress_theme_options[extra_js_head]" rows="8" cols="50" class="large-text code" style="font-family: 'Courier New', Courier, monospace; font-size: 13px; color:#202020;"><?php echo esc_textarea($options['extra_js_head']); ?></textarea>
	<p class="description">(optional) Paste in any additional javascript embed code you want to appear in the <code>&lt;head&gt;</code> of all pages. Be sure to include <code>&lt;script&gt;</code> tags!</p>
<?php
}

/**
 * Renders text field for entering extra css for the document head.
 *
 * @since CalPress 2.0
 */
function calpress_settings_extra_css() {
	$options = calpress_get_theme_options();
	?>
	<textarea name="calpress_theme_options[extra_css_head]" id="calpress_theme_options[extra_css_head]" rows="8" cols="50" class="large-text code" style="font-family: 'Courier New', Courier, monospace; font-size: 13px; color:#202020;"><?php echo esc_textarea($options['extra_css_head']); ?></textarea>
	<p class="description">(optional) Paste in any additional CSS styles you want to appear in the <code>&lt;head&gt;</code> of all pages. This is a great way to add customizations that won't be overwritten during theme upgrades.</p>
<?php
}

/**
 * Renders input field for entering facebook id.
 *
 * @since CalPress 2.0
 */
function calpress_settings_facebook_id() {
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[facebook_id]" id="calpress_theme_options[facebook_id]" type="text" value="<?php echo esc_attr($options['facebook_id']); ?>" class="regular-text code">
	<p class="description">(optional) Enter the ID for your Facebook Application. (log into Facebook and visit <a href="https://developers.facebook.com/apps" target="_blank">this link to see your applications</a>.)</p>
<?php
}

/**
 * Renders input field for entering Facebook secret.
 *
 * @since CalPress 2.0
 */
function calpress_settings_facebook_secret() {
	$options = calpress_get_theme_options();
	?>
	<input name="calpress_theme_options[facebook_secret]" id="calpress_theme_options[facebook_secret]" type="text" value="<?php echo esc_attr($options['facebook_secret']); ?>" class="regular-text code">
	<p class="description">(optional) Enter the app secret key for your Facebook Application. (log into Facebook and visit <a href="https://developers.facebook.com/apps" target="_blank">this link to see your applications</a>.)</p>
<?php
}

/**
 * Renders input field for entering Facebook button layout.
 *
 * @since CalPress 2.0
 */
function calpress_settings_facebook_layout() {
	$options = calpress_get_theme_options();
	?>
	<input type="radio" name="calpress_theme_options[facebook_button_layout]" id="facebook_button_layout1" value="standard" <?php checked( $options['facebook_button_layout'], 'standard' ); ?>>
	<label for="facebook_button_layout1"> Standard</label><br />
	<input type="radio" name="calpress_theme_options[facebook_button_layout]" id="facebook_button_layout2" value="button_count" <?php checked( $options['facebook_button_layout'], 'button_count' ); ?>>
	<label for="facebook_button_layout2"> Button with counter</label><br />
	<input type="radio" name="calpress_theme_options[facebook_button_layout]" id="facebook_button_layout3" value="box_count" <?php checked( $options['facebook_button_layout'], 'box_count' ); ?>>
	<label for="facebook_button_layout3"> Box with counter</label><br />
<?php
}

/**
 * Renders input field for choosing like or recommend.
 *
 * @since CalPress 2.0
 */
function calpress_settings_facebook_action() {
	$options = calpress_get_theme_options();
	?>
	<input type="radio" name="calpress_theme_options[facebook_button_action]" id="facebook_button_action1" value="like" <?php checked( $options['facebook_button_action'], 'like' ); ?>>
	<label for="facebook_button_layout1"> Like</label><br />
	<input type="radio" name="calpress_theme_options[facebook_button_action]" id="facebook_button_action2" value="recommend" <?php checked( $options['facebook_button_action'], 'recommend' ); ?>>
	<label for="facebook_button_layout2"> Recommend</label><br />
<?php
}

/**
 * Renders input field for choosing like or recommend.
 *
 * @since CalPress 2.0
 */
function calpress_settings_facebook_send() {
	$options = calpress_get_theme_options();
	?>
	<input type="radio" name="calpress_theme_options[facebook_send_button]" id="facebook_send_button1" value="true" <?php checked( $options['facebook_send_button'], 'true' ); ?>>
	<label for="facebook_send_button1"> Enabled</label><br />
	<input type="radio" name="calpress_theme_options[facebook_send_button]" id="facebook_send_button2" value="false" <?php checked( $options['facebook_send_button'], 'false' ); ?>>
	<label for="facebook_send_button2"> Disabled</label><br />
<?php
}

/**
 * Returns an array of possible post categories from the site.
 *
 * @since CalPress 2.0
 * @return $array List of categories.
 */
function calpress_post_categories() {
	$front_category = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC'));
	return apply_filters( 'calpress_post_categories', $front_category);
}

/**
 * Returns the options array for CalPress.
 *
 * @since CalPress 2.0
 */
function calpress_theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon('options-general'); ?>
		<?php $theme_name = function_exists( 'wp_get_theme' ) ? wp_get_theme() : get_current_theme(); ?>
		<h2><?php printf( __( '%s Theme Options', 'calpress' ), $theme_name ); ?></h2>
		<?php settings_errors(); ?>
		<h3>Helpful Links</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"></th>
					<td><a href="customize.php">Customize the front page layout, as well as header/background images.</a></td>
				</tr>
			</tbody>
		</table>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'calpress_options' );
				do_settings_sections( 'theme_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see calpress_theme_options_init()
 * @todo set up Reset Options action
 *
 * @since CalPress 2.0
 */
function calpress_theme_options_validate( $input ) {
	$output = $defaults = calpress_get_default_theme_options();
	
	// Color scheme must be in our array of color scheme options
	if ( isset( $input['front_category'] ) )
		$output['front_category'] = $input['front_category'];
		
	if ( isset( $input['featured_category'] ) )
		$output['featured_category'] = $input['featured_category'];
		
	if ( isset( $input['legacy_calpress'] ) )
		$output['legacy_calpress'] = $input['legacy_calpress'];
		
	if ( isset( $input['legacy_soundslides'] ) ){
		if(preg_match('/\/$/', $input['legacy_soundslides'])){
			$output['legacy_soundslides'] = esc_url_raw($input['legacy_soundslides']);
		} else {
			$output['legacy_soundslides'] = esc_url_raw($input['legacy_soundslides']). '/';
		}	
	}
		
	if ( isset( $input['legacy_video'] ) ){
		if(preg_match('/\/$/', $input['legacy_soundslides'])){
			$output['legacy_video'] = esc_url_raw($input['legacy_video']);
		} else {
			$output['legacy_video'] = esc_url_raw($input['legacy_video']). '/';
		}
	}
	
	if ( isset( $input['jw_player'] ) )
		$output['jw_player'] = esc_url_raw($input['jw_player']);

	if ( isset( $input['jw_theme'] ) )
		$output['jw_theme'] = esc_url_raw($input['jw_theme']);

	
	if ( isset( $input['front_page_layout'] ) && @file_exists(CHILDTHEMEFULLPATH . '/featured-' . (string) $input['front_page_layout'] . '.php')){
		$output['front_page_layout'] = $input['front_page_layout'];
	} else {
		add_settings_error('front_page_layout', 'front_page_layout', 'Error: No featured-'. (string) $input['front_page_layout'] . '.php file found in childtheme directory!', 'error');
	}
	
	if ( isset($input['comment_policy'] ) )
		$output['comment_policy'] = esc_attr($input['comment_policy']);
		
	if ( isset($input['featured_stories'] ) )
		$output['featured_stories'] = (array) $input['featured_stories'];
	
	if ( isset( $input['big_news'] ) ){
		if(isset($input['big_news'][2])) $input['big_news'][2] = esc_url($input['big_news'][2]);
		
		$output['big_news'] = (array) $input['big_news'];
	}
	
	if ( isset( $input['share_code'] ) )
		$output['share_code'] = $input['share_code'];
		
	if ( isset( $input['twitter_handle'] ) )
		$output['twitter_handle'] = strip_tags($input['twitter_handle']);
	
	if ( isset( $input['insert_image_into_post'] ) )
		$output['insert_image_into_post'] = (string) $input['insert_image_into_post'];
		
	if ( isset( $input['show_author_profile_on_posts'] ) )
		$output['show_author_profile_on_posts'] = $input['show_author_profile_on_posts'];
		
	if ( isset( $input['category_block'] ) )
		$output['category_block'] = (array) $input['category_block'];
		
	if ( isset( $input['omit_category_block'] ) && !preg_match('/[a-z]/i', $input['omit_category_block'])){
		$temp = explode(",", strip_tags($input['omit_category_block']));
		array_walk($temp, 'trim_value');
		$output['omit_category_block'] = (string) implode(",", $temp);
	} else {
		add_settings_error('omit_category_block', 'omit_category_block', 'Error: Use category ID numbers only to omit categories from blocks.', 'error');
	}
	
	if ( isset( $input['comment_count'] ) )
		$output['comment_count'] = (string) $input['comment_count'];
	
	if ( isset( $input['more_text'] ) )
		$output['more_text'] = $input['more_text'];

	// Link color must be 3 or 6 hexadecimal characters
	if ( isset( $input['theme_color'] ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['theme_color'] ) ){
		$output['theme_color'] = '#' . strtolower( ltrim( $input['theme_color'], '#' ) );
	} else {
		add_settings_error('theme_color', 'theme_color', 'Error: Color bar code not properly formed hex value. Must include # with three or six alphanumerical digits.');
	}

	//TO DO: Actual validation
	if ( isset( $input['google_verification'] ) )
		$output['google_verification'] = strip_tags($input['google_verification']);
	
	if ( isset( $input['google_analytics'] ) && preg_match('/UA-[0-9]+-[0-9]+/i', $input['google_analytics'] ) ) {
		$output['google_analytics'] = strip_tags($input['google_analytics']);
	} else{
		if($input['google_analytics'] != "") {
			add_settings_error('google_analytics', 'bad_google_analytics', 'Error: Google Analytics code must formatted as UA-XXXXXX-X with more or fewer numbers.', 'error');
		}
	}

	if ( isset( $input['extra_js_head'] ) && (preg_match('/\<script+/i', $input['extra_js_head']) || $input['extra_js_head'] == "")){
		$output['extra_js_head'] = $input['extra_js_head'];
	} else {
		add_settings_error('extra_js_head', 'extra_js_head', 'Warning: Extra Javascript embed code requires use of the &lt;script&gt; tag. Without this, raw code may appear on the page.', 'updated');
		$output['extra_js_head'] = $input['extra_js_head'];
	}
	
	if ( isset( $input['extra_css_head'] ) )
		$output['extra_css_head'] = strip_tags(trim($input['extra_css_head']));

	if ( isset( $input['facebook_id'] ) && is_numeric($input['facebook_id'])){
		$output['facebook_id'] = $input['facebook_id'];
	} else {
		if (isset($input['facebook_id']) && $input['facebook_id'] != ""){
			add_settings_error('facebook_id', 'calpress_theme_options[facebook_id]', 'Error: Facebook App ID/API Key can only be numbers.', 'error');
		}
	}
	
	if ( isset( $input['facebook_secret'] ) )
		$output['facebook_secret'] = $input['facebook_secret'];

	if ( isset( $input['facebook_button_layout'] ) )
		$output['facebook_button_layout'] = $input['facebook_button_layout'];
	
	if ( isset( $input['facebook_button_action'] ) )
		$output['facebook_button_action'] = $input['facebook_button_action'];

	if ( isset( $input['facebook_send_button'] ) )
		$output['facebook_send_button'] = $input['facebook_send_button'];
	
	return apply_filters( 'calpress_theme_options_validate', $output, $input, $defaults );
}

function calpress_add_menu(){
	add_menu_page( 'CalPress Options', 'CalPress Options', 'edit_theme_options', 'calpress_menu', 'calpress_menu', '', 95 );
	add_submenu_page('calpress_menu', 'Front Page Customizer', 'Customizer', 'edit_theme_options', 'calpress_customizer', 'calpress_menu' );	
}
//add_action('admin_menu', 'calpress_add_menu');

function calpress_menu(){
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>CalPress 2.0 Options</h2>
		<h3>Helpful Links</h3>

	</div>
<?php
}

?>