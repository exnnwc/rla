<?php

require_once ("achievements.php");
require_once ("display.php");
require_once ("filter.php");
require_once ("notes.php");
require_once ("requirements.php");
require_once ("relations.php");
require_once("tags.php");
require_once("todo.php");
require_once("user.php");
require_once("votes.php");
error_log("DEBUG:".$_POST['function_to_be_called']);
switch (filter_input(INPUT_POST, "function_to_be_called", FILTER_SANITIZE_STRING)) {
    case "abandon_published":
        echo json_encode(abandon_published(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)));
        break;
    case "activate_achievement":
        activate_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "associate_action":
        associate_action(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
	case "change_authorizing_status":
		change_authorizing_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_VALIDATE_BOOLEAN));
		break;
    case "cancel_todo":
        cancel_todo(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "cancel_work":
        cancel_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_description":
        change_description(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        break;
    case "change_documentation_status":
        change_documentation_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING));
        break;
    case "change_due_date":
        echo "EX";
        change_due_date(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'due', FILTER_SANITIZE_STRING));
        break;
    case "change_name":
        change_name(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_name', FILTER_SANITIZE_STRING));
        break;
    case "change_points":
        echo json_encode(change_points(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT),filter_input(INPUT_POST, 'up', FILTER_VALIDATE_BOOLEAN)));
        break;
    case "change_power":
        change_power(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_power', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_quality":
        change_quality(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_quality', FILTER_VALIDATE_BOOLEAN));
        break;
    case "change_rank":
        change_rank(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_rank', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_todo_name":
        change_todo_name(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        break;
    case "change_work_status_of_achievement":
        change_work_status_of_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_work_status_of_action":
        change_work_status_of_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'work', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "clear_due_date":
        clear_due_date(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "complete_achievement":
        complete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "complete_todo":
        complete_todo(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;

    case "count_achievements":
        echo json_encode(count_achievements());
        break;
    case "create_achievement":
        create_achievement(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_action":
        create_action(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING));
        break;
    case "create_documentation":
        create_documentation(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'documentation', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'explanation', FILTER_SANITIZE_STRING));
        break;
    case "create_new_action":
        create_new_action(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING));
        break;
    case "create_note":
        create_note(filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_relation":
        create_relation(filter_input(INPUT_POST, 'a', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'b', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_requirement":
        create_requirement(filter_input(INPUT_POST, 'required_for', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'required_by', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_tag":
        create_tag(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        break;
    case "create_todo":
        create_todo(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_vote":
        create_vote(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), 
          filter_input(INPUT_POST, 'vote', FILTER_VALIDATE_BOOLEAN),
          filter_input(INPUT_POST, 'explanation', FILTER_SANITIZE_STRING));
        break;
    case "create_work":
        create_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "deactivate_achievement":
        deactivate_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "does_username_already_exist":
        echo json_encode(does_username_already_exist(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)));
        break;
    case "remove_achievement":
        remove_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_action":
        delete_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_note":
        delete_note(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_relation":
        delete_relation(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_requirement":
        delete_requirement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_tag":
        delete_tag(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_todo":
        delete_todo(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_top_action":
        delete_top_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "display_history";
        display_history(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "display_queue":
        display_queue();
        break;
    case "fetch_required_filter_status":
        fetch_required_filter_status();
        break;
    case "get_num_of_seconds_until_authorized":
        echo json_encode((int)get_num_of_seconds_until_authorized(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)));
        break;
    case "is_it_active":
        is_it_active(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "echo_if_filter_active":
        echo_if_filter_active();
        break;
    case "list_children":
        list_children(filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_actions":
        list_actions(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_filter_tags":
        list_filter_tags();
        break;
    case "list_new_actions":
        list_new_actions();
        break;
    case "list_new_relations":
        list_new_relations();
        break;
    case "list_new_requirements":
        list_new_requirements(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_new_tags":
        list_new_tags(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_notes":
        list_notes(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_relations":
        list_relations(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_requirements":
        list_requirements(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING));
        break;
    case "list_tags":
        list_tags(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_todo":
        list_todo(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "login":
        echo json_encode(login(filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING), 
          filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
        break;
    case "own_published":
        echo json_encode(own_published(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)));
        break;
    case "publish_achievement":
        echo json_encode(publish_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)));
        break;
    case "register_user":
        echo json_encode(register_user(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING)));
        break;
    case "restore_achievement":
        restore_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "toggle_documentation_status":
        toggle_documentation_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "toggle_quality":
        toggle_quality(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "toggle_locked_status":
        toggle_locked_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "toggle_active_status":
        toggle_active_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "uncomplete_achievement":
        uncomplete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
}
