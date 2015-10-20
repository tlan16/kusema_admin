/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		tmp.unitSelect2Options = {
				minimumInputLength: 1,
				multiple: true,
				width: "100%",
				ajax: {
					delay: 250
					,url: '/ajax/getAll'
					,type: 'GET'
					,data: function (params) {
						return {"searchTxt": 'name like ? or code like ?', 'searchParams': ['%' + params + '%','%' + params + '%'], 'entityName': 'Unit', 'pageNo': 1};
					}
					,results: function(data, page, query) {
						tmp.result = [];
						if(data.resultData && data.resultData.items) {
							data.resultData.items.each(function(item){
								tmp.result.push({'id': item.id, 'text': (item.code ? (item.code+': ') : '') + item.name, 'data': item});
							});
						}
						return { 'results' : tmp.result };
					}
				}
				,cache: true
				,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
				};
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('firstName', tmp.me._item.firstName, $(tmp.me._containerIds.firstName), "First Name" ,true)
			._getInputDiv('lastName', tmp.me._item.lastName, $(tmp.me._containerIds.lastName), "Last Name" ,true)
			._getInputDiv('email', tmp.me._item.email, $(tmp.me._containerIds.email), null ,true)
			._getSelect2Div('Topic','subscribedTopic', tmp.me._item.id ? tmp.me._item.subscribedTopic : [], $(tmp.me._containerIds.subscribedTopic), "Subscribed Topic", false, tmp.subscribedTopicSelect2Options)
			._getSelect2Div('Unit','subscribedUnit', tmp.me._item.id ? tmp.me._item.subscribedUnit : [], $(tmp.me._containerIds.subscribedUnit), "Subscribed Unit", false, tmp.unitSelect2Options)
			._getSelect2Div('Topic','enrolledTopic', tmp.me._item.id ? tmp.me._item.enrolledTopic : [], $(tmp.me._containerIds.enrolledTopic), "Enrolled Topic")
			._getSelect2Div('Unit','enrolledUnit', tmp.me._item.id ? tmp.me._item.enrolledUnit : [], $(tmp.me._containerIds.enrolledUnit), "Enrolled Unit")
			._getSaveBtn()
		;
		return tmp.me;
	}
});