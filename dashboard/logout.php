<?php
session_start();
ob_start();
setcookie('nm_username', '', time() - 10, '/');
setcookie('nm_password', '', time()- 10, '/');
session_destroy();
header('Location: login.php');