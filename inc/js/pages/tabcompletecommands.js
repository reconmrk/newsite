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
});


$(document.body).on('click', '.nm-button' ,function(){
    if(this.hasAttribute('tabcompletecommand')) {
        var id = this.getAttribute('tabcompletecommand');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this tabcomplete command!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get( "../inc/php/dep/pages/tabcompletecommandsmanagement.php?type=delete_command&id=" + id, function( data ) {
                console.log('deleted');
                $( "#data-card-content" ).load('../inc/php/dep/pages/tabcompletecommands.php', function () {
                    console.log('loaded');
                });
            });
        });
    }
});

$( "form" ).submit(function( event ) {
    if(this.id = 'tabcompletecommands') {
        event.preventDefault();
        var string = document.getElementById('search-input').value;
        $.get( "../inc/php/dep/pages/tabcompletecommandsmanagement.php?type=add_command&string=" + string, function( data ) {
            console.log('added ' + string);
            $( "#data-card-content" ).load('../inc/php/dep/pages/tabcompletecommands.php', function () {
                console.log('loaded');
            });
        });
        document.getElementById('search-input').value = '';
    }
});

var isTabCompleteCommandsLoaded = true;