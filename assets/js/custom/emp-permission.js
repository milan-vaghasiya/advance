$(document).ready(function(){
    $('.collapse.in').prev('.panel-heading').addClass('active');
    $('#bs-collapse').on('show.bs.collapse', function(a) {
        $(a.target).prev('.panel-heading').addClass('active');
    }).on('hide.bs.collapse', function(a) {
        $(a.target).prev('.panel-heading').removeClass('active');
    });
    
    $(document).on('click','.checkAll',function(){
        var menu_id = $(this).val();
        if($(this).prop('checked')==true){
            $(".check_"+menu_id).attr('checked',true);  
        }else{
            $(".check_"+menu_id).attr('checked',false);
        }
    });

    $(document).on('change',"#emp_id",function(){
        var emp_id = $(this).val();
        var menu_type = $("#menu_type").val();
        $("#empPermission")[0].reset();
        $(".error").html("");
        $(this).val(emp_id);
        $(this).select2();
        initSelect2();
        $(".chk-col-success").removeAttr("checked");
        
        $.ajax({
            type: "POST",   
            url: base_url + controller + '/editPermission',   
            data: {emp_id:emp_id,menu_type:menu_type},
            dataType:"json"
        }).done(function(response){
            var permission = response.empPermission;
            if(permission.length > 0){
                $.each(response.empPermission,function(key, value) {
                    $("#"+value).attr("checked","checked");
                }); 
            }
        });
    });
});

function resPermission(data,formId){
    if(data.status==1){
        $("#"+formId)[0].reset();
        $(".chk-col-success").removeAttr("checked");
		Swal.fire( 'Success', data.message, 'success' );
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
			Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resCopyPermission(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        colseModal(formId);
		Swal.fire( 'Success', data.message, 'success' );
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
			Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}
