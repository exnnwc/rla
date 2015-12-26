<?php
include ("config.php");
$pref_date_format = "F j, Y g:i:s";

//There could be an issue where users spoof this to see other people's achievements.
//Be sure to check user's session data and page reference before commencing.


$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$statement = $connection->prepare("select * from achievements where id=? and active=1");
$statement->bindValue(1, filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), PDO::PARAM_INT);
$statement->execute();
$achievement = $statement->fetchObject();
?>

<div id="navbar" style='text-align:center'>
    <div style="margin:5px;">
        <a href="<?PHP echo SITE_ROOT; ?>">List</a>
    </div><div style="margin-bottom:10px;">

        <a href="<?php echo SITE_ROOT ?>/?rla=<?php echo fetch_random_achievement_id(); ?>">Random</a>
    </div>
    <div>
        <?php display_nav_menu($achievement->id, $achievement->rank, $achievement->parent); ?>
    </div>    
</div>
<h1 id="achievement_name" style='text-align:center;'> 

    <?php
    echo $achievement->name;
    ?> 

</h1>

<div>
    <div id="new_achievement_name_div" style="display:none;">
        <input id="new_achievement_name" type="text" value="<?php echo $achievement->name; ?>" 
               onkeypress="if (event.keyCode == 13) {
                           changeName(<?php echo $achievement->id; ?>, $('#new_achievement_name').val());
                           $('#show_new_achievement_name').show();
                           $('#hide_new_achievement_name').hide();
                       }"/>

        <input type="button" value="Change name" 
               onclick="changeName(<?php echo $achievement->id; ?>, $('#new_achievement_name').val());
                       $('#show_new_achievement_name').show();
                       $('#hide_new_achievement_name').hide();"/>


    </div>
    <input id="show_new_achievement_name" type="button" value="Edit" 
           onclick="$('#new_achievement_name_div').show();
                   $('#show_new_achievement_name').hide();
                   $('#hide_new_achievement_name').show();" />
    <input id="hide_new_achievement_name" type="button" value="Cancel" style="display:none" 
           onclick="$('#new_achievement_name_div').hide();
                   $('#show_new_achievement_name').show();
                   $('#hide_new_achievement_name').hide();" />
    <input id='delete_achievement_<?php echo $achievement->id; ?>' class='delete_button' type='button' value='Delete' />

</div>

<div>
    Parent: 
    <?php
    if ($achievement->parent == 0) {
        echo "Top level";
    } else {
        echo "<a href='".SITE_ROOT."/?rla=$achievement->parent'>" . fetch_achievement_name($achievement->parent) . "</a>";
    }
    ?>
</div>
<div>
    Created:

    <?php
    echo date($pref_date_format, strtotime($achievement->created));
    ?>

</div>
<div>

    <?php
    echo ($achievement->completed != 0) ? "Completed:" . date($pref_date_format, strtotime($achievement->completed)) : "";
    ?>

</div>
<div> 
    Rank:<?php echo $achievement->rank; ?>
    <div>
        Power:<?php echo $achievement->power; ?>
    </div>

    <div>

        <?php
        echo $achievement->documented ? "Documented (Requires proof of completion)" . display_documentation_menu($achievement->id, 0) : "Undocumented (No proof of completion required)" . display_documentation_menu($achievement->id, 1);
        ?>

    </div>
    <h3>
        Actions
        <input id="hide_new_actions" type="button" value="-" style="display:none"
               onclick="$('#new_actions').hide();
                       $('#hide_new_actions').hide();
                       $('#show_new_actions').show();" />
        <input id="show_new_actions" type="button" value="+" style="" 
               onclick="$('#new_actions').show();
                       $('#hide_new_actions').show();
                       $('#show_new_actions').hide();" />        
    </h3>
        <div id="new_actions" style="display:none;">
            <select id="list_of_current_actions<?php echo $achievement->id; ?>"> </select><br/>
            <input id="new_action_input" type="text" onkeypress="if (event.keyCode==13){CreateAction(<?php echo $achievement->id ?>, this.value)}"/> 
            <input type="button" value="Create Action" onclick="CreateAction(<?php echo $achievement->id ?>, $('#new_action_input').val())"/>
        </div>
    </div>
    <div id="actions<?php echo $achievement->id;?>"> </div>
    <h3>
        Description
        <input id="show_new_description" type='button' value='Edit' onclick="$('#current_description').hide();
                $('#new_description_input').show();
                $('#show_new_description').hide();"/>
    </h3>
    <span id="current_description">
        <?php
        echo $achievement->description ? str_replace("\n", "<BR>", $achievement->description) : "<div style=' font-style:italic;'>There is no description.</div>";
        ?>
    </span>
    <span id="new_description_input" style="display:none">
        <textarea id="new_description" style="width:600px;height:150px;"><?php echo $achievement->description ? $achievement->description : ""; ?></textarea>
        <div>
            <input type="button" value="Cancel" onclick="$('#new_description_input').hide();
                    $('#show_new_description').show();" />
            <input type='button' value='Submit' onclick="changeDescription(<?php echo $achievement->id; ?>, $('#new_description').val())" />
        </div>
    </span>
</div>
<div>
    <h3>
        Children
        <input id="hide_new_children" type="button" value="-" style="display:none"
               onclick="$('#new_children').hide();
                       $('#hide_new_children').hide();
                       $('#show_new_children').show();" />
        <input id="show_new_children" type="button" value="+" style="" 
               onclick="$('#new_children').show();
                       $('#hide_new_children').show();
                       $('#show_new_children').hide();" />

    </h3>
    <div id="new_children" style="display:none">
        <input id="new_achievement<?php echo $achievement->id; ?>" type='text' maxlength="255" 
               onkeypress="if (event.keyCode == 13) {
                           createAchievement(<?php echo $achievement->id; ?>, this.value);
                           this.value = '';
                       }"/>
        <input type="button" value="Quick Create" 
               onclick="
                       createAchievement(<?php echo $achievement->id; ?>, $('#new_achievement<?php echo $achievement->id; ?>').val());"/>

    </div>
    <div id='child_achievements_of_<?php echo $achievement->id; ?>'></div>
</div>


<h2 style='text-align:center;'>
    Other Achievements
    <input id="hide_other_achievements" type="button" value="-" style="float:left;display:none;"
           onclick="$('#other_achievements<?php echo $achievement->id ?>').hide();
                   $('#hide_other_achievements').hide();
                   $('#show_other_achievements').show();" />
    <input id="show_other_achievements" type="button" value="+" style="float:left;" 
           onclick="$('#other_achievements<?php echo $achievement->id ?>').show();
                   $('#hide_other_achievements').show();
                   $('#show_other_achievements').hide();" />
</h2>
<div id="other_achievements<?php echo $achievement->id ?>" style="display:none;">

    <h3>
        Required For Completion
        <input id="show_new_required_for" type="button" value="+" style="margin-left:5px;" 
               onclick="ListNewRequirements(<?php echo $achievement->id; ?>);
                       $('#new_required_for').show();
                       $('#hide_new_required_for').show();
                       $('#show_new_required_for').hide();"/>
        <input id="hide_new_required_for" type="button" value="-" style="margin-left:5px;display:none;" 
               onclick="$('#new_required_for').hide();
                       $('#hide_new_required_for').hide();
                       $('#show_new_required_for').show();"/>
    </h3>
    <div id="new_required_for" style="display:none;">
        <div id="requirements_error<?php echo $achievement->id; ?>" style="color:red;"></div>
        <select id="list_of_new_required_for<?php echo $achievement->id; ?>"></select><br>
        <input type="button" value="Required for completion" 
               onclick="CreateRequirement(<?php echo $achievement->id; ?>, $('#list_of_new_required_for<?php echo $achievement->id; ?>').val(), 'for');"/>
    </div>
    <div id="required_for_<?php echo $achievement->id ?>"></div>

    <h3>
        Required By Others
        <input id="show_new_required_by" type="button" value="+" style="margin-left:5px;" 
               onclick="ListNewRequirements(<?php echo $achievement->id; ?>);
                       $('#new_required_by').show();
                       $('#hide_new_required_by').show();
                       $('#show_new_required_by').hide();"/>
        <input id="hide_new_required_by" type="button" value="-" style="margin-left:5px;display:none;" 
               onclick="$('#new_required_by').hide();
                       $('#hide_new_required_by').hide();
                       $('#show_new_required_by').show();"/>
    </h3>
    <div id="new_required_by" style="display:none;">
        <div id="requirements_error<?php echo $achievement->id; ?>" style="color:red;"></div>
        <select id="list_of_new_required_by<?php echo $achievement->id; ?>"></select><br>       
        <input type="button" value="Required by others" 
               onclick="CreateRequirement($('#list_of_new_required_by<?php echo $achievement->id; ?>').val(), <?php echo $achievement->id; ?>, 'by');"/>
    </div>
    <div id="required_by_<?php echo $achievement->id ?>"></div>


    <div>
        <h3>
            Related
            <input id="show_new_relation" type="button" value="+" style="" 
                   onclick="$('#new_relation').show();
                           $('#hide_new_relation').show();
                           $('#show_new_relation').hide();" />
            <input id="hide_new_relation" type="button" value="-"   style="display:none;"
                   onclick="$('#new_relation').hide();
                           $('#hide_new_relation').hide();
                           $('#show_new_relation').show();" />
        </h3>
        <div id="new_relation" style="display:none;">
            <select id="list_of_new_relations<?php echo $achievement->id ?>" style="text-align:center;">

            </select>

            <input type="button" value="Create Relation" 
                   onclick="CreateRelation(<?php echo $achievement->id ?>, $('#list_of_new_relations<?php echo $achievement->id ?>').val());" />
        </div>
        <div id="relation_error" style="color:red;"></div>
        <div id="list_of_relations<?php echo $achievement->id ?>"></div>

    </div>
</div>





<div>
    <h2 style='text-align:center;'>
        Notes    
        <input id="show_notes" type="button" value="+" style="float:left;display:none;"
               onclick="$('#all_notes').show();
                       $('#hide_notes').show();
                       $('#show_notes').hide();" />
        <input id="hide_notes" type="button" value="-" style="float:left;" 
               onclick="$('#all_notes').hide();
                       $('#hide_notes').hide();
                       $('#show_notes').show();" />
    </h2>
    <div id="all_notes">
        <input id="show_new_notes" type="button" value="Create Note" 
               onclick="$('#show_new_notes').hide();
                       $('#new_notes').show();" />
        <div id="new_notes" style="display:none;">
            <textarea id="new_note_inputted" style='width:400px;height:100px;'></textarea>
            <div>
                <input type="button" value="Cancel"
                       onclick="$('#new_notes').hide();

                               $('#show_new_notes').show();" />
                <input type="button" value="Create Note"
                       onclick="  CreateNote($('#new_note_inputted').val(), <?php echo $achievement->id; ?>, 0);
                               $('#new_notes').hide();
                               $('#hide_new_notes').hide();
                               $('#show_new_notes').show();" />
            </div>
        </div>
        <div id="list_of_notes<?php echo $achievement->id; ?>"></div>
    </div>
</div>

<?php

function display_documentation_menu($id, $status) {
    $menu = "<input type='button' value='";
    if ($status) {
        $menu = $menu . "Documented";
    } else {
        $menu = $menu . "Undocumented";
    }
    $menu = $menu . "' onclick=\"ChangeDocumentationStatus($id, $status)\" />";
    return $menu;
}

function display_categories($active_category) {
    
}

function display_nav_menu($id, $rank, $parent) {
    global $connection;
    if ($rank > 1) {
        $statement = $connection->prepare("select * from achievements where active=1 and rank=? and parent=?");
        $statement->bindValue(1, ($rank - 1), PDO::PARAM_INT);
        $statement->bindValue(2, $parent, PDO::PARAM_INT);
        $statement->execute();
        $prev_achievement = $statement->fetchObject();
        echo "<div title='$prev_achievement->name' style='float:left'>
                <a href='". SITE_ROOT . "/?rla=$prev_achievement->id'>Previous</a>
              </div>";
    } else {
        echo "<div style='float:left;'>Previous</div>";
    }

    echo "<select id='achievement_id' style='text-align:center;'
            onchange=\"window.location.assign('http://" . $_SERVER['SERVER_NAME'] . "/rla/?rla='+$('#achievement_id').val())\">
          <option>Go to another achievement here</option>";
    $statement = $connection->prepare("select * from achievements where active=1 and parent=? and id!=? order by name asc");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id' > $achievement->name</option>";
    }
    echo "</select>";
    $statement = $connection->prepare("select rank from achievements where active=1 and parent=? order by rank desc limit 1");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->execute();
    $highest_rank = $statement->fetchColumn();
    if ($rank < $highest_rank) {
        $statement = $connection->prepare("select * from achievements where active=1 and rank=? and parent=?");
        $statement->bindValue(1, ($rank + 1), PDO::PARAM_INT);
        $statement->bindValue(2, $parent, PDO::PARAM_INT);
        $statement->execute();
        $next_achievement = $statement->fetchObject();
        echo "<div title='$next_achievement->name' style='float:right'>
                <a href='". SITE_ROOT ."/?rla=$next_achievement->id'>Next</a>
              </div>";
    } else {
        echo "<div class='right'>Next</div>";
    }
}

function fetch_achievement_name($id) {
    global $connection;
    $statement = $connection->prepare("select name from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_random_achievement_id() {
    global $connection;
    $statement = $connection->query("select id from achievements where active=1 and parent=0 order by rand() limit 1");
    return $statement->fetchColumn();
}
?>
