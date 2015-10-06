/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {'id': "ID", 'active': 'Active', 'name': 'Name', 'refId': 'Ref ID'};
	}
	,_bindSearchKey: function() {
		var tmp = {}
		tmp.me = this;
		$('searchPanel').getElementsBySelector('[search_field]').each(function(item) {
			item.observe('keydown', function(event) {
				tmp.me.keydown(event, function() {
					$('searchBtn').click();
				});
			})
		});
		return this;
	}
	,loadSelect2: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('.select2').select2();
	}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		console.debug(row.active === true);
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		tmp.row = new Element('span', {'class': 'row'}).addClassName(row.active === true ? '' : 'warning')
			.store('data', row)
			.insert({'bottom': new Element(tmp.tag, {'class': 'name col-md-6'}).update(row.name) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'refId col-md-4'}).update(row.refId) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-md-2'}).update(
				tmp.isTitle === true ?  
					(new Element('span', {'class': 'btn btn-primary btn-xs', 'title': 'New'})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': ' NEW' })
						.observe('click', function(){
							if($$('.save-item-panel').length === 0) {
								$(this).up('thead').insert({'bottom': tmp.me._getEditPanel({}) });
								tmp.me.loadSelect2();
							}
						})
						.hide() //TODO
					)
				: 
					(new Element('span', {'class': 'btn-group btn-group-xs'})
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Delete'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.observe('click', function(){
								tmp.me._openDetailsPage(row);
							})
						})
						.insert({'bottom': new Element('span', {'class': 'btn btn-danger', 'title': 'Delete'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-trash'}) })
							.observe('click', function(){
								if(!confirm('Are you sure you want to delete this item?'))
									return false;
								tmp.me._deleteItem(row, true);
							})
						}) 
					)
			) })
		;
		return tmp.row;
	}
	,_openDetailsPage: function(row) {
		var tmp = {};
		tmp.me = this;
		jQuery.fancybox({
			'width'			: '95%',
			'height'		: '95%',
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'type'			: 'iframe',
			'href'			: '/topic/' + row.id + '.html',
			'helpers'		: {
				'overlay': {
			    	'locked': false
				}
			},
			'beforeClose'	    : function() {
				if(row && row.id && $(tmp.me.resultDivId).down('.item_row[item_id=' + row.id + ']') && $$('iframe.fancybox-iframe').first().contentWindow.pageJs._item) {
					console.debug($(tmp.me.resultDivId).down('.item_row[item_id=' + row.id + ']'));
					$(tmp.me.resultDivId).down('.item_row[item_id=' + row.id + ']').replace(tmp.me._getResultRow($$('iframe.fancybox-iframe').first().contentWindow.pageJs._item));
				}
			}
 		});
		return tmp.me;
	}
	,_updateItem: function(btn, entityId, newValue, method) {
		var tmp = {};
		tmp.me = this;
		tmp.itemId = $(btn).up('.item_row[item_id]').readAttribute('item_id');
		tmp.data = {'itemId': tmp.itemId, 'entityId': entityId, 'newValue': newValue, 'method': method};
		if(tmp.data === null)
			return;

		tmp.me.postAjax(tmp.me.getCallbackId('updateItem'), tmp.data, {
			'onLoading': function () {
				$(btn).hide();
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item)
						return;
					tmp.row = $(tmp.me.resultDivId).down('#'+tmp.me.resultDivId+'-body').down('.item_row[item_id=' + tmp.result.item.id + ']');
					tmp.newRow = tmp.me._getResultRow(tmp.result.item).addClassName('list-group-item').addClassName('item_row').writeAttribute('item_id', tmp.result.item.id);
					tmp.row.replace(tmp.newRow);
				} catch (e) {
					tmp.me.showModalBox('<span class="text-danger">ERROR:</span>', e, true);
					$(btn).show();
				}
			}
		});
		return tmp.me;
	}
});