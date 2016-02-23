<?php

require_once ("achievements.php");
require_once ("work.php");
require_once ("actions.php");
require_once ("display.php");
require_once ("notes.php");
require_once ("requirements.php");
require_once ("relations.php");
switch (filter_input(INPUT_POST, "function_to_be_called", FILTER_SANITIZE_STRING)) {
    case "cancel_work":
        cancel_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_description":
        change_description(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        break;
    case "change_documentation_status":
        change_documentation_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING));
        break;
    case "change_name":
        change_name(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_name', FILTER_SANITIZE_STRING));
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
    case "change_work_status_of_achievement":
        change_work_status_of_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_work_status_of_action":
        change_work_status_of_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'work', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "complete_achievement":
        complete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "count_achievements":
        count_achievements();
        break;
    case "create_achievement":
        create_achievement(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_action":
        create_action(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'reference', FILTER_SANITIZE_NUMBER_INT));
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
    case "create_work":
        create_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_achievement":
        delete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
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
    case "delete_top_action":
        delete_top_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "display_history";
        display_history();
        break;
    case "display_queue":
        display_queue();
        break;
    case "fetch_max_work_status":
        echo json_encode(convert_work_num_to_caption("max_number"));
        break;
    case "is_it_active":
        is_it_active(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_children":
        list_children(filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_actions":
        list_actions(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
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
    case "list_notes":
        list_notes(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_relations":
        list_relations(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_requirements":
        list_requirements(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING));
        break;
    case "toggle_documentation_status":
        toggle_documentation_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "uncomplete_achievement":
        uncomplete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
}
