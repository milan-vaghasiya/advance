<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
								<a href="<?= base_url("rejectionReview/pendingReviewIndex/$source");?>" class="nav-tab btn waves-effect waves-light btn-outline-danger active">Pending Review</a>
                            </li>
                            <li class="nav-item">
								<a href="<?= base_url("rejectionReview/reviewedIndex/$source");?>" class="nav-tab btn waves-effect waves-light btn-outline-success">Reviewed</a>
                            </li>
                        </ul>
					</div>
				</div>
            </div>			
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='cftTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$source?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
    $(document).on("change", "#rr_stage", function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        var process_id = $("#rr_stage").val();
        var prc_id = $("#prc_id").val();
        var log_id = $("#log_id").val();
        $("#rr_by").html("");
		$(".machineId").addClass("hidden");
		$(".operatorId").addClass("hidden");
		
        if (process_id) {
            $.ajax({
                url: base_url  + 'rejectionReview/getRRByOptions',
                type: 'post',
                data: {
                    process_id: process_id,
                    prc_id: prc_id,
                },
                dataType: 'json',
                success: function(data) {
                    $("#rr_by").html(data.rejOption);
                }
            });
        } 
        $("#rr_by").select2();
    });

   
	$(document).on("change", "#rr_by", function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        var rr_by = $("#rr_by").val();
        var process_id = $("#rr_stage").val();
        var prc_id = $("#prc_id").val();
        $("#mc_op_id").html('');$("#mc_op_id").select2();
        $("#in_ch_no").html('');$("#in_ch_no").select2();
		if(rr_by == 0){			
            $.ajax({
                url: base_url  + 'rejectionReview/getOperatorOptions',
                type: 'post',
                data: {
                    process_id: process_id,
                    prc_id: prc_id,
                },
                dataType: 'json',
                success: function(data) {
                    $("#mc_op_id").html(data.option);
                    $("#mc_op_id").select2();
                }
            });
		}else{
            $.ajax({
                url: base_url  + 'rejectionReview/getChNoOptions',
                type: 'post',
                data: {
                    process_id: process_id,
                    prc_id: prc_id,
                    rr_by: rr_by,
                },
                dataType: 'json',
                success: function(data) {
                    $("#in_ch_no").html(data.option);
                    $("#in_ch_no").select2();
                }
            });
        }
    });
});
function getReviewResponse(data,formId=""){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message}); 
        if(formId){
            $('#'+formId)[0].reset(); //closeModal(formId);
            initSelect2();	
        }
        var postData = {'postData':{'log_id':$("#log_id").val(),'source':$("#source").val()},'table_id':"rejTransTable",'tbody_id':'rejTbodyData','tfoot_id':'','fnget':'getReviewHtml'};
        getTransHtml(postData);
        initTable();
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