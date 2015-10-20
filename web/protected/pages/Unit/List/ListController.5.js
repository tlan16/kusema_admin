/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		
		tmp.topics = new Element('table').setStyle('width:100%;')
			.insert({'bottom': new Element('tr')
					.insert({'bottom': new Element('td', {'colspan': 2}).update('<strong>Topic(s)</strong>') })
				});
		if(Array.isArray(row.topics)) {
			row.topics.each(function(item){
				tmp.topics.insert({'bottom': new Element('tr', {'info_id': item.id}).addClassName('topic_row')
					.insert({'bottom': new Element('td').update(item.name) })
					.insert({'bottom': new Element('td')
						.insert({'bottom': new Element('a', {'class': 'pull-right glyphicon glyphicon-remove remove-btn', 'href': 'javascript:void(0)'}) 
							.observe('click', function(e){
								tmp.me._saveItem($(this), $(this).up('[info_id]').readAttribute('info_id'), null, 'removeTopic');
							})
						})
					})
				});
			});
		}
		tmp.topics.insert({'bottom': new Element('tr', {'info_id': 'new'})
			.insert({'bottom': new Element('td', {'colspan': 2})
				.insert({'bottom': new Element('a', {'class': 'glyphicon glyphicon-plus', 'href': 'javascript:void(0)'}) 
					.observe('click', function(e){
						jQuery('.select2').select2("close");
						$(this).replace(new Element('dd')
							.insert({'bottom': tmp.topicInput = new Element('input').addClassName('select2').setStyle('width: 99%;') })
						);
						tmp.me._signRandID(tmp.topicInput);
						tmp.selectBox = jQuery('#'+tmp.topicInput.id).select2({
							minimumInputLength: 1,
							width: "100%",
							ajax: {
								delay: 250
								,url: '/ajax/getAll'
								,type: 'GET'
								,data: function (params) {
									return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Topic', 'pageNo': 1};
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
						tmp.selectBox.select2("open");
						tmp.selectBox.on("change", function(e) {
							if(parseInt($(this).value) !== 0)
								tmp.me._saveItem($(this), $(this).value, null, 'addTopic');
				        });
					})
				})
			})
		});
	
		
		tmp.row = new Element('span', {'class': 'row'})
			.store('data', row)
			.addClassName( (row.active === false && tmp.isTitle === false ) ? 'warning' : '')
			.addClassName('list-group-item')
			.addClassName('item_row')
			.writeAttribute('item_id', row.id)
			.insert({'bottom': new Element(tmp.tag, {'class': 'name col-sm-5 col-xs-12'}).update(row.name) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'refId col-sm-3 col-xs-12'}).update(row.refId) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'topics col-sm-2 col-xs-12'}).update(tmp.isTitle === true ? 'Topics' : tmp.topics ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-sm-2 col-xs-12'}).update(
				tmp.isTitle === true ?  
					(new Element('span', {'class': 'btn btn-primary btn-xs col-xs-12', 'title': 'New'})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': ' NEW' })
						.observe('click', function(){
							tmp.me._openDetailsPage();
						})
					)
				: 
					(new Element('span', {'class': 'btn-group btn-group-xs'})
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Delete'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.observe('click', function(){
								tmp.me._openDetailsPage(row);
							})
						})
						.insert({'bottom': new Element('span')
							.addClassName( (row.active === false && tmp.isTitle === false ) ? 'btn btn-success' : 'btn btn-danger')
							.writeAttribute('title', ((row.active === false && tmp.isTitle === false ) ? 'Re-activate' : 'De-activate') )
							.insert({'bottom': new Element('span') 
								.addClassName( (row.active === false && tmp.isTitle === false ) ? 'glyphicon glyphicon-repeat' : 'glyphicon glyphicon-trash')
							})
							.observe('click', function(){
								if(!confirm('Are you sure you want to ' + (row.active === true ? 'DE-ACTIVATE' : 'RE-ACTIVATE') +' this item?'))
									return false;
								tmp.me._deleteItem(row, row.active);
							})
						}) 
					)
			) })
		;
		return tmp.row;
	}
	,_saveItem: function(btn, entityId, newValue, method) {
		var tmp = {};
		tmp.me = this;
		tmp.itemId = $(btn).up('.item_row[item_id]').readAttribute('item_id');
		tmp.data = {'itemId': tmp.itemId, 'entityId': entityId, 'newValue': newValue, 'method': method};
		if(tmp.data === null)
			return;
	
		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), tmp.data, {
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