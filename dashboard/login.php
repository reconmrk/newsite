<?php
include '../inc/php/dep/config.php';
include '../inc/php/dep/User.php';
include '../inc/php/dep/Group.php';
$loginvideo = '';
$background = '';
$defaultlang = 'English';
$logo = '../inc/img/logo.png';
$full_logo = '../inc/img/full_logo.png';
$jsondata = file_get_contents('../inc/json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'login-video') {
        $loginvideo = $value;
    }
    if ($variable == 'login-background') {
        $background = $value;
    }
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'logo') {
        $logo = $value;
    }
    if ($variable == 'full-logo') {
        $full_logo = $value;
    }
}
session_start();
ob_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
}
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../inc/php/dep/languages/";
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
    include '../inc/php/dep/languages/' . $defaultlang . '.php';
}

if (isset($_COOKIE['nm_username'])) {
    $username = '';
    $salt = 'HM$75Dh(r^#A22j@_';

    $stmnt = $pdo->prepare('SELECT username FROM nm_accounts');
    $stmnt->execute();
    $data = $stmnt->fetchAll();
    foreach ($data as $row) {
        if (md5($salt . $row['username']) == $_COOKIE['nm_username']) {
            $username = $row['username'];
        }
    }
    if ($username != '') {
        $sql = 'SELECT username FROM nm_accounts WHERE username=? AND password=?;';
        $stmnt = $pdo->prepare($sql);
        $stmnt->bindParam(1, $username, 2);
        $stmnt->bindParam(2, $_COOKIE['nm_password'], 2);
        $stmnt->execute();
        $result = $stmnt->fetchAll();
        if (count($result) != 0) {
            $permissions = array();
            foreach ($pdo->query('SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`="nm_accountgroups"') as $item) {
                if ($item['COLUMN_NAME'] == 'id' || $item['COLUMN_NAME'] == 'name') {
                    continue;
                }
                $stmnt2 = $pdo->prepare('SELECT ' . $item['COLUMN_NAME'] . ' FROM nm_accountgroups WHERE name=?');
                $stmnt2->bindParam(1, $result[0][4], 2);
                $stmnt2->execute();
                $res = $stmnt2->fetchAll();
                if ($res[0][0] == '1') {
                    $permissions[] = $item['COLUMN_NAME'];
                }
            }
            $_SESSION['user'] = new User($result[0][1], $result[0][2], new Group($result[0][4], $permissions));
            header('Location: dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['PANEL_TITLE']; ?></title>
    <link rel="icon" href="<?php echo $logo; ?>">
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script defer src='//code.getmdl.io/1.3.0/material.min.js'></script>
</head>
<style>
    #header {
        transition: all 0.5s ease;
    }

    body {
        background-repeat: no-repeat;
        background-size: cover !important;
        background-attachment: fixed;
        background-position: 50% 0;
        background-color: black;
        overflow: hidden;
    }

    #video {
        z-index: -99;
        position: fixed;
        top: 50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        width: 150%;
        height: auto;
        -webkit-transform: translateX(-50%) translateY(-50%);
        transform: translateX(-50%) translateY(-50%);
        filter: blur(30px);
    }

    .logo img {
        margin-top: -100px !important;
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 25%;
    }

    .mdl-layout {
        align-items: center;
        justify-content: center;
    }

    .mdl-layout__content {
        padding: 24px;
        flex: none;
    }

    .mdl-color--grey-100 {
        background-color: transparent !important;
    }
</style>
<body>
<div class="mdl-grid">
    <div class="mdl-layout mdl-js-layout mdl-color--grey-100">
        <div class="logo">
            <img src="<?php echo $full_logo; ?>" draggable="false">
        </div>
        <main class="mdl-layout__content">
            <div class="mdl-card mdl-shadow--6dp">
                <form id="login">
                    <div class="mdl-card__title mdl-color--primary mdl-color-text--white" id="header">
                        <h2 class="mdl-card__title-text" id="title"><?php echo $lang['LOGIN_TITLE']; ?></h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <div class="mdl-textfield mdl-js-textfield">
                            <input class="mdl-textfield__input" type="text" id="username" name="username"/>
                            <label class="mdl-textfield__label"
                                   for="username"><?php echo $lang['LOGIN_USERNAME']; ?></label>
                        </div>
                        <div class="mdl-textfield mdl-js-textfield">
                            <input class="mdl-textfield__input" type="password" id="password" name="password"/>
                            <label class="mdl-textfield__label"
                                   for="userpass"><?php echo $lang['LOGIN_PASSWORD']; ?></label>
                        </div>
                        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-1">
                            <input type="checkbox" id="checkbox-1" class="mdl-checkbox__input" name="remember">
                            <span class="mdl-checkbox__label"><?php echo $lang['LOGIN_REMEMBERME']; ?></span>
                        </label>
                    </div>
                    <div class="mdl-card__actions mdl-card--border">
                        <button class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" type="submit"
                                id="button"><?php echo $lang['LOGIN_LOGIN']; ?></button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<div id="background">

</div>
<script>
    var videosrc = "<?php echo $loginvideo; ?>";
    var backgroundimagesrc = "<?php echo $background; ?>";
    if (videosrc !== null && videosrc) {
        $(document).ready(function () {
            document.getElementById('background').innerHTML = '<video id="video" autoplay="autoplay" loop="loop" preload="none" muted><source src=' + videosrc + ' type="video/mp4"/></video>';
        });
    } else if (backgroundimagesrc !== null && backgroundimagesrc) {
        $(document).ready(function () {
            document.body.style.backgroundImage = 'url(' + backgroundimagesrc + ')';
        });
    }

    $("#login").submit(function (event) {
        event.preventDefault();
        var data = $("#login").serialize();
        $.ajax({
            type: 'POST',
            url: '../inc/php/dep/login.php',
            data: data,
            success: function (response) {
                console.log(response);
                if (response === "true" || response === "false") {
                    if (response === "true") {
                        document.getElementById("title").innerHTML = "<?php echo $lang['LOGIN_LOGGINGIN']; ?>";
                        document.getElementById("header").style.cssText = 'background-color:green !important';
                        document.getElementById("button").disabled = true;

                        setTimeout(function () {
                            window.location.href = "dashboard.php";
                        }, 2000);
                    }
                    if (response === "false") {
                        document.getElementById("title").innerHTML = "<?php echo $lang['LOGIN_WRONGPASSWORD']; ?>";
                        document.getElementById("header").style.cssText = 'background-color:red !important';
                        document.getElementById("username").value = "";
                        document.getElementById("password").value = "";

                        document.getElementById("username").placeholder = "<?php echo $lang['LOGIN_USERNAME']; ?>";
                        document.getElementById("password").placeholder = "<?php echo $lang['LOGIN_PASSWORD']; ?>";

                        setTimeout(function () {
                            document.getElementById("title").innerHTML = "<?php echo $lang['LOGIN_TITLE']; ?>";
                            document.getElementById("header").style.backgroundColor = '';
                        }, 2500);

                    }
                } else {
                    console.log(response);
                    document.getElementById("title").innerHTML = "<?php echo $lang['LOGIN_NODBCONNECTION']; ?>";
                    document.getElementById("header").style.cssText = 'background-color:red !important';
                    document.getElementById("username").value = "";
                    document.getElementById("password").value = "";

                    document.getElementById("username").placeholder = "<?php echo $lang['LOGIN_USERNAME']; ?>";
                    document.getElementById("password").placeholder = "<?php echo $lang['LOGIN_PASSWORD']; ?>";

                    setTimeout(function () {
                        document.getElementById("title").innerHTML = "<?php echo $lang['LOGIN_TITLE']; ?>";
                        document.getElementById("header").style.backgroundColor = '';
                    }, 2500);
                }
            }
        });
    });
</script>
</body>
</html>