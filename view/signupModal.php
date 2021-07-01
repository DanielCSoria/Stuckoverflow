<div class="modal px-2 fade" tabindex="-1" role="dialog" id="signupModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center col-11 ml-2 text-muted font-weight-bold">Sign up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="signupForm">
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="signupPseudo"  class="text-muted my-0 py-0">User name</label>
                        <input type="text" class="form-control mb-1" id="signupPseudo" name="signupPseudo">
                    </div>
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="signupName" class=" text-muted my-0 py-0">Full name</label>
                        <input type="text" class="form-control" id="signupName" name="signupName">
                    </div>
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="signupPassword" class=" text-muted my-0 py-0">Password</label>
                        <input type="password" class="form-control" id="signupPassword" name="signupPassword">
                    </div>
                    <div class="form-group my-0 py-0 mb-2">
                        <label for="signupPasswordConfirm" class=" text-muted my-0 py-0">Password Confirm</label>
                        <input type="password" class="form-control" id="signupPasswordConfirm" name="signupPasswordConfirm">
                    </div>
                    <div class="form-group my-0 py-0 mb-2">
                        <label class=" text-muted my-0 py-0">Email</label>
                        <input type="email" class="form-control" id="signupEmail" name="signupEmail">
                    </div>
                </form>
                </div>
                <div class="modal-footer d-flex">
                    <button type="button" class="btn btn-outline-primary" id="signupConfirm">Sign up</button>
                    <button type="button" class="btn btn-outline-danger"  data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
</div>