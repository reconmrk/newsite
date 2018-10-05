<?php define('DB_SERVER', 'localhost');define('DB_PORT', '');define('DB_USERNAME', 'NetworkManager');define('DB_PASSWORD', 'NetworkManager');define('DB_DATABASE', 'NetworkManager');define('DB_ENCODING', 'utf8');$pdo = new PDO("mysql:host=" .
                DB_SERVER . ";port=" . DB_PORT . ";dbname=" .
                DB_DATABASE . ";charset=" . DB_ENCODING, DB_USERNAME, DB_PASSWORD);