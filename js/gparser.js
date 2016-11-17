jQuery.noConflict();
(function($){
    function htmlEscape(str) {
        return str.replace(/\s*\<h3(.+)?\<\/h3\>\s*/,"").trim();
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
    var gp = {
        wc_api:{
            domain:'shop.gauzymall.com',
            key:'ck_aae4b84777ac446eb62fc0e4276a0ee7b2bbd209',
            secure:'cs_3e1c58bd4f00bdf2fc9c526318200d25dd3d4989'
        },
        _cp:0,
        _i:false,_b:false,
        l:false,
        init:function(){
            if(this._b)return;
            var t=this,cbf = (arguments.length)?arguments[0]:null;
            if(this._l)return cbf();
            this._cp = parseInt(garan.cookie.get("gparser_cp",0));
            $.getJSON("/js/s.json",function(d){
                t._i=true;
                t.l=d;
                $.getJSON("/js/categories.json",function(dc){
                    t.cat.c=dc
                    cbf();
                });
            });
            this._b=true;
        },
        find:function(){
            if(!this._i){this.init(gp.find());}
            var h=document.location.hostname.split(/\./)[0];
            return (typeof this.l[h]!="undefined" && $(this.l[h].j).length)?this.l[h]:false;
        },
        get:function(f){
            f.j=$(f.j);
            var p ={
                shop:f.s,
                //quantity:f.j.find(".b-cart_table-body_col_qty > div > input").val().replace(/\D+/,""),
                currency:'GBP',
                original_price:f.j.find(".price.price__display.regular").text().trim().replace(/[^\d\.\,]/,""),
                regular_price:f.j.find(".price.price__display.regular").text().trim().replace(/[^\d\.\,]/,""),
                title:tr.a(f.j.find(".product-name.pdp-main__name").text().trim()),
                description:tr.a(htmlEscape($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--left-group.pdp-main__slot--border-right > div.pdp-main__slot--outlined.pdp-main__slot--outlined-blue.js-slot-accordion.pdp-main__slot--accordion").html())),
                //description:tr.a(htmlEscape($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--left-group.pdp-main__slot--border-right > div.pdp-main__slot--outlined.pdp-main__slot--outlined-blue.js-slot-accordion.pdp-main__slot--accordion").html())),
                //description:tr.a(encodeURI($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--left-group.pdp-main__slot--border-right > div.pdp-main__slot--outlined.pdp-main__slot--outlined-blue.js-slot-accordion.pdp-main__slot--accordion").html())),
                product_img:f.j.find("img.pdp-main__image").attr("src"),
                images:[],
                product_url:document.location.href.replace(/xray\.bs2/,"gauzymall.com"),
                external_url:document.location.href.replace(/xray\.bs2/,"gauzymall.com"),
                sku:f.c+f.j.find(".pdp-main__number span[itemprop='productID']:first").text().trim(),
                categories:[],
                /*[
                    {id:40},//,name:"Мужская одежда"},
                    {id:"product_cat-50"},//,name:"Мужские сорочки"},
                    {id:"product_cat-51"}//,name:"Одежда"}
                ],*/
                //tags:[],//[52,53,54],
                type:"external",
                //status:"draft"
                /*variations:{
                    color:f.j.find(".b-cart_table-body_col_product-attribute.m-color .b-cart_table-body_col_product-attribute-value").text().trim(),
                    size:f.j.find(".b-cart_table-body_col_product-attribute.m-size .b-cart_table-body_col_product-attribute-value").text().trim(),
                }*/
            };
            var ii = 0;

            $("#pdpMain img.pdp-main__image").each(function(){
                var $t = $(this);
                console.debug($t.attr("src"));
                if(ii>0)p.images.push({src:$t.attr("src").replace(/\?.*$/,""),position:ii-1});
                ii++;
            });
            /*if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_a.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_a.jpg"),position:0});
            if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_b.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_b.jpg"),position:1});
            if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_c.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_c.jpg"),position:2});
            if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_d.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_d.jpg"),position:3});
            if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_e.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_e.jpg"),position:3});
            if($("img[src='"+p.product_img.replace(/_[a-z]\.jpg/,"_f.jpg")+"']").length)p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_f.jpg"),position:3});
            */
            for(i in utag_data.product_category){
                var re = new RegExp(f.f)
                console.log(re);

                console.log(utag_data.product_category[i])
                console.log(utag_data.product_category[i].replace(re,""))
                var c = this.cat.get(utag_data.product_category[i].replace(re,""));
                if(c==false){
                    console.warn("No category {"+utag_data.product_category[i]+"} matching!!!");
                }
                else p.categories.push(c);
            }
            console.log(p);
            this.s(p);
        },
        s:function(p){
            $.ajax({
                url:"//service.garan24.bs2/prod/create",
                type:"post",
                crossDomain: true,
                //contentType:'application/json',
                data: JSON.stringify(p),
                dataType:'json',
                success: function(data){
                    console.debug(data);
                    garan.cookie.set("gparser_cp",parseInt(garan.cookie.get("gparser_cp",0))+1);
                    $(document).trigger("gparser:sent");
                },
                error:function(e){}
            });
        },
        a:function(){
            var t = this;
            this.init(function(){
                var f = t.find();
                if(f!=false){
                    t.get(f);
                }
            });

        },
        cat:{
            c:false,
            get:function(c){
                for(i in this.c){
                    if(c==this.c[i].slug) return this.c[i].id
                }
                return false;
            }
        }
    };
    window.gp=gp;
    var tr = {
        init:function(){
            var t = this;
            $.getJSON("/js/dict.json",function(d){t.d=d;});
        },
        d:[],
        a:function(s){
            for(i in this.d){
                var p  = this.d[i],re = new RegExp(p.pattern);
                s = s.replace(re,p.value);
            }
            return s;
        }
    };
    window.tr=tr;


    $(document).ready(function(){
        tr.init();
        /*
        var curp = $(".tiles-container .tile");// .product-tile .product-image.tile__image > a:nth-child(1)");
        if(curp.length>2) {
            garan.cookie.set("gparser_last_page",document.location.href);
            if(garan.cookie.get("gparser_cp",0)<4)
                document.location = $(curp[garan.cookie.get("gparser_cp",0)]).find(".product-image.tile__image > a:nth-child(1)").attr("href");
        }else
        */
        setTimeout(function(){
            console.debug("let's parse this...");
            gp.a();
        },100);
        $("#add-to-cart").on("click",function(){
            gp.a();
        });
    });
})(jQuery);
