/**
 * The display Comments div
 */
var CommentsDivJs = new Class.create();
CommentsDivJs.prototype = {
	SAVE_BTN_ID: ''
	,_pageJs: null
	,_titleRow: {'author': {'fullName': 'Author'}, 'createdBy': {'person': {'fullname': 'WHO'}}, 'created': 'WHEN', 'content': 'Content'}

	//constructor
	,initialize : function(_pageJs, _entityName, _entityId, _pageSize, _displayDivId, _noHeading) {
		this._pageJs = _pageJs;
		this._pageNo = 1;
		this._entityName = _entityName;
		this._entityId = _entityId;
		this._displayDivId = _displayDivId;
		this._pageSize = (_pageSize || 5);
		this._noHeading = (_noHeading || true);
	}
	/**
	 * setting the display div id
	 */
	,_setDisplayDivId: function (_displayDivId) {
		var tmp = {};
		tmp.me = this;
		tmp.me._displayDivId = _displayDivId;
		return tmp.me;
	}
	/**
	 * Getting the comments row
	 */
	,_getCommentsRow: function(comments) {
		var tmp = {};
		tmp.me = this;
		tmp.tag = comments.id ? 'td' : 'th';
		tmp.newRow =  new Element('tr', {'class': 'comments_row'})
			.store('data', comments)
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-2'}).update(!comments.id ? comments.created : new Element('small').update(tmp.me._pageJs.loadUTCTime(comments.created).toLocaleString() ) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-2'}).update(!comments.id ? comments.author.fullName : new Element('small').update(comments.author.firstName+' '+comments.author.lastName) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'comments col-xs-7'}).update(!comments.id ? comments.content : new Element('small').update( comments.content) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-1 text-right'})
				.insert({'bottom': new Element(tmp.tag, {'class': 'btn-group'})
					.insert({'bottom': !comments.id ? '' : new Element('i', {'class': 'btn btn-xs btn-primary glyphicon glyphicon-pencil'})
						.observe('click', function(e){
							tmp.comments = $(this).up('.comments_row').retrieve('data');
							tmp.me._showEditPanel(tmp.comments);
						})
					})
					.insert({'bottom': !comments.id ? '' : new Element('i', {'class': 'btn btn-xs btn-danger glyphicon glyphicon-trash'}) 
						.observe('click', function(e){
							tmp.btn = $(this);
							if(confirm('This comment will be deactivated, continue?')) {
								tmp.me._disableAllBtns(tmp.btn);
								tmp.me._updateComments(comments.id, "");
							}
						})
					})
				})
			});
		return tmp.newRow;
	}
	,_showEditPanel: function(comments, text, btn) {
		var tmp = {};
		tmp.me = this;
		tmp.comments = (comments || null);
		tmp.text = (text || '');
		tmp.btn = (btn || null);
		
		tmp.textarea = new Element('textarea', {'save-item': 'comments'}).setValue(tmp.comments ? tmp.comments.content : tmp.text);
		tmp.title = (tmp.comments ? ('Editing Comments: posted at ' + tmp.me._pageJs.loadUTCTime(tmp.comments.created).toLocaleString() + ', by ' + tmp.comments.author.firstName + ' ' + tmp.comments.author.lastName) : 'Creating New Comments for ' + tmp.me._entityName );
		tmp.me._pageJs.showModalBox(tmp.title, tmp.textarea);
		
		tmp.me._pageJs._signRandID(tmp.textarea);
		jQuery('#'+tmp.textarea.id).markdown({
			iconlibrary: 'fa'
			,savable: true
			,autofocus: true
			,onSave: function(e) {
				tmp.me._pageJs.hideModalBox();
				if(!tmp.comments || e.getContent() !== comments.content)
					tmp.me._updateComments(tmp.comments ? comments.id : 'new', e.getContent());
			}
			,onChange: function(e) {
				if(tmp.btn && tmp.btn.up('.new_comments_wrapper') && tmp.btn.up('.new_comments_wrapper').down('input[new_comments="comments"]'))
					tmp.btn.up('.new_comments_wrapper').down('input[new_comments="comments"]').setValue(e.getContent());
			}
		});
		
		return tmp.me;
	}
	/**
	 * Ajax: getting the comments into the comments div
	 */
	,_getComments: function (pageNo, resultDivId, btn) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.loadingDiv = tmp.me._pageJs.getLoadingImg();
		tmp.btn = (btn ? $(btn) : undefined);
		tmp.ajax = new Ajax.Request('/ajax/getComments', {
			method: 'get'
			,parameters: {'entity': tmp.me._entityName, 'entityId': tmp.me._entityId, 'orderBy': {'created':'desc'}, 'pageNo': pageNo, 'pageSize': tmp.me._pageSize}
			,onCreate: function() {
				if(tmp.pageNo === 1) {
					$(resultDivId).update(tmp.loadingDiv);
				}
				if(tmp.btn) {
					tmp.me._pageJs._signRandID(tmp.btn);
					jQuery('#' + tmp.btn.id).button('loading');
				}
			}
			,onSuccess: function(transport) {
				try {
					if(tmp.pageNo === 1) {
						$(resultDivId).update('');
					} else {
						//remove the pagination btn
						if($(resultDivId).down('.comments_get_more_btn_div')) {
							$(resultDivId).down('.comments_get_more_btn_div').remove();
						}
					}
					tmp.tbody = $(resultDivId).down('tbody');
					if(!tmp.tbody) {
						$(resultDivId).insert({'bottom': new Element('table', {'class': 'table table-condensed table-hover'})
							.insert({'bottom': new Element('thead').update(tmp.me._getCommentsRow( tmp.me._titleRow ) ) })
							.insert({'bottom': tmp.tbody = new Element('tbody') })
						});
					}
					tmp.result = tmp.me._pageJs.getResp(transport.responseText, false, true);
					if(!tmp.result || !tmp.result.items)
						return;

					//add new data
					tmp.result.items.each(function(item) {
						tmp.tbody.insert({'bottom': tmp.me._getCommentsRow(item) });
					})
					//who new pagination btn
					if(tmp.result.pageStats.pageNumber < tmp.result.pageStats.totalPages) {
						tmp.tbody.insert({'bottom': new Element('tr', {'class': 'comments_get_more_btn_div'})
							.insert({'bottom': new Element('td', {'colspan': 4})
								.insert({'bottom': new Element('span', {'class': 'btn btn-primary btn-xs', 'data-loading-text': 'Getting More ...'})
									.update('Get More Comments')
									.observe('click', function(){
										tmp.me._getComments(pageNo * 1 + 1, resultDivId, this);
										tmp.me._pageNo = pageNo * 1 + 1;
									})
								})
							})
						})
					}
				} catch (e) {
					if(tmp.pageNo === 1) {
						$(resultDivId).insert({'bottom': tmp.me.getAlertBox('ERROR: ', e).addClassName('alert-danger') });
					} else {
						tmp.me._pageJs.showModalBox('<strong class="text-danger">Error</strong>', e);
					}
				}
			}
			,onComplete: function() {
				tmp.loadingDiv.remove();
				if(tmp.btn) {
					jQuery('#' + tmp.btn.id).button('reset');
				}
			}
		});
		return this;
	}
	/**
	 * Ajax: update a comments to this order
	 */
	,_updateComments: function(commentsId, newValue) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs.postAjax(CommentsDivJs.UPDATE_BTN_ID, {'commentsId': commentsId, 'value': newValue, 'entityId': tmp.me._entityId, 'entityName': tmp.me._entityName}, {
			'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me._pageJs.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					tmp.me._pageSize = parseInt(tmp.me._pageNo) * parseInt(tmp.me._pageSize);
				} catch (e) {
					tmp.me._pageJs.showModalBox('<strong class="text-danger">Error</strong>', e);
				}
			}
			,'onComplete': function () {
				tmp.me.render();
			}
		})
		return this;
	}
	/**
	 * Getting a empty comments div
	 */
	,_getEmptyCommentsDiv: function(resultDiv) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'row new_comments_wrapper'})
			.insert({'bottom': new Element('div', {'class': 'col-xs-2 text-right'}).update('<strong>New Comments:</strong>') })
			.insert({'bottom': new Element('div', {'class': 'col-xs-10'})
				.insert({'bottom': new Element('div', {'class': 'input-group'})
					.insert({'bottom': tmp.input = new Element('input', {'class': 'form-control', 'type': 'text', 'new_comments': 'comments', 'placeholder': 'add more comments to this order'})
						.observe('keydown', function(event) {
							tmp.me._pageJs.keydown(event, function() {
								$(event.currentTarget).up('.new_comments_wrapper').down('[new_comments=btn]').click();
							});
						})
					})
					.insert({'bottom': new Element('span', {'class': 'input-group-btn'})
						.insert({'bottom': new Element('button', {'type': 'button', 'new_comments': 'editor_btn', 'class': 'btn btn-sm btn-default', 'data-loading-text': 'saving...'})
							.update('<i class="glyphicon glyphicon-resize-full"></i>')
							.observe('click', function() {
								tmp.me._showEditPanel(null, $F(tmp.input), $(this));
							})
						})
						.insert({'bottom': new Element('button', {'type': 'button', 'new_comments': 'btn', 'class': 'btn btn-sm btn-primary', 'data-loading-text': 'saving...'})
							.update('add')
							.observe('click', function() {
								tmp.btn = $(this);
								tmp.value = $F(tmp.btn.up('.new_comments_wrapper').down('input[new_comments="comments"]'));
								if(tmp.value.trim() !== '') {
									tmp.me._disableAllBtns(tmp.btn);
									tmp.me._updateComments('new', tmp.value);
								}
							})
						})
					})
				})
			});
		return tmp.newDiv;
	}
	,_disableAllBtns(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.btn = (btn || null);
		if(tmp.btn && tmp.btn.up('.panel')) {
			tmp.btn.up('.panel').getElementsBySelector('.btn,input').each(function(btn){
				btn.writeAttribute('disabled',true);
			})
		}
		return tmp.me;
	}
	/**
	 * render the div
	 */
	,render: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'}).update('Comments:') })
			.insert({'bottom': tmp.resultDiv = new Element('div', {'class': 'comments_result_list table-responsive'})})
			.insert({'bottom': new Element('div', {'class': 'panel-footer'}).update(tmp.me._getEmptyCommentsDiv( tmp.resultDiv )) });
		if(tmp.me._noHeading === true)
			tmp.newDiv.down('.panel-heading').hide();
		$(tmp.me._displayDivId).update(tmp.newDiv);
		tmp.me._getComments(tmp.me._pageNo,  tmp.resultDiv);
	}
}
