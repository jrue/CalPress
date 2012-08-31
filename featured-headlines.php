<?php
/**
 * Featured Template Name: Headlines
 *
 * The template file that displays posts for an optional headlines layout.
 * You can add additional featured templates by dropping files with
 * the name featured-{template slug}.php in your theme directory.
 * Make sure to copy this comment block in the new file, and include
 * the first line to specify the name of your template.
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @global array $do_not_duplicate_loop Post ids to not duplicate
 * @global int $number_of_featured_posts How many posts were featured
 * @package WordPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */
?>
<?php 
global $do_not_duplicate_loop;
global $number_of_featured_posts;

$number_of_featured_posts = 6;

$do_not_duplicate_loop = array(); //store featured post(s) so we don't see it twice
$counter = 0;

//get one post from front and featured categories (as define in theme options)
$featured_query = new WP_Query(get_query_arguments_for_front_featured(true, true, $number_of_featured_posts));

foreach($featured_query->posts as $postids):
	$do_not_duplicate_loop[] = $postids->ID;
endforeach;

if(have_posts() && !is_paged()): ?> 
<div id="featured-posts" class="clearfix">
<?php while ($featured_query->have_posts()) : $featured_query->the_post();
	//$do_not_duplicate_loop[] = $post->ID;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('front-featured-post clearfix' . ($counter < 1 ? ' front-featured-first' : '')); ?>>
	<header class="article-header">
		<?php if($counter < 1): ?>
			<div class="post-image-with-caption">
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo calpress_get_featured_image_from_post($post->ID, 'front-featured'); ?></a>
				<?php echo calpress_get_description_from_image($post->ID, true); ?>
			</div>
		<?php endif; ?>
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
		<p class="entry-meta"><?php calpress_co_authors(); ?> | <?php calpress_posted_on(get_the_time('U')); ?></p>
		<?php if($counter < 1): ?>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), 40, calpress_new_excerpt_more()); ?>
		</div>
		<?php endif; ?>
	</header>
</article>
<?php $counter++; ?>
<?php endwhile; wp_reset_postdata(); ?>
</div><!-- #featured-posts -->
<?php endif; //have posts ?>