<?php
/** 
 * CalPress Widgets that are included as part of the theme package.
 * Additional widget can be added by installing plugins. This theme
 * includes some basic widget functionality for a few different features.
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

/**
 * Creates a widget for Generic Advertisements, like Google's Double-Click
 * or any ad service that offers embed code. If additional code in the head
 * is required, please visit CalPress Theme Options
 *
 * @since CalPress 0.9.7
 */
class GenericAdvertisement extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_ad', 'Advertisement', array( 'description' => 'Put HTML code for an advertisement' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		
		$ad_html = empty($instance['ad_html']) ? '' : $instance['ad_html'];
		$ad_category = empty($instance['ad_category']) ? '' : explode(",", $instance['ad_category']);
		
		if(!empty($ad_category)){
			if(is_category($ad_category)){
				echo $before_widget;
				echo '<h3 class="advertisement">Advertisement</h3>'.PHP_EOL;
				echo $ad_html.PHP_EOL;
				echo $after_widget;
			}
		} else {
			echo $before_widget;
			echo '<h3 class="advertisement">Advertisement</h3>'.PHP_EOL;
			echo $ad_html.PHP_EOL;
			echo $after_widget;
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['ad_html'])) 
			$instance['ad_html'] = $new_instance['ad_html'];
			
		if(isset($new_instance['ad_category'])){
			$temp = explode(",", strip_tags($new_instance['ad_category']));
			array_walk($temp, 'trim_value');
			$instance['ad_category'] = (string) implode(",", $temp);
		}
			
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('ad_html' => '', 'ad_category'=> ''));
		$ad_html = isset($instance['ad_html']) ? $instance['ad_html'] : '';
		$ad_category = isset($instance['ad_category']) ? $instance['ad_category'] : '';
    ?>
	<p>To create a advertisement, type or paste in HTML code below.</p> 
	<p>
		<label for="<?php echo $this->get_field_id('ad_html'); ?>"><?php _e('HTML code only:'); ?></label>
		<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('ad_html'); ?>" name="<?php echo $this->get_field_name('ad_html'); ?>"><?php echo esc_textarea($ad_html); ?></textarea>
	</p>
	<p>Type in which categories this ad should appear. These should only be numerical category IDs and they MUST be separated by commas.</p>
	<p>
		<label for="<?php echo $this->get_field_id('ad_category'); ?>">Categories:</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('ad_category'); ?>" name="<?php echo $this->get_field_name('ad_category'); ?>" value="<?php echo esc_attr($ad_category); ?>">
	</p>
<?php
	}
}

/**
 * Creates a widget for Leaderboard Advertisements. It allows you to specify some
 * embed code for the leaderboard spot, and some alternate code when on mobile devices.
 * The alternate code gets swapped out by JavaScript when the screen size shrinks.
 *
 * @since CalPress 0.9.7
 */
class LeaderboardAdvertisement extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_leaderboard', 'Leaderboard Advertisement', array( 'description' => 'Put HTML code for an advertisement' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		
		$title = empty($instance['title']) ? '' : $instance['title'];
		$ad_html = empty($instance['ad_html']) ? '' : $instance['ad_html'];
		$ad_small = empty($instance['ad_small']) ? '' : $instance['ad_small'];
		$ad_category = empty($instance['ad_category']) ? '' : explode(",", $instance['ad_category']);
		
		if(!empty($ad_category)){
			if(is_category($ad_category)){
				echo $before_widget;
				echo '<h3 class="advertisement" style="font-family:Helvetica,Arial,sans-serif;font-size:10px;text-align:center;margin-top:25px;">' . $title .'</h3>'.PHP_EOL;
				echo '<div id="leaderboard-holder" style="overflow:hidden;"></div>';
				echo $after_widget;
			}
		} else {
			echo $before_widget;
			echo '<h3 class="advertisement" style="font-family:Helvetica,Arial,sans-serif;font-size:10px;text-align:center;margin-top:25px;">' . $title .'</h3>'.PHP_EOL;
			echo '<div id="leaderboard-holder" style="overflow:hidden;"></div>';
			echo $after_widget;
		}
		
		echo '
		<script>
		  jQuery(document).ready(function($){
		    if($(window).width() < 620){
		     $("#leaderboard-holder").html(\'' . (empty($ad_small) ? addslashes($ad_html) : addslashes($ad_small)) .'\')
		     .css({
		       \'width\':\'320px\',
		       \'height\':\'50px\',
		       \'margin-left\':\'auto\', 
		       \'margin-right\':\'auto\',
		       \'margin-bottom\':\'-30px\'
		      });
		    } else {
 		     $("#leaderboard-holder").html(\'' . addslashes($ad_html) .'\')
 		     .css({
 		       \'width\':\'728px\',
 		       \'height\':\'90px\',
 		       \'margin-left\':\'auto\', 
 		       \'margin-right\':\'auto\',
 		       \'margin-bottom\':\'-30px\'
 		      });
		    }
		  });
		</script>
		';
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    $instance = wp_parse_args($old_instance, $new_instance);
    
    if(isset($new_instance['title'])) 
			$instance['title'] = $new_instance['title'];

    if(isset($new_instance['ad_html'])) 
			$instance['ad_html'] = $new_instance['ad_html'];
			
		if(isset($new_instance['ad_small'])) 
			$instance['ad_small'] = $new_instance['ad_small'];
			
		if(isset($new_instance['ad_category'])){
			$temp = explode(",", strip_tags($new_instance['ad_category']));
			array_walk($temp, 'trim_value');
			$instance['ad_category'] = (string) implode(",", $temp);
		}
			
    return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'ad_html' => '', 'ad_category'=> '', 'ad_small'=>''));
    ?>
	<p>To create a advertisement, type or paste in HTML code below.</p>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
	  <input type="text" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('ad_html'); ?>"><?php _e('HTML code only:'); ?></label>
		<textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('ad_html'); ?>" name="<?php echo $this->get_field_name('ad_html'); ?>"><?php echo esc_textarea($instance['ad_html']); ?></textarea>
	</p>
	<p>
	  <label for="<?php echo $this->get_field_id('ad_small'); ?>"><?php _e('Optional: HTML code for mobile'); ?></label>
	  <textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('ad_small'); ?>" name="<?php echo $this->get_field_name('ad_small'); ?>"><?php echo esc_textarea($instance['ad_small']); ?></textarea>
	</p>
	<p>Type in which categories this ad should appear. These should only be numerical category IDs and they <strong>MUST</strong> be separated by commas.</p>
	<p>
		<label for="<?php echo $this->get_field_id('ad_category'); ?>">Categories:</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('ad_category'); ?>" name="<?php echo $this->get_field_name('ad_category'); ?>" value="<?php echo esc_attr($instance['ad_category']); ?>">
	</p>
<?php
	}
}


/**
 * Creates a widget for Todays Posts, which is a featured category
 * to display in a widget on the site. 
 *
 * @since CalPress 0.9.7
 */
class FeaturedCategory extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_featured_category', 'Featured Category Scroller', array( 'description' => 'A scrollable category of posts to feature in the sidebar.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
		
		$title = empty($instance['title']) ? '' : $instance['title'];
		$category = empty($instance['category']) ? '' : $instance['category'];
		$numofposts = empty($instance['numberofposts']) ? '' : $instance['numberofposts'];
		$height = empty($instance['height']) ? '50' : $instance['height'];
	
		
		if(!empty($instance['titleimage'])){
			$titleimage = '<div><h2 style="background:url(\'' . $instance['titleimage'] .'\') no-repeat center; text-indent:-9000em; height:' . $height . 'px;">';	
		}

		if(!$category || !$numofposts)
			return;

		//$loop_args['nopaging'] = true;
		$loop_args['post_status'] = 'publish';
		$loop_args['category_name'] = $category;
		$loop_args['posts_per_page'] = (int) $numofposts;
		
		
		$widget_query = new WP_Query($loop_args);
				
		$bullets = '<div>';
		for($i=0; $i < ceil($widget_query->post_count/5); $i++):
			$bullets .= '<span>&bull;</span>';
		endfor;
		$bullets .= '</div>';
		
		if($widget_query->have_posts()){
			$counter = 0;
			echo $before_widget;
			echo (isset($titleimage) ? $titleimage : $before_title) . $title . $after_title;
			//echo '<div id="scroll-up" class="scroll-button"></div>';
			echo '<div id="scroll-pane">';
			echo '<ul class="featured-post-list">';
			while($widget_query->have_posts()):
				$widget_query->the_post();
				if(($counter % 5) == 0) echo '<div class="scroll-block">';
				echo '<li><h4 class="entry-title"><a href="' . get_permalink() . '" title="' . sprintf( __('Permalink to %s', 'CalPress'), the_title_attribute('echo=0') ) . '">' . wp_trim_words( get_the_title() , 11, '...') . '</a></h4>';
				echo '<p class="entry-meta"><span class="time">Posted ' . calpress_posted_on(get_the_time('U'), '', false) . '</span></p></li>';
				if(($counter % 5) == 4) echo '</div>';
				$counter++;
			endwhile;
			if(($counter % $numofposts) != 0) echo '</div>';
			echo '</ul>';
			echo '</div>';
			echo '<div class="scroll-button">' . $bullets . '<span id="scroll-up"></span><span id="scroll-down"></span></div>';
			wp_reset_postdata();
			echo $after_widget;
			
			echo '
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function($){
					$this = $(\'ul.featured-post-list\');
					$pager = $(\'.scroll-button div span\');
					$pager.eq(0).css(\'color\', \'#999\');
					var totalSlides = $(\'ul > div\').length;
					var currentSlide = 1;
					var speed = 500;

					for(var i = $this.children().length, y = 0; i > 0; i--, y++) { 		
						$this.children().eq(y).css(\'zIndex\', i + 99999);
					}

					$(\'#scroll-down\').click(function(){
						if(currentSlide != totalSlides){
							currentSlide++;
							$this.animate({\'top\' : \'-=300\'}, speed);
							$pager.css(\'color\', \'#fff\');
							$pager.eq(currentSlide-1).css(\'color\', \'#999\');
						}
					});
					$(\'#scroll-up\').click(function(){
						if(currentSlide != 1){
							currentSlide--;
							$this.animate({\'top\' : \'+=300\'}, speed);
							$pager.css(\'color\', \'#fff\');
							$pager.eq(currentSlide-1).css(\'color\', \'#999\');
						} 
					});

					$pager.click(function(){
						$this.animate({\'top\' : \'-\' + ($(this).index() * 300)}, speed);
						$pager.css(\'color\', \'#fff\');
						$pager.eq($(this).index()).css(\'color\', \'#999\');
						currentSlide = $(this).index()+1;
					});
				});
			</script>
			';
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = trim(strip_tags($new_instance['title']));
			
		if(isset($new_instance['category']))
			$instance['category'] = $new_instance['category'];
			
		if(isset($new_instance['numberofposts']))
			$instance['numberofposts'] = $new_instance['numberofposts'];
			
		if(isset($new_instance['height']) && is_numeric($new_instance['height']))
			$instance['height'] = (int) $new_instance['height'];
			
		if(isset($new_instance['titleimage'])){
			$headers = calpress_get_headers_curl(esc_url($new_instance['titleimage']), 5);
			$headers_string = implode(' ', $headers);

			if (preg_match('/image\/(png|gif|jpeg|pjpeg|svg\+xml)/i', $headers_string) ){
				
				if(!isset($new_instance['height']) || !is_numeric($new_instance['height']) || (int) $new_instance['height'] < 1):
					$sizes = @getimagesize(esc_url($new_instance['titleimage']));
					if($sizes)
						$instance['height'] = $sizes[1];
				endif;
				$instance['titleimage'] = esc_url($new_instance['titleimage']);
				
			} 
		}
			
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title' => '', 'category'=>'', 'numberofposts'=>'20', 'titleimage'=>'', 'height'=>''));
		$categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC'));

		echo '<p>' . __('Enter a title and pick a category to display posts in this widget', 'CalPress') . '</p>'.PHP_EOL;
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '"></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('category') . '">' . __('Pick a category', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('category') . '" id="' . $this->get_field_id('category') . '">'.PHP_EOL;
		foreach($categories as $category):
			echo '	<option value="' . $category->slug .'" '. selected($category->slug, $instance['category'], false) . '>' . $category->name . '</option>'.PHP_EOL;		
		endforeach;
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('numberofposts') . '">' . __('Number of posts to display', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('numberofposts') . '" id="' . $this->get_field_id('numberofposts') . '">'.PHP_EOL;
		for($i=1; $i<21; $i++):	
			echo '	<option value="' . $i .'" '. selected($i, $instance['numberofposts'], false) . '>' . $i . '</option>'.PHP_EOL;		
		endfor;
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('titleimage') . '">' . __('(Optional) URL to an image for the title 300 width', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('titleimage') . '" class="widefat" name="' . $this->get_field_name('titleimage') . '" value="' . $instance['titleimage'] . '"></p>'.PHP_EOL;
		
		echo '<p><label for"' . $this->get_field_id('height') . '">' . __('Height:') . '</label>'.PHP_EOL;
		echo '<input type="number" min="0" id="' . $this->get_field_id('height') .'" name="' . $this->get_field_name('height') . '" value="' . $instance['height'] .'" style="width:100px;"></p>'.PHP_EOL;
		echo '<p class="description">If left blank, will try to determine height of image automatically</p>';
	}
}

/**
 * Creates a widget for Todays Posts, which is a featured category
 * to display in a widget on the site. 
 *
 * @since CalPress 0.9.7
 */
class CategoryOfPosts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_category_of_posts', 'Top Posts in a Category', array( 'description' => 'Shows the top posts in a category.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
		
		$title = empty($instance['title']) ? '' : $instance['title'];
		$description = empty($instance['description']) ? '' : '<p class="widgetdescription">' . $instance['description'] . '</p>';
		$category = empty($instance['category']) ? '' : $instance['category'];
		$numofposts = empty($instance['numberofposts']) ? '' : $instance['numberofposts'];
		$showdates = empty($instance['showdates']) ? 'true' : $instance['showdates'];

		if(!$category || !$numofposts)
			return;

		$loop_args['post_status'] = 'publish';
		$loop_args['category_name'] = $category;
		$loop_args['posts_per_page'] = (int) $numofposts;
		
		$widget_query = new WP_Query($loop_args);
		
		if($widget_query->have_posts()){
			echo $before_widget;
			echo $before_title . $title . $after_title;
			echo $description;
			echo '<ul class="featured-post-list">';
			while($widget_query->have_posts()):
				$widget_query->the_post();
				echo '<li><a href="' . get_permalink() . '" title="' . sprintf( __('Permalink to %s', 'CalPress'), the_title_attribute('echo=0') ) . '">' . wp_trim_words( get_the_title() , 15, '...') . '</a>';
				if($showdates == "true"):
					echo '<span class="time">Posted ' . calpress_posted_on(get_the_time('U'), '', false) . '</span></li>';
				endif;
			endwhile;
			echo '</ul>';
			wp_reset_postdata();
			echo $after_widget;
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

		$instance['showdates'] = $new_instance['showdates'];

    	if(isset($new_instance['title'])) 
			$instance['title'] = trim(strip_tags($new_instance['title']));
			
		if(isset($new_instance['description'])) 
			$instance['description'] = trim(strip_tags($new_instance['description']));
			
		if(isset($new_instance['category']))
			$instance['category'] = $new_instance['category'];
			
		if(isset($new_instance['numberofposts']))
			$instance['numberofposts'] = $new_instance['numberofposts'];
			
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title' => '', 'category'=>'', 'description'=>'', 'numberofposts'=>'5', 'showdates'=>'true'));
		$categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC'));

		echo '<p>' . __('Enter a title and pick a category to display posts in this widget', 'CalPress') . '</p>'.PHP_EOL;
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('description') . '">' . __('Description', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('description') . '" class="widefat" name="' . $this->get_field_name('description') . '" value="' . $instance['description'] . '" placeholder="' . __('(optional)') . '"></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('category') . '">' . __('Pick a category', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('category') . '" id="' . $this->get_field_id('category') . '">'.PHP_EOL;
		foreach($categories as $category):
			echo '	<option value="' . $category->slug .'" '. selected($category->slug, $instance['category'], false) . '>' . $category->name . '</option>'.PHP_EOL;		
		endforeach;
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><select name="' . $this->get_field_name('showdates') . '" id="' . $this->get_field_id('showdates') . '">'.PHP_EOL;
		echo '	<option value="true" '. selected('true', $instance['showdates'], false) . '>Show post dates</option>'.PHP_EOL;		
		echo '	<option value="false" '. selected('false', $instance['showdates'], false) . '>Hide post dates</option>'.PHP_EOL;		
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('numberofposts') . '">' . __('Number of posts to display', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('numberofposts') . '" id="' . $this->get_field_id('numberofposts') . '">'.PHP_EOL;
		for($i=1; $i<21; $i++):	
			echo '	<option value="' . $i .'" '. selected($i, $instance['numberofposts'], false) . '>' . $i . '</option>'.PHP_EOL;		
		endfor;
		echo '</select></p>'.PHP_EOL;
	}
}


/**
 * Creates a widget for Todays Posts, which is a featured category
 * to display in a widget on the site. 
 *
 * @since CalPress 0.9.7
 */
class FlickrPoolPhoto extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_flickr_pool', 'Flickr Pool Photo', array( 'description' => 'Displays an image from a Flickr pool.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
		
		$title = empty($instance['title']) ? 'Flickr Photo' : $instance['title'];
		$description = empty($instance['description']) ? '' : '<p class="widgetdescription">' . $instance['description'] . '</p>';
		$url = empty($instance['url']) ? '' : $instance['url'];
		$order = empty($instance['order']) ? 'random' : $instance['order'];
		$index = ($order == 'random' ? 'randomFlickrPick' : '0');
		$random_id = rand(100000, 999999);
		
		if(!$url)
			return;
			
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo $description;
		echo '<div id="flickrPoolPhoto_' . $random_id .'"><a><img src="http://l.yimg.com/g/images/iphone/balls-24x12-trans.gif" /></a>';
		echo '<p id="flickrDescription_' . $random_id. '"></p></div>';
		echo $after_widget;
		
		echo '
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function($){
				$.getJSON("' . $url . '", function(data){
					var randomFlickrPick = Math.floor(Math.random() * data.items.length);
					$("#flickrPoolPhoto_' . $random_id . ' img")
						.attr(\'src\', data.items[' . $index . '].media.m.replace("_m.jpg", ".jpg"))
						.css({\'width\':\'100%\', \'height\':\'auto\'});
					$("#flickrPoolPhoto_' . $random_id .' a").attr(\'href\', data.items[' . $index .'].link);
					$("#flickrDescription_' . $random_id. '").html(data.items[' . $index .'].title);
				});
			});
		</script>
		';

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = trim(strip_tags($new_instance['title']));
			
		if(isset($new_instance['description'])) 
			$instance['description'] = trim(strip_tags($new_instance['description']));
			
		if(isset($new_instance['order']))
			$instance['order'] = $new_instance['order'];
			
		if(isset($new_instance['url'])){
			
			$url = parse_url($new_instance['url']);
			parse_str($url['query'], $flickr_vars);
			
			if(!isset($flickr_vars['id']) && !isset($flickr_vars['nsid']))
				return $instance; //wrong url
				
			$flickr_vars['format'] = 'json';
			$flickr_vars['jsoncallback'] = '?';
			
			if(isset($flickr_vars['id']))
				$instance['url'] = 'http://api.flickr.com/services/feeds/groups_pool.gne?' . build_query($flickr_vars);
				
			if(isset($flickr_vars['nsid']))
				$instance['url'] = 'http://api.flickr.com/services/feeds/photoset.gne?' . build_query($flickr_vars);
		}
						
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title' => '', 'description'=>'', 'url'=>'', 'order'=>'random'));

		echo '<p class="description">' . __('This widget will display a photo from a Flickr pool, either newest photo or a random photo.', 'CalPress') . '</p>'.PHP_EOL;
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title above widget', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('description') . '">' . __('Description', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('description') . '" class="widefat" name="' . $this->get_field_name('description') . '" value="' . $instance['description'] . '" placeholder="' . __('(optional)') . '"></p>'.PHP_EOL;

		echo '<p><select name="' . $this->get_field_name('order') . '" id="' . $this->get_field_id('order') . '">'.PHP_EOL;
		echo '	<option value="random" '. selected('random', $instance['order'], false) . '>Display random image from pool</option>'.PHP_EOL;		
		echo '	<option value="newest" '. selected('newest', $instance['order'], false) . '>Display newest image from pool</option>'.PHP_EOL;		
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('url') . '">' . __('URL of Flickr <strong>RSS</strong> feed', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="url" id="' . $this->get_field_id('url') . '" class="widefat" name="' . $this->get_field_name('url') . '" value="' . $instance['url'] . '" placeholder="' . __('http://') . '" required></p>'.PHP_EOL;
		echo '<img src="' . THEMEURI .'/images/flickr-feed.png" style="border:2px solid #999; float:right;" />';
		echo '<p class="description">' . __('Make sure to copy the <strong>RSS url</strong> from your flick pool. You can find this by right-clicking the orange RSS icon at the bottom of the screen.', 'CalPress') . '</p>'.PHP_EOL;
	}
}


/**
 * Creates a widget for Todays Posts, which is a featured category
 * to display in a widget on the site. 
 *
 * @since CalPress 0.9.7
 */
class AboutUS extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_about_us', 'About Us', array( 'description' => 'An about widget with social media links.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
		
		$title = empty($instance['title']) ? '' : $instance['title'];
		$abouttext = empty($instance['abouttext']) ? '' : $instance['abouttext'];
		$link0 = empty($instance['link0']) ? '' : parse_url($instance['link0']);
		$link1 = empty($instance['link1']) ? '' : parse_url($instance['link1']);
		$link2 = empty($instance['link2']) ? '' : parse_url($instance['link2']);
		$link3 = empty($instance['link3']) ? '' : parse_url($instance['link3']);
		$link4 = empty($instance['link4']) ? '' : parse_url($instance['link4']);
		$link5 = empty($instance['link5']) ? '' : parse_url($instance['link5']);
		$link6 = empty($instance['link6']) ? '' : parse_url($instance['link6']);
		
		
		echo $before_widget;
		echo $title ? $before_title . $title . $after_title : '';
		echo $abouttext;
		echo '<ul>';
		for($i=0; $i < 7; $i++):
			if(${'link'.$i}){
				
				preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63})\.[a-z\.]{2,6}$/i', ${'link'.$i}['host'], $domain);
				//style="background:url(\'http://www.google.com/s2/u/0/favicons?domain=' . ${'link'.$i}['host'] .'\') no-repeat 0 0"
				echo '<li>';
				echo '<a href="' . $instance['link'.$i] . '">'. ucwords($domain['domain']) . '</a>';
				echo '</li>';
			}
		endfor;
		echo '</ul>';
		echo $after_widget;

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = trim(strip_tags($new_instance['title']));
			
		if(isset($new_instance['abouttext']))
			$instance['abouttext'] = trim($new_instance['abouttext']);
			
		for($i=0; $i < 7; $i++):
			
			if(isset($new_instance['link'. $i]))
				$instance['link'.$i] = esc_url($new_instance['link'.$i]);
			
		endfor;
		
			
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'abouttext'=>'', 
			'link0'=>'',
			'link1'=>'',
			'link2'=>'',
			'link3'=>'',
			'link4'=>'',
			'link5'=>'',
			'link6'=>'',)
		);

		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('About Us') . '"></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('abouttext') . '">' . __('About this site', 'CalPress') . '</label>'.PHP_EOL;
		echo '<textarea type="text" rows="10" id="' . $this->get_field_id('abouttext') . '" class="widefat" name="' . $this->get_field_name('abouttext') . '" placeholder="' . __('Description') . '">' . esc_textarea($instance['abouttext']) .'</textarea></p>'.PHP_EOL;
		
		echo '<p class="description">' . __('Enter in URLs for any social media sites this site runs.', 'CalPress') . '</p>'.PHP_EOL;
		
		echo '<p><input type="text" id="' . $this->get_field_id('link0') . '" class="widefat" name="' . $this->get_field_name('link0') . '" value="' . esc_url($instance['link0']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link1') . '" class="widefat" name="' . $this->get_field_name('link1') . '" value="' . esc_url($instance['link1']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link2') . '" class="widefat" name="' . $this->get_field_name('link2') . '" value="' . esc_url($instance['link2']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link3') . '" class="widefat" name="' . $this->get_field_name('link3') . '" value="' . esc_url($instance['link3']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link4') . '" class="widefat" name="' . $this->get_field_name('link4') . '" value="' . esc_url($instance['link4']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link5') . '" class="widefat" name="' . $this->get_field_name('link5') . '" value="' . esc_url($instance['link5']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;
		echo '<p><input type="text" id="' . $this->get_field_id('link6') . '" class="widefat" name="' . $this->get_field_name('link6') . '" value="' . esc_url($instance['link6']) . '" placeholder="' . __('Social Media URL') . '"></p>'.PHP_EOL;

	}
}

/**
 * Creates a widget for Todays Posts, which is a featured category
 * to display in a widget on the site. 
 *
 * @since CalPress 0.9.7
 */
class PromoImage extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_promo_image', 'Promotional Image', array( 'description' => 'Displays an image or in-house ad.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
		
		$url = empty($instance['url']) ? '' : $instance['url'];
		$link = empty($instance['link']) ? '' : $instance['link'];
		
		echo $before_widget;
		echo $link ? '<a href="' . $link . '">' : '';
		echo '<img src="' . $url .'" alt="Promo ad" style="width:100%; height:auto;" />';
		echo $link ? '</a>' : '';
		echo $after_widget;
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['link'])) 
			$instance['link'] = esc_url($new_instance['link']);
			
		if(isset($new_instance['url'])) 
			$instance['url'] = esc_url($new_instance['url']);
						
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('link'=>'', 'url'=>''));

		echo '<p class="description">' . __('This widget will display a photo. You can optionally make the photo a clickable link. Width needs to be 300 pixels.', 'CalPress') . '</p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('url') . '">' . __('URL of image', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="url" id="' . $this->get_field_id('url') . '" class="widefat" name="' . $this->get_field_name('url') . '" value="' . $instance['url'] . '" placeholder="' . __('http://') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('link') . '">' . __('Link image to:', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="url" id="' . $this->get_field_id('link') . '" class="widefat" name="' . $this->get_field_name('link') . '" value="' . $instance['link'] . '" placeholder="' . __('(optional link url)') . '"></p>'.PHP_EOL;
		
		if(!empty($instance['url']))
			echo '<p>Current Image:</p><p><img src="' . $instance['url'] . '" alt="current image" style="width:230px; height:auto;"></p>';
	}
}

/**
 *
 * @todo Creates a widget for listing a bunch of links. 
 * @since CalPress 0.9.7
 */
class BunchOfLinks extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_bunch_of_links', 'A Bunch Of Links', array( 'description' => 'Displays some links you specify.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		extract( $args );
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = strip_tags(trim($new_instance['title']));
			
		if(isset($new_instance['description'])) 
			$instance['description'] = strip_tags($new_instance['description']);
			
		if(!empty($new_instance['linktexts'])):
			foreach($new_instance['linktexts'] as $key => $linktext):
				$instance['linktext'][$key] = $linktext;
			endforeach;
		endif;
		
		if(!empty($new_instance['linkurls'])):
			foreach($new_instance['linkurls'] as $key => $linkurls):
				$instance['linkurls'][$key] = esc_url($linkurls);
			endforeach;
		endif;
						
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'description'=>'', 'linktexts'=>array(), 'linkurls'=>array()));
		
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('description') . '">' . __('Description', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('description') . '" class="widefat" name="' . $this->get_field_name('description') . '" value="' . $instance['description'] . '" placeholder="' . __('Optional Description') . '"></p>'.PHP_EOL;
		
		echo '<p>' . __('Links:', 'CalPress') . '</p>'.PHP_EOL;
		
		if(!empty($instance['linkurls'])):
		
			foreach($instance['linkurls'] as $key => $linkurls):
				if(!isset($instance['linktexts'][$key]))
					$instance['linktexts'][$key] = '';
				echo '<p class="linkquestions">'.PHP_EOL;
				echo '<input type="text" id="' . $this->get_field_id('linktexts') . '[]" class="widefat linktext" name="' . $this->get_field_name('linktexts') . '[]" value="' . $instance['linktexts'][$key] . '" placeholder="' . __('Link Text') . '" required>'.PHP_EOL;
				echo '<input type="url" id="' . $this->get_field_id('linkurls') . '[]" class="widefat linkurl" name="' . $this->get_field_name('linkurls') . '[]" value="' . $linkurls . '" placeholder="' . __('Link URL') . '" required>'.PHP_EOL;
				echo '</p>'.PHP_EOL;
			endforeach;
		else:
			echo '<p class="linkquestions">'.PHP_EOL;
			echo '<input type="text" id="' . $this->get_field_id('linktexts') . '[]" class="widefat linktext" name="' . $this->get_field_name('linktexts') . '[]" value="" placeholder="' . __('Link Text') . '" required>'.PHP_EOL;
			echo '<input type="url" id="' . $this->get_field_id('linkurls') . '[]" class="widefat linkurl" name="' . $this->get_field_name('linkurls') . '[]" value="" placeholder="' . __('Link URL') . '" required>'.PHP_EOL;
			echo '</p>'.PHP_EOL;
		endif;
		
		echo '<p><a class="addmorefieldslink" href="javascript:;">'. __('Add another link') . '</a></p>'.PHP_EOL;
		
		echo '
			<script type="text/javascript">
				jQuery(document).ready(function($){
					var onButtonClicked = function(){
						$(".linkquestions").eq(0).clone().insertBefore(this, "p");
					};
					
					$(\'.addmorefieldslink\')
						.unbind(\'click\', onButtonClicked) 
					    .bind(\'click\', onButtonClicked);
					
				});
			</script>
		';

	}
}

/**
 * Widget showing most commented articles 
 *
 * @since CalPress 0.9.7
 */
class MostCommentedPosts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_most_commented', 'Most Commented Articles', array( 'description' => 'Displays a list of most commented articles' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		global $wpdb;
		extract( $args );
		
		$title = empty($instance['title']) ? 'Most Commented' : $instance['title'];
		$description = empty($instance['description']) ? '' : '<p class="widgetdescription">' . $instance['description'] . '</p>'.PHP_EOL;
		$numberofposts = empty($instance['numberofposts']) ? 5 : (int) $instance['numberofposts'];
		$daysback = empty($instance['daysback']) ? 30 : (int) $instance['daysback'];
		$popular = '';
  
	    $posts = $wpdb->get_results("SELECT comment_count, ID, post_title, post_date FROM $wpdb->posts WHERE post_type='post' AND post_status = 'publish' AND DATE_SUB(CURDATE(), INTERVAL $daysback DAY) <= post_date_gmt ORDER BY comment_count DESC LIMIT 0 , $numberofposts");    
	    foreach ($posts as $post) {  
			
	        $id = $post->ID;  
	        $post_title = $post->post_title;  
	        $count = $post->comment_count;
			$date = $post->post_date;

	        if ($count == 0) {
				$popular .= '<ul>'.PHP_EOL;
	            $popular .= '<li>'.PHP_EOL;  
	            $popular .= '<a href="' . get_permalink($id) . '" title="' . $post_title . '">' . $post_title . '</a> '.PHP_EOL; 
	 			$popular .= '<span class="time">Posted: '. calpress_posted_on(strtotime($date), '', false) . '</span>'.PHP_EOL;
	            $popular .= '</li>'.PHP_EOL;  
				$popular .= '</ul>'.PHP_EOL;
	        }  
	    } 
		echo $before_widget;
		echo $before_title . $title . $after_title; 
		echo $description;
	    echo $popular;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = strip_tags(trim($new_instance['title']));
			
		if(isset($new_instance['description'])) 
			$instance['description'] = strip_tags($new_instance['description']);
			
		if(!empty($new_instance['numberofposts']))
			$instance['numberofposts'] = $new_instance['numberofposts'];
		
		if(!empty($new_instance['daysback']))
			$instance['daysback'] = $new_instance['daysback'];
						
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'description'=>'', 'numberofposts'=>'5', 'daysback'=>'20'));
		
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('description') . '">' . __('Description', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('description') . '" class="widefat" name="' . $this->get_field_name('description') . '" value="' . $instance['description'] . '" placeholder="' . __('Optional Description') . '"></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('numberofposts') . '">' . __('Number of posts to display: ', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('numberofposts') . '" id="' . $this->get_field_id('numberofposts') . '">'.PHP_EOL;
		for($i=1; $i<11; $i++):	
			echo '	<option value="' . $i .'" '. selected($i, $instance['numberofposts'], false) . '>' . $i . '</option>'.PHP_EOL;		
		endfor;
		echo '</select></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('daysback') . '">' . __('Number of days back to consider: ', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('daysback') . '" id="' . $this->get_field_id('daysback') . '">'.PHP_EOL;
		for($i=1; $i<31; $i++):	
			echo '	<option value="' . $i .'" '. selected($i, $instance['daysback'], false) . '>' . $i . '</option>'.PHP_EOL;		
		endfor;
		echo '</select></p>'.PHP_EOL;

	}
}

/**
 * Widget showing most commented articles 
 *
 * @since CalPress 0.9.7
 */
class CalPressRecentComments extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'calpress_recent_comments', 'Recent Comments by Comment', array( 'description' => 'Displays a list of recent comments on the site.' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {		
		global $wpdb;
		extract( $args );
		
		$title = empty($instance['title']) ? 'Recent Comments' : $instance['title'];
		$description = empty($instance['description']) ? '' : '<p class="widgetdescription">' . $instance['description'] . '</p>'.PHP_EOL;
		$numberofposts = empty($instance['numberofposts']) ? 5 : (int) $instance['numberofposts'];
		$popular = '';    
	
		$featuredComments = $wpdb->get_results("SELECT wp_comments.comment_ID, wp_comments.comment_content, wp_comments.comment_post_ID, wp_comments.comment_author, wp_comments.comment_date, wp_comments.comment_approved, wp_comments.comment_type, wp_posts.post_title, wp_posts.post_date, wp_posts.post_password FROM $wpdb->comments, $wpdb->posts WHERE wp_comments.comment_approved ='1' AND wp_comments.comment_type = '' AND wp_posts.post_password = '' AND wp_posts.id = wp_comments.comment_post_ID ORDER BY wp_comments.comment_date DESC LIMIT $numberofposts;");
	
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo $description;
		echo '<ul>'.PHP_EOL;
	    foreach ($featuredComments as $featuredComment):  
	        $commentcontent = wp_trim_words($featuredComment->comment_content, 15, '...');
			$commentdate = calpress_posted_on(strtotime($featuredComment->comment_date), '', false);
			$posturl = get_permalink( $featuredComment->comment_post_ID );
			$commenturl = $posturl . "#comment-" . $featuredComment->comment_ID;
			$posttitle = esc_attr($featuredComment->post_title);
			
			echo '	<li><a href="' . $commenturl .'" title="' . __('Link to comment in ') . $posttitle .'">' . $commentcontent . '</a>'.PHP_EOL;
			echo '<span class="time">Posted on: ' . $commentdate . '</span></li>'.PHP_EOL;
	    endforeach;
		echo '</ul>'.PHP_EOL;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    	$instance = wp_parse_args($old_instance, $new_instance);

    	if(isset($new_instance['title'])) 
			$instance['title'] = strip_tags(trim($new_instance['title']));
			
		if(isset($new_instance['description'])) 
			$instance['description'] = strip_tags($new_instance['description']);
			
		if(!empty($new_instance['numberofposts']))
			$instance['numberofposts'] = $new_instance['numberofposts'];
						
    	return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'description'=>'', 'numberofposts'=>'5'));
		
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('title') . '" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $instance['title'] . '" placeholder="' . __('Title') . '" required></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('description') . '">' . __('Description', 'CalPress') . '</label>'.PHP_EOL;
		echo '<input type="text" id="' . $this->get_field_id('description') . '" class="widefat" name="' . $this->get_field_name('description') . '" value="' . $instance['description'] . '" placeholder="' . __('Optional Description') . '"></p>'.PHP_EOL;
		
		echo '<p><label for="' . $this->get_field_id('numberofposts') . '">' . __('Number of posts to display: ', 'CalPress') . '</label>'.PHP_EOL;
		echo '<select name="' . $this->get_field_name('numberofposts') . '" id="' . $this->get_field_id('numberofposts') . '">'.PHP_EOL;
		for($i=1; $i<11; $i++):	
			echo '	<option value="' . $i .'" '. selected($i, $instance['numberofposts'], false) . '>' . $i . '</option>'.PHP_EOL;		
		endfor;
		echo '</select></p>'.PHP_EOL;

	}
}

/**
 * Registers widgets. We also unregister some widgets
 * that are superfluous or not styled with this theme.
 *
 * @uses unregister_widget()
 * @uses register_widget()
 * @return void
 */
function calpress_sidebar_register_widgets() {

	unregister_widget( 'Akismet_Widget');
	unregister_widget( 'WP_Widget_Pages');
	unregister_widget( 'WP_Widget_Calendar');
	unregister_widget( 'WP_Widget_Archives');
	unregister_widget( 'WP_Widget_Links');
	unregister_widget( 'WP_Widget_Recent_Comments');
	
	register_widget( 'PromoImage' );
	register_widget( 'FlickrPoolPhoto' );
	register_widget( 'CategoryOfPosts' );
	register_widget( 'AboutUS' );
	register_widget( 'FeaturedCategory' );
	register_widget( 'GenericAdvertisement' );
	register_widget( 'MostCommentedPosts' );
	register_widget( 'CalPressRecentComments' );
	register_widget( 'LeaderboardAdvertisement' );
	
}

add_action( 'widgets_init', 'calpress_sidebar_register_widgets' );