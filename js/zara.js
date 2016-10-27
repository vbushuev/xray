var parser = {
    selector:"#scrollArea > div.content > div > div.dmc_mb3_shoppingbasket_shoppingbasket_entry > div.formButtons > a.primButton, #jsFirstShoppingBasketFormSubmitButton > a",
    styling:function(){
        garan.currency.converter.action({
            replacement:/(\d+\,\d+)\s*€/i,
            //selector:".currentPrice,.ecwi_recommendations_TEXT_2,.ecwi_recommendations_TEXT_4,#productCurrentPrice1_span,.articlePrice,#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            selector:"#basketAmountContainer,td.total,td.costsValue,#Gratis,td.costsTotalValue",
            currency:"EUR"
        });
        if (window!=window.top) return;
        $("body").css("padding-top","54px");
        $("#usp_bar").hide();
        $("#scrollArea > div.content > div > div.dmc_mb3_orderform_orderline_basket").hide();
        $("#scrollArea > div.meta").hide();
        $("#scrollArea > div.groupNavi > ul > li.last").hide();
        $("#scrollArea > div.content > div > div.dmc_mb3_shoppingbasket_shoppingbasket_entry > table > tbody > tr.tableCount.lastRow").hide();
        $("#scrollArea > div.content > div > div.dmc_mb3_shoppingbasket_shoppingbasket_entry > table > tbody > tr.costsRow").hide();

        $(this.selector)
            .replaceWith('<a class="g-baby-walz-checkout" href="javascript:parser.checkout();"><i class="fa fa-shopping-cart"></i> Оформить заказ</a>')
            //.attr("onClick","")
            .unbind("click").click(function(e){
            //e.preventDefault();
            //e.stopPropagation();

            parser.checkout();
        });//.find("span").text("Оформить заказ");

    },
    init:function(){
        /*var g_first = garan.cookie.get("g_first","yes");
        if(g_first=="yes"){
            garan.cookie.set("googtrans","/fr/ru");
            garan.cookie.set("g_first","no");
        }*/
    },
    parse:function(){
        var products = document.getElementsByTagName('tbody')[1].getElementsByTagName('tr'),pp=[];
        for(x = 0; x < products.length - 4; x++){
            var obj = new Object();
            obj.variations = {};
            if(x % 2 === 0){
                obj.shop = 'baby-walz.fr';
                obj.quantity = products[x].getElementsByClassName('amount')[0].innerHTML.trim().replace(/\D+/,"");
                obj.original_price = products[x].getElementsByClassName('articlePrice')[0].textContent.trim();
                obj.currency = 'EUR';
                obj.title = products[x].getElementsByTagName('td')[1].getElementsByTagName('h5')[0].textContent.trim();
                obj.description = products[x].getElementsByClassName('prodLink')[0].textContent.trim();
                obj.product_url = products[x].getElementsByClassName('prodLink')[0].href.trim();
                obj.product_img = products[x].getElementsByClassName('prod')[0].getElementsByTagName('a')[0].getElementsByTagName('img')[0].src.trim();
                obj.sku = products[x].getElementsByClassName('value')[0].textContent.trim();
                if( products[x].getElementsByTagName('td')[1].getElementsByTagName('div').length ){
                    var divv = products[x].getElementsByTagName('td')[1].getElementsByTagName('div');
                    for(var i = 0;i<divv.length;++i){
                        if (divv[i].textContent.trim().match(/.*coloris.*/i))
                            obj.variations.color = divv[i].textContent.trim().split(':')[1];
                        else if  (divv[i].textContent.trim().match(/.*taille.*/i))
                            obj.variations.size = divv[i].textContent.trim().split(':')[1];
                    }
                }
                obj.product_url = obj.product_url.replace(/\.xray\.bs2|\.gauzymall\.com/,".fr");
                pp.push(obj);
            }
        }
        console.log(pp);
        garan.cart.add2cart(pp);
    },
    checkout:function(){
        if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
        try {
            garan.cart.removeAll();
            parser.parse();
            garan.cart.checkout();
        } catch (e) {
            console.error(e);
        }
    },
}
