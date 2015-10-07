/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	/**
	 * Getting a form group for forms
	 */
	,_getFormGroup: function (label, input, noFormControl) {
		return new Element('div', {'class': 'form-group form-group-sm'})
			.insert({'bottom': new Element('label').update(label) })
			.insert({'bottom': input.addClassName(noFormControl === true ? '' : 'form-control') });
	}
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	,_getBasicInputDiv:function(saveItem, value, container, title, required) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.input = new Element('input')
			.writeAttribute('required', tmp.required)
			.writeAttribute('save-item', saveItem)
			.setValue(value)
			.observe('change',function(e){
				if(!tmp.me._item || !tmp.me._item.id)
					return tmp.me;
				tmp.input = $(this);
				tmp.value = $F(tmp.input);
				if(value !== tmp.value.trim()) {
					tmp.callback = function(result) {
						tmp.result = result;
						if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
							return;
						tmp.me._item = tmp.result.item;
						tmp.me.load();
						tmp.me.refreshParentWindow();
					};
					tmp.me.saveItem(tmp.input, {
						'value': tmp.value
						,'field': tmp.input.readAttribute('save-item')
						,'entityId': tmp.me._item.id
					}, tmp.callback);
				}
			});
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName('col-md-12') );
		
		return tmp.me;
	}
	,_getBasicSelect2Div:function(saveItem, value, container, title, required) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.select2 = new Element('select')
			.writeAttribute('multiple', 'multiple')
			.writeAttribute('required', tmp.required)
			.writeAttribute('save-item', saveItem);
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.select2).addClassName('col-md-12') );
		
		tmp.me._signRandID(tmp.select2);
		console.debug(tmp.select2);
		
		tmp.data = value;
		tmp.value = [];
		tmp.data.each(function(item){
			//console.debug(item);
			tmp.value.push(item.id);
			tmp.select2.insert({'bottom': new Element('option', {'value': item.id, 'selected': true}).update(item.text) });
		});
		
//		console.debug(tmp.data);
//		console.debug(tmp.value);
		
		jQuery('#' + tmp.select2.id).select2({
			minimumInputLength: 3
			,multiple: true
			,data: tmp.data
//			,closeOnSelect: false
//			,val: tmp.value
			,ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'POST'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params.term + '%'], 'entityName': 'Topic'};
				}
				,processResults: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': item.name, 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		})
		.on("change", function(e) {
			if($(this).value)
				console.debug($(this).value);
        });
		console.debug('tmp.data');
		console.debug(tmp.data);
//		jQuery('#'+tmp.select2.id).select2('data', tmp.data);
       jQuery('#'+tmp.select2.id).val(tmp.value);
		
		return tmp.me;
	}
	,_closeFancyBox() {
		if(parent.jQuery && parent.jQuery.fancybox)
			parent.jQuery.fancybox.close();
		return this;
	}
	,_getSaveBtn:function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.container = $(tmp.me._containerIds.saveBtn);
		tmp.input = new Element('i')
			.addClassName('btn btn-success btn-md')
			.update('Save')
			.observe('click',function(e){
				if(tmp.me._item && tmp.me._item.id)
					tmp.me._closeFancyBox();
				tmp.btn = $(this);
				tmp.value = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
				if(tmp.btn.readAttribute('disabled') === "disabled" || tmp.value === null)
					return tmp.me;
				tmp.btn.writeAttribute('disabled', true);
				tmp.callback = function(result) {
					// TODO: close the pop
//					tmp.me.refreshParentWindow();
				};
				tmp.me.saveItem(tmp.input, {
					'value': tmp.value
					,'entityId': 'new'
				}, tmp.callback);
			});
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName('col-md-12') );
		
		return tmp.me;
	}
	,_elTojQuery(el) {
		var tmp = {};
		tmp.me = this;
		tmp.el = (el || null);
		if(tmp.el === null)
			return null;
		tmp.me._signRandID(tmp.el);
		tmp.el = jQuery('#'+tmp.el.id);
		return tmp.el;
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
			tmp.subscribedTopic.push({'id': parseInt(item.id), 'text': item.name});
		});
		tmp.me
			._getBasicInputDiv('firstName', tmp.me._item.firstName, $(tmp.me._containerIds.firstName), "First Name" ,true)
			._getBasicInputDiv('lastName', tmp.me._item.lastName, $(tmp.me._containerIds.lastName), "Last Name" ,true)
			._getBasicInputDiv('email', tmp.me._item.email, $(tmp.me._containerIds.email), null ,true)
			._getBasicSelect2Div('subscribedTopic', tmp.subscribedTopic, $(tmp.me._containerIds.subscribedTopic), "Subscribed Topic" ,true)
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
	,refreshParentWindow: function() {
		var tmp = {};
		tmp.me = this;
		if(!parent.window)
			return;
		tmp.parentWindow = parent.window;
		tmp.row = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' .item_row[item_id=' + tmp.me._item.id + ']');
		if(tmp.row) {
			tmp.row.replace(tmp.parentWindow.pageJs._getResultRow(tmp.me._item));
			if(tmp.row.hasClassName('success'))
				tmp.row.addClassName('success');
		}
		tmp.newPObtn = $(tmp.parentWindow.document.body).down('#' + tmp.me._btnIdNewPO);
		if(tmp.newPObtn) {
			tmp.parentWindow.pageJs.selectProduct(tmp.me._item, tmp.newPObtn);
		}
	}
});