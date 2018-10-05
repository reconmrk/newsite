<?php
include '../dep/permissions.php';
$defaultlang = 'English';
$jsondata = file_get_contents('../../json/settings.json');
$websettings = json_decode($jsondata, true);
foreach ($websettings as $variable => $value) {
    if ($variable == 'default-language') {
        $defaultlang = $value;
    }
}
if (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../dep/languages/";
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
    include '../dep/languages/' . $defaultlang . '.php';
}
?>

<div class="container">
    <div class="content">
        <div class="col-3">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_TOTALPLAYERS']; ?>
                </div>
                <div class="box-body">
                    <div class="small-stats">
                        <!-- <p>150 <small>players</small></p> -->
                        <p dataload="totalplayers"><?php echo $lang['VAR_LOADING']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_TODAYONLINEPLAYERS']; ?>
                </div>
                <div class="box-body">
                    <div class="small-stats">
                        <!-- <p>150 <small>players</small></p> -->
                        <p dataload="todayplayers"><?php echo $lang['VAR_LOADING']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_TODAYSNEWPLAYERS']; ?>
                </div>
                <div class="box-body">
                    <div class="small-stats">
                        <!-- <p>150 <small>players</small></p> -->
                        <p dataload="newplayers"><?php echo $lang['VAR_LOADING']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_TODAYSPLAYTIME']; ?>
                </div>
                <div class="box-body">
                    <div class="small-stats">
                        <!-- <p>150 <small>sessions</small></p> -->
                        <p dataload="todayplaytime"><?php echo $lang['VAR_LOADING']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_PLAYERSTATISTICS']; ?>
                    <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                class="tooltiptext"><?php echo $lang['DASHBOARD_PLAYERSTATISTICS_TOOLTIP']; ?></span>
                    </div>
                </div>
                <div class="box-body">
                    <div id="container" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <?php echo $lang['DASHBOARD_NEWESTPLAYERS']; ?>
                    <div class="tooltip"><i class="material-icons help-icon">help_outline</i><span
                                class="tooltiptext"><?php echo $lang['DASHBOARD_NEWESTPLAYERS_TOOLTOP']; ?></span></div>
                </div>
                <div class="box-body">
                    <div id="livemap">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("p").each(function () {
            if (this.hasAttribute('dataload')) {
                var element = this;

                $.ajax({
                    url: '../inc/php/dep/pages/dashboard.php?dataload=' + this.getAttribute('dataload'),
                    type: 'get',
                    success: function (data) {
                        $(element).text(data);
                    }
                });
            }
        });
    });

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Roboto Th'
            }
        }
    });
    var chart = Highcharts.chart('container', {

        chart: {
            type: 'areaspline',
            backgroundColor: 'transparent',
            zoomType: 'x'
        },

        title: {
            text: '',
            style: {
                fontSize: '18px',
                color: '#212121'
            },
            align: 'left'
        },

        yAxis: {
            title: {
                text: ''
            },
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            labels: {
                style: {
                    color: '#B6B6B6',
                    fontSize: '12px'
                }
            }
        },
        xAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            labels: {
                style: {
                    color: '#B6B6B6',
                    fontSize: '12px'
                }
            },
            type: 'datetime',
            events: {
                afterSetExtremes: function (event) {
                    var date = new Date(event.min);
                    var datevalues = date.getFullYear()
                        + '-' + date.getMonth() + 1
                        + '-' + date.getDate()
                        + ' ' + date.getUTCHours()
                        + ':' + date.getMinutes()
                        + ':' + date.getSeconds();
                    $("#timestamp").text(datevalues);
                }
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },

        plotOptions: {
            series: {
                pointStart: 2010,
                fillOpacity: 0.2,
                marker: {
                    enabled: false
                }
            }
        },
        tooltip: {
            backgroundColor: '#FFFFFF',
            borderColor: '#FFFFFF',
            borderRadius: 2,
            borderWidth: 1
        }
    });
    $.get("../inc/php/dep/charts/dashboard.php?type=newplayers", function (data) {
        chart.addSeries({
            name: 'New Players',
            color: '#2196F3',
            data: JSON.parse(data)
        });
    });

    $.get("../inc/php/dep/charts/dashboard.php?type=sessions", function (data) {
        chart.addSeries({
            name: 'Total Sessions',
            dashStyle: 'ShortDash',
            color: '#2196F3',
            data: JSON.parse(data)
        });
    });
    $.get("../inc/php/dep/charts/dashboard.php?type=peak", function (data) {
        chart.addSeries({
            name: 'Player Peak',
            dashStyle: 'Dot',
            color: '#2196F3',
            data: JSON.parse(data)
        });
    });

    var map = Highcharts.mapChart('livemap', {
        chart: {
            map: 'custom/world',
            backgroundColor: 'transparent'
        },

        title: {
            text: '',
            align: 'left'
        },

        legend: {
            enabled: false
        },

        credits: {
            enabled: false
        },

        tooltip: {
            backgroundColor: '#FFFFFF',
            borderColor: '#FFFFFF',
            borderRadius: 2,
            borderWidth: 1
        },

        mapNavigation: {
            enabled: false
        },

        series: [{
            name: 'Countries',
            enableMouseTracking: false
        }]
    });

    $.get("../inc/php/dep/charts/dashboard.php?type=map", function (data) {
        map.addSeries({
            type: 'mapbubble',
            name: 'Newest Players',
            joinBy: ['iso-a2', 'code'],
            data: JSON.parse(data),
            minSize: 4,
            color: '#2196F3',
            maxSize: '20%',
            tooltip: {
                pointFormat: '<b>{point.name}</b>: {point.z} players',
                headerFormat: ''
            }

        });
    });

</script>