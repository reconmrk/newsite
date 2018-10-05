$("#chat-update").click(function () {
    $("#data-card-chatcontent").load('../inc/php/dep/pages/chat.php?load=load&p=1', function () {
        console.log('reload performed');
    });
});
$("#chat-clear").click(function () {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover these chat messages!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: true
    }, function () {
        $.get("../inc/php/dep/pages/chat.php?load=clear_chat", function (data) {
            $("#data-card-chatcontent").load('../inc/php/dep/pages/chat.php?load=load&p=1', function () {
                console.log('reload performed');
            });
        });
    });
});

$(document).on('click', 'a', function () {
    if (this.hasAttribute('chat')) {
        var attr = $(this).attr('page');
        if (typeof attr !== typeof undefined && attr !== false) {
            $("#data-card-chatcontent").load('../inc/php/dep/pages/chat.php?load=load&p=' + attr, function () {
                console.log('load performed');
            });
        }
    }
});

var isChatLoaded = true;