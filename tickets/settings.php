<?php
include 'inc/php/header.php';
?>
    <h3 style="font-weight: bold">Settings</h3>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p><?php echo $lang['SETTINGS_LANGUAGE']; ?></p>
                    <form id="change_language">
                        <div class="form-group">
                            <select id="ticket_language" class="form-control" name="ticket_language">
                                <?php
                                $language = $_COOKIE['ticket_language'];
                                $langdir = 'inc/php/languages/';
                                if (is_dir($langdir)) {
                                    if ($dh = opendir($langdir)) {
                                        while (($file = readdir($dh)) !== false) {
                                            if ($file != '..' && $file != '.') {
                                                $filename = pathinfo($file, PATHINFO_FILENAME);
                                                if ($language == $filename) {
                                                    echo '<option selected value=' . $filename . '>' . $filename . '</option>';
                                                } else {
                                                    echo '<option value=' . $filename . '>' . $filename . '</option>';
                                                }
                                            }
                                        }
                                        closedir($dh);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $lang['VAR_SAVE']; ?></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p><?php echo $lang['SETTINGS_THEME']; ?></p>
                    <form id="change_theme">
                        <div class="form-group">
                            <select id="ticket_theme" class="form-control" name="ticket_theme">
                                <?php
                                $theme = $_COOKIE['ticket_theme'];
                                $themesdir = 'inc/css/themes/';
                                if (is_dir($themesdir)) {
                                    if ($dh = opendir($themesdir)) {
                                        while (($file = readdir($dh)) !== false) {
                                            if ($file != '..' && $file != '.') {
                                                $filename = pathinfo($file, PATHINFO_FILENAME);
                                                if ($theme == $filename) {
                                                    echo '<option selected value=' . $filename . '>' . $filename . '</option>';
                                                } else {
                                                    echo '<option value=' . $filename . '>' . $filename . '</option>';
                                                }
                                            }
                                        }
                                        closedir($dh);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $lang['VAR_SAVE']; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document.body).on('submit', 'form', function (e) {
            e.preventDefault();
            if (this.id === 'change_language') {
                let data = $(this).serialize();
                let d = new Date();
                d.setTime(d.getTime() + 5000000);
                let expires = "expires=" + d.toUTCString();
                document.cookie = data + ";" + expires + "; path=/";
                console.log('reloading');
                location.reload();
            } else if (this.id === 'change_theme') {
                let data = $(this).serialize();
                let d = new Date();
                d.setTime(d.getTime() + 5000000);
                let expires = "expires=" + d.toUTCString();
                document.cookie = data + ";" + expires + "; path=/";
                console.log('reloading');
                location.reload();
            }
        });
    </script>
<?php
include 'inc/php/footer.php';
?>