/*	Site-specific scripts. This gets loaded in the footer. */

//allows Date.parse of ISO8601 strings https://github.com/csnover/js-iso8601
(function(n,f){var u=n.parse,c=[1,4,5,6,7,10,11];n.parse=function(t){var i,o,a=0;if(o=/^(\d{4}|[+\-]\d{6})(?:-(\d{2})(?:-(\d{2}))?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d{3}))?)?(?:(Z)|([+\-])(\d{2})(?::(\d{2}))?)?)?$/.exec(t)){for(var v=0,r;r=c[v];++v)o[r]=+o[r]||0;o[2]=(+o[2]||1)-1,o[3]=+o[3]||1,o[8]!=="Z"&&o[9]!==f&&(a=o[10]*60+o[11],o[9]==="+"&&(a=0-a)),i=n.UTC(o[1],o[2],o[3],o[4],o[5]+a,o[6],o[7])}else i=u?u(t):NaN;return i}})(Date)

//relative time, based on work by Levani Melikishvili / wp123.info
function calpress_relativetime(a,b){b=typeof b==="undefined"?0:b;if(a>1e12){a=a/1e3}var c=Math.round((new Date).getTime()/1e3)-a;if(c<=b||b==0){if(c>=60*60*24*365){var d=parseInt(c/(60*60*24*365));var e=d>1?"s":"";var f=d+" year"+e+" ago"}else if(c>=60*60*24*7*5){var d=parseInt(c/(60*60*24*30));var e=d>1?"s":"";var f=d+" month"+e+" ago"}else if(c>=60*60*24*7){var d=parseInt(c/(60*60*24*7));var e=d>1?"s":"";var f=d+" week"+e+" ago"}else if(c>=60*60*24){var d=parseInt(c/(60*60*24));if(d==1){var f="Yesterday"}else{var f=d+" days ago"}}else if(c>=60*60){var d=parseInt(c/(60*60));var e=d>1?"s":"";var f=d+" hour"+e+" ago"}else if(c>=60){var d=parseInt(c/60);var e=d>1?"s":"";var f=d+" minute"+e+" ago"}else{var f="moments ago"}return f}else{return false}}

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