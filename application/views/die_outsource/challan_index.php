<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item">
								<a href="<?=base_url($headData->controller.'/index')?>" class="nav-tab btn waves-effect waves-light btn-outline-danger " id="pending_receive" style="outline:0px">Pending</a>
                            </li>
                            <li class="nav-item">
								<a href="<?=base_url($headData->controller.'/challanIndex/1')?>" class="nav-tab btn waves-effect waves-light btn-outline-success <?=($status == 1)?'active':''?>" id="completed_receive" style="outline:0px">Pending Receive</a>
                            </li>
							<li class="nav-item">
								<a href="<?=base_url($headData->controller.'/challanIndex/2')?>" class="nav-tab btn waves-effect waves-light btn-outline-success <?=($status == 2)?'active':''?>" id="completed_receive" style="outline:0px">Complete</a>
                            </li>
                        </ul>
					</div>
					
                    <h4 class="card-title text-center">Outsource</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='outsourceTable' class="table table-bordered ssTable ssTable-cf" data-url='/getChallanDTRows/<?=$status?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/imask/imask.js"></script>
<script>
	$(document).ready(function() {
		initBulkChallanBtn();
		
		$(document).on('click', '.BulkChallan', function() {
			if ($(this).attr('id') == "masterChSelect") {
				if ($(this).prop('checked') == true) {
					$(".bulkCh").show();
					$("input[name='dp_id[]']").prop('checked', true);
				} else {
					$(".bulkCh").hide();
					$("input[name='dp_id[]']").prop('checked', false);
				}
			} else {
				if ($("input[name='dp_id[]']").not(':checked').length != $("input[name='dp_id[]']").length) {
					$(".bulkCh").show();
					$("#masterChSelect").prop('checked', false);
				} else {
					$(".bulkCh").hide();
				}

				if ($("input[name='dp_id[]']:checked").length == $("input[name='dp_id[]']").length) {
					$("#masterChSelect").prop('checked', true);
					$(".bulkCh").show();
				}
				else{$("#masterChSelect").prop('checked', false);}
			}
		});
		
        $(document).on('click', '.bulkCh', function() {
			var dp_id = [];
			$("input[name='dp_id[]']:checked").each(function() {
				dp_id.push(this.value);
			});
			var ids = dp_id.join(",");
			
            var data ={postData:{ids:ids},call_function:'addChallan',modal_id:'bs-right-lg-modal', form_id : 'addChallan', title : 'Outsource Challan',fnsave:'save'}
			modalAction(data);
		});
	});
	function initBulkChallanBtn() {
		var bulkChBtn = '<button class="btn btn-outline-dark bulkCh" tabindex="0" aria-controls="outsourceTable" type="button"><span>Bulk Challan</span></button>';
		$("#outsourceTable_wrapper .dt-buttons").append(bulkChBtn);
		$(".bulkCh").hide();
	}
</script>