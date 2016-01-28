var SITE_NAME = "Real Life Achievements";

$(document.body).ready(function () {
    //document.write("CHECK");

    if ($(document.body).attr('id') === "AchievementsList") {
        document.title = SITE_NAME + " - Achievements List";
        listAchievements("default");
        countAchievements();
        add_keypress_to_inputs();

        add_handlers_to_buttons();

    } else if ($(document.body).attr('id').substr(0, 19) === "achievement_number_") {
        var achievement_id = $(document.body).attr('id').substr(19, $(document.body).attr('id').length - 19);
        document.title = SITE_NAME + " - #" + achievement_id;

        displayProfile(achievement_id);
    } 
});
function add_keypress_to_inputs() {
    $('#new_achievement_text_input').keypress(function (event) {
        if (event.which === 13) {
            createAchievement(0, $('#new_achievement_text_input').val());
            $('#new_achievement_text_input').val("");
        }
    });
}
function add_handlers_to_buttons() {
    $('#new_achievement_button').click(function () {
        createAchievement(0, $('#new_achievement_text_input').val());
        $('#new_achievement_text_input').val("");
    });
    $("#hide_achievements_button").click(function () {
        $('#sorting_menu').hide();
        $(".new_shit").on("click", function () {
            console.log("ASDFAS");
        });
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
        } else {
            var sort_inverse = sort_by + "rev";
        }
        listAchievements(sort_by);
        $("#sort_" + sort_by + "_button").hide();
        $("#sort_" + sort_inverse + "_button").show();
    });

    $(".delete_button").click(function () {
        console.log("work");
    });

}


