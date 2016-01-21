<?php
include ("work.php");
include ("actions.php");
include ("display.php");
switch (filter_input(INPUT_POST, "function_to_be_called", FILTER_SANITIZE_STRING)) {
    case "associate":
        associate(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "cancel_work":
        cancel_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_work":
        change_work(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'work', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_work_status_of_action":
        change_work_status_of_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'work', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_action":
        create_action(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'reference', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_new_action":
        create_new_action(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING));
        break;
    case "create_work":
        create_work(filter_input(INPUT_POST, 'action_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_top_action":
        delete_top_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_action":
        delete_action(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "display_work_history";
        display_work_history();
        break;
    case "display_queue":
        display_queue();
        break;
    case "list_actions":
        list_actions(filter_input(INPUT_POST, 'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_current_actions":
        list_current_actions();
        break;
    case "list_work":
        list_work(filter_input(INPUT_POST, 'work', FILTER_SANITIZE_NUMBER_INT));
        break;
}
