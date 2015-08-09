var ExcelImport = function(){
    var uploadHandler = function(){
        if(!$('.hy-upload').size()) return false;
        $('.hy-upload').each(function(){
            var $the = $(this);
            $the.fileapi({
                url: $.U('System/HyFile/upload'),
                accept: $the.find('[name="filedata"]').attr('accept'),
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
                    //console.log(rst);
                    $.actionAlert(rst);
                    if(rst.status) {
                        $('#excel-form .excel-import').on('click',function(){
                            $.loading();
                            $.post($.U('Excel/Excel/ajax?q=excel_import'),rst.data,function(r){
                                $.unloading();
                               // console.log(r);
                                $.actionAlert(r);
                                //console.log(r);
                                $("#result-error_equal").show();
                                $("#result-error_equal").html(r.data['0'].join(',') || '');
                                $("#result-error-write").html(r.data['1'].join(',') || '无');
                            });
                        });
                    }
                    if(!rst.status) $.actionAlert(rst);
                    $('[name="'+field+'"]').val(rst.data.key);
                    $('.js-browse span:first',$the).html('重新上传');
                    $('.js-browse',$the).removeClass('blue').addClass('btn-success');
                }
            });

            $(this).show();
        });
    }

    return {
        init: function () {
            uploadHandler();
        }
    };
	
}();