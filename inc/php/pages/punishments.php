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
                <div class="data-card" style="margin-top: 20px">
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input"
                                       placeholder="<?php echo $lang['PUNISHMENTS_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
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

<div id="create-punishment" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-punishment-form">
            <div class="nm-input-container">
                <label>Punishment Type *</label>
                <label>
                    <select name="ptype" id="punishment-type" required>
                        <option value="1">Ban</option>
                        <option value="2">Global Ban</option>
                        <option value="3">Temporary Ban</option>
                        <option value="4">Global Temporary Ban</option>
                        <option value="5">IP Ban</option>
                        <option value="6">Global IP Ban</option>
                        <option value="9">Mute</option>
                        <option value="10">Global Mute</option>
                        <option value="11">Temporary Mute</option>
                        <option value="12">Global Temporary Mute</option>
                        <option value="13">IP Mute</option>
                        <option value="14">Global IP Mute</option>
                        <option value="17">Kick</option>
                        <option value="18">Global Kick</option>
                        <option value="19">Warning</option>
                    </select>
                </label>
            </div>
            <div class="nm-input-container">
                <label>Username *</label>
                <label>
                    <input type="text" name="username" required>
                </label>
            </div>
            <div class="nm-input-container" id="server">
                <label>Server *</label>
                <label>
                    <input type="text" name="server" id="serverinput" required>
                </label>
            </div>
            <div class="nm-input-container" id="expiredate">
                <label>Expiration</label>
                <label>
                    <input type="datetime-local" name="ends">
                </label>
            </div>
            <div class="nm-input-container">
                <label>Reason</label>
                <label>
                    <input type="text" name="reason" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;"
                        close="create-punishment">Cancel
                </button>
                <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                        close="create-punishment">Create
                </button>
            </div>
        </form>
    </div>
</div>
<div id="cached-modal"></div>
<script>
    $(document).ready(function () {
        $("#data-card-content").load('../inc/php/dep/pages/punishments.php?p=1', function () {
            console.log('loaded');
            document.getElementById("expiredate").style.display = "none";
        });
    });

    $("input#search-input").focus(function () {
        console.log('triggered');
        $(".data-card-header").each(function () {
            $(this).addClass('is-editing');
        });
    });

    $("input#search-input").focusout(function () {
        console.log('triggered');
        $(".data-card-header").each(function () {
            $(this).removeClass('is-editing');
        });
        if ($(this).val().length === 0) {
            $("#data-card-content").load('../inc/php/dep/pages/punishments.php?p=1', function () {
                console.log('load performed');
            });
        }
    });
    $(document).on('click', 'a', function () {
        if (this.hasAttribute('punishments')) {
            var attr = $(this).attr('page');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-card-content").load('../inc/php/dep/pages/punishments.php?p=' + attr, function () {
                    console.log('load performed');
                });
            }
        } else if (this.hasAttribute('punishmentsearch')) {
            var attr = $(this).attr('page');
            var query = $(this).attr('query');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-card-content").load('../inc/php/dep/pages/punishmentsearch.php?p=' + attr + '&q=' + query, function () {
                    console.log('punishmentsearch load performed');
                });
            }
        }
    });

    /* ---------- PLAYER SEARCH -------------- */
    $("input#search-input").keyup(function () {
        $("#data-card-content").load('../inc/php/dep/pages/punishmentsearch.php?p=1&q=' + this.value, function () {
            console.log('punishmentsearchb load performed');
        });
        if ($(this).val().length === 0) {
            console.log($(this).val().length);
            $("#data-card-content").load('../inc/php/dep/pages/punishments.php?p=1', function () {
                console.log('load performed');
            });
        }
    });

    if (typeof isPunishmentsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/punishments.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>