<?php
/**
 * The template for displaying Category Archive pages.
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
$category = get_queried_object();
get_header(); ?>
	<section id="content" class="clearfix">
		<header>
				<?php
					$category_image = calpress_get_category_meta($category->term_id, 'image');
					$category_description = category_description();
					
					if ( $category_image ){
						echo sprintf( __( '%s', 'CalPress' ), '<h1 style="display:block; height:0; margin:0; text-indent:-9000em;">' . single_cat_title( '', false ) . '</h1>' );
						echo '		<img src="' . $category_image .'" alt="Image for ' . $category->name . ' category" />'.PHP_EOL;
					} else {
						echo sprintf( __( '%s', 'CalPress' ), '<h1>' . single_cat_title( '', false ) . '</h1>' );
					}
					
					if ( ! empty( $category_description ) )
						echo '		<p class="category-description">' . $category_description . '</p>'.PHP_EOL;
				?>
		</header>
				<?php
				/* Run the loop for the category page to output the posts.
				 * If you want to overload this in a child theme then include a file
				 * called loop-category.php and that will be used instead.
				 */
				get_template_part( 'loop', 'category' );
				?>
	</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>