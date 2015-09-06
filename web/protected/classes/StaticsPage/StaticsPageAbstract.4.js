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
		tmp.data = [];
		result.each(function(item){
			tmp.data.push({
				name: item.name,
                y: item.y
			});
		});
		tmp.pie = {
				credits: false,
		        chart: {
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
		switch(type) {
		case 'pie':
			jQuery('#' + tmp.me.getHTMLID('resultDivId')).highcharts(tmp.pie);
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