<?php
/**
 * Sidebar footer. This isn't really a sidebar, rather we're using
 * WordPress' built-in sidebar template to display widgets in the 
 * footer. (footer.php calls this file.) You can change these
 * by going to Appearance -> Widgets in the Dashboard.
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
		<div id="footer-col-1" class="footer-column">
			
			<?php dynamic_sidebar('footer-column-1'); ?>
			
		</div>
		<div id="footer-col-2" class="footer-column">
			
			<?php dynamic_sidebar('footer-column-2'); ?>
			
		</div>
		<div id="footer-col-3" class="footer-column">
			
			<?php dynamic_sidebar('footer-column-3'); ?>
			
		</div>
