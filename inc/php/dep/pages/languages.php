<?php
include_once '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_languages');
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

header('Content-type: text/html; charset=utf-8');

if (!isset($_GET['load'])) {
    $stmt = $pdo->prepare('SELECT id, name FROM nm_languages ORDER BY id ASC');
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        echo '<div class="data-card-body">
                        <table class="fb-table-elem">
                            <thead>
                            <tr>
                                <th>' . $lang['VAR_ID'] . '</th>
                                <th>' . $lang['LANGUAGE_NAME'] . '</th>';
        if (hasPermission('edit_languages')) {
            echo '<th class="text-right">' . $lang['VAR_ACTION'] . '</th>';
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
                                        <span>' . $row['name'] . '</span>
                                    </div>
                                </td>';
            if (hasPermission('edit_languages')) {
                echo '<td class="text-right">
                                     <div class="fb-table-cell-wrapper">
                                        <button class="nm-button nm-raised nm-raised-edit" nmedit-language="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_EDIT'] . '</button>';
                if ($row['id'] != 1) {
                    echo '<button class="nm-button nm-raised nm-raised-delete" nmdelete-language="' . $row['id'] . '" style="position: relative;right: -24px;">' . $lang['VAR_DELETE'] . '</button>';
                }
                echo '</div>
                                </td>';
            }
            echo '</tr>';
        }
        echo '</tbody>
                        </table>
                    </div>';

        echo '<div class="data-card-footer-pagination">';
        if (hasPermission('edit_languages')) {
            echo '<button class="nm-button" id="create-language" modal="create-language"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Create language </button>';
        }
                        echo '</div>';


    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
        if (hasPermission('edit_languages')) {
            echo '<button class="nm-button" id="create-language" modal="create-language"><i class="material-icons" style="position:relative; vertical-align: middle; padding-right: 5px !important; color:gray !important;">add</i> Create language </button>';
        }
    }
} else if ($_GET['load'] == 'edit_lang') {
    handlePermission('edit_languages');
    $id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT language_id, variable, message FROM nm_language_messages WHERE language_id=? ORDER BY variable ASC');
    $stmt->bindParam(1, $id, 1);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-header">
                        <div class="data-card-header-title">
                            Editing language ' . getLanguageName($id) . '
                        </div>
                    </div>
        <div class="data-card-body">';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-row">
                    <div class="codefont-row-key-settings codefont">' . $row['variable'] . '</div>
                    <div class="data-card-row-value-settings">
                    <div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                    <input type="text" placeholder="Message" value="' . htmlentities($row['message']) . '" lang_variable="' . $row['variable'] . '" lang_id="' . $id . '"></div></div>
                </div>';
        }
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($_GET['load'] == 'update') {
    handlePermission('edit_languages');
    header('Content-type: text/html; charset=utf-8');

    $id = $_GET['id'];
    $variable = $_GET['variable'];
    $message = $_GET['message'];

    $message = str_replace(';v1', ' ', $message);
    $message = str_replace(';v2', '&', $message);
    $message = str_replace(';v3', '%', $message);
    $message = str_replace(';v4', '#', $message);
    $message = str_replace(';v5', '%', $message);

    try {
        $stmnt = $pdo->prepare('UPDATE nm_language_messages SET message=? WHERE variable=? AND language_id=?');
        $stmnt->bindParam(1, $message, 2);
        $stmnt->bindParam(2, $variable, 2);
        $stmnt->bindParam(3, $id, 1);
        $stmnt->execute();
    } catch (PDOException $ex) {
        echo 'Error: ' . $ex->getTraceAsString();
    }

    echo $message;
} else if ($_GET['load'] == 'create') {
    handlePermission('edit_languages');
    $name = $_GET['name'];

    $stmnt = $pdo->prepare('INSERT INTO nm_languages(name) VALUES (?)');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->execute();

    $stmnt = $pdo->prepare('SELECT id FROM nm_languages WHERE name=?');
    $stmnt->bindParam(1, $name, 2);
    $stmnt->execute();

    $id = $stmnt->fetch()['id'];

    $stmnt = $pdo->query('CREATE TEMPORARY TABLE temp_table AS SELECT language_id, variable, message, version FROM nm_language_messages WHERE language_id=1;');

    $stmnt = $pdo->prepare('UPDATE temp_table SET language_id=? WHERE language_id=1;');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();

    $stmnt = $pdo->query('INSERT INTO nm_language_messages(language_id, variable, message, version) SELECT language_id, variable, message, version FROM temp_table;');
} else if ($_GET['load'] == 'delete') {
    handlePermission('edit_languages');
    $id = $_GET['id'];

    $stmnt = $pdo->prepare('DELETE FROM nm_languages WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();

    $stmnt = $pdo->prepare('DELETE FROM nm_language_messages WHERE language_id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();

    $defaultlang = getLanguageId(getSetting('setting_language_default'));
    if ($defaultlang == null) {
        $defaultlang = 1;
    }

    $stmnt = $pdo->prepare('UPDATE nm_players SET language=? WHERE language=?');
    $stmnt->bindParam(1, $defaultlang, 1);
    $stmnt->bindParam(2, $id, 1);
    $stmnt->execute();
} else if ($_GET['load'] == 'add_message') {
    handlePermission('edit_languages');
    $variable = $_GET['variable'];
    $message = $_GET['message'];
    $version = $_GET['version'];

    $stmnt = $pdo->prepare('INSERT INTO nm_language_messages(language_id, variable, message, version) SELECT id, ?, ?, ? FROM nm_languages');
    $stmnt->bindColumn(1, $variable, 2);
    $stmnt->bindColumn(2, $message, 2);
    $stmnt->bindColumn(3, $version, 2);
    $stmnt->execute();
} else if ($_GET['load'] == 'search_message') {
    $langId = $_GET['lang'];
    $var = $_GET['q'];

    $query = '%' . $var . '%';

    $stmt = $pdo->prepare('SELECT variable, message FROM nm_language_messages WHERE language_id=? AND variable LIKE ? ORDER BY variable ASC LIMIT 10');
    $stmt->bindParam(1, $langId, 1);
    $stmt->bindParam(2, $query, 2);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-header">
                        <div class="data-card-header-title">
                            Editing language ' . getLanguageName($langId) . '
                        </div>
                    </div>
        <div class="data-card-body">';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-row">
                    <div class="codefont-row-key-settings codefont">' . $row['variable'] . '</div>
                    <div class="data-card-row-value-settings">
                    <div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                    <input type="text" placeholder="Message" value="' . htmlentities($row['message']) . '" lang_variable="' . $row['variable'] . '" lang_id="' . $langId . '"></div></div>
                </div>';
        }
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS_SEARCH'] . ' <strong>' . $var . '</strong></div></div>';
    }
}

function getMessagesLike($lang, $var) {
    global $pdo;

    $query = '%' . $var . '%';

    $stmt = $pdo->prepare('SELECT variable, value FROM nm_language_messages WHERE language_id=? AND variable LIKE ? LIMIT 10');
    $stmt->bindParam(1, $lang, 1);
    $stmt->bindParam(2, $query, 2);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-header">
                        <div class="data-card-header-title">
                            Editing language ' . getLanguageName($lang) . '
                        </div>
                    </div>
        <div class="data-card-body">';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="data-card-row">
                    <div class="codefont-row-key-settings codefont">' . $row['variable'] . '</div>
                    <div class="data-card-row-value-settings">
                    <div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                    <input type="text" placeholder="Message" value="' . htmlentities($row['message']) . '" lang_variable="' . $row['variable'] . '" lang_id="' . $lang . '"></div></div>
                </div>';
        }
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS_SEARCH'] . ' <strong>' . $var . '</strong></div></div>';
    }
}

function getSetting($name)
{
    global $pdo;
    $sql = 'SELECT value FROM nm_values WHERE variable=?';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $name, 2);
    $stmt->execute();
    $res = $stmt->fetch();
    return $res['value'];
}

function getLanguageName($id)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT name FROM nm_languages WHERE id=?');
    $stmt->bindParam(1, $id, 1);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['name'];
}

function getLanguageId($name)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT id FROM nm_languages WHERE name=?');
    $stmt->bindParam(1, $name, 2);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['name'];
}