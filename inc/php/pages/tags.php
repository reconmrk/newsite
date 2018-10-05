<?php
include '../dep/permissions.php';
$defaultlang = 'English';
$jsondata = file_get_contents('../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_tags');
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
    /* Labels */
    .label-danger {
        background-color: #d9534f;
    }

    .label-info {
        background-color: #00C0EF;
    }


    .label-warning {
        background-color: #f0ad4e;
    }

    .label-success {
        background-color: #5cb85c;
    }

    .label {
        display: inline-flex;
        padding: .2em .6em .3em;
        font-size: 100%;
        cursor: pointer;
        font-weight: 700;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        border-radius: .30em;
    }
    
    span:after {
        content: "";
        display: none;
        clear: both;
    }

    /*.nm-button .material-icons {
        color: white !important;
    }*/

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
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-tag" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-tag-form">
            <div class="nm-input-container">
                <label>Tag name *</label>
                <label>
                    <input type="text" name="name" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Tag</label>
                <label>
                    <input type="text" name="tag" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Description</label>
                <label>
                    <input type="text" name="description">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Server</label>
                <label>
                    <input type="text" name="server">
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;" close="create-tag">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="create-tag">Create</button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>

<script>
    $( document ).ready(function() {
        $( "#data-card-content" ).load('../inc/php/dep/pages/tags.php?p=1p', function () {
            console.log('loaded');
        });
    });
    if (typeof isTagsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/tags.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>