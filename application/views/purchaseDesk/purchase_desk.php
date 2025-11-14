<?php $this->load->view('includes/header',['is_minFiles'=>1]); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.enqList{padding:0.4rem;}</style>
<div class="page-content-tab">
	<div class="container-fluid sop">
		<div class="row mt-2">
			<div class="col-md-4">
                <div class="crm-desk-left" id="purchaseBoard">
                    <ul class="nav nav-tabs mb-1 nav-justified" id="cdFilter" role="tablist" style="border-bottom:0px;">
                        <li class="nav-item" role="presentation">
                            <a class="btn btn-outline-info btn-icon-circle btn-icon-circle-sm stageFilter active" data-postdata='{"status":"1"}' data-bs-toggle="tab" href="javascript:void(0)" role="tab" datatip="Pending" flow="down" ><i class="fas fa-info "></i> </a>
                            <span class="badge bg-info w-100">Pending</span>
                        </li>
						<li class="nav-item" role="presentation">
                        	<a class="btn btn-outline-success btn-icon-circle btn-icon-circle-sm stageFilter" data-postdata='{"status":"2"}' data-bs-toggle="tab" href="javascript:void(0)" role="tab" datatip="Approve" flow="down" ><i class="fas fa-check"></i></a>
                            <span class="badge bg-success w-100">Approved</span>
                        </li>
						<li class="nav-item" role="presentation">
                            <a class="btn btn-outline-dark btn-icon-circle btn-icon-circle-sm stageFilter" data-postdata='{"status":"3"}' data-bs-toggle="tab" href="javascript:void(0)" role="tab" datatip="Reject" flow="down" ><i class="fas fa-exclamation-triangle"></i></a>
                            <span class="badge bg-dark w-100">Rejected</span>
                        </li>
                    </ul>
					<div class="cd-search mb-1">
						<div class="input-group">
							<input type="text" id="cd-search" name="cd-search" class="form-control quicksearch" placeholder="Search Here...">
							
							<?php
								$addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addEnquiry', 'form_id' : 'addEnquiry', 'title' : 'New Enquiry', 'js_store_fn' : 'storeEnquiry', 'fnsave' : 'saveEnquiry', 'txt_editor' : 'item_remark'}";
							?>
							<button type="button" class="btn btn-info permission-write press-add-btn" datatip="New Enquiry" onclick="loadform(<?=$addParam?>);" flow="down"><i class="fa fa-plus"></i></button>
							<?php
							$compareParam = "{'modal_id' : 'modal-xl', 'call_function':'quotationComparison', 'form_id' : 'quotationComparison', 'title' : 'Quotation Comparison', 'button' : 'close'}";
							?>
							<button type="button" class="btn btn-primary permission-write press-add-btn" datatip="Quotation Compare" flow="down"  onclick="loadform(<?=$compareParam?>);"><i class="fas fa-exchange-alt"></i></button>
						</div>
					</div>
					<div class="cd-body-left" data-simplebar style="height:70vh;">
						<div class="cd-list">
							<div class="grid enqList"></div>
						</div>
					</div>
                </div>
			</div>
			<div class="col-md-4">
                <div class="crm-desk-right enqDetail" style="height:41vh;">
                    <div class="cd-header">
                        <h6 class="m-0 prc_number">ENQUIRY DETAIL</h6>
                    </div>
                    <div class="sop-body vh-35" data-simplebar>
					    <div>
					        <div class="text-center">
    					        <img src="<?=base_url('assets/images/background/dnf_3.png')?>" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>Enquiry</strong> to see Data</div>
						    </div>
					    </div>
					</div>
                </div>
                <div class="crm-desk-right mt-3 quoteDetail" style="height:41vh;">
                    <div class="cd-header">
                        <h6 class="m-0 partyName">QUOTATION DETAIL</h6>
                    </div>
                    <div class="sop-body vh-35" data-simplebar>
						<div class="prcMaterial">
						    <div class="text-center">
    					        <img src="<?=base_url('assets/images/background/dnf_3.png')?>" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>Enquiry</strong> to see Data</div>
						    </div>
    					</div>
                    </div>
                </div>
			</div>
			<div class="col-md-4">
                <div class="crm-desk-right">
					<!-- 13-05-2024 -->
                    <div class="cd-header">
						<span class="m-0 fs-14">ITEM HISTORY</span>
                    </div>
                    <div class="sop-body" data-simplebar style="height:70vh;">
					    <div class="itemDetail">
					        <div class="text-center">
    					        <img src="<?=base_url('assets/images/background/dnf_2.png')?>" style="width:100%;">
    						    <div class="text-center text-muted font-16 fw-bold">Pleasae click any <strong>ENQUIRY</strong> to see Data</div>
						    </div>
					    </div>
					</div>
                </div>
			</div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer',['is_minFiles'=>1]); ?>
<script src="<?=base_url()?>assets/js/custom/purchase_desk.js?v=<?=time()?>"></script>

<script>
$(document).ready(function(){
    $(document).on('click','.enqNumber',function() {
        var id = $(this).data('id');
		loadItemDetail({enq_id:id});
    });
});

</script>