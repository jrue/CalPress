/*	Site-specific scripts. This gets loaded in the footer. */

//allows Date.parse of ISO8601 strings https://github.com/csnover/js-iso8601
(function(n,f){var u=n.parse,c=[1,4,5,6,7,10,11];n.parse=function(t){var i,o,a=0;if(o=/^(\d{4}|[+\-]\d{6})(?:-(\d{2})(?:-(\d{2}))?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d{3}))?)?(?:(Z)|([+\-])(\d{2})(?::(\d{2}))?)?)?$/.exec(t)){for(var v=0,r;r=c[v];++v)o[r]=+o[r]||0;o[2]=(+o[2]||1)-1,o[3]=+o[3]||1,o[8]!=="Z"&&o[9]!==f&&(a=o[10]*60+o[11],o[9]==="+"&&(a=0-a)),i=n.UTC(o[1],o[2],o[3],o[4],o[5]+a,o[6],o[7])}else i=u?u(t):NaN;return i}})(Date)

//relative time, based on work by Levani Melikishvili / wp123.info
function calpress_relativetime(a,b){b=typeof b==="undefined"?0:b;if(a>1e12){a=a/1e3}var c=Math.round((new Date).getTime()/1e3)-a;if(c<=b||b==0){if(c>=60*60*24*365){var d=parseInt(c/(60*60*24*365));var e=d>1?"s":"";var f=d+" year"+e+" ago"}else if(c>=60*60*24*7*5){var d=parseInt(c/(60*60*24*30));var e=d>1?"s":"";var f=d+" month"+e+" ago"}else if(c>=60*60*24*7){var d=parseInt(c/(60*60*24*7));var e=d>1?"s":"";var f=d+" week"+e+" ago"}else if(c>=60*60*24){var d=parseInt(c/(60*60*24));if(d==1){var f="Yesterday"}else{var f=d+" days ago"}}else if(c>=60*60){var d=parseInt(c/(60*60));var e=d>1?"s":"";var f=d+" hour"+e+" ago"}else if(c>=60){var d=parseInt(c/60);var e=d>1?"s":"";var f=d+" minute"+e+" ago"}else{var f="moments ago"}return f}else{return false}}

/*global jQuery */
/*!
* FitVids 1.0
*
* Copyright 2011, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
*
* Date: Thu Sept 01 18:00:00 2011 -0500
*/

(function( $ ){

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null
    }

    var div = document.createElement('div'),
        ref = document.getElementsByTagName('base')[0] || document.getElementsByTagName('script')[0];

    div.className = 'fit-vids-style';
    div.innerHTML = '&shy;<style>         \
      .fluid-width-video-wrapper {        \
         width: 100%;                     \
         position: relative;              \
         padding: 0;                      \
      }                                   \
                                          \
      .fluid-width-video-wrapper iframe,  \
      .fluid-width-video-wrapper object,  \
      .fluid-width-video-wrapper embed {  \
         position: absolute;              \
         top: 0;                          \
         left: 0;                         \
         width: 100%;                     \
         height: 100%;                    \
      }                                   \
    </style>';

    ref.parentNode.insertBefore(div,ref);

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        "iframe[src*='player.vimeo.com']",
        "iframe[src*='www.youtube.com']",
        "iframe[src*='www.kickstarter.com']",
        "object",
        "embed"
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var $allVideos = $(this).find(selectors.join(','));

      $allVideos.each(function(){
        var $this = $(this);
        if (this.tagName.toLowerCase() == 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
        var height = ( this.tagName.toLowerCase() == 'object' || $this.attr('height') ) ? $this.attr('height') : $this.height(),
            width = $this.attr('width') ? $this.attr('width') : $this.width(),
            aspectRatio = height / width;
        if(!$this.attr('id')){
          var videoID = 'fitvid' + Math.floor(Math.random()*999999);
          $this.attr('id', videoID);
        }
        $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+"%");
        $this.removeAttr('height').removeAttr('width');
      });
    });
  }
})( jQuery );

/* FEATURED POSTS WIDGET SLIDER */
jQuery(document).ready(function($){
	
	//Event's widget title goes to event's page
	$('.eventsListWidget h2.widgettitle').click(function(){
		window.location ='/events/';
	});
	
	$("#toggle-nav-menu").click(function(){
		$('#primary-navigation ul li.menu-item').toggleClass('showNavContent');
		return false;
	});
	
	//change time tags to display relative time if less than 5 hours
	$("time").each(function(){
		var datestring = calpress_relativetime(Date.parse($(this).attr('datetime')), 18000);
		if(datestring){
			$(this).html(datestring);
		}
	});	
});