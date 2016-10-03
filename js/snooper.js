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
    //$("#usp_bar, .meta, .headBasket, .footerBox, #headSearch").hide();
    //$("#header > ul, #header > div.input-box.input-box--pushed.desktop-only.input-box--silent").hide();
    console.debug(document.location.hostname.split(/\./)[0].replace(/[\-]/,""));
    if(typeof parser!="undefined"){
        $(parser.selector)
            .replaceWith('<a href="javascript:parser.parse();" class="garan24-button"><i class="fa fa-cart"></i> Добавить в мультикорзину</a>')
            .click(function(e){
                e.preventDefault;
                parser.parse;
            });
        if(typeof parser.styling != "undefined"){
            parser.styling();
        }
    }

    $("#garan-cart").on("click",function(){
        var $c = $("#garan-cart-full");
        if($c.hasClass("garan24-visible")){
            $c.removeClass("garan24-visible").slideUp();
            return;
        }
        $c.addClass("garan24-visible").slideDown();
    });
    $("#garan-checkout").on("click",function(){
        garan.cart.checkout()
    });
});
