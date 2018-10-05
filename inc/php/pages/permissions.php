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
handlePermission('view_permissions');
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
    .switch-label {
        padding: 1px 0 2px 44px;
    }
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-groups-input" class="data-card-input" placeholder="<?php echo $lang['PERMISSIONS_GROUP_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
                    <div id="data-groups-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-users-input" class="data-card-input" placeholder="<?php echo $lang['PERMISSIONS_PLAYER_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
                    <div id="data-users-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-group" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-group-form">
            <div class="nm-input-container">
                <label>Group name *</label>
                <label>
                    <input type="text" name="name" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Ladder *</label>
                <label>
                    <input type="text" name="ladder" required>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Rank *</label>
                <label>
                    <input type="number" name="rank" min="1" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;" close="create-group">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="create-group">Create</button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>

<script>
    $(document).ready(function () {
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
            console.log('groups loaded');
        });
        $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1', function () {
            console.log('users loaded');
        });
    });
    if (typeof isPermissionsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/permissions.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>