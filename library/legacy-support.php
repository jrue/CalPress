<?php
/**
 * CalPress legacy support. This file has functions needed to support
 * users upgrading from an earlier version of CalPress (before version 1.0)
 *
 * To add legacy CalPress support, make sure to turn support on in Theme Options.
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */

/**
 * Checks if CalPress legacy support is enabled in theme options
 *
 * @since CalPress 0.9.7
 * @return bool True if enabled, false if not. 
 */
function calpress_legacy_support(){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	$legacy = ($options['legacy_calpress'] == "true" ? true : false);
	
	return (bool) $legacy;
}

/**
 * Converts Vimeo IDs from earlier CalPress to full URLs used in 2.0
 *
 * @since CalPress 0.9.7
 * @param string $id The vimeo id metadata with from custom fields
 * @return string Returns a full sanitized Vimeo url.
 */
function calpress_legacy_convert_vimeo_to_urls($id){
	if(is_numeric($id))
		$id = esc_url('http://vimeo.com/' . $id);
		
	return (string) $id;
}

/**
 * Converts YouTube IDs from earlier CalPress to full URLs used in 2.0
 *
 * @since CalPress 0.9.7
 * @param string $id The youtube metadata with from custom fields
 * @return string Return youtube full URL.
 */
function calpress_legacy_convert_youtube_to_urls($id){
	if(!preg_match('/^http\:/i', $id) && $id != '')
		$id = esc_url('http://www.youtube.com/watch?v=' . $id);
	
	return (string) $id;	
}

/**
 * Converts soundslides slug to Flash embed code.
 *
 * @since CalPress 0.9.7
 * @param string $id The post metadata with all custom fields
 * @param string $post_date PHP timestamp to calculate the folder for soundslides
 * @param string $width (optional) Width for soundslides (defaults to lead art)
 * @param string $height (optional) Height for soundslides (defaults to lead art)
 * @return string Return soundslides embed code for embed section
 */
function calpress_legacy_soundslides_embed_code($id, $post_date, $width="620", $height="498"){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	
	if(isset($options['legacy_soundslides']) && $post_date != ''){
		$path = $options['legacy_soundslides'] . mysql2date('Y/m/', $post_date);
	} else {
		return;
	}
	
	$ss = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="' . $width .'" height="' . $height . '" id="soundslider" align="middle">';
	$ss .= '<param name="allowScriptAccess" value="always" />';
	$ss .= '<param name="movie" value="'. $path . $id .'/soundslider.swf?size=2&format=xml&embed_width='. $width .'&embed_height=' . $height .'&autoload=false" />';
	$ss .= '<param name="wmode" value="transparent" />';
	$ss .= '<param name="quality" value="high" />';
	$ss .= '<param name="allowFullScreen" value="true" />';
	$ss .= '<param name="menu" value="false" />';
	$ss .= '<embed src="'. $path . $id. '/soundslider.swf?size=2&format=xml&embed_width='. $width .'&embed_height=' . $height .'&autoload=false" quality="high" bgcolor="#FFFFFF" width="'. $width .'" height="' . $height .'" name="soundslider" align="middle" menu="false" allowScriptAccess="always" wmode="transparent" type="application/x-shockwave-flash" allowFullScreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
	$ss .= '</object>';
	
	return $ss;
}

/**
 * Converts self-hosted videos to URLs. 
 *
 * @since CalPress 0.9.7
 * @param string $id The video metadata with from custom fields
 * @return string Return video full URL.
 */
function calpress_legacy_video_url($id){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	
	if(!preg_match('/^http\:/i', $id) && isset($options['legacy_video'])){
		return $options['legacy_video'] . $id . '/' . $id . '-iPhone.m4v';
	}
	
	return false;
}

/**
 * Converts self-hosted videos to URLs. 
 *
 * @since CalPress 0.9.7
 * @param string $id The video metadata with from custom fields
 * @return string Return video full URL.
 */
function calpress_legacy_video_poster($id){
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	
	if(!preg_match('/^http\:/i', $id) && isset($options['legacy_video'])){
		return $options['legacy_video'] . $id . '/' . $id . '-poster.jpg';
	}
	
	return false;
}

/**
 * Sets the lead art in the new CalPress post_meta framework when user
 * opens up an older post for editing. Checks the existing post_meta, 
 * and tries to set the new lead_art and inline_art values from older values.
 *
 * @since CalPress 0.9.7
 * @param obj $post_custom The post metadata with all custom fields
 * @param array $options The default values from CalPress 2.0 post_meta
 * @param string $post_date (optional) Required for soundslides and audio files
 * @return array Returns the same options with default values replaced as needed
 */
function calpress_legacy_set_lead_art($post_custom, $options, $post_date=''){
	$counter = 0;
	$related = "false";
	
	foreach($post_custom as $key => $custom):
		foreach($custom as $cus):
			switch($key){
				case 'lead_vimeo':
					$options['lead_art'] = array('vimeo');
					$vimeo = calpress_explodeandtrim("|", $cus);
					$options['vimeo'] = array(calpress_legacy_convert_vimeo_to_urls($vimeo[0]));
					if(isset($vimeo[2])) $options['lead_art_caption_vimeo'] = array($vimeo[2]);
					break;
				case 'lead_youtube':
					$options['lead_art'] = array('youtube');
					$youtube = calpress_explodeandtrim("|", $cus);
					$options['youtube'] = array(calpress_legacy_convert_youtube_to_urls($youtube[0]));
					if(isset($youtube[2])) $options['lead_art_caption_youtube'] = array($youtube[2]);
					break;
				case 'lead_video':
					$options['lead_art'] = array('video');
					$lead_video = calpress_explodeandtrim("|", $cus);
					$options['video'] = array(calpress_legacy_video_url($lead_video[0]));
					if(isset($lead_video[2])) $options['lead_art_caption_video'] = array($lead_video[2]);
					break;
				case 'lead_soundslides':
					$options['lead_art'] = array('embed');
					$lead_soundslides = calpress_explodeandtrim("|", $cus);
					$options['embed'] = array(calpress_legacy_soundslides_embed_code($lead_soundslides[0], $post_date, "620", "498"));
					if(isset($lead_soundslides[2])) $options['lead_art_caption_embed'] = array($lead_soundslides[2]);
					break;
				case 'lead_embed':
					$options['lead_art'] = array('embed');
					$options['embed'] = array($cus);
					break;
				case 'inline_vimeo':
					$options['inline_art'][$counter] = 'vimeo';
					$vimeo = calpress_explodeandtrim("|", $cus);
					$options['inline_art_media'][$counter] = calpress_legacy_convert_vimeo_to_urls($vimeo[0]);
					if(isset($vimeo[1])) $options['inline_art_title'][$counter] = $vimeo[1];
					if(isset($vimeo[2])) $options['inline_art_caption'][$counter] = $vimeo[2];
					$counter++;
					break;
				case 'inline_youtube':
					$options['inline_art'][$counter] = 'youtube';
					$youtube = calpress_explodeandtrim("|", $cus);
					$options['inline_art_media'][$counter] = calpress_legacy_convert_youtube_to_urls($youtube[0]);
					if(isset($youtube[1])) $options['inline_art_title'][$counter] = $youtube[1];
					if(isset($youtube[2])) $options['inline_art_caption'][$counter] = $youtube[2];
					$counter ++;
					break;
				case 'inline_video':
					$options['inline_art'][$counter] = 'video';
					$video = calpress_explodeandtrim("|", $cus);
					$options['inline_art_media'][$counter] = calpress_legacy_video_url($video[0]);
					if(isset($video[1])) $options['inline_art_title'][$counter] = $video[1];
					if(isset($video[2])) $options['inline_art_caption'][$counter] = $video[2];
					$counter++;
					break;
				case 'inline_soundslides':
					$options['inline_art'][$counter] = 'embed';
					$lead_soundslides = calpress_explodeandtrim("|", $cus);
					$options['inline_art_media'][$counter] = calpress_legacy_soundslides_embed_code($lead_soundslides[0], $post_date, "300", "241");
					if(isset($lead_soundslides[1])) $options['inline_art_title'][$counter] = $lead_soundslides[1];
					if(isset($lead_soundslides[2])) $options['inline_art_caption'][$counter] = $lead_soundslides[2];
					$counter++;
					break;
				case 'inline_embed':
					$options['inline_art'][$counter] = 'embed';
					$options['inline_art_media'][$counter] = $cus;
					$counter++;
					break;
				case 'related_links':
					$options['inline_art'][$counter] = 'related_links';
					$related_links = calpress_explodeandtrim("|", $cus);
					$options['inline_art_media'][$counter] = $related_links[0];
					if(isset($related_links[1])) $options['inline_art_title'][$counter] = $related_links[1];
					if(isset($related_links[2])) $options['inline_art_caption'][$counter] = $related_links[2];
					$counter++;
					break;
			}
			endforeach;
	endforeach;
	return $options;
}

/**
 * Displays lead art from older posts
 *
 * @since CalPress 0.9.7
 * @uses calpress_vimeo() From post_meta framework
 * @uses calpress_youtube() From post_meta framework
 * @uses calpress_video() From post_meta framework
 * @uses calpress_legacy_soundslides_embed_code() 
 * @global obj $post
 * @return string The html to be used in lead art
 */
function calpress_show_legacy_lead_art(){
	global $post;
	
	$postid = $post->ID; 
	$post_custom = get_post_custom($postid);
	$post_date = $post->post_date;
	
	$html = '';
	
	foreach($post_custom as $key => $custom):
		foreach($custom as $cus):
			switch($key){
				case 'lead_vimeo':
					$vimeo = calpress_explodeandtrim("|", $cus);
					$html = calpress_vimeo('http://vimeo.com/' . $vimeo[0]);
					if(isset($vimeo[2])) $html .= '<div class="wp-caption">' . $vimeo[2] . '</div>';
					break;
				case 'lead_youtube':
					$youtube = calpress_explodeandtrim("|", $cus);
					$html = calpress_youtube('http://www.youtube.com/watch?v=' . $youtube[0]);
					if(isset($youtube[2])) $html .= '<div class="wp-caption">' . $youtube[2] . '</div>';
					break;
				case 'lead_video':
					$lead_video = calpress_explodeandtrim("|", $cus);
					$html = '<script type="text/javascript" src="' . THEMEJS . '/mediaelement/mediaelement-and-player.min.js" charset="utf-8"></script>'.PHP_EOL;
					$html .= '<link rel="stylesheet" href="' . THEMEJS .'/mediaelement/mediaelementplayer.css" />'.PHP_EOL;
					$poster = ' poster="'. calpress_legacy_video_poster($lead_video[0]) .'"';
					$html .= calpress_video(calpress_legacy_video_url($lead_video[0]), '', '', '', false, $poster);
					if(isset($lead_video[2])) $html .= '<div class="wp-caption">' . $lead_video[2] . '</div>';
					break;
				case 'lead_soundslides':
					$lead_soundslides = calpress_explodeandtrim("|", $cus);
					$html = calpress_legacy_soundslides_embed_code($lead_soundslides[0], $post_date, "620", "498");
					if(isset($lead_soundslides[2])) $html .= '<div class="wp-caption">' . $lead_soundslides[2] . '</div>';
					break;
				case 'lead_embed':
					$html = '<script type="text/javascript" src="' . THEMEJS . '/swfobject.js" charset="utf-8"></script>'.PHP_EOL;
					$html .= $cus;
					break;
			}
			endforeach;
	endforeach;
	
	if($html)
		return $html;
		
	$html = calpress_featured_image($post->ID, false);
		
	return $html;
}

/**
 * Displays inline art from older posts
 *
 * @since CalPress 0.9.7
 * @global obj $post
 * @return string the html for inline art
 */
function calpress_show_legacy_inline_art(){
	global $post;
	$options = unserialize(CALPRESSTHEMEOPTIONS);
	
	$postid = $post->ID; 
	$post_custom = get_post_custom($postid);
	$post_date = $post->post_date;
	$scripts = false;//ensures scripts are only loaded once
	
	$html = '';
	
	foreach($post_custom as $key => $custom):
		foreach($custom as $cus):
			switch($key){
				case 'inline_vimeo':
					$vimeo = calpress_explodeandtrim("|", $cus);
					$vimeo[1] = (isset($vimeo[1]) ? $vimeo[1] : '');
					$vimeo[2] = (isset($vimeo[2]) ? $vimeo[2] : '');
					$html .= calpress_vimeo('http://vimeo.com/' . $vimeo[0], $postid, $vimeo[1], $vimeo[2], true);
					break;
				case 'inline_youtube':
					$youtube = calpress_explodeandtrim("|", $cus);
					$youtube[1] = (isset($youtube[1]) ? $youtube[1] : '');
					$youtube[2] = (isset($youtube[2]) ? $youtube[2] : '');
					$html .= calpress_youtube('http://www.youtube.com/watch?v=' . $youtube[0], $postid, $youtube[1], $youtube[2], true);
					break;
				case 'inline_video':
					$lead_video = calpress_explodeandtrim("|", $cus);
					$lead_video[1] = (isset($lead_video[1]) ? $lead_video[1] : '');
					$lead_video[2] = (isset($lead_video[2]) ? $lead_video[2] : '');
					$html .= calpress_video(calpress_legacy_video_url($lead_video[0]), $postid, $lead_video[1], $lead_video[2], true);
					if(!$scripts){
						$html .= '<script type="text/javascript" src="' . THEMEJS . '/mediaelement/mediaelement-and-player.min.js" charset="utf-8"></script>'.PHP_EOL;
						$html .= '<link rel="stylesheet" href="' . THEMEJS .'/mediaelement/mediaelementplayer.css" />'.PHP_EOL;
						$scripts = true;
					}
					break;
				case 'inline_soundslides':
					$lead_soundslides = calpress_explodeandtrim("|", $cus);
					$lead_soundslides[1] = (isset($lead_soundslides[1]) ? $lead_soundslides[1] : '');
					$lead_soundslides[2] = (isset($lead_soundslides[2]) ? $lead_soundslides[2] : '');
					$html .= calpress_legacy_soundslides_embed_code($lead_soundslides[0], $post_date, "300", "241");
					if(isset($lead_soundslides[2])) $html .= '<div class="wp-caption">' . $lead_soundslides[2] . '</div>';
					break;
				case 'inline_audio':
					if(!$scripts){
						$html .= '<script type="text/javascript" src="' . THEMEJS . '/mediaelement/mediaelement-and-player.min.js" charset="utf-8"></script>'.PHP_EOL;
						$html .= '<link rel="stylesheet" href="' . THEMEJS .'/mediaelement/mediaelementplayer.css" />'.PHP_EOL;
						$scripts = true;
					}
					$audio = calpress_explodeandtrim("|", $cus);
					$audio[1] = (isset($audio[1]) ? $audio[1] : '');
					$audio[2] = (isset($audio[2]) ? $audio[2] : '');
					if($options['legacy_soundslides'])
						$html .= calpress_inline_audio( $options['legacy_soundslides'] . mysql2date('Y/m/', $post_date) . $audio[0], $postid, $audio[1], $audio[2]); 
					if(isset($lead_soundslides[2])) $html .= '<div class="wp-caption">' . $lead_soundslides[2] . '</div>';
					break;
				case 'related_links':
					$links = calpress_explodeandtrim("|", $cus);
					$links[1] = (isset($links[1]) ? $links[1] : 'Related');
					$links[2] = (isset($links[2]) ? $links[2] : '');
					$html .= calpress_related_links($links[0], $postid, $links[1], $links[2]);
					break;
				case 'inline_embed':
					$html .= calpress_embed($cus, $postid, '', '', true);
					break;
				case 'inline_story':
					$html .= calpress_embed($cus, $postid, '', '', true);
					break;
				default:
					break;
			}
			endforeach;
	endforeach;
	
	if($html)
		return $html;
		
	return false;
}

/**
 * Mimics WordPress's get_post_thumbnail (featured image) in earlier versions of CalPress. 
 * Will find the first attached image of a post, then will make it the post's featured image.
 * Since we no longer use timthumb, we will try to detect the size. If the upload size doesn't exist, 
 * will try create the necessary sizes and save the image. May fail if the image is too small 
 * and the requested size is too large, in which case we stretch the image with HTML.
 *
 * @since CalPress 0.9.7
 * @param int $thePostID Required ID of the post to retrieve attached images
 * @param bool $imgtag (optional) If true, returns a full img tag string, otherwise just img src.
 * @param string $sizeString (optional) Size slug as set by add_image_size.
 * @return bool|string The img url of the first attached image, false on failure.
 */
function calpress_legacy_image($thePostID, $imgtag=false, $sizeString='large'){
	if ( $images = get_children(
			array(
				'post_parent' => $thePostID, 
				'post_type' => 'attachment', 
				'order' => 'ASC', 
				'orderby' => 'menu_order ID', 
				'post_mime_type' => 'image'
				)
			)
		){
		$image_key = array_keys($images);
		$image = $images[$image_key[0]];
		$img_desc = $image->post_excerpt;
		$attachment_meta = wp_get_attachment_metadata($image->ID);
		$file_url = wp_get_attachment_url($image->ID);//just in case we can't resize
		
		if(array_key_exists('sizes', $attachment_meta))://OK, it's been resized
		
			if(array_key_exists($sizeString, $attachment_meta['sizes'])){ //does it have our requested size?
	
				set_post_thumbnail($thePostID, $image->ID); //set this image as post thumbnail so we don't have to do it again.
				$attachmenturl = wp_get_attachment_image_src($image->ID, $sizeString);
				return $imgtag ? get_the_post_thumbnail( $thePostID, $sizeString).PHP_EOL : $attachmenturl[0];
				
			} else { //generate a copy
				
				if($generated_image = calpress_generate_attachment_size($image->ID, get_attached_file($image->ID), $sizeString)){
					
					$attachmenturl = wp_get_attachment_image_src($image->ID, $sizeString);
					
					if(set_post_thumbnail($thePostID, $image->ID))
						return $imgtag ? get_the_post_thumbnail( $thePostID, $sizeString).PHP_EOL : $attachmenturl[0];
					
					//couldn't attach to post, just return sized file
					return $imgtag ? '<img src="' . $attachmenturl[0] .'" alt="'. $img_desc .'">' . PHP_EOL: $attachmenturl[0];
					
				} else {
					//couldn't even resize this image, just give original file
					return $imgtag ? '<img src="' . $file_url .'" alt="'. $img_desc .'">'.PHP_EOL : $file_url; 
				}
			}
			
		else: //that's weird, no sizes were in attachment meta data. Let's try to create some.
		
			if($generated_image = calpress_generate_attachment_size($image->ID, get_attached_file($image->ID), $sizeString)){
			
				$attachmenturl = wp_get_attachment_image_src($image->ID, $sizeString);
			
				if(set_post_thumbnail($thePostID, $image->ID))
					return ($imgtag ? get_the_post_thumbnail( $thePostID, $sizeString).PHP_EOL : $attachmenturl[0]);
			
				//couldn't attach to post, just return sized file
				return ($imgtag ? '<img src="' . $attachmenturl[0] .'" alt="'. $img_desc .'">' . PHP_EOL: $attachmenturl[0]);
			
			} else {
				//couldn't even resize this image, just give original file
				return ($imgtag ? '<img src="' . $file_url .'" alt="'. $img_desc .'">'.PHP_EOL : $file_url); 
			}
			
		endif;
		
	} else {
		
		//OK, tried everything. Doesn't seem to be any images on this post.
		return false;
	}
}

/**
 * Explode and trim whitespace
 *
 * Returns an array of strings, each of which is a substring of $e split by $s with whitespace removed.
 *
 * @since CalPress 0.7b
 * @param string $s = search string
 * @param string $e = string to explode
 * @return array
 */
//
function calpress_explodeandtrim($e, $s){
    $totrim = explode($e,$s);
    $a = array();
    foreach($totrim as $t) {
        $a[]=trim($t);
    }
    return $a;
}
