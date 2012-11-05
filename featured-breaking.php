<?php
/**
 * Featured Name: Breaking News
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

$liveblog = false;
//Check if LiveBlog Plugin is present
if(class_exists("WPCOM_Liveblog")):
	$liveblog = true;

endif;


if($featured_query->have_posts() && !is_paged()) while ($featured_query->have_posts()) : $featured_query->the_post();

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('front-featured-post clearfix'); ?>>
	<header class="article-header">
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
	</header>
	<?php if(has_post_thumbnail()): ?>
	<div class="post-image-with-caption">
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'calpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo calpress_get_featured_image_from_post($post->ID, 'medium'); ?></a>
		<?php
			$attachment_id = get_post_thumbnail_id($post->ID);
			$description = wp_trim_words(get_post($attachment_id)->post_excerpt, 55, calpress_new_excerpt_more());
		if(isset($description) && $description != '') echo '<div class="wp-caption"><p>' . $description . '</p></div>'.PHP_EOL; ?>
	</div>
	<?php endif; ?>
	<?php if(!$liveblog): ?>
		<p class="entry-meta"><?php calpress_co_authors(); ?> | <?php calpress_posted_on(get_the_time('U')); ?></p>
		<div class="entry-content">
			<?php echo wp_trim_words(get_the_excerpt(), 90, calpress_new_excerpt_more()); ?>
		</div>
	<?php else: //if liveblog ?>
		<?php if(has_post_thumbnail()): $lb_length = 75; ?>
		<div class="entry-content liveblog-front-image">
			<div id="entry-description">
				<?php echo wp_trim_words(get_the_excerpt(), 30, calpress_new_excerpt_more()); ?>
			</div>
		<?php else: $lb_length = 120; ?>
		<div class="entry-content liveblog-front-no-image">
			<div id="entry-description">
				<?php echo wp_trim_words(get_the_excerpt(), 100, calpress_new_excerpt_more()); ?>
			</div>
		<?php endif; ?>
			
			<div id="entry-liveblog">
				<img src="<?php echo THEMEURI; ?>/images/preloader.gif" width="100%" height="125" alt="preloader image" />
			</div>
			<?php if(class_exists('WPCOM_Liveblog') && (bool) get_post_meta($post->ID, 'liveblog', true)): ?>
			<script type="text/javascript" >
			jQuery(document).ready(function($) {
				
				var counter = 0;
				var timestamp = 0;
				var id = setInterval(loadLiveBlogs, 10000);
				loadLiveBlogs();
				
				function success_callback(response, status, xhr){
					if(timestamp != response.latest_timestamp){
						var lb_string = '<h2>Live Updates</h2><ul>';
						var num_of_lb = response.entries.length - 1;
						var lb_counter = 0;
						var i = 0;
						
						while(lb_counter < Math.min(4, response.entries.length)){
							var lb_link = '<a href="<?php the_permalink(); ?>#liveblog-entry-' + response.entries[i].id + '">';					
							var lb_content = $('.liveblog-entry-text', response.entries[num_of_lb - i].html).text().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
							if(lb_content != ""){
								lb_counter++;
								lb_string += '<li>' + lb_link + lb_content.substring(0, <?php echo $lb_length; ?>) + '...</a></li>';
							}
							i++;
							lb_counter = i > response.entries.length-1 ? response.entries.length : lb_counter;
						}
						lb_string += '</ul>';
						$('#entry-liveblog').html(lb_string);
						timestamp = response.latest_timestamp;
					}
				}
				
				function error_callback(response){
					counter++;
					if(counter == 3){
						clearInterval(id);
					}
				}
				
				function current_timestamp() {
					return Math.floor( Date.now() / 1000 );
				};
				
				function loadLiveBlogs(){
					$.ajax( {
						url: '<?php the_permalink(); ?>liveblog/1/' + current_timestamp() + '/',
						data: {},
						type: 'GET',
						dataType: 'json',
						success: success_callback,
						error: error_callback
					} );
				}
				
			});
			</script>
			<?php endif; //end liveblog ?>
		
		</div>
	<?php endif; //liveblog ?>
</article>
<?php endwhile; wp_reset_postdata(); ?>