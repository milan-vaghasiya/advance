<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <?php
            $rejTypeList = ['MFG'=>'Manufacturing','GRN'=>'GRN','Manual'=>'Manual'];
            foreach($rejTypeList as $key=>$value){
                ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card report-card sh-perfect">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col">
                                    <div class="bot_style">
                                        <h2 class="heading"><?=$value?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
               
                        <div class="stage-footer">
                            <a role="button" href="<?=base_url($headData->controller.'/pendingReviewIndex/'.$key)?>" target="_blank" class="stage-btn stk-btn m-0 p-3" datatip="Pending Review" flow="down">
                                <i class="fas fa-cog fs-13"></i> <span class="lable">Pending Review</span>
                            </a>
                            <a role="button" href="<?=base_url($headData->controller.'/reviewedIndex/'.$key)?>" target="_blank" class="stage-btn mfg-btn m-0 p-3" datatip="Reviewed" flow="down">
                                <i class="fas fa-check fs-13"></i> <span class="lable">Reviewed</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
