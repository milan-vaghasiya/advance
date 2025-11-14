<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Calibration - [<?= $itemData->item_name ?>]</h4>
                            </div>                           
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='calibrationDataTable' class="table table-bordered ssTable ssTable-cf" data-url='/getCalibrationData/<?= $itemData->id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>