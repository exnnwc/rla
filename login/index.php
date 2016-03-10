<?php require_once("../php/config.php"); 

require_once("../php/user.php");
?>
<html>
<head>
    <style>
        .login-caption{
            width:90px;
            float:left;
        }
    </style>
    <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
    <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
    <script src="<?php echo SITE_ROOT; ?>/js/login.js"></script>
</head>
<body>
    <?php
    $user_id=fetch_current_user_id();
    if ($user_id==false){
        require_once("login.htm"); 
    } else if ($user_id!=false){
        echo "Logged in as " . fetch_username(fetch_current_user_id()) . ".";
    }
    ?>
</body></html>

