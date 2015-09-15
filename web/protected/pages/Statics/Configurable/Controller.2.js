/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BPCPageJs(), {
	_configDivId: '' // the id of the config div
	,_resultDivId: '' // the id of the result div
	,_presetDivId: '' // the id of the preset div
	
	,setConfigDiv: function(configDivId) {
		this._configDivId = configDivId;
		return this;
	}
	,setResultDiv: function(resultDivId) {
		this._resultDivId = resultDivId;
		return this;
	}
	,setPresetDiv: function(presetDivId) {
		this._presetDivId = presetDivId;
		return this;
	}
	,bindPresetBtn: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('#'+tmp.me._presetDivId).find('.btn').click(function(){
			tmp.btn = jQuery(this);
			tmp.type = tmp.btn.attr('type').trim();
			tmp.entity = tmp.btn.attr('entity').trim();
			tmp.title = tmp.btn.attr('title').trim();
			tmp.searchcriterias = tmp.btn.attr('searchcriterias').trim();
			tmp.action = tmp.btn.attr('action').trim();
			
			tmp.url = ('/statics.html?type='+tmp.type+'&entity='+tmp.entity+'&action='+tmp.action+'&title='+tmp.title);
			jQuery('#'+tmp.me._resultDivId).html(tmp.iframe = jQuery('<iframe/>').attr({'src': tmp.url, 'scrolling': 'no'}));
			iFrameResize({
				log: false
				,minHeight: 400
				,sizeWidth: true
			});
		});
		
		return tmp.me;
	}
	,load: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.me.bindPresetBtn();
		
		return tmp.me;
	}
});