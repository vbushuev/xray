if ( document.location.hostname.match(/brandalley/) ) {

    // Добавление товара в корзину

    $(document.body).on("submit", ".add-to-cart-form", function(event) {

        console.log('Нажата кнопка "Добавить в корзину"');

        //yaCounter40684604.reachGoal('ADD-TO-CART');

        return true;

    });




    // Переход к оформлению заказа

    $( document ).bind( 'gcart:beforeCheckout', function() {

        try {

            console.log('Нажата кнопка "Оформить заказ"');

            //yaCounter40684604.reachGoal('PLACE-ORDER');

            return true;

        } catch ( e ) {

            console.debug( e );

        }

        return true;

    });

    $( document ).bind( 'xray:showLine', function() {

        // Нажатие кнопки "Как купить"

        var howToBuyButton = document.getElementById( "how-to-buy-button" );

        if ( typeof( howToBuyButton ) != 'undefined' && howToBuyButton !== null ) {

            howToBuyButton.addEventListener( "click", function(e) {

            	console.log('Нажата кнопка "Как купить"');

                //yaCounter40684604.reachGoal('HOW-TO-BUY');

                return true;

            });

        }



        // Нажатие кнопки "Доставка"

        var shippingButton = document.getElementById( "shipping-button" );

        if ( typeof( shippingButton ) != 'undefined' && shippingButton !== null ) {

            shippingButton.addEventListener( "click", function(e) {

            	console.log('Нажата кнопка "Доставка"');

                //yaCounter40684604.reachGoal('SHIPPING');

                return true;

            });

        }



        // Нажатие кнопки "Оплата"

        var paymentButton = document.getElementById( "payment-button" );

        if ( typeof( paymentButton ) != 'undefined' && paymentButton !== null ) {

            paymentButton.addEventListener( "click", function(e) {

            	console.log('Нажата кнопка "Оплата"');

                //yaCounter40684604.reachGoal('PAYMENT');

                return true;

            });

        }



        // Нажатие кнопки "О нас"

        var aboutUsButton = document.getElementById( "about-us-button" );

        if ( typeof( aboutUsButton ) != 'undefined' && aboutUsButton !== null ) {

            aboutUsButton.addEventListener( "click", function(e) {

            	console.log('Нажата кнопка "О нас"');

                //yaCounter40684604.reachGoal('ABOUT-US');

                return true;

            });

        }



        // Нажатие кнопки "Акция"

        var promoActionButton = document.getElementById( "promo-action-button" );

        if ( typeof( promoActionButton ) != 'undefined' && promoActionButton !== null ) {

            promoActionButton.addEventListener( "click", function(e) {

            	console.log('Нажата кнопка "Акция"');

                //yaCounter40684604.reachGoal('PROMO-ACTION');

                return true;

            });

        }

    });

}
