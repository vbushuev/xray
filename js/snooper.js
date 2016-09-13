var $ = jQuery.noConflict();
$(document).ready(function() {
    window.G  = new G24();
    // baby-walz.de

    $("#usp_bar, .meta, .headBasket, .footerBox, #headSearch").hide();
    //$("#header > ul, #header > div.input-box.input-box--pushed.desktop-only.input-box--silent").hide();
    console.debug(document.location.hostname.split(/\./)[0].replace(/[\-]/,""));
    //collectData.init();
    $(document).ajaxComplete(function(e, jqXHR, options) {
        collectData.init();
    });
    $(".wrapper").css("padding-top","56px").css("position","relative");
    $(".header").css("position","absolute");

    //ctshirts
    $("#checkout-form  button[type='submit']").click(function(e){
        e.preventDefault();
        $(this).html("<i class='fa fa-cart'></i> Добавить в корзину");
        $("#cart-table .cart-row").each(function(){
            var $t = $(this),
            p = {
                product_id:-1,
                quantity:parseInt($t.find(".item-quantity .js-qty").val()),
                regular_price:parseFloat($t.find(".item-price b:first").text().replace(/[^\d\.\,]+/ig,""))*100,
                title: $t.find(".item-details .name").text(),
                description: $t.find(".item-details .sku").text(),
                product_url:$t.find(".item-image .thumb-link").attr("href"),
                product_img:$t.find(".item-image img").attr("src"),
                weight:"200",
                dimensions:{
                    "height":"100",
                    "width":"10",
                    "depth":"40"
                }
            },
            i = $t.find(".item-image img");
            console.debug(p);
            globalAdd2Cart(e,p,i);
        });
    });
});
