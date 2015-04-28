/**
 * 班级信息
 */
var ClassInfo=function(){

	/**
	 * 初始化表格
	 */
	var initChart = function(){
		if(!$.count(hyDetailJSON.grades)) return $.actionAlert({status:false, info:'数据异常！'});
		var aClass=[];
		$.each(hyDetailJSON.classes,function(k,v){
			aClass.push(v.name);
		});
		var aGrade=[];
		$.each(hyDetailJSON.grades,function(k,v){
			var name=v.name+'级';
			aGrade.push({name:name,value:v.value});
		});
		var option = {
			title : {
		        text: '班级人数汇总统计饼图',
		        subtext: '数据更新：'+$.date('Y-m-d'),
		        x: 550
			},
			tooltip : {
				show: true,
				formatter: "{a} <br>{b} : {c}人 ({d}%)"
			},
			legend: {
				orient : 'vertical',
				x : 'left',
				data: aClass
			},
			toolbox: {
				show : true,
				feature : {
					restore : {show: true},
					saveAsImage : {show: true}
				}
			},
			calculable : true,
			series : [
				{
					name:'年级',
					type:'pie',
					center : ['64%', 290],
					radius : 80,
					itemStyle : {
						normal : {
							label : {
								position : 'inner',
								formatter : function (a,b,c,d) {return (d - 0).toFixed(0) + '%'}
							},
							labelLine : {
								show : false
							}
						},
						emphasis : {
							label : {
								show : true,
								formatter : "{b} {d}%"
							}
						}
					},
					data:aGrade
				},
				{
					name:'年级',
					type:'pie',
					center: ['64%', 290],
					radius: [110, 140],
					data: hyDetailJSON.classes
				}
			]
		};
		$('.chart-container').css({width:860,height:500}).echarts(option);
	};
	
	var listenDetailModal = function(){
		$hyall.on('shown.hyall.detail.chart', function(){
			initChart();
		});
	};
	
	return{
		init: function(){
			listenDetailModal();
		}
	}
}();