/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();

		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('username', (tmp.me._item.username || ''), $(tmp.me._containerIds.username), null, true)
			._getInputDiv('firstname', (tmp.me._item.person ? tmp.me._item.person.firstName : ''), $(tmp.me._containerIds.firstname), null, true)
			._getInputDiv('lastname', (tmp.me._item.person ? tmp.me._item.person.lastName : ''), $(tmp.me._containerIds.lastname), null, true)
			._getInputDiv('password', '', $(tmp.me._containerIds.password), (tmp.me._item.id ? 'New Password' : null), (tmp.me._item.id ? false : true) )
			._getSaveBtn()
		;
		
		if(tmp.me._item && tmp.me._item.id) {
			tmp.me._disableAll($(tmp.me._containerIds.username));
		}
			
		tmp.me._addNewComboBtn($(tmp.me._containerIds.new_store_roles));

		if(tmp.me._item.roles && Array.isArray(tmp.me._item.roles)) {
			tmp.me._item.roles.each(function(item){
				tmp.me._addComboRow(item, $(tmp.me._containerIds.store_roles));
			});
		}
		
		return tmp.me;
	}
	,_addNewComboBtn: function(container) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.newBtn = new Element('button', {'class': 'newStoreBtn btn btn-primary btn-sm'})
			.update('New Role')
			.observe('click', function(e){
				tmp.newBtn.writeAttribute('disabled', true);
				tmp.container.insert({'bottom': tmp.newDiv = new Element('div')});
				tmp.me._signRandID(tmp.newDiv);
				tmp.me._addComboRow(null, tmp.newDiv);
				tmp.newBtn.writeAttribute('disabled', false);
			})
			;
	
		tmp.container.update(tmp.me._getFormGroup('', tmp.newBtn).addClassName('col-xs-12'));
		return tmp.me;
	}
	,_getComboRowDeleteBtn: function(combo, className) {
		var tmp = {};
		tmp.me = this;
		tmp.combo = (combo || null);
		tmp.active = (combo ? combo.active === true : true);
		tmp.className = (className || '');
	
		tmp.deleteBtn = new Element('button', {'class': (tmp.active === true ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-success') })
			.addClassName(tmp.className)
			.setStyle('margin-bottom: 15px;')
			.insert({'bottom': new Element('i', {'class': (tmp.active === true ? 'glyphicon glyphicon-trash' : 'glyphicon glyphicon-repeat')}) })
			.observe('click', function(e){
				if(confirm('This ' + (tmp.combo ? '' : 'newly added ') + 'nutrition will be REMOVED, continue?')) {
					tmp.panel = tmp.deleteBtn.up('.combo');
					if(tmp.combo && tmp.combo.id) {
						tmp.panel.up().insert({'bottom': new Element('input', {'type': 'hidden', 'save-item': 'ignore_' + tmp.combo.id, 'dirty': true}) });
					}
					tmp.panel.remove();
					tmp.me._refreshDirty()._getSaveBtn();
				}
			})
			;
		return tmp.deleteBtn;
	}
	,_addComboRow: function(combo, container) {
		var tmp = {};
		tmp.me = this;
		tmp.combo = (combo || null);
		tmp.container = (container || null);
		if(!tmp.container || !tmp.container.id)
			return tmp.me;
		tmp.container
			.insert({'bottom': new Element('div', {'class': 'combo col-xs-12', 'combo_id': (tmp.combo ? tmp.combo.id : 'new'), 'active': (tmp.combo ? tmp.combo.active : true) })
				.insert({'bottom': new Element('div', {'class': 'row '})
					.insert({'bottom': tmp.role = new Element('div', {'class': 'role col-md-12 col-sm-12  col-xs-12'}) })
					.insert({'bottom': new Element('div', {'class': 'pull-right text-right col-md-1 col-sm-1 col-xs-12'}).update(tmp.me._getComboRowDeleteBtn(tmp.combo, 'col-xs-12')) })
				})
			});
	
		tmp.roleSelect2Options = {
			multiple: false,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': 'Role', 'pageNo': 1};
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
			};
	
		tmp.me
			._getSelect2Div('Role', 'role', (tmp.combo ? {'id': tmp.combo.id, 'text': tmp.combo.name, 'data': tmp.combo} : null), tmp.role, ' ', true, tmp.roleSelect2Options)
		;
		return tmp.me;
	}
	,collectData: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = tmp.me._collectFormData($(tmp.me.getHTMLID('itemDiv')), 'save-item');
		if(!tmp.data)
			return null;
		tmp.data['store_roles'] = [];
		$(tmp.me.getHTMLID('itemDiv')).getElementsBySelector('.combo[combo_id]').each(function(item){
			tmp.combo = tmp.me._collectFormData($(item), 'save-item');
			if(tmp.combo)
				tmp.data['store_roles'].push(tmp.combo);
		});

		return tmp.data;
	}
});