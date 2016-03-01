var SITE_NAME = "Real Life Achievements";

$(document.body).ready(function () {
    if ($(document.body).attr('id') === "AchievementsList") {
        document.title = SITE_NAME + " - Achievements List";
        displayFilterMenu();
        listAchievements("default", "default");
        countAchievements();
        add_keypress_handlers_to_listings();
        add_button_handlers_to_listings();
        add_handlers_to_index(0, false);

    } else if ($(document.body).attr('id').substr(0, 19) === "achievement_number_") {
        var achievement_id = Number($(document.body).attr('id').substr(19, $(document.body).attr('id').length - 19));
        document.title = SITE_NAME + " - #" + achievement_id;
        displayProfile(Number(achievement_id));
        add_handlers_to_index(0, true);
        add_behavior_handlers_to_profile(achievement_id);
        add_keypress_handlers_to_profile(achievement_id);

        add_button_handlers_to_profile(achievement_id);
    }
});

function add_behavior_handlers_to_profile(id){
    $(document).on("focusout", ".new_todo_input", function (event) {
        html_id = event.target.id;
        todo_id = Number(html_id.substr(14, html_id.length - 14));
        $("#todo_input"+todo_id).hide();
        $("#todo_caption"+todo_id).show();
    });

}
function add_button_handlers_to_listings() {
    $('#new_achievement_button').click(function () {
        createAchievement(0, $('#new_achievement_text_input').val());
        $('#new_achievement_text_input').val("");
    });
    $("#hide_achievements_button").click(function () {
        $('#sorting_menu').hide();
        $('#list_of_achievements').hide();
        $('#hide_achievements_button').hide();
        $('#show_achievements_button').show();
    });
    $("#show_achievements_button").click(function () {
        $('#sorting_menu').show();
        $('#list_of_achievements').show();
        $('#hide_achievements_button').show();
        $('#show_achievements_button').hide();
    });

    $(document).on("click", ".sort_button", function (event) {
        var button_id = event.target.id;
        var sort_by = button_id.substr(5, (button_id.length - 12));
        listAchievements("default", sort_by);
    });
    $(document).on("change", ".change_rank_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = id.substr(4, id.length - 4);
        parent = 0;
        rank = $("#rank" + achievement_id).val();
        changeRank(achievement_id, rank, parent);
    });
    $(document).on("click", ".change_quality_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = id.substr(8, id.length - 8);
        state = id.substr(id, 1, 1) === "1";
        changeQuality(achievement_id, state);
    });
    $(document).on("click", ".filter_menu", function (event) {
    });

    $("#show_filter").click(function () {
        $("#show_filter").hide();
        $("#hide_filter").show();
        $("#filter_menu").show();
    });
    $("#hide_filter").click(function () {
        $("#show_filter").show();
        $("#hide_filter").hide();
        $("#filter_menu").hide();
    });
    $(document).on("click", ".filter_text_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        tag = html_id.substr(10, html_id.length - (10 + 12));
        $("#filter_by_" + tag + "_checkbox").prop("checked", !$("#filter_by_" + tag + "_checkbox").prop("checked"));
    });
    $("#filter_button").click(function () {
        filterListings();
    });
    $("#clear_tags_button").click(function () {
        $("input[name='filtered_tags']:checked").removeAttr('checked');
    });

}
function add_button_handlers_to_profile(id) {


    $(document).on("click", "#edit_achievement_name_button", function () {
        changeName(id, $('#new_achievement_name').val());
        $('#show_new_achievement_name').show();
        $('#hide_new_achievement_name').hide();
    });

    $(document).on("click", "#show_new_achievement_name", function () {
        $('#new_achievement_name_div').show();
        $('#show_new_achievement_name').hide();
        $('#hide_new_achievement_name').show();
    });

    $(document).on("click", "#hide_achievement_button", function () {
        $('#new_achievement_name_div').hide();
        $('#show_new_achievement_name').show();
        $('#hide_new_achievement_name').hide();
    });
    $(document).on("click", "#hide_new_actions", function () {
        $('#new_actions').hide();
        $('#hide_new_actions').hide();
        $('#show_new_actions').show();
    });
    $(document).on("click", "#show_new_actions", function () {
        $('#new_actions').show();
        $('#hide_new_actions').show();
        $('#show_new_actions').hide();
    });
    $(document).on("click", "#create_action_button", function () {
        select_val = $("#list_of_current_actions" + id).val();
        input_val = $("#new_action_input").val();
        if (select_val && input_val) {
            //ERROR - don't do anything. 
            return;
        }
        if (input_val) {
            createAction(id, input_val);
        } else if (!input_val) {
            associateAction(id, select_val);
        }
        listAllActions(id);
    });
    $(document).on("click", "#show_new_description", function () {
        $('#current_description').hide();
        $('#new_description_input').show();
        $('#show_new_description').hide();
    });
    $(document).on("click", "#hide_new_description", function () {
        $('#new_description_input').hide();
        $('#show_new_description').show();
    });
    $(document).on("click", "#change_description", function () {
        changeDescription(id, $('#new_description').val());
    });
    $(document).on("click", "#hide_new_children", function () {
        $('#new_children').hide();
        $('#hide_new_children').hide();
        $('#show_new_children').show();
    });
    $(document).on("click", "#show_new_children", function () {
        $('#new_children').show();
        $('#hide_new_children').show();
        $('#show_new_children').hide();
    });
    $(document).on("click", "#create_child", function () {
        createAchievement(id, $('#new_child_name').val());
        $('#new_child_name').val('');
    });
    $(document).on("click", "#hide_other_achievements", function () {
        $('#other_achievements' + id).hide();
        $('#hide_other_achievements').hide();
        $('#show_other_achievements').show();
    });
    $(document).on("click", "#show_other_achievements", function () {
        $('#other_achievements' + id).show();
        $('#hide_other_achievements').show();
        $('#show_other_achievements').hide();
    });
    $(document).on("click", "#show_new_required_for", function () {
        listNewRequirements(id);
        $('#new_required_for').show();
        $('#hide_new_required_for').show();
        $('#show_new_required_for').hide();
    });
    $(document).on("click", "#hide_new_required_for", function () {
        $('#new_required_for').hide();
        $('#hide_new_required_for').hide();
        $('#show_new_required_for').show();
    });
    $(document).on("click", "#create_required_for", function () {
        createRequirement(id, $('#list_of_new_required_for' + id).val(), 'for');
    });
    $(document).on("click", "#show_new_required_by", function () {
        listNewRequirements(id);
        $('#new_required_by').show();
        $('#hide_new_required_by').show();
        $('#show_new_required_by').hide();
    });
    $(document).on("click", "#hide_new_required_by", function () {
        $('#new_required_by').hide();
        $('#hide_new_required_by').hide();
        $('#show_new_required_by').show();
    });
    $(document).on("click", "#create_required_by", function () {
        createRequirement($('#list_of_new_required_by' + id).val(), id, 'by');
    });
    $(document).on("click", "#show_new_relation", function () {
        $('#new_relation').show();
        $('#hide_new_relation').show();
        $('#show_new_relation').hide();
    });
    $(document).on("click", "#hide_new_relation", function () {
        $('#new_relation').hide();
        $('#hide_new_relation').hide();
        $('#show_new_relation').show();
    });
    $(document).on("click", "#create_relation", function () {
        createRelation(id, $('#list_of_new_relations' + id).val());
    });
    $(document).on("click", "#show_notes", function () {
        $('#all_notes').show();
        $('#hide_notes').show();
        $('#show_notes').hide();
    });
    $(document).on("click", "#hide_notes", function () {
        $('#all_notes').hide();
        $('#hide_notes').hide();
        $('#show_notes').show();
    });
    $(document).on("click", "#show_new_notes", function () {
        $('#show_new_notes').hide();
        $('#new_notes').show();
    });
    $(document).on("click", "#cancel_new_note", function () {
        $('#new_notes').hide();
        $('#show_new_notes').show();
    });
    $(document).on("click", "#create_note", function () {
        createNote($('#new_note_inputted').val(), id);
        $('#new_notes').hide();
        $('#hide_new_notes').hide();
        $('#show_new_notes').show();
        $('#new_note_inputted').val('');
    });
    $(document).on("click", "#change_documentation", function (event) {
        toggleDocumentationStatus(id);
    });
    $(document).on("click", ".delete_child_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        achievement_id = JSON.parse(html_id.substr(6, html_id.length - 6));
        deleteAchievement(achievement_id, id, true);
    });
    $(document).on("change", ".change_child_rank_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        achievement_id = html_id.substr(4, html_id.length - 4);
        rank = $("#rank" + achievement_id).val();
        changeRank(achievement_id, rank, id);
    });
    $(document).on("click", ".delete_action_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        action_id = html_id.substr(6, html_id.length - 6);
        deleteAction(action_id, id);
    });
    $(document).on("click", ".delete_note_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        note_id = html_id.substr(4, html_id.length - 4);
        deleteNote(note_id, id);
    });
    $(document).on("click", ".delete_relation_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        relation_id = html_id.substr(8, html_id.length - 8);
        deleteRelation(relation_id, id);
    });
    $(document).on("click", ".delete_requirement_button", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        requirement_id = html_id.substr(11, html_id.length - 11);
        deleteRequirement(requirement_id, id);
    });
    $(document).on("click", "#hide_achievement_information", function () {
        $("#hide_achievement_information").hide();
        $("#show_achievement_information").show();
        $("#achievement_info").hide();
    });
    $(document).on("click", "#show_achievement_information", function () {
        $("#hide_achievement_information").show();
        $("#show_achievement_information").hide();
        $("#achievement_info").show();
    });
    $(document).on("click", ".toggle_active_status", function () {
        toggleActiveStatus(id);
    });
    $(document).on("click", "#achievement_quality", function () {
        toggleQuality(id);
    });

    $(document).on("click", "#new_action_input", function () {
        if ($("#new_action_input").val() === "Create new action here") {
            $("#new_action_input").val("");
        }
    });
    $(document).on("change", ".list_of_current_actions", function (event) {
        $("#new_action_input").val("");

    });

    $(document).on("click", "#show_new_tags", function (event) {
        $("#show_new_tags").hide();
        $("#new_tags").show();
    });
    $(document).on("click", "#hide_new_tags", function (event) {
        $("#show_new_tags").show();
        $("#new_tags").hide();
    });
    $(document).on("click", "#create_tag", function (event) {
        name = $("#new_tag_input").val();
        createTag(id, name);
    });
    $(document).on("click", ".delete_tag", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        tag_id = Number(html_id.substr(6, html_id.length - 6));
        deleteTag(tag_id, id);
    });
    $(document).on("click", ".create_this_tag", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        tag_id = Number(html_id.substr(7, html_id.length - 7));
        name = $("#new_tag" + tag_id).html();
        createTag(id, name);
    });
    $(document).on("keypress", "#new_tag_input", function (event) {
        if (event.key == "Enter") {
            name = $("#new_tag_input").val();
            createTag(id, name);
        }
    });
    $(document).on("click", ".create_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        achievement_id = Number(html_id.substr(4, html_id.length - 4));
        createToDo(achievement_id);
    });
    $(document).on("click", ".show_new_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        todo_id = Number(html_id.substr(12, html_id.length - 12));
        $("#todo_input"+todo_id).show();
        $("#todo_caption"+todo_id).hide();
        setTimeout(function(){$("#todo_input"+todo_id).focus();}, 0);
    });
/*
    $(document).on("click", ".change_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        todo_id = Number(html_id.substr(11, html_id.length - 11));
        $("#todo_input"+todo_id).hide();
        $("#todo_caption"+todo_id).show();
        changeToDoName(id, todo_id, $("#new_todo_input"+todo_id).val());
    });
*/
    $(document).on("click", ".cancel_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        todo_id = Number(html_id.substr(11, html_id.length - 11));
        cancelToDo(id, todo_id);
    });
    $(document).on("click", ".complete_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        todo_id = Number(html_id.substr(13, html_id.length - 13));
        completeToDo(id, todo_id);
    });
    $(document).on("click", ".delete_todo", function (event) {
        html_id = event.target.attributes.id.nodeValue;
        todo_id = Number(html_id.substr(11, html_id.length - 11));
        deleteToDo(id, todo_id);
    });
    $(document).on("click", "", function (event) {

    });
    $(document).on("click", "", function (event) {

    });
    $(document).on("click", "", function (event) {

    });
}

function add_handlers_to_index(parent, from_profile) {
    $(document).on("click", ".cancel_completion_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = Number(id.substr(6, id.length - 6));
        uncompleteAchievement(achievement_id);
    });

    $(document).on("change", ".change_work_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = Number(id.substr(4, id.length - 4));
        toggleWorkStatus(achievement_id);
    });
    $(document).on("click", ".complete_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = Number(id.substr(8, id.length - 8));
        completeAchievement(achievement_id);
    });
    $(document).on("click", ".delete_achievement_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = JSON.parse(id.substr(6, id.length - 6));
        deleteAchievement(achievement_id, parent, from_profile);
    });
    $(document).on("click", ".activate_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = JSON.parse(id.substr(8, id.length - 8));
        activateAchievement(achievement_id);
    });
    $(document).on("click", ".deactivate_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = JSON.parse(id.substr(8, id.length - 8));
        deactivateAchievement(achievement_id);
    });
}

function add_keypress_handlers_to_listings() {
    $('#new_achievement_text_input').keypress(function (event) {
        if (event.which === 13) {
            createAchievement(0, $('#new_achievement_text_input').val());
            $('#new_achievement_text_input').val("");
        }
    });
}

function add_keypress_handlers_to_profile(id) {
    $(document).on("keypress", "#new_achievement_name", function (event) {
        if (event.which === 13) {
            changeName(id, $('#new_achievement_name').val());
            $('#show_new_achievement_name').show();
            $('#hide_new_achievement_name').hide();
        }
    });
    $(document).on("keypress", "#new_action_input", function (event) {
        if (event.which === 13) {
            createAction(id, $("#new_action_input").val());
            listAllActions(id);
        }
    });
    $(document).on("keypress", "#new_child_name", function (event) {
        if (event.which === 13) {
            createAchievement(id, $("#new_child_name").val());
            $('#new_child_name').val('');
        }
    });
    $(document).on("keypress", ".new_todo_input", function (event) {
        if (event.which === 13) {
            html_id = event.target.attributes.id.nodeValue;
            todo_id = Number(html_id.substr(14, html_id.length - 14));
            $("#todo_input"+todo_id).hide();
            $("#todo_caption"+todo_id).show();
            changeToDoName(id, todo_id, $("#new_todo_input"+todo_id).val());
        }
    });
}


function softGenericReload(id) {
    if ($(document.body).attr('id') === "AchievementsList") {
        listAchievements("default", "default");
        countAchievements();
    } else if ($(document.body).attr('id').substr(0, 19) === "achievement_number_") {
        displayProfile(id);
    }
}
