<?php require_once("../php/config.php"); ?>
<html>
<head>
    <style>
        .signup_caption{
            width:90px;
            float:left;
            text-align:right;
        }
        .container_div{
            width:330px;
        }
       div{
        }    
    </style>
    <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
    <script src="register.js"></script>
</head>
<body>
    <div class='container_div'>
        <div class='signup_caption'>
            Username:
        </div>
        <input id='new_username' type='text' />
    </div><div class='container_div'>
        <div class='signup_caption'>
            Password:
        </div>
        <input id='password1' type='password' />
    </div><div class='container_div'>
        <div class='signup_caption' style='color:darkgrey;'>
            (Repeat):
        </div>
        <input id='password2' type='password' />
    </div><div id='password_error' class='container_div' style='text-align:center;' >

    </div><div class='container_div'>
        <div class='signup_caption'>
            Email:
        </div>
        <input id='new_email' type='text' />
    </div><div class='container_div'>
        <input id='register_user' type='button' style='float:right;' value='Submit' />
    </div><div id='error_div' style='color:red;'>

    </div><div>
    </div>
</body></html>

