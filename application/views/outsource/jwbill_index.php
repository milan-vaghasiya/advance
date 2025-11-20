<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="row">
                        <div class="col-md-4">
                            <h4 class="card-title pageHeader">Vendor Challan</h4>
                        </div>      
                        <div class="col-md-3">
                            <select name="party_id" id="party_id" class="form-control select2">
                                <option value="">All Vendor</option>
                                <?php
                                    if(!empty($vendorList)){
                                        foreach($vendorList as $row){
                                        ?>
                                            <option value="<?=$row->id?>"><?=$row->party_name?></option>
                                        <?php }
                                    }
                                ?>
                            </select> 
                        </div>
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>"/> 
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-3">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"/>
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                                <div class="error toDate"></div>
                            </div>
                        </div>                 
                    </div> 
				</div>
            </div>
		</div>
         <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='jobWorkBillTable' class="table table-bordered ssTable ssTable-cf" data-url='/getjobWorkBillDTRows'></table>
                            </div>
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
	reportTable();
    setTimeout(function(){$('.loadData').trigger('click');},50);
    
    $(document).on('click','.loadData',function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var party_id = $('#party_id').val();
        
        $("#jobWorkBillTable").attr("data-url",$("#jobWorkBillTable").data('url')+'/'+from_date+'/'+to_date+'/'+party_id);
        $("#jobWorkBillTable").data("hp_fn_name","");
        $("#jobWorkBillTable").data("page","");
        $("#jobWorkBillTable").data("hp_fn_name",'getProductionDtHeader');
        $("#jobWorkBillTable").data("page",'jwbill');
        ssTable.state.clear();
        initTable();

        setTimeout(() => {
            initbulkUpdateButton();
        }, 1000);
	});

    $(document).on('click', '.BulkRequest', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkUpdate").show();
				$("input[name='log_id[]']").prop('checked', true);
			} else {
				$(".bulkUpdate").hide();
				$("input[name='log_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='log_id[]']").not(':checked').length != $("input[name='log_id[]']").length) {
				$(".bulkUpdate").show();
				$("#masterSelect").prop('checked', false);
			} else {
				$(".bulkUpdate").hide();
			}

			if ($("input[name='log_id[]']:checked").length == $("input[name='log_id[]']").length) {
				$("#masterSelect").prop('checked', true);
				$(".bulkUpdate").show();
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});

    $(document).on('click', '.bulkUpdate', function() {
		var log_id = [];
		$("input[name='log_id[]']:checked").each(function() {
			log_id.push(this.value);
		});
        var log_ids = log_id.join(",");
        setTimeout(() => {
            $('[name="ids"]').val(log_ids);
        }, 500);
	});
});

function initbulkUpdateButton() {
    var billParam = "{'postData':{'log_ids':''},'modal_id' : 'modal-md', 'form_id' : 'billForm', 'title' : 'Vendor Challan','call_function':'jobWorkBill','fnsave':'saveJobWorkBill','js_store_fn':'customStore'}".toString();

	var bulkUpdateBtn = '<button class="btn btn-outline-dark bulkUpdate" tabindex="0" aria-controls="jobWorkBillTable" type="button" onclick="modalAction('+billParam+');"><span>Bulk Update Bill No. & Date </span></button>';

	$("#jobWorkBillTable_wrapper .dt-buttons").append(bulkUpdateBtn);
	$(".bulkUpdate").hide();
}

function challanResponse(data,formId="billForm"){ 
    if(data.status == 1){
        $('#'+formId)[0].reset();
        Swal.fire({ icon: 'success', title: data.message });
        $('.billFormModal').modal('hide');
        $('.loadData').trigger('click');
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}
</script>