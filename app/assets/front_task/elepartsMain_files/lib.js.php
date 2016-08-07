//  CREDITS:

//	Class is slightly based on Base.js : http://dean.edwards.name/weblog/2006/03/base/
//		(c) 2006 Dean Edwards, License: http://creativecommons.org/licenses/LGPL/2.1/

//	Some functions are based on those found in prototype.js : http://prototype.conio.net/
//		(c) 2005 Sam Stephenson <sam@conio.net>, MIT-style license


var Class = function(properties){
	var klass = function(){
		for (p in this) this[p]._proto_ = this;
		if (arguments[0] != 'noinit' && this.initialize) return this.initialize.apply(this, arguments);
	};
	klass.extend = this.extend;
	klass.implement = this.implement;
	klass.prototype = properties;
	return klass;
};

Class.empty = function(){};

Class.create = function(properties){
	return new Class(properties);
};

Class.prototype = {
	extend: function(properties){
		var prototype = new this('noinit');
		for (property in properties){
			var previous = prototype[property];
			var current = properties[property];
			if (previous && previous != current) current = previous.parentize(current) || current;
			prototype[property] = current;
		}
		return new Class(prototype);
	},
	
	implement: function(properties){
		for (property in properties) this.prototype[property] = properties[property];
	}
}

Object.extend = function(){
	var args = arguments;
	if (args[1]) args = [args[0], args[1]];
	else args = [this, args[0]];
	for (property in args[1]) args[0][property] = args[1][property];
	return args[0];
};

Object.Native = function(){
	for (var i = 0; i < arguments.length; i++) arguments[i].extend = Class.prototype.implement;
};

new Object.Native(Function, Array, String);

Function.extend({
	parentize: function(current){
		var previous = this;
		return function(){
			this.parent = previous;
			return current.apply(this, arguments);
		};
	}
});

Function.extend({
	
	pass: function(args, bind){
		var fn = this;
		if ($type(args) != 'array') args = [args];
		return function(){
			fn.apply(bind || fn._proto_ || fn, args);
		};
	},

	bind: function(bind){
		var fn = this;
		return function(){
			return fn.apply(bind, arguments);
		};
	},

	bindAsEventListener: function(bind){
		var fn = this;
		return function(event){
			fn.call(bind, event || window.event);
			return false;
		};
	},
	
	//setTimeout : ¸î ÃÊ ÈÄ ÇÑ¹ø¸¸ ÀÛµ¿ÇÑ´Ù.(¹Ð¸®ÃÊ,¹ÙÀÎµå·Î ¹­ÀÏ ¸Þ¼Òµå°´Ã¼)
	delay: function(ms, bind){
		return setTimeout(this.bind(bind || this._proto_ || this), ms);
	},

	//°è¼Ó ÀÛµ¿(¹Ð¸®ÃÊ,bind·Î ¹­ÀÏ ¸Þ¼Òµå°´Ã¼)
	periodical: function(ms, bind){
		return setInterval(this.bind(bind || this._proto_ || this), ms);
	}

});

//clearTimeout,setInterval ÇØÁ¦
function $clear(timer){
	clearTimeout(timer);
	clearInterval(timer);
	return null;
};

//Å¸ÀÔ ¾Ë¾Æ³»´Â ¸Þ¼Òµå
function $type(obj, types){
	if (!obj) return false;
	var type = false;
	if (obj instanceof Function) type = 'function';
	else if (obj.nodeName){
		if (obj.nodeType == 3 && !/\S/.test(obj.nodeValue)) type = 'textnode';
		else if (obj.nodeType == 1) type = 'element';
	}
	else if (obj instanceof Array) type = 'array';
	else if (typeof obj == 'object') type = 'object';
	else if (typeof obj == 'string') type = 'string';
	else if (typeof obj == 'number' && isFinite(obj)) type = 'number';
	return type;
};

//? ÇÔ¼öÀÎÁö ÆÇ´ÜÇÏ±â ¸Þ¼Òµå???
function $check(obj, objTrue, objFalse){
	if (obj) {
		if (objTrue && $type(objTrue) == 'function') return objTrue();
		else return objTrue || obj;
	} else {
		if (objFalse && $type(objFalse) == 'function') return objFalse();
		return objFalse || false;
	}
};

//ÇÔ¼ö¸¦ ¹­´Â ...
var Chain = new Class({
	//ÇÔ¼ö¸¦ ¹­´Â ...
	chain: function(fn){
		this.chains = this.chains || [];
		this.chains.push(fn);
		return this;
	},
	//ÇÔ¹­¿©Áø ÇÔ¼öÁß¿¡ ¿øÇÏ´Â ÇÔ¼ö¸¦ È£Ãâ
	callChain: function(){
		if (this.chains && this.chains.length) this.chains.splice(0, 1)[0].delay(10, this);
	}

});

if (!Array.prototype.forEach){
	Array.prototype.forEach = function(fn, bind){
		for(var i = 0; i < this.length ; i++) fn.call(bind, this[i], i);
	};
}

Array.extend({
	
	each: Array.prototype.forEach,
	
	copy: function(){
		var nArray = [];
		for (var i = 0; i < this.length; i++) nArray.push(this[i]);
		return nArray;
	},
	
	remove: function(item){
		for (var i = 0; i < this.length; i++){
			if (this[i] == item) this.splice(i, 1);
		}
		return this;
	},
	
	test: function(item){
		for (var i = 0; i < this.length; i++){
			if (this[i] == item) return true;
		};
		return false;
	},
	
	extend: function(nArray){
		for (var i = 0; i < nArray.length; i++) this.push(nArray[i]);
		return this;
	}
	
});

function $A(array){
	return Array.prototype.copy.call(array);
};

String.extend({

	test: function(value, params){
		return this.match(new RegExp(value, params));
	},

	camelCase: function(){
		return this.replace(/-\D/gi, function(match){
			return match.charAt(match.length - 1).toUpperCase();
		});
	},

	capitalize: function(){
		return this.toLowerCase().replace(/\b[a-z]/g, function(match){
			return match.toUpperCase();
		});
	},

	trim: function(){
		return this.replace(/^\s*|\s*$/g,'');
	},

	clean: function(){
		return this.replace(/\s\s/g, ' ').trim();
	},

	rgbToHex: function(array){
		var rgb = this.test('^[rgba]{3,4}\\(([\\d]{0,3}),[\\s]*([\\d]{0,3}),[\\s]*([\\d]{0,3})\\)$');
		var hex = [];
		for (var i = 1; i < rgb.length; i++) hex.push((rgb[i]-0).toString(16));
		var hexText = '#'+hex.join('');
		if (array) return hex;
		else return hexText;
	},

	hexToRgb: function(array){
		var hex = this.test('^[#]{0,1}([\\w]{1,2})([\\w]{1,2})([\\w]{1,2})$');
		var rgb = [];
		for (var i = 1; i < hex.length; i++){
			if (hex[i].length == 1) hex[i] += hex[i];
			rgb.push(parseInt(hex[i], 16));
		}
		var rgbText = 'rgb('+rgb.join(',')+')';
		if (array) return rgb;
		else return rgbText;
	}

});

//°´Ã¼µéÀÌ ÀÚµ¿À¸·Î °¡Áö°Ô µÇ´Â °ª
var Element = new Class({

	//creation

	initialize: function(el){
		if ($type(el) == 'string') el = document.createElement(el);
		return $(el);
	},

	//injecters

	inject: function(el, where){
		var el = $check($(el), $(el), new Element(el));
		switch(where){
			case "before": $(el.parentNode).insertBefore(this, el); break;
			case "after": {
					if (!el.getNext()) $(el.parentNode).appendChild(this);
					else $(el.parentNode).insertBefore(this, el.getNext());
			} break;
			case "inside": el.appendChild(this); break;
		}
		return this;
	},

	injectBefore: function(el){
		return this.inject(el, 'before');
	},

	injectAfter: function(el){
		return this.inject(el, 'after');
	},

	injectInside: function(el){
		return this.inject(el, 'inside');
	},

	adopt: function(el){
		var el = $check($(el), $(el), new Element(el));
		this.appendChild(el);
		return this;
	},

	//actions
	
	remove: function(){
		this.parentNode.removeChild(this);
	},

	clone: function(){
		return $(this.cloneNode(true));
	},

	replaceWith: function(el){
		var el = $check($(el), $(el), new Element(el));
		this.parentNode.replaceChild(el, this);
		return el;
	},
	
	appendText: function(text){
		if (this.getTag() == 'style' && window.ActiveXObject) this.styleSheet.cssText = text;
		else this.appendChild(document.createTextNode(text));
		return this;
	},

	//classnames

	hasClassName: function(className){
		return $check(this.className.test("\\b"+className+"\\b"), true);
	},

	addClassName: function(className){
		if (!this.hasClassName(className)) this.className = (this.className+' '+className.trim()).clean();
		return this;
	},

	removeClassName: function(className){
		if (this.hasClassName(className)) this.className = this.className.replace(className.trim(), '').clean();
		return this;
	},

	toggleClassName: function(className){
		if (this.hasClassName(className)) return this.removeClassName(className);
		else return this.addClassName(className);
	},

	//styles

	setStyle: function(property, value){
		if (property == 'opacity') {
			this.setOpacity(value);
		}
		else {
			if(value != 'NaN' && value != 'undefined' && value != ""){
				try{
					this.style[property.camelCase()] = value;	
				}catch(e){	}
			}
		}
		return this;
	},

	setStyles: function(source){
		if ($type(source) == 'object') {
			for (property in source) this.setStyle(property, source[property]);
		} else if ($type(source) == 'string') this.setAttribute('style', source);
		return this;
	},

	setOpacity: function(opacity){
		if (opacity == 0 && this.style.visibility != "hidden") this.style.visibility = "hidden";
		else if (this.style.visibility != "visible") this.style.visibility = "visible";
		if (window.ActiveXObject) this.style.filter = "alpha(opacity=" + opacity*100 + ")";
		this.style.opacity = opacity;
		return this;
	},

	getStyle: function(property, num){
		var proPerty = property.camelCase();
		var style = $check(this.style[proPerty]);
		if (!style) {
			if (document.defaultView) style = document.defaultView.getComputedStyle(this,null).getPropertyValue(property);
			else if (this.currentStyle) style = this.currentStyle[proPerty];
		}
		if (style && ['color', 'backgroundColor', 'borderColor'].test(proPerty) && style.test('rgb')) style = style.rgbToHex();
		if (['auto', 'transparent'].test(style)) style = 0;
		if (num) return parseInt(style);
		else return style;
	},

	removeStyles: function(){
		$A(arguments).each(function(property){
			this.style[property.camelCase()] = '';
		}, this);
		return this;
	},

	//events

	addEvent: function(action, fn){
		this[action+fn] = fn.bind(this);
		if (this.addEventListener) this.addEventListener(action, fn, false);
		else this.attachEvent('on'+action, this[action+fn]);
		var el = this;
		if (this != window) Unload.functions.push(function(){
			el.removeEvent(action, fn);
			el[action+fn] = null;
		});
		return this;
	},

	removeEvent: function(action, fn){
		if (this.removeEventListener) this.removeEventListener(action, fn, false);
		else this.detachEvent('on'+action, this[action+fn]);
		return this;
	},

	//get non-text elements

	getBrother: function(what){
		var el = this[what+'Sibling'];
		while ($type(el) == 'textnode') el = el[what+'Sibling'];
		return $(el);
	},

	getPrevious: function(){
		return this.getBrother('previous');
	},

	getNext: function(){
		return this.getBrother('next');
	},

	getFirst: function(){
		var el = this.firstChild;
		while ($type(el) == 'textnode') el = el.nextSibling;
		return $(el);
	},

	getLast: function(){
		var el = this.lastChild;
		while ($type(el) == 'textnode')
		el = el.previousSibling;
		return $(el);
	},

	//properties

	setProperty: function(property, value){
		var el = false;
		switch(property){
			case 'class': this.className = value; break;
			case 'style': this.setStyles(value); break;
			case 'name': if (window.ActiveXObject && this.getTag() == 'input'){
				el = $(document.createElement('<input name="'+value+'" />'));
				$A(this.attributes).each(function(attribute){
					if (attribute.name != 'name') el.setProperty(attribute.name, attribute.value);
					
				});
				if (this.parentNode) this.replaceWith(el);
			};
			default: this.setAttribute(property, value);
		}
		return el || this;
	},

	setProperties: function(source){
		for (property in source) this.setProperty(property, source[property]);
		return this;
	},

	setHTML: function(html){
		this.innerHTML = html;
		return this;
	},

	getProperty: function(property){
		return this.getAttribute(property);
	},

	getTag: function(){
		return this.tagName.toLowerCase();
	},

	//position

	getOffset: function(what){
		what = what.capitalize();
		var el = this;
		var offset = 0;
		do {
			offset += el['offset'+what] || 0;
			el = el.offsetParent;
		} while (el);
		return offset;
	},

	getTop: function(){
		return this.getOffset('top');
	},

	getLeft: function(){
		return this.getOffset('left');
	}

});

function $Element(el, method, args){
	if ($type(args) != 'array') args = [args];
	return Element.prototype[method].apply(el, args);
};

new Object.Native(Element);

function $(el){
	if ($type(el) == 'string') el = document.getElementById(el);
	if ($type(el) == 'element'){
		if (!el.extend){
			Unload.elements.push(el);
			el.extend = Object.extend;
			el.extend(Element.prototype);
		}
		return el;
	} else return false;
};

window.addEvent = Element.prototype.addEvent;
window.removeEvent = Element.prototype.removeEvent;

var Unload = {

	elements: [], functions: [], vars: [],
	
	unload: function(){
		Unload.functions.each(function(fn){
			fn();
		});
		
		window.removeEvent('unload', window.removeFunction);
		
		Unload.elements.each(function(el){
			for(p in Element.prototype){
				window[p] = null;
				document[p] = null;
				el[p] = null;
			}
			el.extend = null;
		});
	}
	
};
window.removeFunction = Unload.unload;
window.addEvent('unload', window.removeFunction);

var Fx = fx = {};

Fx.Base = new Class({

	setOptions: function(options){
		this.options = Object.extend({
			duration: 500,
			onComplete: Class.empty,
			onStart: Class.empty,
			unit: 'px',
			wait: true,
			transition: Fx.sinoidal,
			fps: 30
		}, options || {});
	},

	step: function(){
		var currentTime  = (new Date).getTime();
		if (currentTime >= this.options.duration+this.startTime){
			this.clearTimer();
			this.now = this.to;
			this.options.onComplete.pass(this.el, this).delay(10);
			this.callChain();
		} else {
			this.tPos = (currentTime - this.startTime) / this.options.duration;
			this.setNow();
		}
		this.increase();
	},

	setNow: function(){
		this.now = this.compute(this.from, this.to);
	},

	compute: function(from, to){
		return this.options.transition(this.tPos) * (to-from) + from;
	},

	custom: function(from, to){
		if(!this.options.wait) this.clearTimer();
		if (this.timer) return;
		this.options.onStart.pass(this.el, this).delay(10);
		this.from = from;
		this.to = to;
		this.startTime = (new Date).getTime();
		this.timer = this.step.periodical(Math.round(1000/this.options.fps), this);
		return this;
	},

	set: function(to){
		this.now = to;
		this.increase();
		return this;
	},

	clearTimer: function(){
		this.timer = $clear(this.timer);
		return this;
	},

	setStyle: function(el, property, value){
		if (property == 'opacity'){
			if (value == 1 && navigator.userAgent.test('Firefox')) value = 0.9999;
			el.setOpacity(value);
		} else el.setStyle(property, value+this.options.unit);
	}

});

Fx.Base.implement(new Chain);

Fx.Style = Fx.Base.extend({

	initialize: function(el, property, options){
		this.el = $(el);
		this.setOptions(options);
		this.property = property.camelCase();
	},
	
	hide: function(){
		return this.set(0);
	},
	
	goTo: function(val){
		return this.custom(this.now || 0, val);
	},
	
	increase: function(){
		this.setStyle(this.el, this.property, this.now);
	}

});

Fx.Layout = Fx.Style.extend({
	
	initialize: function(el, layout, options){
		this.parent(el, layout, options);
		this.layout = layout.capitalize();
		this.el.setStyle('overflow', 'hidden');
	},
	
	toggle: function(){
		if (this.el['offset'+this.layout] > 0) return this.custom(this.el['offset'+this.layout], 0);
		else return this.custom(0, this.el['scroll'+this.layout]);
	},

	show: function(){
		return this.set(this.el['scroll'+this.layout]);
	}
	
});

Fx.Scroll = Fx.Base.extend({

	initialize: function(el, options) {
		this.element = $(el);
		this.setOptions(options);
	},
	down: function(){
		return this.custom(this.element.scrollTop, this.element.scrollHeight-this.element.offsetHeight);
	},
	up: function(){
		return this.custom(this.element.scrollTop, 0);
	},
	targetMove: function(target){
		return this.custom(this.element.scrollTop, $(target).getTop());
	},
	increase: function(){
		this.element.scrollTop = this.now;
	}
});

Fx.Height = Fx.Layout.extend({

	initialize: function(el, options){
		this.parent(el, 'height', options);
	}

});

Fx.Width = Fx.Layout.extend({

	initialize: function(el, options){
		this.parent(el, 'width', options);
	}

});

Fx.Opacity = Fx.Style.extend({

	initialize: function(el, options){
		this.parent(el, 'opacity', options);
		this.now = 1;
	},

	toggle: function(){
		if (this.now > 0) return this.custom(1, 0);
		else return this.custom(0, 1);
	},

	show: function(){
		this.set(1);
	}

});

Element.extend({

	effect: function(property, options){
		return new Fx.Style(this, property, options);
	}

});


Fx.sinoidal = function(pos){return ((-Math.cos(pos*Math.PI)/2) + 0.5);}; //this transition is from script.aculo.us

Fx.linear = function(pos){return pos;};

Fx.cubic = function(pos){return Math.pow(pos, 3);};

Fx.circ = function(pos){return Math.sqrt(pos);};

function $S(){
	var els = [];
	$A(arguments).each(function(sel){
		if ($type(sel) == 'string') els.extend(document.getElementsBySelector(sel));
		else if ($type(sel) == 'element') els.push($(sel));
	});
	return $$(els);
};

function $E(selector, filter){
	return ($(filter) || document).getElement(selector);
};

function $$(elements){
	return Object.extend(elements, new Elements);
};

Element.extend({

	getElements: function(selector){
		var filters = [];
		selector.clean().split(' ').each(function(sel, i){
			var bits = [];
			var param = [];
			var attr = [];
			if (bits = sel.test('^([\\w]*)')) param['tag'] = bits[1] || '*';
			if (bits = sel.test('([.#]{1})([\\w-]*)$')){
				if (bits[1] == '.') param['class'] = bits[2];
				else param['id'] = bits[2];
			}
			if (bits = sel.test('\\[["\'\\s]{0,1}([\\w-]*)["\'\\s]{0,1}([\\W]{0,1}=){0,2}["\'\\s]{0,1}([\\w-]*)["\'\\s]{0,1}\\]$')){
				attr['name'] = bits[1];
				attr['operator'] = bits[2];
				attr['value'] = bits[3];
			}
			if (i == 0){
				if (param['id']){
					var el = this.getElementById(param['id']);
					if (el && (param['tag'] == '*' || $(el).getTag() == param['tag'])) filters = [el];
					else return false;
				} else {
					filters = $A(this.getElementsByTagName(param['tag']));
				}
			} else {
				filters = $$(filters).filterByTagName(param['tag']);
				if (param['id']) filters = $$(filters).filterById(param['id']);
			}
			if (param['class']) filters = $$(filters).filterByClassName(param['class']);
			if (attr['name']) filters = $$(filters).filterByAttribute(attr['name'], attr['value'], attr['operator']);
		
		}, this);
		filters.each(function(el){
			$(el);
		});
		return $$(filters);
	},
	
	getElement: function(selector){
		return this.getElementsBySelector(selector)[0];
	},

	getElementsBySelector: function(selector){
		var els = [];
		selector.split(',').each(function(sel){
			els.extend(this.getElements(sel));
		}, this);
		return $$(els);
	}

});

document.extend = Object.extend;

document.extend({

	getElementsByClassName: function(className){
		return document.getElements('.'+className);
	},
	getElement: Element.prototype.getElement,
	getElements: Element.prototype.getElements,
	getElementsBySelector: Element.prototype.getElementsBySelector
	
});

var Elements = new Class({
	
	action: function(actions){
		this.each(function(el){
			el = $(el);
			if (actions.initialize) actions.initialize.apply(el);
			for(action in actions){
				var evt = false;
				if (action.test('^on[\\w]{1,}')) el[action] = actions[action];
				else if (evt = action.test('([\\w-]{1,})event$')) el.addEvent(evt[1], actions[action]);
			}
		});
	},

	filterById: function(id){
		var found = [];
		this.each(function(el){
			if (el.id == id) found.push(el);
		});
		return found;
	},

	filterByClassName: function(className){
		var found = [];
		this.each(function(el){
			if ($Element(el, 'hasClassName', className)) found.push(el);
		});
		return found;
	},

	filterByTagName: function(tagName){
		var found = [];
		this.each(function(el){
			found.extend($A(el.getElementsByTagName(tagName)));
		});
		return found;
	},
	
	filterByAttribute: function(name, value, operator){
		var found = [];
		this.each(function(el){
			var att = el.getAttribute(name);
			if(!att) return;
			if (!operator) return found.push(el);
			
			switch(operator){
				case '*=': if (att.test(value)) found.push(el); break;
				case '=': if (att == value) found.push(el); break;
				case '^=': if (att.test('^'+value)) found.push(el); break;
				case '$=': if (att.test(value+'$')) found.push(el);
			}

		});
		return found;
	}

});

new Object.Native(Elements);

var Ajax = ajax = new Class({

	setOptions: function(options){
		this.options = {
			method: 'post',
			postBody: null,
			async: true,
			onComplete: Class.empty,
			onStateChange: Class.empty,
			onFailure: Class.empty,
			update: null,
			evalScripts: false
		};
		Object.extend(this.options, options || {});
	},

	initialize: function(url, options){
		this.setOptions(options);
		this.url = url;
		this.transport = this.getTransport();
	},

	request: function(){		
		this.transport.open(this.options.method, this.url, this.options.async);		
		this.transport.onreadystatechange = this.onStateChange.bind(this);
		if (this.options.method == 'post'){
			this.transport.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			if (this.transport.overrideMimeType) this.transport.setRequestHeader('Connection', 'close');
		}
		switch($type(this.options.postBody)){
			case 'element': this.options.postBody = $(this.options.postBody).toQueryString(); break;
			case 'object': this.options.postBody = Object.toQueryString(this.options.postBody);
		}
		this.transport.send(this.options.postBody);
		return this;
	},

	onStateChange: function(){
		this.options.onStateChange.delay(10, this);
		if (this.transport.readyState != 4) return;
		if (!this.transport.status || (this.transport.status >= 200 && this.transport.status < 300)){
			if (this.options.update) $(this.options.update).setHTML(this.transport.responseText);
			this.options.onComplete.pass([this.transport.responseText, this.transport.responseXML], this).delay(20);
			if (this.options.evalScripts) this.evalScripts.delay(30, this);
		} else this.options.onFailure.delay(20, this);
		this.transport.onreadystatechange = Class.empty;
		this.callChain();
	},

	evalScripts: function(){
		if(scripts = this.transport.responseText.match(/<script[^>]*?>[\S\s]*?<\/script>/g)){
			scripts.each(function(script){
				eval(script.replace(/^<script[^>]*?>/, '').replace(/<\/script>$/, ''));
			});
		}
	},

	getTransport: function(){
		if (window.XMLHttpRequest) return new XMLHttpRequest();
		else if (window.ActiveXObject) return new ActiveXObject('Microsoft.XMLHTTP');
	}

});

Ajax.implement(new Chain);

var fjax = {
	excuteCommand : function(command,agrs,callback,xml){
		$('fjax').setVariable("/fajxObj:callBackName",callback);
		$('fjax').setVariable("/fajxObj:xmlName",xml);
		$('fjax').setVariable("/fajxObj:agrs",agrs);
		$('fjax').setVariable("/fajxObj:command",command);			
	},
	loadXml : function(agrs,callback,xml){
		this.excuteCommand("FJAX.loadXml",agrs,callback,xml);	
	},
	getNode : function(callback,path,xml){
		var agrs = callback +",getNode," + path;
		this.excuteCommand("FJAX.getXml",agrs,callback,xml);
		this.getVar.pass(callback, this).delay(30);	
	},
	getNodes : function(callback,path,xml){
		var agrs = callback +",getNodes," + path;
		this.excuteCommand("FJAX.getXml",agrs,callback,xml);
	},
	getAttribute : function(callback,path,xml){
		var agrs = callback +",getAttribute," + path;
		this.excuteCommand("FJAX.getXml",agrs,callback,xml);
	},
	getAttributes : function(callback,path,xml){
		var agrs = callback +",getAttrbutes," + path;
		this.excuteCommand("FJAX.getXml",agrs,callback,xml);
	},
	getVar : function(a){
		try{
			if($('fjax').getVariable(a) != ""){
				eval(a)($('fjax').getVariable(a));					
			}else{
				this.getVar.pass(a, this).delay(30);	
			}
		}catch(e){
			this.getVar.pass(a, this).delay(30);	
		}
	}
};


Object.toQueryString = function(source){
	var queryString = [];
	for (property in source) queryString.push(encodeURIComponent(property)+'='+encodeURIComponent(source[property]));
	return queryString.join('&');
};

Element.extend({

	send: function(options){
		options = Object.extend(options, {postBody: this.toQueryString(), method: 'post'});
		return new Ajax(this.getProperty('action'), options).request();
	},

	toQueryString: function(){
		var queryString = [];
		$A(this.getElementsByTagName('*')).each(function(el){
			$(el);
			var name = $check(el.name);
			if (!name) return;
			var value = false;
			switch(el.getTag()){
				case 'select': value = el.getElementsByTagName('option')[el.selectedIndex].value; break;
				case 'input': if ( (el.checked && ['checkbox', 'radio'].test(el.type)) || (['hidden', 'text', 'password'].test(el.type)) ) 
					value = el.value; break;
				case 'textarea': value = el.value;
			}
			if (value) queryString.push(encodeURIComponent(name)+'='+encodeURIComponent(value));
		});
		return queryString.join('&');
	}

});

Element.extend({
	//µå·¡±× °¡´ÉÇÏ°Ô ¸¸µé¾î¶ó optionÀ¸·Î 
	makeDraggable: function(options){
		return new Drag.Move(this, options);
	},

	makeResizable: function(options){
		return new Drag.Base(this, 'width', 'height', options);
	}

});

var Window = {
	
	extend: Object.extend,
	
	getWidth: function(){
		return window.innerWidth || document.documentElement.clientWidth || 0;
	},
	
	getHeight: function(){
		return window.innerHeight || document.documentElement.clientHeight || 0;
	},
	
	getScrollHeight: function(){
		return document.documentElement.scrollHeight;
	},
	
	getScrollWidth: function(){
		return document.documentElement.scrollWidth;
	},
	
	getScrollTop: function(){
		return document.documentElement.scrollTop || window.pageYOffset || 0;
	},
	
	getScrollLeft: function(){
		return document.documentElement.scrollLeft || window.pageXOffset || 0;
	},
	
	onLoad: function(fn){
		if (!document.body) return Window.onLoad.pass(fn).delay(50);
		else return fn();
	},
	onDomReady: function(init){
        if(!this._readyCallbacks) {
            var domReady = function(){
                if (arguments.callee.done) return;
                arguments.callee.done = true;
                this._timer = $clear(this._timer);
                this._readyCallbacks.each(function(f) { f(); });
                this._readyCallbacks = null;
            }.bind(this);
            var state = document.readyState;
            if (state && document.childNodes && !document.all && !navigator.taintEnabled){ //khtml
                this._timer = function(){
                    if (document.readyState.test(/loaded|complete/)) domReady();
                }.periodical(50);
            } else if (state && window.ActiveXObject){ //ie
                document.write("<script id=_ie_ready_ defer src=javascript:void(0)><\/script>");
                $('_ie_ready_').onreadystatechange = function(){
                    if (this.readyState == 'complete') domReady();
                };
            } else { //others
                window.addEvent("load", domReady);
                document.addEvent("DOMContentLoaded", domReady);
            }
            this._readyCallbacks = [];
        }
        this._readyCallbacks.push(init);
    }
};

var Cookie = {

	set: function(key, value, duration){
		var date = new Date();
		date.setTime(date.getTime()+((duration || 365)*86400000));
		document.cookie = key+"="+value+"; expires="+date.toGMTString()+"; path=/";
	},

	get: function(key){
		var myValue, myVal;
		document.cookie.split(';').each(function(cookie){
			if(myVal = cookie.trim().test(key+'=(.*)')) myValue = myVal[1];
		});
		return myValue;
	},

	remove: function(key){
		this.set(key, '', -1);
	}

};

var Json = {
	toString: function(el){
		
		var string = [];
		
		var isArray = function(array){
			var string = [];
			array.each(function(ar){
				string.push(Json.toString(ar));
			});
			return string.join(',');
		};
		
		var isObject = function(object){
			var string = [];
			for (property in object) string.push('"'+property+'":'+Json.toString(object[property]));
			return string.join(',');
		};
		
		switch($type(el)){
			case 'string': string.push('"'+el+'"'); break;
			case 'function': string.push(el); break;
			case 'object': string.push('{'+isObject(el)+'}'); break;
			case 'array': string.push('['+isArray(el)+']');
		}
		
		return string.join(',');
	},

	evaluate: function(str){
		return eval('(' + str + ')');
	}
};

var Sortables = new Class({	
	setOptions: function(options) {
		this.options = {
			handles: false,
			fxDuration: 250,
			fxTransition: Fx.sinoidal,
			maxOpacity: 0.5
		};
		Object.extend(this.options, options || {});
	},

	initialize: function(elements, options){		
		this.setOptions(options);
		
		this.options.handles = this.options.handles || elements;
		var trash = new Element('div').injectInside($(document.body));				
		$A(elements).each(function(el, i){			
			var copy = $(el).clone().setStyles({
				'position': 'absolute',
				'opacity': '0',
				'display': 'none'
			}).injectInside(trash);
			var elEffect = el.effect('opacity', {duration: this.options.fxDuration, wait: false, transition: this.options.fxTransition}).set(1);
			var copyEffects = copy.effects({
				duration: this.options.fxDuration,
				wait: false,
				transition: this.options.fxTransition,
				onComplete: function(){
					copy.setStyle('display', 'none');
				}
			});
			var dragger = new Drag.Move(copy, {				
				onStart: function(){
					copy.setHTML(el.innerHTML).setStyles({
						'display': 'block',
						'opacity': this.options.maxOpacity,
						'top': el.getTop()+'px',
						'left': el.getLeft()+'px'						
					});
					elEffect.custom(elEffect.now, this.options.maxOpacity);
				}.bind(this),
				onComplete: function(){
					copyEffects.custom({'opacity': [this.options.maxOpacity, 0], 'top': [copy.getTop(), el.getTop()]});
					elEffect.custom(elEffect.now, 1);
				}.bind(this),
				onDrag: function(){
					if ( el.getPrevious() && copy.getTop() < (el.getPrevious().getTop()) ) el.injectBefore(el.getPrevious());
					else if ( el.getNext() && copy.getTop() > (el.getNext().getTop()) ) el.injectAfter(el.getNext());
				}
			});
			this.options.handles[i].onmousedown = dragger.start.bind(dragger);
		}, this);
	}

});

Fx.Styles = Fx.Base.extend({

	initialize: function(el, options){
		this.el = $(el);
		this.setOptions(options);
		this.now = {};
	},

	setNow: function(){
		for (p in this.from) this.now[p] = this.compute(this.from[p], this.to[p]);
	},

	custom: function(objFromTo){
		var from = {};
		var to = {};
		for (p in objFromTo){
			from[p] = objFromTo[p][0];
			to[p] = objFromTo[p][1];
		}
		return this.parent(from, to);
	},

	resizeTo: function(hto, wto){
		return this.custom({'height': [this.el.offsetHeight, hto], 'width': [this.el.offsetWidth, wto]});
	},

	resizeBy: function(hby, wby){
		return this.custom({'height': [this.el.offsetHeight, this.el.offsetHeight+hby], 'width': [this.el.offsetWidth, this.el.offsetWidth+wby]});
	},

	increase: function(){
		for (p in this.now) this.setStyle(this.el, p, this.now[p]);
	}

});

Fx.Color = Fx.Base.extend({
	
	initialize: function(el, property, options){
		this.el = $(el);
		this.setOptions(options);
		this.property = property.camelCase();
		this.now = [];
	},

	custom: function(from, to){
		return this.parent(from.hexToRgb(true), to.hexToRgb(true));
	},

	setNow: function(){
		[0,1,2].each(function(i){
			this.now[i] = Math.round(this.compute(this.from[i], this.to[i]));
		}, this);
	},

	increase: function(){
		this.el.setStyle(this.property, "rgb("+this.now[0]+","+this.now[1]+","+this.now[2]+")");
	},

	fromColor: function(color){
		return this.custom(color, this.el.getStyle(this.property));
	},

	toColor: function(color){
		return this.custom(this.el.getStyle(this.property), color);
	}

});

Element.extend({

	effects: function(options){
		return new Fx.Styles(this, options);
	}

});

Fx.expoIn = function(pos){return Math.pow(2, 10 * (pos - 1))};
Fx.expoOut = function(pos){return (-Math.pow(2, -10 * pos) + 1)};

Fx.quadIn = function(pos){return Math.pow(pos, 2)};
Fx.quadOut = function(pos){return -(pos)*(pos-2)};

Fx.circOut = function(pos){return Math.sqrt(1 - Math.pow(pos-1,2))};
Fx.circIn = function(pos){return -(Math.sqrt(1 - Math.pow(pos, 2)) - 1)};

Fx.backIn = function(pos){return (pos)*pos*((2.7)*pos - 1.7)};
Fx.backOut = function(pos){return ((pos-1)*(pos-1)*((2.7)*(pos-1) + 1.7) + 1)};

Fx.sineOut = function(pos){return Math.sin(pos * (Math.PI/2))};
Fx.sineIn = function(pos){return -Math.cos(pos * (Math.PI/2)) + 1};
Fx.sineInOut = function(pos){return -(Math.cos(Math.PI*pos) - 1)/2};

//scriptaculous transitions
Fx.wobble = function(pos){return (-Math.cos(pos*Math.PI*(9*pos))/2) + 0.5};
Fx.pulse = function(pos){return (Math.floor(pos*10) % 2 == 0 ? (pos*10-Math.floor(pos*10)) : 1-(pos*10-Math.floor(pos*10)))};


var Tips = new Class({

	setOptions: function(options){
		this.options = {
			transitionStart: fx.sinoidal,
			transitionEnd: fx.sinoidal,
			maxTitleChars: 30,
			fxDuration: 150,
			maxOpacity: 1,
			timeOut: 100,
			className: 'tooltip'
		}
		Object.extend(this.options, options || {});
	},

	initialize: function(elements, options){
		this.elements = elements;
		this.setOptions(options);
		this.toolTip = new Element('div').addClassName(this.options.className).setStyle('position', 'absolute').injectInside(document.body);
		this.toolTitle = new Element('H4').injectInside(this.toolTip);
		this.toolText = new Element('p').injectInside(this.toolTip);
		this.fx = new fx.Style(this.toolTip, 'opacity', {duration: this.options.fxDuration, wait: false}).hide();
		$A(elements).each(function(el){
			$(el).myText = $check(el.title);
			if (el.myText) el.removeAttribute('title');
			if (el.href){
				if (el.href.test('http://')) el.myTitle = el.href.replace('http://', '');
				if (el.href.length > this.options.maxTitleChars) el.myTitle = el.href.substr(0,this.options.maxTitleChars-3)+"...";
			}
			if (el.myText && el.myText.test('::')){
				var dual = el.myText.split('::');
				el.myTitle = dual[0].trim();
				el.myText = dual[1].trim();
			} 
			el.onmouseover = function(){
				this.show(el);
				return false;
			}.bind(this);
			el.onmousemove = this.locate.bindAsEventListener(this);
			el.onmouseout = function(){
				this.timer = $clear(this.timer);
				this.disappear();
			}.bind(this);
		}, this);
	},

	show: function(el){
		this.toolTitle.innerHTML = el.myTitle;
		this.toolText.innerHTML = el.myText;
		this.timer = $clear(this.timer);
		this.fx.options.transition = this.options.transitionStart;
		this.timer = this.appear.delay(this.options.timeOut, this);
	},

	appear: function(){
		this.fx.custom(this.fx.now, this.options.maxOpacity);
	},

	locate: function(evt){
		var doc = document.documentElement;
		this.toolTip.setStyles({'top': evt.clientY + doc.scrollTop + 15 + 'px', 'left': evt.clientX + doc.scrollLeft - 30 + 'px'});
	},

	disappear: function(){
		this.fx.options.transition = this.options.transitionEnd;
		this.fx.custom(this.fx.now, 0);
	}

});

Fx.Elements = Fx.Base.extend({
	
	initialize: function(elements, options){
		this.elements = [];
		elements.each(function(el){
			this.elements.push($(el));
		}, this);
		this.setOptions(options);
		this.now = {};
	},

	setNow: function(){
		for (i in this.from){
			var iFrom = this.from[i];
			var iTo = this.to[i];
			var iNow = this.now[i] = {};
			for (p in iFrom) iNow[p] = this.compute(iFrom[p], iTo[p]);
		}
	},

	custom: function(objObjs){
		var from = {};
		var to = {};
		for (i in objObjs){
			var iProps = objObjs[i];
			var iFrom = from[i] = {};
			var iTo = to[i] = {};
			for (prop in iProps){
				iFrom[prop] = iProps[prop][0];
				iTo[prop] = iProps[prop][1];
			}
		}
		return this.parent(from, to);
	},

	increase: function(){
		for (i in this.now){
			var iNow = this.now[i];
			for (p in iNow) this.setStyle(this.elements[parseInt(i)-1], p, iNow[p]);
		}
	}

});

Fx.Accordion = Fx.Elements.extend({
	
	extendOptions: function(options){
		Object.extend(this.options, Object.extend({
			start: 'open-first',
			fixedHeight: false,
			fixedWidth: false,
			alwaysHide: false,
			wait: false,
			onActive: Class.empty,
			onBackground: Class.empty,
			height: true,
			opacity: true,
			width: false
		}, options || {}));
	},

	initialize: function(togglers, elements, options){
		this.parent(elements, options);
		this.extendOptions(options);
		this.previousClick = 'nan';
		togglers.each(function(tog, i){
			$(tog).addEvent('click', function(){this.showThisHideOpen(i)}.bind(this));
		}, this);
		this.togglers = togglers;
		this.h = {}; this.w = {}; this.o = {};
		this.elements.each(function(el, i){
			this.now[i+1] = {};
			$(el).setStyles({'height': 0, 'overflow': 'hidden'});
		}, this);
		switch(this.options.start){
			case 'first-open': this.elements[0].setStyle('height', this.elements[0].scrollHeight); break;
			case 'open-first': this.showThisHideOpen(0); break;
		}
	},

	hideThis: function(i){
		if (this.options.height) this.h = {'height': [this.elements[i].offsetHeight, 0]};
		if (this.options.width) this.w = {'width': [this.elements[i].offsetWidth, 0]};
		if (this.options.opacity) this.o = {'opacity': [this.now[i+1]['opacity'] || 1, 0]};
	},

	showThis: function(i){
		if (this.options.height) this.h = {'height': [this.elements[i].offsetHeight, this.options.fixedHeight || this.elements[i].scrollHeight]};
		if (this.options.width) this.w = {'width': [this.elements[i].offsetWidth, this.options.fixedWidth || this.elements[i].scrollWidth]};
		if (this.options.opacity) this.o = {'opacity': [this.now[i+1]['opacity'] || 0, 1]};
	},

	showThisHideOpen: function(iToShow){
		if (iToShow != this.previousClick || this.options.alwaysHide){
			this.previousClick = iToShow;
			var objObjs = {};
			var err = false;
			var madeInactive = false;
			this.elements.each(function(el, i){
				this.now[i] = this.now[i] || {};
				if (i != iToShow){
					this.hideThis(i);
				} else if (this.options.alwaysHide){
					if (el.offsetHeight == el.scrollHeight){
						this.hideThis(i);
						madeInactive = true;
					} else if (el.offsetHeight == 0){
						this.showThis(i);
					} else {
						err = true;
					}
				} else if (this.options.wait && this.timer){
					this.previousClick = 'nan';
					err = true;
				} else {
					this.showThis(i);
				}
				objObjs[i+1] = Object.extend(this.h, Object.extend(this.o, this.w));
			}, this);
			if (err) return;
			if (!madeInactive) this.options.onActive.call(this, this.togglers[iToShow]);
			this.togglers.each(function(tog, i){
				if (i != iToShow || madeInactive) this.options.onBackground.call(this, tog);
			}, this);
			return this.custom(objObjs);
		}
	}

});

var debug = {
	trace : function(msg){
		$('trace').value +=  msg + '\n';
	},
	alert : function(msg){
		alert(msg);
	},
	inspect:function(obj){
		for(var i in obj){
			this.trace(i + ':' + obj[i]);
		}
	}
}

var Drag = {
	obj : null,
	// PARAM : O ¿òÁ÷ÀÌ´Â °´Ã¼
	init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper){
		o.onmousedown	= Drag.start;
		o.hmode			= bSwapHorzRef ? false : true ;
		o.vmode			= bSwapVertRef ? false : true ;
		o.root = oRoot && oRoot != null ? oRoot : o ;
		//if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
		//if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
		//if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
		//if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";
		o.minX	= typeof minX != 'undefined' ? minX : null;
		o.minY	= typeof minY != 'undefined' ? minY : null;
		o.maxX	= typeof maxX != 'undefined' ? maxX : null;
		o.maxY	= typeof maxY != 'undefined' ? maxY : null;
		o.xMapper = fXMapper ? fXMapper : null;
		o.yMapper = fYMapper ? fYMapper : null;
		o.root.onDragStart	= new Function();
		o.root.onDragEnd	= new Function();
		o.root.onDrag		= new Function();
	},
	start : function(e){
		var o = Drag.obj = this;
		e = Drag.fixE(e);
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		o.root.onDragStart(x, y);
		o.lastMouseX	= e.clientX;
		o.lastMouseY	= e.clientY;
		if (o.hmode) {
			if (o.minX != null)	o.minMouseX	= e.clientX - x + o.minX;
			if (o.maxX != null)	o.maxMouseX	= o.minMouseX + o.maxX - o.minX;
		} else {
			if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
			if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
		}
		if (o.vmode) {
			if (o.minY != null)	o.minMouseY	= e.clientY - y + o.minY;
			if (o.maxY != null)	o.maxMouseY	= o.minMouseY + o.maxY - o.minY;
		} else {
			if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
			if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
		}
		document.onmousemove	= Drag.drag;
		document.onmouseup	= Drag.end;
		return false;
	},
	drag : function(e){
		e = Drag.fixE(e);
		var o = Drag.obj;
		var ey	= e.clientY;
		var ex	= e.clientX;
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		var nx, ny;
		if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
		if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
		if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
		if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);
		nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
		ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));
		if (o.xMapper)		nx = o.xMapper(y)
		else if (o.yMapper)	ny = o.yMapper(x)
		Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
		Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
		Drag.obj.lastMouseX	= ex;
		Drag.obj.lastMouseY	= ey;
		Drag.obj.root.onDrag(nx, ny);
		return false;
	},
	end : function(){
		document.onmousemove = null;
		document.onmouseup   = null;
		Drag.obj.root.onDragEnd(parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
		Drag.obj = null;
	},
	fixE : function(e){
		if (typeof e == 'undefined') e = window.event;
		if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
		if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
		return e;
	}
};

var move_layer_center = function(layer_obj, layer_width,layer_height) {
	// À©µµ¿ìÀÇ °¡·Î, ¼¼·Î ±æÀÌ±¸ÇÏ±â
	var winWidth, winHeight;
	if (typeof( window.innerWidth ) == 'number') {
		winWidth = window.innerWidth;
		winHeight = window.innerHeight;
		scrollX = scrollY = 0;
	} else {
		if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
			winWidth = document.documentElement.clientWidth;
			winHeight = document.documentElement.clientHeight;
			scrollX = document.documentElement.scrollLeft;
			scrollY = document.documentElement.scrollTop;
		} else {
			if (document.body && ( document.body.clientWidth || document.body.clientHeight )) {
				winWidth = document.body.clientWidth;
				winHeight = document.body.clientHeight;
				scrollX = document.body.scrollLeft;
				scrollY = document.body.scrollTop;
			}
		}
	}

	//·¹ÀÌ¾î ¿ìÃø »ó´ÜÀ¸·Î ÀÌµ¿.
	$(layer_obj).style.position ="absolute";
	$(layer_obj).style.left = (winWidth  - layer_width)/2 + scrollX + "px";
	$(layer_obj).style.top = (winHeight - layer_height)/2 + scrollY + "px";
}


var searchkeystatus = 0;
function getNavigatorType() {
	if ( navigator.appName == "Microsoft Internet Explorer" )
		return 1;
	else if ( navigator.appName == "Netscape" )
		return 2;
	else
		return 0;
}

function setTextBox(event, flag) {
	var textbox = document.forms[searchform].search;
	var _event;
	switch ( getNavigatorType() ) {
		case 1 : // IE
			_event = window.event;
			nodeName = _event.srcElement.nodeName;
			break;
		case 2 : // Netscape
			_event = event;
			nodeName = _event.target.nodeName;
			break;
		default :
			nodeName = "None";
			break;
	}
	key = _event.keyCode;
	textbox.style.backgroundImage="";
	if ( searchkeystatus == 1 && flag && key != 13) {
		textbox.value = "";
		searchkeystatus = 2;
	}
}

function SearchInit(form,tw,mql) {
	document.forms[form].search.focus();
	tw_ie=tw_ff=tw;
	if(mql>0) max_ql=mql;
	if(form.length>0) searchform=form;
}

//var ft_b_ie=56, ft_b_ff=54;		//ÀÚµ¿¿Ï¼º ·¹ÀÌ¾î TOP
//var fl_b_ie=10, fl_b_ff=10;		//ÀÚµ¿¿Ï¼º ·¹ÀÌ¾î LEFT
//var ws_ie=784, ws_ff=804;
var tw_ie=317, tw_ff=317;		//ÀÚµ¿¿Ï¼º ·¹ÀÌ¾î WIDTH
var max_ql=40;					//ÃÖ´ë ±ÛÀÚ¼ö (ÀÌ»óÀÌ¸é .....)
var searchform="search_tform";











var PrdtQuickCls = {
		quickView : function(prcode){
			if(document.getElementById && !document.getElementById("create_openwin"))
			{
				var create_openwin_div = document.createElement("div");
				create_openwin_div.id = "create_openwin";
				document.body.appendChild(create_openwin_div);
			}
			var path=quickview_path+"?productcode="+prcode;
			$('create_openwin').setStyle('display','none');
			$('create_openwin').setStyle('position','absolute');
			$('create_openwin').setStyle('zIndex','1');
			$('create_openwin').setStyle('width','550');
			$('create_openwin').setStyle('height','400');
			move_layer_center($('create_openwin'),550,400);
			var myajax = new Ajax(path,
								{
									onComplete: function(text) {
					 					var searchTag = new Element('div').setHTML(text);
					 					$('create_openwin').setHTML(searchTag.innerHTML);
										$('create_openwin').setStyle('display','block');
									},
									evalScripts : true
								}
						).request();
						return;
		},
		quickFun : function(prcode,separator){
			if(document.getElementById && !document.getElementById("create_openwin"))
			{
				var create_openwin_div = document.createElement("div");
				create_openwin_div.id = "create_openwin";
				document.body.appendChild(create_openwin_div);
			}
			var path=quickfun_path+"?productcode="+prcode+"&qftype="+separator;
			$('create_openwin').setStyle('display','none');
			$('create_openwin').setStyle('position','absolute');
			$('create_openwin').setStyle('zIndex','1');
			$('create_openwin').setStyle('width','350');
			$('create_openwin').setStyle('height','400');
			move_layer_center($('create_openwin'),350,400);
			var myajax = new Ajax(path,
								{
									onComplete: function(text) {
					 					var searchTag = new Element('div').setHTML(text);
					 					$('create_openwin').setHTML(searchTag.innerHTML);
										$('create_openwin').setStyle('display','block');
									},
									evalScripts : true
								}
						).request();
						return;
		},
		quickFun_whislist : function(prcode,mode,opts,option1,option2){
			if(document.getElementById && !document.getElementById("create_openwin"))
			{
				var create_openwin_div = document.createElement("div");
				create_openwin_div.id = "create_openwin";
				document.body.appendChild(create_openwin_div);
			}
			var path=quickfun_path+"?productcode="+prcode+"&mode="+mode+"&opts="+opts+"&option1="+option1+"&option2="+option2;
			var myajax = new Ajax(path,
								{
									onComplete: function(text) {
					 					var searchTag = new Element('div').setHTML(text);
					 					$('create_openwin').setHTML(searchTag.innerHTML);
										$('create_openwin').setStyle('display','block');
									},
									evalScripts : true
								}
						).request();
						return;
		},
		quickFun_basket : function(prcode,mode,opts,option1,option2,code,ordertype,quantity){
			if(document.getElementById && !document.getElementById("create_openwin"))
			{
				var create_openwin_div = document.createElement("div");
				create_openwin_div.id = "create_openwin";
				document.body.appendChild(create_openwin_div);
			}
			var path=quickfun_path+"?productcode="+prcode+"&mode="+mode+"&opts="+opts+"&option1="+option1+"&option2="+option2+"&code="+code+"&ordertype="+ordertype+"&quantity="+quantity;
			var myajax = new Ajax(path,
								{
									onComplete: function(text) {
					 					var searchTag = new Element('div').setHTML(text);
					 					$('create_openwin').setHTML(searchTag.innerHTML);
										$('create_openwin').setStyle('display','block');
									},
									evalScripts : true
								}
						).request();
						return;
		},
		openwinClose : function(){
			$('create_openwin').setStyle('display','none');
			$('create_openwin').setHTML("");
		},
		quickprimg_preview : function(img,width,height) {
			if(document.quickprimg!=null) {
				this.setcnt=0;
				document.quickprimg.src=img;
				document.quickprimg.width=width;
				document.quickprimg.height=height;
			} else {
				if(this.setcnt<=10) {
					this.setcnt++;
					setTimeout("primg_preview('"+img+"','"+width+"','"+height+"')",500);
				}
			}
		},
		QuickReviewMouseOver : function(cnt){
			obj = event.srcElement;
			WinObj=eval("document.all.quickreview"+cnt);
			obj._tid = setTimeout("PrdtQuickCls.QuickReviewView(WinObj)",200);
		},
		QuickReviewView : function(WinObj){
			WinObj.style.visibility = "visible";
		},
		QuickReviewMouseOut : function(cnt){
			obj = event.srcElement;
			WinObj=eval("document.all.quickreview"+cnt);
			WinObj.style.visibility = "hidden";
			clearTimeout(obj._tid);
		}
}






function getCookie(c_name) {
	cookie = document.cookie;
	index = cookie.indexOf(c_name + "=");
	if (index == -1) return "";
	index = cookie.indexOf("=", index) + 1;
	var endstr = cookie.indexOf(";", index);
	if (endstr == -1) endstr = cookie.length;
	return unescape(cookie.substring(index, endstr));
}

function setCookie(c_name, c_value) {
	document.cookie = c_name + "=" + c_value + ";";
}

function top_login_check() {
	if (document.toploginform.id.value.length==0) {
		document.toploginform.id.focus();
		alert("È¸¿ø ID¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if (document.toploginform.passwd.value.length==0) {
		document.toploginform.passwd.focus();
		alert("È¸¿ø ºñ¹Ð¹øÈ£¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if(typeof document.toploginform.ssllogin!="undefined" && document.toploginform.ssllogin.checked==true){
		document.toploginform.target = "toploginiframe";
		document.toploginform.action=document.toploginform.sslurl.value;
	}
	document.toploginform.submit();
}

function left_login_check() {
	if (document.leftloginform.id.value.length==0) {
		document.leftloginform.id.focus();
		alert("È¸¿ø ID¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if (document.leftloginform.passwd.value.length==0) {
		document.leftloginform.passwd.focus();
		alert("È¸¿ø ºñ¹Ð¹øÈ£¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if(typeof document.leftloginform.ssllogin!="undefined" && document.leftloginform.ssllogin.checked==true){
		document.leftloginform.target = "leftloginiframe";
		document.leftloginform.action=document.leftloginform.sslurl.value;
	}
	document.leftloginform.submit();
}

function main_login_check() {
	if (document.mainloginform.id.value.length==0) {
		document.mainloginform.id.focus();
		alert("È¸¿ø ID¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if (document.mainloginform.passwd.value.length==0) {
		document.mainloginform.passwd.focus();
		alert("È¸¿ø ºñ¹Ð¹øÈ£¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
		return;
	}
	if(typeof document.mainloginform.ssllogin!="undefined" && document.mainloginform.ssllogin.checked==true){
		document.mainloginform.target = "mainloginiframe";
		document.mainloginform.action=document.mainloginform.sslurl.value;
	}
	document.mainloginform.submit();
}

function TopCheckKeyLogin() {
	key=event.keyCode;
	if (key==13) {
		top_login_check();
	}
}

function LeftCheckKeyLogin() {
	key=event.keyCode;
	if (key==13) {
		left_login_check();
	}
}

function MainCheckKeyLogin() {
	key=event.keyCode;
	if (key==13) {
		main_login_check();
	}
}

var TopSearchKeyword_before="";
function TopSearchCheck() {
	try {
		if(document.search_tform.search.value.length<2) {
			alert("2±ÛÀÚ ÀÌ»ó ÀÔ·ÂÇØÁÖ¼¼¿ä.");
			document.search_tform.search.focus();
			return;
		}
		if(TopSearchKeyword_before==document.search_tform.search.value){
			alert("ÀÌ¹Ì °Ë»öÁßÀÔ´Ï´Ù. Àá½Ã ±â´Ù·ÁÁÖ½Ê½Ã¿À.");
			return;
		}else TopSearchKeyword_before=document.search_tform.search.value;
		document.search_tform.keyword_word.value = document.search_tform.search.value;
		//document.search_tform.action="/front/productsearch.php";
		document.search_tform.action="/search/keyword_search.php";
		document.search_tform.submit();
	} catch (e) {}
}

function LeftSearchCheck() {
	try {
		if(document.search_lform.search.value.length==0) {
			alert("»óÇ° °Ë»ö¾î¸¦ ÀÔ·ÂÇÏ¼¼¿ä.");
			document.search_lform.search.focus();
			return;
		}
		document.search_lform.submit();
	} catch (e) {}
}

function CheckKeyTopSearch() {
	key=event.keyCode;
	if (key==13) {
		TopSearchCheck();
	}
}

function CheckKeyLeftSearch() {
	key=event.keyCode;
	if (key==13) {
		LeftSearchCheck();
	}
}

function GoMinishop(url){
	window.open(url,'minishopPop','WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
}

function chkFieldMaxLen(max) {
    var obj = event.srcElement;

    if (obj.value.bytes() > max) {
        alert("ÀÔ·ÂÇÒ ¼ö ÀÖ´Â Çã¿ë ¹üÀ§°¡ ÃÊ°úµÇ¾ú½À´Ï´Ù.\n\n" + "ÇÑ±Û" + max/2 + "ÀÚ ÀÌ³» È¤Àº ¿µ¹®/¼ýÀÚ/±âÈ£ " + max + "ÀÚ ÀÌ³»·Î ÀÔ·ÂÀÌ °¡´ÉÇÕ´Ï´Ù.");
        obj.value = obj.value.cut(max);
        obj.focus();
    }
}

function CheckLength(obj) {
	var data = obj.value;
	var numstr = "!@#$%^&*()_+|-=\,./?><0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
		else
			count = count + 2;
	}		
	return  count;
}

function IsAlphaNumeric(data) {
	var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
	}		
	if ( count == data.length )
		return(true);
	else
		return( false );
}	

function IsNumeric(data) {
	var numstr = "0123456789";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
	}		
	if ( count == data.length )
		return(true);
	else
		return( false );
}	

function strnumkeyup(field) {
	if (!isNumber(field.value)) {
		alert("¼ýÀÚ¸¸ ÀÔ·ÂÇÏ¼¼¿ä.");
		field.value=strLenCnt(field.value,field.value.length - 1);
		field.focus();
		return;
	}
}

function strLenCnt(str,lengths) {	//¹®ÀÚ¿­ÀÇ Æ¯Á¤ ±æÀÌ¸¦ ¹ÝÈ¯ÇÑ´Ù.
	var len = 0;
	var newStr = '';

	for (var i=0;i<str.length; i++) {
		var n = str.charCodeAt(i);
		var nv = str.charAt(i);
		if ((n>= 0)&&(n<256)) {
			len ++;
		} else {
			len += 2;
		}

		if (len>lengths)
			break;
		else
			newStr = newStr + nv;
	}
	return newStr;
}

function isNumber(arg) {
	for (i =0 ; i < arg.length; i++) {
	  	if (arg.charCodeAt(i) < 48 || arg.charCodeAt(i) > 57) {
	  		return false;
	  	}
	}
	return true;
}

function IsMailCheck(email) {
	isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|com|kr|net|cn|org|me|info|biz|co|tv|jp|vn|io|asia|cc|mobi|name|tel|vn|email|academy|services|gov|mil|edu|int|eu|ac|moe|tw|global)$/;
	if(isMailChk.test(email)) {
		return true;
	} else {
		return false;
	}
}

function chkBizNo(obj) {
	if (obj.length == 10) {
		var bizID = obj;
		var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
		var tmpBizID, i, c2, remander;
		var chkSum = 0;

		for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);

		c2 = "0" + (checkID[8] * bizID.charAt(8));
		c2 = c2.substring(c2.length - 2, c2.length);

		chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));

		remainder = (10 - (chkSum % 10)) % 10 ;

		if (Math.floor(bizID.charAt(9)) != remainder){
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function chkResNo(obj) {
	if (obj.length == 14) {
		var calStr1 = "2345670892345", biVal = 0, tmpCal, restCal;
		
		for (i=0; i <= 12; i++) {
			if (obj.substring(i,i+1) == "-")
				tmpCal = 1
			else
				biVal = biVal + (parseFloat(obj.substring(i,i+1)) * parseFloat(calStr1.substring(i,i+1)));
		}

		restCal = 11 - (biVal % 11);

		if (restCal == 11) {
			restCal = 1;
		}

		if (restCal == 10) {
			restCal = 0;
		}

		if (restCal == parseFloat(obj.substring(13,14))) {
			return true;
		} else {
			return false;
		}
	}
}

function check_corpnum(sRegNo){
  var re = /-/g;
  sRegNo = sRegNo.replace('-','');

  if (sRegNo.length != 13){
   return false;
  }

  var arr_regno  = sRegNo.split("");
  var arr_wt   = new Array(1,2,1,2,1,2,1,2,1,2,1,2);
  var iSum_regno  = 0;
  var iCheck_digit = 0;

  for (i = 0; i < 12; i++){
    iSum_regno +=  eval(arr_regno[i]) * eval(arr_wt[i]);
  }

  iCheck_digit = 10 - (iSum_regno % 10);

  iCheck_digit = iCheck_digit % 10;

  if (iCheck_digit != arr_regno[12]){
    return false;
  }
  return true;
}

function chkNoChar(str) {
	for(i=0;i<str.length;i++) {
		if(str.charCodeAt(i)==34 || str.charCodeAt(i)==39 || str.charCodeAt(i)==92) {
			return false;
		}
	}
	return true;
}


/* onLoad Handler */
function LH_create() {
	this.LIST = new Array();
	this.add = LH_add;
	this.exec = LH_exec;
}

function LH_add(strExec) {
	this.LIST[this.LIST.length] = strExec;
}

function LH_exec() {
	var list_len = this.LIST.length;
	for (var i = 0; i < list_len; i++) {
		eval(this.LIST[i]);
	}
}

function resize_iframe(name) {
	if (name == null || name == "") {
		name = "calendar_main";
	}
	
	try {
		var oBody   = document.frames(name).document.body;
		var oIFrame = document.all(name);
		var frmWidth  = oBody.scrollWidth;
		var frmHeight = oBody.scrollHeight;
		
		oIFrame.style.height = frmHeight;
		oIFrame.style.width = frmWidth;
		window.status = "";
	} catch (e) {
		window.status = "IFrame Resize Error";
	}
}

function parent_resizeIframe(name) {
	if (parent && parent != this && parent.resize_iframe != null) {
		parent.resize_iframe(name);
	}
}

function quickfun_change_price(temp,temp2,temp3) {
	if(document.quickfun_setform.quickfun_dicker.value>0)
		return;

	if(temp3=="") temp3=1;
	var price = document.quickfun_setform.quickfun_price.value.split("|");
	if(temp==1) {
		if (document.quickfun_form1.option1.selectedIndex>(Number(document.quickfun_setform.quickfun_priceindex.value)+2)) 
			temp = document.quickfun_setform.quickfun_priceindex.value;
		else temp = document.quickfun_form1.option1.selectedIndex;
		document.quickfun_form1.price.value = price[temp];
		document.all["idx_price"].innerHTML = document.quickfun_form1.price.value+"¿ø";
	 }
	if(temp2>0 && temp3>0) {
		if(document.quickfun_setform.quickfun_num.value) {
			var quickfun_num = document.quickfun_setform.quickfun_num.value.split(",");
			if(quickfun_num[(temp3-1)*10+(temp2-1)]==0){
				alert('ÇØ´ç »óÇ°ÀÇ ¿É¼ÇÀº Ç°ÀýµÇ¾ú½À´Ï´Ù. ´Ù¸¥ »óÇ°À» ¼±ÅÃÇÏ¼¼¿ä');
				if(document.quickfun_form1.option1.type!="hidden") document.quickfun_form1.option1.focus();
				return;
			}
		}
	} else {
		if(temp2<=0 && document.quickfun_form1.option1.type!="hidden") document.quickfun_form1.option1.focus();
		else document.quickfun_form1.option2.focus();
		return;
	}
}

function quickfun_chopprice(temp){
	if(document.quickfun_setform.quickfun_dicker.value>0)
		return;
	ind = document.quickfun_form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.quickfun_form1.price.value.replace(/,/g, "");
	document.quickfun_form1.price.value=Number(originalprice)-Number(document.quickfun_form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.quickfun_form1.price.value=Number(document.quickfun_form1.price.value)+Number(optprice);
	
	document.quickfun_form1.opttype[temp].value=optprice;
	var num_str = document.quickfun_form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.quickfun_form1.price.value = result;
	document.all["idx_price"].innerHTML=document.quickfun_form1.price.value+"¿ø";
}

function quickfun_change_quantity(gbn) {
	tmp=document.quickfun_form1.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	document.quickfun_form1.quantity.value=tmp;
}

function quickfun_check_login() {
	if(confirm("·Î±×ÀÎÀÌ ÇÊ¿äÇÑ ¼­ºñ½ºÀÔ´Ï´Ù. ·Î±×ÀÎÀ» ÇÏ½Ã°Ú½À´Ï±î?")) {
		document.location.href=document.quickfun_setform.quickfun_login.value+document.location.pathname.replace(/\//gi,"%2F")+document.quickfun_setform.quickfun_login2.value;
	}
}

function quickfun_CheckForm(gbn,temp2) {
	if(gbn!="wishlist") {
		if(document.quickfun_form1.quantity.value.length==0 || document.quickfun_form1.quantity.value==0) {
			alert("ÁÖ¹®¼ö·®À» ÀÔ·ÂÇÏ¼¼¿ä.");
			document.quickfun_form1.quantity.focus();
			return;
		}
		if(!IsNumeric(document.quickfun_form1.quantity.value)) {
			alert("ÁÖ¹®¼ö·®Àº ¼ýÀÚ¸¸ ÀÔ·ÂÇÏ¼¼¿ä.");
			document.quickfun_form1.quantity.focus();
			return;
		}
		if(jQuery('[name="quickfun_setform"]').find('[name="quickfun_miniq"]').val()>1 && document.quickfun_form1.quantity.value<=1) {
			alert("ÇØ´ç »óÇ°ÀÇ ±¸¸Å¼ö·®Àº "+document.quickfun_setform.quickfun_miniq.value+"°³ ÀÌ»ó ÁÖ¹®ÀÌ °¡´ÉÇÕ´Ï´Ù.");
			document.quickfun_form1.quantity.focus();
			return;
		}
<!--   2009.3.3 Æ÷Àå´ÜÀ§ÀÇ ¹è¼ö·Î ÀÔ·Â ¹Þ±â ½ÃÀÛ -->
		var miniq = Number(document.quickfun_form1.miniq.value);
		var minip = Number(document.quickfun_form1.minip.value);
		var quantity = Number(document.quickfun_form1.quantity.value);
		
		if(miniq>1 && quantity<miniq) {
			alert("ÇØ´ç »óÇ°ÀÇ ±¸¸Å¼ö·®Àº "+miniq+"°³ ÀÌ»ó ÁÖ¹®ÀÌ °¡´ÉÇÕ´Ï´Ù.");
			document.quickfun_form1.quantity.focus();
			return;
		}
		if(minip>1 && document.quickfun_form1.quantity.value==1) {
			alert("ÇØ´ç »óÇ°ÀÇ ±¸¸Å¼ö·®Àº "+minip+"°³ ÀÌ»ó ÁÖ¹®ÀÌ °¡´ÉÇÕ´Ï´Ù.");
			document.quickfun_form1.quantity.value = minip;
			document.quickfun_form1.quantity.focus();
			return;
		}
		if (quantity > miniq){
			var a = quantity;
			var b = minip;
			var c = a/b;
			var d = Math.round(c);
			var e = b*d;
			var f = a*d;
			
			if (e < a){
				var d=d+1;
				var e = b*d;
				var f = a*d;
			}
			
			var g = e*d;
			
			if (g != f){
				if (!(confirm("¼ö·®Àº ¹Ýµå½Ã ÃÖ¼Ò±¸¸Å ¼ö·®ÀÇ ¹è¼ö·Î ÀÔ·ÂÇØ¾ß ÇÕ´Ï´Ù.\nÀÔ·ÂÇÏ½Ç ¼ö·®Àº" + e + "°³ ÀÔ´Ï´Ù.\n¹Ù·Î ÀÔ·ÂµÇµµ·Ï ÇÏ½Ã°Ú½À´Ï±î?"))) {
					return;
				}else{
					document.quickfun_form1.quantity.value = e;
					document.quickfun_form1.quantity.focus();
					return;
				}
			}
		}

<!--   2009.3.3 Æ÷Àå´ÜÀ§ÀÇ ¹è¼ö·Î ÀÔ·Â ¹Þ±â ½ÃÀÛ -->
	}
	if(gbn=="ordernow") {
		document.quickfun_form1.ordertype.value=gbn;
	}
	if(temp2!="") {
		document.quickfun_form1.opts.value="";
		try {
			if(jQuery("#co0").attr('id')){
				var co_check = check_form_customoption();
				if(co_check!="green"){
					alert('ÇÊ¼ö ÀÔ·Â Ç×¸ñÀÔ´Ï´Ù. ÀûÀýÇÑ °ªÀ» ÀÔ·ÂÇÏ°Å³ª ¼±ÅÃÇØ ÁÖ½Ê½Ã¿À.');
					jQuery("#co"+co_check).focus();						
					return;
				}
			}else{
	
				for(i=0;i<temp2;i++) {
					if(document.quickfun_form1.optselect[i].value==1 && document.quickfun_form1.mulopt[i].selectedIndex==0) {
						alert('ÇÊ¼ö¼±ÅÃ Ç×¸ñÀÔ´Ï´Ù. ¿É¼ÇÀ» ¹Ýµå½Ã ¼±ÅÃÇÏ¼¼¿ä');
						document.quickfun_form1.mulopt[i].focus();
						return;
					}
					document.quickfun_form1.opts.value+=document.quickfun_form1.mulopt[i].selectedIndex+",";
				}
			}
		} catch (e) {}
	}
	if(typeof(document.quickfun_form1.option1)!="undefined" && document.quickfun_form1.option1.selectedIndex<2) {
		alert('ÇØ´ç »óÇ°ÀÇ ¿É¼ÇÀ» ¼±ÅÃÇÏ¼¼¿ä.');
		document.quickfun_form1.option1.focus();
		return;
	}
	if(typeof(document.quickfun_form1.option2)!="undefined" && document.quickfun_form1.option2.selectedIndex<2) {
		alert('ÇØ´ç »óÇ°ÀÇ ¿É¼ÇÀ» ¼±ÅÃÇÏ¼¼¿ä.');
		document.quickfun_form1.option2.focus();
		return;
	}
	if(typeof(document.quickfun_form1.option1)!="undefined" && document.quickfun_form1.option1.selectedIndex>=2) {
		temp2=document.quickfun_form1.option1.selectedIndex-1;
		if(typeof(document.quickfun_form1.option2)=="undefined") temp3=1;
		else temp3=document.quickfun_form1.option2.selectedIndex-1;

		if(document.quickfun_setform.quickfun_num.value) {
			var quickfun_num = document.quickfun_setform.quickfun_num.value.split(",");
			if(quickfun_num[(temp3-1)*10+(temp2-1)]==0) {
				alert('ÇØ´ç »óÇ°ÀÇ ¿É¼ÇÀº Ç°ÀýµÇ¾ú½À´Ï´Ù. ´Ù¸¥ ¿É¼ÇÀ» ¼±ÅÃÇÏ¼¼¿ä');
				document.quickfun_form1.option1.focus();
				return;
			}
		}
	}

	if(jQuery("#co0").attr("id")){		
		var co_opts = "";
		for(i=0;i<10;i++){
			var coid = "co"+i;
			if(jQuery("#"+coid).attr('type')=='text'){
				var val1 = jQuery("#"+coid).val();
				var val2 = jQuery("#"+coid+"under").val();
				var valall = parseFloat(val1+"."+val2);
				if(!valall) continue;

				if(co_opts!="") co_opts = co_opts + "," + valall;
				else co_opts = valall + "";
				
			}else if(jQuery("#"+coid).attr('type')=='hidden'){
				var valall = jQuery("#"+coid).val();
				if(co_opts!="") co_opts = co_opts + "," + valall;
				else co_opts = valall + "";	
			}else{
				var vallall = jQuery("#"+coid).find("option:selected").val();
				if(typeof vallall !="undefined") {
					if(co_opts!="") co_opts = co_opts + "," + jQuery("#"+coid).find("option:selected").val();
					else co_opts = jQuery("#"+coid).find("option:selected").val();
				}
			}
		}
	}

	var option1_var = "";
	var option2_var = "";
	if(gbn!="wishlist") {
		if(co_opts) document.quickfun_form1.opts.value=co_opts;
		if(typeof(document.quickfun_form1.option1)!="undefined") option1_var=document.quickfun_form1.option1.value;
		if(typeof(document.quickfun_form1.option2)!="undefined") option2_var=document.quickfun_form1.option2.value;
		PrdtQuickCls.quickFun_basket(document.quickfun_form1.productcode.value,"basket_insert",document.quickfun_form1.opts.value,option1_var,option2_var,document.quickfun_form1.code.value,document.quickfun_form1.ordertype.value,document.quickfun_form1.quantity.value);
	} else {
		if(typeof(document.quickfun_form1.option1)!="undefined") option1_var=document.quickfun_form1.option1.value;
		if(typeof(document.quickfun_form1.option2)!="undefined") option2_var=document.quickfun_form1.option2.value;
		PrdtQuickCls.quickFun_whislist(document.quickfun_form1.productcode.value,gbn,document.quickfun_form1.opts.value,option1_var,option2_var);
	}
}


var divIdNum = null;
function getDivElement(returnid,thisObj,separator)
{
	var divElememt = "";
	var idElememt = "";
	
	if(thisObj && !separator) {
		divIdNum = "";
		if(thisObj.id && (document.all || document.layers)) {
			if(document.all)
				idElememt = document.all[thisObj.id];
			else
				idElememt = document.layers[thisObj.id];
			
			if(idElememt && idElememt.length > 1) {
				for(var i=0; i<idElememt.length; i++) {
					if(idElememt[i] == thisObj) {
						divIdNum = i+"";
						break;
					}
				}

				if(document.all)
					divElememt = document.all[returnid][divIdNum];
				else
					divElememt = document.layers[returnid][divIdNum];
			} else {
				if(document.all)
					divElememt = document.all[returnid];
				else
					divElememt = document.layers[returnid];
			}
		} else {
			divElememt = document.getElementById(returnid);
		}
	} else {
		if(divIdNum && divIdNum.length) {
			if(document.all)
				divElememt = document.all[returnid][divIdNum];
			else
				divElememt = document.layers[returnid][divIdNum];
		} else {
			divElememt = document.getElementById(returnid);
		}
	}
	return divElememt;
}

function quickfun_show(thisObj,protypecode,separator,listtype) {

	//if(!jQuery("#idxqf_"+protypecode).html()) alert("x");
	
	if(document.getElementById("idxqf_"+protypecode))
	{
		divElememt = getDivElement("idxqf_"+protypecode,thisObj,separator);
		divElememt.style.display=separator;
		
		if(separator.length == 0)
		{
			if(!divElememt.style.left)
			{
				if(listtype == "row")
				{
					divElememtMinu = -divElememt.offsetWidth;
					divElememt.style.left=divElememtMinu+"px";
					divElememtMinu2 = divElememt.offsetParent.offsetHeight-Math.ceil(divElememt.offsetParent.offsetHeight/2)-Math.ceil(divElememt.offsetHeight/2);
					divElememt.style.top=divElememtMinu2+"px";
				}
				else
				{
					divElememtMinu = divElememt.offsetParent.offsetWidth - divElememt.offsetLeft - divElememt.offsetWidth;
				
					if(divElememtMinu>0)
						divElememt.style.left=Math.ceil(divElememtMinu/2)+"px";
					else
						divElememt.style.left=divElememt.offsetLeft+"px";

					divElememtMinu2 = divElememt.offsetTop-divElememt.offsetHeight;
					divElememt.style.top=divElememtMinu2+"px";
				}
			}
		}
	}
}

function quickfun_descript(funcObj,protypecode,separator,activeValue,idir,listtype) {
	if(listtype == "row")
	{
		funcObj.src=idir+"images/common/icon_RPL"+activeValue+separator+".gif";
		if(document.getElementById("idxqft_"+protypecode+"_"+separator))
		{
			divElememt = getDivElement("idxqft_"+protypecode+"_"+separator,funcObj);

			if(activeValue=="on")
				divElememt.style.display="";
			else
				divElememt.style.display="none";
		}
	}
	else
	{
		funcObj.src=idir+"images/common/icon_PL"+activeValue+separator+".gif";
		if(document.getElementById("idxqft_"+protypecode))
		{
			divElememt = getDivElement("idxqft_"+protypecode,funcObj);

			if(activeValue=="on")
			{
				divElememt.src=idir+"images/common/icon_PLtext"+separator+".gif";
				divElememt.style.display="";
			}
			else
				divElememt.style.display="none";
		}
	}
}

function plz(productcode){
	PrdtQuickCls.quickFun(productcode,'2');
}

function quickfun_write(idir,protype,productcode,separator,listtype) {
	var quickfunc_str = quickfun_return(idir,protype,productcode,separator,listtype)
	document.write(quickfunc_str);
}

function quickfun_return(idir,protype,productcode,separator,listtype) {
	var quickfunc_str = "";
	var protypecode = protype+""+productcode;
	
	if(listtype == "row")
	{
		if(separator) // »óÇ° Àç°í°¡ ÀÖÀ» °æ¿ì
		{
			quickfunc_str +="<div id=\"idxqf_"+protypecode+"\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout01.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','01','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','01','out','"+idir+"','row');\" onclick=\"DracoquickView('"+productcode+"');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext01.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_01\" style=\"display:none;\"></td></tr>";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout02.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','02','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','02','out','"+idir+"','row');\" onclick=\"DracoquickFun('"+productcode+"','1');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext02.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_02\" style=\"display:none;\"></td></tr>\n";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout03.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','03','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','03','out','"+idir+"','row');\" onclick=\"DracoquickFun('"+productcode+"','2');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext03.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_03\" style=\"display:none;\"></td></tr>\n";
			//quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout04.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','04','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','04','out','"+idir+"','row');\" onclick=\"PrdtQuickCls.quickFun('"+productcode+"','3');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext04.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_04\" style=\"display:none;\"></td></tr>\n";
			quickfunc_str +="</table></div>";
		}
		else // »óÇ°ÀÌ Ç°ÀýÀÏ °æ¿ì
		{
			quickfunc_str +="<div id=\"idxqf_"+protypecode+"\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout01.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','01','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','01','out','"+idir+"','row');\" onclick=\"DracoquickView('"+productcode+"');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext01.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_01\" style=\"display:none;\"></td></tr>";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout02.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','02','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','02','out','"+idir+"','row');\" onclick=\"DracoquickFun('"+productcode+"','1');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext02.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_02\" style=\"display:none;\"></td></tr>\n";
			quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout03.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','03','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','03','out','"+idir+"','row');\" onclick=\"alert('Àç°í°¡ ¾ø½À´Ï´Ù.');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext03.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_03\" style=\"display:none;\"></td></tr>\n";
			//quickfunc_str +="<tr><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLout04.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','04','on','"+idir+"','row');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','04','out','"+idir+"','row');\" onclick=\"alert('Àç°í°¡ ¾ø½À´Ï´Ù.');\"></td><td valign=\"top\"><img src=\""+idir+"images/common/icon_RPLtext04.gif\" border=\"0\" id=\"idxqft_"+protypecode+"_04\" style=\"display:none;\"></td></tr>\n";
			quickfunc_str +="</table></div>";
		}
	}
	else
	{
		if(separator) // »óÇ° Àç°í°¡ ÀÖÀ» °æ¿ì
		{
			quickfunc_str +="<dl align=\"left\" id=\"idxqf_"+protypecode+"\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','01','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','01','out','"+idir+"','');\" onclick=\"DracoquickView('"+productcode+"');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','02','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','02','out','"+idir+"','');\" onclick=\"PrdtQuickCls.quickFun('"+productcode+"','1');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','03','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','03','out','"+idir+"','');\" onclick=\"PrdtQuickCls.quickFun('"+productcode+"','2');\"></dd>";
			//quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','04','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','04','out','"+idir+"','');\" onclick=\"PrdtQuickCls.quickFun('"+productcode+"','3');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;\"><img src=\""+idir+"images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_"+protypecode+"\" style=\"display:none;\"></dd>";
			quickfunc_str +="</dl>";
		}
		else // »óÇ°ÀÌ Ç°ÀýÀÏ °æ¿ì
		{
			quickfunc_str +="<dl align=\"left\" id=\"idxqf_"+protypecode+"\" style=\"position:absolute;z-index:100;bgcolor:#FFFFFF;cursor:hand;display:none;\">";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout01.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','01','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','01','out','"+idir+"','');\" onclick=\"DracoquickView('"+productcode+"');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout02.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','02','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','02','out','"+idir+"','');\" onclick=\"PrdtQuickCls.quickFun('"+productcode+"','1');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout03.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','03','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','03','out','"+idir+"','');\" onclick=\"alert('Àç°í°¡ ¾ø½À´Ï´Ù.');\"></dd>";
			//quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLout04.gif\" onMouseOver=\"quickfun_descript(this,'"+protypecode+"','04','on','"+idir+"','');\" onMouseOut=\"quickfun_descript(this,'"+protypecode+"','04','out','"+idir+"','');\" onclick=\"alert('Àç°í°¡ ¾ø½À´Ï´Ù.');\"></dd>";
			quickfunc_str +="<dd style=\"margin:0;display:inline;\"><img src=\""+idir+"images/common/icon_PLtext01.gif\" border=\"0\" id=\"idxqft_"+protypecode+"\" style=\"display:none;\"></dd>";
			quickfunc_str +="</dl>";
		}
	}
	return quickfunc_str;
}

//flash write
var embedcls = new Class({
	init : function(url,width,height) {
		this.url=url;
		this.width=width;
		this.height=height;
		this.param="<PARAM NAME=movie VALUE=\""+url+"\">";
		this.param+="<PARAM NAME=quality VALUE=high>";
		this.param+="<PARAM NAME=bgcolor VALUE=#FFFFFF>";
		this.param+="<PARAM NAME=wmode VALUE=Transparent>";
	},
	setparam : function(name,value) {
		this.param="<PARAM NAME=\""+name+"\" VALUE=\""+value+"\">"+this.param;
	},
	show : function() {
		embedstr="<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\" WIDTH=\""+this.width+"\" HEIGHT=\""+this.height+"\">";
		embedstr+=this.param;
		embedstr+="<EMBED src=\""+this.url+"\" quality=high bgcolor=#FFFFFF WIDTH="+this.width+" HEIGHT="+this.height+" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>";
		embedstr+="</OBJECT>";
		document.write(embedstr);
	}
});

var flash_show = function(url,width,height) {
	embedobj=new embedcls();
	embedobj.init(url,width,height);
	embedobj.show();
}

// ¿¢¼¿ ½ºÅ¸ÀÏÀÇ ¹Ý¿Ã¸² ÇÔ¼ö Á¤ÀÇ
function roundXL(n, digits) {
	if (digits >= 0) return parseFloat(n.toFixed(digits)); // ¼Ò¼öºÎ ¹Ý¿Ã¸²

	digits = Math.pow(10, digits); // Á¤¼öºÎ ¹Ý¿Ã¸²
	var t = Math.round(n * digits) / digits;

	return parseFloat(t.toFixed(0));
}

function check_customoption(coid){
	var underidx = coid.search("under");
	if(underidx>0) {
		coupid = coid.substring(0,underidx);
		counderid = coid;

	}else{
		coupid = coid;
		counderid = coid+"under";
	}
	var val1 = jQuery("#"+coupid).val();
	var val2 = jQuery("#"+counderid ).val();
	var valall = parseFloat(val1+"."+val2);

	var maxval = parseFloat(jQuery("#"+coupid).attr('max'));
	var minval = parseFloat(jQuery("#"+coupid).attr('min'));
	var stepval = parseFloat(jQuery("#"+coupid).attr('step'));
	var perpriceval = parseFloat(jQuery("#"+coupid).attr('perprice'));

	var r;

	if(valall>maxval) r = maxval;
	else if(valall<minval) r = minval;
	else {
		var stepval_tmp = stepval+"";
		var tmp = stepval_tmp.split(".");
		if(tmp[1]) var under_length = tmp[1].length;
		else var under_length = 0;
		var digits = Math.pow(10, under_length);

		if(under_length>0) {
			valall = valall * digits;
			minval = minval * digits;
			stepval = stepval * digits;
			r = Math.floor((valall - minval)/stepval) * stepval + minval;
			r = r / digits;			
			
		}else{
			r = Math.floor((valall - minval)/stepval) * stepval + minval;
		}
	}
	var r_txt = r+"";
	var r_arr = r_txt.split(".");
	if(!r_arr[1]) r_arr[1]="0";

	jQuery("#"+coupid).val(r_arr[0]);
	jQuery("#"+counderid ).val(r_arr[1]);

	calprice_customoption();

}

function check_customoption2(coid){
	var maxval = parseFloat(jQuery("#"+coid).attr('max'));
	var minval = parseFloat(jQuery("#"+coid).attr('min'));
	var stepval = parseFloat(jQuery("#"+coid).attr('step'));
	var perpriceval = parseFloat(jQuery("#"+coid).attr('perprice'));

	var pprice = 0;
	var total_price = 0;

	var str = "";

	jQuery("#"+coid+"_div input[type='text']").map(function () {
		if(this.value == '' && this.name ==coid+"_m") this.value=0;
		if(this.value == '' && this.name ==coid+"_c") this.value=1;
	
		if(this.name ==coid+"_m"){
			if(this.value>maxval) this.value=maxval;
			if(this.value<minval) this.value=minval;

			if(str!="") str = str+"+";
			str = str + this.value+":";

			if(this.value==0) pprice = 0;
			else pprice = Math.ceil(this.value/stepval)*perpriceval;
		}

		if(this.name ==coid+"_c"){
			str = str + this.value;
			if(pprice!=0) total_price = total_price + pprice * this.value;
		}
	});

	if(total_price != Math.round(total_price)) total_price = roundXL(total_price,1);

	jQuery("#"+coid).val(str);
	jQuery("#idx_price").html(addCommas(total_price));
	jQuery("#selloption_range0_2").val(total_price);
}

function calprice_customoption(){
	if(jQuery("#price_nocomma").val()) var org_price = parseFloat(jQuery("#price_nocomma").val());
	else var org_price = parseFloat(jQuery("[name='price']").val().replace(',',''));
	var total_price = org_price;

	for(i=0;i<10;i++){
		var coid = "co"+i;
		if(jQuery("#"+coid).attr('type')=='text'){
			var val1 = jQuery("#"+coid).val();
			var val2 = jQuery("#"+coid+"under").val();
			var valall = parseFloat(val1+"."+val2);
			if(!valall) continue;
			var perpriceval = parseFloat(jQuery("#"+coid).attr('perprice'));
			total_price += perpriceval * valall;
		}else{
			var price = parseFloat(jQuery("#"+coid).find("option:selected").attr('price'));
			if(!price) continue;
			total_price += price;
		}
	}

	if(total_price != Math.round(total_price)) total_price = roundXL(total_price,1);
	jQuery("#idx_price").html(addCommas(total_price));
	jQuery("#selloption_range0_2").val(total_price);
}

function checkForNumber(str1, str2) {
  var key = event.keyCode;
  if(str1=='co1' && key==13) check_customoption(str2);
  if(str1=='co2' && key==13) check_customoption2(str2);
  if(!(key==8||key==9||key==13||key==144||
      (key>=48&&key<=57)||key==110||key==190)) {
      event.returnValue = false;
  }
}

function checkForNumber2(str1, str2) { //Á¡±îÁö Çã¿ë
  var key = event.keyCode;
  if(!(key==8||key==9||key==13||key==46||key==144||
      (key>=48&&key<=57)||key==110||key==190)) {
      event.returnValue = false;
  }
}

function check_form_customoption(){
	//¸®ÅÏ°ªÀÌ greenÀÌ¸é Á¤»ó //¼ýÀÚ¸é idx¼ø¼­
	for(i=0;i<10;i++){
		var coid = "co"+i;
		if(!jQuery("#"+coid).attr('id')) return "green";	

		if(jQuery("#"+coid).attr('type')=='text'){
			var val1 = jQuery("#"+coid).val();
			var val2 = jQuery("#"+coid+"under").val();
			var valall = parseFloat(val1+"."+val2);
			if(!valall) return i;
		}else if(jQuery("#"+coid).attr('type')=='hidden'){
			var val1 = jQuery("#"+coid).val();
			if(val1.length==0) return i;
		}else{
			if(!jQuery("#"+coid).find("option:selected").val()) return i;
		}
	}
	return "green";	
}


function add_co_text2(str){
	var count = jQuery("#"+str+" > div").length;
	var limit = jQuery("#"+str).attr('repeat');
	if(limit<count) {alert('ÃÖ´ë '+limit+'¹ø¸¸ ÀÔ·Â °¡´É'); return 0;}

	var tmp = jQuery("#"+str+"_div_org").html();
	var currentTime = new Date()
	var t = currentTime.getTime();
	tmp = tmp.replace(/replace_str/g, t);

	jQuery("#"+str+"_div").append(tmp);
}

function del_co_text2(str){
	jQuery("#"+str).remove();	
}

function addCommas(nStr)
{
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

	function onlyNumber(obj, event){ //onKeyUp="return onlyNumber(this, event);" onBlur="onlyNumber(this, event);" 
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = obj.value;
			var tmp = inputVal.replace(/[^0-9]/gi,'');
			if(inputVal != tmp) {
				alert('¼ýÀÚ¸¸ ÀÔ·ÂÇÏ¼¼¿ä.');
				obj.value = tmp;
				return false;				
			}
			else return true;
		}
	}

	function onlyAlphabet(obj, event){ //onKeyUp="return onlyAlphabet(this, event);" onBlur="onlyAlphabet(this, event);"
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = obj.value;
			var tmp = inputVal.replace(/[^a-z]/gi,'');
			if(inputVal != tmp) {
				alert('¿µ¹®¸¸ ÀÔ·ÂÇÏ¼¼¿ä.');
				obj.value = tmp;
				return false;				
			}
			else return true;
		}
	}

	function notHangul(obj, event){ //onKeyUp="return notHangul(this, event);" onBlur="notHangul(this, event);"
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = obj.value;
			var tmp = inputVal.replace(/[^a-z0-9]/gi,'');
			if(inputVal != tmp) {
				alert('¿µ¹®°ú ¼ýÀÚ¸¸ ÀÔ·ÂÇÏ¼¼¿ä.');
				obj.value = tmp;
				return false;				
			}
			else return true;
		}
	}

	function onlyHangul(obj, event){ //onKeyUp="return onlyHangul(this, event);"
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = obj.value;
			var tmp = inputVal.replace(/[^¤¡-¤¾¤¿-¤Ó°¡-ÆR]/gi,'');
			if(inputVal != tmp) {
				alert('ÇÑ±Û¸¸ ÀÔ·ÂÇÏ¼¼¿ä.');
				obj.value = tmp;
				return false;				
			}
			else return true;
		}
	}

	function onlyHangulAlphabet(obj, event){ //onKeyUp="return onlyHangulAlphabet(this, event);"
		if (!(event.keyCode >=37 && event.keyCode<=40)) {
			var inputVal = obj.value;
			var tmp = inputVal.replace(/[^¤¡-¤¾¤¿-¤Ó°¡-ÆRa-z]/gi,'');
			if(inputVal != tmp) {
				alert('ÇÑ±Û°ú ¿µ¹®¸¸ ÀÔ·ÂÇÏ¼¼¿ä.');
				obj.value = tmp;
				return false;				
			}
			else return true;
		}
	}
