<?php
/**
 * Featured Name: Newsy
 *
 * The template file that displays posts for an optional newsy layout.
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
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */
?>
<?php 
global $do_not_duplicate_loop;
global $number_of_featured_posts;

$number_of_featured_posts = 1;
$do_not_duplicate_loop = array(); //store featured post(s) so we don't see it twice

//get one post from front and featured categories (as define in theme options)
$featured_query = new WP_Query(get_query_arguments_for_front_featured(true, true, $number_of_featured_posts));

foreach($featured_query->posts as $postids):
	$do_not_duplicate_loop[] = $postids->ID;
endforeach;

if($featured_query->have_posts() && !is_paged()) while ($featured_query->have_posts()) : $featured_query->the_post();

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('front-featured-post clearfix'); ?>>
	<div class="post-image-with-caption">
		<?php echo calpress_get_featured_image_from_post($post->ID, 'front-featured'); 
		if(has_post_thumbnail()):
			$attachment_id = get_post_thumbnail_id($post->ID);
			$description = get_post($attachment_id)->post_excerpt;
		endif;
		if(isset($description) && $description != '') echo '<div class="wp-caption"><p>' . $description . '</p></div>'.PHP_EOL; ?>
	</div>
	<header class="article-header">
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
		<p class="entry-meta"><?php calpress_co_authors(); ?> | <?php calpress_posted_on(get_the_time('U')); ?></p>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), 55, calpress_new_excerpt_more()); ?>
		</div>
	</header>
</article>
<?php endwhile; wp_reset_postdata(); ?>