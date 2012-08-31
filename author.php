<?php
/**
 * The template for displaying Author Archive pages.
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
?>
<?php if ( have_posts() ) the_post(); 

$currauth = get_userdata(intval($author));

//determine if this is a published author or just a registered user
if ($currauth->wp_user_level >= 2){
	$is_author = true;
    $class_meta = "author-profile";
} else {
	$is_author = false;
    $class_meta = "community-profile";
}

//add to body class
add_filter('calpress_filter_bodyclass', create_function('$classes', 'return $classes . \' ' . $class_meta . '\';'), 10);

get_header();
?>

	<div id="content" class="clearfix">
		<section>
			<header>
				<h1><?php echo $currauth->display_name; ?></h1>
				<?php if(!empty($currauth->title)): ?>
				<h2><?php echo $currauth->title; ?></h2>
				<?php endif; ?>
			</header>

<?php
// If a user has filled out their description, show a bio on their entries.
if ( get_the_author_meta( 'description' ) ) : ?>
	<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'calpress_author_bio_avatar_size', 160 ) ); ?>
	<p><?php trim(the_author_meta( 'description' )); ?></p>
<?php endif; ?>
<?php if($is_author):
echo '<div class="person vcard">';
echo '<ul>';
//TODO: Contact form

// email
if (!empty($currauth->user_email)) {
    printf ('<li class="author-profile aux email">'. __('Email: ', 'CalPress') . '<a href="mailto:%s">%s</a></li>', $currauth->user_email, $currauth->user_email);
}

// twitter
if (!empty($currauth->twitter)) {
    printf ('<li class="author-profile aux twitter">' . __('Twitter: ', 'CalPress') . '<a href="http://twitter.com/%s">@%s</a></li>', $currauth->twitter, $currauth->twitter);
}

// website
if (!empty($currauth->user_url)) {
    printf ('<li class="author-profile aux website">' . __('Website: ', 'CalPress') . '<a href="%s">%s</a></li>', $currauth->user_url, $currauth->user_url);
}
echo '</ul>';
echo '</div><!-- .person -->'.PHP_EOL;
endif;
?>
		</section>
<?php rewind_posts(); ?>
	<div class="author-posts">
		
		<?php get_template_part( 'loop', 'author' ); ?>
		
	</div><!-- .author-posts -->
</div><!-- #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>