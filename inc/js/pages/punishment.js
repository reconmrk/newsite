/* -------------- Buttons ----------------*/
$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('delete-punishment')) {
        let id = this.getAttribute('delete-punishment');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this punishment!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/punishmentmanagement.php?type=delete_punishment&id=" + id, function (data) {
                $("#data-punishment-content").load('../inc/php/dep/pages/punishments.php?p=1', function () {
                    console.log('done delete-punishment');
                });
            });
        });
    } else if (this.hasAttribute('undo-punishment')) {
        let id = this.getAttribute('undo-punishment');
        $.get("../inc/php/dep/pages/punishmentmanagement.php?type=undo_punishment&id=" + id, function (data) {
            $("#data-punishment-content").load('../inc/php/dep/pages/punishment.php?id=' + id, function (data) {
                console.log('done undo-punishment');
            });
        });
    } else if (this.hasAttribute('edit-punishment')) {
        let id = this.getAttribute('edit-punishment');
        let type = this.getAttribute('ptype');
        $("#cached-modal").load("../inc/php/dep/pages/punishment.php?load=load_edit_punishment&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-punishment') {
                    x[i].style.display = "block"
                }
            }
            var select = document.getElementById('type');
            for (let i = 1; i < 20; i++) {
                var el = document.createElement('option');
                el.textContent = getPunishmentType(i);
                el.value = i;
                if (type == i) {
                    console.log('selected');
                    el.selected = true;
                }
                select.appendChild(el);
            }
        });
    } else if (this.hasAttribute('modal')) {
        let x = document.getElementsByClassName("modal");
        for (let i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if (this.hasAttribute('close')) {
        let x = document.getElementsByClassName("modal");
        for (let i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'nmedit-punishment-form') {
        let punishmentId = this.getAttribute('punishmentid');
        let data = $(this).serialize();
        console.log(data);
        swal({
            title: "Are you sure?",
            text: "You will not be able to revert these changes!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, apply it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/punishment.php?load=edit_punishment&id=" + punishmentId + "&" + data, function (data) {
                if (data === 'same uuid') {
                    swal({
                        title: "Error",
                        text: "Failed to update punishment!\nYou can't punish yourself!",
                        type: "warning",
                        closeOnConfirm: true
                    });
                }
                if(data.startsWith('false')) {
                    swal({
                        title: "Error",
                        text: "Failed to update punishment!\n" + data.replace('false', ''),
                        type: "warning",
                        closeOnConfirm: true
                    });
                }
                console.log(data);
                $("#data-punishment-content").load('../inc/php/dep/pages/punishment.php?id=' + punishmentId, function (data) {
                    console.log('done edit punishment');
                });
            });
        });
    }
});

$(document.body).on('click', 'a', function () {
    if (this.hasAttribute('nmbackpunishments')) {
        $("#content").load("../inc/php/pages/punishments.php", function () {
            console.log('done redirect punishments');
        });
    } else if (this.hasAttribute('nmbackreports')) {
        $("#content").load("../inc/php/pages/reports.php", function () {
            console.log('done redirect reports');
        });
    }
});

function getPunishmentType(type) {
    switch (type) {
        case 1:
            return 'ban';
        case 2:
            return 'global ban';
        case 3:
            return 'temporary ban';
        case 4:
            return 'global temporary ban';
        case 5:
            return 'ip ban';
        case 6:
            return 'global ip ban';
        case 7:
            return 'temporary ip ban';
        case 8:
            return 'global temporary ip ban';
        case 9:
            return 'mute';
        case 10:
            return 'global mute';
        case 11:
            return 'temporary mute';
        case 12:
            return 'global temporary mute';
        case 13:
            return 'ip mute';
        case 14:
            return 'global ip mute';
        case 15:
            return 'temporary ip mute';
        case 16:
            return 'global temporary ip mute';
        case 17:
            return 'kick';
        case 18:
            return 'global kick';
        case 19:
            return 'warn';
        case 20:
            return 'report';
        default:
            return 'unknown';
    }
}

var isPunishmentLoaded = true;