$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('nmedit-server')) {
        let id = this.getAttribute('nmedit-server');
        console.log(id);
        $("#cached-modal").load("../inc/php/dep/pages/servers.php?load=load_server&id=" + id, function () {
            let x = document.getElementsByClassName("modal");
            for (let i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-server') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-server')) {
        let id = this.getAttribute('nmdelete-server');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this server!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/servers.php?load=delete_server&id=" + id, function (data) {
                console.log(data);
                $("#data-card-content").load('../inc/php/dep/pages/servers.php', function () {
                    console.log('done delete server');
                });
            });
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

/* --------- CHECKBOX ---------------- */

$(document.body).on('click', '.switch-input', function () {
    if (this.hasAttribute('nmsetserverrestricted')) {
        console.log(this.id + " has been " + this.checked);
        if (this.checked) {
            $.get("../inc/php/dep/pages/servers.php?load=setrestricted&id=" + this.id + "&value=1", function (data) {
                console.log('saved');
            });
        } else {
            $.get("../inc/php/dep/pages/servers.php?load=setrestricted&id=" + this.id + "&value=0", function (data) {
                console.log('saved');
            });
        }
    } else if (this.hasAttribute('nmsetlobbyserver')) {
        console.log(this.getAttribute('nmsetlobbyserver') + " has been " + this.checked);
        if (this.checked) {
            $.get("../inc/php/dep/pages/servers.php?load=setlobby&id=" + this.getAttribute('nmsetlobbyserver') + "&value=1", function (data) {
                console.log('saved');
            });
        } else {
            $.get("../inc/php/dep/pages/servers.php?load=setlobby&id=" + this.getAttribute('nmsetlobbyserver') + "&value=0", function (data) {
                console.log('saved');
            });
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-server-form') {
        var data = $(this).serialize();
        console.log(data);
        let servername = $('#servername').val();
        $.get("../inc/php/dep/pages/servers.php?load=add_server&" + data, function (data) {
            console.log(data);
            if (data === 'false') {
                swal({
                    title: "Error",
                    text: "Failed to insert server into database!\nPerhaps a server with that name already exists?",
                    type: "warning",
                    closeOnConfirm: true
                });
            }
            if (data.toString().indexOf('Error: ') >= 0) {
                swal({
                    title: "Error",
                    text: "Failed to insert server into database!\n" + data.toString().replace('Error: '),
                    type: "warning",
                    closeOnConfirm: true
                });
            }
            if (data === 'true') {
                swal({
                    title: "Success",
                    text: "Successfully added your new server!\nExecute /nm servers reload " + servername + " to register it.",
                    type: "success",
                    closeOnConfirm: true
                });
            }
            $("#data-card-content").load('../inc/php/dep/pages/servers.php', function () {
                console.log('loaded');
            });
        });
    } else if (this.id === 'nmedit-server-form') {
        data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/servers.php?load=edit_server&id=" + this.getAttribute('server') + "&" + data, function (data) {
            console.log(data);
            $("#data-card-content").load('../inc/php/dep/pages/servers.php', function () {
                console.log('loaded');
            });
        });
    }
});

var isServersLoaded = true;