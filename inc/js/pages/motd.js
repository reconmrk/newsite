$(document.body).on('keyup', 'input' ,function(){
    if(this.id === 'icon') {
        var url = this.value;
        if(url.endsWith('.png') ||url.endsWith('.jpg')) {
            console.log('proceed please');
            $('#preview_zone').css("background-image", "url(" + url + "), url(../inc/img/motd.png)");
        }
    } else if(this.id === 'message') {
        var yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
        var newMOTD = yourMOTD.replaceColorCodes();
        $('#motd').text('').append(newMOTD).html();
    } else if (this.id === 'maintenance_message') {
        yourMOTD = this.value.replace(new RegExp('%newline%', 'g'), '\n');
        newMOTD = yourMOTD.replaceColorCodes();
        $('#motd_maintenance').text('').append(newMOTD).html();
    }
});

$(document.body).on('keyup', 'input' ,function(){
    if(this.hasAttribute('variable')) {
        var attr = this.getAttribute('variable');
        var value = this.value;
        value = value.replace(new RegExp(' ', 'g'), ';v1');
        value = value.replace(new RegExp('&', 'g'), ';v2');
        value = value.replace(new RegExp('§', 'g'), ';v3');
        $.get( "../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function( data ) {
            console.log(data)
        });
    }
});

var isMotdLoaded = true;