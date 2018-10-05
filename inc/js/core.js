var documentid = document.getElementById('title');
$(document).ready(function () {
    $("#content").load("../inc/php/pages/dashboard.php", function () {
        console.log('loaded page');
        documentid.innerHTML = "Dashboard";
    });
});
$(".nm-nav-item").click(function () {
    if (this.hasAttribute('addon')) {
        if (!($(this).hasClass('nm-selected-entry'))) {
            var key = $(this).attr('addon');
            $(".nm-nav-item").each(function () {
                $(this).removeClass('nm-selected-entry');
            });
            $(this).addClass('nm-selected-entry');
            $("#content").load("../addons/" + key + "/addon.php", function () {
                console.log('loaded page');
            });
            documentid.innerHTML = this.name;
            $('#content').offset().top = 0;
        }
    } else if (!($(this).hasClass('nm-selected-entry'))) {
        var key = $(this).attr('aria-label');
        $(".nm-nav-item").each(function () {
            $(this).removeClass('nm-selected-entry');
        });
        $(this).addClass('nm-selected-entry');
        //load page
        $("#content").load("../inc/php/pages/" + key + ".php", function () {
            console.log('loaded page');
        });
        documentid.innerHTML = this.name;
        $('#content').offset().top = 0;
    }
});
$(document.body).on('click', 'a', function () {
    if (this.hasAttribute('player')) {
        documentid.innerHTML = 'Player Information';
        $("#content").load("../inc/php/pages/player.php");
        $(".nm-nav-item").each(function () {
            $(this).removeClass('nm-selected-entry');
        });
    } else if (this.hasAttribute('punishment')) {
        var attr = $(this).attr('punishment');
        documentid.innerHTML = 'Punishment Information';
        $("#content").load("../inc/php/pages/punishment.php?loadid=" + attr);
        $(".nm-nav-item").each(function () {
            $(this).removeClass('nm-selected-entry');
        });
    } else if (this.hasAttribute('ticket')) {
        attr = $(this).attr('ticket');
        documentid.innerHTML = 'Ticket';
        $("#content").load("../inc/php/pages/ticket.php?id=" + attr, function (data) {
            if (data === 'no permission') {
                swal({
                    title: "Error",
                    text: "You do not have permission to view this ticket!",
                    type: "warning",
                    closeOnConfirm: true
                });
            }
        });
        $(".nm-nav-item").each(function () {
            $(this).removeClass('nm-selected-entry');
        });
    }
});

function handlePermission(permission) {
    $.get('../inc/php/dep/permission.php?haspermission=' + permission, function (res) {
        if (!res) {

        }
    });
}

var notifications = setInterval(notify, 5000);

function notify() {
    if (!("Notification" in window)) {
        console.log("This browser does not support desktop notification!");
        return;
    }
    $.get("../inc/php/dep/notifications.php", function (data) {
        var jsonData = JSON.parse(data);
        if (jsonData.notifications == null) {
            return;
        }
        console.log(jsonData.notifications);
        for (var i = 0; i < jsonData.notifications.length; ++i) {
            var counter = jsonData.notifications[i];
            console.log(counter);
            if (!Notification) {
                return;
            }
            if (Notification.permission !== "granted") {
                console.log("Not allowed to send Notifications... Requesting permission.");
                Notification.requestPermission();
            } else {
                console.log("Allowed to send notifications");
                var notification = new Notification('NetworkManager', {
                    icon: '../inc/img/logo.png',
                    body: counter.notification
                });
                /*notification.onclick = function () {
                    if (counter.notification.includes("joined your network")) {
                        documentid.innerHTML = 'Players';
                        $("#content").load("../inc/php/pages/players.php?p=1", function () {
                        });
                    } else if (counter.notification.includes("warn") || counter.notification.includes("kick") || counter.notification.includes("mute") || counter.notification.includes("ban")) {
                        documentid.innerHTML = 'Punishments';
                        $("#content").load("../inc/php/pages/punishments.php?p=1", function () {
                        });
                    } else if (counter.notification.includes("report")) {
                        documentid.innerHTML = 'Reports';
                        $("#content").load("../inc/php/pages/reports.php?p=1", function () {
                        });
                    }
                };*/
            }
        }
    });
}