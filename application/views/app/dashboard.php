<?php $this->load->view('app/includes/header'); ?>
<?php $this->load->view('app/includes/topbar'); ?>
	<!-- Page Content -->
    <div class="page-content bottom-content">
        <div class="container">
        	<!-- OUR SERVICE -->
			<!-- <div class="service-area mb-4">
				<div class="service-box">
					<div class="dz-icon mx-auto mb-2">
						<svg clip-rule="evenodd" fill-rule="evenodd" height="24" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 32 32" width="24" xmlns="http://www.w3.org/2000/svg"><g id="Icon"><path d="m23 16c-3.863 0-7 3.137-7 7s3.137 7 7 7 7-3.137 7-7-3.137-7-7-7zm-19 10h9c.552 0 1-.448 1-1s-.448-1-1-1h-9v-19c0-.552.448-1 1-1h6v8c0 .347.179.668.474.851.295.182.663.198.973.043l3.553-1.776s3.553 1.776 3.553 1.776c.31.155.678.139.973-.043.295-.183.474-.504.474-.851v-8h6c.552 0 1 .448 1 1v10c0 .552.448 1 1 1s1-.448 1-1v-10c0-1.656-1.344-3-3-3h-22c-1.656 0-3 1.344-3 3v22c0 1.656 1.344 3 3 3h10c.552 0 1-.448 1-1s-.448-1-1-1h-10c-.552 0-1-.448-1-1zm19-8c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm-3.207 5.707 2 2c.39.391 1.024.391 1.414 0l3-3c.39-.39.39-1.024 0-1.414s-1.024-.39-1.414 0l-2.293 2.293s-1.293-1.293-1.293-1.293c-.39-.39-1.024-.39-1.414 0s-.39 1.024 0 1.414zm-.793-19.707h-6v6.382l2.553-1.276c.281-.141.613-.141.894 0 0 0 2.553 1.276 2.553 1.276z"/></g></svg>
						
					</div>
					<span class="font-14 d-block mb-2">New Leads</span>
					<h5 class="mb-0"></h5>
				</div>
				<div class="service-box">
					<div class="dz-icon mx-auto mb-2">
						<svg enable-background="new 0 0 100 100" height="24" viewBox="0 0 100 100" width="24" xmlns="http://www.w3.org/2000/svg"><path id="Product_Return" d="m98 50c0 26.467-21.533 48-48 48s-48-21.533-48-48c0-1.658 1.342-3 3-3s3 1.342 3 3c0 23.159 18.841 42 42 42s42-18.841 42-42-18.841-42-42-42c-11.163 0-21.526 4.339-29.322 12h11.322c1.658 0 3 1.342 3 3s-1.342 3-3 3h-18c-1.658 0-3-1.342-3-3v-18c0-1.658 1.342-3 3-3s3 1.342 3 3v10.234c8.851-8.448 20.481-13.234 33-13.234 26.467 0 48 21.533 48 48zm-21-12v27c0 1.251-.776 2.37-1.945 2.81l-24 9c-.34.126-.698.19-1.055.19s-.715-.064-1.055-.19l-24-9c-1.169-.44-1.945-1.559-1.945-2.81v-27c0-1.251.776-2.37 1.945-2.81l24-9c.68-.252 1.43-.252 2.109 0l24 9c1.17.44 1.946 1.559 1.946 2.81zm-42.457 0 15.457 5.795 15.457-5.795-15.457-5.795zm-5.543 24.92 18 6.75v-20.59l-18-6.75zm42 0v-20.59l-18 6.75v20.59z"/></svg>
					</div>
					<span class="font-14 d-block mb-2">Orders</span>
					<h5 class="mb-0"></h5>
				</div>
				<div class="service-box" onclick="window.location.href=''">
					<div class="dz-icon mx-auto mb-2">
						<svg height="24" viewBox="0 0 16 16" width="24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 2"><path d="m14 .5h-12a1.5017 1.5017 0 0 0 -1.5 1.5v1a1.4977 1.4977 0 0 0 1 1.4079v7.5921a1.5017 1.5017 0 0 0 1.5 1.5h4.2618a4.4891 4.4891 0 1 0 7.2382-5.2935v-3.7986a1.4977 1.4977 0 0 0 1-1.4079v-1a1.5017 1.5017 0 0 0 -1.5-1.5zm-11 12a.501.501 0 0 1 -.5-.5v-7.5h11v2.7618a4.4725 4.4725 0 0 0 -6.7236 5.2382zm8 2a3.5 3.5 0 1 1 3.5-3.5 3.5042 3.5042 0 0 1 -3.5 3.5zm3.5-11.5a.501.501 0 0 1 -.5.5h-12a.501.501 0 0 1 -.5-.5v-1a.501.501 0 0 1 .5-.5h12a.501.501 0 0 1 .5.5z"/><path d="m11.5 10.793v-1.793a.5.5 0 0 0 -1 0v2a.4993.4993 0 0 0 .1465.3535l1 1a.5.5 0 0 0 .707-.707z"/></svg>
						
					</div>
					<span class="font-14 d-block mb-2">Appointments</span>
					<h5 class="mb-0"><?=!empty($logCount->reminder)?$logCount->reminder:0?></h5>
				</div>
				<div class="service-box">
					<div class="dz-icon mx-auto mb-2">
						<svg enable-background="new 0 0 100 100" height="24" viewBox="0 0 100 100" width="24" xmlns="http://www.w3.org/2000/svg"><path id="Not_Delivered" d="m92 54.066v-19.066c0-.545-.146-1.078-.428-1.544l-16.251-27.091c-1.62-2.692-4.579-4.365-7.719-4.365h-41.204c-3.141 0-6.1 1.673-7.72 4.368l-16.25 27.088c-.282.466-.428.999-.428 1.544v48c0 4.963 4.037 9 9 9h43.066c4.636 3.745 10.524 6 16.934 6 14.889 0 27-12.111 27-27 0-6.41-2.255-12.298-6-16.934zm-42-46.066h17.602c1.049 0 2.036.56 2.575 1.456l13.524 22.544h-33.701zm-26.177 1.459c.539-.899 1.527-1.459 2.575-1.459h17.602v24h-33.701zm-12.823 76.541c-1.655 0-3-1.345-3-3v-45h78v10.565c-4.293-2.88-9.453-4.565-15-4.565-14.889 0-27 12.111-27 27 0 5.547 1.685 10.707 4.565 15zm60 6c-11.578 0-21-9.422-21-21s9.422-21 21-21 21 9.422 21 21-9.422 21-21 21zm-36-42c0 1.658-1.342 3-3 3h-12c-1.658 0-3-1.342-3-3s1.342-3 3-3h12c1.658 0 3 1.342 3 3zm47.121 14.121-6.879 6.879 6.879 6.879c1.172 1.172 1.172 3.07 0 4.242-.586.586-1.353.879-2.121.879s-1.535-.293-2.121-.879l-6.879-6.879-6.879 6.879c-.586.586-1.353.879-2.121.879s-1.535-.293-2.121-.879c-1.172-1.172-1.172-3.07 0-4.242l6.879-6.879-6.879-6.879c-1.172-1.172-1.172-3.07 0-4.242s3.07-1.172 4.242 0l6.879 6.879 6.879-6.879c1.172-1.172 3.07-1.172 4.242 0s1.172 3.07 0 4.242z"/></svg>
					</div>
					<span class="font-14 d-block mb-2">Lost</span>
					<h5 class="mb-0"><?=!empty($logCount->lost_lead)?$logCount->lost_lead:0?></h5>
				</div>
				
			</div>
			
			<div class="card  light   bg-primary">
				<div class="card-body">
					<?php
					
					?>
					<div class="d-flex align-items-center mb-0">
						<div class="top-area">
							<h3 class="quantity"><?=$leadAcheive.'/'.$total_new_lead?></h3>
							<p class="mb-0">Leads Target</p>
						</div>
						<div class="icon-box-2 ms-auto badge-outline-primary">
							<svg height="24" viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m433.798 106.268-96.423-91.222c-10.256-9.703-23.68-15.046-37.798-15.046h-183.577c-30.327 0-55 24.673-55 55v402c0 30.327 24.673 55 55 55h280c30.327 0 55-24.673 55-55v-310.778c0-15.049-6.27-29.612-17.202-39.954zm-29.137 13.732h-74.661c-2.757 0-5-2.243-5-5v-70.364zm-8.661 362h-280c-13.785 0-25-11.215-25-25v-402c0-13.785 11.215-25 25-25h179v85c0 19.299 15.701 35 35 35h91v307c0 13.785-11.215 25-25 25z"></path><path d="m363 200h-220c-8.284 0-15 6.716-15 15s6.716 15 15 15h220c8.284 0 15-6.716 15-15s-6.716-15-15-15z"></path><path d="m363 280h-220c-8.284 0-15 6.716-15 15s6.716 15 15 15h220c8.284 0 15-6.716 15-15s-6.716-15-15-15z"></path><path d="m215.72 360h-72.72c-8.284 0-15 6.716-15 15s6.716 15 15 15h72.72c8.284 0 15-6.716 15-15s-6.716-15-15-15z"></path></svg>
						</div>
					</div>
					<div class="bottom-area">
						<h6 class="review-title text-end justify-content-end"><?=$leadRatio?>%</h6>
						<div class="progress">
							<div class="progress-bar progress-animated progress-bar-striped border-0" style="width: <?=$leadRatio?>%;" role="progressbar">
								<span class="sr-only"><?=$leadRatio?>% Complete</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card  light badge-warning">
				<div class="card-body">
					<div class="d-flex align-items-center mb-0">
						<div class="top-area">
							<h3 class="quantity"></h3>
							<p class="mb-0">Business Target</p>
						</div>
						<div class="icon-box-2 ms-auto badge-outline-warning ">
							<i class="fa-solid fa-indian-rupee-sign"></i>
						</div>
					</div>
					<div class="bottom-area">
						<h6 class="review-title text-end justify-content-end"><?=$salesRatio?>%</h6>
						<div class="progress">
							<div class="progress-bar progress-animated progress-bar-striped border-0 warning" style="width: <?=$salesRatio?>%;" role="progressbar">
								<span class="sr-only"><?=$salesRatio?>% Complete</span>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			
		</div>
    </div>    
    <!-- Page Content End-->

<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>

<script src="<?=base_url('assets/app/index.js')?>"></script>


