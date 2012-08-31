<?php
/**
 * The template file for the home page.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * http://codex.wordpress.org/Template_Hierarchy
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @uses get_calpress_featured_category()
 * @uses calpress_get_featured_post_format()
 * @uses calpress_display_category_blocks()
 * @uses calpress_hook_below_category_blocks()
 * @uses calpress_hook_loopcontent_below()
 * @package WordPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */

get_header(); ?>
	<div id="content" class="clearfix">
		<?php calpress_hook_top_content_div(); ?>
		
<?php 		//don't show featured posts on archive pages, search results, or if user didn't select a featured category.
			if (get_calpress_featured_category() == true)
			 	get_template_part( 'featured', trim(calpress_get_featured_post_format())); 
			
			//get the loop specifically meant for pulling front page posts
			get_template_part( 'loop', 'front'); 
			
			//get the category blocks, only show on front page
			if ( !is_archive() && !is_search() && !is_paged() && is_home()):
				calpress_display_category_blocks(calpress_get_chosen_category_blocks());
				calpress_hook_below_category_blocks();
			endif;
?>
		<?php calpress_hook_loopcontent_below(); ?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>