/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	,_getCommentsDiv() {
		var tmp = {};
		tmp.me = this;

		tmp.container = $(tmp.me._containerIds.comments);
		
		tmp.comments = new Element('div');
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('Comments', tmp.comments, true).addClassName('col-md-12') });
		
		tmp.me._signRandID(tmp.comments);
		
		new CommentsDivJs(tmp.me, 'Question', tmp.me._item.id)._setDisplayDivId(tmp.comments.id).render();
		
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		tmp.topics = [];
		tmp.me._item.topics.each(function(item){
			tmp.topics.push({'id': item.id, 'text': item.name});
		});
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getBasicInputDiv('name', tmp.me._item.name, $(tmp.me._containerIds.name), null ,true)
			._getBasicInputDiv('code', tmp.me._item.code, $(tmp.me._containerIds.code), null ,true)
			._getBasicInputDiv('refId', tmp.me._item.refId, $(tmp.me._containerIds.refId))
			._getBasicSelect2Div('Topic','topics', tmp.topics, $(tmp.me._containerIds.topics), "Topics" ,true)
			._getSaveBtn()
		;
		return tmp.me;
	}
	/**
	 * Public: binding all the js events
	 */
	,bindAllEventNObjects: function() {
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
});