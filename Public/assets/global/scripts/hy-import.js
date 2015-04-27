/**
 * UM富文本编辑器
 */
var UMEditor = function(){
	
	var initEditor=function(toolbar){
		var id = '';
		// 非弹窗
		$('.make-umeditor:not(.umeditor-initialized)').each(function(i){
			if($(this).parents('.modal').size()) return true;
			id=$(this).addClass('umeditor-initialized').attr('id');
			if(!id) id = 'make-umeditor-'+i+'-'+(new Date()).getTime();
			UM.getEditor(id, {
				//编辑器静态资源路径
				UMEDITOR_HOME_URL: Metronic.getGlobalPluginsPath() + 'umeditor/',
				//自定义工具栏 可参考umeditor.config.js
				toolbar: toolbar || [
					'fontfamily fontsize bold italic underline | justifyleft justifycenter justifyright'
				]
			});
		});
		// 弹窗中的UMEditor
		var aEditors = {},mid,isShown={};
		$('body .modal:not(.bootbox)').on('shown.bs.modal',function(){
			mid=$(this).find('.modal-dialog').data('uid');
			if(!mid) {
				mid=Math.floor(Math.random()*1000000);
				$(this).find('.modal-dialog').data('uid', mid);
				isShown[mid] = 0;
			}
			if(isShown[mid]++) return;
			aEditors[mid] = [];
			$('.make-umeditor:not(.umeditor-initialized)').each(function(i){
				id = 'make-umeditor-'+i+'-'+(new Date()).getTime();
				$(this).addClass('umeditor-initialized').attr('id', id);
				aEditors[mid].push(id);
				UM.getEditor(id, {
					//编辑器静态资源路径
					UMEDITOR_HOME_URL : Metronic.getGlobalPluginsPath()+'umeditor/',
					//自定义工具栏 可参考umeditor.config.js
					toolbar: toolbar || [
						'fontfamily fontsize bold italic underline | justifyleft justifycenter justifyright'
					]
				});
			});
		});	
		$('body .modal:not(.bootbox)').on('hide.bs.modal',function(){
			mid = $(this).find('.modal-dialog').data('uid');
			if(!mid) return;
			isShown[mid] = 0;
			if(!aEditors[mid] || !aEditors[mid].length) return;
			$.each(aEditors[mid], function(k, v){
				UM.getEditor(v).destroy();
				$('#'+v).removeClass('umeditor-initialized');
			});
		});
	};
	return{
		init:function(toolbar){
			initEditor(toolbar);
		}
	};
}();