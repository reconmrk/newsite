<?php
include '../permissions.php';
handlePermission('view_players');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$timeformat = 'h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
    }
    if ($variable == 'time-format') {
        $timeformat = $value;
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

$uuid = $_GET['uuid'];

$stmt = $pdo->prepare('SELECT username, ip FROM nm_players where uuid=?');
$stmt->bindParam(1, $uuid, 2);
$stmt->execute();
if ($stmt->rowCount() > 0) {

    $row = $stmt->fetchAll()[0];

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    echo '<div class="data-card-body">';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <p>' . $lang['PLAYER_AVERAGEPLAYTIME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-30 text-right">
                                <p>' . avgPlaytime($uuid) . '</p>
                            </div>
                        </div>
                        ';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <p>' . $lang['PLAYER_JOINSAT'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-30 text-right">
                                <p>' . getAverageDailyLogin($uuid) . '</p>
                            </div>
                        </div>
                        ';
    /*
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <p>Player normally leaves at</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-30 text-right">
                                <p>' . getAverageDailyLogout($uuid) . '</p>
                            </div>
                        </div>
                        ';
    */
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-70">
                                <p>' . $lang['PLAYER_ALTACCOUNTS'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-30 text-right">
								<p style="display: flex;">' . getSecondAccounts($row['ip'], $row['username']) . '</p>
                            </div>
                        </div>
                        ';
    echo '<div id="versions"></div>';
    echo '</div>';
    echo '</div>';
    echo "<script>

Highcharts.setOptions({
    chart: {
        style: {
            fontFamily: 'Roboto'
        }
    }
   });


Highcharts.getOptions().plotOptions.pie.colors = (function () {
        var colors = [],
            base = '#039BE5',
            i;

        for (i = 0; i < 10; i += 1) {
            // Start out with a darkened base color (negative brighten), and end
            // up with a much brighter color
            colors.push(Highcharts.Color(base).brighten((i - 2) / 64).get());
        }
        return colors;
    }());

Highcharts.chart('versions', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            height: 407
        },
        title: {
            text: 'Most used Versions'
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>',
            backgroundColor: '#FFFFFF',
            borderColor: '#FFFFFF',
            borderRadius: 2,
            borderWidth: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: false,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                color: '#5053D3'
            },
            series: {
                color: '#5053D3'
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Players',
            colorByPoint: true,
            data: " . getTotalVersions($uuid) . "
        }]
    });
</script>";

} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty"><p>' . $lang['TEXT_NORESULTS'] . '</p></div></div>';
}

function avgPlaytime($uuid)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT time FROM nm_sessions WHERE uuid=?');
    $stmnt->bindParam(1, $uuid, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $time = 0;
        $data = $stmnt->fetchAll();
        foreach ($data as $row) {
            $time = $time + $row['time'];
        }
        return formatMilliseconds($time / $stmnt->rowCount());
    }
    return gmdate("H:i:s", 0);
}


function getAverageDailyLogin($uuid)
{
    global $pdo;
    global $timeformat;
    $stmnt = $pdo->prepare('SELECT DATE(FROM_UNIXTIME(start/1000)) AS date, start AS total_logins FROM nm_sessions WHERE uuid=? GROUP BY date');
    $stmnt->bindParam(1, $uuid, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {

        $data1 = 0;

        $data = $stmnt->fetchAll();
        foreach ($data as $item) {
            $millis_join = $item['total_logins'];
            $datetest = new DateTime($item['date']);
            $datamillis = $datetest->getTimestamp() / 1000;
            $millis_result = $millis_join - $datamillis;
            $data1 = $data1 + $millis_result;
        }
        $ress = ($data1 / $stmnt->rowCount());
        return date($timeformat, $ress / 1000);
    }
    return '-';
}

function getAverageDailyLogout($uuid)
{
    global $pdo;
    global $timeformat;
    $stmnt = $pdo->prepare('SELECT DATE(FROM_UNIXTIME(end/1000)) AS date, end AS total_logins FROM nm_sessions WHERE uuid=? GROUP BY date');
    $stmnt->bindParam(1, $uuid, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {

        $data1 = 0;

        $data = $stmnt->fetchAll();
        foreach ($data as $item) {
            $millis_join = $item['total_logins'];
            $datetest = new DateTime($item['date']);
            $datamillis = $datetest->getTimestamp() / 1000;
            $millis_result = $millis_join - $datamillis;
            $data1 = $data1 + $millis_result;
        }
        $ress = ($data1 / $stmnt->rowCount());
        return date($timeformat, $ress / 1000);
    }
    return '-';
}

function getSecondAccounts($ip, $name)
{
    global $pdo;
    $stmnt = $pdo->prepare('SELECT uuid,username FROM nm_players WHERE ip=?');
    $stmnt->bindParam(1, $ip, 2);
    $stmnt->execute();
    if ($stmnt->rowCount() > 0) {
        $res = "";
        $data = $stmnt->fetchAll();
        foreach ($data as $item) {
            if ($item['username'] !== $name) {
                $res = $res . '<a style="color: #039BE5 !important;" href="#' . $item['uuid'] . '" player="' . $item['uuid'] . '">' . $item['username'] . '&nbsp;</a> ';
            }
        }
        return $res;
    }
    return '-';
}

function getTotalVersions($uuid)
{
    global $pdo;
    $versionTotal = $pdo->prepare('SELECT uuid FROM nm_logins WHERE version <> 0 AND uuid=?');
    $versionTotal->bindParam(1, $uuid, 2);
    $versionTotal->execute();
    $total = $versionTotal->rowCount();

    $version = $pdo->prepare('SELECT DISTINCT(version) as version, count(version) AS count FROM nm_logins WHERE VERSION <> 0 AND uuid=? GROUP BY version');
    $version->bindParam(1, $uuid, 2);
    $version->execute();
    $result = $version->fetchAll();

    $pie_data = "[";
    foreach ($result as $row) {
        switch ($row['version']) {
            case '401':
                $pie_data = $pie_data . "{name: '1.13.1', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '393':
                $pie_data = $pie_data . "{name: '1.13', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '340':
                $pie_data = $pie_data . "{name: '1.12.2', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '338':
                $pie_data = $pie_data . "{name: '1.12.1', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '335':
                $pie_data = $pie_data . "{name: '1.12', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '316':
                $pie_data = $pie_data . "{name: '1.11.x', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '315':
                $pie_data = $pie_data . "{name: '1.11', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '210':
                $pie_data = $pie_data . "{name: '1.10 - 1.10.2', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '110':
                $pie_data = $pie_data . "{name: '1.9.3 - 1.9.4', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '109':
                $pie_data = $pie_data . "{name: '1.9.2', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '108':
                $pie_data = $pie_data . "{name: '1.9.1', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '107':
                $pie_data = $pie_data . "{name: '1.9', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '47':
                $pie_data = $pie_data . "{name: '1.8 - 1.8.9', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '5':
                $pie_data = $pie_data . "{name: '1.7.6 - 1.7.10', y: " . ($row['count'] / $total) . "}, ";
                break;
            case '4':
                $pie_data = $pie_data . "{name: '1.7.2 - 1.7.5', y: " . ($row['count'] / $total) . "}, ";
                break;
            default:
                $pie_data = $pie_data . "{name: 'snapshot', y: " . ($row['count'] / $total) . "}, ";
                break;
        }
    }
    $pie_data = $pie_data . ']';
    return $pie_data;
}

function formatMilliseconds($milliseconds)
{
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%u:%02u:%02u';
    $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
    return rtrim($time, '0');
}