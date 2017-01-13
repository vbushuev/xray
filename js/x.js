"use strict";
function addScript( src ) {
    var s = document.createElement( 'script' );
    s.setAttribute( 'src', src );
    document.body.appendChild( s );
}
var laravelHost = "//l.gauzymall."+(document.location.host.match(/\.bs2/)?"bs2":"com"),
    site = document.location.host.match(/brandalley/i)?"brandalley":"ctshirts";
var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
var r = new XHR();
r.open("GET", laravelHost+"/xray", true);
r.onreadystatechange = function () {
    if (r.readyState != 4 || r.status != 200) return;
    console.debug(r);
    var gl = document.createElement('div');
    gl.innerHTML = r.responseText;
    document.body.appendChild(gl);
};
r.send("site="+site);
