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
handlePermission('edit_accounts');
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
                <div class="data-card" style="margin-top: 20px">
                    <div id="data-group-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="data-card" style="margin-top: 20px">
                    <div id="data-account-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="create-account" class="modal">
            <div class="modal-content">
                <form id="create-account-form">
                    <div class="nm-input-container">
                        <label>Account name</label>
                        <input type="text" name="account">
                    </div>
                    <div class="nm-input-container">
                        <label>Password</label>
                        <input type="password" name="password">
                    </div>
                    <div class="nm-input-container">
                        <label>Group</label>
                        <select id="create-account" name="group">
                            <?php
                            $stmnt = $pdo->prepare("SELECT * FROM nm_accountgroups");
                            $stmnt->execute();
                            foreach ($stmnt->fetchAll() as $row) {
                                echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nm-input-container" style="    text-align: right;
    padding-right: 16px;
    margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;"
                                close="create-account">Cancel
                        </button>
                        <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                                close="create-account">Create
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="create-group" class="modal">
            <div class="modal-content" style="width: 600px">
                <form id="create-group-form">
                    <div class="nm-input-container">
                        <label>Group name</label>
                        <input type="text" name="name">
                    </div>
                    <div class="nm-input-container">
                        <label>Permissions</label>
                    </div>
                    <div class="row">
                        <?php

                        foreach ($pdo->query("SHOW COLUMNS FROM `nm_accountgroups`") as $item) {
                            if ($item['Field'] == 'id' || $item['Field'] == 'name') {
                                continue;
                            }
                            echo '<div class="col-3">';
                            //echo '' . $item['Field'] . ': <input type="checkbox" name="permission[]" value="' . $item['Field'] . '"><br>';
                            echo '<div class="nm-input-container" style="position: relative; top: -10px; right: 18px; padding: 0 !important;">
                            <input type="checkbox" id="' . $item['Field'] . '" class="switch-input" name="permission[]" value="' . $item['Field'] . '">
                            <label for="' . $item['Field'] . '" class="switch-label"><span style="position: relative !important; top: -4px !important;" class="toggle--on">' . $item['Field'] . '</span><span style="position: relative !important; top: -4px !important;" class="toggle--off">' . $item['Field'] . '</span>
                            </label>
                        </div>';
                            echo '</div>';
                        }


                        ?>
                    </div>
                    <div class="nm-input-container" style="    text-align: right;
    padding-right: 16px;
    margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;"
                                close="create-group">Cancel
                        </button>
                        <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit"
                                close="create-group">Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="cached-modal"></div>
<script>
    $(document).ready(function () {
        $("#data-group-content").load('../inc/php/dep/pages/groups.php', function () {
            console.log('loaded');
        });
        $("#data-account-content").load('../inc/php/dep/pages/accounts.php', function () {
            console.log('loaded');
        });
    });
    if (typeof isAccountsLoaded === 'undefined') {
        $.getScript("../inc/js/pages/accounts.js", function (data, textStatus, jqxhr) {
            console.log(textStatus); //success
            console.log(jqxhr.status); //200
            console.log('Load was performed.');
        });
    }
</script>