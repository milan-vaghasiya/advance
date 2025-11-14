<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6"><h4 class="card-title"><?=$pageHeader?></h4></div>       
							<div class="col-md-6">
                                <select id="item_type" class="form-control float-right" style="width:40%;">
                                    <option value="1">Finish Good</option>
                                    <option value="2">Consumable</option>
                                    <option value="3">Raw Material</option>
                                </select>
                            </div>
                        </div>                                         
                    </div>  
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered ssTable" data-url='/<?=$dataUrl?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change',"#item_type",function(){
        var item_type = $(this).val();
        $("#reportTable").attr("data-url",'/getDTRows/'+item_type);
        initTable();
    });
});

// function editStock(data,button){ 
// 	$("#storeModel").modal();
//     $("#item_id").val(data.id);//console.log(item_id);
// 	$.ajax({
// 		url: base_url +'stockVerification/editStock',
// 		data: {item_id:data.id},
// 		type: "POST",
// 		dataType:'json',
// 		success:function(data){
// 			$("#reportTable").dataTable().fnDestroy();
// 			// $("#theadData").html(data.thead);
// 			$("#tbodyData").html(data.tbody);
// 			initTable(0);
// 		}
// 	});
// }
</script>