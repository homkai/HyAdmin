/**
 * 首页JS
 */
var Index = function(){	
	var handleChats = function(){
		// List容器
		var $list=$('ul.chat-list');
		var lastQueryTime = (new Date()).valueOf();
		// 初始化 slim scroll
		var initScroll = function(){
			$('.chat-scroller').not('.scroller-initialized').addClass('scroller-initialized').slimScroll({
				height:'352px',
				allowPageScroll: true,
	            size: '7px',
	            color: '#bbb',
	            railColor: '#eaeaea',
	            start: 'bottom'
			});
			if($('.chat-scroller').hasClass('scroller-initialized')){
				$('.chat-scroller').slimScroll({
					scrollBy:'-20px'
				}).bind('slimscroll', function(e, pos){
				    switch(pos){
			    		case 'bottom':
				    		$('.chat-scroller').slimScroll({
								scrollBy:'-2px'
							});
				    		refreshList();
				    		break;
				    }
				});
			}
		};
		// 渲染列表
		var renderChats = function(data){
			var tpl='<li class="{:_type}" data-type="{:_type}"><img class="avatar" alt="头像" src="{:avatar_file}" /><div class="message">'+
					'<span class="arrow"></span><a href="javascript:;" class="name">{:user_id_text}</a><span class="datetime">&nbsp;|&nbsp;{:create_time}</span>'+
					'<span class="body"> &nbsp;{:content} </span></div></li>';
			var htList='';
			// 保证原有最后一条与请求数据第一条作者的一致性
			var last={};
			var $last=$list.find('li:last');
			if($last.size()) {
				last.type = $last.data('type')=='in' ? true : false;
				last.user_id_text=$.trim($last.find('.name').text());
			}
			$.each(data.list,function(k,v){
				var type=false;
				if(data.list[k-1]) last=data.list[k-1];
				type=(v['user_id_text']==last['user_id_text'])?last.type:!last.type;
				data.list[k]['type']=type;
				v._type=type?'in':'out';
				htList+=tpl.replace(/\{:(\w+)}/g,function(r1,r2){
					return v[r2] || '';
				});
			});
			$list.data('offset',data.offset).append(htList);
			if($('.chat-scroller').hasClass('scroller-initialized')){
				$('.chat-scroller').slimScroll({
					scrollBy:(data.list.length*70)+'px'
				});
				initScroll();
			}
		};
		// 页面加载时请求数据
		$.post($.U('HyChat/ajax?q=list'), function(r){
			if(!r.status) return;
			renderChats(r.data);
			initScroll();
		});
		// 发布按钮事件处理
		$content=$('[name="input-chat"]',$('.chats'));
		$('.btn-chat').on('click',function(){
			var $the = $(this);
			$the.prop('disabled',true);
			var content=$.trim($content.val());
			var offset =$list.data('offset');
			if(!content) return false;
			lastQueryTime=(new Date()).valueOf();
			$.post($.U('HyChat/ajax?q=add'),{content:content,offset:offset},function(r){
				$content.val('');
				$the.prop('disabled',false);
				if(!r.status) return $.actionAlert(r);
				renderChats(r.data);
			});	
		});
		// 支持回车
		$content.on('keypress',function(e){
			if (e.which == 13) {
				$('.btn-chat').trigger('click');
                return false;
            }
		});
		$('.reload',$('.chats')).on('click',function(){
			refreshList();
		});
		// 下滚刷新列表
		var refreshList = function(){
			if((new Date()).valueOf()-lastQueryTime < 3000) return false;
			$.loading('.chat-scroller-wrapper','刷新列表...');
			var offset =$list.data('offset');
			if(!offset) return false;
			lastQueryTime=(new Date()).valueOf();
			$.post($.U('HyChat/ajax?q=refresh'), {offset:offset}, function(r){
				$.unloading('.chat-scroller-wrapper');
				if(!r.status) return false;
				renderChats(r.data);
			});
		};
	};	
	var initPage = function(){
		$('.time-now').html($.date('Y年m月d日 x'));
		$('.notice').on('click','.notice-file-down',function(e){
			e.preventDefault();
			e.stopPropagation();
			location.href=$(this).data('url');
		});
	};
	return {
		init:function(){
			initPage();
			handleChats();
		}
	};
}();