jQuery.noConflict();
(function($) {
    window.parser = {
        selector:"#DisplayBasketForm .btn_toCheckout",
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
                selector:".productSum, .total > .sum",
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

            //$(".orderSubmitButton").each(function(){
                var btn = $(parser.selector).clone();
                btn.text("Оформить заказ")
                    //.replaceWith('<a class="g-baby-walz-checkout" href="javascript:parser.checkout();"><i class="fa fa-shopping-cart"></i> Оформить заказ</a>')
                    //.attr("onClick","")
                    .unbind("click").click(function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        parser.checkout();
                });//.find("span").text("Оформить заказ");
                $(parser.selector).replaceWith(btn);
            //});
        },
        parse:function(){
            var pp = [];
            $("#DisplayBasketForm > div > div.productList > div.data-panel-full").each(function(){
                var $t = $(this);
                //if($t.find("td.colImage").length && $t.find("td.colDescription a.productName").length){
                    pp.push({
                        shop:"ernsting-family.at",
                        quantity:$t.find(".productAmount > select").val().replace(/\D+/,""),
                        currency:'EUR',
                        original_price:$t.find(".productPrice p").text(),
                        title:$t.find(".productInfo .productLink .articleName").text().trim(),
                        description:"",
                        product_img:"http:"+$t.find(".productImageLink img").attr("src"),
                        product_url:"http://www.ernsting-family.at/"+$t.find(".productInfo .productLink").attr("href").replace(/^\./,""),//.replace(/\.xray\.bs2|\.gauzymall\.com/,".at"),
                        sku:$t.find(".productSize > select").val(),
                        variations:{
                            size:$t.find(".productSize > select option:selected").text(),
                            color:$t.find(".productInfo > p").text().trim()
                        }
                    });
                //}
            });
            //console.log(pp);
            garan.cart.add2cart(pp);
        },
        checkout:function(){
            if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
            //try {
                garan.cart.removeAll();
                parser.parse();
                console.debug(garan.cart);
                garan.cart.checkout();
            //} catch (e) {console.debug(e);}
        },
    }
})(jQuery);
