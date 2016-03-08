<?php
require_once("config.php");
//Security concern check best practice of sending password in post. HTTPS?

function register_user($username, $password, $email){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (does_username_already_exist($username)){
        //BAD username already exists
        return;
    }
    if (does_email_already_exist($email)){
        //BAD email already exists;
        return;
    } 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password_hash==false){
        //BAD password hash failed
        return;
    }
    $statement = $connection ->prepare ("insert into users(username, password, email) values (?, ?, ?)");
    $statement->bindValue(1, $username, PDO::PARAM_STR);
    $statement->bindValue(2, $password_hash, PDO::PARAM_STR);
    $statement->bindValue(3, $email, PDO::PARAM_STR);
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

function login ($login, $password){
    var_dump (preg_match("/.+\@.+/", $login));
    $user = preg_match("/.+\@.+/", $login)
        ? login_with_email($login, $password)
        : login_with_username($login, $password);
    if (password_verify($user->password, $hash)){
        user_logged_in($user->id); 
    }
}

function login_with_email($email, $password){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select id, password from users where active=1 and email=?");
    $statement->bindValue(1, $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchObject();
}

function login_with_username($username, $password){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select id, password from users where active=1 and username=?");
    $statement->bindValue(1, $username, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchObject();
}

