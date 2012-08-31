CalPress 2.0 Theme for WordPress (BETA)
========

A custom WordPress theme for small news publications, built by the UC Berkeley Graduate School of Journalism. 

**This theme is in beta, and documentation will be added as development continues.**

* Theme Name: CalPress 2.0
* Author: [Jeremy Rue](http://journalism.berkeley.edu/faculty/jrue/)
* Website: [http://calpresstheme.org/](http://calpresstheme.org/)
* Employer: [http://journalism.berkeley.edu](http://journalism.berkeley.edu)
* Description: A journalism news theme designed for small publishing operations. Includes support for news-driven front page layouts, advertising widgets, flexible post options, multimedia integration and more.Based on CalPress 0.9 by [Josh Williams](http://joshwilliams.com/), [HTML5 Boilerplate](http://html5boilerplate.com/) and TwentyEleven. This theme is built for the Graduate School of Journalism at the University of California, Berkeley. 
* Copyright (c) 2012 The Regents of the University of California.
* License: [GNU General Public License v2.0](http://www.gnu.org/licenses/gpl-2.0.html)
* Version: 0.9.7
* Tags: uc berkeley, berkeley, newspapers, news, news theme, journalism, journalism theme

## Installation and Usage
Download into your themes folder and activate. If you are upgrading from an earlier version of [CalPress](https://code.google.com/p/calpresstheme/), you need to turn on legacy support in `CalPress Options` to support posts with older post metadata from custom fields.

**Note to users upgrading existing sites:** This theme comes built with special image size handling. When a requested image size doesn't exist, it automatically generates all the news sizes it will ever need, and saves them in the uploads folder. This may cause sluggishness for viewing older posts for the first time as new images are generated. If you run a high traffic site, it is recommended that you use a plugin like [regenerate images](http://wordpress.org/extend/plugins/regenerate-thumbnails/) first, or simply page through older archive pages.

## Plugins
This theme uses features from the following plugins. Installation of these plugins is optional, but there are various functions that are used to make use of various features.

1. [Simple Facebook Connect](http://wordpress.org/extend/plugins/simple-facebook-connect/) We take advantage of the open graph features, and extend them if this plugin is present. This allows images, Vimeo videos and YouTube videos to appear in Facebook and Twitter posts from articles.

2. [The Events Calendar](http://wordpress.org/extend/plugins/the-events-calendar/) We offer a customized widget and some styles when using this plugin.

3. [Co-Authors Plus](http://wordpress.org/extend/plugins/co-authors-plus/) This is fully integrated with co-authors plus to allow multiple authors associated with posts. If not present, it will default to standard WordPress implementation.