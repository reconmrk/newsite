<?php
include '../permissions.php';
handlePermission('view_analytics');
$type = $_GET['type'];

switch ($type) {
    case 'newplayers':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, count(id) as amount from nm_players where firstlogin > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'sessions':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(start/1000) as date) as day, count(id) as amount from nm_sessions where start > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'peak':
        $result = '';
        $result = $result . '[';
        $total = 0;
        $stmnt = $pdo->query('SELECT cast(from_unixtime(LOWER(time)/1000) as date) as day, MAX(LOWER(online)) as amount from nm_serverAnalytics where LOWER(time) > unix_timestamp(date_sub(cast(now() as date), interval 60 day))*1000 group by day;');
        $data_result = $stmnt->fetchAll();
        $data = array();
        foreach ($data_result as $row) {
            $data[] = $row;
            if ($stmnt->rowCount() == ($total + 1)) {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . ']';
            } else {
                $result = $result . '[' . strtotime($row['day']) * 1000 . ',' . $row['amount'] . '],';
            }
            $total++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'map':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(country) AS count FROM nm_players GROUP BY country');
        $total = 0;
        $data = '[';
        $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            if ($stmnt->rowCount() == ($total + 1)) {
                $data = $data . '{"code": "' . $row['country'] . '", "z": ' . $row['count'] . '}';
            } else {
                $data = $data . '{"code": "' . $row['country'] . '", "z": ' . $row['count'] . '},';
            }
            $total++;
        }
        $data = $data . ']';
        echo $data;
        break;
    case 'countrynames':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(country) AS count FROM nm_players GROUP BY country ORDER BY count DESC');
        $data = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        $result = '[';
        $amount = 0;
        foreach ($data as $item) {
            if ($stmnt->rowCount() == ($amount + 1)) {
                $result = $result . '"' . countryCodeToCountry($item['country']) . '"';
            } else {
                $result = $result . '"' . countryCodeToCountry($item['country']) . '", ';
            }
            $amount++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'countrydata':
        $stmnt = $pdo->query('SELECT DISTINCT(country) as country, count(country) AS count FROM nm_players GROUP BY country ORDER BY count DESC');
        $data = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        $result = '[';
        $amount = 0;
        foreach ($data as $item) {
            if ($stmnt->rowCount() == ($amount + 1)) {
                $result = $result . $item['count'];
            } else {
                $result = $result . $item['count'] . ', ';
            }
            $amount++;
        }
        $result = $result . ']';
        echo $result;
        break;
    case 'future':
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, count(id) as amount from nm_players where firstlogin > unix_timestamp(date_sub(cast(now() as date), interval 600 day))*1000 group by day;');

        $totalplayers = 0;
        $before = 0;
        $count = 0;
        $datebefore = round(microtime(true));
        $dayamount = array();
        $values = array();
        foreach ($stmnt->fetchAll() as $item) {
            if ($count > 0) {
                $now = strtotime($item['day']);
                $your_date = $datebefore;
                $datediff = $now - $your_date;
                $diffdays = floor($datediff / (60 * 60 * 24));
                $dayamount[] = $diffdays;
                $calc = (((($item['amount'] + $totalplayers) - $totalplayers) / $totalplayers) / $diffdays) * 100;
                $values[] = $calc;
            }
            $datebefore = strtotime($item['day']);
            $before = $item['amount'];
            $totalplayers += $item['amount'];
            $count++;
        }
        $multiplicator = (array_sum($values) / count($values));
        $daytiffamount = (array_sum($dayamount) / count($dayamount));
        $data = '[';
        $date = new DateTime();
        $res = $totalplayers;
        for ($i = 0; $i < 5; $i++) {
            if ($i == 0) {
                $data = $data . '[' . ($date->getTimestamp() * 1000) . ', ' . $totalplayers . '], ';
            } else {
                $res = $res * (($multiplicator / 100) + 1);
                $date->modify('+' . intval($daytiffamount) . ' days');
                if ($i == 4) {
                    $data = $data . '[' . ($date->getTimestamp() * 1000) . ', ' . $res . ']';
                } else {
                    $data = $data . '[' . ($date->getTimestamp() * 1000) . ', ' . $res . '], ';
                }
            }
        }
        $data = $data . ']';
        echo $data;
        break;
    case 'retention':
        $stmnt = $pdo->query('SELECT cast(from_unixtime(firstlogin/1000) as date) as day, uuid as uuid FROM nm_players');
        if ($stmnt->rowCount() == 0) {
            die('[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]');
        }
        $players = $stmnt->fetchAll();

        $stmnt = $pdo->query('SELECT cast(from_unixtime(start/1000) as date) as day, uuid as uuid FROM nm_sessions');
        if ($stmnt->rowCount() == 0) {
            die('[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]');
        }
        $sessions = $stmnt->fetchAll();

        $playersssions = array();
        $session_count = count($sessions);
        $player_count = count($players);

        foreach ($players as $player) {
            $data = array();
            $data[] = 1;
            $date = new DateTime($player['day']);
            for ($i = 0; $i < 7; $i++) {
                $date->modify('+1 days');
                $amount = 0;
                foreach ($sessions as $session) {
                    if ($amount == ($session_count - 1)) {
                        $data[] = 0;
                        break 2;
                    }
                    if ($session['uuid'] == $player['uuid']) {
                        $sessiondate = new DateTime($session['day']);

                        if ($sessiondate->getTimestamp() == $date->getTimestamp()) {
                            $data[] = 1;
                            break 2;
                        }
                    }
                    $amount++;
                }
            }

            $playersssions[] = $data;
        }

        $result = array();
        foreach ($playersssions as $session) {
            $amount = 0;
            for ($i = 0; $i < count($session); $i++) {
                //echo 'Session: ' . $session[$i] . "\n";
                $amount +=
                    $session[$i];
            }
            $result[] = $amount;
        }

        $retention = array();
        for ($i = 0; $i < 7; $i++) {
            //echo 'result: ' . $result[$i] . "\n";
            $retention[] = floatval(($result[$i] / $player_count) * 100);
        }

        $response = '[';
        for ($i = 0; $i < 7; $i++) {
            if ($i == 6) {
                $response = $response . $retention[$i];
            } else {
                $response = $response . $retention[$i] . ', ';
            }
        }
        $response = $response . ']';
        echo $response;
        break;
}

function countryCodeToCountry($code) {
    $code = strtoupper($code);
    switch ($code) {
        case 'AF':
            return 'Afghanistan';
        case 'UNDEFINED':
            return 'Not Set';
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