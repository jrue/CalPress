<?php
/** 
 * Category blocks are modular sections on the front page that 
 * display the latest post from various categories. 
 *
 * Displaying posts from multiple categories like this increases
 * database queries substantially. To alleviate this, we limit the
 * number of category blocks (default to seven). You are strongly
 * encouraged to use a caching plugin like wp_super_cache when using
 * lots of category blocks.
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


/**
 * Displays lists of posts in each category down the page.
 *
 * @global array $do_not_duplicate_loop Post ids to not duplicate
 * @global obj $post The WordPress Post object
 * @param array $categories integer IDs of which categories to display
 * @param int $limit Limit to a certain number of categories, as it increases server load
 * @since CalPress 2.0
 */
function calpress_display_category_blocks($categories, $limit=7){
	global $post;
	global $do_not_duplicate_loop;
	
	if(empty($categories))
		return;
		
	$categories = (array) $categories;
	
	if(count($categories) > $limit)
		array_splice($categories, $limit);

	foreach($categories as $category): ?>
	
	<section id="category-block-<?php echo $category; ?>" class="category-block <?php if('yes' == calpress_get_category_meta($category, 'categorytype')) echo 'category-block-slideshow'; ?>">
		<header>
			<h2><?php echo get_cat_name($category); ?></h2>
		</header>
		
<?php	
		$cat_block_posts = new WP_Query( array( 'posts_per_page' => 9, 'cat' => $category, 'category__not_in' => calpress_get_omit_category_blocks() ));
		$acounter = 0;
		$amaxposts = $cat_block_posts->post_count -1;


		while( $cat_block_posts->have_posts() ) : $cat_block_posts->the_post();
		
		if (in_array($post->ID, $do_not_duplicate_loop)) { $amaxposts--; continue; } $do_not_duplicate_loop[] = $post->ID;	
		
		if($acounter == 0){ ?>
		<div class="category-block-content">
			<article class="clearfix">
				<header>
					<?php echo calpress_get_featured_image_from_post($post->ID, 'front-category', 'front-category-image'); ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				</header>
				<p class="entry-content">
					<?php echo wp_trim_words(get_the_excerpt(), 29, calpress_new_excerpt_more()); ?>
				</p>
				<p class="entry-meta"><?php calpress_co_authors(); ?> | <?php calpress_posted_on(get_the_time('U')); ?></p>
			</article>
			<ul>
			
<?php 	} elseif($acounter > 0 && $acounter < $amaxposts-1) { ?>
	
				<li><a href="<?php the_permalink(); ?>"><?php echo wp_trim_words(get_the_title(), 13, calpress_new_excerpt_more()); ?></a></li>
			
<?php 	} if($acounter == $amaxposts){ ?>
			</ul>
		</div>
	</section><!-- .category-block -->
<?php 	}

		$acounter++;
		endwhile;
		wp_reset_postdata();
	endforeach;
}