$(document).ready(function(){

    $('.faq h4').click(function(){
        $(this).next('p').slideToggle();
    });

     $('.bxslider').bxSlider({
         slideHeight: 340,
         auto: true
     });

});
