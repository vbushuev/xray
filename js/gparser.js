jQuery.noConflict();
(function($){
    var gp = {
        _i:false,_b:false,
        l:false,
        init:function(){
            if(this._b)return;
            var t=this,cbf = (arguments.length)?arguments[0]:null;
            $.getJSON("/js/s.json",function(d){
                t._i=true;
                t.l=d;
                cbf();
            });
            this._b=true;
        },
        find:function(){
            if(!this._i){this.init(gp.find());}
            var h=document.location.hostname.split(/\./)[0];
            return ($(this.l[h].j).length)?this.l[h]:false;
        },
        get:function(f){
            f.j=$(f.j);
            var p ={
                shop:f.s,
                //quantity:f.j.find(".b-cart_table-body_col_qty > div > input").val().replace(/\D+/,""),
                currency:'GBP',
                original_price:f.j.find(".price.price__display.regular").text().trim().replace(/[^\d\.\,]/,""),
                regular_price:f.j.find(".price.price__display.regular").text().trim().replace(/[^\d\.\,]/,""),
                title:f.j.find(".product-name.pdp-main__name").text().trim(),
                description:$("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--left-group.pdp-main__slot--border-right > div.pdp-main__slot--outlined.pdp-main__slot--outlined-blue.js-slot-accordion.pdp-main__slot--accordion").html(),
                product_img:f.j.find("img.pdp-main__image").attr("src"),
                images:[],
                external_url:document.location.href,
                sku:f.c+f.j.find(".pdp-main__number span[itemprop='productID']:first").text().trim(),
                categories:[40,50,51],
                /*[
                    {id:40},//,name:"Мужская одежда"},
                    {id:"product_cat-50"},//,name:"Мужские сорочки"},
                    {id:"product_cat-51"}//,name:"Одежда"}
                ],*/
                tags:[52,53,54],
                type:"external",
                //status:"draft"
                /*variations:{
                    color:f.j.find(".b-cart_table-body_col_product-attribute.m-color .b-cart_table-body_col_product-attribute-value").text().trim(),
                    size:f.j.find(".b-cart_table-body_col_product-attribute.m-size .b-cart_table-body_col_product-attribute-value").text().trim(),
                }*/
            };
            //p.tags.push($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--right-group.desktop-only > div > div:nth-child(3) > div > section > article > h3").text());
            //p.tags.push($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--right-group.desktop-only > div > div:nth-child(4) > div > section > article > h3").text());
            //p.tags.push($("#wrapper > div:nth-child(5) > div.pdp-main__slot.js-accordion > div.pdp-main__slot.pdp-main__slot--shadowed.pdp-main__slot--full > div.pdp-main__slot.pdp-main__slot--right-group.desktop-only > div > div:nth-child(5) > div > section > article > h3").text());
            p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_a.jpg"),position:0});
            p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_b.jpg"),position:1});
            p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_c.jpg"),position:2});
            p.images.push({src:p.product_img.replace(/_[a-z]\.jpg/,"_d.jpg"),position:3});
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

        }
    };
    window.gp=gp;
    $(document).ready(function(){
        setTimeout(function(){
            console.debug("let's parse this...");
            gp.a();
        },2600);
    });
})(jQuery);
