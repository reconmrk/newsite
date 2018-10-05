<?php
include '../permissions.php';
handlePermission('view_ticket');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
    }
}
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../languages/";
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
    include '../languages/' . $defaultlang . '.php';
}

echo '
<script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.1/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: "textarea",
        branding: false,
        hidden_input: false,
        plugins: "image",
    });
    jQuery(document).ready(function() {
        $("time.timeago").timeago();
    });
</script>
';

if (!isset($_GET['type'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare('SELECT active, title, creator, creation, message FROM nm_tickets_tickets where id=?');
    $stmt->bindParam(1, $id, 2);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {

        $row = $stmt->fetchAll()[0];
        $active = $row['active'];

        echo '
    <div class="col-12">
        <div style="color: inherit;"><i style="top: 5px;" class="material-icons">email</i> ' . $row['title'] . '</div>
        <br>
        <div class="data-card">
            <div class="col-6">
                <div class="nm-input-container" style="padding-bottom: 0;">
                    <label>' . $lang['TICKET_ASSIGNEDTO'] . '</label> 
                    <span>' . loadSelect($id, $lang) . '</span>
                </div>
            </div>
            <div class="col-6">
                <div class="nm-input-container" style="padding-bottom: 0;">
                    <label>' . $lang['TICKET_PRIORITY'] . '</label> 
                    <span>' . loadPrioritySelect($id, $lang) . '</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="data-card">
            <div class="data-card-header">
                <div class="data-card-header-title" id="ticket-title">' . getName($row['creator']) . '
                    <small style="margin-left: 5px;">
                        asked
                        <time class="timeago" datetime="' . date('c', round($row['creation'] / 1000)) . '">' . date($dateformat, $row['creation'] / 1000) . '</time>
                    </small>
                </div>
            </div>
            <div class="data-card-body">
                <div class="ticket-container">
                    ' . $row['message'] . '
                </div>
            </div>
        </div>
    </div>';

        $stmnt1 = $pdo->prepare('SELECT id, uuid, time, message FROM nm_tickets_messages WHERE ticket_id=?');
        $stmnt1->bindParam(1, $id, 1);
        $stmnt1->execute();
        if ($stmnt1->rowCount() > 0) {
            $data = $stmnt1->fetchAll();
            foreach ($data as $row) {
                echo '<div id="ticket-' . $row['id'] . '" class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <div class="data-card-header-title" id="ticket-title">' . getName($row['uuid']) . '
                                <small style="margin-left: 5px;">
                                    replied
                                    <time class="timeago" datetime="' . date('c', round($row['time'] / 1000)) . '">' . date($dateformat, $row['time'] / 1000) . '</time>
                                </small>';
                /*if (getName($row['uuid']) == $_SESSION['user']->getUsername()) {
                    echo '<a id="ticket-edit-message" ticketid="' . $row['id'] . '" class="edit-ticket-message-button material-icons">edit</a>';
                }*/
                echo '</div>
                        </div>
                        <div class="data-card-body">
                            <div class="ticket-container">
                                ' . $row['message'] . '
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }

        echo '<div id="cached-message">
          </div>';

        echo '<div class="col-12">
          <textarea style="width: 100%" id="ticket-message" required></textarea>
          </div>';

        echo '<div class="col-12 text-right no-padding" style="width: 100% !important;">';
        if ($active == '1') {
            echo '<button class="nm-button nm-raised-delete" ticketid="' . $id . '" id="ticket-close">' . $lang['TICKET_CLOSE'] . '</button>';
        }
        echo '<button class="nm-button nm-raised" ticketid="' . $id . '" id="ticket-respond">' . $lang['TICKET_ANSWER'] . '</button>';
        echo '</div>';
    } else {
        echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
    }
} else if ($_GET['type'] == 'assign_ticket') {
    handlePermission('assign_ticket');
    $id = $_GET['id'];
    $user = $_GET['account'];
    $time = round(microtime(true) * 1000);
    $stmnt = $pdo->prepare('UPDATE nm_tickets_tickets SET assigned_from=?, assigned_to=?, assigned_on=? WHERE id=?');
    $stmnt->bindParam(1, $_SESSION['user']->getUsername(), 2);
    $stmnt->bindParam(2, $user, 2);
    $stmnt->bindParam(3, $time, 1);
    $stmnt->bindParam(4, $id, 1);
    $stmnt->execute();
    echo 'Assigned ticket ' . $id . ' to ' . $user;
} else if ($_GET['type'] == 'priority_ticket') {
    handlePermission('priority_ticket');
    $id = $_GET['id'];
    $priority = $_GET['priority'];
    $stmnt = $pdo->prepare('UPDATE nm_tickets_tickets SET priority=? WHERE id=?');
    $stmnt->bindParam(1, $priority, 1);
    $stmnt->bindParam(2, $id, 1);
    $stmnt->execute();
    echo 'Priority set to ' . $priority . ' for ticket ' . $id . '';
} else if ($_GET['type'] == 'respond_ticket') {
    handlePermission('respond_ticket');
    $id = $_GET['id'];
    $message = $_GET['message'];
    $time = round(microtime(true) * 1000);
    $user = getUuid($_SESSION['user']->getUsername());

    $stmnt = $pdo->prepare('UPDATE nm_tickets_tickets SET last_update=?, last_answer=? WHERE id=?');
    $stmnt->bindParam(1, $time, 2);
    $stmnt->bindParam(2, $user, 2);
    $stmnt->bindParam(3, $id, 1);
    $stmnt->execute();

    $stmnt = $pdo->prepare('INSERT INTO nm_tickets_messages(ticket_id, uuid, message, time) VALUES (?, ?, ?, ?)');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->bindParam(2, $user, 2);
    $stmnt->bindParam(3, $message, 2);
    $stmnt->bindParam(4, $time, 1);
    $stmnt->execute();

    echo 'Responded to ticket ' . $id . ' by ' . $user;
} else if ($_GET['type'] == 'delete_ticket') {
    handlePermission('close_ticket');
    $id = $_GET['id'];
    $time = round(microtime(true) * 1000);
    $user = getUuid($_SESSION['user']->getUsername());

    $stmnt = $pdo->prepare('UPDATE nm_tickets_tickets SET closed_on=?, closed_by=?, active=0 WHERE id=?');
    $stmnt->bindParam(1, $time, 2);
    $stmnt->bindParam(2, $user, 2);
    $stmnt->bindParam(3, $id, 1);
    $stmnt->execute();

    echo 'Closed ticket ' . $id . ' by ' . $user;
} else if ($_GET['type'] == 'load_edit_ticket_message') {
    $id = $_GET['id'];

    echo '<div id="nmedit-ticket-message" class="modal" ticketid="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 16px;font-weight: 200;">Edit message</h3>
            <hr style="width: calc(100% - 20px)">
                <form id="nmedit-ticket-message-form" ticketid="' . $id . '">';

    $stmnt = $pdo->prepare('SELECT message FROM nm_tickets_messages WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $row = $stmnt->fetch();

    echo '
          <div class="col-12">
          <textarea style="width: 100%" id="new-ticket-message" required>' . $row['message'] . '</textarea>
          </div>';

    echo '<div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-ticket-message">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-ticket-message">Save</button>
                    </div>
                </form>
            </div>
        </div>';
    die();
} else if ($_GET['type'] == 'edit_ticket_message') {
    $id = $_GET['id'];
    $message = $_GET['message'];
    $time = round(microtime(true) * 1000);
    $user = getUuid($_SESSION['user']->getUsername());

    try {
        $stmnt = $pdo->prepare('UPDATE nm_tickets_messages SET uuid=?, message=?, time=? WHERE id=?;');
        $stmnt->bindParam(1, $user, 2);
        $stmnt->bindParam(2, $message, 2);
        $stmnt->bindParam(3, $time, 1);
        $stmnt->bindParam(4, $id, 1);
        $stmnt->execute();
        die('true');
    } catch (PDOException $ex) {
        die($ex->getTraceAsString());
    }
}

function returnName($name)
{
    if (strlen($name) == 36) {
        return getName($name);
    }
    return $name;
}

function loadSelect($id, $lang)
{
    global $pdo;
    $res1 = '<select id="assign-ticket" assign="' . $id . '">';
    $stmnt = $pdo->prepare('SELECT assigned_to FROM nm_tickets_tickets WHERE id=');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $dat = $stmnt->fetchAll();
    $stmnt = $pdo->query('SELECT username FROM nm_accounts');
    $dat1 = $stmnt->fetchAll();
    $res1 = $res1 . '<option disabled selected value>' . $lang['TEXT_SELECTOPTION'] . '</option>';
    foreach ($dat1 as $row) {
        $select = '';
        if ($row['username'] == $dat['assigned_to']) {
            $select = 'selected';
        }
        $res1 = $res1 . '<option ' . $select . ' value="' . $row['username'] . '">' . $row['username'] . '</option>';
    }
    $res1 = $res1 . '</select>';
    return $res1;
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

function loadPrioritySelect($id, $lang)
{
    global $pdo;
    $res1 = '<select id="priority-ticket" priority="' . $id . '">';
    $stmnt = $pdo->prepare('SELECT priority FROM nm_tickets_tickets WHERE id=?');
    $stmnt->bindParam(1, $id, 1);
    $stmnt->execute();
    $res = $stmnt->fetch();
    //$res1 = $res1 . '<option disabled selected value>' . $lang['TEXT_SELECTOPTION'] . '</option>';
    $arr = array(0, 1, 2, 3);
    foreach ($arr as $row) {
        $select = '';
        if ($row == $res['priority']) {
            $select = 'selected';
        }
        $res1 = $res1 . '<option ' . $select . ' value="' . $row . '">' . priority($row, $lang) . '</option>';
    }
    $res1 = $res1 . '</select>';
    return $res1;
}

function priority($priority, $lang)
{
    switch ($priority) {
        case "0":
            return $lang['TICKET_PRIORITY_NO_PRIORITY'];
        case "1":
            return $lang['TICKET_PRIORITY_LOW'];
        case "2":
            return $lang['TICKET_PRIORITY_MEDIUM'];
        case "3":
            return $lang['TICKET_PRIORITY_HIGH'];
        default:
            return $lang['TICKET_PRIORITY_NO_PRIORITY'];
    }
}