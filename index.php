<html>
    <head>
        <style>

        </style>

    </head>

    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="rla.js">

    </script>
    <?php
    if (!isset($_GET['rla'])) {
        $_GET['rla'] = 0;
    } else {
        $_GET['rla'] = (int) $_GET['rla'];
    }
    if ($_GET['rla'] == 0):
        ?>
        <body onload="ListAchievements(0);">
            <input id="new_achievement" type='text' maxlength="255" onkeypress="if (event.keyCode == 13) {
                            CreateAchievement(0, this.value);
                            this.value = '';
                        }"/>          
            <input type="button" value="Quick Create" onclick="CreateAchievement(0, $('#new_achievement').val());"/>
            <div>
                <input id="sort_rank_button" type="button" value="Rank &#8595;" 
                       onclick="ListAchievements('rank');$('#sort_rank_button').hide();$('#sort_rankrev_button').show();" />
                <input id="sort_rankrev_button" type='button' value="Rank &#8593;"  style="display:none"
                       onclick="ListAchievements('rank_rev');$('#sort_rank_button').show();$('#sort_rankrev_button').hide();" />
                <input id="sort_power_button" type="button" value="Power &#8595;" 
                       onclick="ListAchievements('power');$('#sort_power_button').hide();$('#sort_powerrev_button').show();" />
                <input id="sort_powerrev_button" type="button" value="Power &#8593;" style="display:none"
                       onclick="ListAchievements('power_rev');$('#sort_power_button').show();$('#sort_powerrev_button').hide();" />
                <input id="sort_name_button" type="button" value="Name &#8595;" 
                       onclick="ListAchievements('name');$('#sort_name_button').hide();$('#sort_namerev_button').show();" />
                <input id="sort_namerev_button" type="button" value="Name &#8593;"  style="display:none"
                       onclick="ListAchievements('name_rev');$('#sort_name_button').show();$('#sort_namerev_button').hide();" />
                <input id="sort_created_button" type="button" value="Time &#8595;" 
                       onclick="ListAchievements('created');$('#sort_created_button').hide();$('#sort_createdrev_button').show();" />
                <input id="sort_createdrev_button" type="button" value="Time &#8593;"  style="display:none"
                       onclick="ListAchievements('created_rev');$('#sort_created_button').show();$('#sort_createdrev_button').hide();" />
                
            </div>
            <div id="error"></div>
            <div id="list_of_achievements"></div>
        <?php elseif ($_GET['rla'] > 0): ?>
        <body onload="DisplayAchievement(<?php echo $_GET['rla']; ?>);">
            <div id="error"></div>
            <div id="achievement_profile"></div>
        <?php endif; ?>
    </body>
</html>
