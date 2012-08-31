<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=container div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * @uses wp_footer()
 * @uses calpress_hook_colophon()
 * @uses calpress_hook_above_footer()
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */
?>
	<?php calpress_hook_above_footer(); ?>	
	</div><!-- #container -->
	<footer role="contentinfo" class="clearfix" id="main-footer">
		<div id="footer-widgets">
<?php get_sidebar( 'footer' ); ?>
		</div>
		<div id="colophon">
			
<?php calpress_hook_colophon(); ?>
			
		</div>
	</footer><!-- footer -->
<?php wp_footer(); ?>
</body>
</html>
<?php calpress_hook_after_body(); ?>