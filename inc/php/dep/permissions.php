<?php
include_once 'config.php';
include_once 'Group.php';
include_once 'User.php';
session_start();
if (!isset($_SESSION['user'])) {
    die('denied');
}

function hasPermission($permission)
{
    return $_SESSION['user']->getGroup()->hasPermission($permission);
}

function handlePermission($permission)
{
    if (!hasPermission($permission)) {
        die('no permission');
    }
}

if (isset($_GET['haspermission'])) {
    die(hasPermission($_GET['haspermission']));
}