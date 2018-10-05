<?php
include '../permissions.php';
handlePermission('view_players');
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
$uuid = $_GET['uuid'];
$stmt = $pdo->prepare('SELECT id, punisher, reason, time FROM nm_punishments WHERE type=21 AND uuid=? ORDER BY id DESC');
$stmt->bindParam(1, $uuid, 2);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['NOTES_NOTE'] . '</th>
                                <th>' . $lang['VAR_PUNISHER'] . '</th>
                                <th>' . $lang['VAR_REASON'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>
                                <th class="text-right">' . $lang['VAR_ACTION'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {

        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <a punishment="' . $row['id'] . '"> ' . $lang['NOTES_NOTE'] . ' #' . $row['id'] . ' </a>
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
                                <td class="text-right">
                                        <div class="fb-table-cell-wrapper">
                                             <button class="nm-button nm-raised nm-raised-delete" nmdelete-playernote="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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

function getName($uuid)
{
    if ($uuid == '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c') {
        return 'CONSOLE';
    }
    global $pdo;
    $sql = 'SELECT username FROM nm_players WHERE uuid=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetch()['username'];
}