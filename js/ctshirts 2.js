document.onload = function(){
        setTimeout(doWork(), 500);
}

function doWork(){
    document.getElementsByClassName('button button--green button--mobile button--right item-list__checkout-btn')[1].onclick = doWork();

    var products = document.getElementById('cart-table').getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    
    for(x = 0; x < products.length; x++){
        if(products[x].className !== 'js-product-option-row item-list__row item-list__row--no-border item-list__row--option js-editable'){
        var obj = new Object();

        obj.shop = document.domain;
                                               
        if(products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed item-list__td--no-gift')[0])
        obj.quantity = products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed item-list__td--no-gift')[0].getElementsByTagName('input')[1].value.trim();

        else if(products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed')[0])
        obj.quantity = products[x].getElementsByClassName('item-quantity item-list__td item-list__td--qty item-list__td--pushed')[0].getElementsByTagName('input')[1].value.trim();

        if(products[x].getElementsByClassName('item-price item-list__td item-list__td--mobile item-list__td--price item-list__td--pushed')[0].getElementsByTagName('b')[0])
        obj.original_price = products[x].getElementsByClassName('item-price item-list__td item-list__td--mobile item-list__td--price item-list__td--pushed')[0]
        .getElementsByTagName('b')[0].innerHTML.trim().replace(/D/g, '');

        obj.currency = 'pound';

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
        obj.size = products[x].getElementsByClassName('attribute--value value js-beltSize')[0].textContent.trim();

        else if(products[x].getElementsByClassName('attribute--value value js-collarSize')[0])
        obj.size = products[x].getElementsByClassName('attribute--value value js-collarSize')[0].textContent.trim();

        else if(products[x].getElementsByClassName('attribute--value value js-shoeSize')[0])
        obj.size = products[x].getElementsByClassName('attribute--value value js-shoeSize')[0].textContent.trim();

        else if(products[x].getElementsByClassName('attribute--value value js-simpleSize')[0])
        obj.size = products[x].getElementsByClassName('attribute--value value js-simpleSize')[0].textContent.trim();

        else if(products[x].getElementsByClassName('attribute--value value js-casualShirtSize')[0])
        obj.size = products[x].getElementsByClassName('attribute--value value js-casualShirtSize')[0].textContent.trim();

        
        console.log(obj); 
        }
    }
}