<?php
/**
 * CalPress enqueues file
 *
 * This file contains all of the javascript and css style files 
 * that will be loaded into this site using the WordPresses enqueue
 * function. 
 * 
 * ChildThemes can remove any styles or scripts by creating
 * its own calpress_enqueue_scripts() function.
 *
 * More information on enqueue can be found on WordPress Codex:
 * http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 *
 * Scripts like Modernizr are loaded after stylesheets to prevent FOUC.
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
 * Registers CSS style file locations
 *
 *
 * @since CalPress 0.9.7
 */
function calpress_register_styles() {
	wp_register_style( 'calpress', THEMECSS . '/calpress.css', array(), '1.1.7', 'screen, handheld');
	wp_register_style( 'calpress_960', THEMECSS . '/960.css', array('calpress'), '1.0');
}
add_action('wp_enqueue_scripts', 'calpress_register_styles', 1);//doesn't load in admin

if ( ! function_exists( 'calpress_enqueue_styles' ) ):
/**
 * Loads CSS stylesheets into the head via wp_enqueue_styles
 *
 * For child themes, create your own calpress_enqueue_styles() to override.
 *
 * @since CalPress 0.9.7
 */
function calpress_enqueue_styles(){
	wp_enqueue_style( 'calpress' );
}
endif;
add_action('wp_enqueue_scripts', 'calpress_enqueue_styles', 2);

/**
 * Registers javascript file locations
 *
 *
 * @since CalPress 0.9.7
 */
function calpress_register_scripts() {
    //Modernizer ensures HTML5 support for older IE browsers
	wp_register_script( 'modernizr', THEMEJS .'/modernizr.js', array(), '2.5.3', false);
	wp_register_script( 'respond', THEMEJS .'/respond.js', array('modernizr'), true);
	wp_register_script( 'custom_plugins', THEMEJS . '/plugins.js', array('jquery'), '1.0', true);
	wp_register_script( 'custom_scripts', THEMEJS . '/custom.js', array('jquery', 'modernizr'), '1.0', true);
	wp_register_script( 'iosslider', THEMEJS . '/jquery.iosslider.min.js', array('jquery'), '1.0.27', false);
	wp_register_script( 'feature_comments', THEMEJS . '/feature-comments.js', array('jquery'), '1.1.1', true);
}
add_action('wp_enqueue_scripts', 'calpress_register_scripts', 3);

if ( ! function_exists( 'calpress_enqueue_scripts' ) ):
/**
 * Loads javascript files in the document head via wp_enqueue_script
 * For child themes, create your own calpress_enqueue_scripts() to override.
 *
 * @since CalPress 0.9.7
 */
function calpress_enqueue_scripts(){	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'respond' );
	wp_enqueue_script( 'custom_scripts' );
	wp_enqueue_script( 'feature_comments' );
}
endif;
add_action('wp_enqueue_scripts', 'calpress_enqueue_scripts', 4);

/**
 * Loads featured comments JS script in the admin
 *
 * @since CalPress 0.9.7
 */
function calpress_admin_featured_comments_script(){
	wp_enqueue_script( 'feature_comments', THEMEJS . '/feature-comments.js', array('jquery'), '1.1.1', true);
}
add_action('admin_enqueue_scripts', 'calpress_admin_featured_comments_script');


/**
 * Swap the login logo with the site logo.
 *
 * @since CalPress 0.9.7
 */
function calpress_login_logo() { 
	if(get_header_image()):
	?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo header_image(); ?>);
			-moz-background-size:100% auto; /* Firefox 3.6 */
			background-size:100% auto;
			background-repeat:no-repeat;
			-webkit-background-origin:content-box; /* Safari */
			background-origin:content-box;
			background-position: center;
        }
    </style>
<?php 
	endif;
}

add_action( 'login_enqueue_scripts', 'calpress_login_logo' );

?>