<?php
include '../permissions.php';
handlePermission('view_punishments');
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

$search = $_GET['q'];
$query = '%' . $search . '%';

//%or%
$total = $pdo->prepare('SELECT COUNT(*) FROM nm_punishments INNER JOIN nm_players ON nm_punishments.uuid = nm_players.uuid OR nm_punishments.punisher = nm_players.uuid WHERE type = 20 AND (nm_punishments.id LIKE ? OR nm_punishments.uuid LIKE ? OR nm_punishments.punisher LIKE ? OR username LIKE ? OR nm_punishments.ip LIKE ? OR reason LIKE ?)');
$total->bindParam(1, $query, 2);
$total->bindParam(2, $query, 2);
$total->bindParam(3, $query, 2);
$total->bindParam(4, $query, 2);
$total->bindParam(5, $query, 2);
$total->bindParam(6, $query, 2);
$total->execute();
$total = $total->fetchColumn();
$limit = 10;
$pages = ceil($total / $limit);
$page = $_GET['p'];
$offset = ($page - 1) * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1) ? '<a reportsearch="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '" query="' . $query . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
$nextlink = ($page < $pages) ? '<a reportsearch="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '" query="' . $query . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
$stmt = $pdo->prepare('SELECT nm_punishments.id, nm_punishments.uuid, nm_punishments.punisher, reason, time FROM nm_punishments INNER JOIN nm_players ON nm_punishments.uuid = nm_players.uuid OR nm_punishments.punisher = nm_players.uuid WHERE type = 20 && (nm_punishments.id LIKE ? OR nm_punishments.uuid LIKE ? OR nm_punishments.punisher LIKE ? OR username LIKE ? OR nm_punishments.ip LIKE ? OR reason LIKE ?) ORDER BY time DESC LIMIT ? OFFSET ?');
$stmt->bindParam(1, $query, 2);
$stmt->bindParam(2, $query, 2);
$stmt->bindParam(3, $query, 2);
$stmt->bindParam(4, $query, 2);
$stmt->bindParam(5, $query, 2);
$stmt->bindParam(6, $query, 2);
$stmt->bindParam(7, $limit, 1);
$stmt->bindParam(8, $offset, 1);
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
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS_SEARCH'] . ' <strong>' . $search . '</strong></div></div>';
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
    $sql = "SELECT username FROM nm_players WHERE uuid=:uuid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['username'];
}
