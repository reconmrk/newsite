<?php
include '../permissions.php';
handlePermission('view_players');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$timeformat = 'h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
    }
    if ($variable == 'time-format') {
        $timeformat = $value;
    }
}
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../languages/";
    if (is_dir($langdir)) {
        if ($dh = opendir($langdir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $result = $langdir . $file;
                    switch ($language) {
                        case $filename:
                            include $result;
                            break;
                    }
                }
            }
            closedir($dh);
        }
    }
} else {
    include '../languages/' . $defaultlang . '.php';
}
$uuid = $_GET['uuid'];

$total = $pdo->prepare('SELECT COUNT(*) FROM nm_sessions WHERE uuid=?');
$total->bindParam(1, $uuid, 2);
$total->execute();
$total = $total->fetchColumn();
$limit = 10;
$pages = ceil($total / $limit);
$page = $_GET['p'];
$offset = ($page - 1) * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1) ? '<a playersessions="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
$nextlink = ($page < $pages) ? '<a playersessions="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
$stmt = $pdo->prepare('SELECT start, end, time, ip, version FROM nm_sessions WHERE uuid=? ORDER BY id DESC LIMIT ? OFFSET ?');
$stmt->bindParam(1, $uuid, 2);
$stmt->bindParam(2, $limit, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $res = $stmt->fetchAll();
    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_STARTED'] . '</th>
                                <th>' . $lang['VAR_ENDED'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>';
    if (hasPermission('show_ip')) {
        echo '<th>' . $lang['PLAYER_IPADDRESS'] . '</th>';
    }
    echo '<th>' . $lang['VAR_VERSION'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($res as $row) {


        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . date($dateformat, $row['start'] / 1000) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                         <span>' . date($dateformat, $row['end'] / 1000) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . formatMilliseconds($row['time']) . '</span>
                                    </div>
                                </td>';
        if (hasPermission('show_ip')) {
            echo '<td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['ip'] . '</span>
                                    </div>
                                </td>';
        }
        echo '<td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . getVersion($row['version']) . '</span>
                                    </div>
                                </td>
                            </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>

        <div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                        </div>';
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty"><th>' . $lang['TEXT_NORESULTS'] . '</th></div></div>';
}

function formatMilliseconds($milliseconds)
{
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