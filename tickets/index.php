<?php
include 'inc/php/header.php';
?>
    <h3><?php echo $lang['HOME_TITLE']; ?> <b><?php echo $_SESSION['ticket_user']->getName() ?></b></h3>
<?php
$stmnt = $pdo->prepare('SELECT id,title,last_answer,last_update,active FROM nm_tickets_tickets WHERE creator=? ORDER by active DESC, creation DESC');
$stmnt->bindParam(1, $_SESSION['ticket_user']->getUuid(), 2);
$stmnt->execute();
$data = $stmnt->fetchAll();

if ($stmnt->rowCount() == 0) {
    echo '<h4 style="text-align: center; margin-top: 30px">' . $lang['HOME_NO_TICKETS'] . '</h4>';
} else {
    echo '<table class="table table-hover" style="margin-top: 30px">
    <thead>
    <tr>
        <th>' . $lang['HOME_TICKET'] . '</th>
        <th>' . $lang['HOME_LAST_RESPONSE'] . '</th>
        <th>' . $lang['HOME_STATUS'] . '</th>
        <th class="text-right">' . $lang['HOME_ACTION'] . '</th>
    </tr>
    </thead>
    <tbody>';

    foreach ($data as $item) {
        echo '<tr>
            <td><b>#' . $item['id'] . '</b> ' . $item['title'] . '</td>';
        echo $item['last_answer'] == '' ? '<td>' . $lang['VAR_NO_RESPONSE'] . '</td>' : '<td>' . $lang['VAR_BY'] . ' ' . returnName($item['last_answer']) . ' <time class="timeago" datetime="' . date('c', round($item['last_update'] / 1000)) . '">null</time></td>';

        echo $item['active'] == '1' ? '<td><span class="label label-success">' . $lang['VAR_OPEN'] . '</span></td>' : '<td><span class="label label-danger">' . $lang['VAR_CLOSED'] . '</span></td>';

        echo '<td class="text-right"><a href="ticket.php?id=' . $item['id'] . '">' . $lang['VAR_VIEW'] . '</a></td>
        </tr>';
    }
    echo '</tbody>
</table>';
}

include 'inc/php/footer.php';
?>