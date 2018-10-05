<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_chat');
$colorCodes = new MinecraftColorcodes();
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
$search = $_GET['q'];
$query = '%' . $search . '%';

//%or%
$stmt = $pdo->prepare('SELECT username, nm_chat.uuid, message, server, time FROM nm_chat INNER JOIN nm_players ON nm_chat.uuid = nm_players.uuid WHERE (username LIKE ? || nm_chat.uuid LIKE ? || message LIKE ? || server LIKE ? || nm_chat.ip LIKE ?) ORDER BY time DESC LIMIT 10');
$stmt->bindParam(1, $query, PDO::PARAM_STR);
$stmt->bindParam(2, $query, PDO::PARAM_STR);
$stmt->bindParam(3, $query, PDO::PARAM_STR);
$stmt->bindParam(4, $query, PDO::PARAM_STR);
$stmt->bindParam(5, $query, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['PLAYER_USERNAME'] . '</th>
                                <th>' . $lang['VAR_MESSAGE'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {
        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['uuid'] . '?size=20"> <a href="#' . $row['uuid'] . '" player="' . $row['uuid'] . '">' . getName($row['uuid']) . '</a></span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert(($row['message'])) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row ['server'] . '</span>
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

function getName($uuid) {
    if ($uuid == '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c') {
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