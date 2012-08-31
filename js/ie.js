//ie7 hacks
jQuery(document).ready(function($){
  if ($.browser.msie && $.browser.version == 7)
  {
    $(".tablecell").wrap("<td />");
    $(".tablerow").wrap("<tr />");
    $(".table").wrapInner("<table />");
	
	$(".menu-parent-item").hover(function(){
		$(this).addClass('hover');
	}, function(){
		$(this).removeClass('hover');
	});
	
  }
});
(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());