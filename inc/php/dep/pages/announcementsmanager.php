<?php
include '../permissions.php';
handlePermission('edit_announcements');
header("Content-Type: text/html; UTF-8");

$load = $_GET['load'];

if($load == 'add_announcement') {
    $atype = $_GET['atype'];
    $message = $_GET['message'];
    $server = ($atype == '1' || $atype == '2' ? "" : $_GET['server']);
    $active = $_GET['active'];

    $stmnt = $pdo->prepare('SELECT id FROM nm_announcements WHERE type=? AND message=?');
    $stmnt->bindParam(1, $atype, PDO::PARAM_INT);
    $stmnt->bindParam(2, $message, 2);
    $stmnt->execute();
    if($stmnt->rowCount() == 0) {
        $stmnt = $pdo->prepare('INSERT INTO nm_announcements(`type`,`message`,`server`,`active`) VALUES (?,?,?,?)');
        $stmnt->bindParam(1, $atype, PDO::PARAM_INT);
        $stmnt->bindParam(2, $message, 2);
        $stmnt->bindParam(3, $server, 2);
        $stmnt->bindParam(4, $active, PDO::PARAM_INT);
        $stmnt->execute();

        foreach ($active as $act) {
            $stmnt = $pdo->prepare('UPDATE nm_announcements SET " . $act . "=1 WHERE type=? AND message=?');
            $stmnt->bindParam(1, $atype, PDO::PARAM_INT);
            $stmnt->bindParam(2, $message, 2);
            $stmnt->execute();
        }

        die('true');
    } else {
        die('false');
    }
} else if($load == 'delete_announcement') {
    handlePermission('edit_announcements');
    $id = $_GET['id'];
    $stmnt = $pdo->prepare("DELETE FROM nm_announcements WHERE id=?");
    $stmnt->bindParam(1, $id, PDO::PARAM_INT);
    $stmnt->execute();
} else if($load == 'setannouncementactive') {
    handlePermission('edit_announcements');
    $id = $_GET['id'];
    $active = $_GET['active'];

    $stmnt = $pdo->prepare('UPDATE nm_announcements SET active=? WHERE id=?');
    $stmnt->bindParam(1, $active, PDO::PARAM_INT);
    $stmnt->bindParam(2, $id, PDO::PARAM_INT);
    $stmnt->execute();
    die('true');
} else if ($load == 'edit_announcement') {
    echo 'hi';
    $id = $_GET['id'];
    $load = $_GET['type'];
    $message = $_GET['message'];
    $server = $_GET['server'];

    $stmnt = $pdo->prepare('UPDATE nm_announcements SET type=?, message=?, server=? WHERE id=?');
    $stmnt->bindParam(1,$load, 1);
    $stmnt->bindParam(2,$message, 2);
    $stmnt->bindParam(3,$server, 2);
    $stmnt->bindParam(4,$id, 1);
    $result = $stmnt->execute();
    die($result);
} else if($load == 'load_announcement') {
    handlePermission('edit_announcements');
    $id = $_GET['id'];
    echo '<div id="nmedit-announcement" class="modal" announcement="' . $id . '">
            <div class="modal-content" style="width: 600px !important;">
            <h3 style="font-size: 15px;font-weight: 200">Edit announcement with ID:' . $id . '</h3>
                <form id="nmedit-announcement-form" announcement="' . $id . '">';


    foreach ($pdo->query('SHOW COLUMNS FROM nm_announcements') as $item) {
        if ($item['Field'] == 'id' || $item['Field'] == 'active') {
            continue;
        }
        $stmnt = $pdo->prepare('SELECT ' . $item['Field'] . ' FROM nm_announcements WHERE id=?');
        $stmnt->bindParam(1, $id, 1);
        $stmnt->execute();
        $row = $stmnt->fetch();
        echo '<div class="nm-input-container">
                            <label for="' . $item['Field'] . '">' . $item['Field'] . '</label>
                            <input type="text" id="' . $item['Field'] . '" name="' . $item['Field'] . '" value="' . $row[$item['Field']] . '">
                            </div>';
    }
    echo '          <div class="nm-input-container" style="text-align: right;padding-right: 16px;margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="nmedit-announcement">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="nmedit-announcement">Save</button>
                    </div>
                </form>
            </div>
        </div>';
}
/*    $id = $_GET['id'];
    echo '<div id="edit-announcement" class="modal" announcement="' . $id . '">
            <div class="modal-content" style="width: 600px">
                <form id="edit-announcement-form" announcement="' . $id . '">';

    $stmnt = $pdo->prepare('SELECT * FROM nm_announcements WHERE id=?');
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
    foreach ($stmnt->fetchAll() as $res) {
        echo '<div class="nm-input-container">
                <label>1</label>
                <select id="atype" name="atype" required>
                    <option value="1">global chat announcement</option>
                    <option value="2">globak actionbar announcement</option>
                </select>
            </div>
            <div class="nm-input-container">
                <label>2</label>
                <input type="text" name="message" value="' . $res['message'] . '">
            </div>
            <div class="nm-input-container">
                <label>3</label>
                <input type="text" name="server">
            </div>
            <div class="nm-input-container">
                <label>4</label>
                <select name="active" required>
                    <option value="1">True</option>
                    <option value="0">False</option>
                </select>
            </div>';
    }
                    echo '<div class="nm-input-container" style="    text-align: right;
    padding-right: 16px;
    margin-bottom: -20px;">
                        <button class="nm-button" type="button" style="position: relative;right: -24px;" close="edit-announcement">Cancel</button> <button class="nm-button nm-raised" style="position: relative;right: -24px;" type="submit" close="edit-announcement">Save</button>
                    </div>
                </form>
            </div>
        </div>';
}*/

function getAnnouncementType($type)
{
    switch ($type) {
        case "1" :
            return 'chat all servers';
        case "2":
            return "chat severs only";
        case "3":
            return "chat servers except";
        case "4":
            return "actionbar all servers";
        case "5":
            return "actionbar servers only";
        case "6":
            return "actionbar servers except";
        case "7":
            return "title all servers";
        case "8":
            return "title servers only";
        case "9":
            return "title servers except";
    }
    return null;
}