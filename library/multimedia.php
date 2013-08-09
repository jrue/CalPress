<?php
/**
 * Full multimedia API support. This allows you to easily declare new multimedia
 * elements to support as lead art, inline art or advanced options.
 *
 * Uses calpress_add_media_support() and create functions to handle media.
 *
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @uses calpress_add_media_support()
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */

/**
 * Use this function to add additional multimedia support in posts. 
 * When using this function, you may need to include up to three additional
 * functions for displaying the form field, sanitizing the output, and 
 * displaying the media on the post page. 
 *
 * Default Values in parenthesis:
 * 
 * 'media_id' (string) You must set this as the slug for this media type
 * 'field_label' (string) The label html id, usually a capitalized version of media id
 * 'caption_label' (string) The label html for the caption. If blank, no caption offered.  
 * 'render_form' (string) The function to render the input form on posts admin
 * 'sanitize_output' (string) The function to sanitize output with two arguments, input value and post_id
 * 'display_function' (string) The function to display the multimedia on front end of site.
 * 'error_message' (string) An error message if sanitize validation fails
 * 'description' => (string) Description that appears in the admin below form field
 * 'lead' => (bool) This is a lead art item
 * 'inline' => (bool) This is an inline art item
 * 'default_value' => (string) The default value for this media
 * 'advanced' => (bool) This is an advanced option, initially hidden 
 * 'support_scripts' => (string|bool) Function name to enqueue supported javascripts/css in head
 *
 * @global obj $calpress_supported_multimedia An object to store all of the supported multimedia
 * @param array $args An associative array to override the default values.
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_add_media_support($args){
	global $calpress_supported_multimedia;
	
	//set global in case this was disabled by child theme
	if(!isset($calpress_supported_multimedia))
		$calpress_supported_multimedia = new stdClass;
		
	$default_args = array(
		'media_id' => false,
		'field_label' => false,
		'caption_label' => '',
		'render_form' => false,
		'sanitize_output' => false,
		'display_function' => false,
		'error_message' => 'Could not save multimedia data.',
		'description' => '',
		'lead' => false,
		'inline' => false,
		'default_value' => '',
		'advanced' => false,
		'support_scripts' => false
	);
	
	$return_args = wp_parse_args($args, $default_args);
	
	//no spaces allowed
	$return_args['media_id'] = sanitize_title( (string) $return_args['media_id']);
	$return_args['advanced'] = (bool) $return_args['advanced'];
	
	//make sure there are functions written
	if($return_args['render_form'] !== false)
		if(!function_exists($return_args['render_form']))
			return false;
			
	if($return_args['sanitize_output'] !== false)
		if(!function_exists($return_args['sanitize_output']))
			return false;
			
	if($return_args['display_function'] !== false)
		if(!function_exists($return_args['display_function']))
			return false;
			
	if(!$return_args['media_id'] || !$return_args['field_label'])
		return false;
		
	$media_id = $return_args['media_id'];
	
	$calpress_supported_multimedia->$media_id = $return_args;
	
	return true;
}

//None
calpress_add_media_support(array(
	'media_id' => 'none',
	'field_label' => 'None',
	'lead' => true,
	'inline' => true	
));

//Featured Image
calpress_add_media_support(array(
	'media_id' => 'image',
	'field_label' => 'Featured Image',
	'sanitize_output' => 'featured_image_sanitize',
	'display_function' => 'calpress_featured_image',
	'error_message' => 'No featured image was set for this post.',
	'lead' => true,
	'inline' => false
));

//Photo Gallery
calpress_add_media_support(array(
	'media_id' => 'gallery',
	'field_label' => 'Photo Gallery',
	'sanitize_output' => 'calpress_photo_gallery_verify',
	'error_message' => 'Photo galleries require more than one photo uploaded.',
	'display_function' => 'calpress_photo_gallery',
	'description' => 'Leave media area blank for photo gallery',
	'lead' => true,
	'inline' => true,
	'support_scripts' => 'calpress_photo_gallery_scripts'
));

//Vimeo
calpress_add_media_support(array(
	'media_id' => 'vimeo',
	'field_label' => 'Vimeo',
	'description' => 'Enter in the URL for your Vimeo video. It should end with a number, like this <strong>http://vimeo.com/2391019</strong>',
	'caption_label' => 'Caption for Vimeo:',
	'render_form' => 'calpress_vimeo_input_form',
	'sanitize_output' => 'calpress_sanitize_vimeo',
	'error_message' => "Couldn't recognize Vimeo URL or video doesn't exist. It should end with a number, like this <strong>http://vimeo.com/2391019</strong>",
	'display_function' => 'calpress_vimeo',
	'lead' => true,
	'inline' => true
));

//YouTube
calpress_add_media_support(array(
	'media_id' => 'youtube',
	'field_label' => 'YouTube',
	'description' => 'Enter in the URL for your YouTube video. Make sure it\'s a page where <strong>only</strong> the video you want is displayed, <strong>not</strong> a playlist or channel. The key is to click on the title of the video. Ex: <strong>http://www.youtube.com/watch?v=fao6DeOPvPw</strong>',
	'caption_label' => 'Caption for YouTube video:',
	'error_message' => "Can't recognize YouTube URL, video doesn't exist or video is set to private. Did you paste in a channel page by accident?",
	'render_form' => 'calpress_youtube_input_form',
	'sanitize_output' => 'calpress_validate_youtube',
	'display_function' => 'calpress_youtube',
	'lead' => true,
	'inline' => true
));

//Video
calpress_add_media_support(array(
	'media_id' => 'video',
	'field_label' => 'Hosted Video',
	'description' => 'If you are self-hosting on a media server, enter the URL of the video.',
	'caption_label' => 'Caption for your self-hosted video:',
	'error_message' => "The URL provided for self-hosted video does not resolve or was not found.",
	'render_form' => 'calpress_video_input_form',
	'sanitize_output' => 'calpress_sanitize_video',
	'display_function' => 'calpress_video',
	'lead' => true,
	'inline' => true,
	'support_scripts' => 'calpress_add_video_scripts'
));

//Video
calpress_add_media_support(array(
	'media_id' => 'embed',
	'field_label' => 'Embed Code',
	'description' => 'If you have embed code from a third-party website, you can paste it in this box. Make sure to only paste code from trusted sites, as this can pose some security risks. If there is a <code>width</code> setting, it is recommended to change it to <code>100%</code>.',
	'render_form' => 'calpress_embed_input_form',
	'caption_label' => 'Caption to appear under embed code:',
	'error_message' => "Problem with the embed code submitted.",
	'sanitize_output' => 'calpress_sanitize_embed',
	'display_function' => 'calpress_embed',
	'lead' => true,
	'inline' => true
));

calpress_add_media_support(array(
	'media_id' => 'inline_image',
	'field_label' => 'Inline Image',
	'description' => 'Paste in the URL for an image. Only one URL is allowed, otherwise you will need to add an additional inline element. For images that were uploaded, use the upload-dialogue box above the post and then click the <strong>FILE URL</strong> button. Paste the url here.',
	'error_message' => 'Could not find an image for the URL entered in the inline image spot. Make sure you\'re linking to the actual image file.',
	'render_form' => 'calpress_inline_image_form',
	'sanitize_output' => 'calpress_inline_image_sanitize',
	'display_function' => 'calpress_inline_image',
	'default_value' => false,
	'inline' => true,
	'support_scripts' => 'calpress_add_fancybox_scripts'
));

calpress_add_media_support(array(
	'media_id' => 'inline_audio',
	'field_label' => 'Inline Audio',
	'description' => 'Paste in the URL for an <strong>audio</strong> piece. Only one URL is allowed, otherwise you will need to add an additional inline element. For audio pieces that were uploaded, look for the <strong>LINK URL</strong> in the upload dialogue box and paste here.',
	'error_message' => 'Could not find a valid audio file for the URL entered in the inline audio spot.',
	'render_form' => 'calpress_inline_audio_form',
	'sanitize_output' => 'calpress_inline_audio_sanitize',
	'display_function' => 'calpress_inline_audio',
	'default_value' => false,
	'inline' => true,
	'support_scripts' => 'calpress_add_audio_scripts'
));

calpress_add_media_support(array(
	'media_id' => 'related_links',
	'field_label' => 'Related Links Category',
	'description' => 'Enter a <a href="/wp-admin/edit-tags.php?taxonomy=link_category">link category</a> ID number to display related links on this topic. You can find the id number by clicking a link category, and looking for the tag_id in the URL.',
	'render_form' => 'calpress_related_links_input_form',
	'error_message' => 'Related links do not match link categories or the numbers entered did validate.',
	'sanitize_output' => 'calpress_sanitize_related_links',
	'display_function' => 'calpress_related_links',
	'inline' => true
));

calpress_add_media_support(array(
	'media_id' => 'manual_links',
	'field_label' => 'Manual Links',
	'description' => 'This is a manual way to add links to your article. Enter links, one per line, in this exact format: <code>[link text](http://example.com/)</code>',
	'error_message' => 'Manual links did not validate. The formatting has to be exact, one per line. Example: <code>[link text](http://example.com/)</code>',
	'render_form' => 'calpress_manual_links_input_form',
	'sanitize_output' => 'calpress_sanitize_manual_links',
	'display_function' => 'calpress_manual_links',
	'inline' => true
));

calpress_add_media_support(array(
	'media_id' => 'extracss',
	'field_label' => 'Extra CSS',
	'description' => 'Don\'t include <code>&lt;style&gt;</code> tags.',
	'error_message' => 'Please do not include <code>&lt;style&gt;</code> tags in the extra CSS field.',
	'render_form' => 'calpress_extra_css_input_form',
	'sanitize_output' => 'calpress_sanitize_extra_css',
	'support_scripts' => 'calpress_extra_css',
	'advanced' => true
));

calpress_add_media_support(array(
	'media_id' => 'extrajs',
	'field_label' => 'Extra JavaScript',
	'description' => 'Don\'t include <code>&lt;script&gt;</code> tags.',
	'error_message' => 'Please do not include <code>&lt;script&gt;</code> tags in the extra JavaScript field.',
	'render_form' => 'calpress_extra_js_input_form',
	'sanitize_output' => 'calpress_sanitize_extra_js',
	'support_scripts' => 'calpress_extra_js',
	'advanced' => true
));

calpress_add_media_support(array(
	'media_id' => 'sidebar',
	'field_label' => 'Show Sidebar?',
	'description' => 'Use seldomly.',
	'render_form' => 'calpress_sidebar_input_form',
	'display_function' => 'calpress_sidebar',
	'default_value' => "true",
	'advanced' => true,
	'support_scripts' => 'calpress_sidebar_add_body_class'
));

calpress_add_media_support(array(
	'media_id' => 'override',
	'field_label' => 'Override Checks?',
	'description' => 'If selected, will eliminate checks for media above.',
	'render_form' => 'calpress_override_checks',
	'default_value' => false,
	'advanced' => true
));

//only add media support for polls if the wp-polls plugin is installed
if (function_exists('vote_poll')){
  calpress_add_media_support(array(
    'media_id' => 'wp-polls',
    'field_label' => 'WP Poll',
    'description' => 'Enter the ID number of a poll that was created.',
    'render_form' => 'calpress_wp_polls_render',
    'sanitize_output' => 'calpress_wp_polls_sanitize',
    'display_function' => 'calpress_wp_polls_display',
    'default_value' => '',
    'lead' => false,
  	'inline' => true,
  	'error_message' => 'No poll with that id found!'
  ));
}

/**
 * Unused, but required for now
 *
 * @since CalPress 0.9.7
 */
function calpress_wp_polls_render($val, $name, $id){
  
}

/**
 * WP Polls Plugin.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @param string $title Title for inline element
 * @param string $caption Caption for inline element
 * @param bool $inline is this an inline element?
 * @return string The sanitized input
 */
function calpress_wp_polls_display($input, $post_id='', $title='', $caption='', $inline=false){

  $html  = '<div class="wp-poll-inline inline-item">';
  $html .= $title ? ' <h3>' . esc_attr($title) . '</h3>' : '';
  $html .= '  <ul>';
  $html .= '   <li>' . get_poll($input, false) . '</li>';
  $html .= '  </ul>';
  $html .= $caption ? ' <p class="wp-caption">' . esc_attr($caption) . '</p>': '';
  $html .= '</div>';
  $html .= '<style type="text/css">.wp-poll-inline ul li{list-style-type:none !important;}</style>';

  return $html;
}

/**
 * TODO: Sanitization of polls
 *
 * @param string $input User input
 * @param int $post_id ID of post
 * @return int The user input as an integer
 * @since CalPress 0.9.7
 */
function calpress_wp_polls_sanitize($input, $post_id){
  $input = intval($input);
  return $input;
  
  /* TODO: Possible sanitization
  global $wpdb;
  $input = intval($input);
  $poll_exists = $wpdb->get_var( "SELECT pollq_id FROM $wpdb->wp_pollsq WHERE pollq_id = $input");
  _log($poll_exists);
  if(!is_null($poll_exists)){
    return $input;
  } else {
    return false;
  }
  */
}


/**
 * If photo gallery option is picked, make sure there is more than one photo.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return bool True if more than one image exists, false if not
 */
function calpress_photo_gallery_verify($input, $post_id){
	$images = get_children(
			array(
				'post_parent' => $post_id, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'menu_order ID'
				)
			);
		
	if(count($images) > 1){
		return true;
	} else {
		return false;
	}
	
	return false;
}

/**
 * Displays photo gallery in lead art position
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The html for a photo gallery
 */
function calpress_photo_gallery($post_id, $inline=false, $title='', $caption=''){
	$images = get_children(
			array(
				'post_parent' => $post_id, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'menu_order ID'
				)
			);
	
	if(count($images) < 2)
		return;

	$html = '
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function($){
					
					$(\'.iosSlider\').iosSlider({
						snapToChildren: true,
						scrollbar: false,
						desktopClickDrag: false,
						responsiveSlideContainer: true,
						responsiveSlides: true,
						navPrevSelector: $(\'#nextSlide\'),			
						navNextSelector: $(\'#prevSlide\'),
						navSlideSelector: $(\'.slideshow-page-number\'),
						infiniteSlider: true,
						onSliderLoaded: updatePager,
						onSlideChange: updatePager,
					});
										
					function updatePager(args) {
						$(\'.slideshow-page-number\').removeClass(\'selected\');
						$(\'.slideshow-page-number:eq(\' + args.currentSlideNumber + \')\').addClass(\'selected\');
						$(\'.slideshow-caption\').html(\'<p>\' + $(\'#slideshow-image-\' + args.currentSlideNumber + \' img\').attr(\'alt\') + \'</p>\');
					}
				});
				

			</script>'.PHP_EOL;

	$counter = 0;
	//$html .= $title ? '	<h3>'. esc_attr($title) .'</h3>'.PHP_EOL: '';
	$html .= '<div class="clearfix iosSlider" aria-label="'. __('Below is a Photo Gallery. You may consider skipping this to arrive at the story content.', 'CalPress') . '" role="complementary">'.PHP_EOL;
	$html .= '	<div class="slider">'.PHP_EOL;
	
	foreach($images as $image):

		$image_src = wp_get_attachment_image_src($image->ID, 'carousel-image');
		$image_caption = esc_attr($image->post_excerpt);
		
		$html .= '		<div id="slideshow-image-' . $counter . '" class="slide">'.PHP_EOL;
		$html .= '			<img src="' . $image_src[0] . '" alt="' . trim(strip_tags(wp_trim_words($image_caption, 55, '...'))) . '" />'.PHP_EOL;
		//$html .= '			<div class="wp-caption"><p>' . wp_trim_words($image_caption, 38, '...') . '</p></div>'.PHP_EOL;
		$html .= '		</div><!-- .slide -->'.PHP_EOL;
		
		$counter ++;
	endforeach;
	
	$html .= '	</div><!-- .slider -->'.PHP_EOL;
	$html .= '</div><!-- .iosSlider -->'.PHP_EOL;
	$html .= '<ul class="single-post-slideshow-pager clearfix">';
	$html .= '<li><img src="'. THEMEURI . '/images/arrow-left.png" alt="left arrow" id="nextSlide"></li>';
	
	for($i=0; $i < $counter; $i++):
		$html .= '<li class="slideshow-page-number">' . ($i + 1) . '</li>';	
	endfor;
	
	$html .= '<li><img src="'. THEMEURI . '/images/arrow-right.png" alt="right arrow" id="prevSlide"></li>';
	$html .= '</ul>';
	$html .= '<div class="wp-caption slideshow-caption"></div>';
	
	return $html;
}

/**
 * Enqueues scripts needed for photo gallery
 *
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_photo_gallery_scripts(){
	add_action('wp_enqueue_scripts', 
		create_function('$e=NULL', 
			'wp_enqueue_script("iosslider", "'.THEMEJS.'/jquery.iosslider.min.js", array("jquery"), "1.0.27", false);'
		), 
	10);
		
}


/**
 * Verifies that the post has a featured image.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return bool True if there is a featured image, false if not
 */
function featured_image_sanitize($input, $post_id){
	if(has_post_thumbnail($post_id))
		return true;
		
	return false;
}

/**
 * Returns featured image html.
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @return string|bool The HTML for the featured image if it exists
 */
function calpress_featured_image($post_id, $inline=false){
	$attachment_id = get_post_thumbnail_id($post_id);
	$sidebar = get_post_meta($post_id, 'sidebar', true);
	
	if(!$attachment_id)
		return false;
	
	$html = '';
	
	if($sidebar !== false && $sidebar == "false"){
		$url = wp_get_attachment_image_src($attachment_id, 'full', false);
	} else {
		$url = wp_get_attachment_image_src($attachment_id, 'post-thumbnail', false);
	}
	$alt = esc_attr(trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))));
	$description = get_post($attachment_id)->post_excerpt;

	if(!$alt)
		$alt = trim(strip_tags($description));
	
	$html .= $inline ? '<div class="inline-image">' : '';
	$html .= $inline ? '<a href="' . $url[0] .'" class="fancybox" title="See larger image">' : '';
	if($sidebar !== false && $sidebar == "false"){
		$html .= '<img src="' . $url[0] . '" style="max-width:920px; height:auto;" alt="' . trim(strip_tags($alt)) . '" />'.PHP_EOL;
	} else {
		$html .= '<img src="' . $url[0] . '" style="max-width:620px; height:auto;" alt="' . trim(strip_tags($alt)) . '" />'.PHP_EOL;
	}
	$html .= $inline ? '</a>' : '';
	$html .= '<div class="wp-caption"><p>' . $description . '</p></div>'.PHP_EOL;
	
	return $html;
}

/**
 * Displays the input form for adding Vimeo videos
 *
 * @since CalPress 0.9.7
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_vimeo_input_form($val, $name, $id){ 
	echo '<input type="text" id="'. $id .'" class="multimedia_input" name="'. $name .'" value="'. $val .'" size="60" />';
}

/**
 * Verifies that Vimeo URL input is correct.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_vimeo($input, $post_id){
	if($input != '')
		$input = esc_url(rtrim($input, "/"));
	
	// http://vimeo.com/00000
	if(preg_match('/^(http|https):\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $input, $match)){
		
		/* Too many issues sanitizing Vimeo videos from their server. Just check URL format.

		$headers = calpress_get_headers_curl('http://vimeo.com/api/v2/video/' . $match[4] . '.json', 5);
		if (strpos($headers[0], '200'))
			return $input;
		
		if (empty($headers[0])) //response timed out, still give it to them. Vimeo might be slow
			return $input;
		*/
		return $input;
	}

	return false;
}

/**
 * Returns Vimeo video html
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @return string|bool The HTML for the vimeo if it exists
 */
function calpress_vimeo($input, $post_id='', $title='', $caption='', $inline=false){
	global $wp_embed;

	$html = '';
	
	if($inline){
		$html .= '<div class="inline-item inline-vimeo">'.PHP_EOL;
		$html .= $title ? '<h3>'. esc_attr($title) . '</h3>'.PHP_EOL : '';
	}
	
	$html .= '<div class="video-container">';
	//$html .= '<iframe src="http://player.vimeo.com/video/' . calpress_get_vimeo_id_from_url($input) . '?title=0&byline=0&portrait=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	$html .=  $wp_embed->run_shortcode('[embed]' . $input .'[/embed]');
	$html .= '</div>';
	$html .= $caption ? '<div class="wp-caption">' . esc_attr($caption) . '</div>'.PHP_EOL : '';
	
	if($inline)
		$html .= '</div>';
		
	return $html;
}

/**
 * Displays the input form for adding YouTube videos
 *
 * @since CalPress 0.9.7
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_youtube_input_form($val, $name, $id){ 
	echo '<input type="text" id="' . $id . '" class="multimedia_input" name="' . $name . '" value="' . $val . '" size="60" />';
}

/**
 * Verifies that YouTube URL input is correct.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_validate_youtube($input, $post_id){
	if($input != '')
		$input = esc_url(rtrim($input, "/"));
	
	//regex for every possible YouTube ID http://stackoverflow.com/a/6382259/838158
	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $input, $match)) {
		
		return $input;//'http://www.youtube.com/watch?v=' . $match[1];

		/* issues with live sanitizing YouTube videos.

		$headers = calpress_get_headers_curl('http://gdata.youtube.com/feeds/api/videos/' . $match[1], 5);
		if (strpos($headers[0], '200'))
			return 'http://www.youtube.com/watch?v=' . $match[1];
		
		if (empty($headers[0])) //response timed out, still give it to them. YouTube might be slow
			return 'http://www.youtube.com/watch?v=' . $match[1];
		*/
	}
	
	return false;
}

/**
 * Returns YouTube video html
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @return string|bool The HTML for the YouTube if it exists
 */
function calpress_youtube($input, $post_id='', $title='', $caption='', $inline=false){
	global $wp_embed;
	$html = '';
	
	if($inline){
		$html .= '<div class="inline-item inline-youtube">'.PHP_EOL;
		$html .= $title ? '<h3>'. esc_attr($title) . '</h3>'.PHP_EOL : '';
	}
	
	$html .= '<div class="video-container">';
	$html .=  $wp_embed->run_shortcode('[embed]' . $input .'[/embed]');
	//<iframe src="http://www.youtube.com/embed/' . calpress_get_youtube_id_from_url($input) . '?modestbranding=1&showinfo=0&fs=1&controls=2&rel=0" frameborder="0" allowfullscreen></iframe>
	$html .= '</div>';
	
	$html .= $caption ? '<div class="wp-caption">' . esc_attr($caption) . '</div>'.PHP_EOL : '';

	if($inline)
		$html .= '</div>';

	return $html;	
}


/**
 * Displays the input form for adding self-hosted videos
 *
 * @since CalPress 0.9.7
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_video_input_form($val, $name, $id){
	echo '<input type="text" id="' . $id . '" class="multimedia_input" name="' . $name . '" value="' . $val . '" size="60" />';
}

/**
 * Verifies a self-hosted video exists.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_video($input, $post_id){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	
	if(!preg_match('/^http\:/i', $input) && calpress_legacy_support()){

		return $options['legacy_video'] . $input . '/' . $input . '-iPhone.m4v';
		/*
		$headers = calpress_get_headers_curl($options['legacy_video'] . $input . '/' . $input . '-iPhone.m4v');
		
		if (strpos($headers[0], '200'))
			return $options['legacy_video'] . $input . '/' . $input . '-iPhone.m4v';

		if (empty($headers[0]))
			return $options['legacy_video'] . $input . '/' . $input . '-iPhone.m4v';
		*/	
	} else {
		$headers = calpress_get_headers_curl($input);
		return $input;
		/*
		if (strpos($headers[0], '200'))
			return $input;

		if (empty($headers[0]))
			return $input;
		*/
	}
	
	return false;
}

/**
 * Returns self-hosted video html
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @return string|bool The HTML for the self-hosted video.
 */
function calpress_video($input, $post_id='', $title='', $caption='', $inline=false, $poster=''){
	//global $wp_embed;
	$html = '';
	
	if($inline)
		$html .= '<div class="inline-item inline-video">'.PHP_EOL;
		
	$html .= $title ? '<h3>'. esc_attr($title) . '</h3>'.PHP_EOL : '';

	//Bug in WordPress media element player
	//$html .= wp_video_shortcode(array('src'=>$input));
	
	$html .= '
	
	<video width="100%" height="100%" controls="controls" preload="none"' . $poster .'>
		<!-- MP4 must be first for iPad! -->
		<source src="'. $input .'" type="video/mp4" /><!-- Safari / iOS video -->
		<!-- fallback to Flash: -->
		<div class="video-container">
		<object type="application/x-shockwave-flash" data="'. THEMEURI . '/js/mediaelement/flashmediaelement.swf">
			<param name="movie" value="'. THEMEURI . '/js/mediaelement/flashmediaelement.swf" />
			<param name="flashvars" value="file=' . $input .'&controls=true" />
			<param name="wmode" value="transparent" />
			<param name="allowscriptaccess" value="always" />
			<param name="allowfullscreen" value="true" />
		</object>
		</div>
	</video>
	';
	
	$html .= $caption ? '<div class="wp-caption">' . esc_attr($caption) . '</div>'.PHP_EOL : '';
	
	if($inline)
		$html .= '</div>'.PHP_EOL;
	
	return $html;
}

/**
 * Enqueues scripts needed for lead video
 *
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_add_video_scripts(){
	add_action('wp_enqueue_scripts', 
		create_function('$e=NULL', 
			'wp_enqueue_script("media_element", "'.THEMEJS.'/mediaelement/mediaelement-and-player.min.js", array("jquery"), "2.9.1", false);'
		), 
	10);	
	add_action('wp_enqueue_scripts',
		create_function('$e=NULL',
			'wp_enqueue_style("media_element_style", "'.THEMEJS.'/mediaelement/mediaelementplayer.min.css", array("calpress"), "2.9.1", "screen, handheld");'
		),
	10);
}

/**
 * Displays the textfield form for adding embed code
 *
 * @since CalPress 0.9.7
 * @uses esc_textarea();
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_embed_input_form($val, $name, $id){
	echo '<textarea class="large-text code multimedia_input" style="font-family: "Courier New", Courier, monospace; font-size: 13px; color:#202020;" id="'.$id.'" name="'.$name.'" rows="3">' . esc_textarea($val) . '</textarea>';
	
}

/**
 * Ostensibly validates embed code, but how? 
 * Since embed code can be anything, this just returns the value.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_embed($input, $post_id){
	return $input;
}

/**
 * Returns embed code HTML
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @return string|bool The HTML for the embed code.
 */
function calpress_embed($input, $post_id='', $title='', $caption='', $inline=false){	
	$html = '';
	
	if($inline)
		$html .= '<div class="inline-item">'.PHP_EOL;
		
	$html .= $title ? '<h3>'. esc_attr($title) . '</h3>'.PHP_EOL : '';
	$html .= '<div class="embed-container">';
	$html .= $input . PHP_EOL;
	$html .= '</div>';
	$html .= $caption ? '<div class="wp-caption">' . esc_attr($caption) . '</div>'.PHP_EOL : '';

	if($inline)
		$html .= '</div>';

	return $html;
	
}


function calpress_inline_audio_form($val, $name, $id){
	
}

/**
 * Check the audio URL to see if returns a proper image mime type.
 * Supports any audio content-type.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return bool True if more than one image exists, false if not
 */
function calpress_inline_audio_sanitize($input, $post_id){
	$input = (string) esc_url($input);
	$input = preg_replace('/\?.*/', '', $input);
	
	/* (Firefox can't use content type other than MP3)
	$headers = calpress_get_headers_curl($input, 5);
	$headers_string = implode(' ', $headers);
	
	if (preg_match('/audio\/(.*?)\s/i', $headers_string, $matches) )
		return $input . '?' . urlencode($matches[0]);
	*/
	
	return $input;
}

/**
 * Returns HTML for inline audio
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The HTML code for an inline audio piece
 */
function calpress_inline_audio($input, $post_id=NULL, $title='', $caption=''){
	$url = parse_url($input);
	$query_string = 'type="audio/mp3"';//default to audio/mpeg
	
	/* Media player doesn't work with other content types in FireFox */
	//if(isset($url['query']))
		//$query_string = 'type="'. urldecode($url['query']) . '"';
	
	$input = preg_replace('/\?.*/', '', $input);
	
	$html = '<div class="inline-audio inline-item">';
	$html .= $title ? '<h3>'. esc_attr($title) .'</h3>'.PHP_EOL: ''.PHP_EOL;

	$html .= '<audio controls="controls" preload="none">'.PHP_EOL;
	$html .= '	<source src="' . $input .'" ' . trim($query_string) . ' />'.PHP_EOL;
	$html .= '	<object width="280" height="30" type="application/x-shockwave-flash" data="' . THEMEURI . '/js/mediaelement/flashmediaelement.swf">'.PHP_EOL;
	$html .= '		<param name="movie" value="' . THEMEURI . '/js/mediaelement/flashmediaelement.swf" />'.PHP_EOL;
	$html .= '		<param name="flashvars" value="controls=true&file=' . $input .'" />'.PHP_EOL;
	$html .= '	</object>'.PHP_EOL;
	$html .= '</audio>'.PHP_EOL;

	$html .= $caption ? '<p class="wp-caption">' . esc_attr($caption) . '</p>' : '';
	$html .= '</div>';
	
	return $html;
}

/**
 * Enqueues scripts needed for inline audio
 *
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_add_audio_scripts(){
	add_action('wp_enqueue_scripts', 
		create_function('$e=NULL', 
			'wp_enqueue_script("media_element", "'.THEMEJS.'/mediaelement/mediaelement-and-player.min.js", array("jquery"), "2.9.1", false);'
		), 
	10);	
	add_action('wp_enqueue_scripts',
		create_function('$e=NULL',
			'wp_enqueue_style("media_element_style", "'.THEMEJS.'/mediaelement/mediaelementplayer.min.css", array("calpress"), "2.9.1", "screen, handheld");'
		),
	10);
}

/**
 * Displays the input form for adding Vimeo videos
 *
 * @since CalPress 0.9.7
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_inline_image_form($val, $name, $id){

}

/**
 * Check the image URL to see if returns a proper image mime type.
 * Supports png, jpg, gif and SVG images
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return bool True if more than one image exists, false if not
 */
function calpress_inline_image_sanitize($input, $post_id){
	$input = (string) esc_url($input);
	
	$headers = calpress_get_headers_curl($input, 5);
	$headers_string = implode(' ', $headers);
	
	if (preg_match('/image\/(png|gif|jpeg|pjpeg|svg\+xml)/i', $headers_string) )
		return $input;
		
	return false;	
}

/**
 * Returns HTML for inline images
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return bool True if more than one image exists, false if not
 */
function calpress_inline_image($input, $post_id=NULL, $title='', $caption=''){
	
	$html = '<div class="inline-image inline-item">'.PHP_EOL;
	$html .= $title ? '	<h3>'. esc_attr($title) .'</h3>'.PHP_EOL: '';
	$html .= '	<a href="' . $input .'" class="fancybox" title="See larger image">';
	$html .= '<img src="' . $input .'" alt="'. esc_attr(trim(strip_tags($caption))) .'" style="max-width:300px; height:auto;" />';
	$html .= '</a>'.PHP_EOL;
	$html .= $caption ? '	<p class="wp-caption">' . $caption . '</p>'.PHP_EOL : '';
	$html .= '</div>'.PHP_EOL;
	
	return $html;
}

/**
 * Enqueues fancybox scripts for inline images
 *
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_add_fancybox_scripts(){
	add_action('wp_enqueue_scripts', 
		create_function('$e=NULL', 
			'wp_enqueue_script("fancybox", "'.THEMEJS.'/fancybox/jquery.fancybox.pack.js", array("jquery"), "2.0.6", false);'
		), 
	10);
		
	add_action('wp_enqueue_scripts',
		create_function('$e=NULL',
			'wp_enqueue_style("fancybox", "'.THEMEJS.'/fancybox/jquery.fancybox.css", array("calpress"), "2.0.6", "screen, handheld");'
		),
	10);
}


/**
 * Displays the related links input field
 *
 * @since CalPress 0.9.7
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_related_links_input_form($val, $name, $id){
	echo '<input type="text" id="' . $id . '" name="' . $name . '" value="' . $val . '" size="60" />';
}

/**
 * Validates that the related links are number and are evenly spaced.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_related_links($input, $post_id){
	$cats = get_terms('link_category', array('orderby' => 'name', 'order' => 'ASC', 'hierarchical' => 0));
	$all_categories = array();
	
	foreach($cats as $cat){
		$all_categories[] = $cat->term_id;
	}
	
	$input = preg_split("/[\s]+/", $input);
	foreach($input as $val){
		if(!in_array($val, $all_categories))
			return false;
	}
	
	return implode(' ', $input);
}

/**
 * Returns related links
 *
 * @since CalPress 0.9.7
 * @param string $input The related link category number
 * @param int $post_id The ID of the post (unused)
 * @param string $title (optional) The title for this block of related links
 * @param string $caption (optional) A caption for this block of related links
 * @return string|bool The HTML for the embed code.
 */
function calpress_related_links($input, $post_id=NULL, $title='', $caption=''){
	if(!is_numeric($input))
		return false;
	
	$before = '		<li>';
	
	$html = '<div class="related_links inline-item">'.PHP_EOL;
	$html .= $title ? '	<h3>' . esc_attr($title) . '</h3>'.PHP_EOL : '';
	$html .= $caption ? '	<p class="link-caption">' . esc_attr($caption) . '</p>'.PHP_EOL : '';
	$html .= '	<ul>'.PHP_EOL;
	$html .= wp_list_bookmarks('before=' . $before . '&echo=0&title_li=&categorize=0&category=' . $input . '&orderby=rating&show_images=0&show_updated=0');
	$html .= '	</ul>'.PHP_EOL;
	$html .= '</div><!-- .related_links -->'.PHP_EOL;
	
	return $html;
}

/**
 * Displays the textfield form for adding manual links
 *
 * @since CalPress 0.9.7
 * @uses esc_textarea();
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_manual_links_input_form($val, $name, $id){
	echo '<textarea class="large-text code" style="font-family: "Courier New", Courier, monospace; font-size: 13px; color:#202020;" id="' . $id . '" name="' . $name . '" rows="3">'. esc_textarea($val) . '</textarea>';

}

/**
 * Validates manually added links
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_manual_links($input, $post_id){
	$lines = preg_split('/$\R?^/m', $input);
	
	foreach($lines as $line){
		
		//must start with bracket and have a closing bracket
		if(!preg_match('/^\[(.*?)\]\((http|https)\:\/\/(.*?)\)/', $line))
			return false;	
	}
	
	return strip_tags($input);
}

/**
 * Returns manual links
 *
 * @since CalPress 0.9.7
 * @param string $input The contents of the database for this option
 * @param int $post_id The ID of the post
 * @param string $title (optional) The title for this block of related links
 * @param string $caption (optional) A caption for this block of related links
 * @return string|bool The HTML for the embed code.
 */
function calpress_manual_links($input, $post_id=NULL, $title='', $caption=''){
	$lines = preg_split('/$\R?^/m', $input);
	
	if(!empty($lines)):
		$html = '<div class="related_links inline-item">';
		$html .= $title ? '<h3>' . esc_attr($title) . '</h3>' : '';
		$html .= $caption ? '<p class="link-caption">' . esc_attr($caption) . '</p>' : '';
		$html .= '<ul>';
	
		foreach($lines as $line):
	
		if(preg_match('/^\[(.*?)\]\((.*?)\)/', $line, $matches)):
			$html .= '<li><a href="' . $matches[2] . '" target="_blank">' . esc_attr($matches[1]) . '</a></li>';
		endif;
		
		endforeach;
	
		$html .= '</ul>';
		$html .= '</div>';
	
		return $html;
	endif;
	
	return false;
}

/**
 * Displays the textfield form for adding extra CSS
 *
 * @since CalPress 0.9.7
 * @uses esc_textarea();
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_extra_css_input_form($val, $name, $id){
	echo '<textarea class="large-text code" style="font-family: "Courier New", Courier, monospace; font-size: 13px; color:#202020;" id="' . $id . '" name="'.$name.'" rows="3">' . esc_textarea($val) . '</textarea>';
	
}

/**
 * Validates CSS code
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_extra_css($input, $post_id){
	if(preg_match('/\<style+/i', $input))
		return false;
	
	return $input;
}

/**
 * displays CSS from post meta in the head.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @return void
 */
function calpress_extra_css($input){
	$output = create_function('$var', 'echo "<style type=\"text/css\">\n' . str_replace("\"", "\\\"", $input) . '\n</style>\n";');
	add_action('wp_head', $output);
}

/**
 * Displays the textfield form for adding extra JavaScript
 *
 * @since CalPress 0.9.7
 * @uses esc_textarea();
 * @param string $val The previous value
 * @param string $name The name of the form field
 * @param string $id The HTML id for this form field
 * @return void
 */
function calpress_extra_js_input_form($val, $name, $id){
		echo '<textarea class="large-text code" style="font-family: "Courier New", Courier, monospace; font-size: 13px; color:#202020;" id="' . $id . '" name="'.$name.'" rows="3">' . esc_textarea($val) . '</textarea>';	
}

/**
 * Validates JavaScript code
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The ID of the post
 * @return string The sanitized input
 */
function calpress_sanitize_extra_js($input, $post_id){
	if(preg_match('/\<script+/i', $input))
		return false;
	
	return $input;
}

/**
 * displays JavaScript from post meta in the head.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @return void
 */
function calpress_extra_js($input){
	$output = create_function('$var', 'echo "<script type=\"text/javascript\" charset=\"utf-8\">\n' . str_replace("\"", "\\\"", $input) . '\n</script>\n";');
	add_action('wp_head', $output);
}

/**
 * Displays the input form for the sidebar option
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @return void
 */
function calpress_sidebar_input_form($val, $name, $id){ ?>
	<input type="radio" name="<?php echo $name; ?>" value="true" <?php checked( $val, 'true' ); ?>><label for="remove_sidebar_on"> Show Sidebar</label><br />
	<input type="radio" name="<?php echo $name; ?>" value="false" <?php checked( $val, 'false' ); ?>><label for="remove_sidebar_on"> Remove Sidebar</label><br />
<?php	
}

/**
 * Ostensibly validates the sidebar option
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param int $post_id The post ID
 * @return bool $input
 */
function calpress_sanitize_sidebar($input, $post_id){
	return $input;	
}

/**
 * Returns true if it show sidebar, or false if not.
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @return bool $input
 */
function calpress_sidebar($input){
	if($input)
		return (bool) $input;
	
	return false;
}

/**
 * Adds a classname to the body tag when no sidebar is shown
 *
 * @since CalPress 0.9.7
 * @uses calpress_filter_bodyclass filter (see hooks.php)
 * @param string $input The classname
 * @return void
 */
function calpress_sidebar_add_body_class($input){
	if($input == "false")
		add_filter('calpress_filter_bodyclass', create_function('$classes', 'return $classes . " no-sidebar";'));
}

/**
 * Renders form for eliminating validation checks
 *
 * @since CalPress 0.9.7
 * @param string $input The $_POST results input
 * @param string $name The field name
 * @param string $id The html tag id
 * @return void
 */
function calpress_override_checks($val, $name, $id){
	if($val == "true"){
		$val = ' checked="checked" ';
	} else {
		$val = '';
	}
	echo '<input type="checkbox" name="' . $name . '" id="'.$id.'" value="true" ' . $val . '><label for="' . $id .'"> Remove Checks</label><br />';
}

?>