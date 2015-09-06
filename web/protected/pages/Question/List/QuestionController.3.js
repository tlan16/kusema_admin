/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {'id': "ID", 'active': 'Active', 'title': 'Title', 'content': 'Content'};
	}
	,loadSelect2: function() {
		var tmp = {};
		tmp.me = this;
		
		jQuery('.select2').select2();
	}
	,localizeDate: function(datestring) {
		return moment.utc(datestring).local().format("D MMM YY, h:mm:ss a");
	}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		if(tmp.isTitle === false) {
			tmp.author = (row.author ? 
							(row.authorName === '' ? 
									row.author.fullName
										: 
									(new Element('span')
											.insert({'bottom': new Element('span', {'class': 'text-warning'}).update(row.authorName+'<br/>') })
											.insert({'bottom': new Element('span').update('(alias of "'+row.author.fullName+'")') })
									)
							)
								: 
							'<span><span class="text-info">'+row.createdBy.person.fullName+'</span><br/>(system user)</span>'
						);
			tmp.time = ('Create At ' + tmp.me.localizeDate(row.created) + 
						(parseInt(row.createdBy.id) === 10 ? ('<span class="text-muted"> by System</span>') : ('<span class="text-info"> by ' + row.createdBy.person.fullName + '</span>') ) + '<br/>' + 
						'Update At ' + tmp.me.localizeDate(row.updated) +
						(parseInt(row.updatedBy.id) === 10 ? ('<span class="text-muted"> by System</span>') : ('<span class="text-info"> by ' + row.updatedBy.person.fullName + '</span>') )
					);
			tmp.topics = new Element('dl').insert({'bottom': new Element('dd').update('<strong>Topic(s)</strong>') });
			row.topics.each(function(item){
				tmp.key = Object.keys(item)[0];
				tmp.topics.insert({'bottom': new Element('dd', {'info_id': tmp.key}).addClassName('topic_row').update(item[tmp.key]['name'])
					.insert({'bottom': new Element('a', {'class': 'pull-right glyphicon glyphicon-remove remove-btn', 'href': 'javascript:void(0)'}) 
						.observe('click', function(e){
							tmp.me._updateItem($(this), $(this).up('[info_id]').readAttribute('info_id'), null, 'removeTopic');
						})
					})
				});
			});
			tmp.topics.insert({'bottom': new Element('dd', {'info_id': 'new'})
				.insert({'bottom': new Element('a', {'class': 'glyphicon glyphicon-plus', 'href': 'javascript:void(0)'}) 
					.observe('click', function(e){
						jQuery('.select2').select2("close");
						$(this).replace(new Element('dd')
							.insert({'bottom': tmp.topicInput = new Element('select').addClassName('select2').setStyle('width: 99%;') })
						);
						tmp.me._signRandID(tmp.topicInput);
						jQuery('#'+tmp.topicInput.id).select2({
							minimumInputLength: 3
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
						}).select2("open").on("change", function(e) {
							if(parseInt($(this).value) !== 0)
								tmp.me._updateItem($(this), $(this).value, null, 'addTopic');
				        });
					})
				})
			});
			tmp.units = new Element('dl').insert({'bottom': new Element('dd').update('<strong>Unit(s)</strong>') });
			row.units.each(function(item){
				tmp.key = Object.keys(item)[0];
				tmp.units.insert({'bottom': new Element('dd', {'info_id': tmp.key}).addClassName('unit_row').update('<u>'+item[tmp.key]['code'] + "</u>:\t" + item[tmp.key]['name'])
					.insert({'bottom': new Element('a', {'class': 'pull-right glyphicon glyphicon-remove remove-btn', 'href': 'javascript:void(0)'}) 
						.observe('click', function(e){
							tmp.me._updateItem($(this), $(this).up('[info_id]').readAttribute('info_id'), null, 'removeUnit');
						})
					})
				});
			});
			tmp.units.insert({'bottom': new Element('dd', {'info_id': 'new'})
				.insert({'bottom': new Element('a', {'class': 'glyphicon glyphicon-plus', 'href': 'javascript:void(0)'}) 
					.observe('click', function(e){
						jQuery('.select2').select2("close");
						$(this).replace(new Element('dd')
							.insert({'bottom': tmp.unitInput = new Element('select').addClassName('select2').setStyle('width: 99%;') })
						);
						tmp.me._signRandID(tmp.unitInput);
						jQuery('#'+tmp.unitInput.id).select2({
							minimumInputLength: 3
							,ajax: {
								delay: 250
								,url: '/ajax/getAll'
								,type: 'POST'
								,data: function (params) {
									return {"searchTxt": 'code like ?', 'searchParams': ['%' + params.term + '%'], 'entityName': 'Unit'};
								}
								,processResults: function(data, page, query) {
									tmp.result = [];
									if(data.resultData && data.resultData.items) {
										data.resultData.items.each(function(item){
											tmp.result.push({'id': item.id, 'text': ('<u>'+item.code+'</u>: '+item.name), 'data': item});
										});
									}
									return { 'results' : tmp.result };
								}
							}
							,cache: true
							,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
						}).select2("open").on("change", function(e) {
							if(parseInt($(this).value) !== 0)
								tmp.me._updateItem($(this), $(this).value, null, 'addUnit');
				        });
					})
				})
			});
		}
		tmp.row = new Element('span', {'class': 'row'}).store('data', row).addClassName(row.active === true ? '' : 'warning')
			.insert({'bottom': new Element(tmp.tag, {'class': 'title col-sm-2'}).update(row.title) })
			.insert({'bottom': new Element((tmp.isTitle === true ? 'strong' : 'span'), {'class': 'content col-sm-4'}).update(row.content) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'author col-sm-1 visible-lg visible-md'}).update(tmp.isTitle === true ? 'Author' : tmp.author) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'created col-sm-2 visible-lg visible-md'})
				.update(tmp.isTitle === true ? 'Time' : tmp.time) 
			})
			.insert({'bottom': new Element(tmp.tag, {'class': 'topics col-sm-2 visible-lg visible-md'})
				.insert({'bottom': tmp.isTitle === true ? 'Topics / Units'
						: ( new Element('div')
								.insert({'bottom': tmp.topics })
								.insert({'bottom': tmp.units })
						)
				})
			})
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-xs-1'}).update(
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
					)
				: 
					(new Element('span', {'class': 'btn-group btn-group-xs hidden-xm hidden-sm'})
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