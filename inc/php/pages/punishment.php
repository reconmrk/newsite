<?php
include '../dep/permissions.php';
$defaultlang = 'English';
$jsondata = file_get_contents('../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_punishments');
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../dep/languages/";
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
    include '../dep/languages/' . $defaultlang . '.php';
}
?>
<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['PUNISHMENTS_PUNISHMENT'] ?>
                        </div>
                    </div>
                    <div id="data-punishment-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cached-modal"></div>
<script>
    $(document).ready(function () {
        var id = <?php echo $_GET['loadid']; ?>;
        console.log(id);
        $("#data-punishment-content").load('../inc/php/dep/pages/punishment.php?id=' + id, function (data) {
            console.log('loaded');
        });
    });
    if (typeof isPunishmentLoaded === 'undefined') {
        $.getScript("../inc/js/pages/punishment.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed. (colorcodes)');
        });
    }
</script>