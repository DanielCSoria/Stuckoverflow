var valdiator;

$(function () {
    $("#stats").append("<a class='nav-link mr-2' href='user/stats'>Stats</a>");

    $("#logInConfirm").on('click', function (e) {
        handlesLogIn();
    });

    $("#signinBtn").on('click', function (e) {
        launchLogModal(e);
    });


});

function validateForm() {
    validator = $('#loginForm').validate({
        rules: {
            pseudo: {
                remote: {
                    url: 'user/pseudo_available_service',
                    type: 'post',
                    data: {
                        pseudo: function () {
                            //reverting available service as if its available then it doesnt exists
                            return $("#pseudo").val() == "true" ? "false" : "true";
                        }
                    }
                },
                required: true,

            },
            password: {
                required: true,

            },
        },
        messages: {
            pseudo: {
                remote: "No user with this user name. Please sign up.",
                required: 'Pseudo is required',

            },
            password: {
                required: 'Password is required',

            },
        },

    });
    $("input:text:first").focus();
}

function launchLogModal(e) {
    if (validator)
        validator.resetForm();
    $("#errorsLogin").empty();
    e.preventDefault();
    $("#logModal").modal();
    validateForm();
}

function handlesLogIn() {
    if ($("#loginForm").valid()) {
        $.post("user/login_service/", { "pseudo": $("#pseudo").val(), "password": $("#inputPassword").val() }, function (data) {
            if (data == "true") {
                location.reload();
            }
            else {
                $("#errorsLogin").html('<p class="text-danger ml-5">Incorrect Password</p>');
            }
        });
    }
}




