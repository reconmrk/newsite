<?php
include '../inc/php/dep/config.php';
include 'inc/php/TicketUser.php';
//include 'inc/php/languages/English.php';
$defaultlang = 'English';
session_start();
if (!isset($_SESSION['ticket_user'])) {
    header("Location: login.php");
}
ob_start();
header('Content-type: text/html; charset=utf-8');

if (isset($_COOKIE['ticket_language'])) {
    $language = $_COOKIE['ticket_language'];
    $langdir = 'inc/php/languages/';
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
    include 'inc/php/languages/' . $defaultlang . '.php';
}

function getName($uuid)
{
    if ($uuid == '1a6b7d7c-f2a8-4763-a9a8-b762f309e84c') {
        return 'CONSOLE';
    }
    global $pdo;
    $sql = 'SELECT username FROM nm_players WHERE uuid=?';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(1, $uuid, PDO::PARAM_STR);
    $statement->execute();
    $row = $statement->fetch();
    return $row['username'];
}

function returnName($name)
{
    if (strlen($name) == 36) {
        return getName($name);
    }
    return $name;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NetworkManager</title>
    <link rel='stylesheet' href='inc/css/bootstrap.min.css'>
    <link rel='stylesheet' href='inc/css/style.css'>
    <?php
    if (isset($_COOKIE['ticket_theme'])) {
        $theme = $_COOKIE['ticket_theme'];
        $themedir = 'inc/css/themes/';
        if (is_dir($themedir)) {
            if ($dh = opendir($themedir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '..' && $file != '.') {
                        $filename = pathinfo($file, PATHINFO_FILENAME);
                        $result = $themedir . $file;
                        switch ($theme) {
                            case $filename:
                                echo '<link rel="stylesheet" href=' . $result . '>';
                                break;
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
    ?>
    <script src='//code.jquery.com/jquery-3.3.1.min.js'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.1/tinymce.min.js'></script>
    <script>tinymce.init({
            selector: 'textarea',
            branding: false,
            hidden_input: false,
            plugins: "fullpage, image, media, link, lists, code, searchreplace"
        });</script>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Ticket<i>System</i></a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li><a href="index.php"><?php echo $lang['MENU_HOME']; ?></a></li>
                <li class="dropdown">
                    <a style="cursor: pointer" class="dropdown-toggle"
                       data-toggle="dropdown"><?php echo $lang['MENU_TICKETS']; ?>
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="create.php"><?php echo $lang['MENU_TICKETS_CREATETICKET']; ?></a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a style="cursor: pointer" class="dropdown-toggle" data-toggle="dropdown"><img
                                src="https://crafatar.com/avatars/<?php echo $_SESSION['ticket_user']->getUuid(); ?>"  height="18px">
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="settings.php"><?php echo $lang['MENU_SETTINGS']; ?></a></li>
                        <li><a href="logout.php"><?php echo $lang['MENU_LOGOUT']; ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 60px">