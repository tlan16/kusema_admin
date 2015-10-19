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
		
		tmp.container = $(tmp.me._containerIds.answers);
		
		tmp.answers = new Element('div');
		tmp.me._signRandID(tmp.answers);
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('', tmp.answers, true).addClassName('col-md-12') });
		
		new AnswersDivJs(tmp.me, 'Question', tmp.me._item.id)._setDisplayDivId(tmp.answers.id).render();
		
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		
		tmp.authorSelect2Options = {
				minimumInputLength: 1,
				width: "100%",
				ajax: {
					delay: 250
					,url: '/ajax/getAll'
					,type: 'GET'
					,data: function (params) {
						return {"searchTxt": '(firstName like ? or lastName like ? or email like ?) and refId is not NULL and refId != ?', 'searchParams': ['%' + params + '%', '%' + params + '%', '%' + params + '%', ""], 'entityName': 'Person', 'pageNo': 1};
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
			._getSaveBtn(tmp.me._collectQuestionData)
			._getCommentsDiv()
			._getAnswersDiv()
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
			.update('Close')
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