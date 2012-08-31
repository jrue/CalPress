<?php
/** 
 * CalPress Hooks. Create hooks throughout the template to add content
 * using a childtheme.
 *
 * Inspired by the work of Benedict Eastaugh
 * and the Tarski Theme
 * http://extralogical.net/2007/06/wphooks/
 * http://tarski.googlecode.com/svn/trunk/
 * 
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
 * calpress_hook_colophon()
 *
 * Template function appearing in the footer.php as the last
 * visible content at the bottom of the page.
 *
 * @example add_action('calpress_hook_colophon', 'my_function');
 * @since CalPress 0.9.7
 * @hook action calpress_hook_colophon
 */
function calpress_hook_colophon(){
	do_action('calpress_hook_colophon');
}

/** 
 * calpress_hook_after_body()
 *
 * Template function appearing after the closing html tag of the 
 * document. Only use this to display HTML commented content.
 *
 * @example add_action('calpress_hook_after_body', 'my_function');
 * @since CalPress 0.9.7
 * @hook action calpress_hook_after_body
 */
function calpress_hook_after_body(){
	do_action('calpress_hook_after_body');
}

/** 
 * calpress_hook_after_opening_body_tag()
 *
 * Template function appearing right after the opening body tag of the 
 * document. 
 *
 * @example add_action('calpress_hook_after_opening_body_tag', 'my_function');
 * @since CalPress 0.9.7
 * @hook action calpress_hook_after_opening_body_tag
 */
function calpress_hook_after_opening_body_tag(){
	do_action('calpress_hook_after_opening_body_tag');
}


/**
 * calpress_hook_above_banner()
 *
 * Template function appearing in header.php, allows actions
 * to be executed before the <header role="banner"> block.
 * @example add_action('calpress_hook_above_banner', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_above_banner
 */
function calpress_hook_above_banner() {
	do_action('calpress_hook_above_banner');
}

/**
 * calpress_hook_header()
 *
 * Template function appearing in header.php, allows actions
 * to be executed inside the <header role="banner"> 
 *
 * @example add_action('calpress_hook_header', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_header
 */
function calpress_hook_header() {
	do_action('calpress_hook_header');
}

/**
 * calpress_hook_below_header()
 *
 * Template function appearing in header.php, allows actions
 * to be executed just after <header role="banner"> 
 *
 * @example add_action('calpress_hook_below_header', 'my_function');
 * @since CalPress 0.9.7
 * @hook action calpress_hook_below_header
 */
function calpress_hook_below_header() {
	do_action('calpress_hook_below_header');
}

/**
 * calpress_hook_top_content_div()
 *
 * Template function appearing in home.php and index.php allows actions
 * to be executed first inside the <div id="content"> block.
 * @example add_action('calpress_hook_top_content_div', 'my_function');
 * @since CalPress 0.9.7
 * @hook action calpress_hook_top_content_div
 */
function calpress_hook_top_content_div() {
	do_action('calpress_hook_top_content_div');
}

/**
 * calpress_hook_above_footer()
 *
 * Template function appearing in footer.php, allows actions
 * to be executed at the beginning of the <footer> block. Make sure to include div tags
 * @example add_action('calpress_hook_above_footer', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_above_footer
 */
function calpress_hook_above_footer() {
	do_action('calpress_hook_above_footer');
}

/**
 * calpress_filter_bodyclass()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in loop that produces <body class="XXX XXX XXX"
 * @example add_filter('calpress_filter_bodyclass', 'my_function');
 * @since 0.7
 * @hook action calpress_filter_bodyclass
 */
function calpress_filter_bodyclass() {
	$classes = '';

	return apply_filters('calpress_filter_bodyclass', '', sanitize_html_class($classes));
}


/**
 * calpress_hook_loopcontent_below()
 *
 * Template function appearing in loop-content.php, allows actions
 * to be executed below the default loop content.
 * @example add_action('calpress_hook_loopcontent_below', 'my_function');
 * @example see home.php
 * @since 0.7
 * @hook action calpress_hook_loopcontent_below
 */
function calpress_hook_loopcontent_below() {
	do_action('calpress_hook_loopcontent_below');
}

/**
 * calpress_hook_below_category_blocks()
 *
 * Template function appearing in home.php, allows actions
 * to be executed below category blocks on the front page
 * @example add_action('calpress_hook_below_category_blocks', 'my_function');
 * @example see home.php
 * @since 2.0
 * @hook action calpress_hook_below_category_blocks
 */
function calpress_hook_below_category_blocks(){
	do_action('calpress_hook_below_category_blocks');
}

/**
 * calpress_hook_postcontent_above()
 *
 * Template function appearing in single.php, allows actions
 * to be executed above the content.
 * @example add_action('calpress_hook_postcontent_above', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_postcontent_above
 */
function calpress_hook_postcontent_above() {
	do_action('calpress_hook_postcontent_above');
}

/**
 * calpress_hook_postcontent_below()
 *
 * Template function appearing in single.php, allows actions
 * to be executed below the content.
 * @example add_action('calpress_hook_postcontent_below', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_postcontent_below
 */
function calpress_hook_postcontent_below() {
	do_action('calpress_hook_postcontent_below');
}

/**
 * calpress_hook_front_entry_meta()
 *
 * Template function appearing in loop.php, allows actions
 * to be executed the entry meta.
 *
 * @example add_action('calpress_hook_front_entry_meta', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_front_entry_meta
 */
function calpress_hook_front_entry_meta() {
	do_action('calpress_hook_front_entry_meta');
}

/**
 * calpress_hook_front_entry_footer()
 *
 * Template function appearing in loop.php, allows actions
 * to be executed the entry meta.
 *
 * @example add_action('calpress_hook_front_entry_footer', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_front_entry_footer
 */
function calpress_hook_front_entry_footer() {
	do_action('calpress_hook_front_entry_footer');
}

/**
 * calpress_hook_single_entry_meta()
 *
 * Template function appearing in single.php, allows actions
 * to be executed the entry meta.
 *
 * @example add_action('calpress_hook_single_entry_meta', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_single_entry_meta
 */
function calpress_hook_single_entry_meta() {
	do_action('calpress_hook_single_entry_meta');
}

/**
 * calpress_hook_post_below()
 *
 * Template function appearing in single.php, allows actions
 * to be executed below the div.post.
 * @example add_action('calpress_hook_post_below', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_post_below
 */
function calpress_hook_post_below() {
	do_action('calpress_hook_post_below');
}

/**
 * calpress_hook_authorpage_precontributedcontent()
 *
 * Template function appearing in author.php, allows actions
 * to be executed just inside the contributed content div, where stories and comments go
 * @example add_action('calpress_hook_authorpage_precontributedcontent', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_authorpage_precontributedcontent
 */
function calpress_hook_authorpage_precontributedcontent() {
	do_action('calpress_hook_authorpage_precontributedcontent');
}
?>