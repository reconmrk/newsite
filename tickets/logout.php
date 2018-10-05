<?php
session_start();
ob_start();
setcookie('ticket_username', '', time() - 10, '/');
setcookie('ticket_password', '', time() - 10, '/');
session_destroy();
header('Location: login.php');
?>