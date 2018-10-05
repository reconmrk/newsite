<?php
include '../permissions.php';
handlePermission('edit_tabcompletecommands');

$type = $_GET['type'];
$id = $_GET['id'];
$string = $_GET['string'];

if($type == 'add_command') {
    $data = explode("!", $string);
    $stmnt = $pdo->prepare("INSERT INTO nm_tabcompletecommands(command,permission) VALUES (?,?)");
    $stmnt->bindParam(1, $data[0], 2);
    $stmnt->bindParam(2, $data[1], 2);
    $stmnt->execute();
}

if($type == 'delete_command') {
    $stmnt = $pdo->prepare("DELETE FROM nm_tabcompletecommands WHERE id=?");
    $stmnt->bindParam(1, $id, 2);
    $stmnt->execute();
}
?>