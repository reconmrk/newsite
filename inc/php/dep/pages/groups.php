<?php
include '../permissions.php';
handlePermission('edit_accounts');
$defaultlang = 'English';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
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
$stmt = $pdo->prepare('SELECT id, name FROM nm_accountgroups');
$stmt->execute();
if ($stmt->rowCount() > 0) {

    echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                             <thead>
                                <tr>
                                    <th>' . $lang['GROUPANDACCOUNTS_GROUP'] . '</th>
                                    <th class="text-right">' . $lang['VAR_ACTION'] . '</th>
                                </tr>
                                </thead>
                            <tbody>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


        echo '<tr>
                                <td>
                                        <div class="fb-table-cell-wrapper">
                                            <span>' . $row['name'] . '</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fb-table-cell-wrapper text-right">
                                            <button class="nm-button" edit-group="' . $row['id'] . '" style="position: relative;right: -24px;">Edit</button> <button class="nm-button nm-raised-delete" delete-group="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>
                                        </div>
                                    </td>
                            </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>';

    echo '<div class="data-card-footer text-right">
                            <button class="nm-button" id="create-group" modal="create-group"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Add Group </button>
                        </div>';


} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}