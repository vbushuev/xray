if(document.location.hostname.match(/baby\-walz/)){
// Добавление товара в корзину
var dansLePantier = $( ".buBasket" );

if ( typeof( dansLePantier ) != 'undefined' && dansLePantier != null ) {

    dansLePantier.click( function(e) {

    	console.log('Нажата кнопка "Добавить в корзину"');

        yaCounter40422350.reachGoal('DANS-LE-PANIER');

        return true;

    });

}



// Переход к оформлению заказа

// var checkoutForm = $( "#checkout-form" );
//
// if ( typeof( checkoutForm ) != 'undefined' && checkoutForm != null ) {
//
//     checkoutForm.on( "submit", function(e) {
//
//     	console.log('Нажата кнопка "Оформить заказ"');
//
//         yaCounter40339220.reachGoal('PLACE-ORDER');
//
//         return true;
//
//     });
//
// }

$( document ).bind( 'gcart:beforeCheckout', function() {
    try{
        console.log('Нажата кнопка "Оформить заказ"');
        yaCounter40422350.reachGoal('PLACE-ORDER');
    }catch(e){
        console.debug(e);
    }
    return true;

});

$( document ).bind( 'xray:showLine', function() {

    // Нажатие кнопки "Как купить"

    var howToBuyButton = document.getElementById( "how-to-buy-button" );

    if ( typeof( howToBuyButton ) != 'undefined' && howToBuyButton != null ) {

        howToBuyButton.addEventListener( "click", function(e) {

        	console.log('Нажата кнопка "Как купить"');

            yaCounter40422350.reachGoal('HOW-TO-BUY');

            return true;

        });

    }



    // Нажатие кнопки "Доставка"

    var shippingButton = document.getElementById( "shipping-button" );

    if ( typeof( shippingButton ) != 'undefined' && shippingButton != null ) {

        shippingButton.addEventListener( "click", function(e) {

        	console.log('Нажата кнопка "Доставка"');

            yaCounter40422350.reachGoal('SHIPPING');

            return true;

        });

    }



    // Нажатие кнопки "Оплата"

    var paymentButton = document.getElementById( "payment-button" );

    if ( typeof( paymentButton ) != 'undefined' && paymentButton != null ) {

        paymentButton.addEventListener( "click", function(e) {

        	console.log('Нажата кнопка "Оплата"');

            yaCounter40422350.reachGoal('PAYMENT');

            return true;

        });

    }



    // Нажатие кнопки "О нас"

    var aboutUsButton = document.getElementById( "about-us-button" );

    if ( typeof( aboutUsButton ) != 'undefined' && aboutUsButton != null ) {

        aboutUsButton.addEventListener( "click", function(e) {

        	console.log('Нажата кнопка "О нас"');

            yaCounter40422350.reachGoal('ABOUT-US');

            return true;

        });

    }



    // Нажатие кнопки "Акция"

    var promoActionButton = document.getElementById( "promo-action-button" );

    if ( typeof( promoActionButton ) != 'undefined' && promoActionButton != null ) {

        promoActionButton.addEventListener( "click", function(e) {

        	console.log('Нажата кнопка "Акция"');

            yaCounter40422350.reachGoal('PROMO-ACTION');

            return true;

        });

    }

});
}
