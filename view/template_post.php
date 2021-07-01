<?php
$body = $post->formatted_body();
?>
<div class="wrapper">
    <div class="wrapper_post">
        <div class="row px-0 mx-0">
            <h2><?= $post->get_title(); ?></h2>
        </div>
        <div class="row px-0 mx-0">

            <p class="mr-2"><?= $post->get_time_info() ?></p>



            <?php if ($post->can_be_deleted($user)) : ?>
                <div class="delete_buton">
                    <a class="downvote_button" href="post/delete/<?= $post->get_id() ?>">
                        <i class="far fa-trash-alt fa-1x"></i>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($post->can_be_edited($user)) : ?>
                <div class="delete_buton">
                    <a class="downvote_button" href="post/edit/<?= $post->get_id() ?>">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <div class="row mt-0 ml-1 py-0">

            <?php foreach ($post->get_tags() as $tag) : ?>
                <div class="d-flex align-items-center badge badge-primary rounded-0 mr-2 py-<?= $post->can_be_edited($user) ? '0' : '2' ?>">
                    <a href="post/index/tag/1/<?= $tag->get_id() ?>" class="text-light"><?= $tag->get_tag_name() ?></a>
                    <?php if ($post->can_be_edited($user)) : ?>
                        <form method="POST" action="tag/unlink_tag">
                            <input name="post_id" value="<?= $post->get_id() ?>" type="hidden" />
                            <button name="tag_id" value="<?= $tag->get_id() ?>" class="btn btn-sm mx-0 my-0 text-light"><i class="far fa-times-circle fa-1x"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($post->can_add_tag($user)) : ?>
                <a class="btn btn-sm btn-primary dropdown-toggle my-auto rounded-0 text-light" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Add
                </a>
                <form class="dropdown-menu" method="POST" action="tag/link_post/">
                    <?php foreach ($tags as $tag) : ?>
                        <input name="post_id" type="hidden" value="<?= $post->get_id() ?>"/>
                        <button name="tag_id" value="<?= $tag->get_id() ?>" class="dropdown-item "><?= $tag->get_tag_name() ?></button>
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-2 px-0 mx-0 d-flex py-4">
            <form class="votepan" action="user/vote/" method="post">
                <button class="upvote_button" type="submit" name="vote" value="upvote">
                    <?php if ($post->is_upvoted_by($user)) : ?>
                        <i  class="fas fa-thumbs-up fa-lg green_color"></i>
                    <?php else : ?>
                        <i class="far fa-thumbs-up fa-lg"></i>
                    <?php endif; ?>
                    </i></button>
                <p class="info_vote mt-2"> Score </p>
                <p class="info_vote count mb-2 text-center"><?= $post->get_vote_count(); ?></p>
                <button class="downvote_button" type="submit" name="vote" value="downvote">
                    <?php if ($post->is_downvoted_by($user)) : ?>
                        <i class="fas fa-thumbs-down fa-lg red_color"></i>
                    <?php else : ?>
                        <i class="far fa-thumbs-down fa-lg"></i>
                    <?php endif; ?>

                </button>
                <input type="hidden" value="<?= $post->get_id() ?>" name="id"/>
            </form>
        </div>
        <div class="col-md-10 spec">
            <?= $body ?>
        </div>
    </div>
</div>
<div class="comments mt-5 mb-5">
    <div class="comment-div">
        <?php foreach ($post->get_comments() as $comment) : ?>
            <?php require("view/template_comment.php"); ?>
        <?php endforeach; ?>
    </div>
    <?php if ($user) : ?>
        <a  href="comment/add/<?= $post->get_id() ?>" id="<?= $post->get_id()?>" class="mb-2 commentBtn">add Comment</a>
        <div class="dnone"></div>
    <?php endif; ?>
</div>
</div>