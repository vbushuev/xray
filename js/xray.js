(function($){
    window.xray= {
        service:{
            host:(document.location.hostname.match(/\.bs2/i))?"http://xray24.bs2":"https://x.gauzymall.com"
        },
        currency:{
            _inited:false,
            multiplier:1.05,
            //multiplier:1.00,
            EUR:69.00,
            USD:62.39,
            GBP:76.00,
            RUB:1.00,
            rates:function(){
                if(!xray.currency._inited){
                    console.warn("No currency getted yet.");
                    xray.currency.get(function(){},false);
                }
                var cur = arguments.length?arguments[0]:false;
                if(!cur) cur = 'EUR';
                return ((cur == 'RUB')?1:xray.currency.multiplier)*xray.currency[cur];
            },
            get:function(){
                var cb = (arguments.length&&typeof arguments[0]=="function")?arguments[0]:function(){};
                var sync = (arguments.length>1)?arguments[1]:true;
                $.ajax({
                    url:xray.service.host+"/currency",
                    async:sync,
                    type:"get",
                    dataType: "json",
                    crossDomain: true,
                    success:function(data){
                        var d=data;
                        if(Array.isArray(d)){
                            for(var i=0; i<d.length;++i){
                                var c = d[i];
                                xray.currency[c.iso_code] = c.value;
                            }
                        }
                        cb(d);
                        xray.currency._inited = true;
                    }
                });
            },
            converter:{
                options:{
                    replacement:/[^\d\.\,]/,
                    selector:".amount",
                    currency:"EUR"
                },
                /* Convert currency values into rubles
                 * @param json Object
                 * replacement - RegEx of value search may be array
                 * selector - selector string for html elements consists values, may be array so replacement[i] is for selector[i] and so on. Also each selector can has one replacement
                 * currency - what the original currency (EUR,GBP,USD)
                 */
                action:function(){
                    if(!xray.currency._inited){
                        console.warn("No currency getted yet.");
                        xray.currency.get(function(){},false);
                    }
                    var b = xray.cookie.get("g.currency.convert"), //i don't know why
                        o = $.extend(this.options,arguments.length?arguments[0]:{}),
                        rub = '&#8381;'
                        c = xray.currency.rates(o.currency);
                    if(typeof o.selector == "string") o.selector = [o.selector];
                    for(var i=0;i<o.selector.length;++i){
                        var ss = o.selector[i]
                            rr = (typeof o.replacement == "array")?o.replacement[i]:o.replacement;
                        $(ss).each(function(){
                            var txt = $(this).text();
                            if($(this).is(":visible")&&txt.match(rr)){
                                var amt = $(this).clone();
                                amt.html($(this).text().replace(rr,function(){
                                    var m = arguments[0],
                                        r = arguments[1].replace(/[\,]/,".").replace(/\s*/,"");
                                    //console.debug(r);
                                    r = parseFloat(r)*c;
                                    return xray.number.format(r,2,3,' ','.')+" "+rub;
                                }));
                                $(this).hide();
                                amt.insertAfter($(this));
                            }
                        });
                    }
                }
            }
        },
        cookie:{
            get:function(){
                if(arguments.length<=0)return "";
                var n = arguments[0],
                    d = (arguments.length>1)?arguments[1]:"",
                    v = decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(n).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || d;
                return v;
            },
            set:function(){
                if(arguments.length<=0)return "";
                var n = arguments[0],
                    v = (arguments.length>1)?arguments[1]:"",
                    o = (arguments.length>2)?arguments[2]:{path:'/',domain:document.location.hostname},/*o.expires:9999, o.secure*/
                    e = new Date();
                e.setDate(e.getDate()+((typeof o.expires!="undefined")?o.expires:9999));
                document.cookie = encodeURIComponent(n) + "=" + encodeURIComponent(v)
                    + "; expires=" + e.toUTCString()
                    + (typeof o.domain !="undefined" ? "; domain=" + o.domain : "")
                    + (typeof o.path !="undefined" ? "; path=" + o.path : "")
                    + (typeof o.secure !="undefined" ? "; secure" : "");
                return true;
            },
            delete:function(){
                if(arguments.length<=0)return "";
                var n = arguments[0],
                    e = (arguments.length>1)?arguments[1]:{path:"/",domain:document.location.hostname};
                document.cookie = n + "=" + ((e.path) ? ";path="+e.path:"")+((e.domain)?";domain="+e.domain:"") +";expires=Thu, 01 Jan 1970 00:00:01 GMT";
            }
        },
        form:{
            submit:function(){
                var args = arguments.length?arguments[0]:{form:$("form:first")};
                if(!xray.form.required(args))return false;
                var vars= new xray.form.getvars(args);
                var buttonText = (typeof args.button != "ubdefined")?args.button.html():"submit";
                $.ajax({
                    url:args.url,
                    data:vars.json(),
                    beforeSend:function(){
                        if(typeof args.button != "ubdefined")args.button.html('<i class="fa fa-cog fa-spin fa-fw" aria-hidden="true"></i>');
                    },
                    success:function(d){
                        if(typeof args.callback=="function")args.callback(d);
                        //location.reload();
                    },
                    complete:function(){
                        if(typeof args.button != "ubdefined")args.button.html(buttonText);
                    }
                })
                return false;
            },
            goback:function(){
                var args = arguments.length?arguments[0]:{form:$("form:first")};
                var vars= new xray.form.getvars(args);
                var htmlForm = $('<form method="'+args.type+'" action="'+args.url+'">'// enctype="multipart/form-data">'
                    //+vars.html()
                    +'</form>');
                htmlForm.appendTo("body");
                console.debug("goback vars"+vars);
                htmlForm.submit();
                return false;
            },
            required:function(){
                var args = arguments.length?arguments[0]:{form:$("form:first")}, ret=true;
                console.debug("required list-group "+$(".list-group.required").length);
                console.debug("required list-group active"+$(".list-group.required").find(".list-group-item.active").length);
                args.form.find(".list-group.required").each(function(){
                    var $t = $(this), check = true,val = $t.val();
                    check = check&($t.find(".list-group-item.active").length);
                    console.debug(".list-group-item.active["+check+"] "+$t.find(".list-group-item.active").length)
                    if(!check){
                        $t.effect("shake");
                        ret = false;
                        return false;
                    }
                });
                if(!ret) return ret;
                args.form.find(".input-group.required input:visible,.input-group.required select:visible,.input-group.required textarea:visible,.input-field.required input:visible").each(function(){
                    var $t = $(this), check = true,val = $t.val(),
                        emailRegEx = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

                    check = check&(val.length>0);
                    if($t.hasClass("email"))check = check&emailRegEx.test(val);
                    if(!check){
                        //$t.hasClass("alert")?null:$t.addClass('alert');
                        $t.parent(".required").effect("shake");
                        $t.focus();
                        ret = false;
                        return false;
                    }
                });
                return ret;
            },
            getvars:function(){
                var args = arguments.length?arguments[0]:{form:$("form:first")};
                this.ret = {};
                var tt = this;
                args.form.find("input,select,textarea").each(function(){
                    var $t = $(this),n=$t.attr("name"),
                        v=($t.attr("type")=="checkbox")?($t.is(":checked")?"true":"false"):$t.val();
                    if($t.hasClass("phone")){
                        v=v.replace(/[\(\)\s]/ig,'');
                    }
                    //if(n!="undefined"&&v.length)tt.ret[n] = v;
                    if(n!="undefined")tt.ret[n] = v;
                });
                tt.json=function(){return tt.ret;};
                tt.html=function(){
                    var s="";
                    $.each(tt.ret,function(i,v){
                        s+='<input type="hidden" name="'+i+'" value="'+v+'">';
                    });
                    return s;
                };
            }
        },
        number:{
            format:function(t,n, x, s, c) {
                var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                    num = parseFloat(t).toFixed(Math.max(0, ~~n));

                return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
            }
        },
    };
})(jQuery);
