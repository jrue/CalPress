<?php
/**
 * The default loop that displays posts.
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
 * @since CalPress 1.0
 */
	
if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo calpress_get_featured_image_from_post($post->ID, 'front-category'); ?></a>
		<header class="article-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<p class="entry-meta"><span class="vcard author"><?php calpress_co_authors(); ?></span> <span class="time">Posted <?php calpress_posted_on(); ?></span></p>
			<?php calpress_hook_front_entry_meta(); ?>
		</header>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), 35, calpress_new_excerpt_more()); ?>
		</div>
		<footer>
			<?php if(calpress_show_comment_count()) comments_popup_link( __( 'Leave a comment', 'calpress' ), __( '1 Comment', 'calpress' ), __( '% Comments', 'calpress' ),'','' ); ?>
			<?php edit_post_link( __( 'Edit this article', 'calpress' ), ' | ', '' ); ?>
			<?php calpress_hook_front_entry_footer(); ?>
		</footer>
	</article>
<?php endwhile;

/* Display navigation to next/previous pages when applicable */
if (  $wp_query->max_num_pages > 1 ) : ?>
	<nav id="nav-below" class="paged-navigation clearfix">
		
		<span id="paged-left"><?php next_posts_link( __( 'Older news', 'calpress' ) ); ?></span>
		<span id="paged-right"><?php previous_posts_link( __( 'Recent news', 'calpress' ) ); ?></span>
		
	</nav><!-- #nav-below -->
<?php endif; // don't show pagination, not enough posts. ?>