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
handlePermission('view_ticket');
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
header('Content-type: text/html; charset=utf-8');
?>

<style>
    span:after {
        content: "";
        display: none;
        clear: both;
    }

    i:after {
        content: "";
        display: none;
        clear: both;
    }

    small:after {
        content: "" !important;
        display: none !important;;
        clear: both;
    }

    div.data-card-header-title:after {
        content: "" !important;
        display: none !important;;
        clear: both;
    }

    .edit-ticket-message-button {
        color: white;
        position: inherit !important;
        float: right !important;
        margin-left: 72%;
        -webkit-transform: none !important;
        transform: none !important;
        top: 0 !important;
        pointer-events: unset !important;
    }

    .edit-ticket-message-button a:hover {
        color: black !important;
    }
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div id="data-card-content">
                <div class="col-12">
                    <div class="data-card" style="margin-top: 20px">
                        <div class="data-card-header">
                            <div class="data-card-header-title">
                                <?php echo $lang['TICKET_TICKET'] ?>
                            </div>
                        </div>
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
    var ticketid = <?php echo $_GET['id']; ?>;
    $(document).ready(function () {

        $("#data-card-content").load('../inc/php/dep/pages/ticket.php?id=' + ticketid, function (data) {
            console.log('loaded');
        });
    });
    if (typeof isTicketLoaded === 'undefined') {
        $.getScript("../inc/js/pages/ticket.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>