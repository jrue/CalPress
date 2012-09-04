<?php
/**
 * Featured Name: Carousel
 *
 * The template file that displays posts for an optional carousel.
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
global $do_not_duplicate_loop;
global $number_of_featured_posts;

$number_of_featured_posts = 5;
$do_not_duplicate_loop = array(); 
$navigational_carousel_items = array(); ?>

<?php
	//get one post from front and featured categories (as define in theme options)
	$featured_query = new WP_Query(get_query_arguments_for_front_featured(true, true, $number_of_featured_posts));
	
	foreach($featured_query->posts as $postids):
		$do_not_duplicate_loop[] = $postids->ID;
	endforeach;
	
	$counter = 0;

	if($featured_query->have_posts() && !is_paged()): ?> 
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function($){
			$('#carousel-container').iosSlider({
				snapToChildren: true,
				scrollbar: false,
				desktopClickDrag: false,
				responsiveSlideContainer:true,
				responsiveSlides:true,
				autoSlide: true,
				autoSlideTimer: 5000,
				navSlideSelector: $('.carousel-navigation'),
				onSlideChange: slideContentChange,
				onSliderLoaded: slideContentChange
			});	

			function slideContentChange(args) {
				$('.carousel-navigation').removeClass('selected');
				$('.carousel-navigation:eq(' + args.currentSlideNumber + ')').addClass('selected');
			}
		});
	</script>
	<div id="fluid-height">
		<div id="carousel-container" class="clearfix iosSlider">
			<div class="slider">
	
<?php while ($featured_query->have_posts() && $counter < 5) : $featured_query->the_post();

		if(has_post_thumbnail())://carousel posts must have images
			$navigational_carousel_items[] = $post;//save posts for nav
			$counter++;
?>
		
		<article id="post-<?php the_ID(); ?>" <?php post_class('carousel-article slide'); ?>>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
			<?php echo calpress_get_featured_image_from_post($post->ID, 'carousel-image', 'carousel-image'); ?>
			</a>
			<header class="article-header">
				<h2 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
					<?php the_title(); ?>
					</a>
				</h2>
				<p class="entry-meta"><?php calpress_co_authors(); ?> &ndash; <?php calpress_posted_on(get_the_time('U')); ?></p>
			</header>
		</article>

<?php 
endif; //end has_post_thumbnail
endwhile; //end featured post loop
?>
	</div>
</div><!-- #carousel-container -->
</div><!-- #fluid-height -->
<?php endif; //end have_post() ?>
<?php if(count($navigational_carousel_items) > 0 && !is_paged()): ?>
		<nav id="carousel-navigation" class="clearfix">
			<ul>
<?php foreach($navigational_carousel_items as $nav_item): ?>
				<li id="nav_post-<?php echo $nav_item->ID; ?>" class="carousel-navigation">
					<?php echo calpress_get_featured_image_from_post($nav_item->ID, 'carousel-thumb', 'carousel-thumbnail'); ?>
				
					<header class="nav_carousel_header_tag">
						<h2 class="entry-title"><?php echo wp_trim_words(get_the_title($nav_item->ID), 9, '...'); ?></h2>
					</header><!-- .nav_carousel_header_tag -->
				</li><!-- .nav_post-<?php echo $nav_item->ID; ?> -->
<?php endforeach; 
echo '			</ul>' . PHP_EOL;
echo '		</nav><!-- #carousel-navigation -->'.PHP_EOL; 
endif; //count nav items 
wp_reset_postdata(); 
