/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
     var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
         num = this.toFixed(Math.max(0, ~~n));

     return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

var $ = jQuery.noConflict();
$g= {
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
                o = (arguments.length>2)?arguments[2]:"",
                e = new Date();
            e.setDate(e.getDate()+(typeof o.expires!="undefined")?e.expires:9999);
            document.cookie = encodeURIComponent(n) + "=" + encodeURIComponent(v)
                + "; expires=" + e.toUTCString()
                + (typeof o.domain !="undefined" ? "; domain=" + o.domain : "")
                + (typeof o.path !="undefined" ? "; path=" + o.path : "")
                + (typeof o.secure !="undefined" ? "; secure" : "");
            return true;
        }
    }
};
window.$g = $g;
function G24(){
    var t = this;
    //this.carthost = "//service.garan24.ru/cart";
    this.carthost = (document.location.hostname.match(/\.bs2/i))?"http://service.garan24.bs2/cart":"https://service.garan24.ru/cart";
    this.id = $g.cookie.get("cart_id");
    this.currencyRate = 72*1.1; //10%
    this.cartQuantity = 0;
    this.cartAmount = 0;
    this.x_secret = "cs_ddde7647b888f21548ca27c6b80a973b20cc6091";
    this.x_key = "ck_d80d9961dfe1d5d9dcee803a6d8d674e265c9220";
    this.version = "1.0";
    this.response_url= this.carthost+"/clean?id="+this.id;
    this.order = {
        order_id:this.id,
        order_url:document.location.href,
        order_total:0,
        order_currency:"RUB",
        items:[]
    };
    this.add2cart = function(){
        console.debug("Garan24::add2cart(..)");
        if(!arguments.length){console.debug("nodata to add");return false;}
        var good = arguments[0];
        console.debug(good);
        this.cartQuantity+=good.quantity;
        this.cartAmount+=good.regular_price*good.quantity;
        this.order.order_total = this.cartAmount;
        this.order.items.push(good);
        this.setCartDigits();
        this.set();
    };
    this.remove = function(){
        console.debug("Garan24::remove from cart(..)");
        if(!arguments.length){console.debug("nodata to add");return false;}
        var i = arguments[0],good = this.order.items[i];
        console.debug(good);
        this.cartQuantity-=good.quantity;
        this.cartAmount-=good.regular_price*good.quantity;
        this.order.order_total = this.cartAmount;
        this.order.items.splice(i,1);
        this.setCartDigits();
        this.set();
    };
    this.setCartDigits = function(){
        if(!$("#garan24-cart-quantity").length){
            $("#garan-cart").html('<i class="fa fa-shopping-cart" area-hidden="true"></i><sup id="garan24-cart-quantity">0</sup><span id="garan24-cart-amount">0 руб.</span><div id="garan-cart-full"></div>');
        }
        if(!isNaN(this.cartQuantity))$("#garan24-cart-quantity").html(this.cartQuantity);
        if(!isNaN(this.order.order_total))$("#garan24-cart-amount").html(this.order.order_total.format(0,3,' ','.')+" руб.");
    };
    this.create = function(){
        $.ajax({
            url:this.carthost+"/create",
            type:"get",
            dataType: "json",
            crossDomain: true,
            success:function(data){
                //var d=JSON.parse(data);
                var d=data;
                console.debug("Created cart.");
                console.debug(d);
                G.id=d.id;
                this.id=d.id;
                $g.cookie.set("cart_id",d.id,{expires:1,domain:'.xray.bs2'});
                $g.cookie.set("cart_id",d.id,{expires:1,domain:'.xray.garan24.ru'});
            }
        });
    };
    this.set  =function(){
        var rq = {
            x_secret: "cs_ddde7647b888f21548ca27c6b80a973b20cc6091",
            x_key: "ck_d80d9961dfe1d5d9dcee803a6d8d674e265c9220",
            version: "1.0",
            response_url: "//"+document.location.hostname+"/response.php?id="+this.id,
            order:this.order
        };
        console.debug(this.order);
        //return ;
        $.ajax({
            url:this.carthost+"/update",
            type:"get",
            dataType: "json",
            crossDomain: true,
            data:{id:this.id,value:JSON.stringify(rq)},
            success:function(){console.debug("cart updated");}
        });
    };
    this.get = function(){
        var t = this;
        $.ajax({
            url:this.carthost,
            data:{id:this.id},
            type:"get",
            dataType: "json",
            crossDomain: true,
            //jsonp:false,
            beforeSend:function(x){
                console.debug("Getting data");
            },
            success:function(data){
                var d=JSON.parse(data);
                //var d=data;
                console.debug(d.order);

                if(typeof d.order != "undefined"){
                    t.order=$.extend(G.order,d.order);
                    for(var i in t.order.items){
                        var item = t.order.items[i];
                        //console.debug(item);
                        t.cartQuantity+=parseInt(((typeof item.quantity != "undefined")&&!isNaN(item.quantity))?item.quantity:0);
                    }
                }
                t.cartAmount = parseFloat( t.order.order_total);
                t.setCartDigits();
            }
        });
    };
    this.checkout = function(){
        var rq = {
            x_secret: "cs_ddde7647b888f21548ca27c6b80a973b20cc6091",
            x_key: "ck_d80d9961dfe1d5d9dcee803a6d8d674e265c9220",
            version: "1.0",
            response_url: this.carthost+"/clean?id="+this.id,
            order:this.order
        },$m = $("#garan24-overlay");
        console.debug(this.order);
        //return ;
        $.ajax({
            type:"POST",
            //url:"//service.garan24.ru/checkout/",
            //url:"http://service.garan24.bs2/checkout/",
            url:"https://service.garan24.ru/checkout",
            dataType: "json",
            data:JSON.stringify(rq),
            beforeSend:function(){
                $m.find(".garan24-overlay-message-text").html("Обрабатываются товары из Вашей корзины...");
                $m.fadeIn();
            },
            complete:function(){
                $m.fadeOut();
            },
            success:function(data){
                //var d=JSON.parse(data);
                var d=data;
                console.debug("checkout response ");
                console.debug(d);
                if(!d.error){
                    $m.find(".garan24-overlay-message-text").html("Переход на страницу оформления заказа...");
                    document.location.href = d.redirect_url;
                }
            },
            error:function(x,s){
                $m.find(".garan24-overlay-message-text").html("Неудалось обработать корзину ["+s+"]");
            }
        });
    };
    this.showcart = function(){
        var $c = $("#garan-cart-full"),g="";
        if($c.hasClass("garan24-visible")){
            $c.removeClass("garan24-visible").slideUp();
            return;
        }
        g+="<table>";
        for(var i in this.order.items){
            g+="<tr>";
            var itm = this.order.items[i];
            var vars = "";
            for(var v in itm.variations){
                vars+=v+" "+itm.variations[v];
            }
            g+="<td width='20%'><img alt='"+itm.title+"' src='"+itm.product_img+"'/></td>";
            g+="<td width='5%' style='text-align:left;'><span class='small'>х"+itm.quantity+"</span></td>";
            g+="<td width='5%'><a href='javascript:G.remove("+i+")'><i class='fa fa-trash-o'></i></a></td>";
            g+="<td width='50%' style='text-align:left;'>"+itm.title+" <span class='small'>"+vars+"</span></td>";
            g+="<td width='20%' style='text-align:right;'><span class='currency-amount'>"+parseFloat(itm.regular_price).format(0,3,' ','.')+" руб.</span></td>";
            g+="</tr>";
        }
        g+="<tr class='total'><td>Итого:</td><td colspan='4'>"+this.order.order_total.format(0,3,' ','.')+" руб.</td></tr>";
        g+="</table>";

        $c.addClass("garan24-visible").html(g).slideDown();
    };
    // init
    console.debug("Init cart - "+this.id);
    if(typeof this.id!="undefined" && this.id.length ){
        this.get();
        console.debug("Getting snooper data cart");
    }
    else {
        this.create();
        console.debug("Creating snooper cart");
    }
    $("#garan-checkout").click(function(){G.checkout();});
    $("#garan-cart").click(function(){G.showcart();});
    this.setCartDigits();

    console.debug("snooper loaded!");

    //$(".header").animate({top:"56px"},800,function(){$("#garan24-toper").slideDown();});
    $("body").animate({marginTop:"56px"},800,function(){$("#garan24-toper").slideDown();});

    var mout = function(e){$("#garan24-toper").delay(2000).slideUp();};
    /*$("body").mousemove(function(e){
        if(e.pageY<=60){
            $("#garan24-toper").slideDown().mouseover(function(e){
                console.debug("TOPER: mouse over");
                e.preventDefault();
                e.stopPropagation();
            }).mouseout(function(e){mout(e);});
        }
    });*/
    $("#usp_bar").hide();
}

function globalAdd2Cart(){
    var e = arguments[0],
        p = arguments[1],
        i = arguments[2];
    if(typeof i != "undefined"){
        i.clone()
        .css({'position' : 'fixed', 'z-index' : '999'})
        .appendTo(i)
        .animate(
            {
                opacity: 0.5,
                top: 0,
                left:$("#garan24-cart-quantity").offset().left,
                width: 50,
                height: 50
            },
            800,function() {$(this).remove();
        });
    }
    G.add2cart(p);
    e.preventDefault();
    e.stopPropagation();
    return false;
}

var collectData={
    currentDomain:document.location.hostname.split(/\./)[0].replace(/[\-]/,""),
    domainSelector:{
        "babywalz":".buBasket",
        "ctshirts":"#add-to-cart"
    },
    garanButton:$("<div id=\"garan24-add2cart-block\"><a id=\"garan24-add2cart-button\" class=\"garan24-button garan24-button-primary\" href=\"#\">В корзину&nbsp;&nbsp;<i class=\"fa fa-plus\"></i></a></div>"),
    init:function(){
        var f = (typeof collectData[collectData.currentDomain] != "undefined")?collectData[collectData.currentDomain]:collectData.default;
        collectData.garanButton.addClass("garan24-add2cart-block-"+collectData.currentDomain).unbind("click").click(function(e){f(e);});
        //collectData.replace();
    },
    replace:function(){
        var selector = (typeof collectData.domainSelector[collectData.currentDomain] != "undefined")?collectData.domainSelector[collectData.currentDomain]:"#noselector-data";
        $(selector).each(function(){
            console.debug("Add2basket button found.");
            var $t = $(this);
            var f = (typeof collectData[collectData.currentDomain] != "undefined")?collectData[collectData.currentDomain]:collectData.default;
            $t.click(function(e){
                f(e);
            });
        });
    },
    ctshirts:function(e){
        var qty = parseInt($(".price__qty-input.price__qty-input--main:first").val()), //
        amt = parseFloat($(".price.price__display.regular:first").text().replace(/\,/g,".").replace(/[^\d\.]/g,""))*G.currencyRate;
        console.debug("amt = "+ amt+" qty="+ qty);
        var varis=[];
        var ctvariations = $(".product-variations").attr("data-attributes");
        if(typeof ctvariations!="undefined"){varis[ctvariations.displayName]=ctvariations.displayValue;}
        $(".product-options .attribute--hidden").each(function(){
            var $t=$(this),
                n=$t.find(".label").text().replace(/[\r\n]/g,""),
                v=$t.find(".value select").val();
            varis[n]=v;
        });
        var p = {
            product_id:-1,
            quantity:qty,
            regular_price:amt,
            title: $(".product-name").text(),
            description: $("[itemprop=productID]:first").text().replace(/[\n\r\s]{0,}/g,""),
            product_url:document.location.href,
            product_img:$(".slick-current.slick-active a.main-image").attr("href"),
            weight:"200",
            dimensions:{
                "height":"100",
                "width":"10",
                "depth":"40"
            },
            variations:varis
        },
        i = $(".slick-current:first");
        globalAdd2Cart(e,p,i);
    },
    babywalz:function(e){
        var $p = $("#productDetail");
        var _uid = (typeof selfUID != "undefined")?selfUID:"58688";
        var price = parseFloat($p.find("#productCurrentPrice1_span").text().replace(/\,/g,"."))*G.currencyRate;
        var amount = parseInt($p.find("#productAmountForm_"+_uid).val());
        var variations = [];

        if($p.find(".productComponent_"+_uid).length){
            var v = $p.find(".productComponent_"+_uid).val();
            var k = $p.find(".productComponent_"+_uid).parent().find("label").text();
            variations[k]=v;
        }
        console.debug("Amount="+amount+" Price= "+price+" UID="+_uid);
        var product = {
            product_id: -1,
            quantity: amount,
            regular_price: price,
            title: $p.find(".prodName").text(),
            description: $p.find(".prodName").text(),//encodeURIComponent($p.find(".productCopytext").html()),
            product_url: encodeURIComponent(document.location.href),
            product_img: $p.find(".prodImage img.articleImage:first").attr("src"),//"https://youronlinestore.com/#id.png",
            weight:"200",
            dimensions:{
                "height":"100",
                "width":"10",
                "depth":"40"
            },
            variations:variations
        }
        var i = $p.find(".prodImage");
        globalAdd2Cart(e,product,i);
    },
    default:function(e){}
}
