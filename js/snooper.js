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
    //$("#garan-cart").click();
    if(typeof parser!="undefined"){
        if($(parser.selector).length){
            //$("#add2cart-place").html('');
            $("#add2multicart").removeClass("garan24-button-disabled");
            $("#add2multicart").animate({
                transition: 'all 1s ease-in-out',
                transform:  'scale(1.05,1.05)'
            }, 800, function() {
                console.debug('Pulsed...');
              $("#add2multicart").animate({
                transition: 'all 1s ease-in-out',
                transform:  'scale(1,1)'
            }, 800, function() {});
            });
            //$("#add2multicart").animate({transform: 'scale(1.1, 1.1)'},800,function(){$(this).animate({transform: 'scale(1, 1)'},800,function(){});});
            $(parser.selector).hide();
        }
        /*
        $(parser.selector)
            .replaceWith('<a href="javascript:parser.parse();" class="garan24-button garan24-button-primary"><i class="fa fa-cart"></i> Добавить в мультикорзину</a>')
            .click(function(e){
                parser.parse;

            });*/
        if(typeof parser.styling != "undefined"){
            parser.styling();
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
        $("#garan24-overlay").fadeIn();
        $c.addClass("garan24-visible").slideDown();
    });
    $("#garan-checkout").on("click",function(){
        garan.cart.checkout()
    });
});
