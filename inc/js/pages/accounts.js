$(document.body).on('change', 'select' ,function(){
    if(this.id === 'account-group') {
        var id = this.getAttribute('account');
        var group = this.value;
        console.log(group);
        $.get( "../inc/php/dep/pages/accountmanagement.php?type=edit_account&id=" + id + "&group=" + group, function( data ) {
            console.log(data);
            $( "#data-account-content" ).load('../inc/php/dep/pages/accounts.php', function () {
                console.log('loaded');
            });
        });
    }
});

$(document.body).on('click', 'button' ,function(){
    if(this.hasAttribute('delete-account')) {
        var id = this.getAttribute('delete-account');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this account!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/accountmanagement.php?type=delete_account&id=" + id, function (data) {
                $("#data-account-content").load('../inc/php/dep/pages/accounts.php', function () {
                    console.log('deleted account with id' + id);
                });
            });
        });
    } else if(this.hasAttribute('delete-group')) {
        var id = this.getAttribute('delete-group');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this group!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/accountmanagement.php?type=delete_group&id=" + id, function (data) {
                $("#data-group-content").load('../inc/php/dep/pages/groups.php', function () {
                    console.log('loaded');
                });
                $("#data-account-content").load('../inc/php/dep/pages/accounts.php', function () {
                    console.log('loaded');
                });
            });
        });
    } else if(this.hasAttribute('edit-group')) {
        var id = this.getAttribute('edit-group');
        $( "#cached-modal" ).load( "../inc/php/dep/pages/accountmanagement.php?type=load_group&id=" + id, function () {
            var x = document.getElementsByClassName("modal");
            for(var i = 0; i < x.length; i++) {
                if(x[i].id === 'edit-group') {
                    x[i].style.display = "block"
                }
            }
        });
    } else if(this.hasAttribute('modal')) {

        var x = document.getElementsByClassName("modal");
        for(var i = 0; i < x.length; i++) {
            if(x[i].id === this.getAttribute('modal')) {
                x[i].style.display = "block"
            }
        }
    } else if(this.hasAttribute('close')) {
        var x = document.getElementsByClassName("modal");
        for(var i = 0; i < x.length; i++) {
            if(x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('submit', 'form' ,function(e){
    e.preventDefault();
    if(this.id === 'create-account-form') {
        var data = $(this).serialize();
        console.log(data);
        $.get( "../inc/php/dep/pages/accountmanagement.php?type=create_account&" + data, function( data ) {
            $( "#data-account-content" ).load('../inc/php/dep/pages/accounts.php', function (){});
        });
    } else if(this.id === 'create-group-form') {
        var data = $(this).serialize();
        console.log(data);
        $.get( "../inc/php/dep/pages/accountmanagement.php?type=create_group&" + data, function( data ) {
            $( "#data-group-content" ).load('../inc/php/dep/pages/groups.php', function () {
                console.log('loaded');
            });
            $( "#data-account-content" ).load('../inc/php/dep/pages/accounts.php', function () {
                console.log('loaded');
            });
        });
    } else if(this.id === 'edit-group-form') {
        var data = $(this).serialize();
        console.log(data);
        $.get( "../inc/php/dep/pages/accountmanagement.php?type=edit_group&id="  + this.getAttribute('group') + "&" + data, function( data ) {
            console.log(data);
        });
    }
});

var isAccountsLoaded = true;