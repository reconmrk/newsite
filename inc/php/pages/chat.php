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
handlePermission('view_chat');
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
                                       placeholder="<?php echo $lang['CHAT_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
                    <div id="data-card-chatcontent">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="nm-input-container" style="margin-bottom: -20px;">
                    <button class="nm-button nm-raised" id="chat-update"
                            style="position: relative;right: -24px; margin-left: -24px !important;" type="submit"
                            close="create-account">Refresh
                    </button>
                    <?php if (hasPermission('edit_chat')) {
                        echo '<button class="nm-button nm-raised" id="chat-clear" style="position: relative; left: 20px; !important;" type="submit" close="create-account">ClearChat</button>';
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#data-card-chatcontent").load('../inc/php/dep/pages/chat.php?load=load&p=1', function () {
            console.log('load performed');
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
            $("#data-card-chatcontent").load('../inc/php/dep/pages/chat.php?load=load&p=1', function () {
                console.log('load performed');
            });
        }
    });

    $("input#search-input").keyup(function () {
        if ($(this).val().length === 0) {
            console.log($(this).val().length);
            $("#data-card-content").load('../inc/php/dep/pages/chat.php?load=load&p=1', function () {
                console.log('load performed');
            });
        }
    });

    /* ---------- PLAYER SEARCH -------------- */
    $("input#search-input").keyup(function () {
        $("#data-card-chatcontent").load('../inc/php/dep/pages/chatsearch.php?q=' + this.value, function () {
            console.log('load performed');
        });
        if ($(this).val().length === 0) {
            console.log($(this).val().length);
            $("#data-card-chatcontent").load('../inc/php/dep/pages/chatsearch.php?load=load&p=1', function () {
                console.log('load performed');
            });
        }
    });
    if (typeof isChatLoaded === 'undefined') {
        $.getScript("../inc/js/pages/chat.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>