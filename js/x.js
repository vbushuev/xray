"use strict";
var xG = {
    ajax:function(){
        if(!arguments.length)return;
        var o = {
            url:"/",
            type:"get",
            data:{},
            success:function(){},
            complite:function(){},
            error:function(){}
        };
        o = this._extend(o,arguments[0]);
        var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
        var r = new XHR();
        var uriparams = (o.url.match(/\?/)?"&":"?")+Object.keys(o.data).map(function(k) {return encodeURIComponent(k) + '=' + encodeURIComponent(o.data[k])}).join('&');
        r.open(o.type.toUpperCase(), o.url, true);
        r.onreadystatechange = function () {
            if (r.readyState != 4 || r.status != 200) {
                o.error(r);
                return;
            }
            else o.success(r);
            o.complite(r);
        };

        r.send(o.data);
    },
    checkout:function(){
        var orderId = 'BA'+Math.random(9),
            carthost = (document.location.hostname.match(/\.bs2/i))?"//cart.gauzymall.bs2":"https://cart.gauzymall.com",
            checkouthost = "https://checkout.gauzymall.com",//(document.location.hostname.match(/\.bs2/i))?"//checkout.gauzymall.bs2":"https://checkout.gauzymall.com",
            items = arguments[0];
        var rq = {
            domain_id:8,
            version: "1.0",
            response_url: carthost+"/clean?id="+orderId,
            order:{
                order_id:orderId,
                order_url:document.location.href,
                order_total:0.0,
                order_currency:"EUR",
                items:[]
            }
        },$m = this.find("#garan24-overlay")[0];
        var tot = 0;
        for(var i=0;i<items.length;++i ) {
            var itm = items[i];
            if(typeof itm.product_id == "undefined"){
                if((typeof itm.shop != "undefined")&&(typeof itm.sku != "undefined")){
                    itm.product_id = itm.shop+"_"+itm.sku;
                }
                else itm.product_id = -1;
            }
            //delete itm.variations;
            //delete itm.currency;
            //delete itm.shop;
            //delete itm.sku;
            rq.order.items.push(itm);
            tot+=itm.sale_price*itm.quantity;
            //console.debug(itm);
        }
        rq.order.order_total = tot;
        //$(document).trigger('gcart:beforeCheckout',rq);
        //return ;
        this.ajax({
            type:"POST",
            //url:"//service.garan24.ru/checkout/",
            //url:"http://service.garan24.bs2/checkout/",
            url:checkouthost,
            dataType: "json",
            data:JSON.stringify(rq).replace(/\'/,""),
            beforeSend:function(){
                //if($("#garan-cart-full").hasClass("garan24-visible")){$("#garan-cart").click();}
                //$m.find("#garan24-overlay-message").show();
                //$m.find(".garan24-overlay-message-text").html("Обрабатываются товары из Вашей корзины...");
                //$m.fadeIn();
            },
            complete:function(){
                //$m.delay(4).fadeOut();
                //$(document).trigger('gcart:checkout',rq);
            },
            success:function(data){
                //var d=JSON.parse(data);
                var d=JSON.parse(data.response);
                console.debug("checkout response: ");
                console.debug(d);

                if(!d.error){
                    //$m.find(".garan24-overlay-message-text").html("Переход на страницу оформления заказа...");

                    //document.location.href=garan.cart.isframe?d.redirect_url:"//gauzymall.com/g24-checkout?order-id="+d.id;
                    if(confirm('Переходим на checkout?'))
                    document.location.href = d.redirect_url;
                }
            },
            error:function(x,s){
                //$m.find(".garan24-overlay-message-text").html("Неудалось обработать корзину ["+s+"]");
            }
        });
    },
    insertGreenLine:function(html){
        var gl = document.createElement('div');
        gl.innerHTML = html;
        //document.body.style.marginTop = "50px";
        this.margin({obj:document.body.style,itr:4,interval:5,to:50,callback:function(){
            document.body.appendChild(gl);
            var js = html.replace(/[\s\S]*<script.*?>([\s\S]+?)<\/script>[\s\S]*/ig,"$1");
            eval(js);
        }});

    },
    currency:{
        _multiplier:1.05,
        get:function(){
            if(arguments.length<2)return;
            var pe = arguments[0],cur = arguments[1];
            xG.ajax({
                url:"https://l.gauzymall.com/currency",
                success:function(r){
                    //console.debug(r.responseText);
                    var d = eval(r.responseText);
                    var m = 1;
                    for(var i=0;i<d.length;++i){
                        if(d[i].iso_code == cur){
                            m = d[i].value*xG.currency._multiplier;
                            break;
                        }
                    }
                    for(var i=0;i<pe.length;++i){
                        var p = pe[i];
                        var cp = p.cloneNode();
                        var text  = p.innerHTML.replace(/,/,".").replace(/[^\.\d]+/ig,"");
                        cp.innerHTML = (text*m).toFixed(2)+"руб.";
                        p.parentNode.appendChild(cp);
                        p.style.display = 'none';
                    }
                }
            });
        }
    },
    fadeOut:function(opt){
        //var s = document.getElementById('thing').style;
        opt.obj.opacity = 1;
        (function fade(){(s.opacity-=.1)<0?s.display="none":setTimeout(fade,40)})();
    },
    margin:function(opt){
        xG._option = opt;
        xG._margin();
    },
    find:function(){
        if(!arguments.length)return;
        var e = xG._getElement(arguments[0]);
        if(!Array.isArray(e)&&e.constructor.name!="HTMLCollection")e = [e];
        return e;
    },
    hide:function(){
        if(!arguments.length)return;
        var e = this._getElement(arguments[0]);
        if(!Array.isArray(e)&&e.constructor.name!="HTMLCollection")e = [e];
        for (var i=0;i<e.length;++i) {
            if(typeof e[i]!="undefined" && e[i]!=null) e[i].style.display = 'none';
        }
    },
    hide2:function(){
        if(!arguments.length)return;
        var o = arguments[0],e = this._getElement(arguments[0]);
        if(!Array.isArray(e)&&e.constructor.name!="HTMLCollection")e = [e];
        for (var i=0;i<e.length;++i) {
            e[i].style.display = 'none';
        }
    },
    _getElement:function(){
        if(!arguments.length)return;
        var o = arguments[0],r=[];
        if(typeof o == "string" && o.length){
            var a = o.split(/,/);
            for (var i = 0; i < a.length; i++) {
                if(a[i].match(/^\s*\..+/)){r.concat(document.getElementsByClassName(a[i].replace(/^\s*\.(\S+)/,"$1")));}
                else if(a[i].match(/^\s*#.+/)){r.push(document.getElementById(a[i].replace(/^\s*#(\S+)/,"$1")));}
                else if(a[i].match(/^\s*[a-z0-9\-_]+/i)){r.concat(document.getElementsByTagName(a[i].replace(/(\S+)/,"$1")));}
            }
        }
        console.debug(r);
        return r;
    },
    _getElement2:function(){
        if(!arguments.length)return;
        var o = arguments[0];
        if(typeof o.id != "undefined" && o.id.length) return document.getElementById(o.id);
        if(typeof o.tag != "undefined" && o.tag.length) return document.getElementsByTagName(o.id);
        if(typeof o.class != "undefined" && o.class.length)return document.getElementsByClassName(o.class);
        return [];
    },
    _margin:function(){
        if(typeof xG._option._step == "undefined"){xG._option._step = 0;}
        else if(xG._option._step>=999){return;}
        if(xG._option.obj.marginTop == "")xG._option.obj.marginTop=0;
        if((parseInt(xG._option.obj.marginTop)+xG._option.itr) < xG._option.to){
            xG._option.obj.marginTop=parseInt(xG._option.obj.marginTop)+xG._option.itr+"px";
            xG._option._step++;
            setTimeout(function(){xG._margin();},xG._option.interval);
        }
        else xG._option.callback();
    },
    extend:function(){
        this._option = this._extend(this._option,arguments[0]);
    },
    _extend:function () {
        // Variables
        var extended = {},deep = false,i = 0,length = arguments.length;
        // Check if a deep merge
        if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
            deep = arguments[0];
            i++;
        }

        // Merge the object into the extended object
        var merge = function (obj) {
            for ( var prop in obj ) {
                if ( Object.prototype.hasOwnProperty.call( obj, prop ) ) {
                    // If deep merge and property is an object, merge properties
                    if ( deep && Object.prototype.toString.call(obj[prop]) === '[object Object]' ) {
                        extended[prop] = extend( true, extended[prop], obj[prop] );
                    } else {
                        extended[prop] = obj[prop];
                    }
                }
            }
        };

        // Loop through each object and conduct a merge
        for ( ; i < length; i++ ) {
            var obj = arguments[i];
            merge(obj);
        }

        return extended;

    },
    loadScript:function( src ) {
        var s = document.createElement( 'script' );
        s.setAttribute( 'src', src );
        document.body.appendChild( s );
    },
    _option:{}
    //protected functions
};
window.xG = xG;
(function(){
    xG.ajax({
        url:"//l.gauzymall."+(document.location.host.match(/\.bs2/)?"bs2":"com")+"/xray",
        data:{site:document.location.host.match(/brandalley/i)?"brandalley":"ctshirts"},
        success:function(r){
            console.debug(r);
            xG.insertGreenLine(r.responseText);
            xG.currency.get(".price_stroke,");
            "price_unitaire"
            ".price_unitaire > div > span"
        }
    });
    xG.hide(".wishlist_part,#account-wishlist-lnk,#account-user-group");
    //xG.hide({id:"account-wishlist-lnk"});
    //xG.hide({id:"account-user-group"});
    //xG.hide({class:"wishlist_part"});
})()
