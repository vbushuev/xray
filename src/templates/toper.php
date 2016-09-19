<style>
    @import url("//fonts.googleapis.com/css?family=Lato");
    @import url("//fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic");
    @import url("//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css");
    #garan24-toper{
        display: none;
        font-family: 'Open Sans';
        position: fixed;
        top: 0;
        left:0;
        width:100%;
        height:48px;
        background-color: rgba(85,125,161,.9);
        color: rgba(255,255,255,1);
        z-index: 999;
        overflow: visible;
    }
    @media (min-width: 768px){
        #garan24-toper-content {
            width: 750px;
        }
    }
    @media (min-width: 992px){
        #garan24-toper-content {
            width: 970px;
        }
    }
    @media (min-width: 1200px){
        #garan24-toper-content{
            width: 1170px;
        }
    }
    #garan24-toper-content{
        height:48px;
    }
    #garan24-toper .garan24-logo{
        font-size: 16px;
        font-weight: 700;
    }
    #garan24-toper .garan24-logo code{
        border:solid 2px rgba(255,255,255,1);
        color:rgba(255,255,255,1);
        font-weight: 400;
        display: inline-block;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        height:24px;
        line-height: 24px;
        font-family: 'Lato';
        padding:0 4px;
        font-size: 14px;
    }
    .garan24-button{
        height: 36px;
        line-height: 36px;
        display: inline-block;
        padding: 0 8px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255,1);
        color:rgba(85,125,161,.8);
        text-align: center;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        border:solid 2px rgba(85,125,161,1);
    }
    .garan24-button:hover{
        background-color: rgba(255, 255, 255, 1);
        color:rgba(85,125,161,1);
        border-color: rgba(255, 255, 255, 1);
        text-decoration: none;
    }
    .garan24-button.garan24-button-success{
        background-color: rgba(92,184,92,1);
        color:rgba(255,255,255,1);
    }
    .garan24-button.garan24-button-success:hover{
        border-color: rgba(92,184,92,1);
        color:rgba(255,255,255,1);
    }
    #garan24-toper-content {
        padding: 0 16px;
        margin-left:auto;
        margin-right: auto;
    }
    #garan24-toper-content .garan24-toper-menu{
        list-style: none;
        display: inline-block;
        font-size: 12pt;
        font-family: 'Lato','Open Sans';
        font-weight: 300;
        height:48px;
        line-height: 48px;
        margin:0;
        vertical-align: middle;
    }
    #garan24-toper-content .garan24-toper-menu li{
        display: inline-block;
        height:48px;
        line-height: 48px;
        margin:0;
    }
    #garan24-toper-content .garan24-toper-menu-right{
        float:right;

    }
    #garan24-toper-content .garan24-toper-menu li .garan24-cart{
        height: 36px;
        line-height: 36px;
        display: inline-block;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255,1);
        color:rgba(0,0,0,.6); /*rgba(92,184,92,1)*/
        text-align: center;
        padding: 0 5px;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        border:solid 2px rgba(85,125,161,1);
        position: relative;
    }
    #garan24-toper-content .garan24-toper-menu li .garan24-cart:hover{
        border-color: rgba(255,255,255,1);
    }
    #garan24-toper-content .garan24-toper-menu li .garan24-cart #garan24-cart-quantity{
        -webkit-border-radius: 12px;
        -moz-border-radius: 12px;
        border-radius: 12px;
        width:12px;
        height:12px;
        background-color: rgba(255, 0, 0, 0.5);
        color: rgba(255,255,255,1);
        padding:0 5px;
        font-size: 10px;
    }
    #garan24-toper-content .garan24-toper-menu li .garan24-cart #garan24-cart-amount{
        display: inline-block;
        margin-left: 12px;
        font-weight: 400;
    }
    #garan24-overlay{
        height: 100%;
        width: 100%;
        position:fixed;
        top:56px;left: 0;
        background-color: rgba(0,0,0,.3);
        z-index:999;
        display: none;
    }
    #garan24-overlay-cover{
        position: absolute;
        top:0;left: 0;bottom: 0;right: 0;
        /*
        -webkit-filter: blur(10px);
        -moz-filter: blur(10px);
        -o-filter: blur(10px);
        -ms-filter: blur(10px);
        filter: blur(10px);
        */
    }
    #garan24-overlay-message{
        z-index:1000;
        background-color: rgba(255,255,255,1);
        overflow: auto;
        position: relative;
        margin: 6em 20%;
        padding: 2em;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        font-size: 14pt;
        text-align: center;
    }
    #garan-cart-full{
        display: none;
        z-index: 1000;
        background-color: rgba(255,255,255,1);
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        position: absolute;
        padding: 1em;
        width: 640px;
        overflow: auto;
        right:0;
        border:solid 4px rgba(85,125,161,1);
    }
    #garan-cart-full table{
        width:100%;
    }
    #garan-cart-full table,#garan-cart-full table tr td{
        border:none;
        line-height: normal;
    }
    #garan-cart-full table tr td a{
        color:rgba(85,125,161,.7);
    }
    #garan-cart-full table tr td img{
        height: 64px;
    }
    #garan-cart-full table tr td a:hover{
        color:rgba(85,125,161,1);
    }
    #garan-cart-full table tr.total td{
        border-top:dotted 1px rgba(85,125,161,1);
        text-align: right;
        color:rgba(0,0,0,.8);
        font-size: 110%;
        font-weight: 700;
    }
    #garan-cart-full .small{
        font-size: 90%;
        font-weight: 300;
    }

    #garan-cart-full .currency-amount{
        font-weight: 700;
    }
    #garan24-add2cart-block {
        background-color: rgba(85,125,161,.98);
        padding:1em 3em;
        text-align: center;
        vertical-align: middle;
    }
    .garan24-add2cart-block-ctshirts {}
    .garan24-add2cart-block-babywalz {
        clear:both;
    }
    #garan24-add2cart-block #garan24-add2cart-button{
        padding-left:2em;
        padding-right: 2em;
    }
    /*body{padding-top: 56px;}*/

</style>
<div id="garan24-toper">
    <div id="garan24-toper-content">
        <ul class="garan24-toper-menu garan24-toper-menu-left">
            <li class="garan24-logo">G&nbsp;<code>24</code></li>
        </ul>
        <ul class="garan24-toper-menu garan24-toper-menu-right">
            <li>
                <a id="garan-cart" class="garan24-cart" href="#">
                    <i class="fa fa-spinner fa-spin fa-2x fa-fw" area-hidden="true"></i>
                    <!--
                        <i class="fa fa-shopping-cart" area-hidden="true"></i>
                        <sup id="garan24-cart-quantity">0</sup>
                        <span id="garan24-cart-amount">0 руб.</span>
                        <div id="garan-cart-full"></div>
                    -->
                </a>

            </li>
            <li><a id="garan-checkout" class="garan24-button garan24-button-success" href="#">Оформить заказ</a></li>
        </ul>
    </div>
</div>
<!--
<div id="garan24-add2cart-block"><a id="garan24-add2cart-button" class="garan24-button garan24-button-primary" href="#">В корзину&nbsp;&nbsp;<i class="fa fa-plus"></i></a></div>
-->

<div id="garan24-overlay">
    <!--<div id="garan24-overlay-cover"></div>-->
    <div id="garan24-overlay-message">
        <span class="garan24-overlay-message-text">here is message</span><br />
        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
    </div>
</div>
<script src="/js/garan.js"></script>
<script>
    var $ = jQuery.noConflict();
    $(document).ready(function() {
        window.G  = new G24();
        // baby-walz.de

        //$("#usp_bar, .meta, .headBasket, .footerBox, #headSearch").hide();
        //$("#header > ul, #header > div.input-box.input-box--pushed.desktop-only.input-box--silent").hide();
        console.debug(document.location.hostname.split(/\./)[0].replace(/[\-]/,""));
        //collectData.init();
        //$(document).ajaxComplete(function(e, jqXHR, options) {collectData.init();});
        //$(".wrapper").css("padding-top","56px").css("position","relative");
        //$(".header").css("position","absolute");
    });

</script>
