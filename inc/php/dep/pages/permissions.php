<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_permissions');
$colorCodes = new MinecraftColorcodes();
$defaultlang = 'English';
$dateformat = 'm-d-y H:i:s';
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
$type = $_GET['load'];

if ($type == 'groups') {
    $total = $pdo->query('SELECT COUNT(*) FROM nm_permissions_groups');
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmgroups="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id, name, ladder, rank FROM nm_permissions_groups ORDER BY id ASC LIMIT ? OFFSET ?');
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
                                <th>' . $lang['PERMISSIONS_GROUP_NAME'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_LADDER'] . '</th>
                                <th>' . $lang['PERMISSIONS_GROUP_RANK'] . '</th>
                                <!--<th>' . $lang['PERMISSIONS_ISDEFAULT'] . '</th>-->';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right" > ' . $lang['VAR_ACTION'] . ' </th >';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><a nmgroupid1="' . $row['id'] . '">' . $row['name'] . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['ladder'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['rank'] . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
        if (hasPermission('edit_permissions')) {
            echo '<button class="nm-button" id="create-group" modal="create-group"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_CREATE'] . ' </button>';
        }
                        echo '</div>';

    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
        if (hasPermission('edit_permissions')) {
            echo '<button class="nm-button" id="create-group" modal="create-group"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_CREATE'] . ' </button>';
        }
    }
    die();
} else if ($type == 'grouppermissions') {
    $groupid = $_GET['groupid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_grouppermissions WHERE groupid=?');
    $total->bindParam(1, $groupid, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a groupid="' . $groupid . '" nmgrouppermissions="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a groupid="' . $groupid . '" nmgrouppermissions="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id,permission,world,server,expires FROM nm_permissions_grouppermissions WHERE groupid=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $groupid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    echo '<div style="background-color: #F5F5F5;border-bottom: 1px solid rgba(0,0,0,0.12);"><a href="#' . $groupid . '" nmgroupmembers="' . $groupid . '" style="bottom: 2px" class="nm-button nm-raised">' . $lang['PERMISSIONS_GROUP_MEMBER_LIST'] . '</a><a href="#' . $groupid . '" nmgroupprefixes="' . $groupid . '" style="bottom: 2px" class="nm-button nm-raised">Prefixes</a><a href="#' . $groupid . '" nmgroupsuffixes="' . $groupid . '" style="bottom: 2px" class="nm-button nm-raised">Suffixes</a><a href="#' . $groupid . '" nmgroupparents="' . $groupid . '" style="bottom: 2px" class="nm-button nm-raised">Parents</a></div>';
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['PERMISSIONS_PERMISSION'] . '</th>
                                <th>' . $lang['PERMISSIONS_PERMISSIONS_WORLD'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['PERMISSIONS_EXPIRES'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . ' </th>';
        }
        echo '</tr>
                            </tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            $world = '<span class="label label-warning">' . $row['world'] . '</span>';
            if ($row['world'] == '') {
                $world = '<span class="label label-success">' . $lang['VAR_ALL'] . '</span>';
            }
            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '') {
                $server = '<span class="label label-success">' . $lang['VAR_ALL'] . '</span>';
            }
            $expiredate = date_create($row['expires']);
            $expires = '<span class="label label-warning">' . date_format($expiredate, $dateformat) . '</span>';
            if (is_null($row['expires'])) {
                $expires = '<span class="label label-success">' . $lang['VAR_NEVER'] . '</span>';
            }
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                    <span>' . $row['permission'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . $world . '
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . $server . '
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . $expires . '
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" groupid="' . $groupid . '" nmedit-group-permission="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                             <button class="nm-button nm-raised nm-raised-delete" groupid="' . $groupid . '" nmdelete-group-permission="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackgroups="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">' . $lang['VAR_BACK'] . '</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-permission="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_PERMISSION_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <a class="nm-button nm-raised" nmbackgroups="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">' . $lang['VAR_BACK'] . '</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-permission="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_PERMISSION_ADD'] . ' </button>';
        }
    }
    die();
} else if ($type == 'groupparents') {
    $groupid = $_GET['groupid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_groupparents WHERE groupid=?');
    $total->bindParam(1, $groupid, 1);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a groupid="' . $groupid . '" nmgroupparents="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a groupid="' . $groupid . '" nmgroupparents="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT nm_permissions_groups.id, nm_permissions_groups.name FROM nm_permissions_groupparents INNER JOIN nm_permissions_groups ON nm_permissions_groupparents.parentgroupid = nm_permissions_groups.id WHERE nm_permissions_groupparents.groupid=? ORDER BY nm_permissions_groupparents.id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $groupid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>Group</th>
                                <th>ParentGroup</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </tr>
                            </thead>
                            <tbody>';

        $groupname = getGroupNameByID($groupid);
        foreach ($iterator as $row) {

            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $groupname . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['name'] . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-delete" nmdelete-groupparent="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-parent="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_PARENT_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">No results could be displayed</div></div>
        <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-parent="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_PARENT_ADD'] . ' </button>';
        }
    }
    die();
} else if ($type == 'groupprefixes') {
    $groupid = $_GET['groupid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_groupprefixes WHERE groupid=?');
    $total->bindParam(1, $groupid, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a groupid="' . $groupid . '" prefixes="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a groupid="' . $groupid . '" prefixes="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id, server, prefix FROM nm_permissions_groupprefixes WHERE groupid=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $groupid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['PERMISSIONS_PREFIX'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {
            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '') {
                $server = '<span class="label label-success">' . $lang['VAR_ALL'] . '</span>';
            }
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['prefix'], true) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $server . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" groupid="' . $groupid . '" nmedit-group-prefix="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                        <button class="nm-button nm-raised nm-raised-delete" groupid="' . $groupid . '" nmdelete-group-prefix="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-prefix="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_PREFIX_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">No results could be displayed</div></div>
        <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-prefix="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_PREFIX_ADD'] . ' </button>';
        }
    }
    die();
} else if ($type == 'groupsuffixes') {
    $groupid = $_GET['groupid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_groupsuffixes WHERE groupid=?');
    $total->bindParam(1, $groupid, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a groupid="' . $groupid . '" prefixes="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a groupid="' . $groupid . '" prefixes="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id, server, suffix FROM nm_permissions_groupsuffixes WHERE groupid=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $groupid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['PERMISSIONS_SUFFIX'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {
            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '') {
                $server = '<span class="label label-success">' . $lang['VAR_ALL'] . '</span>';
            }
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['suffix'], true) . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $server . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" groupid="' . $groupid . '" nmedit-group-suffix="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                            <button class="nm-button nm-raised nm-raised-delete" groupid="' . $groupid . '" nmdelete-group-suffix="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-suffix="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_SUFFIX_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">No results could be displayed</div></div>
        <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">Back</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-suffix="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_SUFFIX_ADD'] . ' </button>';
        }
    }
    die();
} else if ($type == 'groupmembers') {
    $groupid = $_GET['groupid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_playergroups WHERE groupid=?');
    $total->bindParam(1, $groupid, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a groupid="' . $groupid . '" nmgroupmembers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a groupid="' . $groupid . '" nmgroupmembers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id,playeruuid,groupid,server,expires FROM nm_permissions_playergroups WHERE groupid=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $groupid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_PLAYER'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['PERMISSIONS_EXPIRES'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {
            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '') {
                $server = '<span class="label label-success">ALL</span>';
            }
            $expiredate = date_create($row['expires']);
            $expires = '<span class="label label-warning">' . date_format($expiredate, $dateformat) . '</span>';
            if ($row['expires'] == '') {
                $expires = '<span class="label label-success">Never</span>';
            }
            $username = '<span>' . getName($row['playeruuid']) . '</span>';
            if (is_null(getName($row['playeruuid']))) {
                $username = '<span class="label label-danger">Unknown</span>';
            }
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['playeruuid'] . '?size=20"> <a href="#' . $row['playeruuid'] . '" player="' . $row['playeruuid'] . '">' . $username . '</a></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $server . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $expires . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-delete" groupid="' . $groupid . '" nmdelete-group-member="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">' . $lang['VAR_BACK'] . '</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-group-member="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_MEMBER_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <a class="nm-button nm-raised" nmbackgroupperms="' . $groupid . '" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit" close="create-account">' . $lang['VAR_BACK'] . '</a>
        <button style="right: -40px;" class="nm-button" add-group-member="' . $groupid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_GROUP_MEMBER_ADD'] . ' </button>';
    }
    die();
} else if ($type == 'users') {
    $total = $pdo->query('SELECT COUNT(*) FROM nm_permissions_players');
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmusers="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT uuid,name,prefix,suffix FROM nm_permissions_players ORDER BY name DESC LIMIT :limit OFFSET :offset');
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_PLAYER'] . '</th>
                                <th>' . $lang['PERMISSIONS_PREFIX'] . '</th>
                                <th>' . $lang['PERMISSIONS_SUFFIX'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
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
                                        <span>' . $colorCodes->convert($row['prefix']) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert($row['suffix']) . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-permplayer="' . $row['uuid'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
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
                            <div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
    die();
} else if ($type == 'userpermissions') {
    $uuid = $_GET['uuid'];
    $total = $pdo->prepare('SELECT COUNT(*) FROM nm_permissions_playerpermissions WHERE playeruuid=?');
    $total->bindParam(1, $uuid, 2);
    $total->execute();
    $total = $total->fetchColumn();
    $limit = 10;
    $pages = ceil($total / $limit);
    $page = $_GET['p'];
    $offset = ($page - 1) * $limit;
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);
    $prevlink = ($page > 1) ? '<a nmuserpermissionspage="' . ($page - 1) . '" useruuid="' . $uuid . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
    $nextlink = ($page < $pages) ? '<a nmuserpermissionspage="' . ($page - 1) . '" useruuid="' . $uuid . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
    $stmt = $pdo->prepare('SELECT id,playeruuid,permission,world,server,expires FROM nm_permissions_playerpermissions WHERE playeruuid=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bindParam(1, $uuid, 1);
    $stmt->bindParam(2, $limit, 1);
    $stmt->bindParam(3, $offset, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['VAR_PLAYER'] . '</th>
                                <th>' . $lang['PERMISSIONS_PERMISSION'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['PERMISSIONS_PERMISSIONS_WORLD'] . '</th>
                                <th>' . $lang['PERMISSIONS_EXPIRES'] . '</th>';
        if (hasPermission('edit_permissions')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {
            $world = '<span class="label label-warning">' . $row['world'] . '</span>';
            if ($row['world'] == '') {
                $world = '<span class="label label-success">All</span>';
            }
            $server = '<span class="label label-warning">' . $row['server'] . '</span>';
            if ($row['server'] == '') {
                $server = '<span class="label label-success">All</span>';
            }
            $expiredate = date_create($row['expires']);
            $expires = '<span class="label label-warning">' . date_format($expiredate, $dateformat) . '</span>';
            if (is_null($row['expires'])) {
                $expires = '<span class="label label-success">Never</span>';
            }
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $row['id'] . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><img src="https://crafatar.com/avatars/' . $row['playeruuid'] . '?size=20"> <a href="#' . $row['playeruuid'] . '" player="' . $row['playeruuid'] . '">' . getName($row['playeruuid']) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="position: relative; top: 0px; padding: 0 !important;">
                                                <input nmuserpermission="' . $row['id'] . '" uuid="' . $uuid . '" type="text" placeholder="Permission" value="' . $row['permission'] . '">
                                            </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $server . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $world . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $expires . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_permissions')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-delete" nmuseruuid="' . $row['playeruuid'] . '" nmdelete-player-permission="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
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
                            <a class="nm-button nm-raised" nmbackusers="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">' . $lang['VAR_BACK'] . '</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-user-permission="' . $uuid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_PERMISSION_ADD'] . ' </button>';
        }
                            echo '<div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>
        <a class="nm-button nm-raised" nmbackusers="" style="position: relative;top: 2px;left: 55px; margin-left: -48px !important;" type="submit">' . $lang['VAR_BACK'] . '</a>';
        if (hasPermission('edit_permissions')) {
            echo '<button style="right: -40px;" class="nm-button" add-user-permission="' . $uuid . '"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['PERMISSIONS_PERMISSION_ADD'] . ' </button>';
        }
    }
    die();
} else if ($type == 'setdefault') {
    $group = $_GET['group'];
    $status = $_GET['value'];

    if ($status) {
        $stmnt = $pdo->prepare('INSERT INTO nm_permissions_playergroups (playeruuid, groupid) VALUES (?, ?)');
        $stmnt->bindParam(1, $status, 5);
        $stmnt->bindParam(2, $group, 2);
        $stmnt->execute();
    }

    $stmnt = $pdo->prepare('UPDATE nm_permissions_groups SET isdefault=? WHERE id=?');
    $stmnt->bindParam(1, $status, 5);
    $stmnt->bindParam(2, $group, 2);
    $stmnt->execute();
    die('true');
} else if ($type == 'delete_group') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groups WHERE id=?;');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $stmnt = $pdo->prepare('SELECT groupid FROM nm_permissions_grouppermissions WHERE groupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_grouppermissions WHERE groupid=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
    }
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_playergroups WHERE groupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupparents WHERE groupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupparents WHERE parentgroupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupprefixes WHERE groupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupsuffixes WHERE groupid=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    echo 'done delete-group';

} else if ($type == 'delete_group_permission') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare("DELETE FROM nm_permissions_grouppermissions WHERE id=?");
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    echo 'done delete-permission';
} else if ($type == 'delete_group_prefix') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_groupprefixes WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupprefixes WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        echo 'done delete-group-prefix';
    } else {
        echo "could not find id: $id";
    }
} else if ($type == 'delete_group_suffix') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_groupsuffixes WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_groupsuffixes WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        echo 'done delete-group-suffix';
    } else {
        echo "could not find id: $id";
    }
} else if ($type == 'delete_group_member') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_playergroups WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_playergroups WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        echo 'done delete-group-member';
    } else {
        echo "could not find id: $id";
    }
} else if ($type == 'delete_group_parent') {
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_playerparent WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_playerparent WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        echo 'done delete-group-parent';
    } else {
        echo "could not find id: $id";
    }
} else if ($type == 'delete_player') {
    $uuid = $_GET['uuid'];
    $stmnt = $pdo->prepare('SELECT uuid FROM nm_permissions_players WHERE uuid=?');
    $stmnt->bindParam(1, $uuid, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare("DELETE FROM nm_permissions_players WHERE uuid=?");
        $stmnt->bindParam(1, $uuid, 2);
        $stmnt->execute();
        echo 'done delete-player';
    } else {
        echo "could not find uuid: $uuid";
    }
} else if ($type == 'delete_player_permission') {
    $uuid = $_GET['uuid'];
    $id = $_GET['id'];
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_playerpermissions WHERE id=?');
    $stmnt->bindParam(1, $id, PDO::PARAM_INT);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $stmnt = $pdo->prepare('DELETE FROM nm_permissions_playerpermissions WHERE id=?');
        $stmnt->bindParam(1, $id, PDO::PARAM_INT);
        $stmnt->execute();
        echo 'done delete-player-permission';
    } else {
        echo "could not find id: $id";
    }
} else if ($type == 'create_group') {
    $name = $_GET['name'];
    $ladder = $_GET['ladder'];
    $rank = $_GET['rank'];

    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_groups WHERE name=?');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt = $pdo->prepare('INSERT INTO nm_permissions_groups (name, ladder, rank) VALUES (?, ?, ?)');
        $stmnt->bindParam(1, $name, 2);
        $stmnt->bindParam(2, $ladder, 2);
        $stmnt->bindParam(3, $rank, PDO::PARAM_INT);
        $stmnt->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'add_groupperm') {
    $groupid = $_GET['groupid'];
    $permission = $_GET['permission'];
    $server = $_GET['server'];
    $world = $_GET['world'];
    $expires = $_GET['expires'] != "" ? strftime('%Y-%m-%d %H:%M:%S', strtotime($_GET['expires'])) : NULL;

    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_grouppermissions WHERE groupid=? AND permission=? AND server=? AND world=?');
    $stmnt->bindParam(1, $groupid, 1);
    $stmnt->bindParam(2, $permission, 2);
    $stmnt->bindParam(3, $server, 2);
    $stmnt->bindParam(4, $world, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt = $pdo->prepare('INSERT INTO nm_permissions_grouppermissions (groupid, permission, server, world, expires) VALUES (?, ?, ?, ?, ?)');
        $stmnt->bindParam(1, $groupid, 1);
        $stmnt->bindParam(2, $permission, 2);
        $stmnt->bindParam(3, $server, 2);
        $stmnt->bindParam(4, $world, 2);
        $stmnt->bindParam(5, $expires, 2);
        $stmnt->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'update_groupperm') {
    $id = $_GET['id'];
    $permission = $_GET['permission'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_grouppermissions SET permission=? WHERE id=?');
    $stmnt->bindParam(1, $permission, 2);
    $stmnt->bindParam(2, $id, 2);
    $stmnt->execute();
    die('true');
} else if ($type == 'update_userperm') {
    $id = $_GET['id'];
    $permission = $_GET['permission'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_playerpermissions SET permission=? WHERE id=?');
    $stmnt->bindParam(1, $permission, 2);
    $stmnt->bindParam(2, $id, 2);
    $stmnt->execute();
    die('true');
} else if ($type == 'add_groupmember') {
    $groupid = $_GET['groupid'];
    $username = $_GET['username'];
    $server = $_GET['server'];

    $uuid = getUuid($username);

    $stmnt = $pdo->prepare('SELECT uuid FROM nm_permissions_playergroups WHERE groupid=? AND playeruuid=?');
    $stmnt->bindParam(1, $groupid, 2);
    $stmnt->bindParam(2, $uuid, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt2 = $pdo->prepare('INSERT INTO nm_permissions_playergroups (groupid, playeruuid, server) VALUES (?, ?, ?)');
        $stmnt2->bindParam(1, $groupid, 2);
        $stmnt2->bindParam(2, $uuid, 2);
        $stmnt2->bindParam(3, $server, 2);
        $stmnt2->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'add_groupparent') {
    $groupid = $_GET['groupid'];
    $parent = $_GET['parent'];

    echo $groupid;
    echo $parent;
    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_groupparents WHERE groupid=? AND parentgroupid=?');
    $stmnt->bindParam(1, $groupid, 1);
    $stmnt->bindParam(2, $parent, 1);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt2 = $pdo->prepare('INSERT INTO nm_permissions_groupparents(groupid, parentgroupid) VALUES (?, ?)');
        $stmnt2->bindParam(1, $groupid, 1);
        $stmnt2->bindParam(2, $parent, 1);
        $stmnt2->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'add_groupprefix') {
    $groupid = $_GET['groupid'];
    $prefix = $_GET['prefix'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('SELECT * FROM nm_permissions_groupprefixes WHERE groupid=? AND prefix=?');
    $stmnt->bindParam(1, $groupid, 2);
    $stmnt->bindParam(2, $prefix, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt2 = $pdo->prepare('INSERT INTO nm_permissions_groupprefixes (groupid, prefix, server) VALUES (?, ?, ?)');
        $stmnt2->bindParam(1, $groupid, 2);
        $stmnt2->bindParam(2, $prefix, 2);
        $stmnt2->bindParam(3, $server, 2);
        $stmnt2->execute();


        die('true');
    } else {
        die('false');
    }
} else if ($type == 'add_groupsuffix') {
    $groupid = $_GET['groupid'];
    $suffix = $_GET['suffix'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('SELECT * FROM nm_permissions_groupsuffixes WHERE groupid=? AND suffix=?');
    $stmnt->bindParam(1, $groupid, 1);
    $stmnt->bindParam(2, $suffix, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt2 = $pdo->prepare('INSERT INTO nm_permissions_groupsuffixes (groupid, suffix, server) VALUES (?, ?, ?)');
        $stmnt2->bindParam(1, $groupid, 1);
        $stmnt2->bindParam(2, $suffix, 2);
        $stmnt2->bindParam(3, $server, 2);
        $stmnt2->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'add_userperm') {
    $uuid = $_GET['useruuid'];
    $permission = $_GET['permission'];
    $server = $_GET['server'];
    $world = $_GET['world'];
    $expires = $_GET['expires'] != "" ? strftime('%Y-%m-%d %H:%M:%S', strtotime($_GET['expires'])) : NULL;

    $stmnt = $pdo->prepare('SELECT id FROM nm_permissions_playerpermissions WHERE playeruuid=? AND permission=? AND server=? AND world=?');
    $stmnt->bindParam(1, $uuid, 2);
    $stmnt->bindParam(2, $permission, 2);
    $stmnt->bindParam(3, $server, 2);
    $stmnt->bindParam(4, $world, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() == 0) {
        $stmnt = $pdo->prepare('INSERT INTO nm_permissions_playerpermissions (playeruuid, permission, server, world, expires) VALUES (?, ?, ?, ?, ?)');
        $stmnt->bindParam(1, $uuid, 2);
        $stmnt->bindParam(2, $permission, 2);
        $stmnt->bindParam(3, $server, 2);
        $stmnt->bindParam(4, $world, 2);
        $stmnt->bindParam(5, $expires, 2);
        $stmnt->execute();

        die('true');
    } else {
        die('false');
    }
} else if ($type == 'edit_user') {
    $uuid = $_GET['useruuid'];
    $prefix = $_GET['prefix'];
    $suffix = $_GET['suffix'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_players SET prefix=?, suffix=? WHERE uuid=?');
    $stmnt->bindParam(1, $prefix, 2);
    $stmnt->bindParam(2, $suffix, 2);
    $stmnt->bindParam(3, $uuid, 2);
    $stmnt->execute();
} else if ($type == 'edit_group') {
    $groupid = $_GET['groupid'];
    $name = $_GET['name'];
    $ladder = $_GET['ladder'];
    $rank = $_GET['rank'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_groups SET name=?, ladder=?, rank=? WHERE id=?');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->bindParam(2, $ladder, 2);
    $stmnt->bindParam(3, $rank, 1);
    $stmnt->bindParam(4, $groupid, 1);
    $stmnt->execute();
    die('true');
} else if ($type == 'edit_group_permission') {
    $id = $_GET['id'];
    $permission = $_GET['permission'];
    $world = $_GET['world'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_grouppermissions SET permission=?, world=?, server=? WHERE id=?');
    $stmnt->bindParam(1, $permission, 2);
    $stmnt->bindParam(2, $world, 2);
    $stmnt->bindParam(3, $server, 2);
    $stmnt->bindParam(4, $id, 1);
    $stmnt->execute();
    die('true');
} else if ($type == 'edit_group_prefix') {
    $id = $_GET['id'];
    $prefix = $_GET['prefix'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_groupprefixes SET prefix=?, server=? WHERE id=?');
    $stmnt->bindParam(1, $prefix, 2);
    $stmnt->bindParam(2, $server, 2);
    $stmnt->bindParam(3, $id, 1);
    $stmnt->execute();
    die('true');
} else if ($type == 'edit_group_suffix') {
    $id = $_GET['id'];
    $suffix = $_GET['suffix'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('UPDATE nm_permissions_groupsuffixes SET suffix=?, server=? WHERE id=?');
    $stmnt->bindParam(1, $suffix, 2);
    $stmnt->bindParam(2, $server, 2);
    $stmnt->bindParam(3, $id, 1);
    $stmnt->execute();
    die('true');
} else if ($type == 'load_add_group_permission') {
    $id = $_GET['id'];
    echo '<div id="add-group-permission" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add permission to group</h3>
                <form id="add-group-permission-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <label>Group ID</label>
                        <input type="text" name="groupid" value="' . $id . '" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Permission</label>
                        <input type="text" name="permission" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Server</label>
                        <input type="text" name="server">
                    </div>
                    <div class="nm-input-container">
                        <label>World</label>
                        <input type="text" name="world">
                    </div>
                    <div class="nm-input-container">
                        <label>Expires</label>
                        <input type="datetime-local" name="expires">
                    </div>             
                    
                    <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-group-permission">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" groupid="' . $id . '" close="add-group-permission">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_add_group_member') {
    $id = $_GET['id'];
    echo '<div id="add-group-member" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add member to group</h3>
                <form id="add-group-member-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <label>Group ID</label>
                        <input type="text" name="groupid" value="' . $id . '" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Server</label>
                        <input type="text" name="server">
                    </div>';

    echo '<div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-group-member">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="add-group-member">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_add_group_parent') {
    $id = $_GET['id'];
    echo '<div id="add-group-parent" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add parent to group ' . $id . '</h3>
                <form id="add-group-parent-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <select name="parent">';
    foreach (getGroups() as $group):
        if ($group['id'] == $id) {
            continue;
        }
        echo '<option value="' . $group['id'] . '">' . $group['name'] . '</option>';
    endforeach;
    echo '</select>
                    </div>';

    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-group-parent">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="add-group-parent">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_add_group_prefix') {
    $id = $_GET['id'];
    echo '<div id="add-group-prefix" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add prefix to group</h3>
                <form id="add-group-prefix-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <label>Group ID</label>
                        <input type="text" name="groupid" value="' . $id . '" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Prefix</label>
                        <input type="text" name="prefix" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Server</label>
                        <input type="text" name="server">
                    </div>';

    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-group-prefix">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="add-group-prefix">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_add_group_suffix') {
    $id = $_GET['id'];
    echo '<div id="add-group-suffix" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add suffix to group</h3>
                <form id="add-group-suffix-form" group="' . $id . '">
                    <div class="nm-input-container">
                        <label>Group ID</label>
                        <input type="text" name="groupid" value="' . $id . '" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Suffix</label>
                        <input type="text" name="suffix" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Server</label>
                        <input type="text" name="server">
                    </div>';

    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-group-suffix">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="add-group-suffix">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_group') {
    $id = $_GET['id'];
    echo '<div id="nmedit-group" class="modal" group="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit group with ID:' . $id . '</h3>
                <form id="nmedit-group-form" group="' . $id . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_permissions_groups`") as $item) {
        if ($item['Field'] == 'id') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_permissions_groups WHERE id=?');
        $stmnt->bindParam(1, $id, 2);
        $stmnt->execute();
        $row = $stmnt->fetch();
        if ($item['Field'] != 'rank') {
            echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
        } else {
            echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="number" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
        }
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-group">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-group">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_add_user_permission') {
    $useruuid = $_GET['uuid'];
    echo '<div id="add-user-permission" class="modal" useruuid="' . $useruuid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Add permission to user</h3>
                <form id="add-user-permission-form" useruuid="' . $useruuid . '">
                    <div class="nm-input-container">
                        <label>User UUID</label>
                        <input type="text" name="useruuid" value="' . $useruuid . '" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Permission</label>
                        <input type="text" name="permission" required>
                    </div>
                    <div class="nm-input-container">
                        <label>Server</label>
                        <input type="text" name="server">
                    </div>
                    <div class="nm-input-container">
                        <label>World</label>
                        <input type="text" name="world">
                    </div>
                    <div class="nm-input-container">
                        <label>Expires</label>
                        <input type="datetime-local" name="expires">
                    </div>                    

                    <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="add-user-permission">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" useruuid="' . $useruuid . '" close="add-user-permission">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_edit_permplayer') {
    $useruuid = $_GET['uuid'];
    echo '<div id="nmedit-permplayer" class="modal" useruuid="' . $useruuid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit user with UUID: ' . $useruuid . '</h3>
                <form id="nmedit-permplayer-form" useruuid="' . $useruuid . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_permissions_players`") as $item) {
        if ($item['Field'] == 'uuid' || $item['Field'] == 'name') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_permissions_players WHERE uuid=?');
        $stmnt->bindParam(1, $useruuid, 2);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-permplayer">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-permplayer">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_edit_group_permission') {
    $id = $_GET['id'];
    $groupid = $_GET['groupid'];
    echo '<div id="nmedit-group-permission" class="modal" permid="' . $id . '" group="' . $groupid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit Permission ID: ' . $id . '</h3>
                <form id="nmedit-group-permission-form" permid="' . $id . '" group="' . $groupid . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_permissions_grouppermissions`") as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'groupid' || $item['Field'] == 'expires') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_permissions_grouppermissions WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-group-permission">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-group-permission">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_edit_group_prefix') {
    $id = $_GET['id'];
    $groupid = $_GET['groupid'];
    echo '<div id="nmedit-group-prefix" class="modal" prefixid="' . $id . '" group="' . $groupid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit Prefix ID: ' . $id . '</h3>
                <form id="nmedit-group-prefix-form" prefixid="' . $id . '" group="' . $groupid . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_permissions_groupprefixes`") as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'groupid') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_permissions_groupprefixes WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-group-prefix">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-group-prefix">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($type == 'load_edit_group_suffix') {
    $id = $_GET['id'];
    $groupid = $_GET['groupid'];
    echo '<div id="nmedit-group-suffix" class="modal" suffixid="' . $id . '" group="' . $groupid . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit Suffix ID: ' . $id . '</h3>
                <form id="nmedit-group-suffix-form" suffixid="' . $id . '" group="' . $groupid . '">';


    foreach ($pdo->query("SHOW COLUMNS FROM `nm_permissions_groupsuffixes`") as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'groupid') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_permissions_groupsuffixes WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-group-suffix">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-group-suffix">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
}

function getGroupNameByID($groupid)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT name FROM nm_permissions_groups WHERE id=?');
    $stmnt->bindParam(1, $groupid, PDO::PARAM_INT);
    $stmnt->execute();
    return $stmnt->fetch()['name'];
}

function getUuid($name)
{
    if ($name == 'CONSOLE') {
        return 'f78a4d8d-d51b-4b39-98a3-230f2de0c670';
    }
    global $pdo;
    $sql = 'SELECT uuid FROM nm_players WHERE username=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['uuid'];
}

function getName($uuid)
{
    if ($uuid == 'f78a4d8d-d51b-4b39-98a3-230f2de0c670') {
        return 'CONSOLE';
    } else if ($uuid == '7387593c-e21f-30fe-ab88-10c7842331c6') {
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

function getGroups()
{
    global $pdo;
    $sql = 'SELECT id, name FROM nm_permissions_groups';
    $stmnt = $pdo->query($sql);
    return $stmnt->fetchAll();
}