<?php
/**
 * Functions to help with posting, resizing or returning information about images.
 * We've built in legacy support in case you're switching from an older theme that 
 * didn't support featured image (formerly called "post thumbnails"). If no featured 
 * image is set on the post, then it will take the first image attached to the post, 
 * resize it and set it as the featured image of that post automatically. Not all 
 * previous posts will be converted at once, but only as images are requested. This
 * may result in slow performance initially after switching to this theme from another
 * as images are generated. If this is a concern, it is suggested you use a thumbnail
 * generator plugin like: http://wordpress.org/extend/plugins/regenerate-thumbnails/
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @since CalPress 0.9.7
 * @package WordPress
 * @subpackage CalPress2 2.0
 */

/**
 * Returns the description from the featured image (formerly post thumbnail)
 *
 * @since CalPress 0.9.7
 * @param int $postID The ID of the post.
 * @param bool $html Include HTML paragraphs tags around the caption?
 * @param string $extra_classes Optionally include extra CSS classes (space delimited) when HTML is true
 * @return bool|string Escaped description, or false if none.
 */
function calpress_get_description_from_image($postID, $html=false, $extra_classes=''){
	$postID = (int) $postID;
	$attachment_image_id = get_post_thumbnail_id($postID);
	//$attachment = get_post( $attachment_image_id );
	$attachment = get_post_field('post_excerpt', $attachment_image_id);
	if($attachment){
		if($html)
			return '<p class="wp-caption '. $extra_classes .'">' . esc_attr($attachment) . '</p>'."\n";
		return $attachment;
	}
	return false;
}

/**
 * Returns the image thumbnail html from a post. Sets one if there isn't one present.
 *
 * @since CalPress 0.9.7
 * @param int $postID The ID of the post.
 * @param string $size The WordPress image size, or one using add_image_size()
 * @param bool|string $extra_classes Optional space delimited string of extra classes to include on the image.
 * @return bool|string Attachment as an img html tag.
 */
function calpress_get_featured_image_from_post($postID, $size='medium', $extra_classes=''){
	global $post;
	global $_wp_additional_image_sizes;
	
	$post_custom = get_post_custom($postID);
	
	$postID = (int) $postID;
	if($extra_classes)
		$extra_classes .= " attachment-" . $size;//restore default classes
	
	if(has_post_thumbnail($postID))://there is a featured image set
		
		$thumb_id = get_post_thumbnail_id($postID);
		$thumb_meta = wp_get_attachment_metadata($thumb_id);
		
		if(calpress_check_image_size_exists($thumb_id, $size))
			return ($extra_classes ? get_the_post_thumbnail( $postID, $size, array('class' => $extra_classes)) : get_the_post_thumbnail( $postID, $size));
				
		//size not present and couldn't generate new one. Since there IS a featured image, just give the next best thing
		return ($extra_classes ? get_the_post_thumbnail( $postID, $size, array('class' => $extra_classes)) : get_the_post_thumbnail( $postID, $size)); 
				
	elseif(!isset($post_custom['lead_art'])): //doesn't have a featured image set, and is a old article
	
		if ( $images = get_children(array(
			'post_parent' => $postID, 
			'post_type' => 'attachment', 
			'order' => 'ASC', 
			'orderby' => 'menu_order ID', 
			'post_mime_type' => 'image'))){
			
				$image_key = array_keys($images);
				$image = $images[$image_key[0]];
				$img_desc = $image->post_excerpt; //just in case we can't resize
				$file_url = wp_get_attachment_url($image->ID);//just in case we can't resize
				$thumb_id = $image->ID;
				//$thumb_meta = wp_get_attachment_metadata($thumb_id);
				
				if(calpress_check_image_size_exists($thumb_id, $size, $postID))//included postID argument so it will set as featured image.
					return ($extra_classes ? get_the_post_thumbnail( $postID, $size, array('class' => $extra_classes)) : get_the_post_thumbnail( $postID, $size));

				//couldn't generate new image for some reason. Just give us the original, because we're nice. 
				return '<img src="' . esc_url_raw($file_url) .'" alt="'. strip_tags($img_desc) .'">' . "\n";
				
			} else{
				
				//this post has no images!
				return false;
			}
	endif;	
}

/**
 * Checks to see if the image size requested is available. If not, it will re-generate the
 * image at those sizes. Set additional image sizes in functions.php using add_image_size().
 *
 * @since CalPress 0.9.7
 * @uses calpress_generate_attachment_size()
 * @param int $postID The PostID this image is attached to.
 * @param int $image_id The image attachment ID
 * @param string $size The size slug specified in theme with add_image_size()
 * @param bool|int $postID Optional. If a post ID is given, will attach this as the featured (thumbnail) image to this post.
 * @return bool Will return false on failure, or true on success.
 */
function calpress_check_image_size_exists($image_id, $size, $postID=false){
	$image_meta = wp_get_attachment_metadata($image_id);
	
	if((array_key_exists('sizes', $image_meta) ? array_key_exists($size, $image_meta['sizes']) : false)){
		return ($postID ? set_post_thumbnail($postID, $image_id) : true);
			
	} else { 
		if($generated_image = calpress_generate_attachment_size($image_id, get_attached_file($image_id), $size, $postID))
			return true;
			
		return false; 
	}
	
}

/**
 * If an image doesn't exist at this size, this function will generate one and attach proper metadata.
 *
 * @since CalPress 0.9.7
 *
 * @param int $image_id Attachment image ID to process.
 * @param string $file_path Full server file path to image.
 * @param string $size The size slug specified in theme with add_image_size()
 * @param bool|int $postID Optional. If a post ID is given, will attach this as the featured (thumbnail) image to this post.
 * @return bool|array Will return false on failure, or image src array on success.
 */
function calpress_generate_attachment_size( $image_id, $file_path, $size, $postID=false) {
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	
	if ( FALSE !== $file_path && @file_exists($file_path) ) {
		set_time_limit( 30 );
		$success = wp_update_attachment_metadata($image_id, wp_generate_attachment_metadata($image_id, $file_path));
		
		if($success && $postID)
			return set_post_thumbnail($postID, $image_id);
		
		if($success)
			return $success;
								
		return false;
	} else {
		return false;
	}
}