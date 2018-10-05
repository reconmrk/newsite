<?php
include '../dep/permissions.php';
handlePermission('view_announcements');
$defaultlang = 'English';
$jsondata = file_get_contents('../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
    }
}
if(isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../dep/languages/";
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
    include '../dep/languages/' . $defaultlang . '.php';
}
?>

<style>
    span:after {
        content: "";
        display: none;
        clear: both;
    }
    .tdmessage {
        max-width: 250px;
        word-wrap:break-word;
    }
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div id="data-card-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="create-announcement" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-announcement-form">
            <div class="nm-input-container">
                <label><?php echo $lang['ANNOUNCEMENTS_TYPE']; ?> *</label>
                <label>
                    <select name="atype" id="announcement-type" required>
                        <option value="1">chat all servers</option>
                        <option value="2">chat severs only</option>
                        <option value="3">chat servers except</option>
                        <option value="4">actionbar all servers</option>
                        <option value="5">actionbar severs only</option>
                        <option value="6">actionbar servers except</option>
                        <option value="7">title all servers</option>
                        <option value="8">title severs only</option>
                        <option value="9">title servers except</option>
                    </select>
                </label>
            </div>
            <div class="nm-input-container">
                <label><?php echo $lang['ANNOUNCEMENTS_MESSAGE']; ?></label>
                <label>
                    <input type="text" name="message" id="messageinput">
                </label>
            </div>
            <div class="nm-input-container" id="server" hidden>
                <label><?php echo $lang['VAR_SERVER']; ?></label>
                <label>
                    <input type="text" name="server" id="serverinput">
                </label>
            </div>
            <div class="nm-input-container">
                <label><?php echo $lang['VAR_ACTIVE']; ?></label>
                <label>
                    <select name="active" required>
                        <option value="1"><?php echo $lang['VAR_TRUE']; ?></option>
                        <option value="0"><?php echo $lang['VAR_FALSE']; ?></option>
                    </select>
                </label>
            </div>
            <div class="nm-input-container" style="    text-align: right;
    padding-right: 16px;
    margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;" close="create-announcement">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="create-announcement">Create</button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>

<script>
    $(document).ready(function() {
        $( "#data-card-content" ).load('../inc/php/dep/pages/announcements.php', function () {
            console.log('loaded');
        });
    });
    if (typeof isAnnouncementsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/announcements.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>