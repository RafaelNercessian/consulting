jQuery(document).ready(function ($) {
    $('.blockfirstsectionheader__div svg').on('click', function (){
        $('.blockfirstsectionheader__section--menu').addClass('show');
        $('.blockfirstsectionheader__section--menu--overlay').addClass('show');
    })
    $('.blockfirstsectionheader__section--menu svg,.blockfirstsectionheader__section--menu--overlay').on('click', function (){
        $('.blockfirstsectionheader__section--menu').removeClass('show');
        $('.blockfirstsectionheader__section--menu--overlay').removeClass('show');
    })
})