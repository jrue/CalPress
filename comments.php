<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to boilerplate_comment which is
 * located in the functions.php file.
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
?>
<?php 

//don't allow people to load this file directly
if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
	die ( 'Please do not load this page directly. Thanks.' ); 

//if post is locked
if ( post_password_required() ) :
	_e( '<p>This post is password protected. Enter the password to view any comments.</p>', 'CalPress' );
	return;
endif;

//start here
if ( have_comments() ) : ?>
	<?php calpress_update_comment_count(); ?>
		<div id="comments-list" class="comments">
			<h3 id="comments-title"><?php
				printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'CalPress' ),
				number_format_i18n( get_comments_number() ));
			?></h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div id="comments-nav-above" class="comments-navigation">
				<?php paginate_comments_links(); ?>
			</div><!-- #comments-nav-above -->
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php wp_list_comments( array( 'type'=>'comment', 'callback' => 'calpress_custom_comments' ) ); ?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div id="comments-nav-below" class="comments-navigation">
					<?php paginate_comments_links(); ?>
			</div><!-- #comments-nav-below -->
<?php endif; // check for comment navigation ?>
		</div><!-- #comments-list -->
<?php endif;// have_comments() ?>

<?php if ( ! comments_open() ) : ?>
	<p><?php _e( 'Comments are closed.', 'CalPress' ); ?></p>
<?php endif; // comments_open() ?>

<?php comment_form(calpress_comment_form_fields()); ?>