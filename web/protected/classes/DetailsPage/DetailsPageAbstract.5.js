/**
 * The DetailsPageJs file
 */
var DetailsPageJs = new Class.create();
DetailsPageJs.prototype = Object.extend(new BPCPageJs(), {
	_item: null //the item we are dealing with
	,_dirty: false
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
			if(!tmp.row.hasClassName('success'))
				tmp.row.addClassName('success');
		} else if($(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body')) {
			$(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body').insert({'top': tmp.parentWindow.pageJs._getResultRow(tmp.me._item) });
			if(tmp.totalEl = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.totalNoOfItemsId))
				tmp.totalEl.update(parseInt(tmp.totalEl.innerHTML) + 1);
		}
	}
	,_getSaveBtn:function() {
		var tmp = {};
		tmp.me = this;
		tmp.me._refreshDirty();
		if(!tmp.me._containerIds || !tmp.me._containerIds.saveBtn)
			return tmp.me;
		tmp.container = $(tmp.me._containerIds.saveBtn);
		if(!tmp.container)
			return tmp.me;
		tmp.save = new Element('i')
			.addClassName('btn btn-success btn-md')
			.update('Save')
			.observe('click',function(e){
				tmp.btn = $(this);
				tmp.data = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
				if(tmp.btn.readAttribute('disabled') === true || tmp.btn.readAttribute('disabled') === 'disabled' || tmp.data === null)
					return tmp.me;
				tmp.me._disableAll($(tmp.me.getHTMLID('itemDiv')));
				if(tmp.data === null)
					return tmp.me;
				if(tmp.me._item && tmp.me._item.id)
					tmp.data.id = tmp.me._item.id;
				tmp.me.saveItem(tmp.input, tmp.data);
			});
		tmp.cancel = new Element('i')
			.addClassName('btn btn-danger btn-md')
			.update('Cancel')
			.observe('click',function(e){
				tmp.me.closeFancyBox();
			});
		
		tmp.container.update('')
			.insert({'bottom': tmp.me._getFormGroup(tmp.title, tmp.save).addClassName('col-md-6') })
			.insert({'bottom': tmp.me._getFormGroup(tmp.title, tmp.cancel).addClassName('pull-right col-md-6') })
		;
		
		if(tmp.me._dirty === false)
			tmp.save.hide();
		return tmp.me;
	}
	,closeFancyBox:function () {
		if(parent.jQuery && parent.jQuery.fancybox)
			parent.jQuery.fancybox.close();
		return this;
	}
	,_getDatePickerDiv:function(saveItem, value, container, title, required, format, className) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-md-12');
		tmp.format = (format || 'DD/MM/YYYY');
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.input = new Element('input')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'dirty': false
			})
			.setValue(value || '')
			;
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName(tmp.className) );
		
		if(typeof jQuery(document).datetimepicker !== 'function')
			return tmp.me;
		
		tmp.me._signRandID(tmp.input);
		tmp.datepicker = jQuery('#'+tmp.input.id).datetimepicker({
			format: tmp.format
			,showClear: !required
		});
		tmp.datepicker.on('dp.change keyup',function(e){
			if(tmp.datepicker.data('DateTimePicker') && tmp.datepicker.data('DateTimePicker').date()) {
				tmp.newValue =tmp.datepicker.data('DateTimePicker').date().local().format('YYYY-MM-DDThh:mm:ss');
				if(saveItem.endsWith('from'))
					tmp.newValue.format('YYYY-MM-DDT00:00:00');
				if(saveItem.endsWith('to'))
					tmp.newValue.format('YYYY-MM-DDT23:59:59');
			}
			else tmp.newValue = '';
			
			console.debug(tmp.newValue);
			tmp.input.writeAttribute('dirty', value !== tmp.newValue);
			tmp.me._refreshDirty()._getSaveBtn();
		});
		
//		typeof jQuery(document).datetimepicker
//		tmp.date = jQuery('#' + item.id).data('DateTimePicker').date();
//		tmp.me._searchCriteria[tmp.field] = tmp.date ? new Date(tmp.date.local().format('YYYY-MM-DDT' + (tmp.field === 'orderDate_from' || tmp.field === 'invDate_from' ? '00:00:00' : '23:59:59'))) : '';
		
		return tmp.me;
	}
	,_getMarkdownDiv(saveItem, value, container, title, required, className) {
		var tmp = {};
		tmp.me = this;
		
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-md-12');

		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return tmp.me;
		
		tmp.content = new Element('textarea')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'dirty': false
			})
			.setValue(value || '');
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.content, true).addClassName(tmp.className) );
		
		if(typeof jQuery(document).markdown !== 'function')
			return tmp.me;
		
		tmp.content = tmp.me._elTojQuery(tmp.content);
		tmp.content.markdown({
			iconlibrary: 'fa'
			,onChange: function(e){
				tmp.content.attr('dirty', e.isDirty() === true);
			} 
			,onBlur: function(e){
				tmp.me._refreshDirty()._getSaveBtn();
			} 
		});
		
		return tmp.me;
	}
	,_getInputDiv:function(saveItem, value, container, title, required, className, isCurrency) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-md-12');
		tmp.isCurrency = (isCurrency === true);
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.input = new Element('input')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'dirty': false
			})
			.setValue(value || '')
			.observe('change',function(e){
				if(tmp.isCurrency === true)
					tmp.input.setValue(tmp.me.getValueFromCurrency($F(tmp.input)));
			})
			.observe('keyup',function(e){
				tmp.input.writeAttribute('dirty', value !== (tmp.isCurrency === true ? tmp.me.getValueFromCurrency($F(tmp.input)) : $F(tmp.input) ) );
				tmp.me._refreshDirty()._getSaveBtn();
			});
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName(tmp.className) );
		
		return tmp.me;
	}
	,_refreshDirty: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.dirty = false;
		$(tmp.me.getHTMLID('itemDiv')).getElementsBySelector('[save-item]').each(function(el){
			if(tmp.dirty === false && (el.readAttribute('dirty') === true || el.readAttribute('dirty') === 'true' || el.readAttribute('dirty') === 'dirty') )
				tmp.dirty = true;
		});
		
		tmp.me._dirty = tmp.dirty;
		return tmp.me;
	}
	,_getSelect2Div:function(searchEntityName, saveItem, value, container, title, required, select2Options, className) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.select2Options = (select2Options || null);
		tmp.className = (className || 'col-md-12');
		
		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.select2 = new Element('input')
			.writeAttribute('required', tmp.required)
			.writeAttribute('save-item', saveItem);
		
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.select2).addClassName(tmp.className) );
		
		tmp.me._signRandID(tmp.select2);
		
		tmp.data = [];
		if(tmp.me._item && tmp.me._item.id) {
			if(Array.isArray(value)) {
				value.each(function(item){
					tmp.data.push({'id': item.id, 'text': item.name, 'data': item});
				});
			} else tmp.data = value;
		}
		console.debug(tmp.data);
		
		tmp.selectBox = jQuery('#'+tmp.select2.id).select2(tmp.select2Options ? tmp.select2Options : {
			minimumInputLength: 1,
			multiple: true,
			allowClear: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': searchEntityName, 'pageNo': 1};
				}
				,results: function(data, page, query) {
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
		});
		tmp.selectBox.on('change', function(){
			tmp.selectBox.attr('dirty', tmp.selectBox.val() !== tmp.me._getNamesString(value,'id',','));
			tmp.me._refreshDirty()._getSaveBtn();
		});
		if(tmp.data)
			tmp.selectBox.select2('data', tmp.data);
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
					tmp.me._item = tmp.result.item;
					if(typeof(onSuccFunc) === 'function')
						onSuccFunc(tmp.result);
					tmp.me.closeFancyBox();
				} catch (e) {
					tmp.me.showModalBox('<strong class="text-danger">ERROR:</strong>', e);
				}
			}
			, 'onComplete': function() {
				if(btn)
					jQuery('#' + btn.id).prop('disabled',false);
				tmp.me.refreshParentWindow();
			}
		});
		return tmp.me;
	}

	,_init: function(){
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
	,setPreData: function(data) {
		if(data)
			this._preSetData = data;
		return this;
	}
	,bindAllEventNObjects: function() {
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