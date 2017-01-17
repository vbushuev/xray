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
        r.open(o.type.toUpperCase(), o.url+uriparams, true);
        r.onreadystatechange = function () {
            if (r.readyState != 4 || r.status != 200) {
                o.error(r);
                return;
            }
            else o.success(r);
            o.complite(r);
        };

        r.send(uriparams);
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
    fadeOut:function(opt){
        //var s = document.getElementById('thing').style;
        opt.obj.opacity = 1;
        (function fade(){(s.opacity-=.1)<0?s.display="none":setTimeout(fade,40)})();
    },
    margin:function(opt){
        xG._option = opt;
        xG._margin();
    },
    hide:function(){
        if(!arguments.length)return;
        var o = arguments[0],e = this._getElement(arguments[0]);
        if(!Array.isArray(e)&&e.constructor.name!="HTMLCollection")e = [e];
        for (var i=0;i<e.length;++i) {
            e[i].style.display = 'none';
        }
    },
    _getElement:function(){
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
        }
    });

    xG.hide({id:"account-wishlist-lnk"});
    xG.hide({id:"account-user-group"});
    xG.hide({class:"wishlist_part"});
})()
