<?php
include '../permissions.php';

$type = $_GET['dataload'];

switch ($type) {
    case 'totalplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players')->fetchColumn();
        break;
    case 'todayplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE lastlogin > (UNIX_TIMESTAMP(CURDATE())*1000)')->fetchColumn();
        break;
    case 'newplayers':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin > (UNIX_TIMESTAMP(CURDATE())*1000)')->fetchColumn();
        break;
    case 'todayplaytime':
        $time = 0;
        $stmnt = $pdo->prepare('SELECT time FROM nm_sessions WHERE start > (UNIX_TIMESTAMP(CURDATE())*1000)');
        $stmnt->execute();
        foreach ($stmnt->fetchAll() as $item) {
            $time = $time + $item['time'];
        }
        echo gmdate('H:i:s', ($time) / 1000);
        break;
}