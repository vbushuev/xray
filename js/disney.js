document.onload = function(){
        setTimeout(doWork(), 100);
}

function doWork(){

    document.getElementsByClassName('btn primary right beginCheckout')[0].onclick = doWork();
     document.getElementsByClassName('btn primary beginCheckout')[0].onclick = doWork();

    var products = document.getElementsByClassName('bagItem clearfix');
    for(x = 0; x < products.length; x++){
        var obj = new Object();
        obj.shop = document.domain;
        obj.quantity = products[x].getElementsByClassName('value')[0].textContent.trim();
        obj.original_price = products[x].getElementsByClassName('amount')[0].textContent.trim();
        obj.currency = 'euro';
        obj.title = products[x].getElementsByClassName('productName')[0].title.trim();
        obj.product_url = products[x].getElementsByClassName('productName')[0].href.trim();
        obj.product_img = products[x].getElementsByClassName('productImage')[0].getElementsByTagName('img')[0].src.trim();
        obj.sku = products[x].getElementsByClassName('productId')[0].textContent.trim().substring(1);
        if(products[x].getElementsByClassName('value')[1]){
        obj.size = products[x].getElementsByClassName('value')[1].textContent.trim();
        }
        console.log(obj);
    }
}

