/**
 * Datatable Wrapper
 */
var Datatable = function() {

    var dataTable, // Datatables object
        dtOptions, // main options
        $table, // actual table jquery object
        $tableContainer, // actual table container object
        ajaxParams = {}, // 检索数据
        $dtSearchWapper, // 检索条JQ载体
        hyPks = [], // 主键数组
        ready = false, // 是否初始化完成
        the;

    var countSelectedRecords = function() {
        var selected = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', $table).size();
        var text = dtOptions.dataTable.language.txGroupActions;
        var $btns=$('.dt-btm-actions .btn',$tableContainer);
        if (selected > 0) {
        	$btns.removeClass('disabled').each(function(){
        		if(selected>$(this).data('max') && $(this).data('max')>0) $(this).addClass('disabled');
        	});
            $('.table-btm-actions > span', $tableContainer).text(text.replace("_TOTAL_", selected));
        } else {
        	$btns.addClass('disabled');
            $('.table-btm-actions > span', $tableContainer).text("");
        }
    };
    $.extend(true, $.fn.DataTable.TableTools.classes, {
    	"container": "btn-group tabletools-wrapper",
        "buttons": {
            "normal": "btn btn-default btn-circle btn-sm hidden-xs"
        }
    });
    return {
        //main function to initiate the module
        init: function(options) {
            if (!$().dataTable) {
                return;
            }
            the = this;
            // default settings
            options = $.extend(true, {
                src: "", // actual table  
                loadingMessage: '正在载入数据...',
                dtBtmActions:'',
                dtSearchForm:'',
                dataTable: {
                    dom: "<'row'<'col-md-12'T>><'row'<'col-md-12'<'table-search-form'>>r><'dt-responsive-wrapper't><'row'<'col-md-6 col-xs-12'<'table-btm-actions'>><'col-md-6 col-xs-12'<'pull-right'pli>>>", // datatable layout
                    pageLength: 10, // default records per page
                    language: { // language settings
                        // metronic spesific
                        txGroupActions: "选中的 _TOTAL_ 条记录",
                        txAjaxRequestGeneralError: "网络请求失败！请检查您的网络连接！",
                        // data tables spesific
                        lengthMenu: "<span class='seperator'>|</span>每页显示 _MENU_ 条",
                        info: "<span class='seperator'>|</span>共 _TOTAL_ 条记录",
                        infoEmpty: "无记录！",
                        emptyTable: "无记录！",
                        zeroRecords: "未找到满足条件的记录！",
                        paginate: {
                            previous: "上一页",
                            next: "下一页",
                            last: "尾页",
                            first: "第一页",
                            page: "当前第",
                            pageOf: "页 总页数"
                        }
                    },
                    order:[0],// default order column
                    pagingType: "bootstrap_extended", // pagination type(bootstrap, bootstrap_full_number or bootstrap_extended)
                    autoWidth: false, // disable fixed width and enable fluid table
                    processing: false, // enable/disable display message box on record load
                    serverSide: true, // enable/disable server side ajax loading
                    tableTools: {
                        sSwfPath: Metronic.getGlobalPluginsPath()+'datatables/extensions/TableTools/swf/copy_csv_xls.swf',
                        aButtons: [{
                            sExtends: "xls",
                            sButtonText: '<i class="fa fa-share fa-fw"></i>导出'
                        }, {
                            sExtends: "print",
                            sButtonText: '<i class="fa fa-print fa-fw"></i>打印',
                            sInfo: '请按"CTR+P"打印表格，或按"ESC"退出打印！',
                            sMessage: "数据由系统自动生成"
                        }]
                    },
                    deferRender: true,
                    ajax: { // define ajax settings
                        url: "", // ajax URL
                        type: "post", // request type
                        timeout: 5000,
                        data: function(data) { // add request parameters before submit
                        	var dtColumns = options.hyall.getDtColumns();
                        	$.each(data,function(k,v){
                        		switch(k){
                        			case 'order':
                        				if(v) data[k]={field:dtColumns[v[0].column].data,sort:v[0].dir};
                        				break;
                        			case 'columns':
                        				delete data[k];
                        				break;
                        		}
                        	});
                        	data['search']={};
                        	if(ajaxParams.action) delete ajaxParams.action;
                            $.each(ajaxParams, function(key, value) {
                            	data['search'][key]=value;
                            });
                            Metronic.blockUI({
                                message: dtOptions.loadingMessage,
                                target: $table,
                                cenrerY: true,
                                boxed: true
                            });
                        },
                        dataSrc: function(res) {
                            if ($('.group-checkable', $table).size() === 1) {
                                $('.group-checkable', $table).attr("checked", false);
                                $.uniform.update($('.group-checkable', $table));
                            }
                            hyPks = [];
                            $.each(res.data, function(k,v){
                            	hyPks.push(v._pk);
                            });
                            Metronic.unblockUI($table);
                            return res.data;
                        },
                        error: function() { // handle general connection errors
                            $.actionAlert({status:false, info:dtOptions.dataTable.language.txAjaxRequestGeneralError});
                            Metronic.unblockUI($table);
                        }
                    },
                    drawCallback: function(oSettings) { // run some code on table redraw
                        Metronic.initUniform($('input[type="checkbox"]', $table)); // reinitialize uniform checkboxes on each table reload
                        countSelectedRecords(); // reset selected records indicator
                    },
                    columns:[]
                }
            }, options);
                        
            dtOptions = options;

            // create table's jquery object
            $table = $(options.src);
            $tableContainer = $table.parents(".hy-table-container");

        	// apply the special class that used to restyle the default datatable
            var tmp = $.fn.dataTableExt.oStdClasses;
            $.fn.dataTableExt.oStdClasses.sWrapper = $.fn.dataTableExt.oStdClasses.sWrapper + " dataTables_extended_wrapper";
            $.fn.dataTableExt.oStdClasses.sLengthSelect = "form-control input-xsmall input-sm input-inline";
            // initialize a datatable
            dataTable = $table.DataTable(dtOptions.dataTable);
            // revert back to default
            $.fn.dataTableExt.oStdClasses.sWrapper = tmp.sWrapper;
            $.fn.dataTableExt.oStdClasses.sLengthSelect = tmp.sLengthSelect;
            // build table group actions panel
            $('.table-btm-actions', $tableContainer).html(dtOptions.dtBtmActions); 
            // build table search form
            $('.table-search-form', $tableContainer).html(dtOptions.dtSearchForm);
            // fix future els
            HyFrame.initAJAX();            
            // handle check/decheck all
            $('.group-checkable', $table).change(function() {
                var $set = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:not([disabled])', $table);
                var checked = $(this).is(":checked");
                $set.each(function() {
                    $(this).attr("checked", checked);
                });
                $.uniform.update($set);
                countSelectedRecords();
            });
            // handle row's checkbox click
            $table.on('change', 'tbody > tr > td:nth-child(1) input[type="checkbox"]', function() {
                countSelectedRecords();
            });
            // handle search
            $dtSearchWapper=$('.table-search-form', $tableContainer);
            // handle filter submit button click
            $dtSearchWapper.on('click', '.dt-search-submit', function(e) {
                e.preventDefault();
                the.submitFilter();
            });
            $dtSearchWapper.on('keypress', '.form-filter:text', function(e) {
	            if (e.which == 13) {
	                the.submitFilter();
	                return false;
	            }
            });
        },
        submitFilter: function() {
            // get all typeable inputs
            $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', $dtSearchWapper).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });
            // get all checkboxes
            $('input.form-filter[type="checkbox"]:checked', $dtSearchWapper).each(function() {
                the.addAjaxParam($(this).attr("name"), $(this).val());
            });
            // get all radio buttons
            $('input.form-filter[type="radio"]:checked', $table).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });
            dataTable.ajax.reload();
        },
        getSelectedRows: function() {
            var rows = [];
            $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', $table).each(function() {
            	rows.push(hyPks[$(this).index('[type="checkbox"]')-1]);
            });
            return rows;
        },
        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },
        addAjaxParam: function(name, value) {
            if (!ajaxParams[name]) {
                ajaxParams[name] = [];
            }
            skip = false;
            for (var i = 0; i < (ajaxParams[name]).length; i++) { // check for duplicates
                if (ajaxParams[name][i] === value) {
                    skip = true;
                }
            }
            if (skip === false) {
                ajaxParams[name].push(value);
            }
        },
        clearAjaxParams: function(name, value) {
            ajaxParams = {};
        },
        getDataTable: function() {
            return dataTable;
        },
        getTableContainer: function() {
            return $tableContainer;
        },
        countSelectedRecords:function(){
        	countSelectedRecords();
        },
        getHyPk:function(idx){
        	return hyPks[idx];
        }
    };

};