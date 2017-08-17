var jscontent = null;
var itemsFilled = false;
var cohost = (document.location.href.match(/\.bs2/))?"//co.bs2":"//co.xrayshopping.com";
var boxberryToken = 'Aw+IJdwZMl1hMwsdq/uvQA==';
var boxberryCallback = function(bb){
    $('input[name=delivery_id]').val(1);
    console.debug(bb);
    var order = cart.get();
    if(order==null)return;
    var addr = bb.address.split(",");
    console.debug(addr);
    $("#_com_form").append('<input type="hidden" name="service_fee" value="'+bb.price+'"/>');
    $("#_com_form").append('<input type="hidden" name="address_postcode" value="'+addr[0]+'"/>');
    $("#_com_form").append('<input type="hidden" name="address_country" value="RU"/>');
    $("#_com_form").append('<input type="hidden" name="address_city" value="'+addr[1]+'"/>');
    $("#_com_form").append('<input type="hidden" name="address_address" value="'+addr.reverse().splice(0,2).reverse().join()+'"/>');
    for(var i in order.items){if(order.items[i].type=="delivery")order.items.splice(i,1);}
    order.items.push({
        title:"Boxberry ПВЗ "+bb.name+"<br/>"+bb.address+"<br/>"+bb.workschedule+" "+bb.phone+"<br/>Ориентировочный срок доставки: "+bb.period,
        brand:"Доставка",
        price:bb.price,
        currency:'RUB',
        quantity:1,
        sku:bb.id,
        type:"delivery"
    });
    cart.set(order);
    items(order);
    $("#_com_main_form_btn_submit").click();
};
var getQueryByName=function(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
var queryParameters = {};
var __p = window.location.href.match(/\?(.+)$/);
if(__p!=null&&__p.length>1){
    var __a = __p[1].split(/\&/);
    for(var i in  __a){
        var nv = __a[i].split(/=/);
        queryParameters[nv[0]] = decodeURIComponent(nv[1].replace(/\+/ig," "));
    }
}
//medimax cart object
var cart = {
    empty:function(){
        var ord = cart.get();
        if(ord!=null && typeof(ord.items)!="undefined"){
            for(var i in ord.items){
                $.post('/cart/delete',{referer:'/basket',itemId:ord.items[i].id});
            }
        }
        $.removeCookie("order");
    },
    parse:function(){
        var o={
            amount:0,
            total:0,
            currency:'EUR',
            shop_id:'1',
            response_url:document.location.href,
            status_id:"0",
            items:[]
        };
        $(".cart-data__item").each(function(){
            var $t = $(this),itm = {
                id:$t.find(".cart-product").attr("id"),
                title:$t.find(".cart-product__description  .cart-product-description__title").text(),
                brand:$t.find(".cart-product__description  .cart-product-description__manufacturer").text(),
                image:$t.find(".cart-product > .cart-product__image > a.cart-product-image__link > .cart-product-image__thumbnail").attr("src"),
                url:$t.find(".cart-product > .cart-product__image > a.cart-product-image__link").attr("href"),
                //price:$t.find(".cart-price__value").text().replace(/[^\d\.,]+/g,"").replace(/\./,"").replace(/,/,"."),
                price:$t.find(".cart-price__value").text().replace(/[^\d\.]*/g,""),
                currency:'EUR',
                original_price:$t.find(".cart-price__value .xg_original_converted").text().replace(/[^\d\.]*/g,""),
                quantity:$t.find("select[name=quantity] option:selected").val(),
                sku:$t.find(".product-no__number").text().replace(/[^:]+:\s*(.+)/,"$1"),
                type:"product"
            };
			itm.original_price=(itm.original_price=="")?itm.price:itm.original_price;
            o.amount+=parseFloat(itm.price)* parseInt(itm.quantity);
            o.total++;
            //console.debug("parse:");
            console.debug(itm);
            o.items.push(itm);
        });
        //cart.set(o);
        $("#_com").trigger("co:parsed",o);
        return o;
    },
    create:function(d){
        $.mcookie("order",JSON.stringify(d));
        return d;
    },
    set:function(d){
        var l = cart.get();
        if(l==null)l={};
        l = $.extend(l,d);
        $.mcookie("order",JSON.stringify(l));
        return l;
    },
    get:function(){
        return (typeof($.mcookie("order"))!="undefined")?JSON.parse($.mcookie("order")):null;
    },
    compare:function(i1,i2){
        if(i1.amount != i2.amount) return false;
        if(i1.currency != i2.currency) return false;
        //if(i1.items.length != i2.items.length) return false;
        //console.debug("comparing: length matched");
        //for(var i in i1)if(i1[i].sku!=i2[i].sku)return false;
        //console.debug("comparing: matched");
        return true;
    }
};

//end cart object

var priceNumber = function(d){
    var r = (d==null)?0:d, a = r.toString().replace(/^\s+/,"").replace(/\s+$/,"").split(/\./),na=[],n="",f="";
    if(d>=1000){
        na = a[0].split("").reverse();var c = 3;
        for(var i in na){
            n+=na[i];
            if(--c<=0){
                n+=" ";
                c=3;
            }
        }
        var tail = (typeof(a[1])=="undefined")?"00":a[1];

        r = n.split("").reverse().join("")+"."+tail;
    }
    r+='&#8381;';
    return r;
};
var page ={
    submit:function(){
		try{
			if(!arguments.length)return;
	        var p=arguments[0],args = {},validated = true;
	        $(p.form+' input,'+p.form+' textarea,'+p.form+' select option:selected').each(function(){
	            var val = $(this).val(),$p = $(this).parent(".-com-form-input"),$t = $(this);
				console.debug($p);
	            //todo add validate data
				if($p.hasClass("-com-input-required")){
					var notvalid = false;
					notvalid = (val.length==0);
					if(notvalid){
						$p.addClass("-com-input-required-alert");
						$t.focus();
						validated = false;
						return false;
					}
				}
				$p.removeClass("-com-input-required-alert");
	            //todo add check required
	            args[$(this).attr("name")]=val;
	        });
			if(validated){
		        $.ajax({
		            url:$(p.form).attr("data-rel"),
		            dataType:"json",
		            data:args,
		            beforeSend:function(){
		                //$(p.form).animate({left:-4000},400,function(){$(this).html('').css("left","4000");});
		                $(p.form).animate({left:-4000},400,function(){}).html('').css("left","4000");
		            },
		            success:function(d){
		                var order = cart.set(d);
		                $("body").trigger("co:checkout",order);
		            }
		        });
			}
		}
        catch(e){
			console.error(e);
			$(".cart-actions__checkout-link").click();
		}
    },
	input:function(){
		var r='',it = 'text',o=$.extend({
			type:'text',
			name:'name',
			title:'Label',
			placeholder:'',
			required: false
		},arguments.length?arguments[0]:{});
		r = '<div class="-com-form-input'+((o.required)?' -com-input-required':'');
		switch(o.type){
			case 'email': r+=' -com-form-input-email';it='text';break;
			case 'phone': r+=' -com-form-input-phone';it='text';break;
			case 'hidden': r+=' -com-form-input-phone';it='hidden';break;
		}
		r+= '"><label for="'+o.name+'">'+o.title+'</label><input type="'+it+'" name="'+o.name+'" placeholder="'+o.placeholder+'"/></div>';
		return r;
	},
	textarea:function(){
		var r='',it = 'text',o=$.extend({
			name:'name',
			title:'Label',
			placeholder:'',
			required: false
		},arguments.length?arguments[0]:{});
		r = '<div class="-com-form-input'+((o.required)?' -com-input-required':'')+'>';
		r+= '"<label for="'+o.name+'">'+o.title+'</label><textarea name="'+o.name+'" placeholder="'+o.placeholder+'"></textarea></div>';
		return r;
	},
	choice:function(){
		var r='',it = 'text',o=$.extend({
			name:'name',
			title:'Label',
			items:[],
			required: false
		},arguments.length?arguments[0]:{});
		r = '<div class="-com-form-input'+((o.required)?' -com-input-required':'')+'>';
		r+= '"<label for="'+o.name+'">'+o.title+'</label><input type="hidden" name="'+o.name+'"/>';
		r+= '<ul class="-com-input-choice">';
		for(var i in o.items){
			r+= '<li><a href="'+o.items[i].action+'">'+o.items[i].option+'</a></li>';
		}
		r+= '</ul>';
		//'<textarea name="'+o.name+'" placeholder="'+o.placeholder+'"></textarea></div>';
		return r;
	}
};
var user = function(d){
    var example = {
        "id": 5,
        "name": "yanusdnd@inbox.ru",
        "email": "yanusdnd@inbox.ru",
        "created_at": "2017-03-16 08:23:54",
        "updated_at": "2017-03-16 12:00:14",
        "phone": "+79265766710",
        "middlename": null,
        "lastname": null,
        "birthdate": null,
        "passport_series": "4512",
        "passport_number": "703670",
        "passport_issue": "",
        "passport_date": "2012-06-26"
    };
    for(var i in d){
        var val = d[i];
        if(val!=null)$("textarea[name='user_"+i+"'],select[name='user_"+i+"'],input[name='user_"+i+"']").val(val);
    }
}
var items = function(d){
    var items = $("#_com_items"),f='',t=0;
    items.html('');
    for(var i in d.items){
        var p=d.items[i];
        //console.debug("Item:");
        //console.debug(p);
        f+='<tr>';
        f+='<td style="width:55%"><b>'+p.brand+'</b><br/>'+p.title+'</td>';
        f+='<td style="width:15%">'+priceNumber(p.price)+'</td>';
        f+='<td style="width:10%">'+(p.quantity)+'</td>';
        f+='<td style="width:20%"><b>'+priceNumber(p.price*p.quantity)+'</b></td>';
        f+='</tr>';
        t+=p.price*p.quantity;
    }
    f+='<tr>';
    f+='<td style="width:55%">&nbsp;</td>';
    f+='<td style="width:15%">&nbsp;</td>';
    f+='<td style="width:10%"><h5>Итог</h5></td>';
    f+='<td style="width:20%"><h5>'+priceNumber(t)+'</h5></td>';
    f+='</tr>';
    items.append('<table>'+f+'</table>');
    itemsFilled = true;
};
var checkout = function(order){
    console.debug(order);
    if(order.status_id!="4")items(order);
    var fillable = ['id','amount','currency','status_id','response_url','user_id','payment_id','delivery_id','service_fee','card_ref','shipping_fee','shipping_tracker','raw_request']
    $("#_com .-com-modal-header h2").html("Заказ #"+order.id);
    $("#_com #_com_form").attr("data-rel",cohost+"/update");//.animate({left:-4000},400,function(){$(this).html('').css("left","4000");});
    var form = $("#_com #_com_form"),f = '';
    if(typeof(order.user_id)!="undefined"&&order.user_id!=null&&order.user_id!="null")$.get(cohost+"/user/"+order.user_id,function(d){
        user(d);
    });
    if(order.status_id == "0" ){
        $("#_com .-com-modal-header h4").html("Контактные данные");
        f+= '<div class="-com-row"><div class="-com-col -com-col-10 -com-offset-1"><h3>Координаты для связи</h3></div></div>';
        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-10 -com-offset-1">'+page.input({type:'email',name:'user_email',title:'E-Mail',placeholder:'email',required:true})+'</div>';
        f+= '<div class="-com-col -com-col-10 -com-offset-1">'+page.input({type:'phone',name:'user_phone',title:'Телефон',placeholder:'телефон',required:true})+'</div>';
        f+= '</div>';
    }
    else if(order.status_id == "1"){
        $("#_com .-com-modal-header h4").html("Способ доставки");

        var el = document.createElement("script");el.setAttribute("src","//points.boxberry.de/js/boxberry.js");document.body.append(el); // add boxberry api

        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-10 -com-offset-1">'+page.choice({name:'delivery_id',title:'Выберете способ доставки',required:true,items:[
			{action:'javascript:{boxberry.open(boxberryCallback,\''+boxberryToken+'\',\'Москва\',\'77461\',0)}',option:'До пункта выдачи BoxBerry'}
			//,{action:'javascript:{$(\'input[name=delivery_id]\').val(2)}',option:'Курьером'}
		]})+'</div></div>';
    }//deliverytype
    else if(order.status_id == "2"){ //paymenttype
        $("#_com .-com-modal-header h4").html("Паспортные данные");
        f+= '<div class="-com-row"><div class="-com-col -com-col-12"><h4>Паспорт</h4></div></div>';
        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_name',title:'Имя',placeholder:'Ваше имя, как в паспорте',required:true})+'</div>';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_middlename',title:'Отчество',placeholder:'Ваше отчество, как в паспорте',required:true})+'</div>';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_lastname',title:'Фамилия',placeholder:'Ваша фамилия, как в паспорте',required:true})+'</div>';
        f+= '</div>';
        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_passport_series',title:'Серия',placeholder:'99 99',required:true})+'</div>';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_passport_number',title:'Номер',placeholder:'999 999',required:true})+'</div>';
        f+= '<div class="-com-col -com-col-4">'+page.input({name:'user_passport_date',title:'Дата',required:true})+'</div>';
        f+= '</div>';
        f+= '<div class="-com-row"><div class="-com-col -com-col-12">'+page.textarea({name:'user_passport_issue',title:'Выдан',required:true,placeholder:'Кем выдан'})+'</div>';
    }
    else if(order.status_id == "3"){ //paymenttype
        $("#_com #_com_form").attr("data-rel",cohost+"/pay");
        $("#_com .-com-modal-header h4").html("Способ оплаты");
        if(typeof(queryParameters.status)!="undefined" && queryParameters.status=="declined" && typeof(queryParameters.error_message)!="undefined"){
            f+= '<div class="-com-row">';
            f+= '<div class="-com-col-10 -com-offset-1 -com-alert-message">';
            f+= '<h4>Ошибка#'+(typeof(queryParameters.error_code)?queryParameters.error_code:"-000")+'</h4>';
            f+= queryParameters.error_message
            f+= '</div></div>';
            f+= '<div class="-com-col-10 -com-offset-1 -com-message">Попробуйте воспользоваться другой картой или выбрать другой способ платежа</div></div>';
        }

        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-10 -com-offset-1">'+page.choice({name:'payment_id',title:'Выберете способ оплаты',items:[
			{action:'javascript:{$(\'input[name=payment_id]\').val(1);$(\'#_com_main_form_btn_submit\').click();}',option:'Оплата после доставки'},
			{action:'javascript:{$(\'input[name=payment_id]\').val(2);$(\'#_com_main_form_btn_submit\').click();}',option:'Оплатить онлайн'}
		]})+'</div></div>';
    }
    else if(order.status_id == "4.1"){ //choose card
        $("#_com .-com-modal-header h4").html("Выберете карту для оплаты");
        $("#_com #_com_form").attr("data-rel",cohost+"/pay");
        f+= '<div class="-com-row">';
		var par = {name:'card_id',title:'Выберете карту для оплаты',required:true,items:[]};
		for(var i in order.cards){
			var card = order.cards[i];
            par.items.push({action:'javascript:{$(\'input[name=card_id]\').val('+card.id+');$(\'#_com_main_form_btn_submit\').click();}',option:card.name});
		}
        f+= '<div class="-com-col -com-col-10 -com-offset-1">'+page.choice(par)+'</div></div>';
    }
    else if(order.status_id == "4.2"){ //add card redirect
        if(typeof(order["redirect-url"])!="undefined"){
            document.location.href=order["redirect-url"];
        }
    }
    else if(order.status_id == "4"){//thanks page & cheque
        cart.empty();
        var d  =new Date();
        $("#_com .-com-modal-header h4").html("Заказ оформлен.");
        $("#_com_main_form_btn_submit").attr('onclick','$("#_com").hide();').text('Закрыть');
        f+= '<div class="-com-row"><div class="-com-col -com-col-12 -com-message"><h4>Заказ успешно оформлен</h4></div></div>';
        f+= '<div class="-com-row">';
        f+= '<div class="-com-col -com-col-10 -com-offset-1">';
        f+= '<b> Чек операции:</b><br/><pre style="width:26em;margin:0 auto;padding:1em 2em;overflow:none;font-size:120%;">';
        f+= '<b>'+queryParameters.descriptor;//'CREATIVE SPACES (EAST SUSSEX) LTD 14 Hackwood, Robertsbridge, East Sussex,TN32 5ER, England and Wales';
        f+= '</b><br/>-------------------------------------';
        f+= '<br/>Дата операции:<span style="float:right;font-weight:700">'+((typeof(queryParameters.tx_date)!="undefined")?queryParameters.tx_date:d.toDateString())+'</span>';
        f+= '<br/>Номер заказа:<span style="float:right;font-weight:700">'+order.id+'</span>';
        f+= '<br/>Терминал:<span style="float:right;font-weight:700">'+((typeof(queryParameters.endpoint)!="undefined")?queryParameters.endpoint:"----")+'</span>';
        f+= '<br/>Магазин:<span style="float:right;font-weight:700">'+document.location.host+'</span>';
        f+= '<br/>Карта:<span style="float:right;font-weight:700">'+queryParameters.bin+'xxxxxx'+queryParameters["last-four-digits"]+'</span>';
        f+= '<br/>Сумма (руб.):<span style="float:right;font-weight:700">'+((typeof(queryParameters.amount)!="undefined")?queryParameters.amount:1)+'</span>';
        f+= '<br/>Код авторизации:<span style="float:right;font-weight:700">'+queryParameters["processor-tx-id"]+'</span>';
        f+= '<br/>-------------------------------------';
        f+= '</pre>';
        f+= '</div>';
        f+= '</div>';
    }
    //console.debug(f);
    form.html(f).animate({left:0},400);
    //form.append('<div class="-com-row"><div class="-com-col -com-col-4">4 of 12</div><div class="-com-col -com-col-4">4 of 12</div><div class="-com-col -com-col-4">4 of 12</div></div>');
    for(var i in order){
        if(fillable.indexOf(i)!=-1 && order[i]!="null" && order[i]!=null && i!="raw_request") form.append('<input type="hidden" name="'+i+'" value="'+order[i]+'"/>');
    }

    //form.append('<input type="hidden" name="amount" value="'+order.amount+'"/>');
    //if(typeof(order.user_id)!="undefined")form.append('<input type="hidden" name="amount" value="'+order.user_id+'"/>');
};
window.globalAjaxInjection = {
	matcher:/^\/cart\/.*$/i,
	cartbefore:function(){
		$(".xray-header-basket__count").html("<img src=\"/css/loader.gif\" style=\"height:24px;margin-left:24px;\"/>");
	},
	cartget:function(d){
		//var s = "("+d.cart.itemsAmount+")шт. "+d.cart.amountGross;
		//var s = ((typeof(d.cart.itemsAmount)=="undefined")?"0":d.cart.itemsAmount)+"шт. "+((typeof(d.cart.amountGross)=="undefined")?"0 &#8381;":d.cart.amountGross);
		var s = d.miniBasket;
		$(".xray-header-basket__count").html(s);
		$(".ajax-cart__action-link").attr("href","/basket").html("Оформить заказ");
		$(".ajax-cart__action-link--basket").hide();
	}
};
/*
window.items = items;
window.user = user;
window.page = page;
window.checkout = checkout;
window.priceNumber = priceNumber;
window.cohost = cohost;
*/
function xrayInit(){
	if(typeof($)=="undefined"){
		console.warn("jQuery not yet loaded.")
		setTimeout(xrayInit,1000);
	}
	else {
		console.clear();
		console.debug("jquery is here: "+typeof($));
		/*!
		 * jQuery Cookie Plugin v1.4.1
		 * https://github.com/carhartl/jquery-cookie
		 *
		 * Copyright 2006, 2014 Klaus Hartl
		 * Released under the MIT license
		 */
		// (function (factory) {
		// 	if (typeof define === 'function' && define.amd) {
		// 		// AMD (Register as an anonymous module)
		// 		define(['jquery'], factory);
		// 	} else if (typeof exports === 'object') {
		// 		// Node/CommonJS
		// 		module.exports = factory(require('jquery'));
		// 	} else {
		// 		// Browser globals
		// 		factory(jQuery);
		// 	}
		// }
		// (function ($) {

			var pluses = /\+/g;

			function encode(s) {
				return config.raw ? s : encodeURIComponent(s);
			}

			function decode(s) {
				return config.raw ? s : decodeURIComponent(s);
			}

			function stringifyCookieValue(value) {
				return encode(config.json ? JSON.stringify(value) : String(value));
			}

			function parseCookieValue(s) {
				if (s.indexOf('"') === 0) {
					// This is a quoted cookie as according to RFC2068, unescape...
					s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
				}

				try {
					// Replace server-side written pluses with spaces.
					// If we can't decode the cookie, ignore it, it's unusable.
					// If we can't parse the cookie, ignore it, it's unusable.
					s = decodeURIComponent(s.replace(pluses, ' '));
					return config.json ? JSON.parse(s) : s;
				} catch(e) {}
			}

			function read(s, converter) {
				var value = config.raw ? s : parseCookieValue(s);
				return $.isFunction(converter) ? converter(value) : value;
			}

			var config = $.mcookie = function (key, value, options) {

				if (arguments.length > 1 && !$.isFunction(value)) {
					options = $.extend({}, config.defaults, options);

					if (typeof options.expires === 'number') {
						var days = options.expires, t = options.expires = new Date();
						t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
					}

					return (document.cookie = [
						encode(key), '=', stringifyCookieValue(value),
						options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
						options.path    ? '; path=' + options.path : '',
						options.domain  ? '; domain=' + options.domain : '',
						options.secure  ? '; secure' : ''
					].join(''));
				}

				// Read

				var result = key ? undefined : {},
					// To prevent the for loop in the first place assign an empty array
					// in case there are no cookies at all. Also prevents odd result when
					// calling $.mcookie().
					cookies = document.cookie ? document.cookie.split('; ') : [],
					i = 0,
					l = cookies.length;

				for (; i < l; i++) {
					var parts = cookies[i].split('='),
						name = decode(parts.shift()),
						cookie = parts.join('=');

					if (key === name) {
						// If second argument (value) is a function it's a converter...
						result = read(cookie, value);
						break;
					}

					// Prevent storing a cookie that we couldn't decode.
					if (!key && (cookie = read(cookie)) !== undefined) {
						result[name] = cookie;
					}
				}

				return result;
			};

			config.defaults = {};

			$.removeCookie = function (key, options) {
				// Must not alter options, thus extending a fresh object...
				$.mcookie(key, '', $.extend({}, options, { expires: -1 }));
				return !$.mcookie(key);
			};

		// }));

		$(document).ready(function(){
		    $(".ajax-cart__action-link").attr("href","/basket").html("Оформить заказ");
			$("button:contains('In den Warenkorb')").html("В корзину");
		    $("body").on("co:checkout",function(e,rorder){checkout(rorder);});
		    $(".cart-actions__checkout-link").attr("href","javascript:{0}").html("Оформить заказ").on("click",function(e){
		        jscontent = $("#_com");
		        e.preventDefault();
		        e.stopPropagation();
		        $(".overlay").hide();
		        $(".show-ajax-cart").hide();
		        var createData = cart.parse();
		        if(!jscontent.length){
		            $.get(cohost+'/com.html',function(d){
		                //console.debug(d);
		                $("body").append(d);
		                var cookie = cart.get();
		                if(cookie!=null && cart.compare(cookie,createData) && typeof(cookie.id)!="undefined"){
		                    order = cookie;
		                    $("body").trigger("co:checkout",order);
		                }else{
		                    cart.set(createData);
		                    $.ajax({
		                        url:cohost+"/create",
		                        crossDomain:true,
		                        data:createData,
		                        success:function(d){
		                            var order = cart.set(d);
		                            $("body").trigger("co:checkout",order);
		                        }
		                    });
		                }
		                $("body").trigger("co:loaded");
		            });
		        }else {$("#_com").show();}
		        return false;
		    });
		    if(typeof(queryParameters._com_response)!="undefined"){
		        cart.set({status_id:(typeof(queryParameters.status_id)?queryParameters.status_id:"3")});
		        $(".cart-actions__checkout-link").click();
		    }

			$(document).ajaxSend(function(e,x,j) {
				if(globalAjaxInjection.matcher.test(j.url)){
					globalAjaxInjection.cartbefore();
				}
			});
			$(document).ajaxSuccess(function(e,x,j,d) {
				console.debug(e,x,j,d);
				if(globalAjaxInjection.matcher.test(j.url)){
					globalAjaxInjection.cartget(d);
				}
			});
			//$.getJSON("/cart/get");

		});
	}
}
if(typeof($)=="undefined")setTimeout(xrayInit,1000);
