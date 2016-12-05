
jQuery.noConflict();
(function($) {
    console.clear = function(){console.debug("console was cleared by ...");}
    var old_alarm = window.alert;
    window.alert = function (s){
        console.debug("alert called");
        //old_alarm(s);
        console.debug("alert called");
    }

    window.parser = {
        selector:".b-checkout_button",
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
            var btn = $(parser.selector);
            btn.unbind("click").click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    parser.checkout();
            }).text("Оформить заказ").css("font-size","12pt");
            //$(parser.selector).replaceWith(btn);
        },
        styling:function(){
            garan.currency.converter.action({
                replacement:/€(\d+\,\d+)/i,
                //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
                selector:".subtotals,.total_price",
                currency:"EUR"
            });
            $("body,header,main,#main").css("margin-top","50px");
            $(".fancybox-overlay-fixed").css("top","40px");
            //$("body").css("margin-top","50px");

            $("header > div.b-header_main-top > div > div.b-header_main-content > ul > li.l-header_service_menu-item.js-flyout-container.js-login_dropdown-container").hide();
            $("header > div.b-header-promo_box").hide();
            $("#pdp-floating,.promoct,.b-checkout_progress_indicator").hide();
            $(".b-login_dropdown").hide();
            $(".b-checkout_content_block").hide();
            $(".b-summary_list-line.b-summary_list-shipping").hide();
            $("#p-cart > main > div > div.l-checkout_cart-left > div.b-cart_table > div.b-cart_table-list > div.b-cart_table-line_body > div:nth-child(2) > div.b-cart_table-cols.b-cart_table-body_col_product > div.b-cart_table-body_col_product-user_actions").hide();
            $(".js-first-visit-banner.b-first_visit_banner").hide();
            $(".b-language_selector").hide();
            $(".b-cart_table-body_col_qty-item_quantity-minus,.b-cart_table-body_col_qty-item_quantity-plus").hide();
            $("#p-cart > main > div > div.l-checkout_cart-left > div.l-benefeet_loyalty.js-benefeet_loyalty").hide();
            $("#p-cart > main > div > div.l-checkout_cart-left > div.b-cart_table > div.b-cart_order_total").hide();
            $("#p-cart > main > div > div.l-checkout_cart-left > h3,#p-cart > main > div > div.l-checkout_cart-left > div.b-cart_coupon_code,#p-cart > main > div > div.l-checkout_cart-left > div.l-benefeet_loyalty.js-benefeet_loyalty,#shippingAnchor,#p-cart > main > div > div.l-checkout_cart-left > div.b-cart_payment_method").hide();


            // correct functions

            /**/

        },
        parse:function(){
            var pp = [];
            $("#p-cart  .b-cart_table-line_body").each(function(){
                var $t = $(this);
                var p = {
                    shop:"geox.com",
                    quantity:$t.find(".b-cart_table-body_col_qty > div > input").val().replace(/\D+/,""),
                    currency:'EUR',
                    original_price:$t.find(".b-cart_table-body_col_total_price .b-cart_table-body_col_total_price-item_total_price-value:last").text().trim()
                    //original_price:$t.find(".b-cart_table-cols.b-cart_table-body_col_total_price-item_total_price .b-cart_table-body_col_total_price-item_total_price-value").text().trim()

                        .replace(/^00/,"").replace(/[^\d\.\,]/,""),
                    title:$t.find(".b-cart_table-body_col_product-product_name-link").text().trim(),
                    description:"",
                    product_img:$t.find(".b-cart_table-body_col_image-image img").attr("src"),
                    product_url:$t.find(".b-cart_table-body_col_product-product_name-link").attr("href").replace(/^\/\//,"http://"),
                    sku:$t.find(".b-cart_table-body_col_product-sku-value").text().trim(),
                    variations:{
                        color:$t.find(".b-cart_table-body_col_product-attribute.m-color .b-cart_table-body_col_product-attribute-value").text().trim(),
                        size:$t.find(".b-cart_table-body_col_product-attribute.m-size .b-cart_table-body_col_product-attribute-value").text().trim(),
                    }
                };
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
