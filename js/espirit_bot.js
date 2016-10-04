//в реализации должно получиться что-то вроде захода в цикле на каждую страницу товара, принта в страницу скрипта и вызова нужной функции с параметрами товара

//Добавление товара в корзину
//цвет не указан, т.к. если вы спарсили товар, то ссылка содержит уже и цвет (для каждого цвета одинакого товара разные ссылки). Изменять кол-во товара методом changeQty в КОРЗИНЕ по индексу товара.
function addToCart(size){
    var sizes = document.getElementsByClassName('font-lucida sizechooser sizeboxes')[0].getElementsByTagName('ul')[0].getElementsByTagName('li');
        for(x = 0; x < sizes.length; x++){
            if(size === sizes[x].textContent.trim()){
                sizes[x].click();
                break;
            }
        }
    //Добавление в корзину
    document.getElementById('basketButtonId_1').click();
}

//Изменение кол-ва товара
//http://www.esprit.fr/panier В параметрах давать индекс товара (какой по счету, начинаю отсчет с нуля) и его кол-во. 
function changeQty(index, qty){
       var qtys = document.getElementsByClassName('countSelection');
       qtys[index].style.display = 'block';
       qtys[index].getElementsByTagName('option')[qty].setAttribute('selected', 'selected');
       qtys[index].getElementsByTagName('option')[qtys[index].selectedIndex].setAttribute('selected', 'selected');
       document.getElementsByClassName('to_checkout button font-serif')[0].click();
}

//Оформление заказа
//https://www.esprit.fr/order/fr/checkout/addresslogisticpartner Я понятия не имею что такое rue, prenom & nom - Фамилия и имя, почтовый индекс, localite - ???, и почта. 
//День рождения в формате 00. Месяц в 00. Год в 0000.
function validateOrder(prenom, nom, birth_day, birh_month, birth_year, rue, postal_code, localite, email){
        document.getElementById('invoiceAddress-Gender').style.display = 'block';
        document.getElementById('invoiceAddress-Gender').getElementsByTagName('option')[0].removeAttribute('selected');
        document.getElementById('invoiceAddress-Gender').getElementsByTagName('option')[1].setAttribute('selected', 'selected');
        document.getElementById('invoiceAddress-Firstname').value = prenom;
        document.getElementById('invoiceAddress-Lastname').value = prenom;
        document.getElementById('invoiceAddress-Birthday-day').value = birth_day;
        document.getElementById('invoiceAddress-Birthday-month').value = birh_month;
        document.getElementById('invoiceAddress-Birthday-year').value = birth_year;
        document.getElementById('invoiceAddress-Street').value = rue;
        document.getElementById('invoiceAddress-Postcode').value = postal_code;
        document.getElementById('invoiceAddress-City').value = localite;
        document.getElementById('invoiceAddress-Email').value = email;
        document.getElementById('invoiceAddress-EmailRepeat').value = email;
        document.getElementById('NextStep_AddressLogisticPartner').click();
}

//Оплата карточкой
//https://www.esprit.fr/order/fr/checkout/checkoutpostprocess
//сс_type - тип карточки (visa, mastercard...), номер, владелец(фамилия имя), месяц и год до какого действительна карта в формате 00-0000, код на задней стороне карты из 4х цифр.
function pay(cc_type, card_number, card_holder, month_validate, year_validate, security_code){
    var select = document.getElementById('cc_type');
    select.style.display = 'block';
    var options = select.getElementsByTagName('option');
    options[select.selectedIndex].removeAttribute('selected');
    for(i = 0; i < options.length; i++){
        if(cc_type === options[i].value.trim()){
            options[i].setAttribute('selected', 'selected');
            break;
        }
    }

    document.getElementById('cardHolder').value = card_holder;
    document.getElementById('cardNumber').value = card_number;
    document.getElementById('validationCode').value = security_code;

    var select = document.getElementById('expirationMonth');
    select.style.display = 'block';
    var options = select.getElementsByTagName('option');
    options[select.selectedIndex].removeAttribute('selected');
    for(i = 0; i < options.length; i++){
        if(month_validate === options[i].value.trim()){
            options[i].setAttribute('selected', 'selected');
            break;
        }
    }

    var select = document.getElementById('expirationYear');
    select.style.display = 'block';
    var options = select.getElementsByTagName('option');
    options[select.selectedIndex].removeAttribute('selected');
    for(i = 0; i < options.length; i++){
        if(month_validate === options[i].value.trim()){
            options[i].setAttribute('selected', 'selected');
            break;
        }
    }

    document.getElementById('confirm').click();



}