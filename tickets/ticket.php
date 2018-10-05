<?php
include 'inc/php/header.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
}
header('Content-type: text/html; charset=utf-8');

$id = $_GET['id'];

$stmnt = $pdo->prepare('SELECT creator,id,title,creation,message,active FROM nm_tickets_tickets WHERE id=?');
$stmnt->bindParam(1, $id, 1);
$stmnt->execute();
if ($stmnt->rowCount() == 0) {
    header('Location: index.php');
}
$res = $stmnt->fetchAll()[0];
if (!($res['creator'] == $_SESSION['ticket_user']->getUuid())) {
    header('Location: index.php');
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];

    $stmnt = $pdo->prepare('INSERT INTO nm_tickets_messages(ticket_id, uuid, message, time) VALUES (?, ?, ?, ?)');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->bindParam(2, $_SESSION['ticket_user']->getUuid(), 2);
    $stmnt->bindParam(3, $message, 2);
    $stmnt->bindParam(4, round(microtime(true) * 1000), 1);
    $stmnt->execute();

    $stmnt = $pdo->prepare('UPDATE nm_tickets_tickets SET last_answer=?, last_update=? WHERE id=?');
    $stmnt->bindParam(1, $_SESSION['ticket_user']->getUuid(), PDO::PARAM_STR);
    $stmnt->bindParam(2, round(microtime(true) * 1000), PDO::PARAM_INT);
    $stmnt->bindParam(3, $id, PDO::PARAM_INT);
    $stmnt->execute();
    header('Location: ticket.php?id=' . $id);
}

function panelColor($name)
{
    $color = 'default';
    if ($name == $_SESSION['ticket_user']->getUuid()) {
        $color = 'primary';
    }
    return $color;
}

?>
<div class="row" style="margin-top: -10px;">
    <div class="col-sm-12">
        <h2 style="margin-bottom: 30px"><?php echo $res['title'] ?>
            <small> #<?php echo $res['id'] ?></small>
        </h2>
    </div>
    <div class="col-sm-12" style="padding: 0">
        <div class="message">
            <div class="col-sm-1">
                <img src="https://crafatar.com/avatars/<?php echo $_SESSION['ticket_user']->getUuid() ?>" height="45px">
            </div>
            <div class="col-sm-11" style="padding: 0">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <b><?php echo $_SESSION['ticket_user']->getName() ?></b> <?php echo '<time class="timeago" datetime="' . date('c', round($res['creation'] / 1000)) . '">null</time>' ?>
                    </div>
                    <div class="panel-body">
                        <?php echo $res['message'] ?>
                    </div>
                </div>
            </div>
        </div>
        <?php

        $stmnt = $pdo->prepare('SELECT uuid,time,message FROM nm_tickets_messages WHERE ticket_id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $data = $stmnt->fetchAll();
        foreach ($data as $item) {
            echo '<div class="message">
            <div class="col-sm-1">
                <img src="https://crafatar.com/avatars/' . $item['uuid'] . '" height="45px">
            </div>
            <div class="col-sm-11" style="padding: 0">
                <div class="panel panel-' . panelColor($item['uuid']) . '">
                    <div class="panel-heading"><b>' . returnName($item['uuid']) . '</b> <time class="timeago" datetime="' . date('c', round($item['time'] / 1000)) . '">null</time></div>
                    <div class="panel-body">
                    ' . $item['message'] . '
                    </div>
                </div>
            </div>
        </div>';
        }
        if ($res['active'] == '1') {
            echo '<div class="message">
            <div class="col-sm-1">
                <img src="https://crafatar.com/avatars/' . $_SESSION['ticket_user']->getUuid() . '" height="45px">
            </div>
            <div class="col-sm-11" style="padding: 0">
                <form method="post">
                    <textarea name="message" hidden required></textarea>
                    <button type="submit" class="btn btn-primary text-right" style="margin-top: 10px">Submit</button>
                </form>
            </div>
        </div>';
        }
        ?>
    </div>
</div>
<?php
include 'inc/php/footer.php';
?>