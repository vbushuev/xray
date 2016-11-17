// Добавление товара в корзину

var addToCartButton = $( "#add-to-cart" );

if ( typeof( addToCartButton ) != 'undefined' && addToCartButton != null ) {

    addToCartButton.on( "click", function(e) {

    	console.log('Нажата кнопка "Добавить в корзину"');

        yaCounter40339220.reachGoal('ADD-TO-CART');

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

    console.log('Нажата кнопка "Оформить заказ"');

    yaCounter40339220.reachGoal('PLACE-ORDER');

    return true;

});



// Нажатие кнопки "Как купить"

var howToBuyButton = document.getElementById( "how-to-buy-button" );

if ( typeof( howToBuyButton ) != 'undefined' && howToBuyButton != null ) {

    howToBuyButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "Как купить"');

        yaCounter40339220.reachGoal('HOW-TO-BUY');

        return true;

    });

}



// Нажатие кнопки "Доставка"

var shippingButton = document.getElementById( "shipping-button" );

if ( typeof( shippingButton ) != 'undefined' && shippingButton != null ) {

    shippingButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "Доставка"');

        yaCounter40339220.reachGoal('SHIPPING');

        return true;

    });

}



// Нажатие кнопки "Оплата"

var paymentButton = document.getElementById( "payment-button" );

if ( typeof( paymentButton ) != 'undefined' && paymentButton != null ) {

    paymentButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "Оплата"');

        yaCounter40339220.reachGoal('PAYMENT');

        return true;

    });

}



// Нажатие кнопки "О нас"

var aboutUsButton = document.getElementById( "about-us-button" );

if ( typeof( aboutUsButton ) != 'undefined' && aboutUsButton != null ) {

    aboutUsButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "О нас"');

        yaCounter40339220.reachGoal('ABOUT-US');

        return true;

    });

}



// Нажатие кнопки "Акция"

var promoActionButton = document.getElementById( "promo-button" );

if ( typeof( promoActionButton ) != 'undefined' && promoActionButton != null ) {

    promoActionButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "Акция"');

        yaCounter40339220.reachGoal('PROMO-ACTION');

        return true;

    });

}



// Нажатие кнопки "Рассрочка"

var creditButton = document.getElementById( "credit-button" );

if ( typeof( creditButton ) != 'undefined' && creditButton !== null ) {

    creditButton.addEventListener( "click", function(e) {

    	console.log('Нажата кнопка "Рассрочка"');

        yaCounter40339220.reachGoal('CREDIT');

        return true;

    });

}
