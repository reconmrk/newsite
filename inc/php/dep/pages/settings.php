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

$load = $_GET['load'];

if ($load == 'plugin') {
    //$stmt = $pdo->prepare('SELECT variable, value FROM nm_values ORDER BY (SUBSTR(LOWER(variable), 1, 1)="m") DESC');
    $stmt = $pdo->prepare('SELECT variable, value FROM nm_values ORDER BY variable ASC');
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        echo '<div class="data-card-body">';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isBlacklisted($row['variable'])) {
                echo '<div class="data-card-row">';
                echo '<div class="codefont-row-key-settings codefont">' . $row['variable'] . '</div>';
                echo '<div class="data-card-row-value-settings">' . getInputType($row['variable'], $row['value'], $lang) . '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($load == 'webbie') {
    echo '<div class="data-card-body">';
    $jsondata = file_get_contents('../../../json/settings.json');
    $websettings = json_decode($jsondata,true);
    foreach ($websettings as $variable => $value) {
        echo '<div class="data-card-row">';
        echo '<div class="codefont-row-key-settings codefont">' . $variable . '</div>';
        echo '<div class="data-card-row-value-settings">' . getInputType($variable, $value, $lang) . '</div>';
        echo '</div>';
    }
}


function isBlacklisted($word)
{
    $bs = array("motd_text", "motd_icon", "motd_description", "maintenance_text", "maintenance_error");
    return in_array($word, $bs);
}

function getInputType($variable, $value, $lang)
{
    if (startsWith($variable, 'module_') || startsWith($variable, 'setting_cache_motd_icon') ||
        endsWith($variable, '_enabled') ||
        startsWith($variable, 'setting_notify_banned_player_join') ||
        startsWith($variable, 'setting_punishments_ipban_ban_ip_only')) {
        return '<div class="nm-input-container" style="position: relative; top: -10px; right: -16px; padding: 0 !important;"><input type="checkbox" id="' . $variable . '" name="set-name" class="switch-input" ' . isChecked($value) . '>
	            <label for="' . $variable . '" class="switch-label"><span style="position: relative !important; top: -4px !important;" class="toggle--on">' . $lang['VAR_ON'] . '</span><span style="position: relative !important; top: -4px !important;" class="toggle--off">' . $lang['VAR_OFF'] . '</span></label></div>';
    } else if (startsWith($variable, 'default-language')) {
        $result = '<div class="nm-input-container" style="position: relative; top: 7px; right: -30px; padding: 0 !important;">
                <select id="default-language" value=' . $value . '>' .
                    $input = 'https://api.github.com/repos/ChimpGamer/NetworkManager/contents/Webbie/languages';
                    $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
                    $context  = stream_context_create($options);
                    $content = file_get_contents($input, false, $context);
                    $content = json_decode($content, true);
                    foreach ($content as $file) {
                        $select = '';
                        if($value == basename($file['name'],'.php')) {
                            $select = 'selected';
                        }
                        $result .= '<option ' . $select . '  variable="' . $variable . '">' . basename($file['name'],'.php') . '</option>';
                    }
                    $result .= '</select>
                </div>';
                    return $result;
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

function isChecked($variable)
{
    if ($variable == '1') {
        return 'checked';
    }
}