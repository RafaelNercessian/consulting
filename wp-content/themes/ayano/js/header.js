jQuery(document).ready(function ($) {
    window.addEventListener('scroll', function () {
        var header = $('#masthead');
        var scrollPosition = window.pageYOffset;
        if (scrollPosition > 100) {
            header.addClass('shrink');
        } else {
            header.removeClass('shrink');
        }
    });
})