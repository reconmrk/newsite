<?php
include '../permissions.php';
$type = $_GET['type'];

switch ($type) {
    case 'newplayers':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, count(id) as amount from nm_players where firstlogin > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'sessions':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(start/1000) as date) as day, count(id) as amount from nm_sessions where start > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'peak':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(LOWER(time)/1000) as date) as day, MAX(LOWER(online)) as amount from nm_serverAnalytics where LOWER(time) > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'map':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(country) AS count FROM nm_players WHERE firstlogin >=((UNIX_TIMESTAMP(CURDATE())*1000)-5184000000) GROUP BY country');
        $total = 0;
        $data = '[';
        $result = $stmnt->fetchAll();
        foreach ($result as $row) {
            if ($stmnt->rowCount() == ($total + 1)) {
                $data = $data . '{"code": "' . $row['country'] . '", "z": ' . $row['count'] . '}';
            } else {
                $data = $data . '{"code": "' . $row['country'] . '", "z": ' . $row['count'] . '},';
            }
            $total++;
        }
        $data = $data . ']';
        echo $data;
        break;
}