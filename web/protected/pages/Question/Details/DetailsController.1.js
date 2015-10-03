/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	/**
	 * Getting a form group for forms
	 */
	,_getFormGroup: function (label, input, noFormControl) {
		return new Element('div', {'class': 'form-group form-group-sm form-group-sm-label'})
			.insert({'bottom': new Element('label').update(label) })
			.insert({'bottom': input.addClassName(noFormControl === true ? '' : 'form-control') });
	}
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	,_getTitleDiv: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.container = $(tmp.me._containerIds.title);
		tmp.input = new Element('input')
			.writeAttribute('save-item', 'title')
			.setValue(tmp.me._item.title)
			.observe('change',function(e){
				tmp.input = $(this);
				tmp.value = $F(tmp.input);
				if(tmp.me._item.title !== tmp.value.trim()) {
					console.debug(tmp.value.trim());
				}
			});
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('Title', tmp.input).addClassName('col-md-12') });
		
		return tmp.me;
	}
	,_getAuthorDiv: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.container = $(tmp.me._containerIds.author);
		
		tmp.alias = new Element('input')
			.writeAttribute('save-item', 'alias')
			.setValue(tmp.me._item.authorName)
			.observe('change',function(e){
				tmp.input = $(this);
				tmp.value = $F(tmp.input);
				if(tmp.me._item.authorName !== tmp.value.trim()) {
					console.debug(tmp.value.trim());
				}
			});
		
		tmp.author = new Element('select')
			.writeAttribute('save-item', 'author')
			.setValue(tmp.me._item.authorName)
			.insert({'bottom': new Element('option')
				.writeAttribute({'value': tmp.me._item.author.id, 'id': tmp.me._item.author.id, 'text': tmp.me.getAuthorDisplay() })
				.update(tmp.me.getAuthorDisplay())
			});
			
		tmp.active = new Element('div')
			.addClassName('btn btn-md')
			.addClassName(tmp.me._item.active === true ? 'btn-danger' : 'btn-success')
			.writeAttribute('save-item', 'active')
			.update(tmp.me._item.active === true ? 'active <i class="fa fa-arrow-right"></i> inactive' : 'inactive <i class="fa fa-arrow-right"></i> active')
			.observe('click',function(e){
				tmp.value = !tmp.me._item.active;
				console.debug(tmp.value);
			});
		
		tmp.container
			.insert({'bottom': tmp.me._getFormGroup('Alias', tmp.alias).addClassName('col-md-4') })
			.insert({'bottom': tmp.me._getFormGroup('Author', tmp.author).addClassName('col-md-4') })
			.insert({'bottom': tmp.me._getFormGroup('Active', tmp.active).addClassName('col-md-4') });
		
		tmp.author = tmp.me._elTojQuery(tmp.author);
		tmp.author.select2({
			minimumInputLength: 3
			,ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'POST'
				,data: function (params) {
					return {"searchTxt": 'firstName like :searchTxt or lastName like :searchTxt or email like :searchTxt', 'searchParams': {'searchTxt': '%' + params.term + '%'}, 'entityName': 'Person'};
				}
				,processResults: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': pageJs.getAuthorDisplay(item.firstName, item.lastName, item.email), 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		}).on("change", function(e) {
			if(parseInt($(this).value) !== 0) {
				tmp.select2 = $(this);
				tmp.value = tmp.select2.value;
				console.debug(tmp.value);
			}
        });
		
		return tmp.me;
	}
	,_elTojQuery(el) {
		var tmp = {};
		tmp.me = this;
		tmp.el = (el || null);
		if(tmp.el === null)
			return null;
		tmp.me._signRandID(tmp.el);
		tmp.el = jQuery('#'+tmp.el.id);
		return tmp.el;
	}
	,getAuthorDisplay(firstname, lastname, email) {
		var tmp = {};
		tmp.me = this;
		
		tmp.firstname = (firstname || tmp.me._item.author.firstName);
		tmp.lastname = (lastname || tmp.me._item.author.lastName);
		tmp.email = (email || tmp.me._item.author.email);
		
		tmp.fullname = (tmp.firstname.trim() === '' ? '' : (tmp.firstname.trim() + ' ') ) + tmp.lastname.trim();
		tmp.email = (tmp.email.trim() === '' ? '' : (' (' + tmp.email.trim() + ')') );
		return tmp.fullname + tmp.email; 
	}
	,_getContentDiv() {
		var tmp = {};
		tmp.me = this;

		tmp.container = $(tmp.me._containerIds.content);
		
		tmp.content = new Element('textarea')
			.writeAttribute('save-item', 'content')
			.setValue(tmp.me._item.content);
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('Content', tmp.content, true).addClassName('col-md-12') });
		
		tmp.content = tmp.me._elTojQuery(tmp.content);
		tmp.content.markdown({
			iconlibrary: 'fa'
		});
		
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me._getTitleDiv()
		._getAuthorDiv()
		._getContentDiv()
		;
		
		return tmp.me;
	}
	/**
	 * Public: binding all the js events
	 */
	,bindAllEventNObjects: function() {
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
	,refreshParentWindow: function() {
		var tmp = {};
		tmp.me = this;
		if(!window.opener)
			return;
		tmp.parentWindow = window.opener;
		tmp.row = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' .product_item[product_id=' + tmp.me._item.id + ']');
		if(tmp.row) {
			tmp.row.replace(tmp.parentWindow.pageJs._getResultRow(tmp.me._item));
			if(tmp.row.hasClassName('success'))
				tmp.row.addClassName('success');
		}
		tmp.newPObtn = $(tmp.parentWindow.document.body).down('#' + tmp.me._btnIdNewPO);
		if(tmp.newPObtn) {
			tmp.parentWindow.pageJs.selectProduct(tmp.me._item, tmp.newPObtn);
		}
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