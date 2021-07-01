<!DOCTYPE html>
<html lang="en">

<head>
    <title>StuckOverflow - Tags</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.css" rel="stylesheet">
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="css/stylebis.css" rel="stylesheet">
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="lib/jquery-validation-1.19.1/jquery.validate.min.js"></script>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
    <script src="view/utilsJs/loadUtils.js"></script>

    <script>
        $(function() {
            var tagName = "";
            var validator;
            $('.tagEdit').each(function() {
                validator = $(this).validate({
                    rules: {
                        edit_tag: {
                            remote: {
                                url: 'tag/tag_name_exists_service',
                                type: 'post',
                                data: {
                                    newtag: function() {
                                        return tagName;
                                    }
                                }
                            },
                            required: true,
                            maxlength: 15,
                        }

                    },
                    messages: {
                        edit_tag: {
                            remote: 'Tag name exists',
                        },
                    },
                    errorPlacement: function(error, element) {
                        let div = element.parent().parent().next('.errorDiv');
                        div.html(error);
                    },
                    success: function(label, element) {
                        label.parent().empty();
                    }


                });
            });

            $(".editTag").on("input", function(e) {
                tagName = $(this).val();
                $(this).parent().valid();
            });

            $('#newTagForm').validate({
                rules: {
                    newtag: {
                        remote: {
                            url: 'tag/tag_name_exists_service',
                            type: 'post',
                            data: {
                                newtag: function() {
                                    return $("#newtag").val();
                                }
                            }
                        },
                        required: true,
                        maxlength: 15,

                    },
                },
                messages: {
                    newtag: {
                        remote: 'Tag name exists',
                    },
                },
                errorPlacement: function(error) {
                    error.insertAfter('.addTag');
                },

            });

            $(".tagEdit").on('submit', function(e) {
                e.preventDefault();
                let editInput = $(this).parent().find('.editTag');
                tagName = editInput.val();
                editInput.valid()
            });

            $("#newTagForm").on('submit',function(e){
                $(this).valid();
            });


        });
    </script>

</head>

<body class="bg p-5">

    <?php require("view/menu.php"); ?>

    <div class="card-body post_container mx-auto rounded bg-light mb-5 mt-5 ">
        <div class="row">
            <div class="row">
                <h4 class="text-muted ml-5  mt-3 mb-3 ">Tag name</h4>
            </div>
            <div class="col text-center">
                <?php if ($user != false) :
                    if ($user->is_admin()) : ?>
                        <h4 class="text-muted mt-3 mb-3">Actions</h4>
                <?php endif;
                endif; ?>
            </div>
        </div>
        <div class="contain">
            <?php

            if ($tags) :
                foreach ($tags as $tag) :
            ?>
                    <div class="pc">
                        <div class="row row_size border-bottom active pt-3 ">
                            <div class="col">
                                <div class="row">
                                    <div class="row text-inline ml-5 mb-3">
                                        <h5 class="mb-0 text-muted"><?= $tag->get_tag_name() ?></h5>
                                        <a href="post/index/tag/1/<?= $tag->get_id() ?>" class="text-invisible">
                                            <h5 class="mb-0 ml-2 text-info">(<?= $tag->count_related_posts($tag->get_id()) ?>) posts</h5>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php if ($user != false) :
                                if ($user->is_admin()) : ?>
                                    <div class="col mb-3 ">
                                        <div class="row ">
                                            <div class="col-8 ">
                                                <div class="input-group ">

                                                    <form class="form-inline tagEdit" method='post' action='tag/edit/'>
                                                        <input type="text" class="form-control editTag" value="<?= $tag->get_tag_name() ?>" name="edit_tag">
                                                        <button class="btn btn-outline ml-1 btn-long dodgerb mr-8" type="submit" name="id" value="<?= $tag->get_id() ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </form>

                                                    <a class="btn btn-danger ml-2 deleteButton" href='tag/delete/<?= $tag->get_id() ?>'>
                                                        <i class="far fa-trash-alt fa-1x"></i>
                                                    </a>
                                                </div>
                                                <div class="errorDiv"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                endforeach;
            else :
                ?>
        </div>
    <?php endif; ?>
    <?php if ($user != false) :
        if ($user->is_admin()) : ?>
            <form id="newTagForm" method='post' action='tag/add'>
                <div class="col-4 mt-4 ml-1 addTag">
                    <div class="input-group mb-3">
                        <input id="newtag" type="text" name="newtag" placeholder="TagName" class="form-control" />
                        <div class="input-group-append">
                            <button class="btn btn-outline btn-long dodgerb mr-8" type="submit"> + </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (count($errors) != 0) : ?>
        <div class="alert alert-danger top_buffer red mt-4 ml-5 mr-5" role="alert">
            <h4 class="alert-heading alert_title text-left">Please correct the following errors!</h4>
            <p class="text-left">Please correct the following error(s) :</p>
            <div class="row">
                <ul class="text-left list-unstyled">
                    <?php foreach ($errors as $error) : ?>
                        <li class="ml-4"><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    <?php endif; ?>
    </div>
    <?php require_once("view/loginModal.php"); ?>
    <?php require_once("view/logoutModal.php"); ?>
    <?php require_once("view/signupModal.php"); ?>

</div>
</body>

</html>