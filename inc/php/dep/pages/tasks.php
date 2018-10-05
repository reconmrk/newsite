<?php
include '../permissions.php';
//handlePermission('view_network');
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
$total = $pdo->query('SELECT COUNT(*) FROM nm_tickets_tickets');
$total = $total->fetchColumn();
$active = 1;
$stmt = $pdo->prepare('SELECT id, creator, title, creation, last_update FROM nm_tickets_tickets WHERE assigned_to=? AND active=? ORDER BY priority DESC');
$stmt->bindParam(1, $_SESSION['user']->getUsername(), 2);
$stmt->bindParam(2, $active, 1);
$stmt->execute();
if ($stmt->rowCount() > 0) {

    $data = $stmt->fetchAll();

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['TICKET_TICKET'] . '</th>
                                <th>' . $lang['VAR_CREATOR'] . '</th>
                                <th>' . $lang['TICKET_TITLE'] . '</th>
                                <th>' . $lang['TICKET_CREATED'] . '</th>
                                <th>' . $lang['TICKET_LASTUPDATE'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($data as $row) {


        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . iconGenerator($row['active']) . '
                                        <a ticket="' . $row['id'] . '"> ' . $lang['TICKET_TICKET'] . ' #' . $row['id'] . ' </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['creator'] . '" player="' . $row['creator'] . '">' . getName($row['creator']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['title'] . '</span>
                                    </div>
                                </td>
                                                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' .date($dateformat, $row['creation']/1000) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' .date($dateformat, $row['last_update']/1000) . '</span>
                                    </div>
                                </td>
                            </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>';
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NOTASKS'] . '</div></div>';
}


function iconGenerator($active) {
    if($active == '0') {
        return '<md-icon class="md-gmp-blue-theme material-icons tl-status-outcome-1">check_circle</md-icon>';
    }
    return '<md-icon class="md-gmp-blue-theme material-icons tl-status-outcome-2">error</md-icon>';
}

function returnName($name) {
    if(strlen($name) == 36) {
        return getName($name);
    }
    return $name;
}

function assigned($assigned) {
    if($assigned == '') {
        return '—';
    }
    return $assigned;
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
    return $statement->fetch()['username'];
}