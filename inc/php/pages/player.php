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
handlePermission('view_players');
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
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['PLAYER_PLAYERINFORMATION'] ?>
                        </div>
                    </div>
                    <div id="data-card-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['PLAYER_PLAYERSTATISTICS'] ?>
                        </div>
                    </div>
                    <div id="data-stats-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['PLAYER_LATESTSESSIONS'] ?>
                        </div>
                    </div>
                    <div id="data-sessions-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['PLAYER_NOTES'] ?>
                        </div>
                    </div>
                    <div id="data-notes-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['TITLE_PUNISHMENTS'] ?>
                        </div>
                    </div>
                    <div id="data-punishments-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>$(document).ready(function () {

        console.log(window.location.href);
        var str = window.location.href;
        var res = str.split("#");

        $("#data-card-content").load('../inc/php/dep/pages/player.php?uuid=' + res[1], function (data) {
            console.log('loaded');
        });

        $("#data-stats-content").load('../inc/php/dep/pages/playerdata.php?uuid=' + res[1], function (data) {
            console.log('loaded');
        });

        $("#data-sessions-content").load('../inc/php/dep/pages/playersessions.php?p=1&uuid=' + res[1], function (data) {
            console.log('loaded');
        });

        $("#data-notes-content").load('../inc/php/dep/pages/playernotes.php?uuid=' + res[1], function (data) {
            console.log('loaded');
        });

        $("#data-punishments-content").load('../inc/php/dep/pages/playerpunishments.php?uuid=' + res[1], function (data) {
            console.log('loaded');
        });

        $(document).on('click', 'a', function () {
            if (this.hasAttribute('playersessions')) {
                var attr = $(this).attr('page');
                if (typeof attr !== typeof undefined && attr !== false) {
                    $("#data-sessions-content").load('../inc/php/dep/pages/playersessions.php?uuid=' + res[1] + '&p=' + attr, function () {
                        console.log('load performed');
                    });
                }
            }
        });

        $(document).on('click', 'button', function () {
           if(this.hasAttribute('nmdelete-playernote')) {
               var attr = $(this).attr('nmdelete-playernote');
               swal({
                   title: "Are you sure?",
                   text: "You will not be able to recover this note!",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes, delete it!",
                   closeOnConfirm: true
               }, function () {
                   $.get("../inc/php/dep/pages/punishmentmanagement.php?type=delete_punishment&id=" + attr, function (data) {
                       $("#data-notes-content").load('../inc/php/dep/pages/playernotes.php?uuid=' + res[1], function (data) {
                           console.log('loaded');
                       });
                   });
               });
           }
        });

    });</script>