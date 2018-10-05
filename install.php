<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NetworkManager</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script defer src="//code.getmdl.io/1.3.0/material.min.js"></script>
</head>
<style>

    @import url('https://fonts.googleapis.com/css?family=Roboto:100,300,400');

    #header {
        transition: all 0.5s ease;
    }

    body {
        background: rgb(29, 38, 113) linear-gradient(to left, rgb(195, 55, 100), rgb(29, 38, 113));
        font-family: 'Roboto', sans-serif !important;
    }

    .mdl-layout {
        align-items: center;
        justify-content: center;
    }

    .mdl-layout__content {
        padding: 24px;
        flex: none;
    }

    .mdl-color--grey-100 {
        background-color: transparent !important;
    }

    h1 {
        color: white;
        font-weight: 100;
    }

</style>
<body>
<div class="mdl-layout mdl-js-layout mdl-color--grey-100">
    <main class="mdl-layout__content">
        <h1 id="motd">Hello There!</h1>
        <div id="install">

        </div>
    </main>
</div>
<script>
    $(document).ready(function () {

        setTimeout(function () {
            $("#motd").fadeOut(function () {
                setTimeout(function () {
                }, 4000);
                $("#motd").text("This is NetworkManager.").fadeIn();
                setTimeout(function () {
                    $("#motd").fadeOut(function () {
                        $("#motd").text("Let's install this!").fadeIn();
                        setTimeout(function () {
                            $("#motd").fadeOut(function () {
                                $('#install').html('<div class="mdl-card mdl-shadow--6dp"> ' +
                                    '<form id="install-form"> ' +
                                    '<div class="mdl-card__title mdl-color--primary mdl-color-text--white" id="header"> ' +
                                    '<h2 class="mdl-card__title-text" id="title">NetworkManager</h2> ' +
                                    '</div> ' +
                                    '<div class="mdl-card__supporting-text"> ' +
                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="text" id="host" name="host" required> <label class="mdl-textfield__label" for="host">Database Host</label> ' +
                                    '</div>' +
                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="text" id="port" name="port" value="3306" required> <label class="mdl-textfield__label" for="port">Database Port</label> ' +
                                    '</div>' +
                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="text" id="database" name="database" required> <label class="mdl-textfield__label" for="database">Database Name</label> ' +
                                    '</div>' +
                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="text" id="username" name="username" required> <label class="mdl-textfield__label" for="username">Database User</label>' +
                                    '</div>' +
                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="password" id="password" name="password" required> <label class="mdl-textfield__label" for="password">Database Password</label> ' +
                                    '</div>' +
                                    '<div class="mdl-card__actions"><button  style="width: 100%" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" type="submit" id="button">Install Now!</button>' +
                                    '</div>' +
                                    '</div>' +
                                    '</form>' +
                                    '</div>').fadeIn();
                                componentHandler.upgradeDom();
                            });
                        }, 4000);
                    });
                }, 4000);
            });
        }, 4000);
    });

    $(document).on("submit", "form", function (e) {
        if (this.id === 'install-form') {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: 'install_data.php',
                data: data,
                success: function (response) {
                    if (response === 'true') {
                        document.getElementById("title").innerHTML = "Installed successfully";
                        document.getElementById("header").style.cssText = 'background-color:green !important';
                        document.getElementById("button").disabled = true;
                        setTimeout(function () {
                            $("#install").fadeOut(function () {
                                $("#motd").text("Awesome!").fadeIn();
                                setTimeout(function () {
                                    $("#motd").fadeOut(function () {
                                        $("#motd").text("Create your first account.").fadeIn();
                                        setTimeout(function () {
                                            $("#motd").fadeOut(function () {
                                                $('#install').html('<div class="mdl-card mdl-shadow--6dp"> ' +
                                                    '<form id="register-form"> ' +
                                                    '<div class="mdl-card__title mdl-color--primary mdl-color-text--white" id="header"> ' +
                                                    '<h2 class="mdl-card__title-text" id="title">NetworkManager</h2> ' +
                                                    '</div> ' +
                                                    '<div class="mdl-card__supporting-text"> ' +
                                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="text" id="username" name="username" required> <label class="mdl-textfield__label" for="host">Username</label> ' +
                                                    '</div>' +
                                                    '<div class="mdl-textfield mdl-js-textfield"> <input class="mdl-textfield__input" type="password" id="password" name="password" required> <label class="mdl-textfield__label" for="database">Password</label> ' +
                                                    '</div>' +
                                                    '<div class="mdl-card__actions"><button  style="width: 100%" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" type="submit" id="button">Create Now!</button>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</form>' +
                                                    '</div>').fadeIn();
                                                componentHandler.upgradeDom();
                                            });
                                        }, 4000);
                                    });
                                }, 4000);
                            });
                        }, 4000);
                    } else if (response === 'file') {
                        document.getElementById("title").innerHTML = "Cannot write to file";
                        document.getElementById("header").style.cssText = 'background-color:red !important';

                        setTimeout(function () {
                            document.getElementById("title").innerHTML = "NetworkManager";
                            document.getElementById("header").style.backgroundColor = '';
                        }, 2500);
                    } else if (response === 'connection') {
                        document.getElementById("title").innerHTML = "Cannot connect to database";
                        document.getElementById("header").style.cssText = 'background-color:red !important';

                        setTimeout(function () {
                            document.getElementById("title").innerHTML = "NetworkManager";
                            document.getElementById("header").style.backgroundColor = '';
                        }, 2500);
                    } else if (response === 'no tables') {
                        document.getElementById("title").innerHTML = "Plugin not installed and configured";
                        document.getElementById("header").style.cssText = 'background-color:red !important';

                        setTimeout(function () {
                            document.getElementById("title").innerHTML = "NetworkManager";
                            document.getElementById("header").style.backgroundColor = '';
                        }, 2500);
                    }
                    // DEBUG
                    console.log(response);
                }
            });
        } else if (this.id === 'register-form') {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: 'install_data.php',
                data: data,
                success: function (response) {
                    if (response === 'true') {
                        document.getElementById("title").innerHTML = "Registered";
                        document.getElementById("header").style.cssText = 'background-color:green !important';
                        document.getElementById("button").disabled = true;
                        setTimeout(function () {
                            $("#install").fadeOut(function () {
                                $("#motd").text("Great!").fadeIn();
                                setTimeout(function () {
                                    $("#motd").fadeOut(function () {
                                        $("#motd").text("Go ahead and login!").fadeIn();
                                        setTimeout(function () {
                                            $("#motd").fadeOut(function () {
                                                window.location.href = "dashboard/login.php";
                                            });
                                        }, 4000);
                                    });
                                }, 4000);
                            });
                        }, 4000);
                    } else if (response === 'connection') {
                        document.getElementById("title").innerHTML = "Something went wrong!";
                        document.getElementById("header").style.cssText = 'background-color:red !important';

                        setTimeout(function () {
                            document.getElementById("title").innerHTML = "NetworkManager";
                            document.getElementById("header").style.backgroundColor = '';
                        }, 2500);
                    }
                    console.log(response);
                }
            });
        }
    });
</script>
</body>
</html>