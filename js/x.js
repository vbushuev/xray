"use strict";
var xG = {
    ajax:function(){
        if(!arguments.length)return;
        var o = {
            url:"/",
            type:"get",
            data:{},
            beforeSend:function(){},
            success:function(){},
            complite:function(){},
            error:function(){}
        };
        o = this._extend(o,arguments[0]);
        o.beforeSend(r);
        var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
        var r = new XHR();
        var uriparams = (o.url.match(/\?/)?"&":"?")+Object.keys(o.data).map(function(k) {return encodeURIComponent(k) + '=' + encodeURIComponent(o.data[k])}).join('&');
        if(o.type=="get")r.open(o.type.toUpperCase(), o.url+uriparams, true);
        else r.open(o.type.toUpperCase(), o.url, true);
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
        var orderId = 'X'+Math.random(9),
            carthost = (document.location.hostname.match(/\.bs2/i))?"//cart.gauzymall.bs2":"https://cart.gauzymall.com",
            checkouthost = "https://checkout.gauzymall.com",//(document.location.hostname.match(/\.bs2/i))?"//checkout.gauzymall.bs2":"https://checkout.gauzymall.com",
            items = arguments[0],
            lc = {},
            localCookies = document.cookie.split(/;/);
        for(var i=0;i<localCookies.length;++i){
            var cookie = localCookies[i].split(/\=/);
            lc[cookie[0].trim()]=encodeURIComponent(cookie[1]);
        }
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
            },
            session:{

                cookies: lc
                //,localStorage:(typeof(window.localStorage)!="undefined")?JSON.stringify(window.localStorage):{}
                //,sessionStorage:typeof(window.sessionStorage)?JSON.stringify(window.sessionStorage):{}
            }
        },$m = this.find("#garan24-overlay")[0];
        var tot = 0,shop;
        for(var i=0;i<items.length;++i ) {
            var itm = items[i];
            if(typeof itm.product_id == "undefined"){
                if((typeof itm.shop != "undefined")&&(typeof itm.sku != "undefined")){
                    itm.product_id = itm.shop+"_"+itm.sku;
                }
                else itm.product_id = -1;
            }
            rq.order.order_currency = itm.currency;
            shop = itm.shop;
            rq.order.items.push(itm);
            tot+=itm.sale_price*itm.quantity;
        }
        rq.session.shop = shop;
        rq.order.order_total = tot;
        var loaderLayer = document.createElement("div");
        loaderLayer.setAttribute("style","position:fixed;top:0;left:0;background-color: rgba(255,255,255,.94);width:100%;height:100%;z-index: 9999;transition: opacity 0.8s ease-in-out;");
        var loaderGif = document.createElement("img");
        loaderGif.setAttribute("src","/css/loader.gif");
        loaderGif.setAttribute("style","position:fixed;top:0;left:-64px;margin: 30% 50%;z-index: 10000;");
        loaderLayer.appendChild(loaderGif);

        this.ajax({
            type:"POST",
            url:checkouthost,
            dataType: "json",
            data:JSON.stringify(rq).replace(/\'/,""),
            beforeSend:function(){
                console.debug("add layer");
                console.debug(loaderLayer);
                document.body.appendChild(loaderLayer);
            },
            complete:function(){
                loaderLayer.style.display = "none";
            },
            success:function(data){
                //var d=JSON.parse(data);
                var d=JSON.parse(data.response);
                console.debug("checkout response: ");
                console.debug(d);

                if(!d.error){
                    //if(confirm('Переходим на checkout?'))
                    document.location.href = d.redirect_url;
                }
            },
            error:function(x,s){
                //$m.find(".garan24-overlay-message-text").html("Неудалось обработать корзину ["+s+"]");
            }
        });
    },
    insertGreenLine:function(html,topelement){
        try{
            var gl = document.createElement('div');
            gl.innerHTML = html;
            if(document.getElementById('main')!=null){
                document.getElementById('main').style.transition = "all .4s ease-in";
                document.getElementById('main').style.marginTop = "50px";
                if(document.getElementById('header')!=null)document.getElementById('header').style.transition = "all .4s ease-in";
                if(document.getElementById('header')!=null)document.getElementById('header').style.top = "50px";
            }
            else{
                document.body.style.transition = "all .4s ease-in";
                document.body.style.marginTop = "50px";
            }
            //setTimeout(function(){
                document.body.appendChild(gl);
                var js = html.replace(/[\s\S]*<script.*?>([\s\S]+?)<\/script>[\s\S]*/ig,"$1");
                try{
                    eval(js);
                }
                catch(e){console.clear();console.error(e);}

            //},400);
            //this.margin({obj:document.body.style.marginTop,itr:2,interval:5,to:50,callback:function(){}});
        }
        catch(e){
            console.error(e);
        }
    },
    currency:{
        _multiplier:1.05,
        rates:false,
        rateReplace:function(){
            if(arguments.length<2)return;
            var pe = arguments[0],cur = arguments[1];
            var m = 1;
            for(var i=0;i<xG.currency.rates.length;++i){
                if(xG.currency.rates[i].iso_code == cur){
                    m = xG.currency.rates[i].value*xG.currency._multiplier;
                    break;
                }
            }
            for(var i=0;i<pe.length;++i){
                var p = pe[i];
                if(p==null || typeof p == "undefined") continue;
                if(xG.hasClass(p,'xg_converted'))continue;
                var cp = p.cloneNode();
                var preInner = p.innerHTML.replace(/,/,".").replace(/^[\r\n\s\t]+/,"").replace(/[\r\n\s]+$/,"");
                var text  = preInner.replace(/(.*?)(\d+\.\d+)[\s\S]*$/i,"$2");
                //console.debug("search price in "+preInner+" >> "+text);
                if(!isNaN(text*m)){
                    cp.innerHTML = "&nbsp;"+(text*m).toFixed(2)+"руб.";
                    p.parentNode.appendChild(cp);
                    p.style.display = 'none';
                    p.classList.add('xg_converted');
                    cp.classList.add('xg_converted');
                }
            }
        },
        get:function(){
            if(arguments.length<2)return;
            var pes = arguments[0],curs = arguments[1];
            if(xG.currency.rates==false){
                xG.ajax({
                    url:"https://l.gauzymall.com/currency",
                    success:function(r){
                        xG.currency.rates = eval(r.responseText);
                        xG.currency.rateReplace(pes,curs);
                    }
                });

            }else xG.currency.rateReplace(pes,curs);

        }
    },
    isMobile:{
        Android: function() {return navigator.userAgent.match(/Android/i);},
        BlackBerry: function() {return navigator.userAgent.match(/BlackBerry/i);},
        iOS: function() {return navigator.userAgent.match(/iPhone|iPad|iPod/i);},
        Opera: function() {return navigator.userAgent.match(/Opera Mini/i);},
        Windows: function() {return navigator.userAgent.match(/IEMobile/i);},
        any: function() {return (xG.isMobile.Android() || xG.isMobile.BlackBerry() || xG.isMobile.iOS() || xG.isMobile.Opera() || xG.isMobile.Windows());}
    },
    fadeOut:function(opt){
        //var s = document.getElementById('thing').style;
        opt.obj.opacity = 1;
        (function fade(){(s.opacity-=.1)<0?s.display="none":setTimeout(fade,40)})();
    },
    hasClass:function(element, className) {
        return !(!className || !element || !element.className
        || !element.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)')));
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
                if(a[i].match(/^\s*#.+/)){r.push(document.getElementById(a[i].replace(/^\s*#(\S+)/,"$1")));}
                else if(a[i].match(/^\s*\..+/)){
                    var e= document.getElementsByClassName(a[i].replace(/^\s*\.(\S+)/,"$1"));
                    for (var j=0;j<e.length;++j) r.push(e[j]);
                }
                else if(a[i].match(/^\s*[a-z0-9\-_]+/i)){
                    var e= document.getElementsByTagName(a[i].replace(/(\S+)/,"$1"));
                    for (var j=0;j<e.length;++j) r.push(e[j]);
                }
            }
        }
        //console.debug(r);
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
        if(xG._option.obj == "")xG._option.obj=0;
        if((parseInt(xG._option.obj)+xG._option.itr) <= xG._option.to){
            xG._option.obj=parseInt(xG._option.obj)+xG._option.itr+"px";
            xG._option._step++;
            setTimeout(function(){xG._margin();},xG._option.interval);
        }
        else if(typeof xG._option.callback!="undefined")xG._option.callback();
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
    var _site = "ctshirts";
    if(document.location.host.match(/g\-ba/i))_site="brandalley";
        //_site="ctshirts";
    console.debug(_site);
    xG.ajax({
        url:"//l.gauzymall."+(document.location.host.match(/\.bs2/)?"bs2":"com")+"/xray",
        //url:"//l.gauzymall.com/xray",
        data:{site:_site},
        success:function(r){
            //var marginElement = (_site=="ctshirts")?document.getElementById('header').style.marginTop:document.body.style.marginTop;
            var marginElement = document.body.style.marginTop;
            xG.insertGreenLine(r.responseText,marginElement);
        },
        complite:function(){
            var loader = document.getElementById('xr_g_cover_layer');
            loader.style.transition="all .4s ease-in";
            loader.style.opacity="0";
            loader.style.display="none";
            loader.style.zIndex="-1";

            setTimeout(function(){loader.parentNode.removeChild(loader);},2000);
        }
    });
})()

/*
XMLHttpRequest.prototype.send = (function(orig){
    return function(){
        console.debug('pre ajax '+ this._HREF);

        if (!/MSIE/.test(navigator.userAgent)){
            this.addEventListener("loadend", function(){console.debug('ajax complete');}, false);
        } else {
            var xhr = this,
            waiter = setInterval(function(){
                if(xhr.readyState && xhr.readyState == 4){
                    console.debug('for IE ajax '+ this._HREF);
                    clearInterval(waiter);
                }
            }, 50);
        }

        return orig.apply(this, arguments);
    }
})(XMLHttpRequest.prototype.send);
*/
