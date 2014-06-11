// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
    'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
    'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
    'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
    'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

$('a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top
    }, 1000);
    return false;
});

$("a[href='#top']").click(function() {
    $("html, body").animate({ 
        scrollTop: 0 
    }, 1000);
    return false;
});


$(window).load(function() {
    $('#slideshow_header').flexslider({
        animation: "fade",
        animationLoop: true,
        slideshow: true,
        slideshowSpeed: 5000,
        itemMargin: 0
    });

    $('#carousel').flexslider({
        animation: "slide",
        controlNav: true,
        animationLoop: true,
        slideshow: false,
        itemWidth: 210,
        itemMargin: 0
    });

});

$(document).ready(function() {
    $(".fancybox").fancybox({
        padding : 0,
        arrows  : true,
        maxWidth    : 1000,
        maxHeight   : 800,
        helpers : {

            overlay : {
                locked: false,
                css : {
                    'background' : 'rgba(58, 42, 45, 0.85)'
                }
            }
        }
    });

    $(".various").fancybox({
        maxWidth    : 800,
        maxHeight   : 600,
        fitToView   : true,
        autoSize    : true,
        closeClick  : false,
        closeBtn    : true,

        helpers : {

            overlay : {
                locked: false,
                css : {
                    'background' : 'rgba(58, 42, 45, 0.85)'
                }
            }
        }

    });


});

// Place any jQuery/helper plugins in here.





