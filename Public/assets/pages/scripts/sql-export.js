var SqlExport = function(){


    var  Pigeonhole = function(){
        $('#allstudent ').on('click','#delallclass',function(){
            var grade = $(this).parent().parent().find('#class_id').find("option:selected").attr('value');

            $('#allstudent').modal('hide');
            $.loading();

            $.post($.U('Excel/SqlExport/ajax?q=sql_export'),{grade:grade},function(r){
                $.unloading();
                $.actionAlert(r);
            });
        });
    }

    return {
        init: function () {
            Pigeonhole();
        }
    };
}();