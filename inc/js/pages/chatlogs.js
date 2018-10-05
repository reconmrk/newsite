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
        $( "#data-card-content" ).load('../inc/php/dep/pages/chatlogs.php?p=1', function () {
            console.log('load performed');
        });
    }
});
$(document).on('click','a',function(){
    if(this.hasAttribute('chatlogs')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $( "#data-card-content" ).load('../inc/php/dep/pages/chatlogs.php?p=' + attr, function () {
                console.log('load performed');
            });
        }
    }
});

$(document.body).on('click', 'button' ,function() {
    if (this.hasAttribute('nmdelete-chatlog')) {
        var uuid = this.getAttribute('nmdelete-chatlog');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this chatlog!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/chatlogs.php?load=delete_chatlog&uuid=" + uuid, function (data) {
                $("#data-card-content").load('../inc/php/dep/pages/chatlogs.php?p=1', function () {
                    console.log('done delete chatlog');
                });
            });
        });
    }
});

/* ---------- PLAYER SEARCH -------------- */
$("input#search-input").keyup(function() {
    $( "#data-card-content" ).load('../inc/php/dep/pages/chatlogsearch.php?q=' + this.value, function () {
        console.log('load performed');
    });
    if($(this).val().length === 0) {
        console.log($(this).val().length);
        $( "#data-card-content" ).load('../inc/php/dep/pages/chatlogsearch.php?p=1', function () {
            console.log('load performed');
        });
    }
});

var isChatLogsLoaded = true;