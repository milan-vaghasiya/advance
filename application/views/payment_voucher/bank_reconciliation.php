<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <div class="input-group">
                            <input type="date" name="from_date" id="from_date" class="form-control fyDates float-right" value="<?=(!empty($from_date))?$from_date:getFyDate()?>">
                            <input type="date" name="to_date" id="to_date" class="form-control fyDates float-right" value="<?=(!empty($to_date))?$to_date:getFyDate()?>">
                            <input type="hidden" name="acc_id" id="acc_id" value="<?=(!empty($acc_id))?$acc_id:""?>">

                            <select  id="status" class="form-control float-right">
                                <option value="-1">All</option>
                                <option value="0">Pending</option>
                                <option value="1">Completed</option>
                            </select>
                            <div class="input-group-append ml-2">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData" id="loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                        </div>
                        <div class="error fromDate"></div>
                        <div class="error toDate"></div>                        
					</div>
                    <h4 class="card-title pageHeader">Bank Reconciliation</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <form id="saveBankReconciliation" data-res_function="resBankReconciliation">
                                <div class="error general_error"></div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered"  style="min-width:1000px;">
                                        <thead class="thead-dark">
                                            <?php
                                                if(!empty($acc_id)):
                                            ?>
                                            <tr>
                                                <th class="text-center" colspan="11"><?=$bank_name?></th>
                                            </tr>
                                            <?php
                                                endif;
                                            ?>
                                            <tr>
                                                <th style="width:5%;">#</th>
                                                <th>Vou. Date</th>
                                                <th>Vou. No.</th>
                                                <th style="width:180px;">Party Name</th>
                                                <th>Ref No.</th>
                                                <th>Ref Date</th>
                                                <th>Bank Name</th>
                                                <th>Amount</th>
                                                <th>Reconciliation Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportData">

                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="customStore({'formId':'saveBankReconciliation','controller':'paymentVoucher','fnsave':'saveBankReconciliation'});">SAVE</button>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
var tableOption = {
    responsive: true,
    "scrollY": '52vh',
    "scrollX": true,
    deferRender: true,
    scroller: true,
    destroy: true,
    "autoWidth" : false,
    pageLength: -1,
    order: [],
    "columnDefs": [
        {type: 'natural',targets: 0},
        {orderable: false,targets: "_all"},
        {className: "text-center",targets: [0, 1]},
        {className: "text-center","targets": "_all"}
    ],    
    language: {search: ""},
    dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [ 'excel',{text: 'Refresh',action: function (){ $(".refreshReportData").trigger('click'); } }],
    "fnInitComplete":function(){  },
    "fnDrawCallback": function() {  }
};

$(document).ready(function(){
    
	<?php if(!empty($acc_id)): ?> 
        setTimeout(function(){$("#loadData").trigger('click');},500); 
    <?php endif; ?>

    $(document).on('click','#loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var acc_id = $("#acc_id").val();
		var status = $("#status").val();
		if($("#from_date").val() == ""){$(".from_date").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".to_date").html("To Date is required.");valid=0;}
        if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getBankTransactions',
                data: {acc_id:acc_id, from_date:from_date, to_date:to_date,status:status},
				type: "POST",
				dataType:'json',
				success:function(data){                    
                    if(data.status===0){
                        $(".error").html("");
                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                    }else if(data.status==1){
                        $("#reportTable").DataTable().clear().destroy();
                        $("#reportData").html(data.tbody);
                        reportTable('reportTable',tableOption);
                    }else{
                        toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    }				
                }
            });
        }
    });  
});

function resBankReconciliation(data,formId){
    if(data.status==1){
        setTimeout(function(){$("#loadData").trigger('click');},500);
        //toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        Swal.fire( 'Success', data.message, 'success' );
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            //toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }	
}
</script>