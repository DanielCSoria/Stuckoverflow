<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $post->get_title() ?></title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.css" rel="stylesheet" />
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="css/bootstrap/js/bootstrap.bundle.js"></script>
    <link href="css/stylebis.css" rel="stylesheet" />
    <script src="view/utilsJs/loadUtils.js"></script>
    <link rel="stylesheet" href="lib/easymde/dist/easymde.min.css">
    <script src="lib/easymde/dist/easymde.min.js"></script>





    <script>
        //the hardest stuff on this page is to handle both edit and add form. Indeed, we dont want 2 forms to be open at the same time, so we need to check wether another form
        //is open or not, and to know if this one is different than the one which was requested, cause if its different we need to close it and open the new one but if its the same
        //then we just want to close it (cause its the second time we click)
        $(function() {
            var easyMDE = new EasyMDE({
                autoDownloadFontAwesome: false,
                element: document.getElementById('mde'),
            });
            var createCommentForm;
            var currentPost;
            var currentComment;
            var editCommentForm;
            $(".commentBtn").on('click', function(e) {
                e.preventDefault();
                manageFormToggleAdd($(this));
                currentPost = $(this);
                createCommentForm = $(this).next();
                if (!createCommentForm.is(":visible")) {
                    let form = "<form class='d-flex align-items-center commentForm'><input type='text' class='form-control commentBody'><button class='btn btn-outline-primary my-2 mx-2 confirmBtn'>Confirm</button><button class='btn btn-outline-danger my-2 cancelBtn'>Cancel</button></form>";
                    createCommentForm.html(form);
                }
                createCommentForm.toggle(200);
            });

            $('#answerForm').validate({
                rules: {
                    body: {
                        required: true,
                    }
                }
            });
            
            //same than for comment button, we need to check few things to handle form close/open efficiently, we're also retrieving comment content to put it into the input
            $(document).on('click', '.editBtn', function(e) {
                e.preventDefault();
                manageFormToggle($(this).data("value"));
                editCommentForm = $(this).parent().parent().find(".dnone");
                currentComment = $(this).data("value");
                let form = "<form class='d-flex align-items-start ml-3'><input type='text' class='form-control editBody'/><button class='btn btn-outline-primary mx-2 confirmEdit'>Confirm</button><button class='btn btn-outline-danger cancelEdit'>Cancel</button></form>";
                editCommentForm.html(form);
                let str = retrieveStr($(this).parent().text());
                editCommentForm.find('.editBody').val(str);
                currentPost = $(this).parent().parent().parent().next('.commentBtn');
                editCommentForm.toggle(250);


            });

            //user votes, if true is answered by the server then we ask for new post vote status (up:true,down:false,count:1) and refresh the votepan after getting theses infos
            //ps : remember we dont have to manage answer sort after vote..(ps : spinner added for fun :p)
            $(".votepan").on('submit', function(e) {
                e.preventDefault();
                var upBtn = $(this).find('.upvote_button');
                var downBtn = $(this).find('.downvote_button');
                var count = $(this).find('.count');
                let tempCount = count.text();
                count.html('<div class="spinner-grow text-primary  py-1 my-1 " role="status"><span class="sr-only">Loading...</span></div>');
                var id = $(this).find('input[name=id]').val();
                $.post("user/vote_service/", {
                    "post_id": id,
                    "action": $(document.activeElement).val()
                }, function(data) {
                    if (data == "true") {
                        $.post("user/vote_status_service/", {
                            "post_id": id
                        }, function(data) {
                            setTimeout(function() {
                                updateVoteStatus(data, upBtn, downBtn, count);
                            }, 1000);
                        });
                    } else {
                        count.html(tempCount);
                        openErroModal("Log in to vote on a post...", "Only members can vote", "user/login");
                    }
                }).fail(function() {
                    openErroModal("Technichal issue.");
                });
            });


            //all these functions are called via document as we talked about, cause i didnt want to rebuild everything the other way(+ i dont think it really matters)
            $(document).on('click', '.confirmEdit', function(e) {
                e.preventDefault();
                editComment(editCommentForm.find(".editBody").val());
            });

            $(document).on('click', '.cancelEdit', function(e) {
                e.preventDefault();
                editCommentForm.toggle(200);
            });

            $(document).on('keydown', '.commentBody', function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    postComment($(this).val());
                    createCommentForm.toggle(200);
                }
            });

            $(document).on('click', '.cancelBtn', function(e) {
                e.preventDefault();
                createCommentForm.toggle(150);
            });

            $(document).on('click', '.confirmBtn', function(e) {
                e.preventDefault();
                createCommentForm.toggle(200);
                postComment(createCommentForm.find(".commentBody").val());
            });

            $(document).on('click', '.deleteBtn', function(e) {
                e.preventDefault();
                currentComment = $(this).data("value");
                currentPost = $(this).parent().parent().parent().next('.commentBtn');
                openDeleteModal();
            });



            $("#deleteConfirm").on("click", function(e) {
                $.post("comment/delete_service/", {
                    "comment_id": currentComment
                }, function(data) {
                    if (data == "true")
                        formatComments();
                    else
                        openErroModal("Cannot delete this comment...", "Access rights error");
                }).fail(function() {
                    openErroModal("Technichal issue.");
                });
            });


            //check if another edit form/add comment form is active, if so hide it before opening the last asked(if the last asked isnt the one who was open..).
            function manageFormToggle(val) {
                if (currentComment && currentComment != val && editCommentForm && editCommentForm.is(":visible"))
                    editCommentForm.toggle(200);
                if (createCommentForm && createCommentForm.is(":visible"))
                    createCommentForm.toggle(200);
            }

            //same than the one before but for add form, could have do something more abstract but well, only two are required here so its a lot of work for nothing
            //but would have definitly done it if more than 2 were required
            function manageFormToggleAdd(val) {
                if (typeof currentPost !== "undefined" && val.attr('id') != currentPost.attr('id') && createCommentForm && createCommentForm.is(":visible"))
                    createCommentForm.toggle(200);
                if (editCommentForm && editCommentForm.is(":visible"))
                    editCommentForm.toggle(200);
            }

            function retrieveStr(text) {
                //in html we had only one <p> for the comment AND its label (Commented x time ago) so, we're doing a bit of string magic here to retrieve comment content
                let str = text.substr(0, text.indexOf('- C') - 1);
                return str;
            }

            //easily working on server's answer and translating it to html
            function updateVoteStatus(data, up, down, count) {
                let res = JSON.parse(data);
                if (res[0].up)
                    up.html('<i  class="fas fa-thumbs-up fa-lg green_color"></i>');
                else
                    up.html('<i class="far fa-thumbs-up fa-lg"></i>');
                if (res[0].down)
                    down.html('<i class="fas fa-thumbs-down fa-lg red_color"></i>');
                else
                    down.html('<i class="far fa-thumbs-down fa-lg"></i>');
                count.html(res[0].count);

            }

            function editComment(body) {
                $.post("comment/edit_service/", {
                    "comment_id": currentComment,
                    "body": body
                }, function(data) {
                    if (data == "true")
                        formatComments();
                    else
                        openErroModal("Cannot edit comment with empty body", "Incorrect comment");
                }).fail(function() {
                    openErroModal("Technichal issue.");
                });
            }

            function postComment(body) {
                $.post("comment/create_service/", {
                    "post_id": currentPost.attr('id'),
                    "body": body
                }, function(data) {
                    if (data == "true")
                        formatComments();
                    else
                        openErroModal("You must enter a body for your comment.", "Incorrect comment");
                }).fail(function() {
                    openErroModal("Technichal issue.");
                });
            }

            function formatComments() {
                $.get("post/get_comments_as_json/" + currentPost.attr('id'),
                    function(data) {
                        comments = JSON.parse(data);
                        let commentDiv = currentPost.parent().find(".comment-div");
                        html = "";
                        for (var i in comments) {
                            html += '<div class="col-md-10 px-0 mx-0 spec">';
                            html += '<p class="commentaire px-0 mx-0 align-baseline">' + comments[i].body + '  - ' + comments[i].time;
                            if (comments[i].editable) {
                                html += ' <a  href="comment/edit/' + comments[i].id + '" data-value="' + comments[i].id + '" class=" dd px-2 editBtn">edit</a>';
                                html += ' <a href="comment/delete/' + comments[i].id + '" data-value="' + comments[i].id + '" class="dd deleteBtn">delete</a>';
                            }
                            html += "</p><div class='dnone'></div></div>";
                        }
                        commentDiv.html(html);
                    });
            }

            function openErroModal(msg) {
                openErroModal(msg, "Error while posting msg..");
            }

            function openErroModal(msg, title) {
                $("#modalLabel").text(title);
                $("#modalBody").text(msg);
                $("#myModal").modal();
            }

            function openDeleteModal() {
                $("#deleteModal").modal();
            }
        });
    </script>

</head>

<body class="bg p-5 mt-3">
    <div class="post_container mx-auto mt-5 rounded px-5 bg-light">
        <div class="row text-left ml-5">
            <?php
            require_once("lib/parsedown-1.7.3/Parsedown.php");
            require("view/menu.php");
            require("view/template_post.php");
            ?>
            <div class="row">
                <h3><?= count($answers) . " answer(s)." ?></h3>
            </div>
            <?php
            include("view/template_answers.php");
            ?>

            <form id="answerForm" method='post' action='post/show/<?=$post->get_id()?>' >
                <textarea name='body' id="mde"></textarea>
                <div class="text-left mt-3 mb-2">
                    <input type='submit' value='Publish Your answer' class="btn btn-info btn-lg dodgerb reduce mb-5"/>
                </div>
            </form>
            <?php if (count($errors) != 0) : ?>
                <div class="alert alert-danger top_buffer red mt-4 ml-5 mr-5" role="alert">
                    <h4 class="alert-heading alert_title text-left">Please correct the following errors!</h4>
                    <p class="text-left">Please correct the following error(s) :</p>
                    <div class="row">
                        <ul class="text-left">
                            <?php foreach ($errors as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            <?php endif; ?>
        </div>
        <div class="modal fade center" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">..</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBody">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Understood</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade center" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-muted text-center">Do you really want to delete this comment ?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <i id="logo" class="far fa-trash-alt fa-7x"></i>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="deleteConfirm" class="btn btn-outline-secondary" data-dismiss="modal">Sure</button>
                        <button type="button" id="cancelConfirm" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once("view/loginModal.php"); ?>
        <?php require_once("view/logoutModal.php"); ?>
        <?php require_once("view/signupModal.php"); ?>



</body>

</html>