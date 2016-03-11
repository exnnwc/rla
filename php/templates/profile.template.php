<div id="navbar" style='text-align:center'>
    <div style="margin:5px;">
        <a href="[@site_root]">Achievements List</a>
    </div><div style="margin-bottom:10px;">

        <a href="[@site_root]/achievement/[@rand_id]">Random</a>
    </div>
    <div>
        [@profile_nav_menu]
    </div>    
</div>
<div>
    <?php if (!$achievement->deleted): ?>
    <input id='delete<?php echo $achievement->id; ?>' 
           class='remove_achievement_button' type='button' value='X' 
           title='
           <?php echo   $achievement->deleted 
                            ? "Archived"
                            : "Delete";
            ?> Achievement #<?php echo $achievement->id; ?>' 
           style="width:25px;height:25px;text-align:center;
           <?php if ($achievement->abandoned){
               echo "color:red;font-weight:bold;";
           }?>"/>
    <?php endif; ?>
    <?php if ($achievement->completed == 0 &&!$achievement->documented): ?>    
    <input id='complete<?php echo $achievement->id; ?>' value="&#10003;" class='complete_button' type='button' style="width:25px;height:25px;text-align:center;"/>
    <?php endif; ?>
    <?php if ($achievement->locked==0):?>
    <span class='toggle_locked_status hand text-button'>[ Lock ] </span>
    <?php endif; ?> 
</div>


<div id="history_section" >
    <span id='show_history' class='hand text-button'>[ Show History ]</span>
    <div id='history' style='display:none;background-color:lightgrey;padding:8px;padding-bottom:0px;'>
        <h1 style='text-align:center;'>
            History        
            <span id='hide_history' class='hand text-button h-normal' style='float:left;'>[ Hide ] </span>
        </h1>
            <div id='achievement_history' style=''> </div> 
    </div>

</div>


<h1 id="achievement_name" style='text-align:center;'> 
    <div 
    <?php if ($achievement->locked==0):?>
        id="show_new_achievement_name" class="hand"
    <?php endif; ?> 
            ><?= $achievement->name ?> </div>

    <div id="hide_new_achievement_name" style="display:none;">
        <div>
            <textarea maxlength="255" id="new_achievement_name"   
                style="width:640px;height:160px;font-size:32px;text-align:center;"><?= $achievement->name ?></textarea>
        </div><div>
            <input id='edit_achievement_name_button' type="button" value="Change name"/>
            <input id="hide_achievement_button" type="button" value="Cancel"/>
        </div>
    </div>
</h1>
<div>
    <span id='achievement_active<?php echo $achievement->id; ?>' class='hand toggle_active_status'
    <?php 
    if (!$achievement->abandoned){
        echo  $achievement->active ? "style='color:green;'>Active" : "style='color:darkred;'>Inactive";
    }
    ?>
    <?php if ($achievement->abandoned):?>
        style='font-weight:bold;'>Abandoned <span id='restore<?php echo $achievement->id;?>' class='restore_achievement_button hand text-button'>
                        [ Undo ]
                    </span>
    <?php endif; ?>
</span>

</div>
<div>
    <?php
    if ($achievement->locked != 0) {
    echo "<span style='font-weight:bold;' title='Achievement's information cannot be changed.'>Locked </span>("
    . date("m/d/y", strtotime($achievement->locked))
        .") <span class='toggle_locked_status hand text-button'>[ Unlock ] </span>";
    }
    ?>

</div>
<div>
    <div>
        <?php
        echo $achievement->documented
        ? "Documented (Requires proof of completion) 
           <span  id='change_documentation' class='hand text-button'>[ Toggle ]</span>"
        : "Undocumented (No proof of completion required)
           <span  id='change_documentation' class='hand text-button'>[ Toggle ]</span>";
        ?>
    </div>
</div>
<div>
    Parent: 
    <?php
    echo ($achievement->parent == 0)
    ? "<a href='".  SITE_ROOT . "/'> Top level</a>"
    : "<a href='" . SITE_ROOT . "/?rla=$achievement->parent'>" . fetch_achievement_name($achievement->parent) . "</a>";
    ?>
</div>
<div>
    Created: <?php echo date($pref_date_format, strtotime($achievement->created)); ?>
</div>
<div>
    Due:    <?php if ($achievement->due==0):?>
                No due date set. 
            <?php elseif ($achievement->due!=0):?>
                <?php 
                    echo date("m/d/y", strtotime($achievement->due)) . " "; 
                    if(date("G", strtotime($achievement->due))!="0"){
                        echo date("gA", strtotime($achievement->due)) . " " ;
                    }
                    $num_of_days_til_due=how_many_days_until_due($achievement->id);
                    echo "<span style='color:red;";
                    if ($num_of_days_til_due<0){
                        echo "font-weight:bold;";
                    }
                    echo "' >".fetch_due_message($num_of_days_til_due) . "</span>";
                ?>
            <?php endif; ?>
                <span id='show_new_due_date' class='hand text-button'>[ + ] </span>
                <span id='hide_new_due_date' class='hand text-button' style='display:none;'>[ - ] </span>
                <div id='new_due_date' style='display:none;'>
                    <div>
                        <?php display_due_date(); ?>
                    </div>
                    <input id='create_new_due_date' type='button' value='Set Due Date' />
                    <input id='clear_due_date' type='button' value='Clear Due Date' />
                </div>

</div>
<div>
    <?php
    ($achievement->completed != 0)
    and print ("Completed:
                        <span style='margin-left:8px;'>"
    . date($pref_date_format, strtotime($achievement->completed))
    . " </span>
                        <span id='cancel$achievement->id' class='text-button hand cancel_completion_button'>
                            [ Undo ]
                        </span>");
    ?>
</div>
<div> 
    Rank: <?php echo $achievement->rank; ?>
</div>
<div>
    Power: <?php echo $achievement->power_adj; ?>
</div>
<div >
    Tags: <span id='list_of_tags<?php echo $achievement->id; ?>' style='margin-right:8px;'></span>
    <span id="show_new_tags" class="hand text-button h-normal" style=''> [ + ] </span>
    <span id='new_tags' style='display:none;'>
        <span id="hide_new_tags" class="hand text-button h-normal" > [ - ] </span>
        <span id="list_of_new_tags<?php echo $achievement->id; ?>"></span>
        <input id="new_tag_input" type='text' style="width:140px;">
        <input id="create_tag" type='button' value='New Tag'>
    </span>
</div>

<h3>
    Description
    <?php if ($achievement->locked==0): ?>
    <span class="h-normal">
        <span id="show_new_description" class="hand text-button show_new_description">[ Edit ]</span>
    </span>
    <?php endif; ?>
</h3>
<span id="current_description">
    <?php
    echo $achievement->description ? format_appropriately($achievement->description) : "<div style=' font-style:italic;'>There is no description.</div>";
    ?>
</span>
<span id="new_description_input" style="display:none">
    <textarea maxlength="20000" id="new_description" style="width:600px;height:150px;"><?php echo $achievement->description ? $achievement->description : ""; ?></textarea>
    <div>
        <input id='change_description' type='button' value='Submit' />
        <input id="hide_new_description" type="button" value="Cancel" />
    </div>
</span>
</div>
<div>
    <h3>
        Children
        <?php if ($achievement->locked==0): ?>
        <input id="hide_new_children" type="button" value="-" style="display:none"/>
        <input id="show_new_children" type="button" value="+" style=""/>
        <?php endif; ?>

    </h3>
    <div id="new_children" style="display:none">
        <input id="new_child_name" type='text' maxlength="255"/>
        <input id="create_child" type="button" value="Quick Create"/>
    </div>
    <div id='child_achievements_of_<?php echo $achievement->id; ?>'>
    </div>
</div>


<h2 style='text-align:center;border-top:1px dashed black;padding-top:32px;padding-bottom:32px;'>

    Other Achievements
    <span id="hide_other_achievements" class="h-normal hand text-button" style="float:left;">[ - ]</span>
    <span id="show_other_achievements" class="h-normal hand text-button" style="float:left;display:none;">[ + ]</span>

</h2>
<div id="other_achievements<?php echo $achievement->id ?>" style="">
    <h3>
        Required For Completion
        <span id="show_new_required_for" class="h-normal hand text-button" style="margin-left:5px;">[ + ]</span>
        <span id="hide_new_required_for" class="h-normal hand text-button" style="margin-left:5px;display:none;">[ - ]</span>
    </h3>
    <div id="new_required_for" style="display:none;">
        <div id="requirements_error<?php echo $achievement->id; ?>" style="color:red;"></div>
        <select id="list_of_new_required_for<?php echo $achievement->id; ?>"></select><br>
        <input id="create_required_for" type="button" value="Require For Completion"/>
    </div>
    <div id="required_for_<?php echo $achievement->id ?>"></div>

    <h3>
        Required By Others
<!--        <span id="show_new_required_by" class="h-normal hand text-button" style="margin-left:5px;">[ + ]</span>-->
        <span id="hide_new_required_by" class="h-normal hand text-button" style="margin-left:5px;display:none;">[ - ]</span>
    </h3>
    <div id="new_required_by" style="display:none;">
        <div id="requirements_error<?php echo $achievement->id; ?>" style="color:red;"></div>
        <select id="list_of_new_required_by<?php echo $achievement->id; ?>"></select><br>       
        <input id="create_required_by" type="button" value="Create Requirement For Other Achievement" />
    </div>
    <div id="required_by_<?php echo $achievement->id ?>"></div>


    <div>
        <h3>
            Related
            <span id="show_new_relation" class="h-normal hand text-button" style="margin-left:5px;">[ + ]</span>
            <span id="hide_new_relation" class="h-normal hand text-button" style="margin-left:5px;display:none;">[ - ]</span>
        </h3>
        <div id="new_relation" style="display:none;">
            <select id="list_of_new_relations<?php echo $achievement->id ?>" style="text-align:center;">

            </select>

            <input id="create_relation" type="button" value="Create Relation" />
        </div>
        <div id="relation_error" style="color:red;"></div>
        <div id="list_of_relations<?php echo $achievement->id ?>"></div>

    </div>
</div>

<div>
    <h2 style='text-align:center;border-top:1px dashed black;padding-top:32px;padding-bottom:32px;'>
        Notes
        <span id="show_notes" class="h-normal hand text-button" style="float:left;display:none;">[ + ]</span>
        <span id="hide_notes" class="h-normal hand text-button" style="float:left;">[ - ]</span>
    </h2>
    <div id="all_notes">
    <h3>
        To Do 
        <span id="todo<?php echo $achievement->id; ?>" class="create_todo hand text-button h-normal" style="">[ New ]</span>
    </h3>
    <div id="todo_list"></div>
        <h3>
            Notes        
            <span id="show_new_notes" class="h-normal hand text-button">[ New ]</span>
       
        </h3>

        <div id="new_notes" style="display:none;">
            <textarea id="new_note_inputted" style='width:400px;height:100px;'></textarea>
            <div>
                <input id="create_note" type="button" value="Create Note"/>
                <input id="cancel_new_note" type="button" value="Cancel"/>
            </div>
        </div>
        <div id="list_of_notes<?php echo $achievement->id; ?>" style='margin-top:16px;'></div>
    </div>
</div>
<div style='padding:20px;'>&nbsp;</div>
