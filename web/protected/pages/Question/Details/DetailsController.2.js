/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	,getAuthorDisplay(firstname, lastname, email) {
		var tmp = {};
		tmp.me = this;
		
		tmp.firstname = (firstname || tmp.me._item.author.firstName);
		tmp.lastname = (lastname || tmp.me._item.author.lastName);
		tmp.email = (email || tmp.me._item.author.email);
		
		tmp.fullname = (tmp.firstname === '' ? '' : (tmp.firstname + ' ') ) + tmp.lastname;
		tmp.email = ((!tmp.email || tmp.email === '') ? '' : (' (' + tmp.email + ')') );
		return tmp.fullname + tmp.email; 
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
	,_getAnswersDiv: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.newAnswer = new Element('div', {'class': ' col-md-12 text-right'}) 
			.insert({'bottom': new Element('label', {'class': 'btn btn-success btn-sm'})
				.update('<b>New Answer</b>')
				.observe('click', function(e){
					tmp.me._showAnswerEditPanel();
				})
			});
		
		tmp.ajax = new Ajax.Request('/ajax/getAnswers', {
			method: 'post'
			,parameters: {'entityId': tmp.me._item.id, 'entity': 'Question'}
			,onSuccess: function(transport) {
				try {
					tmp.result = tmp.me.getResp(transport.responseText, false, true);
					if(!tmp.result || !tmp.result.items)
						return;
					tmp.container = $(tmp.me._containerIds.newAnswer);
					tmp.container.update(tmp.newAnswer);
					
					tmp.container = $(tmp.me._containerIds.answers);
					tmp.result.items.each(function(item){
						tmp.container.insert({'bottom': tmp.me._getAnswerRow(item) });
					});
					tmp.me._initAnswerCommentsDivs();
				} catch (e) {
					tmp.me.showModalBox('<strong class="text-danger">Error</strong>', e);
				}
			}
			,onComplete: function() {
			}
		});
		return tmp.me;
	}
	,_showAnswerEditPanel: function(answer) {
		var tmp = {};
		tmp.me = this;
		tmp.answer = (answer || null);
		
		tmp.textarea = new Element('textarea', {'save-item': (tmp.answer ? 'content' : 'answer')}).setValue(tmp.answer ? tmp.answer.content : '');
		tmp.title = (tmp.answer ? ('Editing Answer: posted at ' 
						+ tmp.me.loadUTCTime(tmp.answer.created).toLocaleString() 
						+ (tmp.answer.author ? ', by ' : '') 
						+ (tmp.answer.author ? tmp.answer.author.firstName : '') 
						+ ' '
						+ (tmp.answer.autho ? tmp.answer.author.lastName : '') 
					) : 'Creating New Answer for Question' );
		tmp.me.showModalBox(tmp.title, tmp.textarea);
		
		tmp.me._signRandID(tmp.textarea);
		jQuery('#'+tmp.textarea.id).markdown({
			iconlibrary: 'fa'
			,savable: true
			,autofocus: true
			,onSave: function(e) {
				tmp.textarea = e.$textarea[0];
				tmp.value = e.getContent();
				tmp.me.hideModalBox();
				if( (!tmp.answer && tmp.value !== '') || (tmp.answer && tmp.value !== tmp.answer.content) ) {
					tmp.callback = function(result) {
						tmp.result = result;
						if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
							return;
						tmp.container = $(tmp.me._containerIds.answers);
						tmp.newRow = tmp.me._getAnswerRow(tmp.result.item);
						if(tmp.container.down('[answer_id="'+tmp.result.item.id+'"]') && tmp.container.down('[answer_id="'+tmp.result.item.id+'"]').down('.answer') ) {
							tmp.container.down('[answer_id="'+tmp.result.item.id+'"]').down('.answer').replace( tmp.newRow.down('.answer') );
						} else {
							tmp.container.insert({'top': tmp.newRow });
							tmp.me._initAnswerCommentsDivs(tmp.result.item.id);
						}
					};
					tmp.me.saveItem(tmp.textarea, {
						'value': tmp.value
						,'field': tmp.textarea.readAttribute('save-item')
						,'entityName': tmp.answer ? 'Answer' : 'Question'
						,'entityId': tmp.answer ? tmp.answer.id : tmp.me._item.id
					}, tmp.callback);
				}
			},
		});
		
		return tmp.me;
	}
	,_getAnswerRow: function(answer) {
		var tmp = {};
		tmp.me = this;
		
		tmp.answer = new Element('div', {'class': 'answer panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'}).update(tmp.me.loadUTCTime(answer.created).toLocaleString() + ', <b>' + tmp.me.getAuthorDisplay(answer.firstName,answer.lastName) + '</b>') })
			.insert({'bottom': new Element('div', {'class': 'panel-body'})
				.insert({'bottom': new Element('span', {'class': 'col-sm-11'}).update(answer.content) })
				.insert({'bottom': new Element('span', {'class': 'col-sm-1 text-right'})
					.insert({'bottom': new Element('span', {'class': 'btn-group'})
						.insert({'bottom': new Element('i', {'class': 'btn btn-xs btn-primary glyphicon glyphicon-pencil'})
							.observe('click',function(e){
								tmp.btn = $(this);
								tmp.me._showAnswerEditPanel(answer);
							})
						}) 
						.insert({'bottom': new Element('i', {'class': 'btn btn-xs btn-danger glyphicon glyphicon-trash', 'save-item': 'active'}) 
							.observe('click',function(e){
								tmp.btn = $(this);
								if(confirm('This answer and all comments related to this answer will be ' + (answer.active === true ? 'deactivate' : 're-activate') + ', continue?')) {
									tmp.value = (answer.active === true ? false : true);
									tmp.container = $(tmp.me._containerIds.answers);
									tmp.container.down('[answer_id="'+answer.id+'"]').getElementsBySelector('.btn,input').each(function(btn){
										btn.writeAttribute('disabled',true);
									});
									tmp.callback = function(result) {
										tmp.result = result;
										if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
											return;
										if(tmp.container.down('[answer_id="'+tmp.result.item.id+'"]') )
											tmp.container.down('[answer_id="'+tmp.result.item.id+'"]').remove();
									};
									tmp.me.saveItem(tmp.btn, {
										'value': tmp.value
										,'field': tmp.btn.readAttribute('save-item')
										,'entityName': 'Answer'
										,'entityId': answer.id
									}, tmp.callback);
								}
							})
						}) 
					})
				})
			});
		tmp.newDiv = tmp.me._getFormGroup('Answer', tmp.answer, true)
			.store(answer)
			.writeAttribute({'class': 'col-md-12', 'answer_id': answer.id});
		return tmp.newDiv;
	}
	,_initAnswerCommentsDivs: function(answer_id) {
		var tmp = {};
		tmp.me = this;
		tmp.answerId = (answer_id || null);
		
		tmp.answersContainer = $(tmp.me._containerIds.answers);
		tmp.answersContainer.getElementsBySelector('[answer_id' + (tmp.answerId ? ('="' + tmp.answerId + '"') : '') + ']').each(function(answerDiv){
			tmp.answerId = answerDiv.readAttribute('answer_id');
			tmp.comments = new Element('div');
			tmp.container = answerDiv;
			tmp.comments = new Element('div');
			tmp.container.insert({'bottom': tmp.me._getFormGroup('Comments', tmp.comments, true).addClassName('col-md-12 comments') });
			tmp.me._signRandID(tmp.comments);
			new CommentsDivJs(tmp.me, 'Answer', tmp.answerId)._setDisplayDivId(tmp.comments.id).render();
		});
		$(tmp.me.getHTMLID('itemDiv')).show();
		tmp.me.removeLoadingImg();
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).insert({'before': tmp.me.getLoadingImg() });
		$(tmp.me.getHTMLID('itemDiv')).hide();
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		
		tmp.authorSelect2Options = {
				minimumInputLength: 1,
				width: "100%",
				ajax: {
					delay: 250
					,url: '/ajax/getAll'
					,type: 'GET'
					,data: function (params) {
						return {"searchTxt": 'firstName like ? or lastName like ? or email like ?', 'searchParams': ['%' + params + '%', '%' + params + '%', '%' + params + '%'], 'entityName': 'Person', 'pageNo': 1};
					}
					,results: function(data, page, query) {
						tmp.result = [];
						if(data.resultData && data.resultData.items) {
							data.resultData.items.each(function(item){
								tmp.result.push({'id': item.id, 'text': item.fullName, 'data': item});
							});
						}
						return { 'results' : tmp.result };
					}
				}
				,cache: true
				,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
			};
		
		tmp.me
			._getInputDiv('title', (tmp.me._item.title || ''), $(tmp.me._containerIds.title), null ,true)
			._getInputDiv('authorName', (tmp.me._item.authorName || ''), $(tmp.me._containerIds.authorName), 'Alias', false, 'col-md-6')
			._getSelect2Div('Person', 'author', (tmp.me._item.author ? {'id': tmp.me._item.author.id, 'text': tmp.me._item.author.fullName, 'data': tmp.me._item.author} : ''), $(tmp.me._containerIds.author), null, true, tmp.authorSelect2Options, 'col-md-6')
			._getMarkdownDiv('content', (tmp.me._item.content || ''), $(tmp.me._containerIds.content), null, true)
			._getCommentsDiv()
			._getAnswersDiv()
			._getSaveBtn(tmp.me._collectQuestionData)
			;
		return tmp.me;
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
				tmp.data = tmp.me._collectQuestionData();
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
	,_collectQuestionData: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = {};
		
		tmp.data['title'] = $F($(tmp.me._containerIds.title).down('[save-item]'));
		tmp.data['authorName'] = $F($(tmp.me._containerIds.authorName).down('[save-item]'));
		tmp.data['author'] = $(tmp.me._containerIds.author).down('[save-item]').value;
		tmp.data['content'] = $F($(tmp.me._containerIds.content).down('[save-item]'));
		
		return tmp.data;
	}
	,readOnlyMode: function(){
		var tmp = {};
		tmp.me = this;
		tmp.me._readOnlyMode = true;
		$$('.btn.btn-loadFullDesc').first().click();
		jQuery("input").prop("disabled", true);
		jQuery("select").prop("disabled", true);
		jQuery(".btn").remove();
	}
});