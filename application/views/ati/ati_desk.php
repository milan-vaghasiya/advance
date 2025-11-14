<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url()?>assets/images/favicon.png">
    <title>NATIVEBIT TECHNOLOGIES</title>
    
	<link href="<?=base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?=base_url();?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
	
	<!-- Custom CSS -->
    <link href="<?=base_url()?>assets/css/jp_helper.css" rel="stylesheet">
</head>

<body class="bg-white">
	<section class="swiper-wrapper">
		<div class="menu-swiper">
			<div class="menu-item">
				<a href="#" class="active">Home</a>
				<a href="#">Contact</a>
				<a href="#">Courses</a>
				<a href="#">About</a>
				<a href="#">Service</a>
				<a href="#">Events</a>
				<a href="#">Courses</a>
				<a href="#">FAQ</a>
				<a href="#">Home</a>
				<a href="#">Contact</a>
				<a href="#">Courses</a>
				<a href="#">About</a>
				<a href="#">Service</a>
				<a href="#">Events</a>
				<a href="#">Courses</a>
				<a href="#">FAQ</a>
			</div> 
			<span class="pointer left-pointer dis"><i class="fa fa-chevron-left"></i></span>
			<span class="pointer right-pointer"><i class="fa fa-chevron-right"></i></span>
		</div>
	</section>
	<div class="container-fluid p-0">
		<div class="card mb-4">
			<header class="card-header">
				
			</header>
			<div class="card-body">
				<div class="row gx-3">
					<?php
						$productList = '';$img=1;$insNm = ['Kyocera','Koerloy','Taegutec','Widia','Sandvik','YG-1','Alumina','Mitsubishi','Deskar','kennametal','Carboloy','Korloy','Kyocera','Koerloy','Taegutec','Widia','Sandvik','YG-1','Alumina','Mitsubishi','Deskar','kennametal','Carboloy','Korloy'];
						for($i=0;$i<24;$i++)
						{
							$imgPath = base_url('assets/uploads/inserts/'.$img.'.png');
							$productList .= '<div class="col-xl-2 col-lg-3 col-md-4 col-6">
												<figure class="card sh-perfect">
													<div class="card-header bg-white text-center">
														<img style="height:100px!important;width:100px!important;" src="'.$imgPath.'" class="img-fluid" alt="Logo">
													</div>
													<figcaption class="card-body text-center">
														<h6 class="card-title m-0">'.$insNm[$i].'</h6>
													</figcaption>
												</figure>
											</div>';
							if($img == 6){$img=1;}else{$img++;}
						}
						echo $productList;
					?>
				</div>
			</div>
		</div>
    </div>		

    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
	<script src="<?=base_url()?>assets/js/jquery/dist/jquery.min.js"></script>
	<script src="<?=base_url()?>assets/js/app.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $(document).ready(function() {
			
        });
		
		var lp, rp, mItems, menu, sc, pos;
		lp = $(".left-pointer");
		rp = $(".right-pointer");
		mItems = $(".menu-item");

		lp.click(function(){
			sc = mItems.width() - 60;
		  pos = mItems.scrollLeft() - sc;
		  mItems.animate({'scrollLeft': pos}, 'slow');
		});
		rp.click(function(){
		  sc = mItems.width() - 60;
		  pos = mItems.scrollLeft() + sc;
		  mItems.animate({'scrollLeft': pos}, 'slow');
		});
		var scrollLeftPrev = 0; 
		mItems.scroll(function(){
		  var newScrollLeft = mItems.scrollLeft(),width=mItems.width(),
					scrollWidth=mItems.get(0).scrollWidth;
		  var offset=8;
		  console.log(scrollWidth - newScrollLeft - width);
		  if (scrollWidth - newScrollLeft - width < offset) {
					console.log('right end');
			$(".right-pointer").addClass("dis");
		  }else{
			$(".right-pointer").removeClass("dis");
		  }
		  if( $(this).scrollLeft() == 0){
			$(".left-pointer").addClass("dis");
		  }else{
			$(".left-pointer").removeClass("dis");
		  }
		  scrollLeftPrev = newScrollLeft;
		});

		const slider1 = document.querySelector('.menu-item');
		let isDown = false;
		let startX;
		let scrollLeft;
		slider1.addEventListener('mousedown', (e) => {
		  isDown = true;
		  slider1.classList.add('active');
		  startX = e.pageX - slider1.offsetLeft;
		  scrollLeft = slider1.scrollLeft;
		});
		slider1.addEventListener('mouseleave', () => {
		  isDown = false;
		  slider1.classList.remove('active');
		});
		slider1.addEventListener('mouseup', () => {
		  isDown = false;
		  slider1.classList.remove('active');
		});
		slider1.addEventListener('mousemove', (e) => {
		  if(!isDown) return;
		  e.preventDefault();
		  const x = e.pageX - slider1.offsetLeft;
		  const walk = (x - startX) * 3; //scroll-fast
		  slider1.scrollLeft = scrollLeft - walk;
		});
        
    </script>
</body>

</html>