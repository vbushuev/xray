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
    $('.HeaderCheckoutLink').text('Корзина');
    $('.HeaderBagEmptyMessage').text('Корзина пуста');
    $('.bagHeader > p:first').text('Моя корзина');
    $('#aViewBag').text('Перейти в корзину').css('width', '100%');
    $('#aCheckout').hide();
    $('.OrderSumm > h2').text('Ваш заказ');
    var SubtotalLabelText = $('#SubtotalLabel').text().replaceArray(['items','item'], ['шт.', 'шт.']);
    $('#SubtotalLabel').text(SubtotalLabelText);
    $('.TotalSumm .col-xs-6:nth-child(1) span').text('Итого');

}

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

        advertPopup.config.advertPopupSelector = '';

    },
    styling:function(){
        garan.currency.converter.action({
            replacement:/£(\d+\.\d+)/i,
            //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            selector:"#BasketSummarySubtotalValue, #TotalValue",
            currency:"GBP"
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

        $( ".ContinueOn" ).unbind( "click" ).click( function(e) {
            e.preventDefault();
            e.stopPropagation();
            parser.checkout();
        }).text( 'Оформить заказ' );
        $( ".ContinueOn" ).removeAttr( "href" );

        translateAndHide();
    },
    parse:function(){
        var pp = [];
        $(".AspNet-GridView > table tbody tr").each(function(){
            var $t = $(this);
            if($t.find("td.productimage").length && $t.find("td.productdesc").length){
                pp.push({
                    shop:"sportsdirect.com",
                    quantity:$t.find("td.prdQuantity input").val(),
                    currency:'GBP',
                    original_price:$t.find("td.itemprice .money").text(),
                    title:$.trim($t.find("td.productdesc a.productTitle").text()),
                    description:"",
                    product_img:$t.find("td.productimage > a > img").attr("src"),
                    product_url:"http://sportsdirect.com"+$t.find("td.productdesc a.productTitle").attr("href").replace(/^\./,""),//.replace(/\.xray\.bs2|\.gauzymall\.com/,".at"),
                    sku:decodeURIComponent($.urlParam('colcode', $t.find("td.productdesc a.productTitle").attr("href"))),
                    variations:{
                        size:$t.find("td.productdesc .productsize span:nth-child(2)").text(),
                        color:$t.find("td.productdesc .productcolour span:nth-child(2)").text()
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
