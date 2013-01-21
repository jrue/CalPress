<?php
/**
 * The template for displaying all pages. Don't show sidebar.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @uses calpress_add_multimedia_scripts()
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 1.0
 */
calpress_add_multimedia_scripts();
get_header(); ?>
	<div id="content" class="clearfix">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( 
				is_front_page() || 
				(function_exists('tribe_is_event') && tribe_is_event()) || 
				(function_exists('tribe_is_venue') && tribe_is_venue())) { ?>
					<h2 class="entry-title"><?php the_title(); ?></h2>
				<?php } else { ?>	
					<h1 class="entry-title visuallyhidden"><?php the_title(); ?></h1>
				<?php } ?>
				<?php if((function_exists('tribe_is_event') && !tribe_is_event()) || !function_exists('tribe_is_event')): ?>
					<div class="entry-lead-art">
						<?php calpress_lead_art(); ?>
					</div>
				<?php endif; ?>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'CalPress' ), 'after' => '' ) ); ?>
						<?php edit_post_link( __( 'Edit', 'CalPress' ), '', '' ); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-## -->
				<?php comments_template( '', true ); ?>
<?php endwhile; ?>
	</div><!-- #container -->
<?php if(
((function_exists('tribe_is_event') && tribe_is_event()) || 
(function_exists('tribe_is_venue') && tribe_is_venue())) &&
(function_exists('tribe_is_month') && !tribe_is_month())): ?>
<?php get_sidebar(); ?>
<?php endif; ?>
<?php get_footer(); ?>