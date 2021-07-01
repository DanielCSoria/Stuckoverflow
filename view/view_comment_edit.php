<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ask a public question</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/look.css" rel="stylesheet" type="text/css" />
    <link href="css/all.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/stylebis.css" rel="stylesheet" type="text/css" />

</head>

<body class="bg p-5 mt-3">
    <div class="post_container mx-auto mt-5 px-4 border rounded">
        <div class="row text-left ml-5">
            <?php
            require("view/menu.php");
            ?>
            <div class="wrapper_post px-5 mt-5 ">
                <?php if ($post->is_a_question()) : ?>
                    <div class="row px-0 mx-0">
                        <h2><?= $post->get_title(); ?></h2>
                    </div>
                <?php endif; ?>
                <div class="row mt-5 ml-5 noflex">
                    <?php
                    $body = $post->formatted_body();
                    echo $body; ?>
                </div>
                <form method='post' action='comment/edit'>
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">Edit your comment.</small>
                        <textarea class="form-control resize" name="comment" aria-describedby="emailHelp"><?= $comment->get_body() ?></textarea>
                    </div>
                    <button type="submit" style="width:15%;" name="id" value="<?= $comment->get_id() ?>" class="btn btn-primary bt-sm mt-2 mb-4 reduce">Edit comment</button>
                </form>
                <?php if (count($errors) != 0) : ?>
                    <div class="alert alert-danger top_buffer red mt-4 ml-5 mr-5" role="alert">
                        <h4 class="alert-heading alert_title text-left"> Please correct the following errors!</h4>
                        <div class="row">
                            <ul class="text-left">
                                <?php foreach ($errors as $error) : ?>
                                    <li class="ml-3"><?= $error ?></li>
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