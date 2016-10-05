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

var $ = jQuery.noConflict();
$(document).ready(function() {
    garan.cart.init();
    $("#garan24-toper").delay(800).fadeIn();
    console.debug(document.location.hostname.split(/\./)[0].replace(/[\-]/,""));

    if(typeof parser!="undefined"){
        if(typeof parser.styling != "undefined"){
            parser.styling();
        }
        if($(parser.selector).length){
            $(parser.selector).hide();
            parser.parse();
            $(".garan-checkout").removeClass("garan24-button-disabled").addClass("garan24-button-pulse");
        }
    }

    $("#garan-cart").on("click",function(){
        var $c = $("#garan-cart-full");
        if($c.hasClass("garan24-visible")){
            $c.removeClass("garan24-visible").slideUp();
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
    $(".garan-checkout").on("click",function(){
        garan.cart.checkout()
    });
});
