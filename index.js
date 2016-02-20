var SITE_NAME = "Real Life Achievements";

$(document.body).ready(function () {
    //document.write("CHECK");

    if ($(document.body).attr('id') === "AchievementsList") {
        document.title = SITE_NAME + " - Achievements List";
        listAchievements("default");
        countAchievements();
        add_keypress_to_listings();
        add_button_handlers_to_listings
        add_handlers_to_index(0, false);

    } else if ($(document.body).attr('id').substr(0, 19) === "achievement_number_") {
        var achievement_id = Number($(document.body).attr('id').substr(19, $(document.body).attr('id').length - 19));
        document.title = SITE_NAME + " - #" + achievement_id;

        displayProfile(Number(achievement_id));
        add_handlers_to_index(0, true);
        add_keypress_handlers_to_profile(achievement_id);
        add_button_handlers_to_profile(achievement_id)
    }
});

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

    $(".sort_button").click(function (event) {
        var button_id = event.target.id;
        var sort_by = button_id.substr(5, (button_id.length - 12));
        if (sort_by.substr((sort_by.length - 3), 3) === "rev") {
            var sort_inverse = sort_by.substr(0, (sort_by.length - 3));
        } else if (sort_by.substr((sort_by.length - 3), 3) != "rev") {
            var sort_inverse = sort_by + "rev";
        }
        listAchievements(sort_by);
        $("#sort_" + sort_by + "_button").hide();
        $("#sort_" + sort_inverse + "_button").show();
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

    $(document).on("click", "#hide_new_achievement_name", function () {
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
        listAllActions(id);
        createAction(id, $("#new_action_input").val());
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
        console.log("ASDFA");
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
        $('#other_achievements'+id).show();
        $('#hide_other_achievements').show();
        $('#show_other_achievements').hide();
    });


}

function add_handlers_to_index(parent, from_profile) {
    $(document).on("click", ".cancel_completion_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = Number(id.substr(6, id.length - 6));
        uncompleteAchievement(achievement_id);
    });

    $(document).on("click", ".change_work_button", function (event) {
        id = event.target.attributes.id.nodeValue;
        achievement_id = Number(id.substr(4, id.length - 4));
        status = $("#work_status" + achievement_id).val();
        toggleWorkStatus(achievement_id, status, parent);
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
            listAllActions(id);
            createAction(id, $("#new_action_input").val());
        }
    });
    $(document).on("keypress", "#new_child_name", function (event) {
        if (event.which === 13) {
            createAchievement(id, $("#new_child_name").val());
            $('#new_child_name').val('');
        }
    });
}


function softGenericReload(id) {
    if ($(document.body).attr('id') === "AchievementsList") {
        listAchievements("default");
    } else if ($(document.body).attr('id').substr(0, 19) === "achievement_number_") {
        displayProfile(id);
    }
}