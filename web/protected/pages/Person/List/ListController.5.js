/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CRUDPageJs(), {
	_titleRowData: {'id': "ID", 'active': 'Active', 'refId': 'Ref ID', 'firstName': 'First Name', 'lastName': 'Last Name', 'email': 'Email'}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'strong' : 'span');
		tmp.row = new Element('span', {'class': 'row'})
			.store('data', row)
			.addClassName( (row.active === false && tmp.isTitle === false ) ? 'warning' : '')
			.addClassName('list-group-item')
			.addClassName('item_row')
			.writeAttribute('item_id', row.id)
			.insert({'bottom': new Element(tmp.tag, {'class': 'firstName col-md-3'}).update(row.firstName) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'lastName col-md-3'}).update(row.lastName) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'refId col-md-2'}).update(row.refId) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'email col-md-2'}).update(row.email) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'text-right btns col-md-2'}).update(
				tmp.isTitle === true ?  
					(new Element('span', {'class': 'btn btn-success btn-xs', 'title': 'New'})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': ' NEW' })
						.observe('click', function(){
						})
						.hide() //TODO: remove this line if need sync new people
					)
				: 
					(new Element('span', {'class': 'btn-group btn-group-xs'})
						.insert({'bottom': tmp.editBtn = new Element('span', {'class': 'btn btn-primary', 'title': 'Delete'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.observe('click', function(){
								tmp.me._openDetailsPage(row);
							})
						})
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
});