<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/rla/php/config.php"); 
?>


<html>
<head>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/rla.css">
        <!--Replace this with a web link when the site goes live.-->
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="index.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/actions.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/error.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/filter.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/listings.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/profile.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/requirements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/notes.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/tags.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/todo.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/work.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/relations.js"></script>        
        <title><?PHP echo SITE_NAME ?></title>
    <style>
        .signup_caption{
            width:150px;
            float:left;
            text-align:right;
        }
        .container_div{
            width:388px;
        }
       div{
        }    
    </style>
    <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
    <script src="register.js"></script>
    <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
</head>
<body>
    <?php 
    require_once ($_SERVER['DOCUMENT_ROOT'] . "/rla/templates/navbar.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/rla/templates/login.php");
    ?>
    <div class='container_div'>
        <div class='signup_caption'>
            Username:
        </div>
        <input id='new_username' type='text' />
    </div><div id='username_error' class='container_div' style='text-align:center;color:red;'>

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
            E-mail (optional):        
        </div>
        <input id='new_email' type='text' />
    </div><div class='container_div'>
        <input id='register_user' type='button' style='float:right;' value='Submit' />
    </div><div id='error_div' style='text-align:center;'>

    </div><div>
    </div>
</body></html>

