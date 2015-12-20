function ChangeDescription(id, description) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_description", id: id, description: description}
    })
            .done(function (result) {
                DisplayAchievement(id);
            });


}
function ChangeName(id, new_name) {
    //document.write(id+ " " + new_name);
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_name", id: id, new_name: new_name}
    })
            .done(function (result) {
//                $("#error").html(result);
                DisplayAchievement(id);
            });
}
function ChangePower(id, new_power, fromProfile) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_power", id: id, new_power: new_power}
    })
            .done(function (result) {
                $("#error").html(result);
                if (fromProfile) {
                    DisplayAchievement(id);
                    //$("#error").html("1");
                } else {
                    ListAchievements(0);
                    //$("#error").html("2");
                }
            });

}
function ChangeRank(id, new_rank, fromProfile, parent) {
    if (new_rank > 0) {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "change_rank", id: id, new_rank: new_rank}
        })
                .done(function (result) {

                    //$("#error").html(result);
                    if (fromProfile) {
                        DisplayChildren(parent);
                        //$("#error").html("1");
                    } else {
                        ListAchievements(0);

                        //$("#error").html("2");
                    }
                });
    }
}
function ChangeDocumentationStatus(id, status) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_documentation_status", id: id, status: status}
    })
            .done(function (result) {
                DisplayAchievement(id);
            });

}
function CreateAchievement(parent, name) {
    // console.log(parent + " " + name);
    if (name.length > 255) {
        $("#error").html("This has too many characters.")
    } else if (name.trim() == "") {

    } else {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "create_quick", parent: parent, name: name}
        })
                .done(function (result) {
                    if (parent == 0) {
                        ListAchievements(0);

                    } else if (parent > 0) {
                        DisplayChildren(parent);
                        //console.log(result);

                    } else {
                        document.write("2");
                    }
                });
    }
}

function CreateAction(achievement_id, action) {
    //console.log(achievement_id + " " + action);
    if (!$('#list_of_current_actions'+achievement_id + ' option:selected').val()){
        reference=0;
        
    } else {
        reference=$('#list_of_current_actions'+achievement_id).val();
    }
   // console.log ($('#list_of_current_actions'+achievement_id).val() + " " + achievement_id + " " + action);
    $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "create_action", achievement_id: achievement_id, action: action, reference:reference}
    })
            .done(function (result) {
                //console.log (result);
                ListActions(achievement_id);
                    });
}
function CreateNote(note, achievement_id, edit) {
    // console.log(edit + " " + achievement_id + " " + note);
    if (note.trim() == "") {


    } else {
        $.ajax({
            method: "POST",
            url: "notes.php",
            data: {function_to_be_called: "create", note: note.trim(), achievement_id: achievement_id, edit: edit}
        })
                .done(function (result) {
                    ListNotes(achievement_id);
                    //    console.log(result);
                });
    }
}

function CreateRelation(a, b) {
    $.ajax({
        method: "POST",
        url: "relations.php",
        data: {function_to_be_called: "create", a: a, b: b}
    })
            .done(function (result) {
                if (result.substr(0, 1) == "0") {
                    $("#relation_error").html(result.substr(2, result.length));
                } else {
                    ListRelations(a);
                }
            });
}
function CreateRequirement(required_for, required_by, type) {
    if (required_for != required_by) {
        $.ajax({
            method: "POST",
            url: "requirements.php",
            data: {function_to_be_called: "create", required_for: required_for, required_by: required_by}
        })
                .done(function (result) {
                    if (result.substr(0, 1) == "0") {
                        if (type == "for") {
                            $("#requirements_error" + required_for).html(result.substr(1, result.length));
                        } else if (type == "by") {
                            $("#requirements_error" + required_by).html(result.substr(1, result.length));
                        }
                    } else {
                        if (type == "for") {
                            ListRequirements(required_for, type);
                        } else if (type == "by") {
                            ListRequirements(required_by, type);
                        }
                    }
                });
    } else {
        if (type == "for") {
            $("#requirements_error" + required_for).html("Achievement cannot be a requirement of itself.");
        } else if (type == "by") {
            $("#requirements_error" + required_by).html("Achievement cannot be a requirement of itself.");
        }

    }
}
function DeleteAchievement(id, parent, fromProfile) {
    if (window.confirm("Are you sure you want to delete this achievement?")) {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    if (fromProfile) {
                        //Need to include code to make a distinction between the parent and child.
                        if (parent == 0) {
                            DisplayAchievement(id);
                        } else if (parent > 0) {
                            DisplayChildren(parent);
                        }
                    } else if (fromProfile == false) {
                        ListAchievements(0);
                    }

                }
                );
    }
}

function DeleteAction(id, achievement_id){
    if (window.confirm("Are you sure you want to delete this action?")) {
        $.ajax({
            method: "POST",
            url: "work/work.php",
            data: {function_to_be_called: "delete_action", id: id}
        })
                .done(function (result) {
                    ListActions(achievement_id);
                });
    }    
}
function DeleteNote(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a relationship?")) {
        $.ajax({
            method: "POST",
            url: "notes.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    ListNotes(achievement_id);
                });
    }
}
function DeleteRelation(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a relationship?")) {
        $.ajax({
            method: "POST",
            url: "relations.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    ListRelations(achievement_id);
                });
    }
}
function DeleteRequirement(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a requirement?")) {
        $.ajax({
            method: "POST",
            url: "requirements.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    ListRequirements(achievement_id, "for");
                    ListRequirements(achievement_id, "by");
                });
    }
}
function DisplayAchievement(id) {

    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "is_it_active", id: id}
    })
            .done(function (result) {
                if (result == "1") {

                    $.ajax({
                        method: "POST",
                        url: "display.php",
                        data: {id: id}
                    })
                            .done(function (result) {
                                $("#achievement_profile").html(result);
                                ListActions(id);
                                ListCurrentActions(id);
                                ListRequirements(id, "for");
                                ListRequirements(id, "by");
                                DisplayChildren(id);
                                ListRelations(id);
                                ListNewRelations(id);
                                ListNotes(id);
                            });
                } else if (result == "0") {
                    $("#achievement_profile").html("This achievement has been deleted.");
                } else {
                    $("#achievement_profile").html("This profile does not exist.");
                }
            });

}
function DisplayChildren(parent) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "list_children", parent: parent}
    })
            .done(function (result) {
                $("#child_achievements_of_" + parent).html(result);

            });

}
function IsItActive(id) {

    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "is_it_active", id: id}
    })
            .done(function (result) {
                $("#achievement_profile").html(typeof result);
            });
}
function ListAchievements(sort) {
    if (sort == 0) {
        sort = "default";

    }
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "list", sort_by: sort}
    })
            .done(function (result) {
                $("#list_of_achievements").html(result);
            });

}
function ListActions(achievement_id){
        $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "list_actions", achievement_id:achievement_id}
    })
            .done(function (result) {
                $("#actions" + achievement_id).html(result);
            });
}
function ListCurrentActions(achievement_id){
    console.log(achievement_id);
        $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "list_current_actions", achievement_id:achievement_id}
    })
            .done(function (result) {
                console.log(result);
                $("#list_of_current_actions" + achievement_id).html(result);
            });    
}
function ListNewRelations(id) {
    $.ajax({
        method: "POST",
        url: "relations.php",
        data: {function_to_be_called: "list_new"}
    })
            .done(function (result) {
                $("#list_of_new_relations" + id).html(result);
            });
}
function ListNotes(achievement_id) {
    //console.log("LISTING NOTES" + achievement_id);
    $.ajax({
        method: "POST",
        url: "notes.php",
        data: {function_to_be_called: "list", achievement_id: achievement_id}
    })
            .done(function (result) {
                // console.log(result);
                $("#list_of_notes" + achievement_id).html(result);
            });

}
function ListRelations(achievement_id) {
    $.ajax({
        method: "POST",
        url: "relations.php",
        data: {function_to_be_called: "list", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#list_of_relations" + achievement_id).html(result);

            });
}
function ListNewRequirements(id) {
    $.ajax({
        method: "POST",
        url: "requirements.php",
        data: {function_to_be_called: "list_new"}
    })
            .done(function (result) {
                $("#list_of_new_required_for" + id).html(result);
                $("#list_of_new_required_by" + id).html(result);
            });
}
function ListRequirements(id, type) {
    $.ajax({
        method: "POST",
        url: "requirements.php",
        data: {function_to_be_called: "list_requirements", id: id, type: type}
    })
            .done(function (result) {
                $("#required_" + type + "_" + id).html(result);
            });
}

function SwitchAchievement(id, status, parent) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "switch", id: id, status: status}
    })
            .done(function (result) {
                if (parent == 0) {
                    ListAchievements(0);
                } else {

                }

            });
}