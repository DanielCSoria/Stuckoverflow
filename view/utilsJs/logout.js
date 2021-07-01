$(function () {

    $("#signoutBtn").on('click', function (e) {
        launchLogOutModal(e);
    });

    $("#logoutConfirm").on('click', function (e) {
        handlesLogOut();
    });


});

function launchLogOutModal(e) {
    e.preventDefault();
    $("#logoutModal").modal();

}

function handlesLogOut() {
    $.post("user/log_out_service/", {}, function (data) {
        if (data == "true") {
            location.reload();
        }
    });
}


