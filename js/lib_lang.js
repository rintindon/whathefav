// provide easy to use translation
// the language files are called lang_EN.js and so on
// and need to be loaded in the html section

var lib_lang = {
	lang:'',
	defaultLang:'en',
	languages:[],
	systemLang:(navigator.userLanguage || navigator.language).substr(0, 2).toLowerCase(),
	langItems:[],
	buildLanguageButtons:function(idOrObject, callback){
			var id = '';
			for (i=0; i<lib_lang.languages.length; i++){
				if (typeof(idOrObject) != 'string')
					id = idOrObject;
				else	
					id = document.getElementById(idOrObject);
				id.innerHTML = id.innerHTML + callback(lib_lang.languages[i]);
			}
		},
	addLang: function(lang){
		lib_lang.languages.push(lang);	
	},
	setLang: function(lang){
		lib_lang.lang = lang;
		//document.cookie = "lib_lang = "+lang;
		eval('this.langItems = lang_'+lib_lang.lang+';');
	},
	changeLang:function(lang, callback){
		lib_lang.setLang(lang);
		lib_lang.onLoaded(callback);
	},
	reload:function(callback, p){
		lib_lang.onLoaded(callback, p);
	},
	gt : function(textHandle){
		// returns the translated text into the previously set language
		return lib_lang.langItems[textHandle];
	},
	getText:this.gt,
	onLoaded:function(c,param){
		// convert all textes
		// set language from cookie or system, if lang is not set
		/*document.cookie.split(';').forEach( function(a){
				var b=a.split('='); 
				if (b[0]=='lib_lang') 
					lib_lang.lang = b[1];
		} );*/
		if (lib_lang.lang==''){
			lib_lang.setLang(lib_lang.systemLang);
		}
		var textes = document.querySelectorAll('[data-lang]');
		for (var i=0; i<textes.length;i++){
		// if the text is in another attribute, then set that attribute, otherwise the html
			if (textes[i].getAttribute('data-lang-type') !== null)
				textes[i].setAttribute(textes[i].getAttribute('data-lang-type'), lng(textes[i].getAttribute('data-lang')));
			else
				textes[i].innerHTML = lng(textes[i].getAttribute('data-lang'));
		}
		if (typeof(c) == 'function')
			c(param);
	}
}

// create aliases
var lng = lib_lang.gt;
var setLng = lib_lang.changeLang;

// start the translation, only if wanted
if (document.querySelectorAll('[data-lang-option="no-autostart"]').length == 0)
	window.addEventListener('load', lib_lang.onLoaded, false);