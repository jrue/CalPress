<?php 
/**
 * This is the template for the output of the events list widget. 
 *
 * This file is used by The Events Calendar Widget. See
 * http://wordpress.org/extend/plugins/the-events-calendar/
 *
 * @return string
 */

// Vars set:
// '$event->AllDay',
// '$event->StartDate',
// '$event->EndDate',
// '$event->ShowMapLink',
// '$event->ShowMap',
// '$event->Cost',
// '$event->Phone',

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

$event = array();
$tribe_ecp = TribeEvents::instance();
reset($tribe_ecp->metaTags); // Move pointer to beginning of array.
foreach($tribe_ecp->metaTags as $tag){
	$var_name = str_replace('_Event','',$tag);
	$event[$var_name] = tribe_get_event_meta( $post->ID, $tag, true );
}

$event = (object) $event; //Easier to work with.

?>
<li <?php //post_class('clearfix'); ?> class="clearfix">
	<div class="event-image">
		<a href="<?php echo get_permalink($post->ID) ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
	</div>
	<div class="event">
		<a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a>
	</div>
	<div class="when entry-meta">
		<?php
			$space = false;
			$output = '';

			$st_date = date("D M jS", strtotime($event->StartDate));
			echo $st_date . date(" g:ia", strtotime($event->StartDate));

			$en_date = date("D M jS", strtotime($event->EndDate));
			if ( $st_date !== $en_date ) {
			   echo ' - ' . $en_date . date(" g:ia", strtotime($event->EndDate));
			}

			if($event->AllDay) {
				echo ' <small>('.__('All Day','tribe-events-calendar').')</small>';
         }
      ?> 
	</div>
</li>
<?php $alt_text = ( empty( $alt_text ) ) ? 'alt' : ''; ?>
