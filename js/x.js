function addScript( src ) {
    var s = document.createElement( 'script' );
    s.setAttribute( 'src', src );
    document.body.appendChild( s );
}
var laravelHost = "//l.gauzymall."+(document.location.host.match(/\.bs2/)?"bs2":"com");
(function(xgj$){
    xgj$(document).ready(function(){
        console.debug("here iam");
        //draw green line
        xgj$.ajax({
            url:laravelHost+"/xray",
            crossDomain:true,
            success:function(d){
                xgj$("body").append(d).animate({marginTop: "50px"},400,"linear",function(){
                    console.debug("animated");
                    //xgj$("#_xg_green_line").css("zIndex","999");
                });
            }
        });


        /*
        xgj$("#panier-valider").text("чекаутнемся").unbind("click").attr("href","javascript:{0}").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            console.debug("Go to our checkout");
            return false;
        })*/
    });

})(jQuery);
