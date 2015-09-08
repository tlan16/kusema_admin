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

	,_drawChart: function(result, type, title) {
		var tmp = {};
		tmp.me = this;
		switch(type) {
		case 'pie':
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
						text: title
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
		case 'stock':
			jQuery('#' + tmp.me.getHTMLID('resultDivId')).highcharts('StockChart', {
	            rangeSelector : {
	                selected : 1
	            },
	            title : {
	                text : title
	            },
	            series : [{
	                name : 'HQCC',
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

	,_getData: function(type, title) {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('resultDivId')).update(tmp.me.getLoadingImg());
		tmp.me.postAjax(tmp.me.getCallbackId('getData'), tmp.me._searchCriterias, {
			'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						throw 'Syste Error: No result came back!';
					tmp.me._drawChart(tmp.result, type, title);
				} catch (e) {
					$(tmp.me.getHTMLID('resultDivId')).update(tmp.me.getAlertBox('ERROR:', e).addClassName('alert-danger'));
				}
			}
		});
		return tmp.me;
	}

	,load: function (searchCriterias, type, title) {
		this._searchCriterias = searchCriterias;
		return this._getData(type,title);
	}
});