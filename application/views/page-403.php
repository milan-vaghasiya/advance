<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">

    <link href="<?=base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url();?>assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body>
    <div class="w3-display-middle" style="top:40%;">
        <center><img class="w3-center w3-animate-zoom" src="<?=base_url('assets/images/logo.png')?>" style="width:200px;margin-bottom:10px;padding-bottom:10px;border-bottom:2px dotted #000000;"/></center>
        <h1 class="w3-jumbo w3-animate-top w3-center text-twitter">Access Denied</h1>
        <hr class="w3-border-white w3-animate-left" style="margin:auto;width:50%">
        <h3 class="w3-center w3-animate-right">You Don't Have Permission To View This Page</h3>
        <h3 class="w3-center w3-animate-zoom">🚫🚫🚫🚫</h3>
        <h4 class="w3-center w3-animate-zoom text-info" style="text-decoration: underline;">Error code:403 Forbidden</h4>
        <h4 class="w3-center w3-animate-zoom"><a href="<?=base_url()?>" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Back to home</a></h4>
    </div>
    
</body>

</html>