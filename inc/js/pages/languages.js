$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('nmedit-language')) {
        let id = this.getAttribute('nmedit-language');
        langId = id;
        $("#data-card-content").load('../inc/php/dep/pages/languages.php?load=edit_lang&id=' + id, function (data) {
            console.log('editlang load performed');
            document.getElementById("nm-languages-search-bar").style.display = "block";
        });
    } else if (this.hasAttribute('nmdelete-language')) {
        let id = this.getAttribute('nmdelete-language');
        if (id === '1') {
            alert('You cannot delete the default plugin language!');
            return;
        }
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this language!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/languages.php?load=delete&id=" + id, function (data) {
                $("#data-card-content").load('../inc/php/dep/pages/languages.php', function () {
                    console.log('done delete group');
                });
            });
        });
    } else if (this.hasAttribute('modal')) {
        var x = document.getElementsByClassName("modal");
        for (var i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if (this.hasAttribute('close')) {
        x = document.getElementsByClassName("modal");
        for (i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-language-form') {
        var data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/languages.php?load=create&" + data, function (data) {
            $("#data-card-content").load('../inc/php/dep/pages/languages.php', function () {
                console.log('done create language');
            });
        });
    }
});

$(document.body).on('keyup', 'input', function () {
    if (this.hasAttribute('lang_variable')) {
        var id = this.getAttribute('lang_id');
        var attr = this.getAttribute('lang_variable');
        var message = this.value
            .replace(new RegExp(' ', 'g'), ';v1')
            .replace(new RegExp('&', 'g'), ';v2')
            .replace(new RegExp('§', 'g'), ';v3')
            .replace(new RegExp('#', 'g'), ';v4')
            .replace(new RegExp('%', 'g'), ';v5');
        $.get("../inc/php/dep/pages/languages.php?load=update&id=" + id + "&variable=" + attr + "&message=" + message, function (data) {
            console.log(data);
            if (data.toString().indexOf('Error:') >= 0) {
                swal({
                    title: "Error",
                    text: data.toString().replace('Error: ', ''),
                    type: "warning",
                });
            }
        });
    }
});

var isLanguagesLoaded = true;