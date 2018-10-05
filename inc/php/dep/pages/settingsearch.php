<?php
include '../permissions.php';
handlePermission('view_network');
header('Content-type: text/html; charset=utf-8');
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
$search = $_GET['q'];
$query = '%' . $search . '%';

//%or%
$stmt = $pdo->prepare('SELECT variable, value FROM nm_values WHERE variable LIKE ? LIMIT 10');
$stmt->bindParam(1, $query, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">';
    foreach ($iterator as $row) {
        if(!isBlacklisted($row['variable'])) {
            echo '<div class="data-card-row">';
            echo '<div class="codefont-row-key-settings codefont">' . $row['variable'] . '</div>';
            echo '<div class="data-card-row-value-settings">' . getInputType($row['variable'], $row['value'], $lang) . '</div>';
            echo '</div>';
        }
    }
    echo '</div>';
} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS_SEARCH'] . ' <strong>' . $search . '</strong></div></div>';
}


function isBlacklisted($word) {
    $bs = array("motd_text", "motd_icon", "maintenance_text", "maintenance_error");
    if(in_array($word, $bs)) {
        return true;
    }
    return false;
}

function getInputType($variable, $value,$lang) {
    if (startsWith($variable, 'module_') || startsWith($variable, 'setting_cache_motd_icon') ||
        endsWith($variable, '_enabled') ||
        startsWith($variable, 'setting_notify_banned_player_join')) {
        return '<div class="nm-input-container" style="position: relative; top: -10px;
    right: -16px; padding: 0 !important;"><input type="checkbox" id="' . $variable . '" name="set-name" class="switch-input" ' . isChecked($value) . '>
	<label for="' . $variable . '" class="switch-label"><span style="position: relative !important; top: -4px !important;" class="toggle--on">' . $lang['VAR_ON'] . '</span><span style="position: relative !important; top: -4px !important;" class="toggle--off">' . $lang['VAR_OFF'] . '</span></label></div>';
    } else {
        return '<div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                                        <input type="text" placeholder="Value" value="' . $value . '" variable="' . $variable . '">
                                    </div>';
    }
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function isChecked($variable) {
    if($variable == '1') {
        return 'checked';
    }
}
