<?php
/**
 * The main fallback template file. If any other templates are missing i.e.
 * category.php or author.php, they will always fallback to this file. We
 * tried to make this theme complete enough so this file doesn't get displayed.
 *
 * http://codex.wordpress.org/Template_Hierarchy
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
	<div id="content" class="clearfix">
		<?php calpress_hook_top_content_div(); ?>		
		<?php get_template_part( 'loop', 'index'); ?>
		<?php calpress_hook_loopcontent_below(); ?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>