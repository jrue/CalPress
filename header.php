<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div role="main">
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @uses calpress_hook_after_opening_body_tag()
 * @uses calpress_hook_above_banner()
 * @uses calpress_hook_header()
 * @uses calpress_hook_below_header()
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie6 lte7 lte8 lte9"><![endif]-->
<!--[if IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie7 lte7 lte8 lte9"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="no-js ie ie8 lte8 lte9"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="no-js ie ie9 lte9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
	if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
	wp_head();
?>
</head>
<body <?php body_class(calpress_filter_bodyclass()); ?>>
	<?php calpress_hook_after_opening_body_tag(); ?>
	<?php wp_nav_menu( calpress_top_nav_menu_login_link(false) ); ?>
	<?php calpress_hook_above_banner(); ?>
	<header role="banner" id="header">
		<?php calpress_header_image(); ?>
		<?php calpress_hook_header(); ?>
	</header>
	<?php calpress_hook_below_header(); ?>
	<nav id="primary-navigation" role="navigation">
		<?php wp_nav_menu( calpress_primary_navigational_menu_arguments() ); ?>
	</nav>
	<div id="container" role="main" class="clearfix">