<?php
include '../dep/permissions.php';
$defaultlang = 'English';
$jsondata = file_get_contents('../../json/settings.json');
$websettings = json_decode($jsondata,true);
foreach ($websettings as $variable => $value) {
    if($variable == 'default-language') {
        $defaultlang = $value;
    }
}
handlePermission('view_reports');
if(isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
    $langdir = "../dep/languages/";
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
    include '../dep/languages/' . $defaultlang . '.php';
}
?>
<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="data-card" style="margin-top: 20px">
                    <div class="data-card-header">
                        <div class="data-card-search-box">
                            <i class="material-icons">search</i>
                            <form>
                                <input type="text" id="search-input" class="data-card-input" placeholder="<?php echo $lang['REPORTS_SEARCH']; ?>" autocomplete="off">
                            </form>
                        </div>
                    </div>
                    <div id="data-card-content">
                        <div class="data-card-body">
                            <div class="nm-search-results-empty"><?php echo $lang['VAR_LOADING']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#data-card-content").load('../inc/php/dep/pages/reports.php?p=1', function () {
            console.log('load performed');
        });
    });

    $( "input#search-input" ).focus(function() {
        console.log('triggered');
        $(".data-card-header").each(function() {
            $(this).addClass('is-editing');
        });
    });

    $( "input#search-input" ).focusout(function() {
        console.log('triggered');
        $(".data-card-header").each(function() {
            $(this).removeClass('is-editing');
        });
        if($(this).val().length == 0) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/reports.php?p=1', function () {
                console.log('load performed');
            });
        }
    });
    $(document).on('click','a',function(){
        if(this.hasAttribute('reports')) {
            var attr = $(this).attr('page');
            if (typeof attr !== typeof undefined && attr !== false) {
                $( "#data-card-content" ).load('../inc/php/dep/pages/reports.php?p=' + attr, function () {
                    console.log('load performed');
                });
            }
        } else if(this.hasAttribute('reportsearch')) {
            var attr = $(this).attr('page');
            var query = $(this).attr('query');
            if (typeof attr !== typeof undefined && attr !== false) {
                $( "#data-card-content" ).load('../inc/php/dep/pages/reportsearch.php?p=' + attr + '&q=' + query, function () {
                    console.log('reportsearch load performed');
                });
            }
        }
    });

    /* ---------- PLAYER SEARCH -------------- */
    $("input#search-input").keyup(function() {
        $( "#data-card-content" ).load('../inc/php/dep/pages/reportsearch.php?p=1&q=' + this.value, function () {
            console.log('punishmentsearchb load performed');
        });
        if($(this).val().length === 0) {
            console.log($(this).val().length);
            $( "#data-card-content" ).load('../inc/php/dep/pages/reports.php?p=1', function () {
                console.log('load performed');
            });
        }
    });
</script>