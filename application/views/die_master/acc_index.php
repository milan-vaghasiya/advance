<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button onclick="statusTab('dieEntryTable','0');" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Available</button>
                            </li>
                    
                            <li class="nav-item">
                                <button onclick="statusTab('dieEntryTable','4');" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Rejected</button>
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
                            <table id='dieEntryTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDieEntryDTRows/'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>