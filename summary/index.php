<?php 
require_once ("../php/config.php"); 
require_once("../php/tags.php");
?>
<!DOCTYPE html>
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
    </head>

    <?php
    $id = isset($_GET['id']) 
        ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) 
        : 0;
    if ($id == 0):
    ?>
        <body id="AchievementsList">
            <?php 
            include("../templates/navbar.php"); 
            include("../templates/login.php"); 
            if (isset($_SESSION['user'])):?>
            <div style='clear:both;'>
                <input id="new_achievement_text_input" type='text' maxlength="255" />          
                <input id="new_achievement_button" type="button" value="Quick Create" />
            </div>
            <?php endif;?>
            <div style='clear:both;'>
                <input id="hide_achievements_button" type='button' value='Hide'  />
                <input id="show_achievements_button" type='button' value='Show' style="display:none" />
                <span>Total: 
                    <span id="private_achievement_count">
                        <span id="private_achievement_total"></span>
                        (   
                        <span id="working_total" style='color:green'></span> 
                        /   
                        <span id="nonworking_total" style='color:red'></span> 
                        )
                        <span id='private_filtered_total' style='color:#a9a9a9;'> </span>
                    </span>
                    <span id="public_achievement_count">
                        <span id="public_achievement_total"></span>
                        <span id='public_filtered_total' style='color:#a9a9a9;'></span>                          
                    </span>
                </span>
                <span id='show_filter' class="hand text-button">[ Filter ]</span>
                <span id='hide_filter' class="hand text-button" style='display:none;'>[ Hide Filter ]</span>
            </div>
            <div id="filter_menu" style="display:none">
                <div>
                        <span id='show_only_locked'> 
                            Locked
                        </span>
                        <input name='show_only_filter' value='locked' type='checkbox' 
                        <?php if (isset($_SESSION['filter']['show_only']) && in_array("locked", $_SESSION['filter']['show_only'])):?>
                               checked
                        <?php endif; ?>
                               />
                        <span id='show_only_unlocked'>
                            Unlocked 
                        </span>
                        <input name='show_only_filter' value='unlocked' type='checkbox' 
                        <?php if (isset($_SESSION['filter']['show_only']) && in_array("unlocked", $_SESSION['filter']['show_only'])):?>
                               checked
                        <?php endif; ?>
                               />
                </div>
                <div id="required_filter_caption">
                    Hide Achievements That Require Others Before Completing? 
                    <input id='required_filter_checkbox' type='checkbox' />                   
                </div>
                <div>
                    
                    Tags
                    <span id="clear_tags_button" class="hand text-button">
                        [Clear]
                    </span>
                    :
                    <span id="list_of_filter_tags"></span>
                </div>
                <input id="filter_button" class="" type="button" value="Filter" />
                
            </div>

            <div id="list_of_achievements"></div>
        <?php elseif ($id > 0): ?>
        <body id="achievement_number_<?php echo $id; ?>" >
            <?php 
            include("../templates/navbar.php");
            include("../templates/login.php"); 
 ?>
            <div id="error"></div>
            <div id="achievement_profile"></div>
        <?php endif; ?>
    </body>
</html>
