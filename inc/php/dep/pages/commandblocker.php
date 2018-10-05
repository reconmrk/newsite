<?php
include '../permissions.php';
handlePermission('view_commandblocker');
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
    $stmt = $pdo->prepare('SELECT id, command, server FROM nm_blockedcommands');
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-body">';
            echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <div class="nm-input-container" style="position: relative; top: 7px; padding: 0 !important;">
                                     <input command="' . $row['id'] . '" type="text" placeholder="Value" value="' . $row['command'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-70">
                                <div class="nm-input-container" style="position: relative; top: 7px; right: -60px; padding: 0 !important;">
                                     <input server="' . $row['id'] . '" type="text" placeholder="Global" value="' . $row['server'] . '">
                                 </div>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p><button class="nm-button nm-raised nm-raised-delete" command="' . $row['id'] . '"style=" position: relative;right: -34px;">' . $lang['VAR_DELETE'] . '</button> </p>
                            </div>
                        </div>';
        }

        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($_GET['load'] == 'add') {
    handlePermission('edit_commandblocker');
    $string = $_GET['string'];

    $data = explode('!', $string);
    $stmnt = $pdo->prepare('INSERT INTO nm_blockedcommands(command,server) VALUES (?,?)');
    $stmnt->bindParam(1, $data[0], 2);
    $stmnt->bindParam(2, $data[1], 2);
    $stmnt->execute();
    die(true);
} else if ($_GET['load'] == 'remove') {
    handlePermission('edit_commandblocker');
    $id = $_GET['id'];

    $stmnt = $pdo->prepare('DELETE FROM nm_blockedcommands WHERE id=?');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
} else if ($_GET['load'] == 'update') {
    handlePermission('edit_commandblocker');
    $id = $_GET['id'];
    $word = $_GET['command'];
    $server = $_GET['server'];
    if($word == null) {
        $stmnt = $pdo->prepare('UPDATE nm_blockedcommands SET server=? WHERE id=?');
        $stmnt->bindParam(1, $server, 2);
        $stmnt->bindParam(2, $id, 1);
        $stmnt->execute();
        die(true);
    } else if($server == null) {
        $stmnt = $pdo->prepare('UPDATE nm_blockedcommands SET command=? WHERE id=?');
        $stmnt->bindParam(1, $word, 2);
        $stmnt->bindParam(2, $id, 1);
        $stmnt->execute();
        die(true);
    } else {
        die(false);
    }
}
?>