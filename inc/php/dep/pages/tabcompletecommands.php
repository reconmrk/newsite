<?php
include '../permissions.php';
handlePermission('view_tabcompletecommands');
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
$stmt = $pdo->prepare('SELECT id, command, permission FROM nm_tabcompletecommands');
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">';
    foreach ($iterator as $row) {
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $row['command'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $row['permission'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p><button class="nm-button nm-raised nm-raised-delete" tabcompletecommand="' . $row['id'] . '"style=" position: relative;right: -34px;">' . $lang['VAR_DELETE'] . '</button> </p>
                            </div>
                        </div>';
    }

    echo '</div>';
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}
?>