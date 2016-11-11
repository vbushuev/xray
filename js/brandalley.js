$.urlParam = function(name, url) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
    if (results === null){
       return null;
    }
    else{
       return results[1] || 0;
    }
};

String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  for (var i = 0; i < find.length; i++) {
    replaceString = replaceString.replace(find[i], replace[i]);
  }
  return replaceString;
};

function translateAndHide() {

    //$('.addToBag').text('Добавить в корзину');
    // $('.HeaderCheckoutLink').text('Корзина');
    // $('.HeaderBagEmptyMessage').text('Корзина пуста');
    // $('.bagHeader > p:first').text('Моя корзина');
    // $('#aViewBag').text('Перейти в корзину').css('width', '100%');
    // $('#aCheckout').hide();
    // $('.OrderSumm > h2').text('Ваш заказ');
    // var SubtotalLabelText = $('#SubtotalLabel').text().replaceArray(['items','item'], ['шт.', 'шт.']);
    // $('#SubtotalLabel').text(SubtotalLabelText);
    $('.total_command_left span').text('Подитог');
    $('.total_command_left .big_total_text').text('Итого');
    $('input.button_add_cart').val('Добавить в корзину');
}

$(document).on("show.bs.modal", ".modal", function() {

    var popup = $(this);

    if ( popup.hasClass('inscription-private-sale') ) {

        popup.prev().remove();
        popup.remove();

    }

});

var parser = {
    selector:".ContinueOn",
    init:function(){
        // var messageIsShown = garan.cookie.get( "greetings_message" );
        // if ( messageIsShown != "is_shown" ) {
        //     var popup = $( '.bs-overlay.ctshirts-greetings' );
        //     var message = $( '.bs-overlay.ctshirts-greetings .bs-popup-window' );
        //     popup.css( 'display', 'block' );
        //     $( '.start-shopping' ).click( function (e) {
        //         e.preventDefault();
        //         //popup.css( 'display', 'none' );
        //         garan.cookie.set("greetings_message","is_shown");
        //         message.animate({top: "-1000"},{
        //             duration: 400,
        //             complete: function() {
        //                 popup.animate({opacity: "0"},{
        //                     duration: 400,
        //                     complete: function() {popup.css( 'display', 'none' );}
        //                 });
        //             }
        //         });
        //     });
        //
        // }

        translateAndHide();

    },
    styling:function(){
        garan.currency.converter.action({
            replacement:/(\d+\,\d+)\s*€/i,
            //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            selector:".big_total_number, .total_command_right span, .block_price span, .price_total",
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

        $( "#panier-valider" ).unbind( "click" ).click( function(e) {
            e.preventDefault();
            e.stopPropagation();
            parser.checkout();
        }).text( 'Оформить заказ' );
        $( "#panier-valider" ).removeAttr( "href" ).css( 'cursor', 'pointer' );

        //translateAndHide();
    },
    parse:function(){
        var pp = [];
        var row = $("tr.cart-item");
        row.each(function(){
            var $t = $(this);
            if($t.find("td:nth-of-type(1)").length && $t.find("td:nth-of-type(2)").length){
                pp.push({
                    shop:"brandalley.fr",
                    quantity:$t.find("input.quantity").val(),
                    currency:'EUR',
                    original_price:$t.find("td.price_unitaire span.text_extra_big:nth-of-type(1)").text(),
                    title:$.trim($t.find(".title_product").text()),
                    description:$t.find("ss_title_product a").text(),
                    product_img:'http:' + $t.find(".articleItemImg img").attr("src"),
                    product_url:"http://sportsdirect.com"+$t.find(".title_product a").attr("href"),
                    sku:$t.find(".title_product a").attr("href").replace(/\D/g,''),
                    variations:{
                        size:$t.find(".info_product_sup").text(),
                        color:""
                    }
                });
            }
        });
        console.log(pp);
        garan.cart.add2cart(pp);
    },
    checkout:function(){
        if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
        //try {
            garan.cart.removeAll();
            parser.parse();
            garan.cart.checkout();
        //} catch (e) {console.debug(e);}
    },
}
