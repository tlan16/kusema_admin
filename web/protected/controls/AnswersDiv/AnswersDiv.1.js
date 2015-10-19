/**
 * The display Answers div
 */
var AnswersDivJs = new Class.create();
AnswersDivJs.prototype = {
	SAVE_BTN_ID: ''
	,_pageJs: null
	,_titleRow: {'author': {'fullName': 'Author'}, 'createdBy': {'person': {'fullname': 'WHO'}}, 'created': 'WHEN', 'content': 'Content'}

	//constructor
	,initialize : function(_pageJs, _entityName, _entityId, _pageSize, _displayDivId, _noHeading) {
		this._pageJs = _pageJs;
		this._pageNo = 1;
		this._entityName = (_entityName || 'Question');
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
	,_addNewAnserRow: function(container) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		if(!tmp.container)
			return tmp.me;
		if(!tmp.container.id)
			tmp.me._signRandID(tmp.container);
		if(!$(tmp.container.id))
			return tmp.me;
		
		tmp.newDiv = new Element('div').setStyle('margin-bottom: 10px;')
			.insert({'bottom': new Element('i').addClassName('btn btn-md btn-success').update('NEW ANSWER') 
				.observe('click', function(e){
					tmp.me._showEditPanel();
				})
			});
		
		tmp.container.insert({'bottom': tmp.newDiv });
		
		return tmp.me;
	}
	/**
	 * Getting the Answers row
	 */
	,_addAnswerRow: function(container, answer) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		
		if(!tmp.container)
			return tmp.me;
		if(!tmp.container.id)
			tmp.me._signRandID(tmp.container);
		if(!$(tmp.container.id))
			return tmp.me;
		
		tmp.panel = new Element('div', {'class': 'panel panel-default answer_panel', 'answer_id': answer.id})
			.store(answer)
			.insert({'bottom': new Element('div', {'class': 'panel-heading'}).update('Answer:') })
			.insert({'bottom': new Element('div', {'class': 'answer_result_list table-responsive'})
				.insert({'bottom': new Element('table', {'class': 'table table-condensed table-hover'})
					.insert({'bottom': new Element('thead').update(tmp.me._getAnserRow( tmp.me._titleRow )) })
					.insert({'bottom': new Element('tbody') 
						.insert({'bottom': tmp.me._getAnserRow(answer) })
					})
				})
			});
		if(tmp.me._noHeading === true)
			tmp.panel.down('.panel-heading').hide();
		
		tmp.container
			.insert({'bottom': new Element('label').update('Answer') })
			.insert({'bottom': tmp.panel });
		if(answer.id) {
			tmp.container
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': tmp.commentsDiv = new Element('label', {'class': 'col-md-11 col-md-offset-1'})
						.update('Comments')
						.insert({'bottom': new Element('span', {'class': 'text-muted'}).update(' of the Answer') })
					})
					.insert({'bottom': tmp.commentsDiv = new Element('div', {'class': 'col-md-11 col-md-offset-1 answer_comments_container', 'answer_id': answer.id}) })
				});
			tmp.me._pageJs._signRandID(tmp.commentsDiv);
			new CommentsDivJs(tmp.me._pageJs, 'Answer', answer.id)._setDisplayDivId(tmp.commentsDiv.id).render();
		}
		return tmp.me;
	}
	,_getAnserRow: function(answer) {
		var tmp = {};
		tmp.me = this;
		
		tmp.tag = answer.id ? 'td' : 'th';
		tmp.newRow =  new Element('tr', {'class': 'answer_row', 'answer_id': answer.id})
			.store('data', answer)
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-2'}).update(!answer.id ? answer.created : new Element('small').update(tmp.me._pageJs.loadUTCTime(answer.created).toLocaleString() ) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-2'}).update(!answer.id ? answer.author.fullName : new Element('small').update(answer.author.firstName+' '+answer.author.lastName) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'content col-xs-7'}).update(!answer.id ? answer.content : new Element('small').update( answer.content) ) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-1 text-right'})
				.insert({'bottom': new Element(tmp.tag, {'class': 'btn-group'})
					.insert({'bottom': !answer.id ? '' : new Element('i', {'class': 'btn btn-xs btn-primary glyphicon glyphicon-pencil'})
						.observe('click', function(e){
							tmp.answer = $(this).up('.answer_row').retrieve('data');
							tmp.me._showEditPanel(tmp.answer);
						})
					})
					.insert({'bottom': !answer.id ? '' : new Element('i', {'class': 'btn btn-xs btn-danger glyphicon glyphicon-trash'}) 
						.observe('click', function(e){
							tmp.btn = $(this);
							if(confirm('This answer will be deactivated, continue?')) {
								tmp.me._disableAllBtns(tmp.btn);
								tmp.me._updateAnswer(answer.id, "");
							}
						})
					})
				})
			});
		return tmp.newRow;
	}
	,_showEditPanel: function(answer, text) {
		var tmp = {};
		tmp.me = this;
		tmp.answer = (answer || null);
		tmp.text = (text || '');
		
		tmp.textarea = new Element('textarea', {'save-item': 'content'}).setValue(tmp.answer ? tmp.answer.content : tmp.text);
		tmp.title = (tmp.answer ? ('Editing Answer: posted at ' + tmp.me._pageJs.loadUTCTime(tmp.answer.created).toLocaleString() + ', by ' + tmp.answer.author.firstName + ' ' + tmp.answer.author.lastName) : 'Creating New Answer for ' + tmp.me._entityName );
		tmp.me._pageJs.showModalBox(tmp.title, tmp.textarea);
		
		tmp.me._pageJs._signRandID(tmp.textarea);
		jQuery('#'+tmp.textarea.id).markdown({
			iconlibrary: 'fa'
			,savable: true
			,autofocus: true
			,onSave: function(e) {
				tmp.me._pageJs.hideModalBox();
				if(!tmp.answer || (e.getContent() !== tmp.answer.content) )
					tmp.me._updateAnswer(tmp.answer ? tmp.answer.id : 'new', e.getContent());
			}
		});
		
		return tmp.me;
	}
	/**
	 * Ajax: getting the Answers into the answers div
	 */
	,_getAnswers: function (pageNo, resultDivId, btn) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.loadingDiv = tmp.me._pageJs.getLoadingImg();
		tmp.btn = (btn ? $(btn) : undefined);
		tmp.ajax = new Ajax.Request('/ajax/getAnswers', {
			method: 'get'
			,parameters: {'entity': tmp.me._entityName, 'entityId': tmp.me._entityId, 'orderBy': {'created':'desc'}, 'pageNo': null, 'pageSize': tmp.me._pageSize}
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
					tmp.result = tmp.me._pageJs.getResp(transport.responseText, false, true);
					if(!tmp.result || !tmp.result.items)
						return;
					
					tmp.me._addNewAnserRow($(resultDivId));
					tmp.result.items.each(function(item) {
						tmp.me._addAnswerRow($(resultDivId), item);
					});
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
	 * Ajax: update a answer to this order
	 */
	,_updateAnswer: function(answerId, newValue) {
		var tmp = {};
		tmp.me = this;
		tmp.data = {'answerId': answerId, 'value': newValue, 'entityId': tmp.me._entityId, 'entityName': tmp.me._entityName};
		tmp.me._pageJs.postAjax(AnswersDivJs.UPDATE_BTN_ID, tmp.data, {
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
		tmp.me._getAnswers(tmp.me._pageNo, $(tmp.me._displayDivId));
		return tmp.me;
	}
}
