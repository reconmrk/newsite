<?php
include '../permissions.php';
include '../MinecraftColorcodes.php';
handlePermission('view_players');
$colorCodes = new MinecraftColorcodes();
$defaultlang = 'English';
$dateformat = 'm/d/y h:i a';
$jsondata = file_get_contents('../../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
    if ($variable == 'date-format') {
        $dateformat = $value;
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

echo '<style>
    /* Labels */
    .label-danger {
        color: white;
        background-color: #d9534f;
    }

    .label-info {
        color: white;
        background-color: #00C0EF;
    }


    .label-warning {
        color: white;
        background-color: #f0ad4e;
    }

    .label-success {
        color: white;
        background-color: #5cb85c;
    }
    
    .label{display:inline;padding:.4em .9em .5em;font-size:75%;font-weight:bold;line-height:1;color:#ffffff;max-width:100%;vertical-align: baseline; }
    
    span:after {
        content: none;
        clear: both;
    }
</style>';

$uuid = $_GET['uuid'];

$stmt = $pdo->prepare('SELECT username, nickname, ip, country, version, firstlogin, lastlogin, lastlogout, online, playtime FROM nm_players where uuid=?');
$stmt->bindParam(1, $uuid, 2);
$stmt->execute();
if ($stmt->rowCount() > 0) {

    $row = $stmt->fetchAll()[0];

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $iterator = new IteratorIterator($stmt);

    if ($row['online'] == '0') {
        $online = '<span class="label label-danger">False</span>';
    } else {
        $online = '<span class="label label-success">True</span>';
    }

    echo '<div class="data-card-body">';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_USERNAME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p><img src="https://crafatar.com/avatars/' . $uuid . '"> ' . $row['username'] . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_NICKNAME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . $colorCodes->convert($row['nickname'],true) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_UUID'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . $uuid . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_COUNTRY'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . countryCodeToCountry($row['country']) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-50">
                                <p>' . $lang['PLAYER_LATESTMINECRAFTVERSION'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-50 text-right">
                                <p>' . getVersion($row['version']) . '</p>
                            </div>
                        </div>';
    if (hasPermission('show_ip')) {
        echo '<div class="data-card-row">
								<div class="data-card-row-value nm-data-card-30">
									<p>' . $lang['PLAYER_IPADDRESS'] . '</p>
								</div>
								<div class="data-card-row-value nm-data-card-70 text-right">
									<p>' . $row['ip'] . '</p>
								</div>
							</div>';
    }
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_JOINED'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['firstlogin'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_LASTLOGIN'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['lastlogin'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_LASTLOGOUT'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . date($dateformat, $row['lastlogout'] / 1000) . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_ONLINE'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . $online . '</p>
                            </div>
                        </div>';
    echo '<div class="data-card-row">
                            <div class="data-card-row-value nm-data-card-30">
                                <p>' . $lang['PLAYER_PLAYTIME'] . '</p>
                            </div>
                            <div class="data-card-row-value nm-data-card-70 text-right">
                                <p>' . formatMilliseconds($row['playtime']) . '</p>
                            </div>
                        </div>';

    echo '</div>';

} else {
    echo '<div class="data-card-body"><div class="nm-search-results-empty">' . $lang['TEXT_NORESULTS'] . '</div></div>';
}

function getVersion($id)
{
    switch ($id) {
        case '401':
            return '1.13.1';
        case '393':
            return '1.13';
        case '340':
            return '1.12.2';
        case '338':
            return '1.12.1';
        case '335':
            return '1.12';
        case '316':
            return '1.11.x';
        case '315':
            return '1.11';
        case '210':
            return '1.10 - 1.10.2';
        case '110':
            return '1.9.3 - 1.9.4';
        case '109':
            return '1.9.2';
        case '108':
            return '1.9.1';
        case '107':
            return '1.9';
        case '47':
            return '1.8 - 1.8.9';
        case '5':
            return '1.7.6 - 1.7.10';
        case '4':
            return '1.7.2 - 1.7.5';
        default:
            return 'snapshot';
    }
}

function countryCodeToCountry($code)
{
    $code = strtoupper($code);
    switch ($code) {
        case 'AF':
            return 'Afghanistan';
        case 'AX':
            return 'Aland Islands';
        case 'AL':
            return 'Albania';
        case 'DZ':
            return 'Algeria';
        case 'AS':
            return 'American Samoa';
        case 'AD':
            return 'Andorra';
        case 'AO':
            return 'Angola';
        case 'AI':
            return 'Anguilla';
        case 'AQ':
            return 'Antarctica';
        case 'AG':
            return 'Antigua and Barbuda';
        case 'AR':
            return 'Argentina';
        case 'AM':
            return 'Armenia';
        case 'AW':
            return 'Aruba';
        case 'AU':
            return 'Australia';
        case 'AT':
            return 'Austria';
        case 'AZ':
            return 'Azerbaijan';
        case 'BS':
            return 'Bahamas the';
        case 'BH':
            return 'Bahrain';
        case 'BD':
            return 'Bangladesh';
        case 'BB':
            return 'Barbados';
        case 'BY':
            return 'Belarus';
        case 'BE':
            return 'Belgium';
        case 'BZ':
            return 'Belize';
        case 'BJ':
            return 'Benin';
        case 'BM':
            return 'Bermuda';
        case 'BT':
            return 'Bhutan';
        case 'BO':
            return 'Bolivia';
        case 'BA':
            return 'Bosnia and Herzegovina';
        case 'BW':
            return 'Botswana';
        case 'BV':
            return 'Bouvet Island (Bouvetoya)';
        case 'BR':
            return 'Brazil';
        case 'IO':
            return 'British Indian Ocean Territory (Chagos Archipelago)';
        case 'VG':
            return 'British Virgin Islands';
        case 'BN':
            return 'Brunei Darussalam';
        case 'BG':
            return 'Bulgaria';
        case 'BF':
            return 'Burkina Faso';
        case 'BI':
            return 'Burundi';
        case 'KH':
            return 'Cambodia';
        case 'CM':
            return 'Cameroon';
        case 'CA':
            return 'Canada';
        case 'CV':
            return 'Cape Verde';
        case 'KY':
            return 'Cayman Islands';
        case 'CF':
            return 'Central African Republic';
        case 'TD':
            return 'Chad';
        case 'CL':
            return 'Chile';
        case 'CN':
            return 'China';
        case 'CX':
            return 'Christmas Island';
        case 'CC':
            return 'Cocos (Keeling) Islands';
        case 'CO':
            return 'Colombia';
        case 'KM':
            return 'Comoros the';
        case 'CD':
            return 'Congo';
        case 'CG':
            return 'Congo the';
        case 'CK':
            return 'Cook Islands';
        case 'CR':
            return 'Costa Rica';
        case 'CI':
            return 'Cote d\'Ivoire';
        case 'HR':
            return 'Croatia';
        case 'CU':
            return 'Cuba';
        case 'CY':
            return 'Cyprus';
        case 'CZ':
            return 'Czech Republic';
        case 'DK':
            return 'Denmark';
        case 'DJ':
            return 'Djibouti';
        case 'DM':
            return 'Dominica';
        case 'DO':
            return 'Dominican Republic';
        case 'EC':
            return 'Ecuador';
        case 'EG':
            return 'Egypt';
        case 'SV':
            return 'El Salvador';
        case 'GQ':
            return 'Equatorial Guinea';
        case 'ER':
            return 'Eritrea';
        case 'EE':
            return 'Estonia';
        case 'ET':
            return 'Ethiopia';
        case 'FO':
            return 'Faroe Islands';
        case 'FK':
            return 'Falkland Islands (Malvinas)';
        case 'FJ':
            return 'Fiji the Fiji Islands';
        case 'FI':
            return 'Finland';
        case 'FR':
            return 'France, French Republic';
        case 'GF':
            return 'French Guiana';
        case 'PF':
            return 'French Polynesia';
        case 'TF':
            return 'French Southern Territories';
        case 'GA':
            return 'Gabon';
        case 'GM':
            return 'Gambia the';
        case 'GE':
            return 'Georgia';
        case 'DE':
            return 'Germany';
        case 'GH':
            return 'Ghana';
        case 'GI':
            return 'Gibraltar';
        case 'GR':
            return 'Greece';
        case 'GL':
            return 'Greenland';
        case 'GD':
            return 'Grenada';
        case 'GP':
            return 'Guadeloupe';
        case 'GU':
            return 'Guam';
        case 'GT':
            return 'Guatemala';
        case 'GG':
            return 'Guernsey';
        case 'GN':
            return 'Guinea';
        case 'GW':
            return 'Guinea-Bissau';
        case 'GY':
            return 'Guyana';
        case 'HT':
            return 'Haiti';
        case 'HM':
            return 'Heard Island and McDonald Islands';
        case 'VA':
            return 'Holy See (Vatican City State)';
        case 'HN':
            return 'Honduras';
        case 'HK':
            return 'Hong Kong';
        case 'HU':
            return 'Hungary';
        case 'IS':
            return 'Iceland';
        case 'IN':
            return 'India';
        case 'ID':
            return 'Indonesia';
        case 'IR':
            return 'Iran';
        case 'IQ':
            return 'Iraq';
        case 'IE':
            return 'Ireland';
        case 'IM':
            return 'Isle of Man';
        case 'IL':
            return 'Israel';
        case 'IT':
            return 'Italy';
        case 'JM':
            return 'Jamaica';
        case 'JP':
            return 'Japan';
        case 'JE':
            return 'Jersey';
        case 'JO':
            return 'Jordan';
        case 'KZ':
            return 'Kazakhstan';
        case 'KE':
            return 'Kenya';
        case 'KI':
            return 'Kiribati';
        case 'KP':
            return 'Korea';
        case 'KR':
            return 'Korea';
        case 'KW':
            return 'Kuwait';
        case 'KG':
            return 'Kyrgyz Republic';
        case 'LA':
            return 'Lao';
        case 'LV':
            return 'Latvia';
        case 'LB':
            return 'Lebanon';
        case 'LS':
            return 'Lesotho';
        case 'LR':
            return 'Liberia';
        case 'LY':
            return 'Libyan Arab Jamahiriya';
        case 'LI':
            return 'Liechtenstein';
        case 'LT':
            return 'Lithuania';
        case 'LU':
            return 'Luxembourg';
        case 'MO':
            return 'Macao';
        case 'MK':
            return 'Macedonia';
        case 'MG':
            return 'Madagascar';
        case 'MW':
            return 'Malawi';
        case 'MY':
            return 'Malaysia';
        case 'MV':
            return 'Maldives';
        case 'ML':
            return 'Mali';
        case 'MT':
            return 'Malta';
        case 'MH':
            return 'Marshall Islands';
        case 'MQ':
            return 'Martinique';
        case 'MR':
            return 'Mauritania';
        case 'MU':
            return 'Mauritius';
        case 'YT':
            return 'Mayotte';
        case 'MX':
            return 'Mexico';
        case 'FM':
            return 'Micronesia';
        case 'MD':
            return 'Moldova';
        case 'MC':
            return 'Monaco';
        case 'MN':
            return 'Mongolia';
        case 'ME':
            return 'Montenegro';
        case 'MS':
            return 'Montserrat';
        case 'MA':
            return 'Morocco';
        case 'MZ':
            return 'Mozambique';
        case 'MM':
            return 'Myanmar';
        case 'NA':
            return 'Namibia';
        case 'NR':
            return 'Nauru';
        case 'NP':
            return 'Nepal';
        case 'AN':
            return 'Netherlands Antilles';
        case 'NL':
            return 'Netherlands';
        case 'NC':
            return 'New Caledonia';
        case 'NZ':
            return 'New Zealand';
        case 'NI':
            return 'Nicaragua';
        case 'NE':
            return 'Niger';
        case 'NG':
            return 'Nigeria';
        case 'NU':
            return 'Niue';
        case 'NF':
            return 'Norfolk Island';
        case 'MP':
            return 'Northern Mariana Islands';
        case 'NO':
            return 'Norway';
        case 'OM':
            return 'Oman';
        case 'PK':
            return 'Pakistan';
        case 'PW':
            return 'Palau';
        case 'PS':
            return 'Palestinian Territory';
        case 'PA':
            return 'Panama';
        case 'PG':
            return 'Papua New Guinea';
        case 'PY':
            return 'Paraguay';
        case 'PE':
            return 'Peru';
        case 'PH':
            return 'Philippines';
        case 'PN':
            return 'Pitcairn Islands';
        case 'PL':
            return 'Poland';
        case 'PT':
            return 'Portugal, Portuguese Republic';
        case 'PR':
            return 'Puerto Rico';
        case 'QA':
            return 'Qatar';
        case 'RE':
            return 'Reunion';
        case 'RO':
            return 'Romania';
        case 'RU':
            return 'Russian Federation';
        case 'RW':
            return 'Rwanda';
        case 'BL':
            return 'Saint Barthelemy';
        case 'SH':
            return 'Saint Helena';
        case 'KN':
            return 'Saint Kitts and Nevis';
        case 'LC':
            return 'Saint Lucia';
        case 'MF':
            return 'Saint Martin';
        case 'PM':
            return 'Saint Pierre and Miquelon';
        case 'VC':
            return 'Saint Vincent and the Grenadines';
        case 'WS':
            return 'Samoa';
        case 'SM':
            return 'San Marino';
        case 'ST':
            return 'Sao Tome and Principe';
        case 'SA':
            return 'Saudi Arabia';
        case 'SN':
            return 'Senegal';
        case 'RS':
            return 'Serbia';
        case 'SC':
            return 'Seychelles';
        case 'SL':
            return 'Sierra Leone';
        case 'SG':
            return 'Singapore';
        case 'SK':
            return 'Slovakia (Slovak Republic)';
        case 'SI':
            return 'Slovenia';
        case 'SB':
            return 'Solomon Islands';
        case 'SO':
            return 'Somalia, Somali Republic';
        case 'ZA':
            return 'South Africa';
        case 'GS':
            return 'South Georgia and the South Sandwich Islands';
        case 'ES':
            return 'Spain';
        case 'LK':
            return 'Sri Lanka';
        case 'SD':
            return 'Sudan';
        case 'SR':
            return 'Suriname';
        case 'SJ':
            return 'Svalbard & Jan Mayen Islands';
        case 'SZ':
            return 'Swaziland';
        case 'SE':
            return 'Sweden';
        case 'CH':
            return 'Switzerland, Swiss Confederation';
        case 'SY':
            return 'Syrian Arab Republic';
        case 'TW':
            return 'Taiwan';
        case 'TJ':
            return 'Tajikistan';
        case 'TZ':
            return 'Tanzania';
        case 'TH':
            return 'Thailand';
        case 'TL':
            return 'Timor-Leste';
        case 'TG':
            return 'Togo';
        case 'TK':
            return 'Tokelau';
        case 'TO':
            return 'Tonga';
        case 'TT':
            return 'Trinidad and Tobago';
        case 'TN':
            return 'Tunisia';
        case 'TR':
            return 'Turkey';
        case 'TM':
            return 'Turkmenistan';
        case 'TC':
            return 'Turks and Caicos Islands';
        case 'TV':
            return 'Tuvalu';
        case 'UG':
            return 'Uganda';
        case 'UA':
            return 'Ukraine';
        case 'AE':
            return 'United Arab Emirates';
        case 'GB':
            return 'United Kingdom';
        case 'US':
            return 'United States of America';
        case 'UM':
            return 'United States Minor Outlying Islands';
        case 'VI':
            return 'United States Virgin Islands';
        case 'UY':
            return 'Uruguay, Eastern Republic of';
        case 'UZ':
            return 'Uzbekistan';
        case 'VU':
            return 'Vanuatu';
        case 'VE':
            return 'Venezuela';
        case 'VN':
            return 'Vietnam';
        case 'WF':
            return 'Wallis and Futuna';
        case 'EH':
            return 'Western Sahara';
        case 'YE':
            return 'Yemen';
        case 'ZM':
            return 'Zambia';
        case 'ZW':
            return 'Zimbabwe';
    }
    return '—';
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