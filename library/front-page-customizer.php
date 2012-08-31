<?php
/**
 *
 * Uses WordPress 3.4 theme customizer to adjust front page layout.
 *
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @param $wp_customize Theme Customizer object
 * @return void
 * @package WordPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */
function calpress_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->remove_section('static_front_page');
	$wp_customize->remove_section('nav');

	$options  = calpress_get_theme_options();
	$defaults = calpress_get_default_theme_options();
	
	$wp_customize->add_section( 'calpress_layout', array(
		'title'    => __( 'Layout', 'CalPress' ),
		'priority' => 1
	) );

	$wp_customize->add_setting( 'calpress_theme_options[front_page_layout]', array(
		'default'    => $defaults['front_page_layout'],
		'type'       => 'option',
		'capability' => 'edit_theme_options'
	) );

	$post_formats = calpress_return_all_featured_post_formats();

	$wp_customize->add_control( 'calpress_front_page_layout', array(
		'label'    => __( 'Front Page Layout', 'CalPress' ),
		'section'  => 'calpress_layout',
		'settings' => 'calpress_theme_options[front_page_layout]',
		'type'     => 'select',
		'choices'  => $post_formats,
		'priority' => 5,
	) );
	
	if ( $wp_customize->is_preview() && ! is_admin() )
	    add_action( 'wp_footer', 'calpress_customize_preview', 21);
	
	
	
	/*
	// Link Color (added to Color Scheme section in Theme Customizer)
	$wp_customize->add_setting( 'twentyeleven_theme_options[link_color]', array(
		'default'           => twentyeleven_get_default_link_color( $options['color_scheme'] ),
		'type'              => 'option',
		'sanitize_callback' => 'sanitize_hex_color',
		'capability'        => 'edit_theme_options',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
		'label'    => __( 'Link Color', 'twentyeleven' ),
		'section'  => 'colors',
		'settings' => 'twentyeleven_theme_options[link_color]',
	) ) );

	// Default Layout
	$wp_customize->add_section( 'twentyeleven_layout', array(
		'title'    => __( 'Layout', 'twentyeleven' ),
		'priority' => 50,
	) );

	$wp_customize->add_setting( 'twentyeleven_theme_options[theme_layout]', array(
		'type'              => 'option',
		'default'           => $defaults['theme_layout'],
		'sanitize_callback' => 'sanitize_key',
	) );

	$layouts = twentyeleven_layouts();
	$choices = array();
	foreach ( $layouts as $layout ) {
		$choices[$layout['value']] = $layout['label'];
	}

	$wp_customize->add_control( 'twentyeleven_theme_options[theme_layout]', array(
		'section'    => 'twentyeleven_layout',
		'type'       => 'radio',
		'choices'    => $choices,
	) );
	*/
}

if(is_wp_version( '3.4' )){ 
	add_action( 'customize_register', 'calpress_customize_register' );
}

function calpress_customize_preview(){
	
}