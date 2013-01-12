<?php
/**
 * The template for displaying Search Results.
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
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */

get_header(); ?>
	<section id="content" class="clearfix">
<?php calpress_hook_top_content_div(); ?>
    <header>
    	<h1 class="page-title"><?php _e( 'Search results for:', 'CalPress' ) ?> <span><?php the_search_query() ?></h1>
    </header>
<?php if (!calpress_google_search()): ?>  

<?php
	rewind_posts();
	get_template_part( 'loop', 'archive' );
  calpress_hook_loopcontent_below();
?>

<?php endif; //not using google custom search ?>

	</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>