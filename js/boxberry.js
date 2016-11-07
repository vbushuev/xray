if (!Function.prototype.bind) {
	Function.prototype.bind = function(oThis) {
		if (typeof this !== 'function') {
			throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
		}

		var aArgs = Array.prototype.slice.call(arguments, 1),
		fToBind = this,
		fNOP    = function() {},
		fBound  = function() {
			return fToBind.apply(this instanceof fNOP && oThis
				? this
				: oThis,
			aArgs.concat(Array.prototype.slice.call(arguments)));
		};

		fNOP.prototype = this.prototype;
		fBound.prototype = new fNOP();

		return fBound;
	};
}
function bb_getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}
var boxberry = {

	parameters : new Object(),

	init: function(){
		this._callback_function = (this._callback_function ? this._callback_function : null);
		this._overlay = (this._overlay ? this._overlay : null);
		this._frame = (this._frame ? this._frame : null);

		var h = document.getElementsByTagName('HEAD')[0];

		var el = document.createElement('LINK');
			el.rel = 'stylesheet';
			el.type = 'text/css';
			el.href = 'https://points.boxberry.de/css/boxberry.css';
			h.appendChild(el);
		var el = document.createElement('SCRIPT');
			el.src = 'https://points.boxberry.de/js/postmessage.js';
			el.onload = function(){
			pm.bind('boxberry-map-point-select', function(data){

					this.callCallbackSelfFunction(data);
					this.hideOverlay();
					this.hideContainer();
				}.bind(this));
			}.bind(this);
			h.appendChild(el);


	},
	makeUrl: function(parameters){
		var url = '?';
		for(var index in parameters) {
		   url = url + index+'='+parameters[index]+'&';
		}
		return url+'host='+ location.hostname;
	},
	openOnPage: function(element){
		this.parameters.element = element;
	},
	open: function(callback_function, api_token, custom_city, target_start ,ordersum, weight, paysum, height, width, depth ){

			this.parameters.chgPaysum = (this.parameters.paysum == paysum ? false : true);
			this.parameters.chgCity = (this.parameters.custom_city == encodeURIComponent(custom_city) ? false : true);
			this.parameters.reloadmap =  (this.parameters.chgPaysum || this.parameters.chgCity ? true : false);
			this.parameters.calc = 1;
			this.parameters.select_office = 1;
			this.parameters.ordersum = (ordersum !== undefined ?  ordersum : '');
			this.parameters.paysum = (paysum !== undefined ?  paysum : '');
			this.parameters.weight = (weight !== undefined ?  weight : 0);
			this.parameters.height = (height !== undefined ?  height : 0);
			this.parameters.width = (width !== undefined ?  width : 0);
			this.parameters.depth = (depth !== undefined ?  depth : 0);
			//api_token parameters handler
				if (api_token % 1 === 0){
					this.parameters.api_token = '';
					this.parameters.custom_city = encodeURIComponent(custom_city);
					this.parameters.target_start = '68';
					this.parameters.calc = 0;
				}else{
					this.parameters.api_token = (api_token !== undefined ? encodeURIComponent(api_token) : '');
					this.parameters.custom_city = (custom_city !== undefined ?  encodeURIComponent(custom_city) : '');
					if (target_start !=''){
						this.parameters.target_start = target_start;
					}
                }
			//callback_function parameters handler
			if (typeof callback_function === 'string'){
				callback_function = window[callback_function];
			}
			if (callback_function == undefined){
				this.parameters.calc = 0;
				this.parameters.select_office = 0;
			}
			this._callback_function = callback_function;
			if (this.parameters.element){
					this.showContainerOnPage();
				}else{
					this.showOverlay();
					this.showContainer();
			}


	},
	openspvz: function(callback_function, api_token, custom_city, target_start ,ordersum, weight, paysum, height, width, depth ){

			this.parameters.pip = 1;
			this.parameters.chgPaysum = (this.parameters.paysum == paysum ? false : true);
			this.parameters.chgCity = (this.parameters.custom_city == encodeURIComponent(custom_city) ? false : true);
			this.parameters.reloadmap =  (this.parameters.chgPaysum || this.parameters.chgCity ? true : false);
			this.parameters.calc = 0;
			this.parameters.select_office = 1;
			this.parameters.ordersum = (ordersum !== undefined ?  ordersum : '');
			this.parameters.paysum = (paysum !== undefined ?  paysum : '');
			this.parameters.weight = (weight !== undefined ?  weight : 1);
			this.parameters.height = (height !== undefined ?  height : 5);
			this.parameters.width = (width !== undefined ?  width : 5);
			this.parameters.depth = (depth !== undefined ?  depth : 5);
			//api_token parameters handler
				if (api_token % 1 === 0){
					this.parameters.api_token = '';
					this.parameters.custom_city = encodeURIComponent(custom_city);
					this.parameters.target_start = '68';
				}else{
					this.parameters.api_token = (api_token !== undefined ? encodeURIComponent(api_token) : '');
					this.parameters.custom_city = (custom_city !== undefined ?  encodeURIComponent(custom_city) : '');
					//this.parameters.target_start = (target_start !== undefined ?  target_start : '68');
                }
			//callback_function parameters handler
			if (typeof callback_function === 'string'){
				callback_function = window[callback_function];
			}
			if (callback_function == undefined){
				this.parameters.calc = 0;
				this.parameters.select_office = 0;
			}
			this._callback_function = callback_function;
			this.showOverlay();
			this.showContainer();


	},
	openKD: function(){
		this._callback_function = callback_function;
		this.parameters.kd=1;
		this.parameters.reloadmap=true;
		this.showOverlay();
		this.showContainer();
	},
	callCallbackSelfFunction: function(){
		if (this._callback_function){
			this._callback_function.apply(window, arguments);
		}
	},

	showOverlay: function(){
		if (!this._overlay){
			this._overlay = document.createElement('DIV');
			this._overlay.className = 'boxberry_overlay';
			document.getElementsByTagName('BODY')[0].appendChild(this._overlay);
		}
		this._overlay.style.display = 'block';
	},

	hideOverlay: function(){

		if (this._overlay){
			this._overlay.style.display = 'none';
		}
	},
	showContainerOnPage: function(){
		content = document.getElementById(this.parameters.element);
		if (content){
			this._frame = document.createElement('IFRAME');
			this._frame.src =   'https://points.boxberry.de/map' + this.makeUrl (this.parameters);
			this._frame.frameBorder = "0";
			this._frame.height = "580px";
			this._frame.width = "100%";
			content.appendChild(this._frame);
		}
	},
	showContainer: function(){

		if (this.parameters.reloadmap){
			this._container = document.createElement('DIV');
			this._container.className = 'boxberry_container';

			document.getElementsByTagName('BODY')[0].appendChild(this._container);


			var toppanel = document.createElement('DIV');
				toppanel.className = 'boxberry_toppanel';
				var a = document.createElement('A');
				a.href = '#';
				a.innerHTML = '<img src="https://points.boxberry.de/img/close_round_button.png" alt="close">';
				a.className = 'boxberry_container_close';
				a.onclick = function(){
					this.hideOverlay();
					this.hideContainer();
					return false;
				}.bind(this);
			toppanel.appendChild(a);
			this._container.appendChild(toppanel);


			var content = document.createElement('DIV');
				content.className = 'boxberry_content';
			this._content = content;
			this._container.appendChild(content);

				this._frame = document.createElement('IFRAME');
				this._frame.src =   'https://points.boxberry.de/map' + this.makeUrl (this.parameters);

				this._frame.frameborder = '0';
				this._frame.style.border = '0';
				if (document.body.offsetWidth <= 630){
					this._frame.style.height = '1160px';
				}else{
					this._frame.style.height = '580px';
				}
				this._frame.style.width = '100%';
				content.appendChild(this._frame);


			if (this._frame.contentWindow){
        		pm({
					target: this._frame.contentWindow,
					type: 'boxberry-map-init',
					data: {init:true}
				});
			}
		}
		this._container.style.display = 'block';
        var s = this.getPageScroll();

		if (document.documentElement.clientHeight > 1200){
			ytop = document.documentElement.clientHeight/2;
		}else{
			ytop = document.documentElement.clientHeight;
		}
		this._container.style.top = (s.top + Math.max(0, (ytop - this._container.offsetHeight) / 2)) + 'px';
		this._container.style.left = (s.left + Math.max(0, (document.documentElement.clientWidth - this._container.offsetWidth) / 2)) + 'px';
	},

	hideContainer: function(){
		if (this._frame){
			pm({
				target: this._frame.contentWindow,
				type: 'boxberry-map-destroy',
				data: {}
			});
		}
		if (this._container){

			this._container.style.display = 'none';
		}

	},

	getPageScroll: function(){

		var doc = document;
		var html = doc.documentElement;
		var body = doc.body;

		var top = (doc && doc.scrollTop) || (doc.body && doc.body.scrollTop) || (html && html.scrollTop) || 0;
			top -= html.clientTop;

		var left = (doc && doc.scrollLeft) || (doc.body && doc.body.scrollLeft) || (html && html.scrollLeft) || 0;
			left -= html.clientLeft;

		return {
			top: top,
			left: left
		};
	}


};

boxberry.init();


function setBXBCookie(key, value) {
	var expires = new Date();
	expires.setTime(expires.getTime() + (30 * 60 * 1000));
	document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function pvz_delivery_remote(result){

	var settings_element = bb_getCookie('element_bxb');
		if (settings_element != null){
			var element = document.getElementById(settings_element);
		}
		if (element != undefined){
			setBXBCookie('bxb_city', result.name);
			setBXBCookie('bxb_address', result.address);
			element.value= 'Boxberry: ' + result.address + "\n";
			setBXBCookie('bxb_price', result.price);
			setBXBCookie('bxb_period', result.period);
			if (settings_element.indexOf("soa") == -1){
				submitForm();
			}
		}

		return false;
}
function pvz_delivery_cod_remote(result){
	var settings_element = bb_getCookie('element_bxb');
		if (settings_element != null){
			var element = document.getElementById(settings_element);
		}
		if (element != undefined){

			setBXBCookie('bxb_city', result.name);
			setBXBCookie('bxb_address', result.address);
			element.value= 'Boxberry: ' + result.address + "\n";
			setBXBCookie('bxb_price_cod', result.price);
			setBXBCookie('bxb_period_cod', result.period);
			if (settings_element.indexOf("soa") == -1){
				submitForm();
			}
		}
	return false;
}
setBXBCookie('settings_activate', 1);
