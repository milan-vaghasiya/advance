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
    <title>Login - <?=(!empty(SITENAME))?SITENAME:""?></title>
    
	<link href="<?=base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?=base_url();?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
	
	<!-- Custom CSS -->
    <link href="<?=base_url()?>assets/css/jp_helper.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/login.css" rel="stylesheet">
</head>

<body>
	<div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xxl-3 col-lg-4 col-md-5">
                    <div class="auth-full-page-content d-flex p-sm-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-3 mb-md-3 text-center">
                                    <a href="javascript:void(0);" class="d-block auth-logo">
                                        <img src="<?=base_url()?>assets/images/logo.png" alt="logo" width="80%" />
                                    </a>
                                </div>
                                <div class="auth-content my-auto">
                                    <div class="text-center">
                                        <h5 class="mb-0">Welcome !</h5>
                                        <p class="text-muted mt-2">Sign in to continue</p>
                                    </div>
                                    <?php if($errorMsg = $this->session->flashdata('loginError')): ?>
                                        <div class="error errorMsg text-center"><?=$errorMsg?></div>
                                    <?php endif; ?>
                                    <form class="custom-form mt-2 pt-2" id="loginform" action="<?=base_url('admin/login/auth');?>" method="post">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-start"><div class="flex-grow-1"><label class="form-label">Username</label></div></div>
                                            <div class="input-group auth-pass-inputgroup">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                <input type="text" name="user_name" id="user_name" class="form-control form-control-lg" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                        <?=form_error('user_name')?>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-start"><div class="flex-grow-1"><label class="form-label">Password</label></div></div>
                                            <div class="input-group auth-pass-inputgroup">
                                                <span class="input-group-text" id="password-addon"><i class="fa fa-key"></i></span>
                                                <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                        <?=form_error('password')?>
                                        <div class="row mb-4 text-center">
                                            <div class="col">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input filled-in chk-col-success" value="lsRememberMe" id="rememberMe" onclick="lsRememberMe();">
                                                    <label class="form-check-label" for="rememberMe"> Remember me</label>
                                                </div>  
                                            </div>                                            
                                        </div>
                                        <div class="mb-3">
                                            <button class="btn btn-facebook btn-round btn-outline-dashed w-100 p-2" type="submit">Log In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="login-poweredby font-medium pad-5">NATIVEBIT TECHNOLOGIES</div>
                </div>
                <!-- end col -->
                <div class="col-xxl-9 col-lg-8 col-md-7 auth-bg">
                    <div class=" pt-md-5 p-4 d-flex">
                        <div class="bg-overlay bg-primary1"></div>
                        <ul class="bg-bubbles">
                            <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
                        </ul>
                        <!-- end bubble effect -->
                        <h4 class="col-xxl-12 p-0 p-sm-4 px-xl-0 text-white text-center">NATIVEBIT TECHNOLOGIES</h4>
                    </div>
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
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });

        $('#to-login').on("click", function() {
            $("#recoverform").fadeOut();
            $("#loginform").slideDown();            
        });

        $("#user_name").focus();

        const rmCheck = document.getElementById("rememberMe"),
        emailInput = document.getElementById("user_name");

        if (localStorage.checkbox && localStorage.checkbox !== "") {
            rmCheck.setAttribute("checked", "checked");
            emailInput.value = localStorage.username;
        } else {
            rmCheck.removeAttribute("checked");
            emailInput.value = "";
        }

        function lsRememberMe() {
            if (rmCheck.checked && emailInput.value !== "") {
                localStorage.username = emailInput.value;
                localStorage.checkbox = rmCheck.value;
            } else {
                localStorage.username = "";
                localStorage.checkbox = "";
            }
        }
        
    </script>
</body>

</html>