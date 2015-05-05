/**
 * HyFrame Page-All
 */
jQuery.fn.hyall = function(config){
	
	var dt = {}; // Datatables instance
	var dtOptions = {}; // Datatables options
	var dtColumns = []; // Datatables columns
	var $dtContainer;
	var $formValidator = {}; //表单验证JQ载体
	var $formModal = {}; // HyAll 页面  modal JQ对象
	var the = this; //全局对象
		
	this.initialized = false;
	this.config = config,
	this.allModalObservers = [],
	this.detailModalObservers = [];
    this.initObservers = [];
    
	/**
	 * 上传文件扩展名对应 MINE type
	 */
	var ext2mine = {
		jpg:'image/jpeg',
		png:'image/png',
		swf:'application/x-shockwave-flash',
		zip:'application/zip',
		rar:'application/octet-stream',
		doc:'application/msword',
		docx:'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		xls:'application/vnd.ms-excel',
		xlsx:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		ppt:'application/vnd.ms-powerpoint',
		pptx:'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		txt:'text/plain',
		pdf:'application/pdf'
	};
	/**
	 * 根据扩展名取MINE字符串
	 */
	var getMinesByExt = function(exts){
		if(!exts) return '';
		if('string' === typeof exts) exts = exts.split(',');
		var mines = [];
		$.each(exts, function(k,v){
			mines.push(ext2mine[v] || '');
		});
		return mines.join(',');
	};
	/**
	 * 处理“其他”选择项
	 */
	var selectAddonHandler = function(){
    	the.getFormModal().on('change', 'select.select-addon', function(){
        	var $addon=the.getFormModal().find(':text[data-addon="'+$(this).attr('id')+'"]');
        	if(!$addon.size()) return;
        	var val=$(this).val();
        	if($(this)[0].selectedIndex===$(this)[0].options.length-1) {
        		$(this).find('option:selected').val('');
        		$addon.val('');
        		$addon.parents('.form-group').show();
        	}else{
        		$addon.val('__FALSE__');
        		$addon.parents('.form-group').hide();
        	}
        });
        the.getFormModal().on('keyup', ':text.select-addon', function(){
        	$belong=the.getFormModal().find('#'+$(this).data('addon'));
        	if(!$belong.size()) return;
        	$belong.find('option:selected').val($(this).val());
        	if(!$.isEmptyObject($formValidator))$formValidator.validate().element($belong);
        });
    };
    /**
     * 自动刷新DT的操作反馈提示
     */
    this.dtActionAlert = function(r){
    	return $.actionAlert(r, false, function(r){
    		if(r.status && !$.isEmptyObject(dt)) dt.getDataTable().ajax.reload(undefined, false);
    	});
    };
	/**
	 * 表单验证
	 * options:{form, rules, hy:true|false, onComplete:callback}
	 */ 
	this.validateFormHandler = function(options) {
        $formValidator = options.form;
        var error = $('.alert-danger', $formValidator);
        var success = $('.alert-success', $formValidator);	    	
        $formValidator.validate($.extend(true, {
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: true,
            ignore: "", // validate all fields including form hidden input
            rules: {},
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) { 
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) { 
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                success.hide();
                error.show();
                Metronic.scrollTo(error, -200);
            },
            highlight: function (element) { // hightlight error inputs
               $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            },
            debug:true,
            ignore:':not([name])',
            submitHandler: function (form) {
                success.show();
                error.hide();
                if(!options.hy) {
                	$(form).submit();
                	return false;
                }
                the.actionsHandlers.formSubmit($(form),options.onComplete);
            }
        }, options));
        if($().datetimepicker){
	        HyFrame.initDatetimePicker();
        	$('input.datepickers,input.datetimepickers').datetimepicker().on('dateChange',function(){
        		$formValidator.validate().element($(this));
        	});
        }
        $('.select2s').on('change',function(){
        	$formValidator.validate().element($(this));
        });
    },
	/**
	 * 表单项生成器
	 */
    this.formBuilderTypes = {
    	select: function(root, k, v, val){
    		if(!root.select) root.select={};
			if(v.search) root.select.first=root.select.first||(root.title||v.title)+'...';
    		if(!v.search && root.select.addon) root.style+=' select-addon';
			var isMul=(!val[k] && root.select.multiple)||(val[k] && root.select.editmultiple);
    		var html='';
			html+='<select name="'+k+( isMul ? '[]" multiple' : '"')+' id="'+k+'" class="form-control bs-selects '+(root.style || '')+'" '+(root.attr || '')+(root.select.title ? ' title="'+root.select.title+'"' : '')+' >';
			if('_search'==root.options) root.options=v.list.search.options || false;
			if(root.options){
				if(root.select.first) html+='<option value="">'+root.select.first+'</option>';
				if(val[k] && !root.options[val[k]]) html+='<option value="'+val[k]+'">'+(val[k+'_text'] || '...')+'</option>';
				if(val[k] && !$.isArray(val[k])) val[k]=val[k].split(',');
				if(root.select.optgroup){
					$.each(root.options,function(k1,v1){
						html+='<optgroup label="'+k1+'">';
						$.each(v1,function(k2,v2){
							html+='<option value="'+k2+'" '+((val[k] && $.inArray(k2,val[k])>-1) ? 'selected' : '')+'>'+v2+'</option>';
						});
						html+='</optgroup>';
					});
				}else{
					$.each(root.options,function(k1,v1){
						html+='<option value="'+k1+'" '+((val[k] && $.inArray(k1,val[k])>-1) ? 'selected' : '')+'>'+v1+'</option>';
					});
				}
				if(!v.search && root.select.addon) {
	    			html+='<option value="">其它（请补充）</option>';
	    		}
			}
			html+='</select>';
    		if(!v.search && root.select.addon){
    			var htmlAddon = '<div class="form-group" style="display: none;"><label class="control-label col-md-3 col-sm-10">请补充<span class="required" aria-required="true">&nbsp;*</span></label><div class="col-md-6 col-sm-10"><input type="text" class="form-control select-addon" value="__FALSE__" data-addon="'+k+'" name="_addon_'+k+'"></div></div>'
    			the.on('shown.hyall.form', function(){
    				the.getFormModal().find('#'+k).parents('.form-group').after(htmlAddon);
    			});
    		}
			return html;
    	},
    	date: function(root, k, v, val){
			return '<div data-placement="top" title="点击选择时间" '+(/form-filter/.test(root.style) ? 'style="width:220px !important;"' : '')+' class="tooltips '+(root.style||'')+'"><div class="input-group date" ><input type="text" '+(root.attr || '')+' id="'+k+'" name="'+k+'" '+(val[k] ? 'value="'+val[k]+'"' : '')+' class="form-control datepickers '+(root.style||'').replace(/\sinput-\w+/g,'')+'" readonly><span class="input-group-addon"><span class="fa fa-calendar"></span></span></div></div>';
    	},
    	datetime: function(root, k, v, val){
    		root.style=(root.style||'')+' datetimepickers';
    		return the.formBuilderTypes.date(root,k,v,val);
    	},
    	daterange: function(root, k, v, val){
    		if(!root.daterange) root.daterange={};
    		var val1=val[(root.daterange.from || k)] || root.daterange.value1;
    		var val2=val[(root.daterange.to || k+'_to')] || root.daterange.value2;
    		return '<div '+(/form-filter/.test(root.style) ? 'style="width:290px !important;"' : '')+'  data-placement="right"  title="点击选择时间" class="tooltips '+(root.style||'')+'"><div class="input-group input-daterange " ><input type="text" class="form-control datepickers '+(root.style||'').replace(/\sinput-\w+/g,'')+'" '+(root.attr || '')+' '+(val1 ? 'value="'+val1+'"' : '')+' name="'+(root.daterange.from || k)+'" id="'+k+'" readonly ><span class="input-group-addon"> - </span><input type="text" readonly  class="form-control datepickers '+(root.style||'').replace(/\sinput-\w+/g,'')+'" '+(val2 ? 'value="'+val2+'"' : '')+' name="'+(root.daterange.to || k+'_to')+'" id="'+k+'_to" ></div></div>';
    	},
    	textarea: function(root,k,v,val){
    		return '<textarea class="form-control '+(root.style||'')+'" '+' name="'+k+'" id="'+k+'" '+(root.attr || '')+' >'+(val[k] || '')+'</textarea>';
    	},
    	file: function(root, k, v, val){
    		if(!root.file) root.file={};
    		if(!v) v=false;
    		if(!val) val=false;
    		var exts = getMinesByExt(root.file.ext);
    		// TODO 唉，说多了都是泪呀！
    		return ['<div class="hy-upload display-none" data-field="'+k+'">',
    		'<div class="row" style="width:260px;"><div class="col-xs-6">',
				'<div class="b-upload__hint">'+ ( val[k] ? '<a href="'+val[k]+'" target="_blank">原始文件</a>' : '请先选择文件' ) + '</div>',
				'<div class="js-files b-upload__files">',
				'<div class="js-file-tpl b-thumb" data-id="<%=uid%>" title="<%-name%>, <%-sizeText%>">',
					'<div data-fileapi="file.remove" class="b-thumb__del">✖</div>',
					'<div class="b-thumb__preview">',
						'<div class="b-thumb__preview__pic"></div>',
					'</div><% if( /^image/.test(type) ){ %><div data-fileapi="file.rotate.cw" class="b-thumb__rotate"></div><% } %><div class="b-thumb__progress progress progress-small"><div class="bar"></div></div>',
					'<div class="b-thumb__name"><%-name%></div>',
				'</div>',
			'</div>',
			'</div>',
			'<div class="col-xs-6">',
				'<div class="btn blue btn-small js-fileapi-wrapper js-browse btn-select"">',
					'<span>选择</span>',
					'<input type="file" '+(exts ? ' accept="'+exts+'"' : '' ) + ' name="filedata" /><input type="hidden" name="'+k+'" />',
				'</div>',
				'<div class="js-upload btn purple btn-small btn-upload">',
					'<span>上传</span>',
				'</div>',
			'</div></div>',
			'</div>'].join('');
    	},
    	html: function(root, k, v, val){
    		return root.html.replace(/\{:([\w\-#]+)}/g,function(r0,r1){
    			switch(r1){
    				case '#':
    					return val[k];
    				default:
    					return val[r1];
    			}
    		});
    	},
    	_default:function(root, k, v, val){
    		return '<input type="text" '+(val[k] ? 'value="'+val[k]+'"' : '')+' '+(root.tip ? 'placeholder="'+root.tip+'"' : '')+'" class="form-control '+(root.style || '')+'" '+' name="'+k+'" id="'+k+'" '+(root.attr || '')+' >';
    	}
    };
    /**
     * 表单生成器
     */ 
    this.formBuilder = function(columns, val, hidden, type){
    	var html = '';
    	if(!val) val = false;
    	if(hidden)
    	$.each(hidden, function(k, v){
    		html+='<input type="hidden" name="'+k+'" value="'+v+'">';
    	});
    	var rules = {};
    	var _type = type;
	    $.each(columns, function(k, v){
	    	if(!v.form || false===v.form[_type]) return true;
	    	var title=(v.form.title || v.title);
	    	if(!title) return true;
	    	if(!v.form.validate) v['form']['validate']=false;
	    	else rules[('select'===v.form.type && v.form.select && v.form.select.multiple)?k+'[]':k]=v.form.validate;
	    	html+='<div class="form-group"><label class="control-label col-md-3 col-sm-10" for="'+k+'">'+title;
	    	html+=(v.form.validate.required ? '<span class="required">&nbsp;*</span>' : '&nbsp;&nbsp;')+ '</label><div class="col-md-6 col-sm-10">';
	    	if(!v.form.type || 'text'==v.form.type) v['form']['type']='_default';
	    	var type=v.form.type.toLowerCase();
	    	v.search=false;
	    	try{
	    		html+=eval("the.formBuilderTypes[type](v.form,k,v,val);");
	    	}catch(e){
	    		alert('HyFrame Alert : Please implement the interface formBuilderTypes.'+type+' manually!');
	    	}
	    	html+='</div>'+((v.form.tip && type!=='_default' && type!=='textarea')?'<span class="help-block">'+v.form.tip+'</span>':'')+'</div>';
	    });
    	return {html:html,rules:rules};
    };
	/**
	 * 操作处理
	 */
    this.actionsHandlers = {
    	formSubmit:function($form,onComplete){
    		$modal=$form.parents('.modal');
    		if($modal.size()) $modal.find('.btn').prop('disabled',true);
    		$.post($.U('ajax', {'q': $form.find('[name="_submit"]').val()}),$form.serialize(),function(r){
    			$('.form-group',$form).removeClass('has-success');
    			if($modal.size()) $modal.modal('toggle').find('.btn').prop('disabled',false);
				the.dtActionAlert(r);
				if($.isFunction(onComplete)) onComplete(r,$form);
			});
    	},
    	initAdd: function($modal){
		    var build=the.formBuilder(the.config.columns, false, {_submit:'insert'}, 'add');
		    var tpl={
					modal: 'add',
					size: $modal.data('size') || '',
					title:{
						icon:'fa-plus-circle',
						action:'新增'
					},
					body:{
						main:build.html
					}
			};
			the.setModal(tpl);
    	    if(the.config.options.tips.add) $modal.find('.alert-tips').show().find('span').html(the.config.options.tips.add);
    	    the.validateFormHandler({form: $modal.find('form'), rules: build.rules, hy: true});
    		$modal.on('shown.bs.modal',function(){
    			the.trigger('shown.hyall.form.add');
    		});
			$modal.modal('toggle');
    	},
    	initDetail: function(data, tpl, $con, baseURL){
    		var $modal=$('.hy-detail-modal', the);
    		$modal.on('hide.bs.modal',function(){
    			the.detailModalObservers=[];
    		});
    		var doLoad = function(data,tpl){
                $.loading();
                tpl=$.extend({
		    			_base:'<div class="modal-dialog modal-lg detail-modal-{:modal}"><div class="modal-content"><form class="{:formStyle}"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>{:title}</div><div class="modal-body">{:body}</div><div class="modal-footer">{:buttons}</div></form></div></div>',
						modal:'',
						formStyle:'form-horizontal',
		    			title:{
							_base:'<h4 class="caption"><i class="fa font-green-sharp {:icon} fa-fw"></i><span class="caption-subject font-green-sharp bold"><span class="detail-title">{:title}</span></span></h4>',
							icon:'fa-search',
							title: '浏览'+the.config.title+'信息'
						},
						body:{
							_base:'<div class="form-inner"><div class="alert alert-info alert-tips display-none"><strong>操作提示：</strong><span></span></div><div class="hy-detail-container">{:main}</div></div>',
							main:''
						},
						buttons:'<button type="button" data-dismiss="modal" class="btn blue">关闭</button>'
                },tpl);
    			$.post(baseURL || $.U('ajax?q=detail'), $.extend({},data), function(html){
    				$.unloading();
    				if(!$con || !$con.size()){
        				tpl.modal = (tpl.modal || data.type || 'default');
        				tpl.body.main=html;
        				$modal.html(HyFrame.tplRplRecursive(tpl));
        				var once=0;
                		$modal.on('shown.bs.modal',function(){
                			if(once++) return;
                			if(data.type) the.trigger('shown.hyall.detail.'+data.type);
                		});
        				$modal.modal('show');
    				}else{
    					$con.html(html);
            			the.trigger('shown.hyall.detail');
            			if(data.type) the.trigger('shown.hyall.detail.'+data.type);
    				}
    			});
    		};
    		if($.isPlainObject(data)) {
    			doLoad(data, tpl);
    			return;
    		}
    		$dtContainer.find('table').on('click','.dt-detail',function(){
    			var idx = the.getRowIdx($(this));
		    	var $checks=$('tbody > tr > td:nth-child(1) input[type="checkbox"]', $dtContainer);
		    	if($checks.size()){
			    	$checks.prop('checked',false);
			    	$checks.eq(idx).not('[disabled]').prop('checked',true);
			    	$.uniform.update($checks);
			    	dt.countSelectedRecords();
		    	}
				var data={pk:dt.getHyPk(idx)};
				doLoad(data);
	    	    return;
		    });
    		$dtContainer.find('.dt-top-actions,.dt-bottom-actions').on('click','.dt-detail',function(){
				var type=$(this).data('detail');
				if(!type) return;
    			var option=the.config.options.buttons[type];
    			var title='<h4 class="caption"><i class="fa font-green-sharp '+(option.icon||'')+' fa-fw"></i><span class="caption-subject font-green-sharp bold">'+(option.title||'')+'{title}信息</span></h4>'.replace('{title}',the.config.title);
    			doLoad({type: type}, {title: title});
	    	    return;
    		});
    	},
    	actionEdit: function(rows, $modal, columns){
    		$.loading();
    	    $.each(rows,function(k,v){
    	    	$.post($.U('ajax?q=edit'), {pk:v}, function(r){
    	    		$.unloading();
    	    		if(!r.status){
    	    			the.dtActionAlert(r);
    	    			return false;
    	    		}
    	    		if($.isPlainObject(r.columns)) columns=r.columns;
            	    var build=the.formBuilder(columns,r.val,{_submit:'update',_token:r.token},'edit');
            	    var tpl={
        					modal:'edit',
        					size: $modal.data('size') || '',
        					title:{
        						icon:'fa-edit',
        						action:'编辑'
        					},
        					body:{
        						main:build.html
        					}
        			};
        			the.setModal(tpl);
            	    the.validateFormHandler({form: $modal.find('form'), rules: build.rules, hy: true});
	        	    if(the.config.options.tips.edit) $modal.find('.alert-tips').show().find('span').html(the.config.options.tips.edit);
	        		$modal.on('shown.bs.modal',function(){
	        			the.trigger('shown.hyall.form.edit');
	        		});
	        	    $modal.modal('toggle');
    	    	});
    	    });
    	},
    	actionDelete: function(rows){
    		$.post($.U('ajax?q=delete'), {pk:rows.join(',')},function(r){
    			the.dtActionAlert(r);
    		});
    	}
    };
	/**
	 * 设定DataTable配置
	 */
	this.setDtOptions = function(options){
		dtOptions=options;
	};
	/**
	 * 取得DataTable列信息
	 */
	this.getDtColumns = function(){
		return dtColumns;
	};
	/**
	 * 取得元素所在表格行的索引
	 */
	this.getRowIdx = function($el){
		$tbody = $dtContainer.find('table tbody');
		var idx = 0;
		var $child = $el.parents('tr.child');
		if($child.size()) idx = $tbody.find('[role="row"]').index($child.prev());
		else idx = $tbody.find('[role="row"]').index($el.parents('[role="row"]'));
    	return idx;
	};
	/**
	 * 取得表单验证jQ载体
	 */
	this.getFormValidator = function(){
		return $.isEmptyObject($formValidator) ? false : $formValidator;
	};
    /**
     * 取得DataTable实例
     */
    this.getDataTable = function(){
    	return dt;
    };
    /**
     * 取得DataTable实例
     */
    this.getDtContainer = function(){
    	return $dtContainer;
    };
    /**
     * 取得allModel jQ载体
     */
    this.getFormModal = function(){
    	if($.isEmptyObject($formModal)) $formModal = $('.hy-form-modal', the);
    	return $formModal;
    };
    /**
     * modal生成器
     * tpl：每一项可以是html字符串也可以是对象，如果是对象，则必须有_base元素，对象中支持使用{:xxx}递归替换
     */ 
    this.setModal = function(tpl){
    	tpl.size = tpl.size=='defalut' ? '' : (tpl.size=='large' ? 'modal-lg' : (tpl.size=='full' ? 'modal-full' : ''));
    	tpl = $.extend(true, {
    			_base: '<div class="modal-dialog all-modal-{:modal} {:size}"><div class="modal-content"><form class="{:formStyle}"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>{:title}</div><div class="modal-body">{:body}</div><div class="modal-footer">{:buttons}</div></form></div></div>',
				modal: '',
				size: '',
				formStyle: 'form-horizontal',
    			title: {
					_base: '<h4 class="caption"><i class="fa font-green-sharp {:icon} fa-fw"></i><span class="caption-subject font-green-sharp bold"><span class="action">{:action}</span><span class="hy-title">{:title}</span></span></h4>'.replace('{:title}',the.config.title),
					icon: '',
					action: ''
				},
				body: {
					_base: '<div class="scroller no-scroller-xs form-inner" '+($.isPhone ? 'data-height="320px"' : 'style="height:320px;" data-always-visible="1"')+'><div class="alert alert-info alert-tips display-none"><strong>操作提示：</strong><span></span></div>{:main}</div>',
					main: ''
				},
				buttons: '<button type="button" data-dismiss="modal" class="btn default">取消</button><button type="submit" class="btn green submit">提交</button>'
    	}, tpl);
    	the.getFormModal().html(HyFrame.tplRplRecursive(tpl));
    };
    /**
     * 初始化
     */
	this.init = function(){
		if(the.initialized) return the;
		// datatable start
	    dt = new Datatable();
		// dt-data init
		if(config.title) $('.hy-title').html(config.title);
		if(config.subtitle) $('#hy-subtitle').html(config.subtitle);
		if(!config.options.checkbox) {
			dtColumns.push({data:'_null',width:'1',className:'',defaultContent:'',title:'',orderable:false});
		}else {
			dtColumns.push({data:'_checkbox',width:'2%',title:'<input type="checkbox" title="全选/取消" class="group-checkable">',orderable:false,defaultContent:'<input type="checkbox">'});
		}
		$.each(config.columns,function(k,v){
			if(true===v.list.hidden || '_checkbox'==k) return true;
			dtColumns.push({data:k,title:v.list.title||v.title,width:v.list.width,className:v.list.style,orderable:(false===v.list.order ? false : true)});
		});
		var lengthMenu=[[config.options.limit, 25, 50, 100, 200],[config.options.limit, 25, 50, 100, 200]];
		if(config.options.all) {
			lengthMenu[0].push(-1);
			lengthMenu[1].push('全部');
		}
		// dt-search init
		var searchShow = false;
		var dtSearchForm = '<div class="row"><div class="col-md-11 col-sm-12 form-filter-wrapper">';
		$.each(config.columns, function(k, v){
			if(!v.list.search) return true;
			searchShow=true;
			if(!v.list.search.type) v['list']['search']['type'] = '_default';
			if(!v.list.search.type || 'text'==v.list.search.type) v['list']['search']['type'] = '_default';
	    	var type = v.list.search.type.toLowerCase();
			v['list']['search']['style'] = 'form-filter input-inline input-small '+(v['list']['search']['style'] || '');
			v['search'] = true;
			if($.url('?'+k)){
				if(!the.config.options.buttons) the.config.options.buttons={};
				if(!the.config.options.buttons.goBack) config.options.buttons.goBack={title:'返回',icon:'fa-history',href:"javascript:history.go(-1);"};
				dt.setAjaxParam(k,$.url('?'+k));
			}else{
				dtSearchForm+='select'!=v.list.search.type ? '<span class="form-label">'+(v.list.search.title || v.title)+'：</span>' : '';
				dtSearchForm+='<div class="form-filter-block">'+eval("the.formBuilderTypes[type](v.list.search,k,v,false);")+'</div>';
			}
		});
		dtSearchForm+='</div><div class="col-md-1 col-sm-12"><button class="btn btn-sm purple-plum dt-search-submit pull-right"><i class="fa fa-search"></i>&nbsp;检索</button><div class="clearfix"></div></div></div>';
		// dt-btm-actions
		var dtBtmActions='';
	    if(config.options.actions)
	    $.each(config.options.actions,function(k,v){
	    	dtBtmActions+='<a href="javascript:;" data-value="'+k+'" data-confirm="'+(v.confirm ? 'true' : 'false')+'" '+(v.max ? 'title="每次只可'+v.title+v.max+'条记录！"' : '')+' data-max="'+(v.max || -1)+'" class="btn btn-default disabled" >'+v.title+'</a>';
	    });
	    dtBtmActions='<div class="dt-btm-actions btn-group">'+dtBtmActions+'</div>&nbsp;<span></span>';
	    if(config.options['export'] || config.options.print){
			var tools =  {
            	tableTools: {
                    aButtons: []
                },
            };
			if(config.options['export']) {
				tools.tableTools.aButtons.push({
	                sExtends: config.options['export'],
	                sButtonText: '<i class="fa fa-share fa-fw"></i>导出'
	            });
				tools.tableTools.sSwfPath = Metronic.getGlobalPluginsPath()+'datatables/extensions/TableTools/swf/copy_csv_xls' + ('pdf'===config.options['export'] ? '_pdf' : '') + '.swf';
			}
			if(config.options.print) tools.tableTools.aButtons.push({
                sExtends: "print",
                sButtonText: '<i class="fa fa-print fa-fw"></i>打印',
                sInfo: '请按"CTR+P"打印表格，或按"ESC"退出打印！',
                sMessage: "数据由系统自动生成"
            });
		}
	    // init datatable
        dt.init($.extend(true, {
        	hyall: the, 
            src: '.hy-table-container table',
            dtSearchForm:dtSearchForm,
            dtBtmActions:dtBtmActions,
            dataTable: $.extend({
                lengthMenu: lengthMenu,
                pageLength: lengthMenu[0][0],
                ajax: {
                	url: $.U('ajax?q=list')
                },
                columns: dtColumns
            }, tools ? tools : {})
        }, dtOptions));
        $dtContainer = dt.getTableContainer()
        if(!config.options.checkbox) {
			$('.dt-bottom-actions', $dtContainer).remove();
		}
		if(!config.options.dtResponsive){
			$dtContainer.find('table').removeClass('dt-responsive');
		}
	    // init hy-form-modal
		var $modal=the.getFormModal();
	    $modal.data('size', config.options.formSize);
	    // hy-top-actions init
		if($.isPlainObject(config.options.buttons)){
			var $ctx = $('.dt-top-actions', $dtContainer), html = '';
			$.each(config.options.buttons, function(k,v){
				html+='<a href="{:href}" class="btn btn-default btn-sm btn-circle pull-right {:style}" {:data}><span class="fa {:icon} fa-fw"></span><span class="hidden-480">{:title}</span></a>'.replace(/\{:(\w+)}/g,function(r1,r2){
					switch(r2){
						case 'style':
							return v.detail ? ' dt-detail' : ''; 
						case 'data':
							return (v.detail ? 'data-detail="'+k+'"' : '')+' data-action="'+k+'"';
						case 'href':
							return v[r2] || 'javascript:;';
						default:
							return v[r2] || '';
					}
				});
			});
			$ctx.append(html);
			// add click
			if(config.options.buttons.add) $('[data-action="add"]', $ctx).on('click',function(e){
	            e.stopPropagation();
				the.actionsHandlers.initAdd($modal);
			});
		}
	    // dt-bottom-actions init
	    $dtContainer.on('click', '.dt-btm-actions .btn', function (e) {
            e.stopPropagation();
            var $btn = $(this);
            var handle = function($btn){
	            var action = $btn.data('value');
	            var max = $btn.data('max');
			    var rows = dt.getSelectedRows();
			    if(!rows.length) return false;
			    if(max>0 && rows.length>max) return false;
            	eval('the.actionsHandlers.action'+$.ucfirst(action)+'(rows,$modal,config.columns);');
            };
            if($(this).data('confirm')){
	            $.confirm('<big class="text-danger">确认'+$(this).html()+$(this).parents('.table-btm-actions').find('span').html()+'?</big>',function(){
	            	handle($btn);
	            });
            }else{
	            handle($btn);
            }
        });
	    the.on('shown.hyall.form', function(){
	    	selectAddonHandler();
	    	HyFrame.uploadHandler();
	    	HyFrame.initAJAX();
	    });
		$modal.on('shown.bs.modal',function(){
			the.trigger('shown.hyall.form');
		});
	    // init hy-detail
	    the.actionsHandlers.initDetail();
	    the.initialized = true;
	    return the;
	};
	/**
	 * 重新装载
	 */
    this.reload = function(config){
    	the.initialized = false;
    	if(config) the.config = config;
    	the.init();
    };
	// 自动初始化
	return this.init();
};