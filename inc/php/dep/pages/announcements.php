<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_announcements');
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

$stmt = $pdo->prepare('SELECT id,type,message,server,active FROM nm_announcements');
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['ANNOUNCEMENTS_TYPE'] . '</th>
                                <th style="overflow: hidden;">' . $lang['ANNOUNCEMENTS_MESSAGE'] . '</th>
                                <th>' . $lang['VAR_SERVER'] . '</th>
                                <th>' . $lang['VAR_ACTIVE'] . '</th>';
    if (hasPermission('edit_announcements')) {
        echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
    }
    echo '</tr>
                            </thead>
                            <tbody>';
    foreach ($iterator as $row) {

        $server = $row['server'];
        if (is_null($row['server']) || $row['server'] == '') {
            $server = $lang['VAR_GLOBAL'];
        }
        if ($row['active'] != '1') {
            $active = $lang['VAR_INACTIVE'];
        } else {
            $active = $lang['VAR_ACTIVE'];
        }
        echo '<tr>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $row['id'] . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . getAnnouncementType($row['type']) . '</p></span>
                                    </div>
                                </td>
                                <td class="tdmessage">
                                    <div class="fb-table-cell-wrapper">
                                        <span>' . $colorCodes->convert(str_replace("%newline%", "\n", $row['message']), true) . '</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <span><p>' . $server . '</p></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fb-table-cell-wrapper">
                                        <div class="nm-input-container" style="display: grid; position: relative; top: 0px;padding: 0 !important;"><input type="checkbox" id="' . $row['id'] . '" name="set-active" nmsetannouncementactive="' . $row['id'] . '" class="switch-input" ' . isChecked($row['active']) . '>
                                            <label for="' . $row['id'] . '" class="switch-label" style="margin-left: 0 !important;"><span style="position: relative !important;" class="toggle--on">' . $lang['VAR_TRUE'] . '</span><span style="position: relative !important;" class="toggle--off">' . $lang['VAR_FALSE'] . '</span></label>
                                        </div>
                                    </div>
                                </td>';
        if (hasPermission('edit_announcements')) {
            echo '<td class="text-right">
                                        <div class="fb-table-cell-wrapper">
                                             <button class="nm-button nm-raised nm-raised-edit" nmedit-announcement="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>
                                             <button class="nm-button nm-raised nm-raised-delete" delete-announcement="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                        </div>
                                    </td>';
        }
        echo '</tr>';
    }
    echo '</tbody>
                        </table>
                    </div>';

    echo '<div class="data-card-footer-pagination">';
    if (hasPermission('edit_announcements')) {
        echo '<button style="color: grey;" class="nm-button" id="create-announcement" modal="create-announcement"><i class="material-icons">add</i> ' . $lang['ANNOUNCEMENTS_CREATE_ANNOUNCEMENT'] . ' </button>';
    }
    echo '</div>';

} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    if (hasPermission('edit_announcements')) {
        echo '<button style="color: grey;" class="nm-button" id="create-announcement" modal="create-announcement"><i class="material-icons">add</i> ' . $lang['ANNOUNCEMENTS_CREATE_ANNOUNCEMENT'] . ' </button>';
    }
}

function getAnnouncementType($type)
{
    switch ($type) {
        case "1" :
            return 'chat all servers';
        case "2":
            return "chat severs only";
        case "3":
            return "chat servers except";
        case "4":
            return "actionbar all servers";
        case "5":
            return "actionbar servers only";
        case "6":
            return "actionbar servers except";
        case "7":
            return "title all servers";
        case "8":
            return "title servers only";
        case "9":
            return "title servers except";
    }
    return null;
}

function isChecked($variable)
{
    if ($variable == '1') {
        return 'checked';
    }
}