$( "#tickets-update" ).click(function() {
    $( "#data-card-tickets-content" ).load('../inc/php/dep/pages/tickets.php?p=1', function () {
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
        $( "#data-card-tickets-content" ).load('../inc/php/dep/pages/tickets.php?p=1', function () {
            console.log('load performed');
        });
    }
});
$(document).on('click','a',function(){
    if(this.hasAttribute('tickets')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $( "#data-card-tickets-content" ).load('../inc/php/dep/pages/tickets.php?p=' + attr, function () {
                console.log('load performed');
            });
        }
    }
});

/* ---------- PLAYER SEARCH -------------- */
$("input#search-input").keyup(function() {
    $( "#data-card-tickets-content" ).load('../inc/php/dep/pages/ticketsearch.php?q=' + this.value, function () {
        console.log('load performed');
    });
    if($(this).val().length === 0) {
        console.log($(this).val().length);
        $( "#data-card-tickets-content" ).load('../inc/php/dep/pages/tickets.php?p=1', function () {
            console.log('load performed');
        });
    }
});

var isTicketsLoaded = true;