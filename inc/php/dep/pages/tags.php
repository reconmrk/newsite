<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_tags');
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

if (!isset($_GET['load'])) {
    $total = $pdo->query('SELECT COUNT(*) FROM nm_tags');
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmtags="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmtags="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id, name, tag, description, server FROM nm_tags ORDER BY id ASC LIMIT ? OFFSET ?');
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
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['TAGS_TAG_NAME'] . '</th>
                                <th>' . $lang['TAGS_TAG_TAG'] . '</th>
                                <th>' . $lang['TAGS_TAG_DESCRIPTION'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>';
        if (hasPermission('edit_tags')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
                            echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '' || $row['server'] == 'all') {
                $server = '<span class="label label-success">' . $lang['VAR_ALL'] . '</span>';
            }

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['name'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['tag'], true) . '</span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['description'], true) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $server . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_tags')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-tag="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-tag="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                     </div>
                                </td>';
            }
                            echo '</tr>';
        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>';
        if (hasPermission('edit_tags')) {
            echo '<button class="nm-button" id="create-tag" modal="create-tag"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['TAGS_TAG_CREATE'] . ' </button>';
        }
                        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
        if (hasPermission('edit_tags')) {
            echo '<button class="nm-button" id="create-tag" modal="create-tag"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['TAGS_TAG_CREATE'] . ' </button>';
        }
    }
} else if ($_GET['load'] == 'create_tag') {
    handlePermission('edit_tags');
    $name = $_GET['name'];
    $tag = $_GET['tag'];
    $description = $_GET['description'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('INSERT INTO nm_tags(name,tag,description,server) VALUES (?,?,?,?)');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->bindParam(2, $tag, 2);
    $stmnt->bindParam(3, $description, 2);
    $stmnt->bindParam(4, $server, 2);
    $stmnt->execute();
} else if ($_GET['load'] == 'delete_tag') {
    handlePermission('edit_tags');
    $id = $_GET['id'];

    $stmnt = $pdo->prepare('DELETE FROM nm_tags WHERE id=?');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
} else if ($_GET['load'] == 'load_tag') {
    handlePermission('edit_tags');
    $id = $_GET['id'];

    echo '<div id="nmedit-tag" class="modal" tag="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit tag with ID:' . $id . '</h3>
                <form id="nmedit-tag-form" tag="' . $id . '">';


    foreach ($pdo->query('SHOW COLUMNS FROM nm_tags') as $item) {
        if ($item['Field'] == 'id') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_tags WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-tag">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-tag">Save</button>
                    </div>
                </form>
            </div>
        </div>';
} else if ($_GET['load'] == 'edit_tag') {
    $id = $_GET['id'];
    $name = $_GET['name'];
    $tag = $_GET['tag'];
    $description = $_GET['description'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('UPDATE nm_tags SET name=?, tag=?, description=?, server=? WHERE id=?');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->bindParam(2, $tag, 2);
    $stmnt->bindParam(3, $description, 2);
    $stmnt->bindParam(4, $server, 2);
    $stmnt->bindParam(5, $id, 1);
    $result = $stmnt->execute();
    die($result);
}
?>