jQuery.noConflict();
(function($) {
    $(document).ready(function($) {
        garan.cart.init();
        garan.currency.get();
        if (window == window.top){
            $(".gauzymall-fixed-navbar:first").show();
            $(document).trigger('xray:showLine');
        }
        //var current_host = document.location.hostname.split(/\./)[0];
        //console.debug("current multiHost = " + current_host);
        // init multi cart

        // control multi cart

        $('.navbar-brand').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#choose-your-way-section)').fadeOut();
            $('#choose-your-way-section').fadeToggle();
        });

        $('.shipping').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#shipping-section)').fadeOut();
            $('#shipping-section').fadeToggle();
        });
        $('.payment').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#payment-section)').fadeOut();
            $('#payment-section').fadeToggle();
        });
        $('.how-to-buy').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#how-to-buy-section)').fadeOut();
            $('#how-to-buy-section').fadeToggle();
        });
        $('.about-us').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#about-us-section)').fadeOut();
            $('#about-us-section').fadeToggle();
        });
        $('.promo').click(function(e) {
            e.preventDefault();
            $('.bs-overlay:not(#promo-section)').fadeOut();
            $('#promo-section').fadeToggle();
        });
        $('.bs-popup-close').click(function() {
            $('.bs-overlay').fadeOut();
        });
        $( '.animated-attention' ).click( function(e) {
            $( this ).removeClass( 'animated-attention' );
            $( '.animated-attention-circle-wrapper' ).hide();
            $( '.animated-attention-circle' ).hide();
        });

        /* END */

        //Styling of original shop
        if (typeof parser != "undefined") {
            if (typeof parser.init != "undefined") parser.init();
            if (typeof parser.styling != "undefined") parser.styling();
            $(document).ajaxComplete(function(event, xhr, settings) {
                parser.styling();
            });
            garan.cart.update = function() {
                try {
                    garan.cart.removeAll();
                    parser.parse();
                } catch (e) {
                    console.error(e);
                }
            };
            var bodyNode = $('body');
            //bodyNode.append('<script src="/js/jquery-2.2.4.min.js"></script>');
            //bodyNode.append('<script src="/js/bootstrap.min.js"></script>');
            //bodyNode.append('<script>var $jq1 = jQuery.noConflict(true);</script>');
        }
        // Google Analytics
        try {ga('send', 'event', 'events', 'visit', 'visit', 1, false);} catch (e) {console.warn("no ga");console.error(e);}

        $(document).bind("gcart:beforeCheckout",function(e,o){
            console.debug("beforeCheckout triggered.");
            console.debug(o);
        });
        //$(".translate").click(function(){autoTranslate();});
        if(garan.cookie.get("googtrans","no")=="no"){garan.cookie.set("googtrans","/de/ru");document.location.reload();}
    });
    window.autoTranslate = function(){
        console.debug("Google code "+$("#google_translate_element select > option:nth-child(2)").text());
        //$("#google_translate_element select > option:nth-child(2)").attr("selected","selected");
        $("#google_translate_element select").val('ru').change();
        //$("#google_translate_element select > option:nth-child(2)").click();
        garan.cookie.set("googtrans","/de/ru");
        document.location.reload();
    }
})(jQuery);

/*
function globalAdd2Cart(){
    var e = arguments[0],
        p = arguments[1],
        i = arguments[2];
    if(typeof i != "undefined"){
        i.clone()
        .css({'position' : 'fixed', 'z-index' : '999'})
        .appendTo(i)
        .animate(
            {
                opacity: 0.5,
                top: 0,
                left:$("#garan24-cart-quantity").offset().left,
                width: 50,
                height: 50
            },
            800,function() {$(this).remove();
        });
    }
    garan.cart.add2cart(p);
    e.preventDefault();
    e.stopPropagation();
    return false;
}
*/
/*
    $("#garan-cart").click(function(){
        var $c = $("#garan-cart-full");
        if($c.hasClass("garan24-visible")){
            $c.removeClass("garan24-visible").fadeOut();
            $("#garan24-overlay").fadeOut();
            //$("#garan24-overlay #garan24-overlay-message").delay(300).show();
            return;
        }
        $("#garan24-overlay #garan24-overlay-message").hide();
        $("#garan24-overlay").fadeIn().on("mouseover",function(){
            $("#garan-cart").click();
            $(this).unbind("mouseover");
        });
        $c.addClass("garan24-visible").slideDown();
    });
    $("#add-to-cart").click(function(){
        if(typeof ga!="undefined")ga('send','event', 'events', 'add2cart', 'add2cart',2, false);else console.debug("no ga!!! add2cart");
    });
    $(".garan-checkout").click(function(){
        if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
    });
*/
