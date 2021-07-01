<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ask a public question</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.css" rel="stylesheet" />
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <link href="css/stylebis.css" rel="stylesheet" />
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="view/utilsJs/loadUtils.js"></script>
    <script src="css/bootstrap/js/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="lib/easymde/dist/easymde.min.css">
    <script src="lib/easymde/dist/easymde.min.js"></script>


    <script>
        $(function() {
            //delete initialisation of easyMDE var to test comment's body validation
            var easyMDE = new EasyMDE();
            var nbTags = <?= Configuration::get("max_tags") ?>;
            
            validateAskForm();
            $("input:text:first").focus();

            function validateAskForm() {
                validator = $('#askForm').validate({
                    rules: {
                        title: {
                            required: true,
                            minlength: 1,
                            maxlength: 60

                        },
                        'tags[]': {
                            maxlength: nbTags

                        },
                        body: {
                            required:true,
                        }
                    },
                    messages: {
                        'tags[]': {
                            maxlength: "Can't add more than " + nbTags + " tags"
                        }
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("name") == "tags[]")
                            error.insertAfter(".tags-div");
                        else if(element.attr('name') == "title")
                            element.next().html(error);
                        else
                            error.insertAfter(element);
                    }
                });

            };
            function validateCheckboxes() {
                    let nbChecked = 0;
                    let finish = false;
                    $(".form-check-input").each(function(index) {
                        if ($(this).is(':checked'))
                            ++nbChecked;
                        if (nbChecked > nbTags) {
                            finish = true;
                            return false;
                        }
                    });

                    if (finish)
                        return false;
                    return true;
                }
        });
    </script>
</head>

<body class="bg p-5 mt-3">
    <?php require("view/menu.php"); ?>
    <div class="post_container mx-auto mt-5 rounded px-5 bg-light">
        <div class="row mx-5">
            <div class="col text-left mb-2">
                <form method='post' action='post/ask' id="askForm">
                    <h2 class="text-muted mt-4">Title</h2>
                    <p>Be specific and imagine you're asking a question to another person</p>
                    <input class="form-control" name='title' value="<?= $title ?>">
                    <div class="mt-2"></div>
                    <h2 class="text-muted mt-4">Tags</h2>
                    <p>Add up to <?= Configuration::get("max_tags") ?> tags to describe what your question is about</p>
                    <div class="tags-div">
                        <?php
                        if ($tags) :
                            foreach ($tags as $tag) :
                        ?>
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" name="tags[]" <?= in_array($tag, $selected_tags) ? 'checked' : ' ' ?> type="checkbox" id="<?= $tag->get_id() ?>" value="<?= $tag->get_id() ?>">
                                    <label class="form-check-label"><?= $tag->get_tag_name() ?></label>
                                </div>

                            <?php
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-muted mt-1">Body</h2>
                    <p>Include all the informations someone would need to answer your question</p>
                    <textarea id="mde" class="form-control mt-5" name='body'><?= $body ?></textarea>

                    <div class="row mt-5">
                        <div class="text-left ml-3">
                            <input type='submit' name="submit" value='Publish Your Question' class="btn btn-info btn-lg dodgerb  mb-3 submitBtn">
                        </div>
                    </div>
                </form>

                <?php if (count($errors) != 0) : ?>
                    <div class="alert alert-danger top_buffer red mt-4 ml-5 mr-5" role="alert">
                        <h4 class="alert-heading alert_title text-left">Please correct the following errors!</h4>
                        <p class="text-left">Please correct the following error(s) :</p>
                        <div class="row">
                            <ul class="text-left">
                                <?php foreach ($errors as $error) : ?>
                                    <li class="ml-4"><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php require_once("view/loginModal.php"); ?>
    <?php require_once("view/logoutModal.php"); ?>
    <?php require_once("view/signupModal.php"); ?>



</body>

</html>