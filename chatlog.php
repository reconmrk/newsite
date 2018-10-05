<?php
include 'inc/php/dep/config.php';
function ChatColor($chat)
{
    preg_match_all("/[^§&]*[^§&]|[§&][0-9a-z][^§&]*/", $chat, $brokenupstrings);
    $returnstring = "";
    foreach ($brokenupstrings as $results) {
        $ending = '';
        foreach ($results as $individual) {
            $code = preg_split("/[&§][0-9a-z]/", $individual);
            preg_match("/[&§][0-9a-z]/", $individual, $prefix);
            if (isset($prefix[0])) {
                $actualcode = substr($prefix[0], 1);
                switch ($actualcode) {
                    case "1":
                        $returnstring = $returnstring . '<FONT COLOR="0000AA">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "2":
                        $returnstring = $returnstring . '<FONT COLOR="00AA00">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "3":
                        $returnstring = $returnstring . '<FONT COLOR="00AAAA">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "4":
                        $returnstring = $returnstring . '<FONT COLOR="AA0000">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "5":
                        $returnstring = $returnstring . '<FONT COLOR="AA00AA">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "6":
                        $returnstring = $returnstring . '<FONT COLOR="FFAA00">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "7":
                        $returnstring = $returnstring . '<FONT COLOR="AAAAAA">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "8":
                        $returnstring = $returnstring . '<FONT COLOR="555555">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "9":
                        $returnstring = $returnstring . '<FONT COLOR="5555FF">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "a":
                        $returnstring = $returnstring . '<FONT COLOR="55FF55">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "b":
                        $returnstring = $returnstring . '<FONT COLOR="55FFFF">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "c":
                        $returnstring = $returnstring . '<FONT COLOR="FF5555">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "d":
                        $returnstring = $returnstring . '<FONT COLOR="FF55FF">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "e":
                        $returnstring = $returnstring . '<FONT COLOR="FFFF55">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "f":
                        $returnstring = $returnstring . '<FONT COLOR="FFFFFF">';
                        $ending = $ending . "</FONT>";
                        break;
                    case "l":
                        if (strlen($individual) > 2) {
                            $returnstring = $returnstring . '<span style="font-weight:bold;">';
                            $ending = "</span>" . $ending;
                        }
                        break;
                    case "m":
                        if (strlen($individual) > 2) {
                            $returnstring = $returnstring . '<strike>';
                            $ending = "</strike>" . $ending;
                        }
                        break;
                    case "n":
                        if (strlen($individual) > 2) {
                            $returnstring = $returnstring . '<span style="text-decoration: underline;">';
                            $ending = "</span>" . $ending;
                        }
                        break;
                    case "o":
                        if (strlen($individual) > 2) {
                            $returnstring = $returnstring . '<i>';
                            $ending = "</i>" . $ending;
                        }
                        break;
                    case "r":
                        $returnstring = $returnstring . $ending;
                        $ending = '';
                        break;
                }
                if (isset($code[1])) {
                    $returnstring = $returnstring . $code[1];
                    if (isset($ending) && strlen($individual) > 2) {
                        $returnstring = $returnstring . $ending;
                        $ending = '';
                    }
                }
            } else {
                $returnstring = $returnstring . $individual;
            }
        }
    }

    return $returnstring;
}

$uuid = $_GET['uuid'];
if (empty($_GET)) {
    die('no uuid');
}
if (!isset($_GET['uuid'])) {
    die('no uuid');
}

$stmnt = $pdo->prepare('SELECT creator, tracked, time FROM nm_chatlogs WHERE uuid=?');
$stmnt->bindParam(1, $uuid, 2);
$stmnt->execute();
if ($stmnt->rowCount() == 0) {
    die('invalid uuid');
}
$data = $stmnt->fetchAll()[0];
$creator = $data['creator'];
$tracked = $data['tracked'];
$time = $data['time'];

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

$name = getName($tracked);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatLog - NetworkManager</title>
    <link rel="stylesheet" href="inc/css/themes/default/style.css">
    <link rel="stylesheet" href="inc/css/grid.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="title">ChatLog of <?php echo getName($tracked); ?>
                <small style="color: lightgray"> by <?php echo getName($creator); ?>
                    on <?php echo date('l jS \of F Y h:i:s A', round($time / 1000)); ?></small>
            </h1>
        </div>
        <?php
        echo '<div class="col-12" style="font-size: 18px;">';
        echo '<table class="tbl" style="width: 100%;">
                <tbody>';


        function startsWith($haystack, $needle)
        {
            $length = strlen($needle);
            return (substr($haystack, 0, $length) === $needle);
        }

        $stmnt = $pdo->prepare('SELECT message, server, time FROM nm_chat WHERE uuid=? AND time <=? ORDER BY id DESC LIMIT 25');
        $stmnt->bindParam(1, $tracked);
        $stmnt->bindParam(2, $time);
        $stmnt->execute();
        $res = $stmnt->fetchAll();
        echo '<tr>';
        echo '<td><b>Player</b></td>';
        echo '<td><b>Message</b></td>';
        echo '<td><b>Server</b></td>';
        echo '<td class="text-right"><b>Time</b></td>';
        echo '</tr>';
        foreach ($res as $item) {
            if (substr($item['message'], 0, 1) === "/") {
                echo '<tr>';
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td><img src="https://crafatar.com/avatars/' . $tracked . '?size=20"> ' . $name . '</td>';
                echo '<td>' . ChatColor($item['message']) . '</td>';
                echo '<td>' . htmlspecialchars($item['server']) . '</td>';
                echo '<td class="text-right">' . date('l jS \of F Y h:i:s A', round($item['time'] / 1000)) . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
        echo '</div>';
        ?>
    </div>
</div>
</body>
</html>