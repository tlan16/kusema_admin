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
			
			tmp.me.updateResult(tmp.type, tmp.entity, tmp.action, tmp.title, tmp.searchcriterias);
		});
		return tmp.me;
	}
	,bindConfigBtn: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('#'+tmp.me._configDivId).find('.btn').click(function(){
			tmp.btn = jQuery(this);
			tmp.type = jQuery('#'+tmp.me._configDivId+' [chart_field="type"]').val().trim();
			tmp.entity = jQuery('#'+tmp.me._configDivId+' [chart_field="entity"]').val().trim();
			tmp.title = jQuery('#'+tmp.me._configDivId+' [chart_field="title"]').val().trim();
			tmp.searchcriterias = "";
			tmp.action = jQuery('#'+tmp.me._configDivId+' [chart_field="action"]').val().trim();
			
			tmp.me.updateResult(tmp.type, tmp.entity, tmp.action, tmp.title, tmp.searchcriterias);
		});
		jQuery('#'+tmp.me._configDivId).find('input').keydown(function(e){
			if(e.keyCode == 13)
				jQuery('#'+tmp.me._configDivId).find('.btn').click();
		});
		return tmp.me;
	}
	,updateResult(type,entity,action,title,searchcriterias) {
		var tmp = {};
		tmp.me = this;
		
		if(jQuery('#'+tmp.me._resultDivId+' .loading-img').length > 0)
			return tmp.me;
		
		tmp.url = ('/statics.html?type='+type+'&entity='+entity+'&action='+action+'&title='+title);
		jQuery('#'+tmp.me._resultDivId).html(tmp.iframe = jQuery('<iframe/>').attr({'src': tmp.url, 'scrolling': 'no'}));
		iFrameResize({
			log: false
			,minHeight: 400
			,sizeWidth: true
		});
		jQuery('#'+tmp.me._configDivId+' [chart_field="type"]').val(type);
		jQuery('#'+tmp.me._configDivId+' [chart_field="entity"]').val(entity);
		jQuery('#'+tmp.me._configDivId+' [chart_field="action"]').val(action);
		
		return tmp.me;
	}
	,load: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.me.bindPresetBtn().bindConfigBtn();
		
		return tmp.me;
	}
});