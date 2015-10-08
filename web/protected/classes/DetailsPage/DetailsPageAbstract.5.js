/**
 * The DetailsPageJs file
 */
var DetailsPageJs = new Class.create();
DetailsPageJs.prototype = Object.extend(new BPCPageJs(), {
	_item: null //the item we are dealing with

	/**
	 * Getting a form group for forms
	 */
	,_getFormGroup: function (label, input, noFormControl) {
		return new Element('div', {'class': 'form-group form-group-sm'})
			.insert({'bottom': new Element('label').update(label) })
			.insert({'bottom': input.addClassName(noFormControl === true ? '' : 'form-control') });
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
		} else if($(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body')) {
			console.debug(tmp.me._item);
			$(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body').insert({'top': tmp.parentWindow.pageJs._getResultRow(tmp.me._item) });
		}
	}
	,_getSaveBtn:function() {
		var tmp = {};
		tmp.me = this;
		
		if(!tmp.me._containerIds || !tmp.me._containerIds.saveBtn)
			return tmp.me;
		tmp.container = $(tmp.me._containerIds.saveBtn);
		if(!tmp.container)
			return tmp.me;
		tmp.input = new Element('i')
			.addClassName('btn btn-success btn-md')
			.update('Save')
			.observe('click',function(e){
				tmp.btn = $(this);
				if(tmp.me._item && tmp.me._item.id) {
					tmp.me._closeFancyBox();
					return tmp.me;
				}
				tmp.value = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
				if(tmp.btn.readAttribute('disabled') === "disabled" || tmp.value === null)
					return tmp.me;
				tmp.btn.writeAttribute('disabled', true);
				tmp.callback = function(result) {
					tmp.result = result;
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					tmp.me._item = tmp.result.item;
					tmp.me._closeFancyBox();
					tmp.me.refreshParentWindow();
				};
				tmp.me.saveItem(tmp.input, {
					'value': tmp.value
					,'entityId': 'new'
				}, tmp.callback);
			});
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName('col-md-12') );
		
		return tmp.me;
	}
	,_closeFancyBox() {
		if(parent.jQuery && parent.jQuery.fancybox)
			parent.jQuery.fancybox.close();
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
	,_getBasicSelect2Div:function(searchEntityName, saveItem, value, container, title, required) {
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
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.select2, true).addClassName('col-md-12') );
		
		tmp.me._signRandID(tmp.select2);
		
		tmp.data = value;
		tmp.value = [];
		tmp.data.each(function(item){
			tmp.value.push(item.id);
			tmp.select2.insert({'bottom': new Element('option', {'value': item.id, 'selected': true}).update(item.text) });
		});
		
		
		jQuery('#' + tmp.select2.id).select2({
			minimumInputLength: 1
			,multiple: true
			,data: tmp.data
			,width: "100%"
			,ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'POST'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params.term + '%'], 'entityName': searchEntityName};
				}
				,processResults: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': ( (item.code ? item.code+' ' : '') + item.name), 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		})
		.val(tmp.value)
		.on("change", function(e) {
			tmp.select2 = jQuery('#' + tmp.select2.id);
			tmp.value = tmp.select2.val();
			tmp.select2.prop('disabled', true);
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
				,'field': tmp.select2.attr('save-item')
				,'entityId': tmp.me._item.id
			}, tmp.callback);
        });
		
		return tmp.me;
	}
	
	,setItem: function(item) {
		this._item = item;
		return this;
	}
	,saveItem: function(btn, data, onSuccFunc) {
		var tmp = {};
		tmp.me = this;
		if(btn) {
			tmp.me._signRandID(btn);
			jQuery('#' + btn.id).prop('disabled',true);
		}
		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), data, {
			'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					if(typeof(onSuccFunc) === 'function')
						onSuccFunc(tmp.result);
				} catch (e) {
					tmp.me.showModalBox('<strong class="text-danger">ERROR:</strong>', e);
				}
			}
			, 'onComplete': function() {
				if(btn) {
					jQuery('#' + btn.id).prop('disabled',false);
				}
			}
		});
		return tmp.me;
	}

	,_init: function(){
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}

	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		$(tmp.me.getHTMLID('itemDiv')).update(tmp.me._getItemDiv());
		return tmp.me;
	}
});