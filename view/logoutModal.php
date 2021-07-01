<div class="modal fade center" id="logoutModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title text-center col-11 ml-2 text-muted font-weight-bold">Log out</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center">
                        <i  class="fas fa-sign-out-alt fa-7x text-danger"></i>
                        <p class="text-muted mt-2">Do you really want to sign out ? </p>
                    </div>
                    <div id="errorSignup"></div>
                    <div class="modal-footer">
                        <button type="button" id="logoutConfirm" class="btn btn-outline-secondary" data-dismiss="modal">Confirm</button>
                        <button type="button" id="logoutCancel" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>