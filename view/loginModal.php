
    <div class="modal px-2 fade" tabindex="-1" role="dialog" id="logModal">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center col-11 ml-2 text-muted font-weight-bold">Sign in</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="loginForm">
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="pseudo"  class="text-muted my-0 py-0">User name</label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo">
                    </div>
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="inputPassword" class=" text-muted my-0 py-0">Password</label>
                        <input type="password" class="form-control" id="inputPassword" name="password">
                    </div>
                </form>
                </div>
                <div id="errorsLogin"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="logInConfirm">Log in</button>
                    <button type="button" class="btn btn-outline-danger" id="logInCancel" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
