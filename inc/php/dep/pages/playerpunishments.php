<?php
include '../permissions.php';
handlePermission('view_players');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
    }
    if($variable == 'date-format') {
        $dateformat = $value;
    }
}
if(isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../languages/";
    if(is_dir($langdir)) {
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
$stmt = $pdo->prepare('SELECT id, type, punisher, reason, time FROM nm_punishments WHERE uuid=? AND type != 20 AND type != 21 ORDER BY id DESC');
$stmt->bindParam(1, $uuid, 2);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_PUNISHMENT'] . '</th>
                                <th>' . $lang['VAR_PUNISHER'] . '</th>
                                <th>' . $lang['VAR_REASON'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {

        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a punishment="' . $row['id'] . '">' . getPunishmentType($row['type']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a player="' . $row['punisher'] . '">' . getName($row ['punisher']) . '</a></span>
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
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}


function getPunishmentType($type) {
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

function getName($uuid) {
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