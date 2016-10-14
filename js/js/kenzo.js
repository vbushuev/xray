var parser = {
    styling:function(){
        $("header").css("top","60px");
    },
    selector:"form button[type='submit'][name='dwfrm_cart_checkoutCart']",
    parse:function(){
        var products = document.getElementsByClassName('product');
        for(x = 0; x < products.length; x++){
            garan.cart.add2cart({
                shop:document.domain,
            	quantity:products[x].getElementsByClassName('col-8-2 desc')[0].getElementsByClassName('sod_label')[0].getElementsByTagName('span')[0].textContent.trim(),
            	original_price:products[x].getElementsByClassName('col-8-2 desc')[1].getElementsByClassName('price')[0].innerHTML.trim() ,
                currency:"EUR",
            	title:products[x].getElementsByClassName('col-8-2 desc')[0].getElementsByClassName('title')[0].textContent.trim(),
            	product_url:products[x].getElementsByClassName('col-8-2')[0].getElementsByTagName('a')[0].href.trim(),
            	product_img:products[x].getElementsByClassName('col-8-2')[0].getElementsByTagName('a')[0].getElementsByClassName('img-responsive')[0].getAttribute('src'),
                variations:{
                    size:products[x].getElementsByClassName('col-8-2 desc')[0].getElementsByClassName('size')[0].innerHTML.trim().substring(6),
                    color:products[x].getElementsByClassName('col-8-2 desc')[0].getElementsByClassName('color')[0].textContent.trim().substring(7)
                }
            });
        }
    }
}
