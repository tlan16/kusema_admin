/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getInputDiv('code', (tmp.me._item.code || ''), $(tmp.me._containerIds.code), null, true)
			._getSelect2Div('Topic','topics', tmp.topics, $(tmp.me._containerIds.topics), "Topics" ,true)
			._getSaveBtn()
		;
		return tmp.me;
	}
});