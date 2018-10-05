<?php
session_start();
include 'TicketUser.php';
include '../../../inc/php/dep/config.php';
ob_start();

function getUuid($name)
{
    if ($name == 'CONSOLE') {
        return '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c';
    }
    global $pdo;
    $sql = 'SELECT uuid FROM nm_players WHERE username=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['uuid'];
}

try {
    if (isset($_POST['username'])) {
        $uuid = getUuid($_POST['username']);
        if ($uuid == '') {
            die('false');
        }
        $password = $_POST['password'];
        $salt = '#5JEet8LWTvzx4i$_';
        $hash = hash('sha256', $salt . $password);
        $sql = 'SELECT uuid FROM nm_tickets_accounts WHERE uuid=? AND password=?';
        $stmnt = $pdo->prepare($sql);
        $stmnt->bindParam(1, $uuid, PDO::PARAM_STR);
        $stmnt->bindParam(2, $hash, PDO::PARAM_STR);
        $stmnt->execute();
        if ($stmnt->rowCount() != 0) {
            $_SESSION['ticket_user'] = new TicketUser($_POST['username'], $uuid);
            if (isset($_POST['remember'])) {
                setcookie('ticket_username', $uuid, time() + 604800, '/');
                setcookie('ticket_password', $hash, time() + 604800, '/');
            }
            echo 'true';
        } else {
            echo 'false';
        }
    }
} catch (Exception $e) {
    die('error');
}