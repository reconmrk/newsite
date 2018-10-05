<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_chat');
$colorCodes = new MinecraftColorcodes();
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
$load = $_GET['load'];
if ($load == 'load') {
    $total = $pdo->query('SELECT COUNT(id) FROM nm_chat');
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a chat="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a chat="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT uuid, message, server, time FROM nm_chat ORDER BY time DESC LIMIT :limit OFFSET :offset');
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

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
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['uuid'] . '?size=20"> <a href="#' . $row['uuid'] . '" player="' . $row['uuid'] . '">' . getName($row['uuid']) . '</a></span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['message']) . '</span>
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

        echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                        </div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($load == 'clear_chat') {
    handlePermission('edit_chat');
    $stmnt = $pdo->prepare('TRUNCATE TABLE nm_chat');
    $stmnt->execute();
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