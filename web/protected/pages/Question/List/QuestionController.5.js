/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_getTitleRowData: function() {
		return {'id': "ID", 'active': 'Active', 'title': 'Title', 'content': 'Content'};
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
		
		jQuery('select.select2').each(function(){
			tmp.options = {};
			if($(this).readAttribute('data-minimum-results-for-search') === 'Infinity' || $(this).readAttribute('data-minimum-results-for-search') === 'infinity' || $(this).readAttribute('data-minimum-results-for-search') == -1)
				tmp.options['minimumResultsForSearch'] = 'Infinity';
			jQuery(this).select2(tmp.options);
		});
		
		tmp.selectBox = jQuery('#searchPanel [search_field="quest.topics"]').select2({
			minimumInputLength: 1,
			allowClear: true,
			multiple: true,
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
		
		tmp.selectBox = jQuery('#searchPanel [search_field="quest.units"]').select2({
			minimumInputLength: 1,
			allowClear: true,
			multiple: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'code like ? or name like ?', 'searchParams': ['%' + params + '%', '%' + params + '%'], 'entityName': 'Unit', 'pageNo': 1};
				}
				,results: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': item.code + ': ' + item.name, 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		});
		
		jQuery('.datepicker').datetimepicker({
			format: 'DD/MM/YYYY'
		});
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
			tmp.time = ('Create At ' + tmp.me.loadUTCTime(row.created).toLocaleString() 
						+ '<br/>' + 
						'Update At ' + tmp.me.loadUTCTime(row.updated).toLocaleString()
					);
			tmp.topics = new Element('table')
				.insert({'bottom': new Element('tr')
					.insert({'bottom': new Element('td', {'colspan': 2}).update('<strong>Topic(s)</strong>') })
				});
			row.topics.each(function(item){
				tmp.topics.insert({'bottom': new Element('tr', {'info_id': item.id}).addClassName('topic_row')
					.insert({'bottom': new Element('td').update(item.name) })
					.insert({'bottom': new Element('td')
						.insert({'bottom': new Element('a', {'class': 'pull-right glyphicon glyphicon-remove remove-btn', 'href': 'javascript:void(0)'}) 
							.observe('click', function(e){
								tmp.me._updateItem($(this), $(this).up('[info_id]').readAttribute('info_id'), null, 'removeTopic');
							})
						})
					})
				});
			});
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
									tmp.me._updateItem($(this), $(this).value, null, 'addTopic');
					        });
						})
					})
				})
			});
			tmp.units = new Element('table')
				.insert({'bottom': new Element('tr')
					.insert({'bottom': new Element('td', {'colspan': 2}).update('<strong>Unit(s)</strong>') })
				});
			row.units.each(function(item){
				tmp.units.insert({'bottom': new Element('tr', {'info_id': item.id}).addClassName('unit_row')
					.insert({'bottom': new Element('td').update(item.code + ": " + item.name) })
					.insert({'bottom': new Element('td')
						.insert({'bottom': new Element('a', {'class': 'pull-right glyphicon glyphicon-remove remove-btn', 'href': 'javascript:void(0)'}) 
							.observe('click', function(e){
								tmp.me._updateItem($(this), $(this).up('[info_id]').readAttribute('info_id'), null, 'removeUnit');
							})
						})
					})
				});
			});
			if(!row.units || row.units.length === 0) {
				tmp.units.insert({'bottom': new Element('tr', {'info_id': 'new'})
					.insert({'bottom': new Element('td', {'colspan': 2})
						.insert({'bottom': new Element('a', {'class': 'glyphicon glyphicon-plus', 'href': 'javascript:void(0)'})
							.observe('click', function(e){
								jQuery('.select2').select2("close");
								$(this).replace(new Element('dd')
									.insert({'bottom': tmp.unitInput = new Element('input').addClassName('select2').setStyle('width: 99%;') })
								);
								tmp.me._signRandID(tmp.unitInput);
								tmp.selectBox = jQuery('#'+tmp.unitInput.id).select2({
									minimumInputLength: 1,
									width: "100%",
									ajax: {
										delay: 250
										,url: '/ajax/getAll'
										,type: 'GET'
										,data: function (params) {
											return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Unit', 'pageNo': 1};
										}
										,results: function(data, page, query) {
											tmp.result = [];
											if(data.resultData && data.resultData.items) {
												data.resultData.items.each(function(item){
													console.debug(item);
													tmp.result.push({'id': item.id, 'text': (item.code+': '+item.name), 'data': item});
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
										tmp.me._updateItem($(this), $(this).value, null, 'addUnit');
						        });
							})
						})
					})
				});
			}
		}
		tmp.row = new Element('span', {'class': 'row'})
			.store('data', row)
			.addClassName( (row.active === false && tmp.isTitle === false ) ? 'warning' : '')
			.addClassName('list-group-item')
			.addClassName('item_row')
			.writeAttribute('item_id', row.id)
			.addClassName(tmp.isTitle === true ? 'hidden-xs': '')
			.insert({'bottom': new Element(tmp.tag, {'class': 'title col-xs-12 col-sm-2 col-md-2 col-lg-2'}).update(row.title) })
			.insert({'bottom': new Element((tmp.isTitle === true ? 'strong' : 'span'), {'class': 'content col-xs-12 col-sm-2 col-md-2 col-lg-2'}).update(row.content) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'author col-sm-2 visible-lg visible-md visible-sm'}).update(tmp.isTitle === true ? 'Author' : tmp.author) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2 visible-lg visible-md visible-sm'})
				.insert({'bottom': new Element(tmp.tag, {'class': 'vote col-sm-3'}).update(tmp.isTitle === true ? 'Vote' : row.vote) })
				.insert({'bottom': new Element(tmp.tag, {'class': 'refId col-sm-9'}).update(tmp.isTitle === true ? 'Ref ID' : row.refId) })
			})
			.insert({'bottom': new Element(tmp.tag, {'class': 'created col-sm-1 visible-lg visible-md visible-sm'})
				.update(tmp.isTitle === true ? 'Time' : tmp.time) 
			})
			.insert({'bottom': new Element(tmp.tag, {'class': 'topics col-sm-2 visible-lg visible-md visible-sm'})
				.insert({'bottom': tmp.isTitle === true ? 'Topics / Units'
						: ( new Element('div')
								.insert({'bottom': tmp.units })
								.insert({'bottom': tmp.topics })
						)
				})
			})
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-xs-12 col-sm-1 col-md-1 col-lg-1'}).update(
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
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Edit'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.observe('click', function(){
								tmp.me._openDetailsPage(row);
							})
						})
						.insert({'bottom': new Element('span', {'class': (row.active ? 'btn btn-danger' : 'btn btn-success'), 'title': (row.active ? 'DE-ACTIVATE' : 'RE-ACTIVATE') })
							.insert({'bottom': new Element('span', {'class': (row.active ? 'glyphicon glyphicon-trash' : 'glyphicon glyphicon-repeat') }) })
							.observe('click', function(){
								if(!confirm('Are you sure you want to ' + (row.active ? 'DE-ACTIVATE' : 'RE-ACTIVATE') + ' this item?'))
									return false;
								tmp.me._deleteItem(row, row.active);
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
			'modal'			: true,
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'type'			: 'iframe',
			'href'			: '/question/' + row.id + '.html',
			'helpers'		: {
				'overlay': {
			    	'locked': false
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