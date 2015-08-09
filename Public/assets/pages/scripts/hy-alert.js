/**
 * 系统提醒
 */
var HyAlert = function(){

	/**
	 * 标记已读
	 */
	var setRead = function(){
		$hyall.actionsHandlers.actionRead = function(rows){
			$.post($.U('ajax?q=read'), {pk: rows.join(',')}, function(r){
                $hyall.dtActionAlert(r);
			});
		};
	};
	/**
	 * 详情弹窗后自动标记已读
	 */
	var autoSetRead = function(){
		var $dtContainer = $hyall.getDtContainer();
		if(!$dtContainer.find('.dt-btm-actions [data-value="read"]').size()) return;
		$dtContainer.on('click', '.dt-detail', function(){
			var idx = $hyall.getRowIdx($(this));
	    	var $checks = $('tbody > tr > td:nth-child(1) input[type="checkbox"]', $dtContainer);
	    	if($checks.size()){
		    	if($checks.eq(idx).prop('disabled')) return;
		    	$checks.eq(idx).prop('checked', false);
		    	$checks.eq(idx).prop('disabled', true);
		    	$.uniform.update($checks);
	    	}
		});
	};
	/**
	 * dt上方按钮初始化
	 */
	var initDtTopBtns = function(){
		$('.dt-top-actions').on('click','[data-action="new"]',function(){
			location.href=$.U('HyAlertA2/all');
		});
		$('.dt-top-actions').on('click','[data-action="all"]',function(){
			location.href=$.U('HyAlertA3/all');
		});
		$('.dt-top-actions').on('click','[data-action="back"]',function(){
			history.go(-1);
		});
	};
	
	return{
		init: function(){
			setRead();
			autoSetRead();
			initDtTopBtns();
		}
	};
}();