<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_servers');
header("Content-Type: text/html; UTF-8");
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
    $stmt = $pdo->prepare('SELECT id, servername, ip, port, motd, restricted, islobby, online FROM nm_servers');
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">

                <div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['VAR_IP'] . '</th>
                                <th>' . $lang['SERVERS_PORT'] . '</th>
                                <th>' . $lang['SERVERS_MOTD'] . '</th>
                                <th>' . $lang['SERVERS_RESTRICTED'] . '</th>
                                <th>' . $lang['SERVERS_ISLOBBY'] . '</th>
                                <th>' . $lang['SERVERS_ONLINE'] . '</th>';
        if (hasPermission('edit_servers')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
        }
        echo '</tr>
                            </thead>
                            <tbody>';
        foreach ($iterator as $row) {

            $online = $row['online'] != '1' ? '<span class="label label-danger">OFFLINE</span>' : '<span class="label label-success">ONLINE</span>';
            echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $row['id'] . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $row['servername'] . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $row['ip'] . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $row['port'] . '</p></span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $colorCodes->convert(str_replace("%newline%", "\n", $row['motd']), true) . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="display: grid; position: relative; top: 0px;padding: 0 !important;"><input type="checkbox" id="' . $row['id'] . '" name="set-restricted" nmsetserverrestricted="' . $row['id'] . '" class="switch-input" ' . isChecked($row['restricted']) . ' ' . hasSwitchPermission() . '>
                                            <label for="' . $row['id'] . '" class="switch-label" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="display: grid; position: relative; top: 0px;padding: 0 !important;"><input type="checkbox" id="' . $row['servername'] . '" name="set-lobby" nmsetlobbyserver="' . $row['id'] . '" class="switch-input" ' . isChecked($row['islobby']) . ' ' . hasSwitchPermission() . '>
                                            <label for="' . $row['servername'] . '" class="switch-label" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                                        </div>
                                    </div>
                                </td>     
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        ' . $online . '
                                    </div>
                                </td>';
            if (hasPermission('edit_servers')) {
                echo '<td class="text-right">
                                        <div class="fb-table-cell-wrapper">
                                             <button class="nm-button nm-raised nm-raised-edit" nmedit-server="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                             <button class="nm-button nm-raised nm-raised-delete" nmdelete-server="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                        </div>
                                    </td>';
            }
            echo '</tr>';
        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">';
        if (hasPermission('edit_servers')) {
            echo '<button class="nm-button" id="create-server" modal="create-server"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['SERVERS_ADD_SERVER'] . ' </button>';
        }
        echo '</div>';

    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
        if (hasPermission('edit_servers')) {
            echo '<button class="nm-button" id="create-server" modal="create-server"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> ' . $lang['SERVERS_ADD_SERVER'] . ' </button>';
        }
    }
} else if ($_GET['load'] == 'add_server') {
    handlePermission('edit_servers');
    $servername = $_GET['servername'];
    $ip = $_GET['ip'];
    $port = $_GET['port'];
    $motd = $_GET['motd'] == '' ? '' : $_GET['motd'];
    $allowedversions = $_GET['allowed_versions'] == '' ? null : $_GET['allowed_versions'];
    $restricted = (isset($_GET['restricted']) && $_GET['restricted'] == 'on' ? true : false);
    $isLobby = (isset($_GET['islobby']) && $_GET['islobby'] == 'on' ? true : false);

    try {
        $stmnt = $pdo->prepare('SELECT id FROM nm_servers WHERE servername=?');
        $stmnt->bindParam(1, $servername, 2);
        $stmnt->execute();
        if ($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_servers(servername, ip, port, motd, allowed_versions, restricted, islobby) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmnt->bindParam(1, $servername, 2);
            $stmnt->bindParam(2, $ip, 2);
            $stmnt->bindParam(3, $port, 2);
            $stmnt->bindParam(4, $motd, 2);
            $stmnt->bindParam(5, $allowedversions, 2);
            $stmnt->bindParam(6, $restricted, PDO::PARAM_BOOL);
            $stmnt->bindParam(7, $isLobby, PDO::PARAM_BOOL);
            $stmnt->execute();

            die('true');
        } else {
            die('false');
        }
    } catch (PDOException $ex) {
        echo 'Error: ' . $ex->getTraceAsString();
    }

} else if ($_GET['load'] == 'delete_server') {
    handlePermission('edit_servers');
    $id = $_GET['id'];

    $stmnt = $pdo->prepare('DELETE FROM nm_servers WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $result = $stmnt->execute();
    die($result);
} else if ($_GET['load'] == 'load_server') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    echo '<div id="nmedit-server" class="modal" server="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit server with ID:' . $id . '</h3>
                <form id="nmedit-server-form" server="' . $id . '">';


    foreach ($pdo->query('SHOW COLUMNS FROM nm_servers') as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'restricted' || $item['Field'] == 'islobby' | $item['Field'] == 'online') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_servers WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-server">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-server">Save</button>
                    </div>
                </form>
            </div>
        </div>';
} else if ($_GET['load'] == 'edit_server') {
    $id = $_GET['id'];
    $servername = $_GET['servername'];
    $ip = $_GET['ip'];
    $port = $_GET['port'];
    $motd = $_GET['motd'];
    $allowedversions = $_GET['allowed_versions'] == '' ? null : $_GET['allowed_versions'];

    $stmnt = $pdo->prepare('UPDATE nm_servers SET servername=?, ip=?, port=?, motd=?, allowed_versions=? WHERE id=?');
    $stmnt->bindParam(1, $servername, 2);
    $stmnt->bindParam(2, $ip, 2);
    $stmnt->bindParam(3, $port, 2);
    $stmnt->bindParam(4, $motd, 2);
    $stmnt->bindParam(5, $allowedversions, 2);
    $stmnt->bindParam(6, $id, 1);
    $result = $stmnt->execute();
    die($result);
} else if ($_GET['load'] == 'setrestricted') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    $value = $_GET['value'];
    $stmnt = $pdo->prepare('UPDATE nm_servers SET restricted=? WHERE id=?');
    $stmnt->bindParam(1, $value, 2);
    $stmnt->bindParam(2, $id, 1);
    $result = $stmnt->execute();
    die($result);
} else if ($_GET['load'] == 'setlobby') {
    handlePermission('edit_servers');
    $id = $_GET['id'];
    $value = $_GET['value'];
    $stmnt = $pdo->prepare('UPDATE nm_servers SET islobby=? WHERE id=?');
    $stmnt->bindParam(1, $value, 2);
    $stmnt->bindParam(2, $id, 1);
    $result = $stmnt->execute();
    die($result);
}

function isChecked($variable)
{
    if ($variable == '1') {
        return 'checked';
    }
}

function hasSwitchPermission() {
    if (!hasPermission('edit_servers')) {
        return 'disabled readonly';
    }
}