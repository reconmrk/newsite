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
handlePermission('view_network');
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
</style>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['MOTD_MOTDGENERATOR']; ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="data-card-row-value" style="padding: 20px">
                            <div class="nm-input-container">
                                <label><?php echo $lang['VAR_MESSAGE']; ?></label>
                                <?php
                                $stmnt = $pdo->query('SELECT value FROM nm_values WHERE variable="motd_text"');
                                $res = $stmnt->fetchAll()[0];
                                ?>
                                <input type="text" placeholder="Message" id="message" variable="motd_text" value="<?php echo $res['value']; ?>">
                            </div>
                            <div class="nm-input-container">
                                <label><?php echo $lang['VAR_HOVERMESSAGE']; ?></label>
                                <?php
                                $stmnt = $pdo->query('SELECT value FROM nm_values WHERE variable="motd_description"');
                                $res = $stmnt->fetchAll()[0];
                                ?>
                                <input type="text" placeholder="Description" id="description" variable="motd_description" value="<?php echo $res['value']; ?>">
                            </div>
                            <div class="nm-input-container">
                                <?php
                                $stmnt = $pdo->query('SELECT value FROM nm_values WHERE variable="motd_icon"');
                                $res = $stmnt->fetchAll()[0];
                                ?>
                                <label><?php echo $lang['VAR_ICON']; ?></label>
                                <input type="text" id="icon" placeholder="Icon" variable="motd_icon" value="<?php echo $res['value']; ?>">
                            </div>
                            <div class="nm-input-container">
                                <label><?php echo $lang['MOTD_PREVIEW']; ?></label>
                                <div class="preview_zone" id="preview_zone">
                                    <div class="server-name">Minecraft Server <span class="ping">143/200</div>
                                    <span class="preview_motd" id="motd">Your MOTD should be here</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-header-title">
                            <?php echo $lang['MOTD_MAINTENANCEMODE']; ?>
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="data-card-row-value" style="padding: 20px">
                            <div class="nm-input-container">
                                <?php
                                $stmnt = $pdo->prepare('SELECT value FROM nm_values WHERE variable="maintenance_text"');
                                $stmnt->execute();
                                $res = $stmnt->fetchAll()[0];
                                ?>
                                <label><?php echo $lang['TITLE_MOTD']; ?></label>
                                <input type="text" placeholder="MOTD" id="maintenance_message" variable="maintenance_text" value="<?php echo $res['value']; ?>">
                            </div>
                            <div class="nm-input-container">
                                <label><?php echo $lang['MOTD_PREVIEW']; ?></label>
                                <div class="preview_zone" id="preview_zone_maintenance">
                                    <div class="server-name">Minecraft Server <span class="ping">143/200</div>
                                    <span class="preview_motd" id="motd_maintenance">Your MOTD should be here</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof isMotdLoaded === 'undefined') {
        $.getScript("../inc/js/pages/colorcodes.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed. (colorcodes)');
        });
        $.getScript("../inc/js/pages/motd.js", function(data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed. (motd)');
        });
    }
    $(document).ready(function() {
        setTimeout(function () {
            var yourMOTD = document.getElementById('message').value.replace(new RegExp('%newline%', 'g'), '\n');
            var newMOTD = yourMOTD.replaceColorCodes();
            $('#motd').text('').append(newMOTD).html();

            var url = document.getElementById('icon').value;
            if(url.endsWith('.png') ||url.endsWith('.jpg')) {
                console.log('proceed please');
                $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
                $('#preview_zone_maintenance').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
            }

            yourMOTD = document.getElementById('maintenance_message').value.replace(new RegExp('%newline%', 'g'), '\n');
            newMOTD = yourMOTD.replaceColorCodes();
            $('#motd_maintenance').text('').append(newMOTD).html();
        }, 25);
    });
</script>