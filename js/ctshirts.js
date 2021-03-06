function openPopup ( name ) {

    var popup = $( '#' + name );

    $( 'body' ).css( 'overflow', 'hidden' );

    popup.show();

    var link = 'http://' + window.location.hostname + '/' + name;

    History.pushState( null, null, link );

    console.log( "popup opened" );

}

function closePopup ( name ) {

    var popup = $( '#' + name );

    popup.hide();

    $( 'body' ).css( 'overflow', 'auto' );

    History.back();

}

function closeOpenedPopups () {

    var popups = $( '.bs-overlay' );

    $.each( popups, function( key, value ) {

        if ( $( this ).css( 'display' ) == 'block' ) {

            var popupName = $( this ).attr( 'id' ).replace(/\#/g, '');

            closePopup( popupName );

        }

    });

}

//ctshirts
var parser = {
    loaded:false,
    init:function() {

        $("#add-to-cart").on("click",function(e){
            $.ajax({
                //url:"//service.garan24.bs2/analytics",
                url:"//l.gauzymall.com/analytics",
                success:function(d){
                    console.log(d);
                }
            });
        });
        // $( '.choose-your-way' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#choose-your-way-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });
        //
        // $( '.shipping' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#shipping-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });
        //
        // $( '.payment' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#payment-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });
        //
        // $( '.how-to-buy' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#how-to-buy-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });
        //
        // $( '.about-us' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#about-us-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });

        // $( '.promo' ).click( function (e) {
        //
        //     e.preventDefault();
        //
        //     History.pushState( null, null, "#promo-section" );
        //
        //     $( '.bs-overlay' ).hide();
        //
        //     $( '#promo-section' ).show();
        //
        //     $( 'body' ).css( 'overflow', 'hidden' );
        //
        // });

        $( '.gr' ).click( function (e) {

            e.preventDefault();

            $( '.bs-overlay' ).hide();

            $( '#gr-section' ).show();

            $( 'body' ).css( 'overflow', 'hidden' );

        });

        $( '.animated-attention' ).click( function(e) {

            $( this ).removeClass( 'animated-attention' );

            $( '.animated-attention-circle-wrapper' ).hide();
            $( '.animated-attention-circle' ).hide();

        });

        var currentUrl = window.location.href;

        // SIZES
        // BEGIN

        if ( currentUrl.indexOf( 'shirt' ) !== -1 ) {

            var sizesLinkContainer = $( '.pdp-main__size-guide' );

            sizesLinkContainer.html( '<a class="pdp-main__size-guide-link formal-shirts-sizes bs-popup-open" id="formal-shirts-sizes-button">Руководство по размерам</a>' );

        }

        // END

        $( '.bs-popup-open' ).on( 'click', function(e) {

            e.preventDefault();

            closeOpenedPopups();

            var hash = $( this ).attr( 'id' ).replace( /\-button/g, '-section' );

            if ( typeof hash != 'undefined' && hash !== null ) {

                openPopup( hash );

            }

        });

        $( '.bs-popup-close' ).on( 'click', function(e) {

            e.preventDefault();

            var hash = $( this ).closest( '.bs-overlay' ).attr( 'id' ).replace(/\#/g, '');

            console.log( "popup closed" );

            closePopup( hash );

            //$( '.bs-overlay' ).hide();

        });

        $( '.close-popup' ).click( function(e) {

            e.preventDefault();

            History.back();

            $( '.bs-overlay' ).hide();

            $( 'body' ).css( 'overflow', 'auto' );

        });

        var splitedCurrentUrl = currentUrl.split("/");

        var popupName = splitedCurrentUrl[3];

        if ( popupName.indexOf( '-section' ) !== -1 ) {

            var buttonId = '#' + popupName.replace(/\-section/g, '') + '-button.bs-popup-open';

            $( buttonId ).trigger( 'click' );

        }

        var messageIsShown = garan.cookie.get( "greetings_message" );

        if ( messageIsShown != "is_shown" ) {

            $( '#g24-rate' ).text(garan.currency.rates('GBP').format(2,3,' ','.'));
            var popup = $( '.bs-overlay.ctshirts-greetings' );
            var message = $( '.bs-overlay.ctshirts-greetings .bs-popup-window' );

            popup.css( 'display', 'block' );

            $( '.start-shopping' ).on( 'click', function (e) {
                e.preventDefault();

                //popup.css( 'display', 'none' );

                garan.cookie.set("greetings_message","is_shown");

                message.animate(
                    {
                        top: "-1000"
                    },
                    {
                        duration: 400,
                        complete: function() {
                            popup.animate(
                                {
                                    opacity: "0"
                                },
                                {
                                    duration: 400,
                                    complete: function() {
                                        popup.css( 'display', 'none' );
                                    }
                                }
                            );
                        }
                    }
                );
            });

        }
        //$("[type='submit']").click(function(){parser.converter();});
    },
    styling:function(){

        garan.currency.converter.action({
            replacement:/£\s*(\d+\.\d*).*/i,
            //£ 29.95
            selector:".cart-row .item-price, #js-order-subtotal",
            currency:"GBP"
        });

        $("header").css("top","52px");
        $("#main").css("margin-top","52px");
        $(".js-header-search,.input-box--silent,.header__customer,#cart-items-form .order-shipping,#shippingSwitcherLink ").hide();
        $("#cart-items-form > div.panel--flexed.item-list__total").hide();
        $("#footer > div.main__area > div:nth-child(1)").hide();
        $("#footer > div.main__area > div:nth-child(2) > div.content__block.desktop-only.content__block--right.content__block--changecountry").hide();
        $("#cart-items-form > div.js-order-totals-section.panel--flexed.item-list__header--footer-helper").hide();
        $("#cart-table span.js-gift-message-status.item-list__addgift").hide();
        $(".product-options").hide();
        /*$("div.tile__pricing--listing.sale:contains('£'),span:contains('£'),b:contains('£'),td:contains('£')").each(function(){
            var $t = $(this),cur = garan.currency.rates('GBP'),txt=$t.text();
            txt = txt.replace(/\£(\d+\.?\d*)/g,function(m){
            //txt.replace(/\£(\d+\.?\d*)/g,function(m){
                var r = cur*parseFloat(m.replace(/\£/,''));
                console.debug('replacement '+m+" * "+cur);
                return r.format(2,3,' ','.')+' руб.';
            });
            $t.text(txt);
        });*/
        //parser.converter();
        $("#garan-currency").html('£1 = '+garan.currency.rates('GBP').format(2,3,' ','.')+' руб.');

        Urls.welcomeMat = null;




        //$(this.selector).replaceWith('<a class="garan-checkout garan24-button garan24-button-success" href="javascript:{parser.checkout();}" style="float:right;"><i class="fa fa-shopping-bag"></i> Оформить заказ</a>');
        $(this.selector).unbind("click").on("click",function(e){
            e.preventDefault();
            e.stopPropagation();
            if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
            parser.checkout();
        }).find("span").text("Оформить заказ");
        /*
        console.debug(btn[0]);
        $("#cart-items-form > div.panel--flexed.item-list__header--footer-total.item-list__header--footer-helper > table tbody").append(
            "<tr><td></th><td>"+btn+"</td></tr>"
        );
        */


        // site selector
        /*var ss = $(".change-country");
        console.debug("Choose country "+ss.length);
        if(ss.length){
            $( ".ui-dialog" ).dialog( "close" );
            document.location.href = "/on/demandware.store/Sites-CTShirts-UK-Site/en_GB/Site-Change?siteID=CTShirts-UK&userSelectedSite=true";
        }*/
        //auto update cart


        //$("#mini-cart > div.js-minicart-total.minicart__total").hide();

        /*if($("#mini-cart > div.js-minicart-total.minicart__total > a > span.js-minicart-quantity").text().length){
            //garan.cart.removeAll();
            parser.parseMini();
        }*/
        $(document).ready(function(){
            $( document ).ajaxComplete(function( event, xhr, settings ) {
                if ( settings.url.match(/.*\/cart\/get.*/i) ) {
                    console.debug( "Triggered ajaxComplete handler. The result is " + settings.url);
                    var js = JSON.parse(xhr.responseText);
                    console.debug(js);
                    garan.cart.removeAll();
                    parser.parseMini(js);
                }
                if( garan.cart.inited){
                    if ( (document.location.href.match(/.*\/cart/)&&!this.loaded) || settings.url.match(/.*Cart\-UpdateCart.*/))  {
                        this.loaded = true;
                        console.debug( "Triggered ajaxComplete handler. parse ");
                        //var js = JSON.parse(xhr.responseText);
                        //console.debug(js);
                        garan.cart.removeAll();
                        parser.parse();

                    }
                    else if ( (!document.location.href.match(/.*\/cart/)&&!this.loaded) || settings.url.match(/.*Cart\-AddProduct.*/))  {
                        this.loaded = true;
                        console.debug( "Triggered ajaxComplete handler. parseMini2");
                        //var js = JSON.parse(xhr.responseText);
                        //console.debug(js);
                        garan.cart.removeAll();
                        parser.parseMini2();
                    }
                }
            });
        });
    },
    checkout:function(){
        garan.cart.removeAll();
        this.parse();
        garan.cart.checkout();
    },
    selector:"#checkout-form  button[type='submit']",
    parse:function(){
        //document.getElementsByClassName('button button--green button--mobile button--right item-list__checkout-btn')[1].onclick = doWork();
        var objs = [];
        if(typeof document.getElementById('cart-table') != "undefined"){

        var products = document.getElementById('cart-table').getElementsByTagName('tbody')[0].getElementsByTagName('tr');


        for(x = 0; x < products.length; x++){
            if(products[x].className !== 'js-product-option-row item-list__row item-list__row--no-border item-list__row--option js-editable'){
                var obj = new Object();
                obj.currency = 'GBP';
                obj.variations = {};
                obj.shop = document.domain;

                if(products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed item-list__td--no-gift')[0])
                obj.quantity = products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed item-list__td--no-gift')[0].getElementsByTagName('input')[1].value.trim();

                else if(products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed')[0])
                obj.quantity = products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed')[0].getElementsByTagName('input')[1].value.trim();

                if(products[x].getElementsByClassName('item-price item-list__td item-list__td--mobile item-list__td--price item-list__td--pushed')[0].getElementsByTagName('b')[0])
                obj.original_price = products[x].getElementsByClassName('item-price item-list__td item-list__td--mobile item-list__td--price item-list__td--pushed')[0]
                .getElementsByTagName('b')[0].innerHTML.trim().replace(/[^\d\.\,]*/g, '');



                if(products[x].getElementsByClassName('name item-list__lineitem-name')[0].getElementsByTagName('a')[0])
                obj.title = products[x].getElementsByClassName('name item-list__lineitem-name')[0].getElementsByTagName('a')[0].title.trim();

                else if(products[x].getElementsByClassName('name item-list__lineitem-name')[0])
                obj.title = products[x].getElementsByClassName('name item-list__lineitem-name')[0].innerHTML;

                if(products[x].getElementsByClassName('name item-list__lineitem-name')[0].getElementsByTagName('a')[0])
                obj.product_url = products[x].getElementsByClassName('name item-list__lineitem-name')[0].getElementsByTagName('a')[0].href.trim();

                if(products[x].getElementsByClassName('basket-img_class_attr')[0])
                obj.product_img = products[x].getElementsByClassName('basket-img_class_attr')[0].src.trim();

                if(products[x].getElementsByClassName('sku')[0])
                obj.sku = products[x].getElementsByClassName('sku')[0].getElementsByClassName('value')[0].textContent.trim();

                if(products[x].getElementsByClassName('attribute--value value js-beltSize')[0])
                obj.variations.size = products[x].getElementsByClassName('attribute--value value js-beltSize')[0].textContent.trim();

                else if(products[x].getElementsByClassName('attribute--value value js-collarSize')[0])
                obj.variations.size = products[x].getElementsByClassName('attribute--value value js-collarSize')[0].textContent.trim();

                else if(products[x].getElementsByClassName('attribute--value value js-shoeSize')[0])
                obj.variations.size = products[x].getElementsByClassName('attribute--value value js-shoeSize')[0].textContent.trim();

                else if(products[x].getElementsByClassName('attribute--value value js-simpleSize')[0])
                obj.variations.size = products[x].getElementsByClassName('attribute--value value js-simpleSize')[0].textContent.trim();

                else if(products[x].getElementsByClassName('attribute--value value js-casualShirtSize')[0])
                obj.variations.size = products[x].getElementsByClassName('attribute--value value js-casualShirtSize')[0].textContent.trim();

                else if(products[x].getElementsByClassName('attribute--value value js-jacketSize')[0]){
                    obj.variations.size = products[x].getElementsByClassName('attribute--value value js-jacketSize')[0].textContent.trim();
                    if(products[x].getElementsByClassName('attribute--value value js-jacketLength')[0]){
                        obj.variations.size += '/'+products[x].getElementsByClassName('attribute--value value js-jacketLength')[0].textContent.trim();
                    }
                }

                else if(products[x].getElementsByClassName('attribute--value value js-trouserWaist')[0]){
                    obj.variations.size = products[x].getElementsByClassName('attribute--value value js-trouserWaist')[0].textContent.trim();
                    if(products[x].getElementsByClassName('attribute--value value js-trouserLength')[0]){
                        obj.variations.size += '/'+products[x].getElementsByClassName('attribute--value value js-trouserLength')[0].textContent.trim();
                    }
                }
                objs.push(obj);
            }
        }
        }
        console.log(objs);
        garan.cart.add2cart(objs);
        if(arguments.length&& typeof arguments[0]!="function"){
            var cb = arguments[0];
            cb();
        };
        //$("#garan-cart").click();
    },
    parseMini:function(){
        var psl = arguments[0], pp = [],fprice = function(ctp){
            var ret = ((ctp.OriginalSalePrice)?ctp.OriginalSalePrice:ctp.OriginalListPrice);
            if(typeof psl!="undefined" || typeof psl.discountsList!="undeifned"){

                for(var j in psl.discountsList){
                    var ctd = psl.discountsList[j];
                    if(ctd.ProductCartItemId == ctp.CartItemId) return (ret-ret*ctd.OriginalDiscountValue/100);
                }
            }
            return ret;
        };
        if(typeof psl=="undefined" || typeof psl.productsList=="undeifned") return;
        for(var i in psl.productsList){
            var ctp = psl.productsList[i], p = {
                shop:document.domain,
                quantity:ctp.OrderedQuantity,
                title:ctp.Name,
                description:ctp.Description,
                original_price:""+fprice(ctp),//((ctp.OriginalSalePrice)?ctp.OriginalSalePrice:ctp.OriginalListPrice),
                currency:"GBP",
                product_url:ctp.URL,
                product_img:ctp.ImageURL,
                variations:{
                    size:"",
                    length:"",
                    cuff:"",
                    color:""
                },
                sku:ctp.ProductCode
            };
            pp.push(p);
        }
        //console.debug(pp);
        garan.cart.add2cart(pp);
    },
    parseMini2:function(){
        var pp = [];
        //$("#mini-cart > div.js-mini-cart-content.minicart__content > div.js-mini-cart-products.minicart__products.minicart__products--shadow > div > div.js-mini-cart-product.minicart__productitem.minicart__productitem--just-added").each(function(){
        $("#mini-cart > div.js-mini-cart-content.minicart__content > div.js-mini-cart-products.minicart__products.minicart__products--shadow > div > div").each(function(){
            var $t = $(this),p = {
                shop:document.domain,
                currency:"GBP",
                quantity:1,
                title:$t.find(".minicart__details > div.minicart__name > div").text(),
                original_price:$t.find(".minicart__pricing > .minicart__item-total-price").text().trim().replace(/[^\d\.\,]+/ig,""),
                variations:{
                    size:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value:nth-child(1)").text().trim(),
                    length:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-sleeveLength").text().trim(),
                    cuff:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-cuffType").text().trim(),
                    width:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-shoeWidth").text().trim(),
                    color:""
                },
                product_url:document.location.href,
                product_img:$t.find(".minicart__line .minicart__image img").attr("src"),
                sku:""
            };
            if($t.find(".minicart__pricing > div > div > span").length){
                p.quantity = $t.find(".minicart__pricing > div > div span:nth-child(1)").text().replace(/\D*/ig,"");
                p.original_price = $t.find(".minicart__pricing > div > div span:nth-child(3) b").text().replace(/[^\d\.\,]+/ig,"");
            }
            pp.push(p);
        });
        console.debug(pp);
        garan.cart.add2cart(pp);
    },
    converterold:function(){
        //var currencyRate = parseFloat(83.39);


        garan.currency.get(function(){
            var currencyRate = garan.currency.rates("GBP");
            var cartRowTotals = $('.cart-row .item-total');

            cartRowTotals.each(function( index ) {

                var priceWithCurrencySign = $( this ).clone()
                    .children()
                    .remove()
                    .end()
                    .text();

                var price = parseFloat( priceWithCurrencySign.replace('£', '') );

                var priceString = price.format(2,3,' ','.');

                var priceInRubles = price * currencyRate;

                var priceInRublesString = priceInRubles.format(2,3,' ','.') + " руб.";

                var replaced = $( this ).html().replace( '£' + priceString, priceInRublesString );

                $( this ).html( replaced );

            });



            var cartRowItemsPrice = $('.cart-row .item-price b');

            cartRowItemsPrice.each(function( index ) {

                var price = parseFloat( $( this ).text().replace( '£', '' ) );

                var priceString = price.format(2,3,' ','.');

                var priceInRubles = price * currencyRate;

                var priceInRublesString = priceInRubles.format(2,3,' ','.') + " руб.";

                var replaced = $( this ).html().replace( '£' + priceString, priceInRublesString );

                $( this ).html( replaced );

            });



            var cartRowItemsPriceWas = $('.cart-row .item-price div.item-list__was-price');

            cartRowItemsPriceWas.each(function( index ) {

                var itemPriceWasString = $( this ).text();

                var price = parseFloat( itemPriceWasString.replace(/[^0-9\.]/g, '') );

                var priceString = price.format(2,3,' ','.');

                var priceInRubles = price * currencyRate;

                var priceInRublesString = priceInRubles.format(2,3,' ','.') + " руб.";

                var replaced = $( this ).html().replace( '£' + priceString, priceInRublesString );

                $( this ).html( replaced );

            });



            var cartRowGiftsPrice = $( '.cart-row .item-list__addgift .button--caption' );

            cartRowGiftsPrice.each(function( index ) {

                var price = parseFloat( $( this ).text().replace(/[^0-9\.]/g, '') );

                var priceString = price.format(2,3,' ','.');

                var priceInRubles = price * currencyRate;

                var priceInRublesString = priceInRubles.format(2,3,' ','.') + " руб.";

                var replaced = $( this ).html().replace( '£' + priceString, priceInRublesString );

                $( this ).html( replaced );

            });



            var youveSaved = $( '.js-cart-panel #js-cart-youve-saved-area b' );

            var youveSavedAmountString = youveSaved.text().replace(/[^0-9\.\£]/g, '');

            var youveSavedAmount = parseFloat( youveSaved.text().replace(/[^0-9\.]/g, '') );

            var youveSavedAmountInRubles = youveSavedAmount * currencyRate;

            var youveSavedAmountInRublesString = youveSavedAmountInRubles.format(2,3,' ','.') + " руб.";

            if (youveSaved.length) {

                var youveSavedReplaced = youveSaved.html().replace( youveSavedAmountString, youveSavedAmountInRublesString );

                youveSaved.html( youveSavedReplaced );

            }



            var orderTotal = $( '#js-order-subtotal' );

            var orderTotalString = $( '#js-order-subtotal' ).text().replace(/[^0-9\.\£]/g, '');

            var orderTotalPrice = orderTotalString.replace( '£', '' );

            var orderTotalPriceInRubles = parseFloat( orderTotalPrice ) * currencyRate;

            var orderTotalPriceInRublesString = orderTotalPriceInRubles.format(2,3,' ','.') + " руб.";

            if (orderTotal.length) {

                var orderTotalReplaced = orderTotal.html().replace( orderTotalString, orderTotalPriceInRublesString );

                orderTotal.html( orderTotalReplaced );

            }

        },false);
        /*garan.currency.converter.action({
            replacement:/(\d+\,\d+)\s*€/i,
            //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            selector:".big_total_number, .total_command_right span, .block_price span, .price_total",
            currency:"EUR"
        });*/

    }
};
