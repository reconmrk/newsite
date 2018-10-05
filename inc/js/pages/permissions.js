$("input#search-groups-input").focus(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).addClass('is-editing');
    });
});

$("input#search-groups-input").focusout(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).removeClass('is-editing');
    });
    if ($(this).val().length === 0) {
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
            console.log('group load performed');
        });
    }
});

$("input#search-users-input").focus(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).addClass('is-editing');
    });
});

$("input#search-users-input").focusout(function () {
    console.log('triggered');
    $(".data-card-header").each(function () {
        $(this).removeClass('is-editing');
    });
    if ($(this).val().length === 0) {
        $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1', function () {
            console.log('player load performed');
        });
    }
});

/* ---------- PLAYER SEARCH -------------- */
$("input#search-groups-input").keyup(function () {
    $("#data-groups-content").load('../inc/php/dep/pages/permissionssearch.php?load=groups&p=1&q=' + this.value, function () {
        console.log('load performed');
    });
    if ($(this).val().length === 0) {
        console.log($(this).val().length);
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
            console.log('load performed');
        });
    }
});

$("input#search-users-input").keyup(function () {
    $("#data-users-content").load('../inc/php/dep/pages/permissionssearch.php?load=users&p=1&q=' + this.value, function () {
        console.log('load performed');
    });
    if ($(this).val().length === 0) {
        console.log($(this).val().length);
        $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1', function () {
            console.log('load performed');
        });
    }
});

/* ---------------- PAGES ------------------ */
$(document).on('click', 'a', function () {
    if (this.hasAttribute('nmgroups')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=' + attr, function () {
                console.log('group load performed');
            });
        }
    } else if (this.hasAttribute('nmusers')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=' + attr, function () {
                console.log('users load performed');
            });
        }
    } else if (this.hasAttribute('nmuserpermissionspage')) {
        var attr = $(this).attr('page');
        var uuid = $(this).attr('useruuid');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=userpermissions&p=' + attr + '&uuid=' + uuid, function () {
                console.log('users load performed');
            });
        }
    } else if (this.hasAttribute('nmgrouppermissions')) {
        if (this.hasAttribute('groupid')) {
            var attr = $(this).attr('page');
            var id = $(this).attr('groupid');
            if (typeof attr !== typeof undefined && attr !== false) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=' + attr, 'groupid=' + id, function () {
                    console.log('grouppermissions load performed page');
                });
            }
        }
    }
});

/* -------------- Redirect PAGES ---------------- */
$(document).on('click', 'a', function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (this.hasAttribute('nmgroupid1')) {
        var attr = $(this).attr('nmgroupid1');
        console.log(attr);
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=1&groupid=' + attr, function (data) {
            console.log('grouppermissions load performed');
        });
    } else if (this.hasAttribute('nmgroupmembers')) {
        var attr = $(this).attr('nmgroupmembers');
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupmembers&p=1&groupid=' + attr, function (data) {
            console.log('groupmembers load performed');
        });
    } else if (this.hasAttribute('nmgroupprefixes')) {
        var attr = $(this).attr('nmgroupprefixes');
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupprefixes&p=1&groupid=' + attr, function (data) {
            console.log('groupprefixes load performed');
        });
    } else if (this.hasAttribute('nmgroupsuffixes')) {
        var attr = $(this).attr('nmgroupsuffixes');
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupsuffixes&p=1&groupid=' + attr, function (data) {
            console.log('groupsuffixes load performed');
        });
    } else if (this.hasAttribute('nmgroupparents')) {
        var attr = $(this).attr('nmgroupparents');
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupparents&p=1&groupid=' + attr, function (data) {
            console.log('groupparents load performed');
        });
    } else if (this.hasAttribute('userpermissions')) {
        var attr = $(this).attr('userpermissions');
        $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=userpermissions&p=1&uuid=' + attr, function (data) {
            console.log('userpermission load performed');
        });
    } else if (this.hasAttribute('nmbackgroups')) {
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
            console.log('groups loaded');
        });
    } else if (this.hasAttribute('nmbackgroupperms')) {
        var attr = $(this).attr('nmbackgroupperms');
        $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=1&groupid=' + attr, function () {
            console.log('grouppermissions loaded');
        });
    } else if (this.hasAttribute('nmbackusers')) {
        $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1', function () {
            console.log('users loaded');
        });
    }
});

$(document.body).on('click', '.switch-input', function () {
    if (this.hasAttribute('nmgroupid')) {
        console.log(this.id + " has been " + this.checked);
        if (this.checked) {
            $.get("../inc/php/dep/pages/permissions.php?load=setdefault&group=" + this.id + "&value=1", function (data) {
                console.log(data)
            });
        } else {
            $.get("../inc/php/dep/pages/permissions.php?load=setdefault&group=" + this.id + "&value=0", function (data) {
            });
        }
    }
});

$(document.body).on('blur', 'input', function () {
    if (this.hasAttribute('nmuserpermission') && this.hasAttribute('uuid')) {
        var attr = this.getAttribute('nmuserpermission');
        var uuid = this.getAttribute('uuid');
        var server = this.getAttribute('server');
        console.log(attr);
        var value = this.value.replace(new RegExp(' ', 'g'), ';v1');
        $.get("../inc/php/dep/pages/permissions.php?load=update_userperm&id=" + attr + "&permission=" + value, function (data) {
            console.log(data);
            $("#data-user-content").load('../inc/php/dep/pages/permissions.php?load=userpermissions&p=1&uuid=' + uuid, function () {
                console.log('userpermissions loaded');
            });
        });
    }
});

$(document.body).on('click', 'button', function () {
    if (this.hasAttribute('nmedit-group')) {
        var id = this.getAttribute('nmedit-group');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_group&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-group') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-group')) {
        var id = this.getAttribute('nmdelete-group');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this permission group!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
                    console.log('done delete group');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-group-permission')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmedit-group-permission');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_edit_group_permission&id=" + id + '&groupid=' + gid, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-group-permission') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-group-permission')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmdelete-group-permission');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this permission!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group_permission&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=1&groupid=' + gid, function () {
                    console.log('done delete permission');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-group-prefix')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmedit-group-prefix');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_edit_group_prefix&id=" + id + '&groupid=' + gid, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-group-prefix') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-group-prefix')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmdelete-group-prefix');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this group prefix!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group_prefix&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupprefixes&p=1&groupid=' + gid, function () {
                    console.log('done delete group prefix');
                });
            });
        });
    } else if (this.hasAttribute('nmedit-group-suffix')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmedit-group-suffix');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_edit_group_suffix&id=" + id + '&groupid=' + gid, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-group-suffix') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmdelete-group-suffix')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmdelete-group-suffix');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this group suffix!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group_suffix&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupsuffixes&p=1&groupid=' + gid, function () {
                    console.log('done delete group suffix');
                });
            });
        });
    } else if (this.hasAttribute('nmdelete-group-member')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmdelete-group-member');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this group member!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group_member&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupmembers&p=1&groupid=' + gid, function () {
                    console.log('done delete group member');
                });
            });
        });
    } else if (this.hasAttribute('nmdelete-groupparent')) {
        var gid = this.getAttribute('groupid');
        var id = this.getAttribute('nmdelete-groupparent');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this group parent!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_group_parent&id=" + id, function (data) {
                $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupparents&p=1&groupid=' + gid, function () {
                    console.log('done delete group parent');
                });
            });
        });
    } else if (this.hasAttribute('nmdelete-permplayer')) {
        var uuid = this.getAttribute('nmdelete-permplayer');
        $.get("../inc/php/dep/pages/permissions.php?load=delete_player&uuid=" + uuid, function (data) {
            $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1', function () {
                console.log('done delete nm permission player');
            });
        });
    } else if (this.hasAttribute('nmdelete-player-permission')) {
        var uuid = this.getAttribute('nmuseruuid');
        var id = this.getAttribute('nmdelete-player-permission');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this permission!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/permissions.php?load=delete_player_permission&id=" + id, function (data) {
                $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=userpermissions&p=1&uuid=' + uuid, function () {
                    console.log('done delete player permission');
                });
            });
        });
    } else if (this.hasAttribute('add-group-permission')) {
        var id = this.getAttribute('add-group-permission');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_group_permission&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-group-permission') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('add-group-member')) {
        var id = this.getAttribute('add-group-member');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_group_member&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-group-member') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('add-group-parent')) {
        var id = this.getAttribute('add-group-parent');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_group_parent&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-group-parent') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('add-group-prefix')) {
        var id = this.getAttribute('add-group-prefix');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_group_prefix&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-group-prefix') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('add-group-suffix')) {
        var id = this.getAttribute('add-group-suffix');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_group_suffix&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-group-suffix') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('add-user-permission')) {
        var id = this.getAttribute('add-user-permission');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_add_user_permission&uuid=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'add-user-permission') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('nmedit-permplayer')) {
        var id = this.getAttribute('nmedit-permplayer');
        $("#cached-modal").load("../inc/php/dep/pages/permissions.php?load=load_edit_permplayer&uuid=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for (var i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-permplayer') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if (this.hasAttribute('modal')) {
        var x = document.getElementsByClassName("modal");
        for (var i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if (this.hasAttribute('close')) {
        var x = document.getElementsByClassName("modal");
        for (var i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'create-group-form') {
        var data = $(this).serialize();
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=create_group&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
                console.log('done create group');
            });
        });
    } else if (this.id === 'nmedit-group-form') {
        data = $(this).serialize();
        var gid = this.getAttribute('group');
        console.log(data + ' ' + gid);
        $.get("../inc/php/dep/pages/permissions.php?load=edit_group&groupid=" + gid + "&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groups&p=1', function () {
                console.log('done edit group ' + data);
            });
        });
    } else if (this.id === 'add-group-permission-form') {
        console.log(data);
        data = $(this).serialize();
        gid = this.getAttribute('group');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_groupperm&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=1&groupid=' + gid, function () {
            });
        });
    } else if (this.id === 'nmedit-group-permission-form') {
        data = $(this).serialize();
        var id = this.getAttribute('permid');
        var gid = this.getAttribute('group');
        console.log(data + ' ' + gid);
        $.get("../inc/php/dep/pages/permissions.php?load=edit_group_permission&id=" + id + "&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=grouppermissions&p=1&groupid=' + gid, function () {
                console.log('done edit group ' + data);
            });
        });
    } else if (this.id === 'add-group-parent-form') {
        var data = $(this).serialize();
        var gid = this.getAttribute('group');
        console.log(gid);
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_groupparent&groupid=" + gid + "&" + data, function (data) {
            console.log(data);
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupparents&p=1&groupid=' + gid, function () {
            });
        });
    } else if (this.id === 'add-group-prefix-form') {
        var data = $(this).serialize();
        var gid = this.getAttribute('group');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_groupprefix&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupprefixes&p=1&groupid=' + gid, function () {
            });
        });
    } else if (this.id === 'nmedit-group-prefix-form') {
        data = $(this).serialize();
        var id = this.getAttribute('prefixid');
        var gid = this.getAttribute('group');
        console.log(data + ' ' + gid);
        $.get("../inc/php/dep/pages/permissions.php?load=edit_group_prefix&id=" + id + "&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupprefixes&p=1&groupid=' + gid, function () {
                console.log('done edit group prefix ' + data);
            });
        });
    } else if (this.id === 'add-group-suffix-form') {
        var data = $(this).serialize();
        var gid = this.getAttribute('group');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_groupsuffix&" + data, function (data) {
            console.log(data);
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupsuffixes&p=1&groupid=' + gid, function () {
            });
        });
    } else if (this.id === 'nmedit-group-suffix-form') {
        data = $(this).serialize();
        var id = this.getAttribute('suffixid');
        var gid = this.getAttribute('group');
        console.log(data + ' ' + gid);
        $.get("../inc/php/dep/pages/permissions.php?load=edit_group_suffix&id=" + id + "&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupsuffixes&p=1&groupid=' + gid, function () {
                console.log('done edit group prefix ' + data);
            });
        });
    } else if (this.id === 'add-group-member-form') {
        var data = $(this).serialize();
        var gid = this.getAttribute('group');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_groupmember&" + data, function (data) {
            $("#data-groups-content").load('../inc/php/dep/pages/permissions.php?load=groupmembers&p=1&groupid=' + gid, function () {
            });
        });
    } else if (this.id === 'add-user-permission-form') {
        var data = $(this).serialize();
        var uuid = this.getAttribute('useruuid');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=add_userperm&" + data, function (data) {
            console.log(data);
            $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=userpermissions&p=1&uuid=' + uuid, function () {
            });
        });
    } else if (this.id === 'nmedit-permplayer-form') {
        var data = $(this).serialize();
        var uuid = this.getAttribute('useruuid');
        console.log(data);
        $.get("../inc/php/dep/pages/permissions.php?load=edit_user&useruuid=" + uuid + "&" + data, function (data) {
            console.log(data);
            $("#data-users-content").load('../inc/php/dep/pages/permissions.php?load=users&p=1&uuid=' + uuid, function () {
            });
        });
    }
});

var isPermissionsLoaded = true;