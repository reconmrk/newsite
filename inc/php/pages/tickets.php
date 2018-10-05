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
handlePermission('view_tickets');
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
                <div class="data-card" style="margin-top: 20px">
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input"
                                       placeholder="<?php echo $lang['TICKET_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
                    <div id="data-card-tickets-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="nm-input-container" style="margin-bottom: -20px;">
                    <button class="nm-button nm-raised" id="tickets-update"
                            style="position: relative;right: -24px; margin-left: -24px !important;" type="submit"
                            close="create-account">Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#data-card-tickets-content").load('../inc/php/dep/pages/tickets.php?p=1', function () {
            console.log('loaded');
        });
    });
    if (typeof isTicketsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/tickets.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>