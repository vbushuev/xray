var parser = {
    selector:"#scrollArea > div.content > div > div.dmc_mb3_shoppingbasket_shoppingbasket_entry > div.formButtons > a.primButton, #jsFirstShoppingBasketFormSubmitButton > a",
    styling:function(){
        $("body").css("top","54px");
        $("#usp_bar").hide();
        $(this.selector).attr("onClick","").unbind("click").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            if(typeof ga!="undefined")ga('send','event','events','checkout','checkout',5,false);else console.debug("no ga!!! checkout");
            parser.checkout();
        });//.find("span").text("Оформить заказ");
    },
    init:function(){},
    parse:function(){
        var products = document.getElementsByTagName('tbody')[1].getElementsByTagName('tr'),pp=[];
        for(x = 0; x < products.length - 4; x++){
            var obj = new Object();
            obj.variations = {};
            if(x % 2 === 0){
                obj.shop = document.domain;
                obj.quantity = products[x].getElementsByClassName('amount')[0].innerHTML.trim();
                obj.original_price = products[x].getElementsByClassName('articlePrice')[0].textContent.trim();
                obj.currency = 'EUR';
                obj.title = products[x].getElementsByClassName('prodLink')[0].textContent.trim();
                obj.product_url = products[x].getElementsByClassName('prodLink')[0].href.trim();
                obj.product_img = products[x].getElementsByClassName('prod')[0].getElementsByTagName('a')[0].getElementsByTagName('img')[0].src.trim();
                obj.sku = products[x].getElementsByClassName('value')[0].textContent.trim();
                if(    products[x].getElementsByTagName('td')[1].getElementsByTagName('div').length ){
                    if (products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(0, 7) === 'Coloris')
                    obj.variations.color = products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(22);
                    else obj.variations.size = products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(13);
                }
                pp.push(obj);
            }
        }
        console.log(pp);
        garan.cart.add2cart(pp);
    },
    checkout:function(){
        this.parse();
        garan.cart.checkout();
    },
}
