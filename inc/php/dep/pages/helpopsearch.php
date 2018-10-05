<?php
include '../permissions.php';
handlePermission('view_helpop');
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
$total = $pdo->prepare('SELECT COUNT(*) FROM nm_helpop INNER JOIN nm_players ON nm_helpop.requester = nm_players.uuid WHERE (username LIKE ? OR nm_helpop.id LIKE ? OR requester LIKE ? OR server LIKE ? OR message LIKE ?)');
$total->bindParam(1, $query, 2);
$total->bindParam(2, $query, 2);
$total->bindParam(3, $query, 2);
$total->bindParam(4, $query, 2);
$total->bindParam(5, $query, 2);
$total->execute();
$total = $total->fetchColumn();
$limit = 10;
$pages = ceil($total / $limit);
$page = $_GET['p'];
$offset = ($page - 1) * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1) ? '<a helpopsearch="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '" query="' . $query . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
$nextlink = ($page < $pages) ? '<a helpopsearch="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '" query="' . $query . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
$stmt = $pdo->prepare('SELECT requester, message, server, time FROM nm_helpop INNER JOIN nm_players ON nm_helpop.requester = nm_players.uuid WHERE (username LIKE ? OR nm_helpop.id LIKE ? OR requester LIKE ? OR server LIKE ? OR message LIKE ?) ORDER BY nm_helpop.id DESC LIMIT ? OFFSET ?');
$stmt->bindParam(1, $query, 2);
$stmt->bindParam(2, $query, 2);
$stmt->bindParam(3, $query, 2);
$stmt->bindParam(4, $query, 2);
$stmt->bindParam(5, $query, 2);
$stmt->bindParam(6, $limit, 1);
$stmt->bindParam(7, $offset, 1);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['HELPOP_REQUESTER'] . '</th>
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
                                        <span><a href="#' . $row['requester'] . '" player="' . $row['requester'] . '"><img src="https://crafatar.com/avatars/' . $row['requester'] . '?size=20"> ' . getName($row['requester']) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['message'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['server'] . '</span>
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
