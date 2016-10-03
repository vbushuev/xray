var parser = {
    selector:"#checkout-form  button[type='submit']",
    parse:function(){
        var products = document.getElementsByTagName('tbody')[1].getElementsByTagName('tr');
        for(x = 0; x < products.length - 4; x++){
            var obj = new Object();
            if(x % 2 === 0){
            obj.shop = document.domain;

            obj.quantity = products[x].getElementsByClassName('amount')[0].innerHTML.trim();

            obj.original_price = products[x].getElementsByClassName('articlePrice')[0].textContent.trim();

            obj.currency = 'euro';

            obj.title = products[x].getElementsByClassName('prodLink')[0].textContent.trim();

            obj.product_url = products[x].getElementsByClassName('prodLink')[0].href.trim();

            obj.product_img = products[x].getElementsByClassName('prod')[0].getElementsByTagName('a')[0].getElementsByTagName('img')[0].src.trim();

            obj.sku = products[x].getElementsByClassName('value')[0].textContent.trim();

            if(products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(0, 7) === 'Coloris')
            obj.color = products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(22);

            else
            obj.size = products[x].getElementsByTagName('td')[1].getElementsByTagName('div')[0].textContent.trim().substring(13);

            console.log(obj);
            }

        }
    }
}
