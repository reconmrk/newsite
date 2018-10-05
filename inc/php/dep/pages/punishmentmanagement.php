<?php
include '../permissions.php';
handlePermission('edit_punishments');
$type = $_GET['type'];

if ($type == 'delete_punishment') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('DELETE FROM nm_punishments WHERE id=?;');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
    echo 'done delete-punishment';
} else if ($type == 'undo_punishment') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('UPDATE nm_punishments SET active=0 WHERE id=?;');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
    echo 'done unban-punishment';
} else if ($type == 'create_punishment') {
    $punishmenttype = $_GET['ptype'];
    $uuid = getUuid($_GET['username']);
    if ($uuid == null) {
        die('false');
    }
    $punisher = getUuid($_SESSION['user']->getUsername());
    if ($punisher == null) {
        die('false');
    }
    if ($uuid == $punisher) {
        die('same uuid');
    }
    $time = round(microtime(true) * 1000);
    $ends = $_GET['ends'] != "" ? strtotime($_GET['ends']) * 1000 : -1;
    $ip = getIP($uuid);
    $server = $_GET['server'] != "" ? $_GET['server'] : null;
    $reason = $_GET['reason'];

    echo $punishmenttype . ', ' . $uuid . ', ' . $punisher . ', ' . $time . ', ' . $ends . ', ' . $ip . ', ' . $server . ', ' . $reason . "\n";
    try {
        $stmnt = $pdo->prepare('INSERT nm_punishments (type, uuid, punisher, time, end, reason, ip, server, unbanner, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmnt->bindParam(1, $punishmenttype, 1);
        $stmnt->bindParam(2, $uuid, 2);
        $stmnt->bindParam(3, $punisher, 2);
        $stmnt->bindParam(4, $time, 1);
        $stmnt->bindParam(5, $ends, 1);
        $stmnt->bindParam(6, $reason, 2);
        $stmnt->bindParam(7, $ip, 2);
        $stmnt->bindParam(8, $server, 2);
        $stmnt->bindValue(9, null, 2);
        $stmnt->bindValue(10, true, PDO::PARAM_BOOL);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die($ex->getTraceAsString());
    }
}

function getUuid($name)
{
    if ($name == 'CONSOLE') {
        return 'f78a4d8d-d51b-4b39-98a3-230f2de0c670';
    }
    global $pdo;
    $statement = $pdo->prepare('SELECT uuid FROM nm_players WHERE username=?');
    $statement->bindParam(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['uuid'];
}

function getIP($uuid)
{
    global $pdo;
    $statement = $pdo->prepare('SELECT ip FROM nm_players WHERE uuid=?');
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['ip'];
}