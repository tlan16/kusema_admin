/**
 * The page Js file
 */
var CRUDPageJs = new Class.create();
CRUDPageJs.prototype = Object.extend(new BPCPageJs(), {
	resultDivId: '' //the html id of the result div
	,searchDivId: '' //the html id of the search div
	,totalNoOfItemsId: '' //the html if of the total no of items
	,_pagination: {'pageNo': 1, 'pageSize': 30} //the pagination details
	,_searchCriteria: {} //the searching criteria
	,_nextPageColSpan: 5

	,setHTMLIds: function(resultDivId, searchDivId, totalNoOfItemsId) {
		this.resultDivId = resultDivId;
		this.searchDivId = searchDivId;
		this.totalNoOfItemsId = totalNoOfItemsId;
		return this;
	}

	,getSearchCriteria: function() {
		var tmp = {};
		tmp.me = this;
		if(tmp.me._searchCriteria === null)
			tmp.me._searchCriteria = {};
		tmp.nothingTosearch = true;
		$(tmp.me.searchDivId).getElementsBySelector('[search_field]').each(function(item) {
			tmp.me._searchCriteria[item.readAttribute('search_field')] = $F(item);
			if(($F(item) instanceof Array && $F(item).size() > 0) || (typeof $F(item) === 'string' && !$F(item).blank()))
				tmp.nothingTosearch = false;
		});
		if(tmp.nothingTosearch === true)
			tmp.me._searchCriteria = null;
		return this;
	}

	,getResults: function(reset, pageSize) {
		var tmp = {};
		tmp.me = this;
		tmp.reset = (reset || false);
		tmp.resultDiv = $(tmp.me.resultDivId);

		if(tmp.reset === true)
			tmp.me._pagination.pageNo = 1;
		tmp.me._pagination.pageSize = (pageSize || tmp.me._pagination.pageSize);
		tmp.me.postAjax(tmp.me.getCallbackId('getItems'), {'pagination': tmp.me._pagination, 'searchCriteria': tmp.me._searchCriteria}, {
			'onCreate': function () {
				jQuery('#' + tmp.me.searchDivId + ' #searchBtn').button('loading');
				//reset div
				if(tmp.reset === true) {
					tmp.resultDiv.update('').addClassName('list-group')
						.insert({'bottom': new Element('div', {'class': 'container-fluid', 'id': tmp.me.resultDivId+'-head' }) })
						.insert({'bottom': new Element('div', {'class': 'container-fluid', 'id': tmp.me.resultDivId+'-body' }) })
						.insert({'bottom': tmp.me.getLoadingImg() })
					;
				}
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						return;
					$(tmp.me.totalNoOfItemsId).update(tmp.result.pageStats.totalRows);

					//reset div
					if(tmp.reset === true) {
						tmp.resultDiv.down('#'+tmp.me.resultDivId+'-head')
							.insert({'bottom': tmp.me._getResultRow(tmp.me._getTitleRowData(), true).addClassName('list-group-item').setStyle('font-weight: bold;') });
						if(!tmp.result.items || tmp.result.items.size() === 0) {
							tmp.resultDiv.insert({'bottom': tmp.me.getAlertBox('Nothing found.', '').addClassName('alert-warning') });
						}
					}
					//remove next page button
					tmp.resultDiv.getElementsBySelector('.paginWrapper').each(function(item){
						item.remove();
					});

					//show all items
					tmp.body = $(tmp.resultDiv).down('#'+tmp.me.resultDivId+'-body');
					tmp.result.items.each(function(item) {
						tmp.body.insert({'bottom': tmp.me._getResultRow(item)
							.addClassName('list-group-item')
							.addClassName('item_row')
							.writeAttribute('item_id', item.id) 
						});
					});
					//show the next page button
					if(tmp.result.pageStats.pageNumber < tmp.result.pageStats.totalPages)
						tmp.resultDiv.insert({'bottom': tmp.me._getNextPageBtn().addClassName('paginWrapper') });
				} catch (e) {
					tmp.resultDiv.insert({'bottom': tmp.me.getAlertBox('Error', e).addClassName('alert-danger') });
				}
			}
			,'onComplete': function() {
				jQuery('#' + tmp.me.searchDivId + ' #searchBtn').button('reset');
				tmp.resultDiv.getElementsBySelector('.loading-img').each(function(item){
					item.remove();
				});
				return tmp.me;
			}
		});
	}

	,_getNextPageBtn: function() {
		var tmp = {};
		tmp.me = this;
		return new Element('tfoot')
			.insert({'bottom': new Element('tr')
				.insert({'bottom': new Element('td', {'colspan': tmp.me._nextPageColSpan, 'class': 'text-center'})
					.insert({'bottom': new Element('span', {'class': 'btn btn-primary', 'data-loading-text':"Fetching more results ..."}).update('Show More')
						.observe('click', function() {
							tmp.me._pagination.pageNo = tmp.me._pagination.pageNo*1 + 1;
							jQuery(this).button('loading');
							tmp.me.getResults();
						})
					})
				})
			});
	}

	,_saveItem: function(btn, savePanel, attrName) {
		var tmp = {};
		tmp.me = this;
		tmp.data = tmp.me._collectFormData(savePanel, attrName);
		if(tmp.data === null)
			return;

		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), {'item': tmp.data}, {
			'onCreate': function () {
				if(tmp.data.id) {
					savePanel.addClassName('item_row').writeAttribute('item_id', tmp.data.id);
				}
				savePanel.hide();
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item)
						return;
					tmp.row = $(tmp.me.resultDivId).down('#'+tmp.me.resultDivId+'-body').down('.item_row[item_id=' + tmp.result.item.id + ']');
					tmp.newRow = tmp.me._getResultRow(tmp.result.item).addClassName('item_row').writeAttribute('item_id', tmp.result.item.id);
					if(!tmp.row)
					{
						$(tmp.me.resultDivId).down('tbody').insert({'top': tmp.newRow });
						savePanel.remove();
						$(tmp.me.totalNoOfItemsId).update($(tmp.me.totalNoOfItemsId).innerHTML * 1 + 1);
					}
					else
					{
						tmp.row.replace(tmp.newRow);
					}

				} catch (e) {
					tmp.me.showModalBox('<span class="text-danger">ERROR:</span>', e, true);
					savePanel.show();
				}
			}
		});
		return tmp.me;
	}

	,_deleteItem: function(row, deactivate) {
		var tmp = {};
		tmp.me = this;
		tmp.deactivate = (deactivate || false);
		tmp.row = $(tmp.me.resultDivId).down('#'+tmp.me.resultDivId+'-body').down('.item_row[item_id=' + row.id + ']');
		tmp.me.postAjax(tmp.me.getCallbackId('deleteItems'), {'ids': [row.id], 'deactivate': (deactivate===true)}, {
			'onCreate': function () {
				if(tmp.row) {
					tmp.row.hide();
				}
			}
			,'onSuccess': function(sender, param) {
				try{
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						return;
					tmp.count = $(tmp.me.totalNoOfItemsId).innerHTML * 1 - 1;
					$(tmp.me.totalNoOfItemsId).update(tmp.count <= 0 ? 0 : tmp.count);
					if(tmp.row) {
						if(tmp.result && tmp.result.items && Array.isArray(tmp.result.items) && tmp.result.items.length > 0)
							tmp.row.replace( tmp.me._getResultRow(tmp.result.items[0]) );
						else tmp.row.remove();
					}
				} catch (e) {
					tmp.me.showModalBox('<span class="text-danger">ERROR</span>', e, true);
					if(tmp.row) {
						tmp.row.show();
					}
				}
			}
		});
		return tmp.me;
	}
});