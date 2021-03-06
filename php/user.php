<?php
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/achievements.php");
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/config.php");
//Security concern check best practice of sending password in post. HTTPS?

function add_points_if_necessary($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (have_points_already_been_added($id)){
        return false;
    }
    $statement = $connection->prepare("update users set points=points+1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();

    $points=fetch_user_points($id);
    $statement=$connection->prepare("insert into point_log (user_id, total) values (?, $points)");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute(); 

}

function does_username_already_exist($username){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare ("select count(*) from users where active=1 and username=?");
    $statement->bindValue(1, $username, PDO::PARAM_STR);
    $statement->execute();
    $num_of_users=(int)$statement->fetchColumn();
    if ($num_of_users>1){
            //BAD There should not be more than one user with htis name.
        return true;
    }
    return (boolean)$num_of_users;
}

function does_email_already_exist($email){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare ("select count(*) from users where active=1 and email=?");
    $statement->bindValue(1, $email, PDO::PARAM_STR);
    $statement->execute();
    $num_of_users=(int)$statement->fetchColumn();
    if ($num_of_users>1){
            //BAD There should not be more than one user with this email.
            return true;
    }
    return (boolean)$num_of_users;
}

function fetch_username($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select username from users where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return (string)$statement->fetchColumn();
}

function fetch_current_user_id(){
    if (!isset($_SESSION['user'])){
        return false;
    } else if (isset($_SESSION['user'])){
        return $_SESSION['user']['id'];
    }
}
function fetch_sign_up_date($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select created from users where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_user_points($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select points from users where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return (int)$statement->fetchColumn();
}

function have_points_already_been_added($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement=$connection->prepare ("select count(*) from point_log where user_id=? order by created desc limit 1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn() == 0){
        return false;
    }
    $query = "select datediff(now(), created) from point_log where user_id=? order by created desc limit 1";
    $statement=$connection->prepare ($query);
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $days_since_last_login=$statement->fetchColumn();
    if ($days_since_last_login==0){
        return true;
    } 
    return false;
}
function login ($login, $password){
    $is_login_email = preg_match("/.+\@.+/", $login);
    if ($is_login_email==false){
        //BAD preg_match failed.
    }
    
    $user = (boolean) $is_login_email
        ?  login_with_email($login, $password)
        : login_with_username($login, $password);
    if ($user===false){
        return [false, "Invalid login information!"];
    }
    $successful_login=password_verify($password, $user->password);
    if ($successful_login){
        user_logged_in($user->id); 
        return [true, "Loggin in..."];
    } else if (!$successful_login){
        return [false, "Invalid login information."];
    }
}

function login_with_email($email, $password){
    if (!does_email_already_exist($email)){
        return false;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select id, password from users where active=1 and email=?");
    $statement->bindValue(1, $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchObject();
}

function login_with_username($username, $password){
    if (!does_username_already_exist($username)){
        return false;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select id, password from users where active=1 and username=?");
    $statement->bindValue(1, $username, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchObject();
}

function seconds_since_last_registration(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select (to_seconds(now())- to_seconds(created)) from users order by created desc limit 1");
    return (int) $statement->fetchColumn();
}


function register_user($username, $password, $email){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (does_username_already_exist($username)){
        //BAD username already exists
        return [false, "Username already exists!"];
    }
    if ($email!=NULL && does_email_already_exist($email)){
        //BAD email already exists;
        return [false, "E-mail already exists!"];
    } 
    $seconds_since_last_registration = seconds_since_last_registration();
    if ($seconds_since_last_registration<60){
        //BAD registering too soon after last registration
        return [false, "Too soon since last registration. Wait " . (SECS_BTWN_REGISTRATIONS - $seconds_since_last_registration) . " seconds then try again."];
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password_hash==false){
        //BAD password hash failed
        return [false, "Unable to create username. Please try again!"];
    }
    $statement = $connection ->prepare ("insert into users(username, password, email) values (?, ?, ?)");
    $statement->bindValue(1, $username, PDO::PARAM_STR);
    $statement->bindValue(2, $password_hash, PDO::PARAM_STR);
    $statement->bindValue(3, $email, PDO::PARAM_STR);
    $statement->execute();
    return [true, "Username created!"]; 
}

function user_logged_in($id){
    $user['id']=$id;
    $user['name']=fetch_username($id);    
    $_SESSION['user']=$user;
    add_points_if_necessary($id);//Move this to a chron when this is hosted on a server.
}

