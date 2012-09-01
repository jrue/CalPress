<?php
/**
 * Sets up the CalPress theme functions and definitions.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'calpress_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
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

//define some constants to make it easy to change if WordPress ever deprecates these functions.
define('THEMEURI', get_template_directory_uri()); //web address to parent theme
define('THEMEPATH', get_template_directory()); //absolute server path to parent theme
define('THEMELIB', TEMPLATEPATH . '/library'); //absolute server path to parent theme library
define('THEMEJS', get_template_directory_uri() .'/js');
define('THEMECSS', get_template_directory_uri() .'/css');
define('THEMELIBURI', get_template_directory_uri() .'/library'); //web address to parent theme library
define('CHILDTHEMEURI', get_stylesheet_directory_uri()); //web address to child theme
define('CHILDTHEMEFULLPATH', get_stylesheet_directory()); //absolute server path to child theme or parent if no child
define('CALPRESSTHEMEOPTIONS', serialize(get_option('calpress_theme_options'))); //all theme options (unserialize to use as an array)
define('CALPRESSCATEGORYMETA', serialize(get_option('calpress_category_meta'))); //Category metadata (unserialize to use as an array)

//includes
require_once(THEMELIB . '/theme-options.php'); //admin menus and options
require_once(THEMELIB . '/enqueue.php'); //adds main CSS and JS files to head
require_once(THEMELIB . '/helpers.php'); //helper functions
require_once(THEMELIB . '/front-page-customizer.php'); //new wp3.4 feature to visually customize front page
require_once(THEMELIB . '/image-helpers.php'); //image resize support
require_once(THEMELIB . '/legacy-support.php'); //pre CalPress 1.0 support
require_once(THEMELIB . '/multimedia.php'); //multimedia framework
require_once(THEMELIB . '/category-meta.php'); //images associated with categories
require_once(THEMELIB . '/post-meta.php');  //adds mutimedia meta boxes in posts/pages
require_once(THEMELIB . '/shortcodes.php'); //Shortcode support
require_once(THEMELIB . '/widgets.php'); //pre-packaged widgets with CalPress
require_once(THEMELIB . '/category-blocks.php'); //The category modules that appear on front page.
require_once(THEMELIB . '/hooks.php'); //various custom hooks

//uses hook to setup theme so functions can be overridden in a child theme.
add_action( 'after_setup_theme', 'calpress_setup' );

if ( ! function_exists( 'calpress_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 *
 * To override calpress_setup() in a child theme, add your own calpress_setup to your child theme's
 * functions.php file.
 *
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses register_sidebar() To add widget template areas
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function calpress_setup() {

	//uses custom TinyMCE styles in the visual editor of the admin
	add_editor_style();
	
	//internationalization. Put new languages in the language folder
	load_theme_textdomain( 'calpress', TEMPLATEPATH . '/languages' );
	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
	
	//theme header defaults
	$header_defaults = array(
		'default-image'          => get_template_directory_uri() . '/images/headers/default-header.png',
		'random-default'         => false,
		'width'                  => 490,
		'height'                 => 130,
		'flex-height'            => true,
		'flex-width'             => true,
		'default-text-color'     => '000',
		'header-text'            => false,
		'uploads'                => true
	);
	
	register_default_headers(array(
		'default' => array(
			'url' => '%s/images/headers/default-header.png',
			'thumbnail_url' => '%s/images/headers/default-header-thumb.png',
			'description' => 'CalPress Default'
		)
	));
	
	//theme background defaults
	$background_defaults = array(
		'default-color'          => 'f4f4f4',
		'default-image'          => THEMEURI . '/images/backgrounds/lil_fiber.png',
	);
	
	//add WP 3.0 theme support features
	if ( function_exists( 'add_theme_support' ) ) {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		if ( function_exists( 'is_wp_version')){
			if(is_wp_version( '3.4' )){ 
				add_theme_support( 'custom-header', $header_defaults );
				add_theme_support( 'custom-background', $background_defaults);
			}
		}
	
		//when a user uploads an image, the following copies will be made.
		if ( function_exists( 'add_image_size' ) ) { 
			add_image_size( 'carousel-image', 620, 400, false ); 
			add_image_size( 'carousel-thumb', 113, 40, true );
			add_image_size( 'front-featured', 300, 9999, false);
			add_image_size( 'front-category', 180, 116, true);
		}
		if (function_exists( 'set_post_thumbnail_size') ){
			//The default size for post thumbnails.
			set_post_thumbnail_size('620', '9999', false);
		}
	
	}
	
	//register navigational menus to be set in the admin (this theme supports one menu by default)
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation Menu', 'calpress' ),
		'top_bar' => __( 'Top of the page menu', 'calpress')
	) );
	
	
	//register sidebars
	$calpress_top_sidebar = array(
		'name'          => __('Top Sidebar', 'CalPress'),
		'id'            => 'top-sidebar',
		'description'   => __('These appear on the FRONT page only.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="widgetheader"><h2 class="widgettitle">',
		'after_title'   => '</h2></div>' 
	);
	
	$calpress_lower_sidebar = array(
		'name'          => __('Lower Sidebar', 'CalPress'),
		'id'            => 'lower-sidebar',
		'description'   => __('These appear on EVERY page.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="widgetheader"><h2 class="widgettitle">',
		'after_title'   => '</h2></div>' 
	);
	
	$calpress_inside_sidebar = array(
		'name'          => __('Inside Sidebar', 'CalPress'),
		'id'            => 'inside-sidebar',
		'description'   => __('These appear everywhere EXCEPT the front page.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="widgetheader"><h2 class="widgettitle">',
		'after_title'   => '</h2></div>' 
	);
	
	$calpress_footer_column_1 = array(
		'name'          => __('Footer Left', 'CalPress'),
		'id'            => 'footer-column-1',
		'description'   => __('Left footer column widgets.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="footerwidgettitle">',
		'after_title'   => '</h2>' 
	);
	
	$calpress_footer_column_2 = array(
		'name'          => __('Footer Middle', 'CalPress'),
		'id'            => 'footer-column-2',
		'description'   => __('Middle footer column widgets.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="footerwidgettitle">',
		'after_title'   => '</h2>' 
	);
	
	$calpress_footer_column_3 =array(
		'name'          => __('Footer Right', 'CalPress'),
		'id'            => 'footer-column-3',
		'description'   => __('Right footer column widgets.', 'CalPress'),
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="footerwidgettitle">',
		'after_title'   => '</h2>' 
	);
	
	register_sidebar($calpress_top_sidebar);
	register_sidebar($calpress_lower_sidebar);
	register_sidebar($calpress_footer_column_1);
	register_sidebar($calpress_footer_column_2);
	register_sidebar($calpress_footer_column_3);

}
endif; //end calpress_setup

?>