<?php
/**
 * The template for displaying attachments. We don't display sidebar on attachment pages.
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
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php if ( ! empty( $post->post_parent ) ) : ?>
					<h1 class="page-title"><a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', 'CalPress' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery"><?php printf( __( '%s', 'CalPress' ), get_the_title( $post->post_parent ) ); ?></a></h1>
				<?php endif; ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>
					<div class="entry-meta">
						<p><span class="vcard author"><?php calpress_co_authors(false); ?></span> <span class="time">Posted <?php calpress_posted_on(get_the_time('U')); ?></span></p>
						<?php
							if ( wp_attachment_is_image() ) {
								$metadata = wp_get_attachment_metadata();
								printf( __( 'Full size is %s pixels', 'CalPress'),
									sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
										wp_get_attachment_url(),
										esc_attr( __('Link to full-size image', 'CalPress') ),
										$metadata['width'],
										$metadata['height']
									)
								);
							}
						?>
						<?php edit_post_link( __( 'Edit', 'CalPress' ), '', '' ); ?>
					</div><!-- .entry-meta -->
					<div class="entry-content">
						<div class="entry-attachment">
<?php if ( wp_attachment_is_image() ) : ?>
							<p><?php
								$attachment_size = apply_filters( 'calpress_attachment_size', 900 );
								echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
							?></p>
							<nav id="nav-below" class="navigation">
								<div class="nav-previous"><?php previous_image_link( false ); ?></div>
								<div class="nav-next"><?php next_image_link( false ); ?></div>
							</nav><!-- #nav-below -->
<?php else : ?>
							<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
<?php endif; ?>
						</div><!-- .entry-attachment -->
						<div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>
<?php the_content( __( 'Continue reading &rarr;', 'CalPress' ) ); ?>
<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'CalPress' ), 'after' => '' ) ); ?>
<?php comments_template(); ?>
					</div><!-- .entry-content -->
				</article>
<?php endwhile; rewind_posts(); ?>
	</div>
<?php get_footer(); ?>