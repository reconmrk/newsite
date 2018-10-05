<?php
session_start();
include 'User.php';
include 'Group.php';
include 'config.php';
ob_start();

try {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $salt = 'HM$75Dh(r^#A22j@_';
        $pwhash = md5($salt . $password);
        $stmnt = $pdo->prepare('SELECT * FROM nm_accounts WHERE username=? AND password=?;');
        $stmnt->bindParam(1, $username, PDO::PARAM_STR);
        $stmnt->bindParam(2, $pwhash, PDO::PARAM_STR);
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
                if($res[0][0] == '1') {
                    $permissions[] = $item['COLUMN_NAME'];
                }
            }
            $_SESSION['user'] = new User($result[0][1], $result[0][2], new Group($result[0][4], $permissions));
            if(isset($_POST['remember'])) {
                setcookie('nm_username', md5($salt . $username), time()+604800, '/');
                setcookie('nm_password', md5($salt . $password), time()+604800, '/');
            }
            echo 'true';
        } else {
            echo 'false';
        }
    }
} catch (Exception $e) {
    die('error');
}