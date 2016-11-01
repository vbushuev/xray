var isMob = /^\/m\/.*$/i;
console.debug("Screen width :"+screen.width+" isMOB:"+isMob.test(document.location.pathname));
if(screen.width<500 && !isMob.test(document.location.pathname)){
    console.debug(document.location.hostname+"/m"+document.location.pathname);
    //document.location.href =document.location.hostname+"/m"+document.location.pathname
}
var parser = {
    selector:"#weiter_btn",
    init:function(){
        var messageIsShown = garan.cookie.get( "greetings_message" );
        if ( messageIsShown != "is_shown" ) {
            var popup = $( '.bs-overlay.ctshirts-greetings' );
            var message = $( '.bs-overlay.ctshirts-greetings .bs-popup-window' );
            popup.css( 'display', 'block' );
            $( '.start-shopping' ).click( function (e) {
                e.preventDefault();
                //popup.css( 'display', 'none' );
                garan.cookie.set("greetings_message","is_shown");
                message.animate({top: "-1000"},{
                    duration: 400,
                    complete: function() {
                        popup.animate({opacity: "0"},{
                            duration: 400,
                            complete: function() {popup.css( 'display', 'none' );}
                        });
                    }
                });
            });

        }
    },
    styling:function(){
        garan.currency.converter.action({
            replacement:/(\d+\,\d+)\s*€/i,
            //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            selector:".colSummary",
            currency:"EUR"
        });
        if (window!=window.top) return;
        $("body").css("padding-top","50px");
        $(".sec.service").hide();
        $(".m-CheckoutStep1ButtonPanel-paypalExpressLink").hide();
        $(".m-CheckoutStep1ButtonPanel-buttonChoiceSeparator").hide();
        $("tr.basketGiftcard,tr.basketShippingCost").hide();
        $("body > div.pageborder > div > div.bottomBoxFull > div > div > div.linkLists > div.container.withBg.inner.payments").hide();
        $("body > div.pageborder > div > div.headerWrapper > div.l-header.js-header > div.l-header-benefit").hide();
        $("body > div.pageborder > div > div.headerWrapper > div.l-header.js-header > div.l-header-main > div > div.l-header-meta > div.m-metanav").hide();
        $("#footerInternationalBox,#footerLinksBox").hide();
        $("td.colOption > a.addProductToWatchlist.onlyClickOnce.notLoggedIn").hide();
        $("td.colDescription > div > div.ath_DELIVERY_TIME").hide();
        $("#basketForm > section > div.m-delivery-list-basket--wide > div > a").hide();

        $(".orderSubmitButton").each(function(){
            var btn = $(this).find(parser.selector).clone();
            btn.find("span span input").val("Оформить заказ")
                //.replaceWith('<a class="g-baby-walz-checkout" href="javascript:parser.checkout();"><i class="fa fa-shopping-cart"></i> Оформить заказ</a>')
                //.attr("onClick","")
                .unbind("click").click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    parser.checkout();
            });//.find("span").text("Оформить заказ");
            $(this).find(parser.selector).replaceWith(btn);
        });
    },
    parse:function(){
        var pp = [];
        $("#basketForm > section > table tbody tr:not(.basketSummary,.orderstarter)").each(function(){
            var $t = $(this);
            if($t.find("td.colImage").length && $t.find("td.colDescription a.productName").length){
                pp.push({
                    shop:"eduscho.at",
                    quantity:$t.find("td.colQuantity").text().replace(/\D+/,""),
                    currency:'EUR',
                    original_price:$t.find("td.colPrice .currentPriceTotal").text(),
                    title:$t.find("td.colDescription > a").text(),
                    description:"",
                    product_img:$t.find("td.colImage > a > img").attr("src"),
                    product_url:"http://www.eduscho.at"+$t.find("td.colDescription a.productName").attr("href").replace(/^\./,""),//.replace(/\.xray\.bs2|\.gauzymall\.com/,".at"),
                    sku:$t.find("td.colDescription > a").attr("href").replace(/.+\-([a-z0-9]+)?(\.html|\?).*/,"$1"),
                    variations:{
                        size:$t.find("td.colDescription > div > div:nth-child(1) > span").text(),
                        color:$t.find("#id3fc > tr:nth-child(3) > td.colDescription > div > div:nth-child(2):not(.ath_DELIVERY_TIME)").text()
                    }
                });
            }
        });
        console.log(pp);
        garan.cart.add2cart(pp);
    },
    checkout:function(){
        if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
        try {
            garan.cart.removeAll();
            parser.parse();
            garan.cart.checkout();
        } catch (e) {console.debug(e);}
    },
}
