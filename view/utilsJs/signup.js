//used to eventually reset form..
var validator;
$(function () {

    $("#signupBtn").on('click',function(e){
        launchSignupModal(e);
     });

     $("#signupConfirm").on('click',function(e){
         handleSignup();
     }); 
});

function validateSignup() {
    validator = $('#signupForm').validate({
        errorClass: "error",
        rules: {
            signupPseudo: {
                remote: {
                    url: 'user/pseudo_available_service',
                    type: 'post',
                    data: {
                        pseudo: function () {
                            return $("#signupPseudo").val();
                        }
                    }
                },
                required: true,
                minlength: 3,
                maxlength: 20,
                regex: /^[a-zA-Z][a-zA-Z0-9]*$/
            },
            signupPassword: {
                required: true,
                minlength: 8,
                maxlength: 16,
                regex: [/[A-Z]/, /\d/, /['";!:,.\/?\\-]/]

            },
            signupPasswordConfirm: {
                required: true,
                equalTo: "#signupPassword"
            },
            signupName: {
                required: true,
                minlength: 3,
                maxlength: 20
            },
            signupEmail: {
                remote: {
                    url: 'user/mail_available_service',
                    type: 'post',
                    data: {
                        pseudo: function () {
                            return $("#signupEmail").val();
                        }
                    }
                },
                required: true,
                email: true
            }

        },
        messages: {
            signupPseudo: {
                remote: 'This pseudo is already taken',
                required: 'Pseudo is required',
                regex: 'User name must start by a letter and must contain only letters and numbers.'

            },
            signupPassword: {
                required: 'Password is required',
                regex: 'Wrong password format'
            },
            signupPasswordConfirm: {
                required: 'You must confirm your password',
                equalTo: 'Must be identical to password above',
            },
            signupName: {
                required: 'Full name required',
            },
            signupEmail: {
                required: 'Email is required',
                email: 'Invalid mail address',
                remote : 'Email already taken'
            }
        },

    });
    $("input:text:first").focus();
}


function launchSignupModal(e) {
    if (validator)
        validator.resetForm();
    e.preventDefault();
    $("#signupModal").modal();
    validateSignup();
}

function handleSignup() {
    if ($("#signupForm").valid()) {
        let pseudo = $("#signupPseudo").val();
        let password = $("#signupPassword").val();
        let name = $("#signupName").val();
        let mail = $("#signupEmail").val();
        let pwdConfirm = $("#signupPasswordConfirm").val();
        $.post("user/signup_service/", { "signupPseudo": pseudo, "signupPassword": password,"signupName": name, "signupEmail": mail, "signupPasswordConfirm": pwdConfirm }, function (data) {
            if (data == "true") {
              location.reload();
            }
            else{
             $("#errorSignup").html('<p class="text-danger ml-5">Unexcpected error encountered while trying to sign up.</p>');
            }
        });
    }
}