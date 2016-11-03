jQuery.noConflict();
(function($) {
    window.parser = {
        selector:"#btn_checkout",
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
                replacement:/€(\d+\,\d+)/i,
                //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
                selector:".subtotals",
                currency:"EUR"
            });

            $("#divGlobalContainer").hide();

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
        },
        parse:function(){
            var pp = [];
            $("#itemlist .itemlist").each(function(){
                var $t = $(this);
                var p = {
                    shop:"forever21.com",
                    quantity:$t.find(".ck_qty_ttl .ck_qty_count").text().replace(/\D+/,""),
                    currency:'EUR',
                    original_price:$t.find(".subtotals").text().replace(/[^\d\.\,]/,""),
                    title:$t.find(".s_itemname > h1").text().trim(),
                    description:"",
                    product_img:"http://www."+$t.find(".ck_s_itempic > a img").attr("src").replace(/(\.gauzymall\.com|\.xray\.bs2)/,".com").replace(/^\/\//,""),
                    product_url:"http://www.forever21.com/"+$t.find(".ck_s_itempic > a").attr("href"),
                    sku:$t.find(".ck_s_itempic > a").attr("href").replace(/productid=(\d+)/ig,"$1"),
                    variations:{
                        color:$t.find(".n_ck_s_itemoption_list ul:nth-child(1) > li:nth-child(2)").text().trim(),
                        size:$t.find(".n_ck_s_itemoption_list  ul:nth-child(2) > li:nth-child(2)").text().trim(),
                    }
                };
                p.original_price = parseFloat(p.original_price)/parseInt(p.quantity);
                pp.push(p);
            });
            console.log(pp);
            garan.cart.add2cart(pp);
        },
        checkout:function(){
            if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
            //try {
                garan.cart.removeAll();
                parser.parse();
                //console.debug(garan.cart);
                garan.cart.checkout();
            //} catch (e) {console.debug(e);}
        },
    }
})(jQuery);
$=jQuery.noConflict();
