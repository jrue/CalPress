<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
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
global $do_not_duplicate_loop; //get posts from featured so we don't display them twice
global $number_of_featured_posts;

if(!$number_of_featured_posts)
	$number_of_featured_posts = 0;
	
if(!$do_not_duplicate_loop)
	$do_not_duplicate_loop = array();

$front_query = new WP_Query(get_query_arguments_for_front_featured(true, false, calpress_num_posts_for_front_loop($number_of_featured_posts)));

/* If there are no posts to display, such as an empty archive page */
if ( !$front_query->have_posts() ) : ?>
	<article id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'No Posts Found', 'calpress' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Sorry no results were found. Perhaps searching will help find a related post.', 'calpress' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->
	<script>
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>
<?php endif;

if(!is_paged()):

$counter = 0;
while ($front_query->have_posts()) : $front_query->the_post();
  if (in_array($post->ID, $do_not_duplicate_loop)) { continue; } $do_not_duplicate_loop[] = $post->ID; ?>
	<?php $featured_image = calpress_get_featured_image_from_post($post->ID, 'front-category'); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('newsy-front clearfix ' . calpress_calculate_row_classes($counter, 3, 'newsy')); ?>>
		<?php if($featured_image): ?>
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo $featured_image; ?></a>
		<?php endif; ?>
		<header class="article-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo wp_trim_words(get_the_title(), 10, '...'); ?></a></h1>
			<p class="entry-meta"><span class="vcard author"><?php calpress_co_authors(false); ?></span> <br /><span class="time">Posted <?php calpress_posted_on(get_the_time('U')); ?></span></p>
			<?php calpress_hook_front_entry_meta(); ?>
		</header>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), ($featured_image ? 15 : 55), calpress_new_excerpt_more()); ?>
		</div>
		<footer>
			<?php if(calpress_show_comment_count()) comments_popup_link( __( 'Leave a comment', 'calpress' ), __( '1 Comment', 'calpress' ), __( '% Comments', 'calpress' ),'','' ); ?>
			<?php edit_post_link( __( 'Edit this article', 'calpress' ), ' | ', '' ); ?>
			<?php calpress_hook_front_entry_footer(); ?>
		</footer>
	</article>
<?php $counter++; endwhile; wp_reset_postdata();

else: //is_paged()

while ($front_query->have_posts()) : $front_query->the_post(); 
  if (in_array($post->ID, $do_not_duplicate_loop)) { continue; } ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo calpress_get_featured_image_from_post($post->ID, 'front-category'); ?></a>
		<header class="article-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'CalPress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<p class="entry-meta"><span class="vcard author"><?php calpress_co_authors(); ?></span> <span class="time">Posted <?php calpress_posted_on(get_the_time('U')); ?></span></p>
		</header>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), 35, calpress_new_excerpt_more()); ?>
		</div>
		<footer>
			<?php echo 'Filed under: '; the_category(', '); echo ' | '; ?>
			<?php if(calpress_show_comment_count()) comments_popup_link( __( 'Leave a comment', 'CalPress' ), __( '1 Comment', 'CalPress' ), __( '% Comments', 'calpress' ),'','' ); ?>
			<?php edit_post_link( __( 'Edit this article', 'CalPress' ), ' | ', '' ); ?>
			<?php calpress_hook_front_entry_footer(); ?>
		</footer>
	</article>
<?php endwhile; wp_reset_postdata();

endif; //is_paged()
	
/* Display navigation to next/previous pages when applicable */
if (  $front_query->max_num_pages > 1 ) : ?>
	<nav id="nav-below" class="paged-navigation clearfix">
		
		<span id="paged-left"><?php next_posts_link( __( 'Older news', 'calpress' ) ); ?></span>
		<span id="paged-right"><?php previous_posts_link( __( 'Recent news', 'calpress' ) ); ?></span>
		
	</nav><!-- #nav-below -->
<?php endif; // don't show pagination, not enough posts. ?>