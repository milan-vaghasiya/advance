<!DOCTYPE html>
<html lang="en">
<head>
		
    <!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
	<meta name="theme-color" content="#92E136">
	<meta name="author" content="DexignZone"> 
	<meta name="robots" content="index, follow"> 
    <meta name="keywords" content="android, ios, mobile, application template, progressive web app, ui kit, multiple color, dark layout">
	<meta name="description" content="W3Grocery - Complete solution of some popular application like - grocery app, shop vendor app, driver app and progressive web app">
	<meta property="og:title" content="W3Grocery: Pre-Build Grocery Mobile App Template ( Bootstrap 5 + PWA )">
	<meta property="og:description" content="W3Grocery - Complete solution of some popular application like - grocery app, shop vendor app, driver app and progressive web app">
	<meta property="og:image" content="https://w3grocery.dexignzone.com/xhtml/social-image.png">
	<meta name="format-detection" content="telephone=no">
	
	<!-- Favicons Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?=base_url('assets/app/images/favicon.png')?>">
    
    <!-- Title -->
	<title>NBT CRM</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/app/css/style.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/app/css/jp_helper.css')?>">
    

</head>   
<body data-theme-color="color-teal" class="login_page">
<div class="page-wraper">
    
	<!-- Preloader -->
	<div id="preloader">
		<div class="loader">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
	</div>
    <!-- Preloader end-->

    <!-- Page Content -->
    <div class="page-content">
        <!-- Banner -->
        <div class="banner-wrapper mb-0 py-2">
            <div class="container inner-wrapper">
                <img src="<?=base_url('assets/app/images/logo.png')?>" style="width:50%" alt="/">
            </div>
        </div>
		<div class="text-center bg-warning text-dark fw-bold p-1">JUST AUTOMIZING ROUTINE ACTIVITIES</div>
        <!-- Banner End -->
		<div class="container">
			<div class="card dz-form-group login_box">
				<div class="card-header d-block border-0 text-center">
					<h2 class="title mb-0">Please login to your account</h2>
				</div>
				<div class="card-body">
					<form id="loginform" action="<?=base_url('app/login/auth');?>" method="POST">
						<div class="mb-3 input-group input-mini">
							<span class="input-group-text"><i class="fa fa-user"></i></span>
							<input type="text" class="form-control" placeholder="User ID" id="user_name" name="user_name">
						</div>
						<!--
						<div class="mb-3 input-group input-group-icon">
							<div class="input-group-text">
								<div class="input-icon">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.208 5.35425C23.1103 4.7009 22.7817 4.10419 22.2818 3.67229C21.7819 3.24039 21.1439 3.00188 20.4832 3H3.51674C2.85613 3.00188 2.21805 3.24039 1.71818 3.67229C1.2183 4.10419 0.889719 4.7009 0.791992 5.35425L12 12.6068L23.208 5.35425Z" fill="#7D8FAB"></path>
										<path d="M12.4072 14.13C12.2859 14.2085 12.1445 14.2502 12 14.2502C11.8555 14.2502 11.7141 14.2085 11.5927 14.13L0.75 7.1145V18.2332C0.750794 18.9668 1.04255 19.6701 1.56124 20.1887C2.07993 20.7074 2.78321 20.9992 3.51675 21H20.4833C21.2168 20.9992 21.9201 20.7074 22.4388 20.1887C22.9575 19.6701 23.2492 18.9668 23.25 18.2332V7.11375L12.4072 14.13Z" fill="#7D8FAB"></path>
									</svg>
								</div>
							</div>
							<input type="text" class="form-control" placeholder="User Name" id="user_name" name="user_name">
							
						</div>-->
						<?=form_error('user_name')?>
						<div class="mb-3 input-group input-mini">
							<span class="input-group-text"><i class="fa fa-lock"></i></span>
							<input type="password" class="form-control dz-password" placeholder="Password"  id="password" name="password">
							<span class="input-group-text show-pass"> 
								<i class="fa fa-eye-slash"></i>
								<i class="fa fa-eye"></i>
							</span>
						</div>
						<!--
						<div class="mb-3 input-group input-group-icon">
							<div class="input-group-text">
								<div class="input-icon">
									<svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M13 6H12V3C12 2.20435 11.6839 1.44129 11.1213 0.87868C10.5587 0.316071 9.79565 0 9 0H5C4.20435 0 3.44129 0.316071 2.87868 0.87868C2.31607 1.44129 2 2.20435 2 3V6H1C0.734784 6 0.48043 6.10536 0.292893 6.29289C0.105357 6.48043 0 6.73478 0 7V15C0 16.3261 0.526784 17.5979 1.46447 18.5355C2.40215 19.4732 3.67392 20 5 20H9C10.3261 20 11.5979 19.4732 12.5355 18.5355C13.4732 17.5979 14 16.3261 14 15V7C14 6.73478 13.8946 6.48043 13.7071 6.29289C13.5196 6.10536 13.2652 6 13 6ZM4 3C4 2.73478 4.10536 2.48043 4.29289 2.29289C4.48043 2.10536 4.73478 2 5 2H9C9.26522 2 9.51957 2.10536 9.70711 2.29289C9.89464 2.48043 10 2.73478 10 3V6H4V3ZM8 13.72V15C8 15.2652 7.89464 15.5196 7.70711 15.7071C7.51957 15.8946 7.26522 16 7 16C6.73478 16 6.48043 15.8946 6.29289 15.7071C6.10536 15.5196 6 15.2652 6 15V13.72C5.69772 13.5455 5.44638 13.2949 5.27095 12.9932C5.09552 12.6914 5.00211 12.349 5 12C5 11.4696 5.21071 10.9609 5.58579 10.5858C5.96086 10.2107 6.46957 10 7 10C7.53043 10 8.03914 10.2107 8.41421 10.5858C8.78929 10.9609 9 11.4696 9 12C8.99789 12.349 8.90448 12.6914 8.72905 12.9932C8.55362 13.2949 8.30228 13.5455 8 13.72Z" fill="#7D8FAB"/>
									</svg>
								</div>
							</div>
							<input type="password" class="form-control dz-password" placeholder="Password"  id="password" name="password">
							
							<span class="input-group-text show-pass"> 
								<i class="fa fa-eye-slash text-primary"></i>
								<i class="fa fa-eye text-primary"></i>
							</span>
							
						</div>-->
						<?=form_error('password')?>
						<div class="input-group">
							<button type="submit" class="btn mt-2 btn-warning w-100">SIGN IN</a>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>
    <!-- Page Content End -->
    
</div>
<!--**********************************
    Scripts
***********************************-->
<script src="<?=base_url('assets/app/js/jquery.js')?>"></script>
<script src="<?=base_url('assets/app/vendor/bootstrap/js/bootstrap.bundle.min.js')?>"></script>
<script src="<?=base_url('assets/app/vendor/swiper/swiper-bundle.min.js')?>"></script><!-- Swiper -->
<script src="<?=base_url('assets/app/js/dz.carousel.js')?>"></script><!-- Swiper -->
<script src="<?=base_url('assets/app/js/settings.js')?>"></script>
<script src="<?=base_url('assets/app/js/custom.js')?>"></script>
</body>
</html>