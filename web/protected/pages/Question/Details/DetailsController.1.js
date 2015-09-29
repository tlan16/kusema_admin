/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_imgPanelId: 'images_panel'             //the html id of the iamges panel
	,_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	/**
	 * Getting a form group for forms
	 */
	,_getFormGroup: function (label, input) {
		return new Element('div', {'class': 'form-group form-group-sm form-group-sm-label'})
			.insert({'bottom': new Element('label').update(label) })
			.insert({'bottom': input.addClassName('form-control') });
	}
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	/**
	 * displaying the item
	 */
	,_getItemDiv: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-sm-4'})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-8'})
				})
			});
		return tmp.newDiv;
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