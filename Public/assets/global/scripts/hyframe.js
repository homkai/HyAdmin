/**
 * HyFrame 框架基础
 */
var HyFrame = function(){
	
	var the;
	
	/**
	 * 扩展常用的jQuery Tools
	 */
	$.extend({
		/**
		 * URL参数解析，默认解析当前URL
		 * 类PHP parse_url：$.url('parse', [url])
		 * 获取GET参数的值： $.url('?[param]', [url])
		 * 获取锚点的值： $.url('#[param]', [url])
		 */
		url: function(arg, url){
			url = url || location.href;
			if(!arg) return url;
			if('?' === arg.charAt(0) || '#' === arg.charAt(0)){
				var params = {},
				    key = arg.split(arg.charAt(0))[1],
				    reg = '?'===arg.charAt(0) ? /.+\?([^#]+).*/ : /.+#(.+)/;
				    query = url.replace(reg, "$1");
				if(query){
					var parts = query.split("&"), kv;
				    for(var i = 0, ii = parts.length; i < ii; i++){
				        kv = parts[i].split("=");
				        params[kv[0]] = kv[1];
				    }
				}
				return key ? params[key] || '' : params;
			}
			var parse = arguments[1] ? url.match(/([^\/]*\/\/:)?([^(\/\?#)]*)(\/?[^(\?#)]*)\??([^#]*)#?(.*)/) : window.location;
			switch(arg){
				case 'parse':
					if(!arguments[1]) {
						return {
							host: parse.hostname,
							port: parse.port,
							path: parse.pathname,
							query: parse.search.replace(/^\?/, ''),
							fragment: parse.hash.replace(/^#/, '')
						}
					}else{
						var host = parse[1] && parse[2].split(':') || {};
						return {
							host: host[0],
							port: host[1],
							path: parse[1] ? parse[3] : parse[2]+parse[3],
							query: parse[4],
							fragment: parse[5]				
						}
					}
					break;
				case 'domain':
					var hostname = parse.hostname || parse[1].split(':')[0];
					// 判断是否是IP：
					if (/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/.test(hostname)) return hostname;
					var arr = hostname.split('.');
					if(arr.length > 2) arr.shift();
					return arr.join('.');
			}
		},
		/**
		 * 类ThinkPHP U方法
		 */
		U: function(parse, data){
			var parse = $.url('parse', parse);
			var getBaseURL = function(type){
				switch(type){
					case 'I':
						return _INDEX_;
					case 'M':
						return _MODULE_;
					case 'C':
						return _CONTROLLER_;
					case 'A':
						return _ACTION_;
				}
			};
			var url = '';
			if(!parse.path) {
				url = getBaseURL('A');
			}else{
				var path = parse.path.split('/');
				switch(path.length){
					case 3:
						url = getBaseURL('I');
						break;
					case 2:
						url = getBaseURL('M');
						break;
					case 1:
						url = getBaseURL('C');
						break;
				}
				url += '/'+parse.path;
			}
			var addQuery = function(url, query){
				if(!query) return url;
				if(url.indexOf('?') > -1) return url+'&'+query;
				return url+'?'+query;
			};
			if(!data) return addQuery(url, parse.query) + (parse.fragment ? '#'+parse.fragment : '');
			var params = [];
			for(var i in data) params.push(i + '=' + (data[i] || ''));
			return addQuery(addQuery(url, parse.query), params.join('&')) + (parse.fragment ? '#'+parse.fragment : '');
		},
		/**
		 * $.log 记录日志的别名  console.log()
		 */
		log: function(){
			if(!window.console) return;
			if(arguments.length > 1) arguments[0] += ' => ';
			return console.log.apply(console, arguments);
		},
		/**
		 * $.date 类PHP date方法 $.data('Y-m-d H:i:s x', [timestamp])
		 * x为中文星期
		 * timestamp 为毫秒级型的
		 */
		date: function(format, timestamp){
			var date = new Date(timestamp), weekday;
			return format.replace(/[YmdHisx]/g, function(m0){
				switch(m0){
					case 'Y':
						return date.getFullYear();
					case 'm':
						return date.getMonth()+1;
					case 'd':
						return date.getDate();
					case 'H':
						return date.getHours();
					case 'i':
						return date.getMinutes();
					case 's':
						return date.getSeconds();
					case 'x':
						switch(date.getDay()){
							case 0:
								return '星期日';
							case 1:
								return '星期一';
							case 2:
								return '星期二';
							case 3:
								return '星期三';
							case 4:
								return '星期四';
							case 5:
								return '星期五';
							case 6:
								return '星期六';
						}
				}
			});
		},
		/**
		 * $.count 类 PHP count
		 * 返回字符串或者数组的长度
		 * 返回对象的元素个数
		 */
		count: function(obj){
			if('string' === typeof obj || $.isArray(obj)) return obj.length;
			if($.isPlainObject(obj)){
				var count = 0;
				for(var i in obj) count++;
				return count;
			}
		},
		/**
		 * $.ucfirst 类 PHP ucfirst
		 * 大写首字母
		 */
		ucfirst: function(str){
			return str.replace(/(^|\s+)\w/g,function(s){
	            return s.toUpperCase();
	        })
		},
		/**
		 * 语音播放功能（Chrome 33+）
		 * 生成全局的$.utterance
		 */
		speak: function(text, postCallback, prevCallback){
		    if(!('speechSynthesis' in window)) return false;
		    if(!text) return true;
	        if(prevCallback) prevCallback();
		    if($.utterance) window.speechSynthesis.cancel();
		    $.utterance = new SpeechSynthesisUtterance();
		    $.utterance.lang = 'zh-CN';
		    $.utterance.finished = false;
		    $.utterance.onend = function(event) {
			    $.utterance.finished=true;
    	        if(postCallback) postCallback();
    	    };
	    	$.utterance.text = text;
	    	window.speechSynthesis.speak($.utterance);
		},
		/**
		 * 正在载入BlockUI
		 */
		loading: function(target, message){
			Metronic.blockUI({
				message: message || '正在载入...',
				target:target,
                cenrerY: true,
                boxed: true
            });
		},
		/**
		 * 取消载入BlockUI
		 */
		unloading: function(target){
			Metronic.unblockUI(target);
		},
		/**
		 * HyFrame 确认消息弹窗（非阻塞）
		 */
		confirm: function(message, callback_confirm, callback_cancle){
			bootbox.dialog({
                message: message,
                title: '<h4 class="caption"><i class="fa fa-warning font-green-sharp fa-fw"></i><span class="caption-subject font-green-sharp bold">确认操作</span></h4>',
                buttons: {
                 confirm: {
                    label: "继续",
                    className: "blue",
                    callback: function() {
                    	if($.isFunction(callback_confirm))
                    		callback_confirm();
                    }
                  },
                  cancel: {
                    label: "取消",
                    className: "default",
                    callback: function() {
                    	if($.isFunction(callback_cancle))
                    		callback_cancle();
                    }
                  }
                }
            });
		},
	    /**
	     * 操作结果反馈提示
	     */
	    actionAlert: function(r, noHide, callback){
			Metronic.alert({
				type: r.status ? 'success' : 'warning',
				icon:  r.status ? 'check' : 'warning',
	            message: r.info,
	            container: '#hy-alert-wrapper',
	            place: 'append',//'prepend'
	            closeInSeconds: noHide ? 0 : 5
	        });
			if(r.reload) return location.reload();
        	if($.isFunction(callback)) callback(r);
			return false;
	    },
		/**
		 * localStorage|globalStorage支持（第三方插件支持）
		 */
		store: store,
		/**
		 * 屏幕宽度是否是移动设备
		 */ 
		isPhone: $(window).width() < 480
	});
	/**
	 * 将ECharts注册成为jQuery DOM插件
	 * 传入一个PlainObject则执行init方法 option
	 * 第一个参数传字符串，则执行对应的其他方法
	 * 不传参数，则返回该echarts实例
	 */	
	if('undefined' !== typeof echarts){
		$.fn.echarts = function(e){
			if($.isPlainObject(arguments[0])){
				var _chart = echarts.init(this[0]);
				var _echarts = false;
				$(this).data('_echarts', (_echarts = _chart.setOption(arguments[0])));
				return _echarts;
			}else{
				var _echarts = $(this).data('_echarts');
				if('string' === typeof arguments[0]){
					var args = [];
					for(var i in arguments) args.push(arguments[i]);
					return _echarts && _echarts[arguments[0]].apply(_echarts, args.shift);
				}else if(0 === arguments.length){
					return _echarts;
				}
			}
		};
	};
	// 插件语言设置
	if('undefined' !== typeof bootbox){
		bootbox.setDefaults('locale','zh_CN');
	}
	// 插件语言设置
    if($.fn.datetimepicker) {
    	$.fn.datetimepicker.dates['zh-CN'] = {
    			days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
    			daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
    			daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
    			months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
    			monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
    			today: "今日",
    			suffix: [],
    			meridiem: ["上午", "下午"]
    	};
    }
    if($.fn.selectpicker){
    	$.fn.selectpicker.defaults = {
			noneSelectedText: '请选择...',
			noneResultsText: '没有找到匹配项',
			countSelectedText: '选中{1}中的{0}项',
			maxOptionsText: ['超出限制 (最多选择{n}项)', '组选择超出限制(最多选择{n}组)'],
			multipleSeparator: ', '
    	};
    }
	// 插件语言设置
    if($.fn.select2){
        $.fn.select2.locales['zh-CN'] = {
                formatNoMatches: function () { return "没有找到匹配项"; },
                formatInputTooShort: function (input, min) { var n = min - input.length; return "请再输入" + n + "个字符";},
                formatInputTooLong: function (input, max) { var n = input.length - max; return "请删掉" + n + "个字符";},
                formatSelectionTooBig: function (limit) { return "你只能选择最多" + limit + "项"; },
                formatLoadMore: function (pageNumber) { return "加载结果中…"; },
                formatSearching: function () { return "搜索中…"; }
        };
        $.extend($.fn.select2.defaults, $.fn.select2.locales['zh-CN']);
    }
	// 插件语言设置
    if($().validate){
    	$.extend($.validator.messages, {
    		required: "必须填写",
    		remote: "请修正此栏位",
    		email: "请输入有效的电子邮件",
    		url: "请输入有效的网址",
    		date: "请输入有效的日期",
    		dateISO: "请输入有效的日期 (YYYY-MM-DD)",
    		number: "请输入正确的数字",
    		digits: "只可输入数字",
    		creditcard: "请输入有效的信用卡号码",
    		equalTo: "你的输入不相同",
    		extension: "请输入有效的后缀",
    		maxlength: $.validator.format("最多 {0} 个字"),
    		minlength: $.validator.format("最少 {0} 个字"),
    		rangelength: $.validator.format("请输入长度为 {0} 至 {1} 之間的字串"),
    		range: $.validator.format("请输入 {0} 至 {1} 之间的数值"),
    		max: $.validator.format("请输入不大于 {0} 的数值"),
    		min: $.validator.format("请输入不小于 {0} 的数值")
    	});
    	// 扩展验证规则
		$.validator.addMethod("regex", function(text, element, params) {
			var reg = new RegExp(params);
			return this.optional(element) ||  reg.test(text); 
		}, "格式不正确！");
	    $.validator.addMethod("phone", function(value, element) {
	    	return this.optional(element) || /^1[3578]d{9}$/.test(value);
	    }, "请输入正确的手机号！");
    }
	// 工具条刷新
	$('.hy-refresh').click(function(e){
		e.preventDefault();
		location.reload();
	});
	// 返回上一步
	$('.hy-history').click(function(e){
		e.preventDefault();
		history.go(-1);
	});
	
	// 保持在线
	var keepOnline = function(){
		var ivKeepOnline, text,
			key = $.store.get('_homyit_token_seed_'),
			counter = 1,
			url = $.U('System/HyStart/online'),
		keepOnline = function(){
			text = '#Homyit'+(counter++)+'#Keep#Try#'+Math.floor(Math.random()*10000000);
			$.get(url, {keepTry: crypto_aes(text, key)},function(r){
				if(!r.status) clearInterval(ivKeepOnline);
			});
		};
		ivKeepOnline = setInterval(keepOnline, 1000*60);
	};

	// 增强防护
	var protectSession = function(){
		var key = $.store.get('_homyit_token_seed_');
		if(!key){
			return bootbox.alert('<h3 class="text-danger">\u4e3a\u4e86\u6700\u5927\u9650\u5ea6\u4fdd\u8bc1\u60a8\u7684\u4fe1\u606f\u5b89\u5168\uff0c\u7f51\u7ad9\u8bbf\u95ee\u671f\u95f4\uff0c\u8bf7\u52ff\u6e05\u7a7a\u7f13\u5b58\u6216\u5207\u6362\u6d4f\u89c8\u5668\u6a21\u5f0f\u3002\u73b0\u5728\u6211\u4eec\u9700\u8981\u518d\u6b21\u5bf9\u60a8\u8fdb\u884c\u8eab\u4efd\u8ba4\u8bc1\uff0c\u8bf7\u91cd\u65b0\u767b\u5f55\u540e\u7ee7\u7eed\uff01</h3>',function(){
				location.href = $.U('System/HyStart/login');
			});
		}
		var counter = $.store.get('_homyit_token_counter_') + 1;
		$.store.set('_homyit_token_counter_', counter);
		var text = '#Homyit'+counter+'#Base#Auth#'+Math.floor(Math.random()*10000000);
		var encrypted = crypto_aes(text, key);
		$.cookieStorage.set('_homyit_token_', encrypted);
	};

    // 系统检索入口初始化
    var initFrameSeach = function(){
        $('.hy-search-form').attr('action',$.U('System/User/search'));
    	var $input = $('.hy-search-form .search-typeahead');
		var engine = new Bloodhound({
		  name: 'animals',
		  local: [],
		  remote: $.U('System/User/ajax?q=serachTypeahead&filter=%QUERY'),
		  datumTokenizer: function(d) {
		    return Bloodhound.tokenizers.whitespace(d.val);
		  },
		  queryTokenizer: Bloodhound.tokenizers.whitespace
		});
		engine.initialize();        
		$input.typeahead({
			minLength: 1,
			highlight: true,
        },{
			name: 'query',
			displayKey: 'name',
			source: engine.ttAdapter(),
			templates: {
				suggestion: Handlebars.compile([
						'<div class="media">',
							'<div class="media-body">',
								'<h4 class="media-heading">{{name}} <small class="pull-right">{{no}}</small></h4>',
								'<p  class="text-right" style="width:200px;">{{className}}</p>',
							'</div>',
						'</div>',].join('')),
				empty:'<h4>找不到该学生</h4>'
			}
      	});
	};
	
	var initFrameTop = function(){
		//首部点击邮件跳转
		$('.top-menu').on('click','.lq-my-mail',function(){
			var pk=$(this).attr('data-id');
			lq.readThis(pk);
			$.store.set('myMail',pk);
			window.location.href = $.U('System/Inbox/all');		
		});
	};
	
	return the = {
		init: function(){
			keepOnline();
//			initFrameSeach();
//			initFrameTop();
		},
		advanced: function(){
			protectSession();
		},
		/**
		 * 初始化导航菜单
		 */ 
		initMenu: function(data){
			if(data && !$.isEmptyObject(data)){
				$('#hy-sidebar-menu').empty();
				var html='';
				$.each(data || {},function(i,node){
					html+='<li'+(node.first?' class="start"':'')+'>';
					html+=(node.name?'<a href="'+node.name+'" class="hy-menu-trace" ':'<a  href="javascript:;" ')+' data-menu-first="'+i+'">';
					html+='<i class="'+(node.options?node.options:'')+'"></i><span class="title">'+node.title+'</span>';
					if(!node.children){
						html+='</a>';
					}else{
						html+='<span class="is-selected"></span><span class="arrow"></span></a><ul class="sub-menu">';
						$.each(node.children,function(j,child){
							html+='<li>'+(child.title?'<a href="'+child.name+'" class="hy-menu-trace" data-menu-pid="'+i+'" data-menu-second="'+j+'">':'<a  href="javascript:;">')+'<i class="'+(child.options?child.options:'')+'"></i>&nbsp;<span>'+child.title+'</span></a></li>';
						});
						html+='</ul>';
					}
					html+='</li>';
				});
				$.store.set('HomyitStuSys_menuDom',html)
				$('#hy-sidebar-menu').html(html);
				$('#hy-sidebar-menu > li.start').addClass('active');
			}else{
				var dom = $.store.get('HomyitStuSys_menuDom');
				if(!dom) return location.href = $('System/Index/index');
				var first=($.store.get('HomyitStuSys_menuFirst') || {}).index;
				var second=($.store.get('HomyitStuSys_menuSecond') || {}).index;
				$('#hy-sidebar-menu').html(dom);
				var $first=$('#hy-sidebar-menu > li > [data-menu-first="'+first+'"]');
				$first.find('.is-selected').addClass('selected');
				$first.find('.arrow').addClass('open').closest('li').addClass('active open');
				$('#hy-sidebar-menu > li > .sub-menu [data-menu-second="'+second+'"]').closest('li').addClass('active');
			}
			$('.hy-menu-trace').on('click',function(){
				if($(this).data('menu-second')) {
					$.store.set('HomyitStuSys_menuSecond',{index:$(this).data('menu-second'),text:$(this).find('span').html()});
					var first=$("[data-menu-first='"+$(this).data('menu-pid')+"']");
					$.store.set('HomyitStuSys_menuFirst',{index:first.data('menu-first'),text:first.find('span.title').html()});
				}else if($(this).data('menu-first')) {
					$.store.set('HomyitStuSys_menuFirst',{index:$(this).data('menu-first'),text:$(this).find('span.title').html()});
					$.store.remove('HomyitStuSys_menuSecond');
				}
				return true;
			});
		},
		/**
		 * 初始化面包屑
		 */ 
		initBreadcrumb: function(data){
			if(!data){
				data = [];
				var first = $.store.get('HomyitStuSys_menuFirst');
				if(first) data.push({text:first.text});
				var second = $.store.get('HomyitStuSys_menuSecond');
				if(second) data.push({text:second.text});
			}
			$('#hy-breadcrumb').empty();
			var html='<li><span class="hidden-xs">&nbsp;</span><i class="fa fa-home"></i><span class="hidden-xs">&nbsp;</span><a href="'+$.U('System/Index/index')+'" class="hy-menu-trace" data-menu-first="0">\u9996\u9875</a><span class="hidden-xs">&nbsp;</span><i class="fa fa-angle-right"></i>';
			$.each(data,function(i, node){
				html+='<li><span class="hidden-xs">&nbsp;</span>'+(node.icon ? '<i class="'+node.icon+'"></i><span class="hidden-xs">&nbsp;</span>' : '');
				html+='<a href="'+(node.url ? node.url : 'javascript:;')+'">'+node.text+'</a><span class="hidden-xs">&nbsp;</span>';
				html+=(i+1==data.length) ? '' : '<i class="fa fa-angle-right"></i>';
			});
			$('#hy-breadcrumb').html(html);
		},
	    /**
	     * 文件上传处理
	     */
		uploadHandler: function(){
	    	if(!$('.hy-upload').size()) return false;
	    	$('.hy-upload').each(function(){
	    		var $the = $(this);
	    		$the.fileapi({
	    		   url: $.U('System/HyFile/upload'),
	    		   multiple: false,
	    		   maxFiles:1,
	    		   elements: {
	    		      ctrl: { upload: '.js-upload' },
	    		      empty: { show: '.b-upload__hint' },
	    		      emptyQueue: { hide: '.js-upload' },
	    		      list: '.js-files',
	    		      file: {
	    		         tpl: '.js-file-tpl',
	    		         preview: {
	    		            el: '.b-thumb__preview',
	    		            width: 80,
	    		            height: 80
	    		         },
	    		         upload: { show: '.progress', hide: '.b-thumb__rotate' },
	    		         complete: { hide: '.progress' },
	    		         progress: '.progress .bar'
	    		      }
	    		   },
	    		   onSelect:function(evt,data){
	    			   $('.js-file-tpl',$the).eq(0).remove();
	    		   },
	    		   onComplete:function(e,rst){
	    			   var field=rst.widget.$el.data('field');
	    			   var rst=rst.result;
	    			   if(!rst.status) $.actionAlert(rst);
	    			   $('[name="'+field+'"]').val(rst.data.key);
	    			   $('.js-browse span:first',$the).html('重新上传');
	    			   $('.js-browse',$the).removeClass('blue').addClass('btn-success');
	    		   }
	    		});
	    		$(this).show();
	    	});
	    },
		/**
		 * 递归实现模板替换
		 */ 
	    tplRplRecursive: function(t){
			return t._base.replace(/\{:(\w+)}/g,function(r0,r1){
	    		if($.isPlainObject(t[r1])) return the.tplRplRecursive(t[r1]);
				return t[r1] || '';
			});
		},
	    initSelect2: function(){
	    	if(!$().select2 || isPhone) return;
	    	$('.select2s:not(.no-select2)').addClass('no-select2').select2({
                placeholder: "请选择...",
                allowClear: true
            });
	    },
	    initBsSelect: function(){
	    	if(!$().selectpicker) return;
	    	var isSearch;
	    	$('.bs-selects:not(.no-bs-select)').each(function(){
	    		isSearch = false===$(this).data('search')?false:true;
		    	$(this).selectpicker({
		            iconBase: 'fa',
		            tickIcon: 'fa-check',
		            size:6,
		            liveSearch: isSearch,
		            selectedTextFormat:'count > 3',
		            mobile: HyFrame.isPhone && !isSearch
		        });
	    	});
	    },
	    initDatetimePicker: function(){
	    	if(!$().datetimepicker) return;
	    	var config = {
	    			fontAwesome: true,
					language: 'zh-CN',
			        weekStart: 0,
			        todayBtn:  1,
					autoclose: 1,
					todayHighlight: 1,
					startView: 2,
					forceParse: 0
			};
	    	var $dp=$('input.datepickers:not(.datetimepickers)');
			if($dp.size()){
				var format,showDay;
				$dp.each(function(){
					format = $(this).data('date-format') ? $(this).data('date-format') : 'yyyy-mm-dd';
					showDay = /dd/.test(format);
					$(this).datetimepicker($.extend({},config,{
							format: format,
							startView: showDay ? 2 : 3,
							minView: showDay ? 2 : 3
					}));
				});
			}
			var $dtp=$('input.datetimepickers');
			if($dtp.size()){
				$dtp.each(function(){
					$(this).datetimepicker($.extend({},config,{
						format:$(this).data('date-format') ? $(this).data('date-format') : 'yyyy-mm-dd hh:ii',
				        showMeridian: 1
					}));
				});
			}
			// 日期范围控制
			$('.input-daterange .datepickers').click(function(){
				var $the=$(this);
				var idx=$the.index('.input-daterange .datepickers');
				var val=$the.parents('.input-daterange').find('.datepickers').eq(idx?0:1).val();
				if(!val) return;
				$the.datetimepicker(idx?'setStartDate':'setEndDate',val);
			});
	    },
	    initSlimScroll: function(){
	    	if($.isPhone) $('.scroller.no-scroller-xs').removeClass('scroller').css({height:'auto'});
	    },
	    initAJAX: function(){
	    	the.initSelect2();
	    	the.initDatetimePicker();
	    	the.initBsSelect();
	    	the.initSlimScroll();
	    	Metronic.initAjax();
	    }
	}
}();