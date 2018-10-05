<?php
include_once '../inc/php/dep/Group.php';
include_once '../inc/php/dep/User.php';
session_start();
ob_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] == "") {
    header('Location: login.php');
}
include_once '../inc/php/dep/permissions.php';
$defaultlang = 'English';
$defaulttheme = null;
$logo = '../inc/img/logo.png';
$fulllogo = '../inc/img/full_logo.png';
$jsondata = file_get_contents('../inc/json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    switch ($variable) {
        case 'default-language':
            $defaultlang = $value;
            break;
        case 'default-theme':
            if ($value == 'Default') {
                break;
            }
            $defaulttheme = $value;
            break;
        case 'logo':
            $logo = $value;
            break;
        case 'full-logo':
            $fulllogo = $value;
            break;
    }
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
$themefile = null;
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
    $themedir = "../inc/css/themes/";
    if (is_dir($themedir)) {
        if ($dh = opendir($themedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $result = $themedir . $file;
                    switch ($theme) {
                        case $filename:
                            $themefile = $result;
                            break;
                    }
                }
            }
            closedir($dh);
        }
    }
} else if ($defaulttheme != null) {
    $themefile = '../inc/css/themes/' . $defaulttheme . '.css';
}

function is_dir_empty($dir)
{
    if (!is_readable($dir)) return NULL;
    return (count(scandir($dir)) == 2);
}

function getUuid($name)
{
    if ($name == 'CONSOLE') {
        return '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c';
    }
    global $pdo;
    $sql = 'SELECT uuid FROM nm_players WHERE username=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['uuid'];
}

function isModuleEnabled($name)
{
    global $pdo;
    $sql = 'SELECT value FROM nm_values WHERE variable=?';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->execute();
    $res = $stmt->fetch();
    return ($res['value'] == 1 ? true : false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
    <title><?php echo $lang['PANEL_TITLE']; ?></title>
    <link rel="icon" href="<?php echo $logo; ?>">
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto+Mono:300,400,500">
    <link rel="stylesheet" href="../inc/css/header.css">
    <link rel="stylesheet" href="../inc/css/style.css">
    <link rel="stylesheet" href="../inc/css/grid.css">
    <link rel="stylesheet" href="../inc/css/table.css">
    <link rel="stylesheet" href="../inc/css/datacard.css">
    <?php
    if ($themefile != null) {
        echo '<link rel="stylesheet" href="' . $themefile . '">';
    } ?>
    <link rel="stylesheet" href="../inc/css/sweetalert.css">

    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="//code.highcharts.com/stock/highstock.js"></script>
    <script src="//code.highcharts.com/maps/modules/map.js"></script>
    <script src="//code.highcharts.com/mapdata/custom/world.js"></script>
    <script src="//code.highcharts.com/highcharts-more.js"></script>
    <script src="../inc/js/jquery.timeago.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.1/tinymce.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.1/jquery.tinymce.min.js"></script>

    <script>
        tinymce.init({
            selector: "textarea",
            branding: false,
            hidden_input: false,
            plugins: "image",
        });

        function GetClock() {
            let d = new Date();
            let nhour = d.getHours(), nmin = d.getMinutes(), nsec = d.getSeconds();
            if (nmin <= 9) {
                nmin = "0" + nmin;
            }
            if (nsec <= 9) {
                nsec = "0" + nsec;
            }
            document.getElementById('clockbox').innerHTML = "" + nhour + ":" + nmin + ":" + nsec + "";
        }

        window.onload = function () {
            GetClock();
            setInterval(GetClock, 1000);
        }
    </script>

</head>
<body>
<div id="wrapper">
    <nm-appbar>
        <div class="nm-appbar">
            <div class="nm-appbar-top-left">
                <img src="<?php echo $fulllogo; ?>" class="nm-appbar-logo">
            </div>
            <div class="nm-appbar-top-center">
                <h3 style="font-size: 25px;font-weight: 200;margin: 0;line-height: 1.9em;" id="clockbox"></h3>
            </div>
            <div class="nm-appbar-user-area">
                <div class="nm-appbar-more-actions-menu">
                    <div class="tooltip">
                        <span class="tooltiptext">Tasks</span>
                        <a class="nm-nav-item nm-button" style="display: inherit !important; min-width: 0 !important;"
                           aria-label="tasks" name="Tasks">
                            <i class="material-icons">work</i>
                        </a>
                    </div>
                </div>
                <div class="nm-appbar-more-actions-menu">
                    <div class="tooltip">
                        <span class="tooltiptext">Settings</span>
                        <a class="nm-nav-item nm-button" style="display: inherit !important; min-width: 0 !important;"
                           aria-label="account" name="Account Settings">
                            <i class="material-icons">settings</i>
                        </a>
                    </div>
                </div>
                <div class="nm-appbar-user-avatar-menu">
                    <button class="nm-button nm-appbar-avatar-button"
                            style="display: inherit !important; min-width: 0 !important;">
                        <?php
                        $uuid = getUuid($_SESSION['user']->getUsername()) != null ? getUuid($_SESSION['user']->getUsername()) : '8667ba71b85a4004af54457a9734eed7';
                        echo '<img src="https://crafatar.com/avatars/' . $uuid . '?default=MHF_Steve&overlay"><p class="nm-entry-displayname">' . $_SESSION['user']->getUsername() . '</p></img>
                </button>';
                        ?>
                </div>
            </div>
        </div>
    </nm-appbar>
    <div class="site-wrapper">
        <nm-navbar>
            <div class="nm-nav">
                <div class="nm-nav-group nm-nav-group-root">
                    <nm-navbar-item class="nm-navbar-item-overview">
                        <a class="nm-nav-item md-gmp-blue-theme nm-selected-entry" aria-label="dashboard"
                           name="<?php echo $lang['TITLE_DASHBOARD']; ?>">
                            <div class="nm-nav-item-header-lockup">
                                <i class="c5e-nav-icon nm-icons material-icons">dashboard</i>
                                <span class="nm-entry-displayname"><?php echo $lang['TITLE_OVERVIEW']; ?></span>
                            </div>
                        </a>
                    </nm-navbar-item>
                </div>
                <?php
                if (hasPermission('view_analytics')) {
                    echo '<nm-navbar-item>
                    <a class="nm-nav-item" aria-label="analytics" name="' . $lang['TITLE_ANALYTICS'] . '">
                        <div class="nm-nav-item-lockup">
                            <i class="nm-nav-icon nm-icons material-icons">timeline</i>
                            <span class="nm-entry-displayname">' . $lang['TITLE_ANALYTICS'] . '</span>
                        </div>
                    </a>
                </nm-navbar-item>';
                }

                if (hasPermission('view_players') || hasPermission('view_tickets') || hasPermission('view_punishments') || hasPermission('view_chatlogs') || hasPermission('view_chat') || hasPermission("view_helpop") || hasPermission('view_reports')) {
                    echo '<div class="nm-nav-group">';
                    if (hasPermission('view_players')) {
                        echo '<div class="nm-nav-heading">' . $lang['TITLE_PLAYERS'] . '</div>
                    <nm-navbar-item><a class="nm-nav-item" aria-label="players" name="' . $lang['TITLE_PLAYERS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">group</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_PLAYERS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_punishments') && isModuleEnabled('module_punishments')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="punishments" name="' . $lang['TITLE_PUNISHMENTS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">gavel</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_PUNISHMENTS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_reports') && isModuleEnabled('module_reports')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="reports" name="' . $lang['TITLE_REPORTS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">report_problem</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_REPORTS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_chat')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="chat" name="' . $lang['TITLE_CHAT'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">chat</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_CHAT'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_chatlogs') && isModuleEnabled('module_chatlogs')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="chatlogs" name="' . $lang['TITLE_CHATLOGS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">bug_report</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_CHATLOGS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_helpop') && isModuleEnabled('module_helpop')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="helpop" name="' . $lang['TITLE_HELPOP'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">help</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_HELPOP'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_tickets') && isModuleEnabled('module_tickets')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="tickets" name="' . $lang['TITLE_TICKETS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">forum</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_TICKETS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    echo ' </div>';
                }

                if (hasPermission('view_network') || hasPermission('view_filter') || hasPermission("view_permissions") || hasPermission("view_commandblocker") || hasPermission("view_tabcompletecommands") || hasPermission('view_announcements')) {
                    echo '<div class="nm-nav-group">
                    <div class="nm-nav-heading">' . $lang['TITLE_NETWORK'] . '</div>';
                    if (hasPermission('view_network')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#settings" aria-label="settings" name="' . $lang['TITLE_NETWORKSETTINGS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">settings</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_SETTINGS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                    <nm-navbar-item><a class="nm-nav-item" aria-label="languages" name="Languages">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">language</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_LANGUAGES'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                    <nm-navbar-item><a class="nm-nav-item" href="#motd" aria-label="motd" name="' . $lang['TITLE_MOTDANDMAINTENANCE'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">date_range</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_MOTD'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_permissions') && (isModuleEnabled('module_permissions_bungee') || isModuleEnabled('module_permissions_spigot'))) {
                        echo '<nm-navbar-item><a class="nm-nav-item" aria-label="permissions" name="' . $lang['TITLE_PERMISSIONS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">lock</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_PERMISSIONS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_servers') && isModuleEnabled('module_servermanager')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#servers" aria-label="servers" name="' . $lang['TITLE_SERVERS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">important_devices</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_SERVERS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    if (hasPermission('view_tags') && isModuleEnabled('module_tags')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#tags" aria-label="tags" name="' . $lang['TITLE_TAGS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">label</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_TAGS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }

                    if (hasPermission('view_filter') && isModuleEnabled('module_filter')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#filter" aria-label="filter" name="' . $lang['TITLE_FILTER'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">alarm_off</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_FILTER'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }

                    if (hasPermission('view_commandblocker') && isModuleEnabled('module_commandblocker')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#commandblocker" aria-label="commandblocker" name="' . $lang['TITLE_COMMANDBLOCKER'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">block</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_COMMANDBLOCKER'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }

                    if (hasPermission('view_announcements') && isModuleEnabled('module_announcements')) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#announcements" aria-label="announcements" name="' . $lang['TITLE_ANNOUNCEMENTS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">announcement</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_ANNOUNCEMENTS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }

                    if (hasPermission("view_tabcompletecommands")) {
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#tabcompletecommands" aria-label="tabcompletecommands" name="' . $lang['TITLE_TABCOMPLETECOMMANDS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">arrow_forward</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_TABCOMPLETECOMMANDS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }
                    echo ' </div>';
                }

                if (hasPermission('view_accounts')) {
                    echo '<div class="nm-nav-group">
                    <div class="nm-nav-heading">' . $lang['TITLE_ADMINISTRATION'] . '</div>
                    <nm-navbar-item><a class="nm-nav-item" href="#accounts" aria-label="accounts" name="' . $lang['TITLE_GROUPSANDACCOUNTS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">group_add</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_GROUPSANDACCOUNTS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                </div>';
                }
                if (hasPermission('view_addons')) {
                    echo '<div class="nm-nav-group">
                    <div class="nm-nav-heading">' . $lang['TITLE_ADDONS'] . '</div>';


                    /*
                    <nm-navbar-item><a class="nm-nav-item" href="#accounts" aria-label="accounts" name="Accounts & Groups">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">group_add</i>
                                <span class="nm-entry-displayname">Groups & Accounts</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                    */

                    foreach (scandir("../addons") as $file) {
                        if ('.' === $file) continue;
                        if ('..' === $file) continue;
                        echo '<nm-navbar-item><a class="nm-nav-item" href="#accounts" addon="' . $file . '" aria-label="' . $file . '" name="' . $file . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">extension</i>
                                <span class="nm-entry-displayname">' . $file . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>';
                    }

                    echo '</div>';
                }

                echo '<div class="nm-nav-group">
                    <div class="nm-nav-heading">' . $lang['TITLE_ACCOUNT'] . '</div>
                    <nm-navbar-item><a class="nm-nav-item" href="#tasks" aria-label="tasks" name="' . $lang['TITLE_TASKS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">work</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_TASKS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                    <nm-navbar-item><a class="nm-nav-item" href="#account" aria-label="account" name="' . $lang['TITLE_ACCOUNTSETTINGS'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">settings</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_ACCOUNTSETTINGS'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                    <nm-navbar-item><a class="nm-nav-item" href="logout.php" aria-label="account" name="' . $lang['TITLE_LOGOUT'] . '">
                            <div class="nm-nav-item-lockup">
                                <i class="nm-nav-icon nm-icons material-icons">exit_to_app</i>
                                <span class="nm-entry-displayname">' . $lang['TITLE_LOGOUT'] . '</span>
                            </div>
                        </a>
                    </nm-navbar-item>
                </div>
            </div>
        </nm-navbar>
        <div class="site-content">
            <nm-feature-bar>
                <div class="nm-featurebar">
                    <div class="nm-featurebar-main">
                        <div class="nm-featurebar-title-lockup">
                            <div class="nm-featurebar-title" id="title">' . $lang['TITLE_DASHBOARD'] . '</div>
                        </div>
                    </div>
                </div>
            </nm-feature-bar>
            <div id="body-wrapper">
                <div id="content">'; ?>

                <!--
                <div id="header-wrapper">
                    <div id="header-top">

                    </div>
                    <div id="header-nav-wrapper">
                        <div id="navigation">
                            <a href="#" onclick="openNav()"><i class="material-icons">menu</i>Dashboard</a>
                        </div>
                        <div id="header-menu" class="topnav">
                            <ul>
                                <li><a class="active" href="#home"><i class="material-icons">search</i></a></li>
                                <li><a href="#news"><i class="material-icons">timeline</i></a></li>
                                <li><a href="#contact"><i class="material-icons">work</i></a></li>
                                <li><a href="#about"><img src="//crafatar.com/avatars/Dunios" class="profile-image"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                -->
