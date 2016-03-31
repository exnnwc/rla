<?php
require_once("work.php");
function display_due_date(){
   $months=["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $month_select = "<select id='new_due_date_month'><option></option>";
    foreach($months as $month_num=>$month){
        $month < 10 
            ? $month_val = "0" . (string)($month_num+1)
            : $month_val = (string)($month_num+1);
        $month_select = $month_select . "<option value='$month_val'>$month</option>";
    }
    $month_select = $month_select . "</select>";

    $days=range(1,31);
    $day_select = "<select id='new_due_date_day'><option></option>";
    foreach ($days as $day){
        $day<10 
            ? $day_val=(string)"0".$day
            : $day_val = (string)$day;
        $day_select= $day_select . "<option value='$day_val'>$day</option>";
    }
    $day_select = $day_select . "</select>";

    $current_year = date("Y");
    $years=range($current_year, $current_year+100);
    $year_select="<select id='new_due_date_year'>";
    foreach ($years as $year){
        $year_select=$year_select . "<option value='$year'>$year</option>";
    }
    $year_select = $year_select . "</select>";
    $hours=range(0,23);
    $time_select = "<select id='new_due_date_time'>";
    foreach ($hours as $hour){
        $hour<10 
        ? $hour = "0".$hour.":00"
        : $hour = $hour.":00";
        $time_select = $time_select . "<option value='$hour'>$hour</option>";
    }
    $time_select = $time_select . "</select>";
    echo ($month_select . $day_select . $year_select . $time_select );
}
function display_history($id){
    $date=0;
    $time=0;
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select * from history where achievement_id=? order by created desc");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($history=$statement->fetchObject()){
        if ($date!=date("m/d/y", strtotime($history->created))){
            $date=date("m/d/y", strtotime($history->created));
            echo "<h3 style='text-align:center;margin-bottom:0'>$date</h3>";
        }
        if ($time!=date("m/d/y g:i", strtotime($history->created))){
            $time=date("m/d/y g:i", strtotime($history->created)); 
            echo "<div>". date("g:i" , strtotime($history->created)) . "</div>";
        }
        echo "<div> - $history->message</div>";
    }
        
}
function display_todo_completion($todo){
    echo $todo->completed!=0
      ?"cancel_todo"
      : "complete_todo";
}
function display_vote_timer($id){
    $num_of_seconds_passed = get_num_of_seconds_until_authorized($id);
    if ($num_of_seconds_passed>3599){
        $hours = floor ($num_of_seconds_passed/3600);
        $num_of_seconds_passed = $num_of_seconds_passed-($hours*3600);
        $minutes = floor($num_of_seconds_passed/60);
        $num_of_seconds_passed = $num_of_seconds_passed - ($minutes*60);
        $seconds = $num_of_seconds_passed;
    }else if ($num_of_seconds_passed>59){
        $hours = 0;
        $minutes = floor($num_of_seconds_passed/60);
        $seconds = $num_of_seconds_passed - ($minutes*60);
    } else if ($num_of_seconds_passed<60){
        $hours =0;
        $minutes = 0;
        $seconds = $num_of_seconds_passed;
    }
    return  "<script>startTimer($id);</script>
 <span id='vote_timer$id' title='adfa'>".$hours . "h " . $minutes . "m " . $seconds . "s</span>";

}
function fetch_action_listing($action) {
    $string = "<span style='margin-left:20px;cursor:pointer;";
    $string = has_action_been_worked_on($action->id) ? $string . "color:grey;text-decoration:line-through;' 
                        title='Cancel work'  
                        onmouseover=\"$(this).css('text-decoration', 'none');\"  
                        onmouseleave=\"$(this).css('text-decoration', 'line-through');\"
                        onclick=\"cancelWork($action->id);\"" : $string . "' title='Click to indicate action worked.' 
                        onmouseover=\"$(this).css('text-decoration', 'line-through');\" 
                        onmouseleave=\"$(this).css('text-decoration', 'none');\" 
                        onclick=\"createWork($action->id);\"";
    $string = $string . "> $action->name [" . convert_work_num_to_caption($action->work) . "]</span>";
    if ($action->type == 1) {
        $string = $string . "<input id='action_quantity_$action->id' type='number' value='$action->default_quantity'/>";
    }
    return $string;
}

function fetch_child_menu($achievement) {
    return "<input id='delete$achievement->id' class='delete_child_button' type='button' value='X' />
            <input id='rank$achievement->id' type='number' 
                class='change_child_rank_button' value='$achievement->rank' style='width:32px;text-align:center;' />";
}
function fetch_listing_row($achievement) {
    $user_id = fetch_current_user_id();
    //this could be written so much better.
/*        $string = " <tr ><td>
                    <input id='rank$achievement->id' type='number' 
                        class='change_rank_button' value='$achievement->rank' style='width:40px;text-align:center;' />
                </td>";*/   
    $string = "<tr style='background-color:#eeeeee;'>";
    if ($achievement->parent==0){
        $string = $string . "   <td>
                                    <span id='power_caption$achievement->id' class='hand power_caption'>
                                        $achievement->power
                                    </span>
                                    <input type='number' id='power_input$achievement->id' class='power_input'
                                        value='$achievement->power' style='width:40px;text-align:center;display:none;'/> 
                                </td><td>
                                    $achievement->power_adj
                                </td>";
    }
        $string = $string . "<td style='text-align:left'>";
    if ($achievement->owner===$user_id){
        $string = $string . "
                        <input type='button'  id='activity$achievement->id' ";
        $string = !$achievement->active 
            ? $string . "  class='activate_button' style='background-color:red;' />" 
            : $string . "  class='deactivate_button' style='background-color:green;' />";

    }
    $string = $string . "<a href='" . SITE_ROOT . "/summary/?id=$achievement->id' style='color:grey;text-decoration:none;";
    if ($achievement->owner===$user_id){
        if ($achievement->completed!=0){
            $string = $string . "text-decoration:line-through;";
        } else if ($achievement->active) {
            $string = $string . "color:green;";
        } else if (!$achievement->active) {
            $string = $string . "color:red;";
        }
    }
    if (strlen($achievement->name)>48){
        $achievement->name = substr($achievement->name, 0, 48) . "...";
    }
    $string = $string . "'>
                    $achievement->name 
                    </a>";
    $num_of_days_til_due= how_many_days_until_due($achievement->id);
    if ($num_of_days_til_due!=false){
        $string=$string . "<span style='color:";
        $string = $num_of_days_til_due < 0
                    ? $string . "red;font-weight:bold;"
                    : $string . "grey;"; 

        $string = $string . "'> " . fetch_due_message($num_of_days_til_due);
        $string= $string . "</span>";
    }
    if ($achievement->public){
        $string = $string . "<span style='color:grey;float:right;font-style:italic;'>Publicly Visible</span>";
    }
    $string = $string . "</td></tr>";
    return $string;
}
function list_actions($achievement_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from actions where active=1 and achievement_id=? order by name");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ((int) $statement->fetchColumn() == 0) {
        echo "<div style='font-style:italic'>No actions registered for this achievement.</div>";
        return;
    }
    $statement = $connection->prepare("select * from actions where active=1 and achievement_id=? order by name");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        echo "  <div>
                    <input id='action$action->id' class='delete_action_button' type='button' value='X'/>
                    $action->name
                </div>";
    }
}

function list_children($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from achievements where deleted=0 and parent=? limit 1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>This achievement has no children.</div>";
        return;
    }
    $statement = $connection->prepare("select * from achievements where deleted=0 and parent=? order by rank");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    echo "<table>";
    while ($achievement = $statement->fetchObject()) {
        echo fetch_listing_row($achievement);
    }
    echo "</table>";
}

function list_filter_tags(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $user_id = fetch_current_user_id();
    if ($user_id==false){
        $user_id=0;
    }
    $statement = $connection->query("select * from tags where active=1 and achievement_id=0 and user_id=$user_id");
    while ($tag = $statement->fetchObject()){
        echo "  <input id='filter_by_".$tag->name."_checkbox' name='filtered_tags' type='checkbox' 
                    class='filter_menu' value='$tag->id'"; 
        if (isset($_SESSION['filter']['filter_tags']) && in_array($tag->id, $_SESSION['filter']['filter_tags'])){
            echo " checked";
        }
        echo "      />
                <span id='filter_by_".$tag->name."_text_button' class='hand text-button filter_text_button ";
        if (isset($_SESSION['filter']['filter_tags']) && in_array($tag->id, $_SESSION['filter']['filter_tags'])){
            echo " active-filter active-tag";
        }
        echo "      '>
                    $tag->name ($tag->tally)
                </span>";
    }
}
function list_new_actions() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from actions where achievement_id=0 and active=1");
    $statement->execute();
    echo "<option value=''>Select from previous actions here</option>";
    while ($action = $statement->fetchObject()) {
        echo "<option value='$action->id'>$action->name</option>";
    }
}

function list_new_relations() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from achievements where deleted=0 order by name");
    echo "<option>Please indicate which achievement you'd like to create a relation for.</option>";
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function list_new_requirements($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    echo "<option >Please indicate which achievement you'd like to make a requirement.</option>";
    $statement = $connection->prepare("select * from achievements where deleted=0 and parent=0 order by name asc");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function list_new_tags($achievement_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select * from tags where active=1 and achievement_id=0 order by name asc");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($tag = $statement->fetchObject()) {
        if (!is_it_already_tagged($achievement_id, $tag->name)) {
            echo "<span id='new_tag$tag->id' class='create_this_tag hand text-button' style='margin-left:8px;'>$tag->name</span>";
        }
    }
}

function list_notes($achievement_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from notes where active=1 and achievement=? order by created desc");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ((int) $statement->fetchColumn() == 0) {
        echo "<div style='font-style:italic;'>This achievement has no notes.</div>";
    }
    $statement = $connection->prepare("select * from notes where active=1 and achievement=? order by created desc");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($note = $statement->fetchObject()) {
        echo "<div style='background-color:lightgray;width:800px;'>
                <h6 style='background-color:white';margin:0px;>
                    <input id='note$note->id' class='delete_note_button' type='button' value='X' /> "
        . date("m/d/y h:i:s", strtotime($note->created))
        . "</h6>
                <div style='padding:12px;'>" .
        str_replace("\n", "<BR>", $note->body);
        echo "</div>
            </div>";
    }
}

function list_relations($achievement_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (fetch_number_of_relations_for($achievement_id) == 0) {
        echo "<div style=' font-style:italic;'>No other achievements are related.</div>";
        return;
    }
    $query = "select achievements.id, achievements.name, relations.id from achievements 
                inner join relations on achievements.id=relations.a or achievements.id=relations.b where relations.active=1 and 
                ((relations.a=:id and relations.b!=:id) or (relations.a!=:id and relations.b=:id)) and achievements.id!=:id";
    $statement = $connection->prepare($query);
    $statement->bindValue(":id", $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($result = $statement->fetch(PDO::FETCH_NUM)) {
        $db_achievement_id = $result[0];
        $achievement_name = $result[1];
        $relation_id = $result[2];
        echo "  <div>
                    <input id='relation$relation_id' class='delete_relation_button' type='button' value='X'/>
                    <a href='" . \SITE_ROOT . "?rla=$db_achievement_id'>$achievement_name</a>
                </div>";
    }
}

function list_requirements($id, $type) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $type_arr = ["for" => "by", "by" => "for"];
    $other_type = $type_arr[$type];
    if (there_are_no_requirements($id, $type)) {
        return;
    }
    $statement = $connection->prepare("select achievements.id, achievements.name, requirements.id, achievements.completed, requirements.required_$type from achievements 
                                         inner join requirements on achievements.id=requirements.required_$other_type 
                                         where requirements.active=1 and required_$type=? order by achievements.name");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($result = $statement->fetch(PDO::FETCH_NUM)) {
        $achievement_id = $result[0];
        $achievement_name = $result[1];
        $requirement_id = $result[2];
        $achievement_completed = $result[3];
        echo "  <div>
                    <input id='requirement$requirement_id' class='delete_requirement_button' type='button' value='X' />
                    <a ";
        if ($achievement_completed!=0){
           echo " style='text-decoration:line-through;' "; 

        }
        echo "          href='" . SITE_ROOT . "/summary/?id=$achievement_id'>$achievement_name</a>
                </div>";
    }
}

function list_tags($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from tags where active=1 and achievement_id=? order by name asc");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn()==0){
        echo "None.";
        return;
    }
    $statement = $connection->prepare("select * from tags where active=1 and achievement_id=? order by name asc");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($tag = $statement->fetchObject()) {
        echo "  <span class='tag-box' style=''>

                    <span class='' style=''> $tag->name </span>
                    <span id='delete$tag->id' class='delete_tag hand ' style='color:darkred;'>[x]</span>
                </span>";
    }
}
function list_todo($achievement_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    
    $statement=$connection->prepare("select count(*) from todo where active=1 and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn()==0){
        echo "<div style='font-style:italic;'>None.</div>";
        return;
    }
    $statement=$connection->prepare("select * from todo where active=1 and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($todo=$statement->fetchObject()){
        echo "  <div>
              <input id='delete_todo$todo->id' class='delete_todo' type='button' value='X' />
                    <Span id='todo_caption$todo->id' class='show_new_todo hand' title='Click to edit'
                        style='padding-left:8px;";
        if ($todo->name==NULL){
            echo "background-color:lightgrey;";
        }
        if ($todo->completed!=0){    
            echo "text-decoration:line-through;";
        }
        echo "' >";
        echo $todo->name==NULL 
            ? "Input here."
            : $todo->name; 
        echo "      </span>
                    <span id='todo_input$todo->id' style='display:none;'>
                        <input id='new_todo_input$todo->id' class='new_todo_input' type='text' value='$todo->name' />
                        <!--<span id='change_todo$todo->id' class='change_todo hand text-button' >Submit</span>-->
                    </span>
                    <input id='";
        display_todo_completion($todo);
        echo "$todo->id' class='";
        display_todo_completion($todo);
        echo "' type='checkbox'";
        if ($todo->completed!=0){
            echo " checked";
        }
        echo "/>
                </div>";
    }

}

function list_undocumented_requirements($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $start=false;
    $achievement_ids = fetch_all_requirements($id);
    foreach ($achievement_ids as $achievement_id){
        if ($start){
            echo "/";
        }
        $achievement = fetch_achievement($achievement_id);
        echo "<a href='".SITE_ROOT."/summary/?id=$achievement_id'>$achievement->name</a></div>";
        $start=true;
    }
}


function there_are_no_requirements($id, $type) {
    if (count_requirements_with($id, $type) == 0) {
        echo "<div style=' font-style:italic;'>";
        if ($type == "for") {
            echo "No other achievements required to complete this achievement.";
        } else if ($type == "by") {
            echo "No other achievements require this achievement for completion.";
        }
        echo "</div>";
    }
}
