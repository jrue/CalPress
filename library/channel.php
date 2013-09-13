<?php
/** 
 * CalPress Facebook Channel File
 *
 * Facebook needs a Channel file to address cross-domain issues on certain browsers
 * @see https://developers.facebook.com/docs/javascript/gettingstarted/#channel
 * 
 * CalPress is a project of the University of California 
 * Berkeley Graduate School of Journalism
 * http://journalism.berkeley.edu
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

 $cache_expire = 60*60*24*365;
 header("Pragma: public");
 header("Cache-Control: max-age=".$cache_expire);
 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
 ?>
 <script src="//connect.facebook.net/en_US/all.js"></script>