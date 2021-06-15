<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


    <title>
        Clean Air Project Dashboard - Login    </title>
  
    
<style>
.login-container h1 {
    color: #1d74e7;
    font-weight: 700;
    font-size: 26px;
    margin: 0 0 15px 0;
    line-height: 1.2;
    font-family: "Open Sans", sans-serif;
}
.login-form {
    padding: 2.5rem 5rem;
    max-width: 100%;
    margin: auto;
    height: 82vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
	text-align: center;
}
.login-container {
    width: 50vw;
    height: 82vh;
    margin: 9vh auto 0;
    border: 0.1rem solid #c6cce2;
    border-radius: 1.8rem;
    -webkit-box-shadow: 0px 0px 12px 3px rgba(0, 0, 0, 0.17);
    -moz-box-shadow: 0px 0px 12px 3px rgba(0, 0, 0, 0.17);
    box-shadow: 0px 0px 12px 3px rgba(0, 0, 0, 0.17);
	font-family: "Open Sans", sans-serif;
	font-size:13px;
}
.login-form a {
    color: #4d84e6;
    margin-top: 2rem;
    font-size: 16px; display:block;
    text-decoration: underline;
}

</style>
</head>
<body class="login_admin" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 authentication-form-wrapper">
              
                <div class="mtop40 authentication-form">
                    <div class="row">
  </div>
                   
                                                            <div class="login-container">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                
                                <div class="login-form">
                                    <div class="row mB0">
										<h1><?php echo $heading; ?></h1>
										<?php echo $message; ?>
										<a href="javascript:history.back();">Click to reload this page</a>
                                    </div>
                                    
                                </div>


                            </div>
                            
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>

        
</body>



</html>