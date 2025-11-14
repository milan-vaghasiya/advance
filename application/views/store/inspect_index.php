<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> <button onclick="statusTab('requisitionTable',1);" class="btn waves-effect waves-light   btn-outline-info active mr-1" data-bs-toggle="tab" aria-expanded="false">Pending</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('requisitionTable',2);" class="btn waves-effect waves-light btn-outline-info mr-1" data-bs-toggle="tab" aria-expanded="false">Complete</button> </li>
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
                            <table id='requisitionTable' class="table table-bordered ssTable ssTable-cf" data-url='/getInspDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>