/**
 * The StaticsPageJs file
 */
var StaticsPageJs = new Class.create();
StaticsPageJs.prototype = Object.extend(new BPCPageJs(), {
	_htmlIds: {'resultDivId': ''}
	,_searchCriterias: {}

	,setHTMLIDs: function(resultDivId) {
		this._htmlIds.resultDivId = resultDivId;
		return this;
	}

	,_drawChart: function(result) {
		var tmp = {};
		tmp.me = this;
		switch(tmp.me._type) {
		case 'pie': {
			tmp.data = [];
			tmp.index = 0;
			tmp.indexOfMax = null;
			tmp.max = 0;
			result.each(function(item){
				tmp.data.push({
					name: item.name,
					y: item.y
				});
				if(parseFloat(item.y) > parseFloat(tmp.max)) {
					tmp.max = parseFloat(item.y);
					tmp.indexOfMax = tmp.index; 
				}
				tmp.index++;
			});
			if(tmp.max !== null) {
				tmp.max = tmp.data[tmp.indexOfMax];
				tmp.max.sliced = true;
				tmp.max.selected = true;
			}
			tmp.pie = {
					credits: false,
					chart: {
						renderTo: tmp.me.getHTMLID('resultDivId'),
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},
					title: {
						text: tmp.me._title
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
								style: {
									color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
								}
							}
						}
					},
					series: [{
						name: "Topics",
						colorByPoint: true,
						data: tmp.data
					}]
			};
			jQuery('#' + tmp.me.getHTMLID('resultDivId')).highcharts(tmp.pie);
			break;
		}
		case 'stock': {
			jQuery('#' + tmp.me.getHTMLID('resultDivId')).highcharts('StockChart', {
	            rangeSelector : {
	                selected : 1
	            },
	            title : {
	                text : tmp.me._title
	            },
	            series : [{
	                name : 'Count',
	                data : result,
	                tooltip: {
	                    valueDecimals: 0
	                }
	            }]
	        });
			break;
		}
		case 'onedaystock':
			jQuery('#' + tmp.me.getHTMLID('resultDivId')).highcharts('StockChart', {
				rangeSelector : {
					buttons: [{
						type: 'minute',
						count: 30,
						text: '30min'
					}, {
						type: 'hour',
						count: 1,
						text: '1h'
					}, {
						type: 'hour',
						count: 3,
						text: '3h'
					}, {
						type: 'hour',
						count: 6,
						text: '6h'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected : 4
				},
				title : {
					text : tmp.me._title
				},
				series : [{
					name : 'Count',
					data : result,
					tooltip: {
						valueDecimals: 0
					}
				}]
			});
			break;
		}
		return tmp.me;
	}

	,_getData: function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('resultDivId')).update(tmp.me.getLoadingImg());
		tmp.data = {
			'searchCriterias':tmp.me._searchCriterias,
			'type':tmp.me._type,
			'entity':tmp.me._entity,
			'title':tmp.me._title,
			'action':tmp.me._action
		};
		tmp.me.postAjax(tmp.me.getCallbackId('getData'), tmp.data, {
			'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						throw 'Syste Error: No result came back!';
					tmp.me._drawChart(tmp.result);
				} catch (e) {
					$(tmp.me.getHTMLID('resultDivId')).update(tmp.me.getAlertBox('ERROR:', e).addClassName('alert-danger'));
				}
			}
		});
		return tmp.me;
	}

	,load: function (searchCriterias, type, entity, title, action) {
		this._searchCriterias = searchCriterias;
		this._type = type;
		this._entity = entity;
		this._title = title;
		this._action = action;
		return this._getData();
	}
});