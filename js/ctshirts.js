//ctshirts
var parser = {
    styling:function(){
        $("header").css("top","80px");
        $("#main").css("margin-top","80px");
        $(".js-header-search,.input-box--silent,.header__customer,#cart-items-form .order-shipping,#shippingSwitcherLink ").hide();
        $("#cart-items-form > div.panel--flexed.item-list__total").hide();
        $("#footer > div.main__area > div:nth-child(1)").hide();
        $("#footer > div.main__area > div:nth-child(2) > div.content__block.desktop-only.content__block--right.content__block--changecountry").hide();
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
        $("#garan-currency").html('£1 = '+garan.currency.rates('GBP').format(2,3,' ','.')+' руб.');
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
        $( document ).ajaxComplete(function( event, xhr, settings ) {

            if ( settings.url.match(/.*\/cart\/get.*/i) ) {
                console.debug( "Triggered ajaxComplete handler. The result is " + settings.url);
                var js = JSON.parse(xhr.responseText);
                console.debug(js);
                garan.cart.removeAll();
                parser.parseMini(js);
            }
        });
    },
    selector:"#checkout-form  button[type='submit']",
    parse:function(){
        //document.getElementsByClassName('button button--green button--mobile button--right item-list__checkout-btn')[1].onclick = doWork();
        var products = document.getElementById('cart-table').getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        var objs = [];

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
            .getElementsByTagName('b')[0].innerHTML.trim().replace(/D/g, '');



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

            //console.log(obj);

            objs.push(obj);
            }
        }

        garan.cart.add2cart(objs);
        if(arguments.length&& typeof arguments[0]!="function"){
            var cb = arguments[0];
            cb();
        };
        $("#garan-cart").click();
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
        $("#mini-cart > div.js-mini-cart-content.minicart__content > div.js-mini-cart-products.minicart__products.minicart__products--shadow > div > div.js-mini-cart-product.minicart__productitem.minicart__productitem--just-added").each(function(){
            var $t = $(this),p = {
                shop:document.domain,
                quantity:1,
                title:$t.find(".minicart__details > div.minicart__name > div").text(),
                original_price:$t.find(".minicart__pricing > .minicart__item-total-price").text(),
                variations:{
                    size:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-collarSize").text(),
                    length:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-sleeveLength").text(),
                    cuff:$t.find(".minicart__details > div.minicart__options > b.attribute--value.value.js-cuffType").text(),
                    color:""
                },
                product_url:document.location.href,
                product_img:$t.find(".minicart__line .minicart__image img").attr("src"),
                sku:""
            };
            if($(".minicart__pricing > div > div.minicart__total-caption .minicart__price span").length){
                p.quantity = $(".minicart__pricing > div > div.minicart__total-caption .minicart__price span:nth-child(1)").text();
                p.original_price = $(".minicart__pricing > div > div.minicart__total-caption .minicart__price span:nth-child(3)").text();
            }
            pp.push(p);
        });
        console.debug(pp);
    }
};
