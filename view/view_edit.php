<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit post</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/look.css" rel="stylesheet" type="text/css" />
    <link href="css/all.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/stylebis.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="lib/jquery-validation-1.19.1/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="lib/easymde/dist/easymde.min.css">
    <script src="lib/easymde/dist/easymde.min.js"></script>
    <script>
       $(function() {
            var easyMDE = new EasyMDE();
            easyMDE.validate();

            $('#editForm').validate({
                rules: {
                    title: {
                        required: true,
                        minlength: 1,
                        maxlength: 60
                    },
                    body: {
                        required: true,
                    }
                }
            });
       });

    </script>
</head>

<body>
    <?php require("view/menu.php"); ?>

    <body class="bg p-5">
        <div class="card-header post_container mx-auto mt-5 border rounded">
            <div class="row">
                <div class="col text-left mb-2 ">
                    <form method='post' action='post/edit' id="editForm">
                        <?php if ($post->is_a_question()) : ?>
                            <h2 class="text-muted mt-4">Title</h2>
                            <input class="title_ask" name='title' value="<?= $post->get_title() ?>"><br><br>
                        <?php endif; ?>
                        <h2 class="text-muted mt-4">Body</h2>
                        <p>Include all the informations someone would need to answer your question</p>
                        <textarea class="body" name='body'><?= $post->get_body() ?></textarea><br><br>
                        <div class="row">
                            <div class="text-left ml-3">
                                <input type='submit' value="   Edit   " class="btn btn-info btn-lg dodgerb  mb-3">

                                <input type='hidden' value="<?= $post->get_id() ?>" name="post_id">
                            </div>
                        </div>
                    </form>
                    <?php if (count($errors) != 0) : ?>
                        <div class="alert alert-danger top_buffer red mt-4" role="alert">
                            <h4 class="alert-heading alert_title text-left">Please correct the following errors!</h4>

                            <div class="row ml-3">
                                <ul class="text-left">
                                    <ul>
                                        <?php foreach ($errors as $error) : ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </body>

</html>