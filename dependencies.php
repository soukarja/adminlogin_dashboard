<?php


require 'settings/config.php';
require 'settings/sql.php';
require 'settings/settings.php';


$roles = [
    "Admin",
    "Editor"
];
$table_name = "users";
$auth_directory = 'auth';
$auth_url = $siteLink . $auth_directory;
$success_link = $siteLink . 'admin';

session_start();

$session_key = "logged_in";
$session_key_userID = "logged_in_id";

function isLoggedIn()
{
    global $session_key;
    if (isset($_SESSION[$session_key]) && $_SESSION[$session_key] === true) {
        return true;
    }

    return false;
}


function logout()
{
    global $auth_url, $_SESSION;
    session_start();

    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session.
    session_destroy();


    
}


setPHPErrors(false);
