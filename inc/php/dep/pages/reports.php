<?php
include '../permissions.php';
handlePermission('view_reports');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
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

$total = $pdo->query('SELECT COUNT(*) FROM nm_punishments WHERE type = 20');
$total = $total->fetchColumn();
$limit = 10;
$pages = ceil($total / $limit);
$page = $_GET['p'];
$offset = ($page - 1) * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1) ? '<a reports="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
$nextlink = ($page < $pages) ? '<a reports="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
$stmt = $pdo->prepare('SELECT id, uuid, punisher, reason, time, active FROM nm_punishments WHERE type = 20 ORDER BY id DESC LIMIT ? OFFSET ?');
$stmt->bindParam(1, $limit, PDO::PARAM_INT);
$stmt->bindParam(2, $offset, PDO::PARAM_INT);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['REPORTS_REPORT'] . '</th>
                                <th>' . $lang['VAR_PLAYER'] . '</th>
                                <th>' . $lang['REPORTS_REPORTER'] . '</th>
                                <th>' . $lang['VAR_REASON'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {

        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . iconGenerator($row['active']) . '
                                        <a href="#' . $row['id'] . '" punishment="' . $row['id'] . '"> ' . $lang['REPORTS_REPORT'] . ' #' . $row['id'] . ' </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['uuid'] . '" player="' . $row['uuid'] . '">' . getName($row['uuid']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['punisher'] . '" player="' . $row['punisher'] . '">' . getName($row ['punisher']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['reason'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span> ' . date($dateformat, $row['time'] / 1000) . ' </span>
                                    </div>
                                </td>
                            </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>';

    echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                        </div>';
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}

function iconGenerator($active)
{
    if ($active == '0') {
        return '<md-icon class="md-gmp-blue-theme material-icons tl-status-outcome-1">check_circle</md-icon>';
    }
    return '<md-icon class="md-gmp-blue-theme material-icons tl-status-outcome-2">error</md-icon>';
}

function getPunishmentType($type)
{
    switch ($type) {
        case '1':
            return 'ban';
        case '2':
            return 'global ban';
        case '3':
            return 'temporary ban';
        case '4':
            return 'global temporary ban';
        case '5':
            return 'ip ban';
        case '6':
            return 'global ip ban';
        case '7':
            return 'temporary ip ban';
        case '8':
            return 'global temporary ip ban';
        case '9':
            return 'mute';
        case '10':
            return 'global mute';
        case '11':
            return 'temporary mute';
        case '12':
            return 'global temporary mute';
        case '13':
            return 'ip mute';
        case '14':
            return 'global ip mute';
        case '15':
            return 'temporary ip mute';
        case '16':
            return 'global temporary ip mute';
        case '17':
            return 'kick';
        case '18':
            return 'global kick';
        case '19':
            return 'warn';
        case '20':
            return 'report';
        default:
            return 'unknown';
    }
}

function getName($uuid)
{
    if ($uuid == 'f78a4d8d-d51b-4b39-98a3-230f2de0c670') {
        return 'CONSOLE';
    }
    global $pdo;
    $sql = 'SELECT username FROM nm_players WHERE uuid=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['username'];
}