$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'change-password') {
        var data = $(this).serialize();
        $.get("../inc/php/dep/pages/accountmanagement.php?type=change_password&" + data, function (data) {
            swal(data);
            //add modal here
        });
    } else if (this.id === 'change-language') {
        var data = $(this).serialize();
        var success = true;
        $.get("../inc/php/dep/pages/accountmanagement.php?type=change_language&" + data, function (response) {
            console.log(response);
            if (response.toString().indexOf('Could not write to the settings file!') >= 0) {
                success = false;
                swal({
                    title: "Error",
                    text: "Could not write to the settings file!",
                    type: "warning",
                });
            }
            if (response.toString().indexOf('Caught exception: Could not open ') >= 0) {
                success = false;
                swal({
                    title: "Error",
                    text: "Could not write to the language folder",
                    type: "warning",
                });
            }
			if (response.toString().indexOf('Status Code:') >= 0) {
                success = false;
                swal({
                    title: "Error",
                    text: "Could not download requested language file!\n" + response.toString().replace('Status Code: ') + ' error code.',
                    type: "warning",
                });
            }
            if (response.toString().indexOf('Downloaded!') >= 0) {
                swal({
                    title: "Success",
                    text: "Successfully changed language!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, reload!",
                    closeOnConfirm: true
                }, function () {
                    var d = new Date();
                    d.setTime(d.getTime() + 5000000);
                    var expires = "expires=" + d.toUTCString();
                    document.cookie = data + ";" + expires + "; path=/";
                    console.log('reloading');
                    location.reload();
                });
            }
        }).fail(function (error) {
            swal({
                title: "Error",
                text: "An unknown error occured!\nPlease check your browser console for more info.",
                type: "warning",
            });
            console.log(error);
            console.log(error.status);
        });
    } else if (this.id === 'change-theme') {
        var data = $(this).serialize();
        var d = new Date();
        d.setTime(d.getTime() + 5000000);
        var expires = "expires=" + d.toUTCString();
        document.cookie = data + ";" + expires + "; path=/";
        location.reload();
    }
});

$.getJSON('https://api.github.com/repos/ChimpGamer/NetworkManager/contents/Webbie/languages', function (data) {
    $.each(data, function (i, obj) {
        var name = obj.name.replace(/\.[^.$]+$/, '');
        var selected = '';
        if (getCookie('language') === name) {
            selected = 'selected';
        }
        $('#language').append($('<option ' + selected + ' >').text(name).attr('value', name));
    });
});

$.getJSON('https://api.github.com/repos/ChimpGamer/NetworkManager/contents/Webbie/themes', function (data) {
    $.each(data, function (i, obj) {
        var name = obj.name.replace(/\.[^.$]+$/, '');
        var selected = '';
        if (getCookie('theme') + '.php' === name) {
            selected = 'selected';
        }
        $('#theme').append($('<option ' + selected + ' >').text(name).attr('value', name));
    });
});

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

var isAccountLoaded = true;