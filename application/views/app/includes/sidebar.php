<!-- Sidebar -->
<div class="dark-overlay"></div>
	<div class="sidebar style-2">
		<a href="javascript:void(0)" class="side-menu-logo">
			<img src="<?=base_url("assets/app/images/logo_text.png")?>" alt="logo" style="width:60px;">
		</a>
		<ul class="nav navbar-nav" data-simplebar>	
			<li class="nav-label">Main Menu</li>
			<li>
				<a class="nav-link" href="<?=base_url("app/dashboard")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/dashboard')?'active':''?>">
					<span class="dz-icon">
						<i class="fa-solid fa-house"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Home</span>
				</a>
			</li>
			<?=$this->permission->getEmployeeAppMenus()?>
			<li>
				<a class="nav-link" href="<?=base_url('app/login/logout')?>">
					<span class="dz-icon">
						<i class="fas fa-sign-out-alt"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Logout</span>
				</a>
			</li>
		</ul>
    </div>
</div>
<!-- Sidebar End -->