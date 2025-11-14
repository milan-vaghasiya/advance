<?php $this->load->view('admin/includes/header'); ?>
	
<div class="page-content-tab">
    <div class="container-fluid" style="padding:0px 10px;">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Nativebit</a>
                            </li><!--end nav-item-->
                            <li class="breadcrumb-item"><a href="#">Project</a>
                            </li><!--end nav-item-->
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Dashboard</h4>
                </div><!--end page-title-box-->
            </div><!--end col-->
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="pt-3">
                            <h3 class="text-dark text-center font-30 fw-bold line-height-lg">Nativebit <br>Technologies</h3>
                            <div class="text-center text-muted font-16 fw-bold pt-3 pb-1">Revolutionize The Way You Work</div>
                            
                            <div class="text-center py-3 mb-4">
                                <a href="#" class="btn btn-primary">Experince The Excellance</a>
                            </div>
                            <img src="<?=base_url()?>assets/images/small/business.png" alt="" class="img-fluid px-3 mb-2">
                        </div>
                    </div><!--end card-body--> 
                </div><!--end card-->                            
            </div> <!--end col-->
            <div class="col-lg-9">
                <div class="row justify-content-center"> 
                    <div class="col-lg-3">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row d-flex">
                                    <div class="col-3">
                                        <i class="ti ti-users font-36 align-self-center text-dark"></i>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <div id="dash_spark_1" class="mb-3"></div>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <h3 class="text-dark my-0 font-22 fw-bold">24000</h3>
                                        <p class="text-muted mb-0 fw-semibold">Sessions</p>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-body--> 
                        </div><!--end card-->                                     
                    </div> <!--end col--> 
                    <div class="col-lg-3">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row d-flex">
                                    <div class="col-3">
                                        <i class="ti ti-clock font-36 align-self-center text-dark"></i>
                                    </div><!--end col-->
                                    <div class="col-auto ms-auto align-self-center">
                                        <span class="badge badge-soft-success px-2 py-1 font-11">Active</span>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <div id="dash_spark_2" class="mb-3"></div>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <h3 class="text-dark my-0 font-22 fw-bold">00:18</h3>
                                        <p class="text-muted mb-0 fw-semibold">Avg.Sessions</p>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-body--> 
                        </div><!--end card-->                                     
                    </div> <!--end col--> 
                    <div class="col-lg-3">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row d-flex">
                                    <div class="col-3">
                                        <i class="ti ti-activity font-36 align-self-center text-dark"></i>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <div id="dash_spark_3" class="mb-3"></div>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <h3 class="text-dark my-0 font-22 fw-bold">&#8377; 2400</h3>
                                        <p class="text-muted mb-0 fw-semibold">Bounce Rate</p>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-body--> 
                        </div><!--end card-->                                     
                    </div> <!--end col--> 
                    
                    <div class="col-lg-3">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="row d-flex">
                                    <div class="col-3">
                                        <i class="ti ti-confetti font-36 align-self-center text-dark"></i>
                                    </div><!--end col-->
                                    <div class="col-auto ms-auto align-self-center">
                                        <span class="badge badge-soft-danger px-2 py-1 font-11">-2%</span>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <div id="dash_spark_4" class="mb-3"></div>
                                    </div><!--end col-->
                                    <div class="col-12 ms-auto align-self-center">
                                        <h3 class="text-dark my-0 font-22 fw-bold">85000</h3>
                                        <p class="text-muted mb-0 fw-semibold">Goal Completions</p>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-body--> 
                        </div><!--end card-->                                     
                    </div> <!--end col-->                                                                   
                </div><!--end row-->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">                      
                                        <h4 class="card-title">Audience Overview</h4>                      
                                    </div><!--end col-->
                                    <div class="col-auto"> 
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                This Year<i class="las la-angle-down ms-1"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                                <a class="dropdown-item" href="#">Last Month</a>
                                                <a class="dropdown-item" href="#">This Year</a>
                                            </div>
                                        </div>               
                                    </div><!--end col-->
                                </div>  <!--end row-->                                  
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="">
                                    <div id="ana_dash_1" class="apex-charts"></div>
                                </div> 
                            </div><!--end card-body--> 
                        </div><!--end card-->
                    </div>
                </div> 
            </div><!--end col-->                        
        </div><!--end row-->

    </div>
</div>

<?php $this->load->view('admin/includes/footer'); ?>

<!-- Javascript  -->   
<script src="<?=base_url()?>assets/plugins/chartjs/chart.js"></script>
<script src="<?=base_url()?>assets/plugins/lightpicker/litepicker.js"></script>
<script src="<?=base_url()?>assets/plugins/apexcharts/apexcharts.min.js"></script>
<script src="<?=base_url()?>assets/pages/analytics-index.init.js"></script>