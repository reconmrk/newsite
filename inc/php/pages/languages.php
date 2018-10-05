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
handlePermission('view_network');
header('Content-Type: text/html; charset=UTF-8');
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
                    <div id="nm-languages-search-bar" class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input"
                                       placeholder="Search message by variable" autocomplete="off">
                            </form>
                        </div>
                    </div>
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

<div id="create-language" class="modal">
    <div class="modal-content" style="width: 600px !important;">
        <form id="create-language-form">
            <div class="nm-input-container">
                <label>Language name *</label>
                <label>
                    <input type="text" name="name" required>
                </label>
            </div>
            <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                <button class="nm-button" type="button" style="position: relative;right: -24px;" close="create-language">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="create-language">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
    var langId = 1;
    $(document).ready(function () {
        $("#data-card-content").load('../inc/php/dep/pages/languages.php', function () {
            console.log('load performed');
        });
        document.getElementById("nm-languages-search-bar").style.display = "none";
    });

    var searchinput = $("input#search-input");

    searchinput.focus(function () {
        console.log('triggered');
        $(".data-card-header").each(function () {
            $(this).addClass('is-editing');
        });
    });

    searchinput.focusout(function () {
        console.log('triggered');
        $(".data-card-header").each(function () {
            $(this).removeClass('is-editing');
        });
        if ($(this).val().length === 0) {
            $("#data-card-content").load('../inc/php/dep/pages/languages.php?load=edit_lang&id=' + langId, function () {
                console.log('load performed');
            });
        }
    });

    /* ---------- Settings SEARCH -------------- */
    searchinput.keyup(function () {
        $("#data-card-content").load('../inc/php/dep/pages/languages.php?load=search_message&lang=' + langId + '&q=' + this.value, function () {
            console.log('load performed');
        });
        if ($(this).val().length === 0) {
            console.log($(this).val().length);
            $("#data-card-content").load('../inc/php/dep/pages/languages.php?load=edit_lang&id=' + langId, function () {
                console.log('load performed');
            });
        }
    });

    if (typeof isLanguagesLoaded === 'undefined') {
        $.getScript("../inc/js/pages/languages.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>