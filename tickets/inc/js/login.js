$("#login-form").submit(function (event) {
    event.preventDefault();
    let data = $(this).serialize();
    $.ajax({
        type: 'POST',
        url: 'inc/php/login.php',
        data: data,
        success: function (response) {
            if (response === 'true' || response === 'false') {
                if (response === 'true') {
                    window.location.href = "index.php";
                } else if (response === 'false') {
                    document.getElementById("login-submit").value = "WRONG PASSWORD";
                    document.getElementById("login-submit").style.cssText = 'background-color:red !important';
                    document.getElementById("username").value = "";
                    document.getElementById("password").value = "";
                    document.getElementById("username").placeholder = "Username";
                    document.getElementById("password").placeholder = "Password";

                    setTimeout(function () {
                        document.getElementById("login-submit").value = "LOG IN";
                        document.getElementById("login-submit").style.cssText = '';
                    }, 2500);
                }
            } else {
                console.log(response);
            }
        }
    });
});