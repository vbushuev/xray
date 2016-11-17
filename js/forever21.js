jQuery.noConflict();
(function($) {
    window.parser = {
        selector:"#btn_checkout",
        init:function(){

            $( "#addtobag" ).on("click",function(e){
                $.ajax({
                    //url:"//service.garan24.bs2/analytics",
                    url:"//l.gauzymall.com/analytics",
                    success:function(d){
                        console.log(d);
                    }
                });
            });

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
                selector:".subtotals,.total_price",
                currency:"EUR"
            });

            $("#divGlobalContainer").hide();
            $(".new_sign_in,.new_header_country").hide();
            $("div.shopping_summarylist > ul:nth-child(4)").hide();
            $("#checkout_placeorder > div:nth-child(1)").hide();
            $("#ctl00_MainContent_divFreeShipping").hide();
            $("#promotion").hide();

            $(".t_wishlist").css("margin-left:1em;");
            $(".btn_checkout").hide();
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
            // correct functions

            /**/

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


window.fnChangeColor=function(category, id, colorId, colorName) {
    var url = '/EU/Ajax/Ajax_Product.aspx?method=CHANGEPRODUCTCOLOR&category=' + category + '&productid=' + id + '&colorid=' + colorId;
    $.ajax({
        type: "GET",
        url: url,
        dataType:"json",
        success: function (result) {
            $('.ItemImage.Main').fadeOut('fast', function () {
                $('.ItemImage.Main').attr('src', result.ProductDefaultImageURL);
                $('.ItemImage.Main').fadeIn('fast');
            });

            $('#pdp_thumbnail').fadeOut('fast', function () {
                $('#pdp_thumbnail').html(result.ProductButtonImageHTML);
                $('#pdp_thumbnail').fadeIn('fast');
            });

            $('#ulProductSize').fadeOut('fast', function () {
                $('#ulProductSize').html(result.ProductSizeHTML);
                $('#ulProductSize').fadeIn('fast');
            });

            $('#spanSelectedColorName').fadeOut('fast', function () {
                $('#spanSelectedColorName').html(colorName);
                $('#spanSelectedColorName').fadeIn('fast');
            });

            $('#ulProductColor li').removeClass('selected');
            $('#colorid_' + colorId).addClass('selected');
            $('#ulProductSize input:checkbox:checked').prop('checked', false);

            // set selected color value to hidden control
            $('.hdSelectedColor').val(colorId);
            $('.hdSelectedColorName').val(colorName);
            $('.hdSelectedSize').val(result.SelectedSizeID);

            fnChangeSizePDP();
        }
    });
}
function fnSubtractQty(lineItemId) {
    if (parseInt($("#spanQty_" + lineItemId).text()) != 1) {
        $.ajax({
            type: "POST",
            url: AppPath + "/ajax/ajax_cart.aspx?action=subtractqty&lineItemId=" + lineItemId,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (response) {
                console.debug(response);
                if (response["resultValue"] == "OK") {
                    $("#spanQty_" + lineItemId).text(response["qty"]);
                    $("#divSubTotal_" + lineItemId).text(response["extendedPrice"]);
                }
                else {
                    fnOpenPopup("failed", msgChangeQtyError, "N");
                }

                fnReloadContents();
            },
            error: function (msg) {
                fnOpenPopup("failed", msgChangeQtyError, "N");
            },
            timeout: 60000
        });
    }

    return false;
}
function fnAdditionQty(lineItemId) {

    if (parseInt($("#spanQty_" + lineItemId).text()) < 20) {
        $.ajax({
            type: "POST",
            url: AppPath + "/ajax/ajax_cart.aspx?action=additionqty&lineItemId=" + lineItemId,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (response) {
                console.debug(response);
                if (response["resultValue"] == "OK") {
                    $("#spanQty_" + lineItemId).text(response["qty"]);
                    $("#divSubTotal_" + lineItemId).text(response["extendedPrice"]);
                }
                else {
                    fnOpenPopup("failed", msgChangeQtyError, "N");
                }

                fnReloadContents();
            },
            error: function (msg) {
                fnOpenPopup("failed", msgChangeQtyError, "N");
            },
            timeout: 60000
        });
    }
}
function fnReloadContents() {
    document.location.reload();
    //$("#ctl00_MainContent_btnReload").click();
}
