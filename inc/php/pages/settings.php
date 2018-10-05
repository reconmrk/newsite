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
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <div class="nm-input-container" style="margin-bottom: -20px;">
                                <button id="Webbie" style="bottom: 2px;right: 55px" class="nm-button nm-raised">
                                    WebInterface
                                </button>
                                <button id="Plugin" style="bottom: 2px;right: 65px" class="nm-button nm-raised">Plugin
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="nm-settings-search-bar" class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input"
                                       placeholder="<?php echo $lang['SETTINGS_SEARCH']; ?>" autocomplete="off">
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
<script>
    $(document).ready(function () {
        $("#data-card-content").load('../inc/php/dep/pages/settings.php?load=webbie', function () {
            console.log('load performed');
        });
        document.getElementById("nm-settings-search-bar").style.display = "none";
    });
    $("#Webbie").click(function () {
        $("#data-card-content").load('../inc/php/dep/pages/settings.php?load=webbie', function (data) {
            document.getElementById("nm-settings-search-bar").style.display = "none";
            document.getElementById("data-card-content").style.display = "block";
            console.log('Webbie settings load performed');
        });
    });

    $("#Plugin").click(function () {
        $("#data-card-content").load('../inc/php/dep/pages/settings.php?load=plugin', function (data) {
            document.getElementById("nm-settings-search-bar").style.display = "block";
            document.getElementById("data-card-content").style.display = "block";
            console.log('Plugin settings load performed');
        });
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
            $("#data-card-content").load('../inc/php/dep/pages/settings.php?load=plugin', function () {
                console.log('load performed');
            });
        }
    });

    /* ---------- Settings SEARCH -------------- */
    searchinput.keyup(function () {
        $("#data-card-content").load('../inc/php/dep/pages/settingsearch.php?q=' + this.value, function () {
            console.log('load performed');
        });
        if ($(this).val().length === 0) {
            console.log($(this).val().length);
            $("#data-card-content").load('../inc/php/dep/pages/settings.php?load=plugin', function () {
                console.log('load performed');
            });
        }
    });
    if (typeof isSettingsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/settings.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>