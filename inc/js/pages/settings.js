/* --------- CHECKBOX ---------------- */

$(document.body).on('click', '.switch-input', function () {
    console.log(this.id + " has been " + this.checked);
    if (this.checked) {
        $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + this.id + "&value=1", function (data) {
            console.log('saved');
        });
    } else {
        $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + this.id + "&value=0", function (data) {
            console.log('saved');
        });
    }
});

$(document.body).on('keyup', 'input', function () {
    if (this.hasAttribute('variable')) {
        var attr = this.getAttribute('variable');
        console.log(attr);
        var value = this.value
            .replace(new RegExp(' ', 'g'), ';v1')
            .replace(new RegExp('&', 'g'), ';v2')
            .replace(new RegExp('§', 'g'), ';v3')
            .replace(new RegExp('#', 'g'), ';v4');
        $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + attr + "&value=" + value, function (data) {
            console.log(data);
            if(data.toString().indexOf('Error:') >= 0) {
                swal({
                    title: "Error",
                    text: data.toString().replace('Error: ', ''),
                    type: "warning",
                });
            } else {
                if (data.toString().indexOf('Could not write to the settings file!') >= 0) {
                    swal({
                        title: "Error",
                        text: "Could not write to the settings file!",
                        type: "warning",
                    });
                }
                if (data.toString().indexOf('Caught exception: Could not open ') >= 0) {
                    swal({
                        title: "Error",
                        text: "Could not write to the language folder",
                        type: "warning",
                    });
                }
				if (data.toString().indexOf('Status Code:') >= 0) {
					swal({
						title: "Error",
                        text: "Could not download requested language file!\n" + response.toString().replace('Status Code: '),
						type: "warning",
					});
				}
            }
        });
    }
});

$(document.body).on('change', 'select', function (e) {
    var optionSelected = $(this).find("option:selected");
    if (optionSelected.attr('variable')) {
        var value = optionSelected.text();
        var variable = optionSelected.attr('variable');
        var success = true;
        $.get("../inc/php/dep/pages/settingsupdate.php?variable=" + variable + "&value=" + value, function (data) {
            console.log(data);
            if (data.toString().indexOf('Could not write to the settings file!') >= 0) {
                success = false;
                swal({
                    title: "Error",
                    text: "Could not write to the settings file!",
                    type: "warning",
                });
            }
            if (data.toString().indexOf('Caught exception: Could not open ') >= 0) {
                success = false;
                swal({
                    title: "Error",
                    text: "Could not write to the language folder",
                    type: "warning",
                });
            }
            if (data.toString().indexOf('Downloaded!') >= 0) {
                swal({
                    title: "Success",
                    text: "Successfully changed language!",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, reload!",
                    closeOnConfirm: true
                }, function () {
                    console.log('reloading');
                    location.reload();
                });
            }
        });
    }
});

var isSettingsLoaded = true;