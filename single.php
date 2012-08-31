<?php
/**
 * The Template for displaying all single posts.
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
calpress_add_multimedia_scripts();

get_header(); ?>
			<div id="content" class="clearfix">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-lead-art">
						<?php calpress_lead_art(); ?>
					</div>
					<div class="entry-meta">
						<div class="share-code clearfix">
							<?php calpress_hook_single_entry_meta(); ?>
						</div>
						<span class="vcard author"><?php calpress_co_authors(); ?></span><span class="time">Posted <?php calpress_posted_on(); ?></span>
						<?php edit_post_link( __( 'Edit this post', 'CalPress' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-meta -->
					<?php calpress_inline_art(); ?>
					<div class="entry-content clearfix">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
					<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'CalPress' ), 'after' => '' ) ); ?>
<?php if ( get_the_author_meta( 'description' ) && calpress_show_author_profile_on_posts()) : // If a user has filled out their description, show a bio on their entries  ?>
					<footer id="entry-author-info" class="clearfix">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ) ); ?>
						<h2><?php printf( esc_attr__( 'About %s', 'CalPress' ), get_the_author() ); ?></h2>
						<?php the_author_meta( 'description' ); ?>
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s &rarr;', 'CalPress' ), get_the_author() ); ?>
						</a>
					</footer><!-- #entry-author-info -->
					<div class="filed-under">
						<p><?php echo 'Filed under: '; the_category(', '); ?></p>
						<?php the_tags('<p>Tagged:', ', ', '</p>'); ?>
					</div>
<?php endif; ?>
				</article><!-- #post-## -->
				<?php comments_template( '/comments.php', true ); ?>
<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
<?php if(calpress_show_sidebar()): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>
<?php get_footer(); ?>
