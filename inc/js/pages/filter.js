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

$("input#search-input" ).keyup(function() {
    if($(this).val().length === 0) {
        console.log($(this).val().length);
        $( "#data-card-content" ).load('../inc/php/dep/pages/filter.php?load=load', function () {
            console.log('load performed');
        });
    }
});


$(document.body).on('blur', 'input', function () {
    if (this.hasAttribute('word')) {
        var attr = this.getAttribute('word');
        console.log(attr);
        var value = this.value;
        value = value.replace(new RegExp(' ', 'g'), ';v1');
        value = value.replace(new RegExp('&', 'g'), ';v2');
        value = value.replace(new RegExp('§', 'g'), ';v3');
        $.get( "../inc/php/dep/pages/filter.php?load=update&id=" + attr + "&word=" + value, function( data ) {
            console.log(data);
            $( "#data-card-content").load('../inc/php/dep/pages/filter.php?load=load', function () {
                console.log('loaded');
            });
        });
    } else if(this.hasAttribute('server')) {
        var attr = this.getAttribute('server');
        console.log(attr);
        var value = this.value;
        value = value.replace(new RegExp(' ', 'g'), ';v1');
        value = value.replace(new RegExp('&', 'g'), ';v2');
        value = value.replace(new RegExp('§', 'g'), ';v3');
        $.get( "../inc/php/dep/pages/filter.php?load=update&id=" + attr + "&server=" + value, function( data ) {
            console.log(data);
            $( "#data-card-content").load('../inc/php/dep/pages/filter.php?load=load', function () {
                console.log('loaded');
            });
        });
    }
});

$(document.body).on('click', '.nm-button' ,function(){
    if(this.hasAttribute('word')) {
        var id = this.getAttribute('word');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this filter word!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
        }, function () {
            $.get( "../inc/php/dep/pages/filter.php?load=remove&id=" + id, function( data ) {
                console.log('deleted ' + id);
                $( "#data-card-content" ).load('../inc/php/dep/pages/filter.php?load=load', function () {
                    console.log('loaded');
                });
            });
        });
    }
});

$( "form" ).submit(function( event ) {
    if(this.id = 'filter') {
        event.preventDefault();
        var string = document.getElementById('search-input').value;
        $.get( "../inc/php/dep/pages/filter.php?load=add&string=" + string, function( data ) {
            console.log('added ' + string);
            $( "#data-card-content" ).load('../inc/php/dep/pages/filter.php?load=load', function () {
                console.log('loaded');
            });
        });
        document.getElementById('search-input').value = '';
    }
});

var isFilterLoaded = true;