<div class="menubar-area style-5 footer-fixed">
	<div class="toolbar-inner menubar-nav">
		<a href="<?=base_url("app/dashboard")?>" data-page_url="app/dashboard" class="nav-link ">
			<div class="shape">
				<i class="fa-solid fa-house"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Home</span>
		</a>
		<?=$this->permission->getEmployeeAppMenus(1)?>
		
	</div>
</div>