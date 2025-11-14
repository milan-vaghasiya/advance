<?php $this->load->view('includes/header'); ?>
	
<div class="page-content-tab">
    <div class="container-fluid" style="padding:0px 10px;">
        
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
                                        <h4 class="card-title">Income VS Expenses</h4>                      
                                    </div>
                                </div>                        
                            </div>
                            <div class="card-body">
                                <div class="">
                                    <div id="invoice_chart" class="apex-charts"></div>
                                </div> 
                            </div>
                        </div>
                        
                    </div>
                </div> 
            </div><!--end col-->                        
        </div><!--end row-->

    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<!-- Javascript  -->   
<script src="<?=base_url()?>assets/plugins/chartjs/chart.js"></script>
<script src="<?=base_url()?>assets/plugins/lightpicker/litepicker.js"></script>
<script src="<?=base_url()?>assets/plugins/apexcharts/apexcharts.min.js"></script>
<script src="<?=base_url()?>assets/pages/analytics-index.init.js"></script>

<script>
$(document).ready(function(){
    var options = {
        series: [{
            type: 'column',
			name: 'Income',
            data: [
                <?=!empty($invoiceData->si4)?$invoiceData->si4:0?>,
                <?=!empty($invoiceData->si5)?$invoiceData->si5:0?>,
                <?=!empty($invoiceData->si6)?$invoiceData->si6:0?>,
                <?=!empty($invoiceData->si7)?$invoiceData->si7:0?>,
                <?=!empty($invoiceData->si8)?$invoiceData->si8:0?>,
                <?=!empty($invoiceData->si9)?$invoiceData->si9:0?>,
                <?=!empty($invoiceData->si10)?$invoiceData->si10:0?>,
                <?=!empty($invoiceData->si11)?$invoiceData->si11:0?>,
                <?=!empty($invoiceData->si12)?$invoiceData->si12:0?>,
                <?=!empty($invoiceData->si1)?$invoiceData->si1:0?>,
                <?=!empty($invoiceData->si2)?$invoiceData->si2:0?>,
                <?=!empty($invoiceData->si3)?$invoiceData->si3:0?>
            ]
        }, 
        {
            type: 'line',
			name: 'Expenses',
            data: [
                <?=!empty($invoiceData->pi4)?$invoiceData->pi4:0?>,
                <?=!empty($invoiceData->pi5)?$invoiceData->pi5:0?>,
                <?=!empty($invoiceData->pi6)?$invoiceData->pi6:0?>,
                <?=!empty($invoiceData->pi7)?$invoiceData->pi7:0?>,
                <?=!empty($invoiceData->pi8)?$invoiceData->pi8:0?>,
                <?=!empty($invoiceData->pi9)?$invoiceData->pi9:0?>,
                <?=!empty($invoiceData->pi10)?$invoiceData->pi10:0?>,
                <?=!empty($invoiceData->pi11)?$invoiceData->pi11:0?>,
                <?=!empty($invoiceData->pi12)?$invoiceData->pi12:0?>,
                <?=!empty($invoiceData->pi1)?$invoiceData->pi1:0?>,
                <?=!empty($invoiceData->pi2)?$invoiceData->pi2:0?>,
                <?=!empty($invoiceData->pi3)?$invoiceData->pi3:0?>
            ]
        }],
        chart: {
            height: 260,
            type: 'line',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '30%',
            },
        },
        stroke: {
            width: [0, 2],
        },

        dataLabels: {
            enabled: true,
            enabledOnSeries: [1],
            style: {
                colors: ['rgba(255, 255, 255, .6)'],
            },
            background: {
                enabled: true,
                foreColor: '#b2bdcc',
                padding: 4,
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#b2bdcc',
                opacity: 0.9,
            },
        },
        colors: ["#a4b1c3", "#6f7b8b"],
        xaxis: {
            categories: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        },
        grid: {
            row: {
                colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.2,           
            },
            strokeDashArray: 2.5,
        },
    };

    var chartMain = new ApexCharts(document.querySelector("#invoice_chart"), options);
    chartMain.render();
});
</script>