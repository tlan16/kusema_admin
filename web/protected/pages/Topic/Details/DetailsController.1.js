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
	,_getBasicInputDiv:function(saveItem, value, container, title) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.input = new Element('input')
			.writeAttribute('save-item', saveItem)
			.setValue(value)
			.observe('change',function(e){
				tmp.input = $(this);
				tmp.value = $F(tmp.input);
				if(value !== tmp.value.trim()) {
					tmp.callback = function(result) {
						tmp.result = result;
						if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
							return;
						tmp.me._item = tmp.result.item;
						tmp.me.load();
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
		tmp.me._getBasicInputDiv('name', tmp.me._item.name, $(tmp.me._containerIds.name))
			._getBasicInputDiv('refId', tmp.me._item.refId, $(tmp.me._containerIds.refId))
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
		if(!window.opener)
			return;
		tmp.parentWindow = window.opener;
		tmp.row = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' .product_item[product_id=' + tmp.me._item.id + ']');
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