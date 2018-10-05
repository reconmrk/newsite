$( "#helpop-update" ).click(function() {
    $( "#data-card-content" ).load('../inc/php/dep/pages/helpop.php?p=1', function () {
        console.log('load performed');
    });
});

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
    if($(this).val().length === 0) {
        $( "#data-card-content" ).load('../inc/php/dep/pages/helpop.php?p=1', function () {
            console.log('load performed');
        });
    }
});
$(document).on('click','a',function(){
    if(this.hasAttribute('helpop')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/helpop.php?p=' + attr, function () {
                console.log('load performed');
            });
        }
    } else if(this.hasAttribute('helpopsearch')) {
        var attr = $(this).attr('page');
        var query = $(this).attr('query');
        if (typeof attr !== typeof undefined && attr !== false) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/helpopsearch.php?p=' + attr + '&q=' + query, function () {
                console.log('helpopsearch load performed');
            });
        }
    }
});

$(document.body).on('click', 'button' ,function() {
    if (this.hasAttribute('nmdelete-helpop')) {
        var id = this.getAttribute('nmdelete-helpop');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this helpop request!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/helpop.php?load=delete_helpop&id=" + id, function (data) {
                $("#data-card-content").load('../inc/php/dep/pages/helpop.php?p=1', function () {
                    console.log('done delete helpop request');
                });
            });
        });
    }
});

/* ---------- PLAYER SEARCH -------------- */
$("input#search-input").keyup(function() {
    $( "#data-card-content" ).load('../inc/php/dep/pages/helpopsearch.php?p=1&q=' + this.value, function () {
        console.log('helpopsearch load performed');
    });
    if($(this).val().length === 0) {
        console.log($(this).val().length);
        $( "#data-card-content" ).load('../inc/php/dep/pages/helpop.php?p=1', function () {
            console.log('load performed');
        });
    }
});

var isHelpOPLoaded = true;