<div class="left-sidebar">
	<!-- LOGO -->
	<div class="brand bg-dark">
		<a href="javascript:void(0);" class="logo">
			<span>
				<img src="<?=base_url()?>assets/images/logo_text.png" alt="logo-large" class="logo-lg logo-light" style="height:50px;">
				<img src="<?=base_url()?>assets/images/logo_text.png" alt="logo-large" class="logo-lg logo-dark">
			</span>
		</a>
	</div>
	<div class="border-end">
		<ul class="nav nav-tabs menu-tab nav-justified" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-bs-toggle="tab" href="#Main" role="tab" aria-selected="true">N<span>avigation</span></a>
			</li>
		</ul>
	</div>
	<!-- Tab panes -->
	<!--end logo-->
	<div class="menu-content h-100" data-simplebar>
		<div class="menu-body navbar-vertical">
			<div class="collapse navbar-collapse tab-content" id="sidebarCollapse">
				<!-- Navigation -->
				<ul class="navbar-nav tab-pane active" id="Main" role="tabpanel">
					<li class="nav-item">
						<a class="nav-link" href="<?=base_url('admin/dashboard')?>"><i class="mdi mdi-desktop-mac-dashboard menu-icon"></i> <span>Dashboard</span></a>
					</li>	
					<li class="nav-item">
						<a class="nav-link" href="<?=base_url('admin/clientMaster')?>"><i class="menu-icon fas fa-address-book"></i> <span>Clients</span></a>
					</li>				
				</ul>
				<ul class="navbar-nav tab-pane" id="Extra" role="tabpanel">
					<li>
						<div class="update-msg text-center position-relative">
							<button type="button" class="btn-close position-absolute end-0 me-2" aria-label="Close"></button>
							<h5 class="mt-0">Nativebit</h5>
							<p class="mb-3">We Design and Develop Clean and High Quality ERP</p>
							<a href="javascript: void(0);" class="btn btn-outline-warning btn-sm">Upgrade your plan</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>