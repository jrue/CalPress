<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 * If no widgets are set, then the sidebar will show some defaults
 * like a search form, archives and some meta data.
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
	<div id="sidebar" role="complementary">
<?php
if(is_home()) //only show top sidebar on home page
	dynamic_sidebar( 'top-sidebar' );
	
if (!is_home()) //only show inside sidebar on non home pages
	dynamic_sidebar( 'inside-sidebar');
	
if (is_active_sidebar( 'lower-sidebar' )): //always show lower sidebar
	dynamic_sidebar( 'lower-sidebar' );

//no sidebars active, show default
elseif(!is_active_sidebar( 'lower-sidebar' ) && !is_active_sidebar('inside-sidebar') && (!is_active_sidebar( 'top-sidebar' ) || !is_home())): ?>
		<!-- default sidebar if no widgets present -->
		<ul id="xoxo">
			<li>
				<?php get_search_form(); ?>
			</li>
			<li>
				<h3><?php _e( 'Archives', 'CalPress' ); ?></h3>
				<ul>
<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>
			<li>
				<h3><?php _e( 'Meta', 'CalPress' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>
		</ul>
<?php endif; // end primary widget area ?>
	</div><!-- #sidebar -->