<?php
include_once "../inc/connect-inc.php";
include_once "../inc/filter_input_.php";

spl_autoload_register(function ($class_name){
    $inc_file = 'classes/' . $class_name . '.php';
    if (file_exists($inc_file))
        include_once $inc_file;
    else if (file_exists("../" . $inc_file))
        include_once "../" . $inc_file;
});

$mysqli = MyDB::get_db_instance();

$u = new UserSessions();