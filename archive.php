<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
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

get_header(); ?>
	<section id="content" class="clearfix">
<?php if ( have_posts() ) the_post(); ?>
					<h1 class="page-title"><?php
						if ( is_day() ) :
							printf( __( 'Archive for %s', 'CalPress' ), get_the_date() );
						elseif ( is_month() ) :
							printf( __( 'Archive for %s', 'CalPress' ), get_the_date('F Y') );
						elseif ( is_year() ) :
							printf( __( 'Archive for %s', 'CalPress' ), get_the_date('Y') );
						else :
							_e( 'Blog Archives', 'CalPress' );
						endif;
					?></h1>
<?php
	rewind_posts();
	get_template_part( 'loop', 'archive' );
?>
	</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>