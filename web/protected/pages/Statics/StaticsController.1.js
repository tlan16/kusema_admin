/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new StaticsPageJs(), {
	load: function (searchCriterias) {
		this._searchCriterias = searchCriterias;
		return this._getData();
	}
});
