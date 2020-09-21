
(function($){
  "use strict";

    // Toggle menu
     $(".navbar-toggle").click(function() {
        $(this).toggleClass('in');
    });


    /*** Sticky header */
    $(window).scroll(function() {

        if ($(window).scrollTop() > 0) {
          $(".header").addClass("sticky");
        } 
        else {
          $(".header").removeClass("sticky");
        }
    });


    /*** Header height = gutter height */

    function setGutterHeight(){
        var header = document.querySelector('.navbar'),
            gutter = document.querySelector('.header_gutter');
            gutter.style.height = header.offsetHeight + 'px';
    }

    window.onload = setGutterHeight;
    window.onresize = setGutterHeight;


    /** Case Study Slider **/ 
    $(".banner").slick({
        dots: true,
        infinite: true,
        slidesToShow: 1,
        arrows: false
    });

    /** Testimonials Slider **/ 
    $(".testimonial-quote-slider").slick({
        dots: true,
        infinite: true,
        arrows: false,
        asNavFor: '.testimonial-image-slider'
    });

    $('.testimonial-image-slider').slick({
        dots: false,
        infinite: true,
        arrows: false,
        vertical: true,
        centerMode: true,
        asNavFor: '.testimonial-quote-slider'
    });

    // initialize works mixitup 
    function photoInitiateMixItUp(){
        var container = $('.portfolios'),
            mobileFilter = $('#FilterSelect');

        container.mixItUp();

        // filter for mobile
        mobileFilter.on('change', function(){
            container.mixItUp('filter', this.value);
        });
    }

    photoInitiateMixItUp();
    
    $('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] 
        },
        callbacks: {
            beforeOpen: function() {
              // just a hack that adds mfp-anim class to markup 
               this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
               this.st.mainClass = this.st.el.attr('data-effect');
            }
          },
          closeOnContentClick: true,
          midClick: true, 
		image: {
		}
    });

})(jQuery);