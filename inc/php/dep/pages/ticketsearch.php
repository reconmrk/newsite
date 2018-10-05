<?php
include '../permissions.php';
handlePermission('view_tickets');
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
$stmt = $pdo->prepare('SELECT id, creator, title, assigned_to, last_update FROM nm_tickets_tickets WHERE (title LIKE ? || creator LIKE ? || last_update LIKE ? || assigned_to LIKE ?) LIMIT 10');
$stmt->bindParam(1, $query, PDO::PARAM_STR);
$stmt->bindParam(2, $query, PDO::PARAM_STR);
$stmt->bindParam(3, $query, PDO::PARAM_STR);
$stmt->bindParam(4, $query, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['TICKET_TICKET'] . '</th>
                                <th>' . $lang['VAR_CREATOR'] . '</th>
                                <th>' . $lang['TICKET_TITLE'] . '</th>
                                <th>' . $lang['TICKET_ASSIGNEDTO'] . '</th>
                                <th>' . $lang['TICKET_LASTUPDATE'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {


        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . iconGenerator($row['active']) . '
                                        <a> ' . $lang['TICKET_TICKET'] . ' #' . $row['id'] . ' </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#player_' . $row['creator'] . '">' . getName($row['creator']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['title'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>'. assigned($row['assigned_to']) . '</span>
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
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS_SEARCH'] . ' <strong>' . $search . '</strong></div></div>';
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