<?php
include '../permissions.php';
handlePermission('view_network');
header('Content-type: text/html; charset=utf-8');

$variable = $_GET['variable'];
$value = $_GET['value'];

$value = str_replace(';v1', ' ', $value);
$value = str_replace(';v2', '&', $value);
$value = str_replace(';v3', '%', $value);
$value = str_replace(';v4', '#', $value);

$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable1 => $value1) {
    if ($variable == $variable1) {
        $websettings[$variable] = $value;
        $json = json_encode($websettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($variable == 'default-language') {
            $result = downloadLanguageFile($value);
            echo $result;
            if (startsWith($result, 'Caught exception:')) {
                return;
            }
        }
        if (!file_put_contents('../../../json/settings.json', $json)) {
            echo 'Could not write to the settings file!';
        }
        die('true');
    }
}

try {
    $stmnt = $pdo->prepare('UPDATE nm_values SET value=? WHERE variable=?');
    $stmnt->bindParam(1, $value, 2);
    $stmnt->bindParam(2, $variable, 2);
    $stmnt->execute();
} catch (PDOException $ex) {
    echo 'Error: ' . $ex->getTraceAsString();
}

echo $value;

function downloadLanguageFile($language)
{
    $source = 'https://raw.githubusercontent.com/ChimpGamer/NetworkManager/master/Webbie/languages/' . $language . '.php';
    $destination = '../languages/' . $language . '.php';

    return storeUrlToFilesystem($source, $destination);
}

function storeUrlToFilesystem($source, $destination)
{
    try {
        $fp = fopen($destination, 'w+');
        if ($fp === false) {
            throw new Exception('Could not open ' . $destination);
        }

        if(!extension_loaded('curl')) {
            throw new Exception('The Php extension cURL is not installed!');
        }
        //Create a cURL handle.
        $ch = curl_init($source);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if ($statusCode == 200) {
            echo 'Downloaded!';
        } else {
            echo "Status Code: " . $statusCode;
        }
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
    return false;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
