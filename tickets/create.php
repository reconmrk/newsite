<?php
include 'inc/php/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];

    $stmnt = $pdo->prepare('INSERT INTO nm_tickets_tickets(creator, title, message, creation, last_update, priority, active) VALUES (?, ?, ?, ?, ?, 0, ?);');
    $stmnt->bindParam(1, $_SESSION['ticket_user']->getUuid(), 2);
    $stmnt->bindParam(2, $title, 2);
    $stmnt->bindParam(3, $message, 2);
    $stmnt->bindParam(4, round(microtime(true)*1000), 1);
    $stmnt->bindParam(5, round(microtime(true)*1000), 1);
    $active = true;
    $stmnt->bindParam(6, $active, PDO::PARAM_BOOL);
    $stmnt->execute();
    //header('Location: index.php');
}

echo '<form method="post">
    <div class="form-group">
        <label for="title">' . $lang['TICKET_CREATE_TITLE'] . '</label>
        <input type="text" class="form-control" id="title" name="title">
    </div>
    <div class="form-group">
        <label for="message">'. $lang['TICKET_CREATE_MESSAGE'] . '</label>
        <textarea style="height: 300px" name="message" id="message" hidden required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">' . $lang['TICKET_CREATE_SUBMIT'] . '</button>
</form>';

include 'inc/php/footer.php';
?>