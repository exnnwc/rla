<?php
$pref_date_format = "F j, Y g:i:s";

//There could be an issue where users spoof this to see other people's achievements.
//Be sure to check user's session data and page reference before commencing.


$connection = new PDO("mysql:host=localhost;dbname=rla", "root", "");
$statement = $connection->prepare("select * from achievements where id=? and active=1");
$statement->bindValue(1, $_POST['id'], PDO::PARAM_INT);
$statement->execute();
$achievement = $statement->fetchObject();
?>
<div id="navbar" style='text-align:center'>

    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/rla/">Back To List</a>
    <div>
    <?php display_nav_menu($achievement->id, $achievement->rank, $achievement->parent); ?>
    </div>    
</div>
<h1> 

    <?php
    echo $achievement->name;
    ?> 

</h1>

<div>
    <div id="new_achievement_name_div" style="display:none;">
        <input id="new_achievement_name" type="text" value="<?php echo $achievement->name; ?>" />
        
        <input type="button" value="Change name" 
          onclick="ChangeName(<?php echo $achievement->id; ?>, $('#new_achievement_name').val());
                   $('#show_new_achievement_name').show(); 
                   $('#hide_new_achievement_name').hide();"/>
<!--ChangeName(<?php echo $achievement->id; ?>, $('#new_achievement_name').val());
               $('#show_new_achievement_name').show();
               $('#hide_new_achievement_name').hide();" />!-->

    </div>
    <input id="show_new_achievement_name" type="button" value="Edit" 
      onclick="$('#new_achievement_name_div').show();
               $('#show_new_achievement_name').hide();
               $('#hide_new_achievement_name').show();" />
    <input id="hide_new_achievement_name" type="button" value="Cancel" style="display:none" 
      onclick="$('#new_achievement_name_div').hide();
               $('#show_new_achievement_name').show();
               $('#hide_new_achievement_name').hide();" />
    <input type='button' value='Delete' onclick="DeleteAchievement(
    <?php echo $achievement->id; ?>,
    <?php echo $achievement->parent; ?>
        , true)" />

</div>
<div>
    Created:

    <?php
    echo date($pref_date_format, strtotime($achievement->created))
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
<!--<div>
    <h3>
        Category <input type="button" value="Edit" />
    </h3>
    <div style="display:none;" />
    <div><input type='radio' />None</div>
    <div style='margin-left:30px;'>N/A</div>
<?php
display_categories($achievement->category);
?>
</div>


<div>
<?php
echo $achievement->category ? $achievement->category : "None selected.";
?>
</div>-->
<h3>
    Description
    <input type='button' value='Edit' onclick="$('#current_description').hide();
        $('#new_description_input').show()"/>
</h3>
<span id="current_description">
    <?php
    echo $achievement->description ? str_replace("\n", "<BR>", $achievement->description) : "There is no description.";
    ?>
</span>
<span id="new_description_input" style="display:none">
    <textarea id="new_description" style="width:600px;height:150px;">
<?php
echo $achievement->description ? $achievement->description : "";
?>
    </textarea>
    <input type='button' value='Submit' onclick="ChangeDescription(<?php echo $achievement->id; ?>, $('#new_description').val())" />
</span>
</div>
<div>
    <h3>Requirements
        <input id="show_new_requirement" type="button" value="+" style="margin-left:5px;" 
               onclick="ListNewRequirements(<?php echo $achievement->id; ?>);
                       $('#new_requirement_div').show();
                       $('#hide_new_requirement').show();
                       $('#show_new_requirement').hide();"/>
        <input id="hide_new_requirement" type="button" value="-" style="margin-left:5px;display:none;" 
               onclick="$('#new_requirement_div').hide();
                       $('#hide_new_requirement').hide();
                       $('#show_new_requirement').show();"/>
    </h3>
    <div id="new_requirement_div" style="display:none;">
        <div id="requirements_error<?php echo $achievement->id; ?>" style="color:red;"></div>
        <select id="list_of_new_requirements<?php echo $achievement->id; ?>"></select><br>
        <input type="button" value="Required for completion" 
               onclick="CreateRequirement(<?php echo $achievement->id; ?>, $('#list_of_new_requirements<?php echo $achievement->id; ?>').val(), 'for');"/>
        <input type="button" value="Required by others" 
               onclick="CreateRequirement($('#list_of_new_requirements<?php echo $achievement->id; ?>').val(), <?php echo $achievement->id; ?>, 'by');"/>
    </div>
</div>
<h4>Require For Completion</h4>
<div id="required_for_<?php echo $achievement->id ?>"></div>
<h4>Required By Others</h4>
<div id="required_by_<?php echo $achievement->id ?>"></div>

<div>
    <h3>
        Sub-Achievements
    </h3>
    <input id="new_achievement<?php echo $achievement->id; ?>" type='text' maxlength="255" onkeypress="if (event.keyCode == 13) {
            CreateAchievement(<?php echo $achievement->id; ?>, this.value);
            this.value = '';
        }"/>
    <input type="button" value="Quick Create" onclick="CreateAchievement(<?php echo $achievement->id; ?>, $('#new_achievement<?php echo $achievement->id; ?>')"/>
    <div id='child_achievements_of_<?php echo $achievement->id; ?>'></div>
</div>
<?php

function display_documentation_menu($id, $status) {
    $menu = "<input type='button' value='Switch to ";
    if ($status) {
        $menu = $menu . "documented";
    } else {
        $menu = $menu . "undocumented";
    }
    $menu = $menu . "' onclick=\"ChangeDocumentationStatus($id, $status)\" />";
    return $menu;
}

function display_categories($active_category) {
    
}

function display_nav_menu($id, $rank, $parent){
    global $connection;
    if ($rank>1){
        $statement=$connection->prepare("select * from achievements where active=1 and rank=? and parent=?");
        $statement->bindValue(1, ($rank-1), PDO::PARAM_INT);
        $statement->bindValue(2, $parent, PDO::PARAM_INT);
        $statement->execute();
        $prev_achievement=$statement->fetchObject();
        echo "<div title='$prev_achievement->name' style='float:left'>
                <a href='http://" . $_SERVER['SERVER_NAME'] ."/rla/?rla=$prev_achievement->id'>Previous</a>
              </div>";
    } else {
        echo "<div style='float:left;'>Previous</div>";
    }

    echo "<select id='achievement_id' style='text-align:center;'
            onchange=\"window.location.assign('http://" . $_SERVER['SERVER_NAME'] ."/rla/?rla='+$('#achievement_id').val())\">
          <option>Go to another achievement here</option>";
    $statement=$connection->prepare ("select * from achievements where active=1 and parent=? and id!=? order by name asc");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement=$statement->fetchObject()){
        echo "<option value='$achievement->id' > $achievement->name</option>";
    }
    echo "</select>";
    $statement=$connection->prepare("select rank from achievements where active=1 and parent=? order by rank desc limit 1");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->execute();
    $highest_rank=$statement->fetchColumn();
    if ($rank<$highest_rank){
        $statement=$connection->prepare("select * from achievements where active=1 and rank=? and parent=?");
        $statement->bindValue(1, ($rank+1), PDO::PARAM_INT);
        $statement->bindValue(2, $parent, PDO::PARAM_INT);
        $statement->execute();
        $next_achievement=$statement->fetchObject();
        echo "<div title='$next_achievement->name' style='float:right'>
                <a href='http://" . $_SERVER['SERVER_NAME'] ."/rla/?rla=$next_achievement->id'>Next</a>
              </div>";
    } else {
        echo "<div class='right'>Next</div>";
    }
}
?>
