<?php
include_once '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_permissions');
$colorCodes = new MinecraftColorcodes();
$defaultlang = 'English';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
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
$type = $_GET['load'];
$search = $_GET['q'];
$query = '%' . $search . '%';

if ($type == 'groups') {
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_groups WHERE (id LIKE ? || name LIKE ? || ladder LIKE ?)');
    $total->bindParam(1, $query, 2);
    $total->bindParam(2, $query, 2);
    $total->bindParam(3, $query, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT * FROM nm_permissions_groups WHERE (id LIKE ? || name LIKE ? || ladder LIKE ?) ORDER BY id ASC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $query, 2);
    $stmt->bindParam(2, $query, 2);
    $stmt->bindParam(3, $query, 2);
    $stmt->bindParam(4, $limit, PDO::PARAM_INT);
    $stmt->bindParam(5, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_NAME'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_LADDER'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_RANK'] . '</th>
                                <th class="text-right">' . $lang['VAR_ACTION'] . '</th>
                            </tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a href="#' . $row['id'] . '" nmgroupid1="' . $row['id'] . '">' . $row['name'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="position: relative; top: 0px; padding: 0 !important;">
                                                <input nmgroupladder="' . $row['id'] . '" type="text" placeholder="Ladder" value="' . $row['ladder'] . '">
                                            </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="position: relative; top: 0px; padding: 0 !important;">
                                                <input nmgrouprank="' . $row['id'] . '" type="number" placeholder="Rank" value="' . $row['rank'] . '">
                                            </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                     </div>
                                </td>
                </tr>';

        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                            <button style="color: grey;" class="nm-button" id="create-group" modal="create-group"><i class="material-icons">add</i> Create group </button>
                        </div>';


    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <button style="color: grey;" class="nm-button" id="create-group" modal="create-group"><i class="material-icons">add</i> Create group </button>';
    }
} else if ($type == 'users') {
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_players WHERE (uuid LIKE ? || name LIKE ? || prefix LIKE ? || suffix LIKE ?)');
    $total->bindParam(1, $query, 2);
    $total->bindParam(2, $query, 2);
    $total->bindParam(3, $query, 2);
    $total->bindParam(4, $query, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT uuid,name,prefix,suffix FROM nm_permissions_players WHERE (uuid LIKE ? || name LIKE ? || prefix LIKE ? || suffix LIKE ?) ORDER BY name DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $query, 2);
    $stmt->bindParam(2, $query, 2);
    $stmt->bindParam(3, $query, 2);
    $stmt->bindParam(4, $query, 2);
    $stmt->bindParam(5, $limit, PDO::PARAM_INT);
    $stmt->bindParam(6, $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>Player</th>
                                <th>Prefix</th>
                                <th>Suffix</th>
                                <!--<th class="text-right">Actions</th-->
                            </tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['uuid'] . '?size=20"> <a href="#' . $row['uuid'] . '" userpermissions="' . $row['uuid'] . '">' . $row['name'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes::convert($row['prefix']) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes::convert($row['suffix']) . '</span>
                                    </div>
                                </td>
                            </tr>';
        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                            <div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
}

function getUuid($name)
{
    if ($name == 'CONSOLE') {
        return '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c';
    }
    global $pdo;
    $sql = "SELECT uuid FROM nm_players WHERE username=?";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['uuid'];
}

function getName($uuid)
{
    if ($uuid == '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c') {
        return 'CONSOLE';
    }
    if ($uuid == '7387593c-e21f-30fe-ab88-10c7842331c6') {
        return '[default]';
    }
    global $pdo;
    $sql = 'SELECT username FROM nm_players WHERE uuid=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['username'];
}