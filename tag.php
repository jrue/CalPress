<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CalPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */

get_header(); ?>
	<section id="content" class="clearfix">
		<header>
				<h1><?php
					printf( __( 'Tag Archives: %s', 'CalPress' ), '' . single_tag_title( '', false ) . '' );
				?></h1>
		</header>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 get_template_part( 'loop', 'tag' );
?>
	</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>