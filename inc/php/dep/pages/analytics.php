<?php
include '../permissions.php';
handlePermission('view_analytics');

$type = $_GET['dataload'];

switch ($type) {
    case 'mnp':
        //echo $pdo->query('SELECT COUNT(id) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP((LAST_DAY(NOW())+INTERVAL 1 DAY)-INTERVAL 1 MONTH)*1000')->fetchColumn();
		echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 MONTH)*1000')->fetchColumn();
        break;
    case 'wnp':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000')->fetchColumn();
        break;
    case 'dnp':
        echo $pdo->query('SELECT COUNT(*) FROM nm_players WHERE firstlogin >= UNIX_TIMESTAMP(CURDATE())*1000')->fetchColumn();
        break;
    case 'mrp':
        //$stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP((LAST_DAY(NOW())+INTERVAL 1 DAY)-INTERVAL 1 MONTH)*1000');
		$stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 MONTH)*1000');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'wrp':
        $stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'drp':
        $stmnt = $pdo->query('SELECT COUNT(DISTINCT(uuid)) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE())*1000');
        $res = $stmnt->fetchAll()[0];
        echo $res[0];
        break;
    case 'dpt':
        $stmnt = $pdo->query('SELECT SUM(time) FROM nm_sessions WHERE start >= (UNIX_TIMESTAMP(CURDATE())*1000)');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);;
        break;
    case 'wpt':
        $stmnt = $pdo->query('SELECT SUM(time) FROM nm_sessions WHERE start >= UNIX_TIMESTAMP(CURDATE()- INTERVAL 1 WEEK)*1000');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);
        break;
    case 'tpt':
        $stmnt = $pdo->query('SELECT SUM(playtime) FROM nm_players');
        echo formatMilliseconds($stmnt->fetchAll()[0][0]);
        break;
    case 'versions':
        $stmnt2 = $pdo->query('SELECT DISTINCT COUNT(version) FROM nm_players');
        $total = $stmnt2->fetchAll()[0][0];

        echo '<table class="data-table"><thead><tr class="header"><th>Version</th><th>Players</th><th>Population</th></tr></thead><tbody>';
        $stmnt = $pdo->query('SELECT DISTINCT(version) as version, count(version) AS count FROM nm_players GROUP BY version ORDER BY count DESC ');
        foreach ($stmnt->fetchAll() as $item) {
            echo '<tr class="row"">';
            echo '<td>' . getVersion($item['version']) . '</td>';
            echo '<td>' . $item['count'] . '</td>';
            echo '<td>' . number_format((($item['count'] / $total) * 100), 2, '.', ' ') . '%</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        break;
    case 'onlineplayers':
        $result = '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT TIME, ONLINE FROM nm_serverAnalytics');
        foreach ($stmnt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . $row['TIME'] . ',' . floatval($row['ONLINE']) . ']';
            } else {
                $result = $result . '[' . $row['TIME'] . ',' . floatval($row['ONLINE']) . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
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
}

function getVersion($id)
{
    switch ($id) {
        case '401':
            return '1.13.1';
        case '393':
            return '1.13';
        case '340':
            return '1.12.2';
        case '338':
            return '1.12.1';
        case '335':
            return '1.12';
        case '316':
            return '1.11.x';
        case '315':
            return '1.11';
        case '210':
            return '1.10 - 1.10.2';
        case '110':
            return '1.9.3 - 1.9.4';
        case '109':
            return '1.9.2';
        case '108':
            return '1.9.1';
        case '107':
            return '1.9';
        case '47':
            return '1.8 - 1.8.9';
        case '5':
            return '1.7.6 - 1.7.10';
        case '4':
            return '1.7.2 - 1.7.5';
        default:
            return 'snapshot';
    }
}

function formatMilliseconds($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%u:%02u:%02u';
    $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
    return rtrim($time, '0');
}