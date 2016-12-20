function addScript( src ) {
    var s = document.createElement( 'script' );
    s.setAttribute( 'src', src );
    document.body.appendChild( s );
}
if(typeof $ != "undefined"){
    addScript("/js/jquery-2.2.4.min.js");
    console.debug("load jquery");
}
$(document).ready(function(){
    console.debug("here iam");
    //draw green line
    $.ajax({
        url:"//l.gauzymall.com/xray",
        crossDomain:true,
        success:function(d){
            //$("body").append('<div id="_xg_green_line" style="position:fixed;top:0;left:0;height:50px;background-color:rgba(0, 191, 128,.9);color: rgba(255,255,255,1);z-index:-1;width:100%;">Green line</div>')
            $("body").append(d).animate({marginTop: "50px"},200,"linear",function(){
                console.debug("animated");
                //$("#_xg_green_line").css("zIndex","999");
            });
        }
    });


    /*
    $("#panier-valider").text("чекаутнемся").unbind("click").attr("href","javascript:{0}").click(function(e){
        e.preventDefault();
        e.stopPropagation();
        console.debug("Go to our checkout");
        return false;
    })*/
});
