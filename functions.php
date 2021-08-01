<?php
require 'dependencies.php';

function errorOccurred($mssg = "Error Occurred")
{
    echo $mssg;
    exit;
}

if (!isset($_POST['action']))
    errorOccurred();

$action = trim($_POST['action']);

if ($action == "newuser") {

    if (!isset($_POST['username']) || empty(trim($_POST['username'])))
        errorOccurred("Username is required");
    if (!isset($_POST['password']) || empty(trim($_POST['password'])))
        errorOccurred("Password is required");

    if (!isset($_POST['id']))
        errorOccurred();

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $uid = $_POST['id'];

    $role = "1";
    if (isset($_POST['role']) && !empty(trim($_POST['role'])))
        $role = $_POST['role'];

    if (!executeQuery("DESCRIBE `$table_name`")) {
        $createTable = new CreateTable($table_name);
        $createTable->createColumn('id', "INT", 11, "", false, true, true);
        $createTable->createColumn('username', "TEXT");
        $createTable->createColumn('password', "TEXT");
        $createTable->createColumn('role', "TEXT");
        $createTable->createColumn('status', "TEXT", 0, "active");
        if (!$createTable->createTable())
            errorOccurred("Error Occurred while creating table");
    }

    


    if ($uid == 0) {
        $insertRow = new InsertRow($table_name);
        $insertRow->addColumnData('username', $username);
        $insertRow->addColumnData('password', password_hash($password, PASSWORD_DEFAULT));
        $insertRow->addColumnData('role', $role);
        if ($insertRow->insertRow())
            errorOccurred("success");
        else
            errorOccurred();
    } else {

        if (countRows("SELECT * FROM `$table_name` WHERE `username` = '$username' AND id <> $uid") > 0) {
            errorOccurred("This Username is already taken");
        }

        $updateRow = new UpdateRow($table_name);
        $updateRow->addColumnData('username', $username);
        $updateRow->addColumnData('password', password_hash($password, PASSWORD_DEFAULT));
        if ($updateRow->updateRow("WHERE id = $uid"))
            errorOccurred("success");
        else
            errorOccurred();
    }
} else if ($action == "login") {
    if (!isset($_POST['username']) || empty(trim($_POST['username'])))
        errorOccurred("Username is required");
    if (!isset($_POST['password']) || empty(trim($_POST['password'])))
        errorOccurred("Password is required");

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // print_r(fetchData("SELECT * FROM `$table_name` WHERE `username` = '$username'"));
    // echo "<br><br><br>";
    // echo password_verify($password, fetchData("SELECT * FROM `$table_name` WHERE `username` = '$username'")["password"]);
    // echo "<br><br><br>";

    // if (countRows("SELECT * FROM `$table_name` WHERE `username` = '$username'") > 0) {
    $data = fetchData("SELECT * FROM `$table_name` WHERE `username` = '$username'");

    if (isset($data["password"]) && password_verify($password, $data["password"])) {

        if ($data['status'] == 'disabled')
            errorOccurred("This account is temporarily disabled.<br>Please contact your administrator.");
        $_SESSION[$session_key] = true;
        $_SESSION[$session_key_userID] = $data["id"];
        errorOccurred('success');
    } else {
        errorOccurred("Invalid Username/Password");
    }
} else if ($action == "copyLink") {


    if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
        $username = trim($_POST['username']);
        if (countRows("SELECT * FROM `$table_name` WHERE `username` = '$username'") > 0) {
            errorOccurred("Error: This Username is already taken");
        }
    } else
        $username = "";


    $role = "1";
    if (isset($_POST['role']) && !empty(trim($_POST['role'])))
        $role =  $_POST['role'];

    $insertRow = new InsertRow($table_name);
    $insertRow->addColumnData('username', $username);
    $insertRow->addColumnData('password', '');
    $insertRow->addColumnData('role', $role);
    $insertRow->insertRow();

    $id = fetchData("SELECT * FROM `$table_name` ORDER BY id DESC")["id"];
    echo $auth_url . "/new-user/invite/" . $id;
    exit;
} else if ($action == "deleteLink") {
    if (!isset($_POST['id']))
        errorOccurred();

    $uid = $_POST['id'];

    if (executeQuery("DELETE FROM `$table_name` WHERE id = '$uid'")) {
        errorOccurred('success');
    } else {
        errorOccurred("Error Occurred while deleting link");
    }
} else if ($action == "changepass") {

    if (!isset($_POST['password']) || empty(trim($_POST['password'])))
        errorOccurred("Password is required");

    if (!isset($_POST['id']))
        errorOccurred();


    $password = $_POST['password'];
    $uid = $_POST['id'];

    $data = fetchData("SELECT * FROM `$table_name` WHERE id = '$uid'");
    if (password_verify($password, $data['password'])) {
        errorOccurred("New password cannot be the same as the current one");
    }

    $updateRow = new UpdateRow($table_name);
    $updateRow->addColumnData('password', password_hash($password, PASSWORD_DEFAULT));
    if ($updateRow->updateRow("WHERE id = $uid")) {
        logout();
        errorOccurred("success");
    } else
        errorOccurred();
} else {
    errorOccurred();
}
