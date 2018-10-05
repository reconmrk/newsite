$(document.body).on('change', 'select', function () {
    console.log(this.id);
    if (this.id === 'assign-ticket') {
        var id = this.getAttribute('assign');
        var user = this.value;
        console.log(user);
        $.get("../inc/php/dep/pages/ticket.php?type=assign_ticket&id=" + id + "&account=" + user, function (data) {
            console.log(data);
        });
    } else if (this.id === 'priority-ticket') {
        var id = this.getAttribute('priority');
        var priority = this.value;
        console.log(priority);
        $.get("../inc/php/dep/pages/ticket.php?type=priority_ticket&id=" + id + "&priority=" + priority, function (data) {
            console.log(data);
        });
    }
});

$(document.body).on('click', 'button', function () {
    if (this.id === 'ticket-respond') {
        var id = this.getAttribute('ticketid');
        var message = tinyMCE.activeEditor.getContent();
        if (message === '') {
            swal('You need to enter a valid message to reply!');
            return;
        }
        $.get("../inc/php/dep/pages/ticket.php?type=respond_ticket&id=" + id + "&message=" + message, function (data) {

            $("#cached-message").append('<div id="ticket-' + id + '" class="col-12"><div class="data-card"> <div class="data-card-header"> <div class="data-card-header-title" id="ticket-title">You<small> a moment ago</small></div></div> <div class="data-card-body"> <div class="ticket-container">' + message + '</div> </div> </div> </div>');
        });
    } else if (this.id === 'ticket-close') {
        let id = this.getAttribute('ticketid');
        swal({
            title: "Are you sure?",
            //text: "You will not be able to recover this server!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, close it!",
            closeOnConfirm: true
        }, function () {
            $.get("../inc/php/dep/pages/ticket.php?type=delete_ticket&id=" + id, function (data) {
                document.getElementById('ticket-close').remove();
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
        if (this.getAttribute('close') === 'nmedit-ticket-message') {
            $("#data-card-content").load('../inc/php/dep/pages/ticket.php?id=' + ticketid, function (data) {
                console.log('loaded');
            });
        }
        let x = document.getElementsByClassName("modal");
        for (let i = 0; i < x.length; i++) {
            if (x[i].id === this.getAttribute('close')) {
                x[i].style.display = "none"
            }
        }
    }
});

$(document.body).on('click', 'a', function () {
    if (this.id === 'ticket-edit-message') {
        let id = this.getAttribute('ticketid');
        $("#mceu_12").remove();
        $("#cached-modal").load('../inc/php/dep/pages/ticket.php?type=load_edit_ticket_message&id=' + id, function () {
            let x = document.getElementsByClassName("modal");
            for (let i = 0; i < x.length; i++) {
                if (x[i].id === 'nmedit-ticket-message') {
                    x[i].style.display = "block"
                }
            }
        });
    }
});

$(document.body).on('submit', 'form', function (e) {
    e.preventDefault();
    if (this.id === 'nmedit-ticket-message-form') {
        let id = this.getAttribute('ticketid');
        let message = document.getElementById('new-ticket-message').value;
        if (message === '') {
            swal('You need to enter a valid message to reply!');
            return;
        }
        console.log(id);
        let elem = document.getElementById('ticket-' + id);
        $.get("../inc/php/dep/pages/ticket.php?type=edit_ticket_message&id=" + id + "&message=" + message, function (data) {
            console.log(data);
            if (data === 'true') {
                elem.innerHTML =
                    '<div class="data-card">' +
                    '<div class="data-card-header">' +
                    '<div class="data-card-header-title" id="ticket-title">' +
                    'You<small style="margin-left: 5px;"> a moment ago</small>' +
                    '<a id="ticket-edit-message" ticketid="' + id + '" class="edit-ticket-message-button material-icons">edit</a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="data-card-body">' +
                    '<div class="ticket-container">' + message + '</div>' +
                    '</div>' +
                    '</div>';
            }
        });
    }
});

function tinyMca_text(field_id) {

    if (jQuery("#" + field_id + ":hidden").length > 0) {
        return tinyMCE.get(field_id).getContent();
    }
    else {
        return jQuery('#' + field_id).val();
    }

}

var isTicketLoaded = true;