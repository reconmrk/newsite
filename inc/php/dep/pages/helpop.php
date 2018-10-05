<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
if(!isset($_GET['load'])) {
    try {
        $total = $pdo->query('SELECT COUNT(*) FROM nm_helpop');
        $total = $total->fetchColumn();
        $limit = 10;
        $pages = ceil($total / $limit);
        $page = $_GET['p'];
        $offset = ($page - 1) * $limit;
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);
        $prevlink = ($page > 1) ? '<a helpop="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
        $nextlink = ($page < $pages) ? '<a helpop="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
        $stmt = $pdo->prepare('SELECT id, requester, message, server, time FROM nm_helpop ORDER BY id DESC LIMIT ? OFFSET ?');
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
                                <th>' . $lang['HELPOP_REQUESTER'] . '</th>
                                <th>' . $lang['VAR_MESSAGE'] . '</th>
								<th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['VAR_TIME'] . '</th>';
            if (hasPermission('edit_helpop')) {
                echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
            }
                            echo '</tr>
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
                                </td>';
                if (hasPermission('edit_helpop')) {
                    echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-helpop="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                     </div>
                                </td>';
                }
                            echo '</tr>';
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

    } catch (Exception $ex) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
} else if($_GET['load'] == 'delete_helpop') {
    handlePermission('edit_helpop');
    $id = $_GET['id'];

    $stmnt = $pdo->prepare('DELETE FROM nm_helpop WHERE id=?');
    $stmnt->bindParam(1, $id, 2);
    $result = $stmnt->execute();
    die($result);
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