<?php
include '../permissions.php';
handlePermission('view_players');
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
    }
    if($variable == 'date-format') {
        $dateformat = $value;
    }
}
if(isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../languages/";
    if(is_dir($langdir)) {
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

$total = $pdo->query('SELECT COUNT(*) FROM nm_players');
$total = $total->fetchColumn();
$limit = 10;
$pages = ceil($total / $limit);
$page = $_GET['p'];
$offset = ($page - 1) * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1) ? '<a players="' . ($page - 1) . '" class="nm-pagination-icon"  page="' . ($page - 1) . '"><i class="material-icons">keyboard_arrow_left</i></a>' : '';
$nextlink = ($page < $pages) ? '<a players="' . ($page - 1) . '" class="nm-pagination-icon" page="' . ($page + 1) . '"><i class="material-icons">keyboard_arrow_right</i></a>' : '';
$stmt = $pdo->prepare('SELECT username,uuid,firstlogin FROM nm_players ORDER BY firstlogin DESC LIMIT :limit OFFSET :offset');
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
if ($stmt->rowCount() > 0) {

    echo '<div class="data-card-body">';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-40">
                                <p><img src="https://crafatar.com/avatars/' . $row['uuid'] . '?overlay"> ' . $row['username'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-40">
                                <p>' . date($dateformat, $row['firstlogin']/1000) . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-20 text-right">
                                <p><a href="#' . $row['uuid'] . '" player="' . $row['uuid'] . '" style="color: #039BE5 !important;">' . $lang['VAR_VIEW'] . '</a></p>
                            </div>
                        </div>';
    }

    echo '</div>';

    echo '<div class="data-card-footer-pagination">
                            <div class="nm-pagination-bar">' . $start . '-' . $end . ' of ' . $total . ' ' . $prevlink . $nextlink . '</div>
                        </div>';


} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty" ' . $lang['TEXT_NORESULTS'] . '</div></div>';
}
?>