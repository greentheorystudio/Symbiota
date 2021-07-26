$(document).ready(function() {
    var contentSections = $('.cd-section');
    var navigationItems = $('#cd-vertical-nav a');

    updateNavigation();

    document.addEventListener('scroll', function (event) {
        updateNavigation();
    }, true);

    navigationItems.on('click', function(event){
        event.preventDefault();
        smoothScroll(this.hash);
    });

    $('.touch #cd-vertical-nav a').on('click', function(){
        $('.touch #cd-vertical-nav').removeClass('open');
    });

    function updateNavigation() {
        contentSections.each(function(){
            $this = $(this);
            var activeSection = $('#cd-vertical-nav a[href="#'+$this.attr('id')+'"]').data('number') - 1;
            if ( ( $this.offset().top - $(window).height()/2 < $(window).scrollTop() ) && ( $this.offset().top + $this.height() - $(window).height()/2 > $(window).scrollTop() ) ) {
                navigationItems.eq(activeSection).addClass('is-selected');
            }else {
                navigationItems.eq(activeSection).removeClass('is-selected');
            }
        });
    }

    function smoothScroll(hash) {
        var element = document.querySelector(hash);
        element.scrollIntoView({ behavior: 'smooth', block: 'start'});
    }
});
