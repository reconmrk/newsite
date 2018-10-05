<?php
include 'config.php';
include 'User.php';
include 'Group.php';
session_start();

$user = $_SESSION['user']->getUsername();

$stmnt = $pdo->prepare('SELECT notifications FROM nm_accounts WHERE username=?');
$stmnt->bindParam(1, $user, 2);
$stmnt->execute();

$notification = $stmnt->fetchAll()[0][0];

$empty = '[]';
$stmnt = $pdo->prepare('UPDATE nm_accounts SET notifications=? WHERE username=?');
$stmnt->bindParam(1, $empty, 2);
$stmnt->bindParam(2, $user, 2);
$stmnt->execute();

die($notification);