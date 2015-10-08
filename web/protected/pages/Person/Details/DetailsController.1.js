/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
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
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.subscribedTopic = [];
		tmp.me._item.subscribedTopic.each(function(item){
			tmp.subscribedTopic.push({'id': item.id, 'text': ( (item.code ? item.code+' ' : '') + item.name)});
		});
		tmp.subscribedUnit = [];
		tmp.me._item.subscribedUnit.each(function(item){
			tmp.subscribedUnit.push({'id': item.id, 'text': ( (item.code ? item.code+' ' : '') + item.name)});
		});
		tmp.enrolledTopic = [];
		tmp.me._item.enrolledTopic.each(function(item){
			tmp.enrolledTopic.push({'id': item.id, 'text': ( (item.code ? item.code+' ' : '') + item.name)});
		});
		tmp.enrolledUnit = [];
		tmp.me._item.enrolledUnit.each(function(item){
			tmp.enrolledUnit.push({'id': item.id, 'text': ( (item.code ? item.code+' ' : '') + item.name)});
		});
		tmp.me
			._getBasicInputDiv('firstName', tmp.me._item.firstName, $(tmp.me._containerIds.firstName), "First Name" ,true)
			._getBasicInputDiv('lastName', tmp.me._item.lastName, $(tmp.me._containerIds.lastName), "Last Name" ,true)
			._getBasicInputDiv('email', tmp.me._item.email, $(tmp.me._containerIds.email), null ,true)
			._getBasicSelect2Div('Topic','subscribedTopic', tmp.subscribedTopic, $(tmp.me._containerIds.subscribedTopic), "Subscribed Topic" ,true)
			._getBasicSelect2Div('Unit','subscribedUnit', tmp.subscribedUnit, $(tmp.me._containerIds.subscribedUnit), "Subscribed Unit" ,true)
			._getBasicSelect2Div('Topic','enrolledTopic', tmp.enrolledTopic, $(tmp.me._containerIds.enrolledTopic), "Enrolled Topic" ,true)
			._getBasicSelect2Div('Unit','enrolledUnit', tmp.enrolledUnit, $(tmp.me._containerIds.enrolledUnit), "Enrolled Unit" ,true)
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