<?php $this->load->view('includes/header',['is_minFiles'=>1]); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>
<div class="page-content-tab">
	<div class="container-fluid sop">
		<div class="row">
			<div class="col-md-8">
			    <div class="crm-desk-right prcProcess" style="height:84vh;">
                    <div class="cd-header">
                        <h6 class="m-0 partyName">PROCESS DETAIL</h6>
                    </div>
                    <div class="sop-body" data-simplebar style="height:76vh;">
						<div class="col-md-3">
							<ul class="nav nav-tabs mb-1 nav-justified" id="cdFilter" role="tablist" style="border-bottom:0px;">
								<li class="nav-item" role="presentation">
									<a class="btn btn-outline-info btn-icon-circle btn-icon-circle-sm stageFilter active " data-move_type='1' data-bs-toggle="tab" href="javascript:void(0)" role="tab" datatip="Pending" flow="down" ><i class="fas fa-info "></i> </a>
									<span class="badge bg-info w-100">Regular</span>
								</li>
								<li class="nav-item" role="presentation">
									<a class="btn btn-outline-primary btn-icon-circle btn-icon-circle-sm stageFilter" data-move_type='2' data-bs-toggle="tab" href="javascript:void(0)" role="tab" datatip="In Progess" flow="down" ><i class="fas fa-cog"></i></a>
									<span class="badge bg-primary w-100">Rework</span>
								</li>
							</ul>
						</div>
						<div class="activity salesLog processDetail">
						    <!-- <img src="<?=base_url('assets/images/background/dnf_1.png')?>" style="width:100%;">
						    <h3 class="text-danger text-center font-24 fw-bold line-height-lg">Sorry!<br><span class="text-dark">Data Not Found</span></h3>
						    <div class="text-center text-muted font-16 fw-bold pt-3 pb-1">Please click any <strong>PRC</strong> to see Data</div> -->
						</div>
                    </div>
                </div>
			</div>
			<div class="col-md-4">
                <div class="crm-desk-right prcDetail" style="height:41vh;">
                    <div class="cd-header">
                        <h6 class="m-0 prc_number">PRC DETAIL</h6>
                    </div>
                    <div class="sop-body vh-35" data-simplebar>
					    <div>
					        <div class="text-center">
    					        <img src="<?=base_url('assets/images/background/dnf_2.png')?>" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
						    </div>
					    </div>
					</div>
                </div>
                <div class="crm-desk-right mt-3" style="height:41vh;">
                    <div class="cd-header" >
                        <h6 class="m-0 partyName">MATERIAL DETAIL</h6>
						<div class="cd-features" style="padding: 7px 0px;">
							<div class="dropdown d-inline-block">
								<?php $extMtParam = "{'postData':{'prc_id' : ".$prc_id."},'modal_id' : 'modal-large', 'call_function':'getExtraIssueMaterial','button':'close', 'title':'Extra Material'}"; ?>
								<button type="button" class="btn btn-primary btn-sm" onclick="loadform(<?=$extMtParam?>)"> Extra Material</button>
							</div>
						</div>
                    </div>
                    <div class="sop-body vh-35" data-simplebar>
						<div class="prcMaterial">
						    <div class="text-center">
    					        <img src="<?=base_url('assets/images/background/dnf_3.png')?>" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
						    </div>
    					</div>
                    </div>
                </div>
			</div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer',['is_minFiles'=>1]); ?>
<script src="<?=base_url()?>assets/js/custom/sop_desk.js?v=<?=time()?>"></script>

<script>
$(document).ready(function(){
    loadProcessDetail({prc_id:<?=$prc_id?>,'move_type':1});
	$(document).on('click','.stageFilter',function(){
		var move_type = $(this).data('move_type') || 1;
		loadProcessDetail({prc_id:<?=$prc_id?>,move_type:move_type});
	});
});

</script>