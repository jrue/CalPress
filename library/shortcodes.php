<?php
/** 
 * CalPress Shortcodes
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
 * pullquote_shortcode()
 *
 * Creates a pullquote shortcode with float options.
 *
 * @param int Timestamp to compare to now.
 * @return string Sentence saying how long ago it was updated
 * @since CalPress 0.9
*/
function pullquote_shortcode( $atts, $content = null ) {
    if($atts['align']){
        if($atts['align'] == "right"){
            $float = "right";
        }else{
            $float = "left";
        }
        return '<blockquote class="pullquote '. $float .'"><p>' . trim($content) . '</p></blockquote>';
    } else {
        return '<blockquote class="pullquote"><p>' . trim($content) . '</p></blockquote>';
    }
}
add_shortcode('pullquote', 'pullquote_shortcode');

/**
 * gallery_shortcode()
 *
 * Override the default gallery shortcode
 *
 * @param int Timestamp to compare to now.
 * @return string Sentence saying how long ago it was updated
 * @since CalPress 0.9
*/
function calpres_gallery_shortcode( $atts, $content = null){
	
}
//add_shortcode('shortcode', 'calpres_gallery_shortcode');



?>