<?php require_once("../php/config.php"); ?>
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
    if (!isset($_SESSION['user'])){
        require_once("login.htm"); 
    } else if (isset($_SESSION['user'])){
        echo "Logged in as " . $_SESSION['username'] . ".";
    }
    ?>
</body></html>

