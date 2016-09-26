document.onload = function(){
    setTimeout(function(){
    var checkout = document.getElementsByClassName('btn ga_minicart');
    checkout[0].onclick = doWork();
    }, 100);
}

function doWork(){
    var products = document.getElementsByClassName('product');
    for(x = 0; x < products.length; x++){
        var json = '{\nshop:"';
        json += document.domain+'",\n';
        json += 'quantity:"' + products[x].getElementsByClassName('quantity')[0].innerHTML.trim() + '",\n';
        json += 'original_price:"' + products[x].getElementsByClassName('price')[0].innerHTML.trim() + '",\n';
        json += 'currency:"euro",\n';
        json += 'title:"' + products[x].getElementsByClassName('title')[0].textContent.trim() + '",\n';
        json += 'description:"' + products[x].getElementsByClassName('col-left')[0].getElementsByTagName('a')[0].getElementsByClassName('img-responsive')[0].getAttribute('alt') + '",\n';
        json += 'product_url:"' + products[x].getElementsByClassName('col-left')[0].getElementsByTagName('a')[0].href.trim() + '",\n';
        json += 'product_img:"' + products[x].getElementsByClassName('col-left')[0].getElementsByTagName('a')[0].getElementsByClassName('img-responsive')[0].getAttribute('src') + '",\n';
        json += 'variations:{\n "size":"' + products[x].getElementsByClassName('size')[0].innerHTML.trim().substring(6) + '"\n';
        json += 'color:"' + products[x].getElementsByClassName('col-left')[0].getElementsByTagName('a')[0].getElementsByClassName('img-responsive')[0].getAttribute('alt').split(',')[1].trim() + '",\n';
        json += '}\n}';
        console.log(json+'');
    }
}
