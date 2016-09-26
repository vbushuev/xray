document.onload = function(){
    if(location.href === 'http://www.damart.fr/card'){
        setTimeout(doWork(), 100);
    }
}

function doWork(){
    document.getElementById('validateBasketDBasket_WAR_damartexfrontportlet_LAYOUT_12617').onclick = doWork();
    var products = document.getElementsByClassName('modify_product_active ');
    var obj = new Object();
    for(x = 0; x < products.length; x++){
        obj.shop = document.domain;
        obj.quantity = products[x].getElementsByClassName('spinner')[0].getElementsByTagName('input')[0].value.trim();
        obj.original_price = products[x].getElementsByClassName('price')[0].textContent.trim();
        obj.currency = 'euro';
        obj.title = products[x].getElementsByClassName('name')[0].getElementsByTagName('a')[0].title.trim();
        obj.product_url = products[x].getElementsByTagName('figure')[0].getElementsByTagName('a')[0].href.trim();
        obj.product_img = products[x].getElementsByTagName('figure')[0].getElementsByTagName('a')[0].getElementsByTagName('img')[0].src.trim();
        obj.sku = products[x].getElementsByClassName('choose_option')[0].getElementsByTagName('li')[2].textContent.trim().substring(18);
        obj.color = products[x].getElementsByClassName('choose')[0].getElementsByClassName('val')[0].textContent.trim();
        obj.size = products[x].getElementsByClassName('choose')[1].getElementsByClassName('val')[0].textContent.trim();
        console.log(obj);
    }
}