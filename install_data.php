<?php
error_reporting(0);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['host'])) {
        $host = $_POST['host'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $port = $_POST['port'];
        $database = $_POST['database'];
		$encoding = "utf8";

        try {
            $myfile = fopen("inc/php/dep/config.php", "w") or die("file");
            $txt = "<?php define('DB_SERVER', '" . $host ."');";
            fwrite($myfile, $txt);
            $txt = "define('DB_PORT', '" . $port ."');";
            fwrite($myfile, $txt);
            $txt = "define('DB_USERNAME', '" . $username ."');";
            fwrite($myfile, $txt);
            $txt = "define('DB_PASSWORD', '" . $password ."');";
            fwrite($myfile, $txt);
            $txt = "define('DB_DATABASE', '" . $database ."');";
            fwrite($myfile, $txt);
			$txt = "define('DB_ENCODING', '" . $encoding ."');";
			fwrite($myfile, $txt);

            $txt = '$pdo = new PDO("mysql:host=" .
                DB_SERVER . ";port=" . DB_PORT . ";dbname=" .
                DB_DATABASE . ";charset=" . DB_ENCODING, DB_USERNAME, DB_PASSWORD);';

            fwrite($myfile, $txt);
            fclose($myfile);

            $pdo = new PDO('mysql:host=' .
                $host . ';port=' . $port . ';dbname=' .
                $database . ";charset=" . $encoding, $username, $password);

            $stmnt = $pdo->prepare('SELECT count(*) AS totalTables FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=?;');
            $stmnt->bindParam(1, $database, 2);
            $stmnt->execute();
            if($stmnt->fetchColumn() == 0) {
                die('no tables ' . $stmnt->fetchColumn());
            }
            die('true');
        } catch (Exception $e) {
            die('connection');

        }
    } else {
        include "inc/php/dep/config.php";
        $username = $_POST['username'];
        $password = $_POST['password'];
        $usergroup = 'administrator';
        $notifications = '[]';

        $stmnt = $pdo->prepare('SELECT * FROM nm_accountgroups WHERE name=?');
        $stmnt->bindColumn(1, $usergroup, 2);
        $stmnt->execute();
        if($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare("INSERT into nm_accountgroups(name, administrator) VALUES ('administrator', TRUE )");
            $stmnt->execute();
        }


        $stmnt = $pdo->prepare('SELECT * FROM nm_accounts WHERE username=?');
        $stmnt->bindColumn(1, $username, 2);
        $stmnt->execute();
        if($stmnt->rowCount() == 0) {
            $stmnt = $pdo->prepare('INSERT INTO nm_accounts(username, password, notifications, usergroup) VALUES (?, ?, ?, ?)');
            $salt = 'HM$75Dh(r^#A22j@_';
            $pwhash = md5($salt . $password);
            $stmnt->bindParam(1, $username, 2);
            $stmnt->bindParam(2, $pwhash, 2);
            $stmnt->bindParam(3, $notifications, 2);
            $stmnt->bindParam(4, $usergroup, 2);
            $stmnt->execute();
        }
        die('true');
    }
}