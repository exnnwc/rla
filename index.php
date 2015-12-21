<html>
    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="index.js"></script>
    <script src="achievements.js"></script>
    <script src="rla.js"></script>
   

    <?php
    if (isset($_GET['rla'])) {
        $rla = filter_input(INPUT_GET, 'rla', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $rla = 0;
    }
    if ($rla == 0):
        ?>
        <body id="AchievementsList">
            <div id="error"></div>
            <div>
                <input id="new_achievement_text_input" type='text' maxlength="255" />          
                <input id="new_achievement_button" type="button" value="Quick Create" />
            </div>
            <div>
                <input id="hide_achievements_button" type='button' value='Hide'  />
                <input id="show_achievements_button" type='button' value='Show' style="display:none" />
            </div>
            <span id="sorting_menu">
                <input id="sort_rank_button" class="sort_button" type="button" value="Rank &#8595;"/>
                <input id="sort_rankrev_button" class="sort_button"  type='button' value="Rank &#8593;"  style="display:none" />
                <input id="sort_power_button" class="sort_button"  type="button" value="Power &#8595;" />
                <input id="sort_powerrev_button" class="sort_button"  type="button" value="Power &#8593;" style="display:none"/>
                <input id="sort_name_button"  class="sort_button" type="button" value="Name &#8595;" />
                <input id="sort_namerev_button"  class="sort_button" type="button" value="Name &#8593;"  style="display:none"/>
                <input id="sort_created_button"  class="sort_button" type="button" value="Time &#8595;" />
                <input id="sort_createdrev_button"  class="sort_button" type="button" value="Time &#8593;"  style="display:none"/>
            </span>
            <div id="list_of_achievements"></div>
        <?php elseif ($rla > 0): ?>
        <body id="achievement_number_<?php echo $rla; ?>" >
            <div id="error"></div>
            <div id="achievement_profile"></div>
        <?php endif; ?>
    </body>
</html>
