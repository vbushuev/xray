jQuery.noConflict();
(function($){
    var api_host = document.location.href.match(/xray\.bs2/)?"//service.garan24.bs2":"//l.gauzymall.com";
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
            $.getJSON(api_host+"/prod/s",function(d){
                t._i=true;
                t.l=d;
                $.getJSON(api_host+"/prod/categories",function(dc){
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
                //type:"external",
                type:"variable",
                variations:[],
                attributes:[]
                //status:"draft"
                /*variations:{
                    color:f.j.find(".b-cart_table-body_col_product-attribute.m-color .b-cart_table-body_col_product-attribute-value").text().trim(),
                    size:f.j.find(".b-cart_table-body_col_product-attribute.m-size .b-cart_table-body_col_product-attribute-value").text().trim(),
                }*/
            },ii = 0,gp=this;
            $("#pdpMain img.pdp-main__image").each(function(){
                var $t = $(this);
                //console.debug($t.attr("src"));
                if(ii>0)p.images.push({src:$t.attr("src").replace(/\?.*$/,""),position:ii-1});
                ii++;
            });
            for(i in utag_data.product_category){
                var re = new RegExp(f.f)
                var c = this.cat.get(utag_data.product_category[i].replace(re,""));
                if(c==false) console.warn("No category {"+utag_data.product_category[i]+"} matching!!!");
                else p.categories = p.categories.concat(c);
            }
            var ai = 0;
            if($(".swatches.size.attribute.attribute__variants-swatches").length){
                p.attributes.push({
                    name:"Размер",
                    position:ai,
                    visible:true,
                    variation:true,
                    options:[]
                });
                $("ul.swatches.size.attribute.attribute__variants-swatches > li:not(.unselectable)").each(function(t){//sized
                    var $t = $(this),v = $t.find("div").text().trim();
                    p.variations.push({
                        sku:p.sku+v,
                        regular_price:p.regular_price
                    });
                    p.attributes[ai].options.push(v);
                });
                ai++;
            }
            if($(".swatches.width.attribute.attribute__variants-swatches").length){
                p.attributes.push({
                    name:"Размер воротника",
                    slug:"collar",
                    position:ai,
                    visible:true,
                    variation:true,
                    options:[]
                });
                $("ul.swatches.width.attribute.attribute__variants-swatches > li:not(.unselectable)").each(function(t){//sized
                    var $t = $(this),v = $t.find("div").text().trim();
                    p.variations.push({
                        sku:p.sku+v,
                        regular_price:p.regular_price
                    });
                    p.attributes[ai].options.push(v);
                });
                ai++;
            }
            if($(".swatches.length.attribute.attribute__variants-swatches").length){
                p.attributes.push({
                    name:"Длинна рукава",
                    slug:"sleeve",
                    position:ai,
                    visible:true,
                    variation:true,
                    options:[]
                });
                $("ul.swatches.length.attribute.attribute__variants-swatches > li:not(.unselectable)").each(function(t){//sized
                    var $t = $(this),v = $t.find("div").text().trim().replace(/[\D\s]+/,"");
                    p.variations.push({
                        sku:p.sku+v,
                        regular_price:p.regular_price
                    });
                    p.attributes[ai].options.push(v);
                });
                ai++;
            }
            if($(".swatches.cufftype.attribute.attribute__variants-swatches").length){
                p.attributes.push({
                    name:"Тип манжета",
                    slug:"cuff",
                    position:ai,
                    visible:true,
                    variation:true,
                    options:[]
                });
                $("ul.swatches.cufftype.attribute.attribute__variants-swatches > li:not(.unselectable)").each(function(t){//sized
                    var $t = $(this),v = $t.find("div").text().trim();
                    p.variations.push({
                        sku:p.sku+v,
                        regular_price:p.regular_price
                    });
                    p.attributes[ai].options.push(v);
                });
                ai++;
            }
            $(document).trigger("gparser:parsed",p);
            if(p.sku.length) this.s(p);
        },
        s:function(p){
            $.ajax({
                url:api_host+"/prod/create",
                type:"post",
                crossDomain: true,
                //contentType:'application/json',
                data: JSON.stringify(p),
                dataType:'json',
                success: function(data){
                    $(document).trigger("gparser:sent",data);
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
                var r = [];
                for(i in this.c){
                    if(c==this.c[i].slug){
                        r.push(this.c[i].id);
                        r = r.concat(this.getParent(this.c[i].parent));
                        break;
                    }
                }
                return r.length?r:false;
            },
            getParent:function(c){
                var r = [];
                for(i in this.c){
                    if(c==this.c[i].id){
                        r.push(this.c[i].id);
                        r = r.concat(this.getParent(this.c[i].parent));
                        break;
                    }
                }
                return r;
            }
        },
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
        /*var curp = $(".tiles-container .tile");// .product-tile .product-image.tile__image > a:nth-child(1)");
        if(curp.length>3) {
            garan.cookie.set("gparser_last_page",document.location.href);
            if(garan.cookie.get("gparser_cp",0)<4)
            //if(garan.cookie.get("gparser_cp",0)<4)
                document.location = $(curp[garan.cookie.get("gparser_cp",0)]).find(".product-image.tile__image > a:nth-child(1)").attr("href");
        }else */
        gp.a();
        $(document).bind("gparser:parsed",function(e,d){
            console.debug("Parsed");
            console.debug(d);
        })
        $(document).bind("gparser:sent",function(e,d){
            console.debug("Sent");
            console.debug(d);
            //garan.cookie.set("gparser_cp",parseInt(garan.cookie.get("gparser_cp",0))+1);
            history.back();
        })
        //setTimeout(function(){console.debug("let's parse this...");gp.a();},100);
        //$("#add-to-cart").on("click",function(){gp.a();});

    });
})(jQuery);
