<?php
foreach ($answers as $answer) :
    $body = $answer->formatted_body();
?>

    <div class="wrapper_post">
        <div class="row">
        <div class="col-md-2 px-0 mx-0 d-flex py-4">
                   <form class="acceptPan" action="user/accept" method="post">
                    <?php if ($answer->is_accepted()) : ?>
                        <i id="validate_answ" class="fas fa-check fa-3x pl-2"></i>
                        <?php if ($answer->must_show_unaccept_ico($user)) : ?>
                            <button class="upvote_button" type="submit" name="unaccept" value="<?= $answer->get_id() ?>">
                                <i class="fas fa-times fa-3x red_color"></i>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
                <form class="votepan" method="POST" action="user/vote">
                    <button class="upvote_button" type="submit" name="vote" value="upvote">
                        <?php if ($answer->is_upvoted_by($user)) : ?>
                            <i class="fas fa-thumbs-up fa-lg green_color"></i>
                        <?php else : ?>
                            <i class="far fa-thumbs-up fa-lg"></i>
                        <?php endif; ?>
                        </button>
                    <p class="info_vote mt-2"> Score </p>
                    <p class="info_vote mb-2 text-center count"><?= $answer->get_vote_count(); ?></p>
                    <button class="downvote_button" type="submit" name="vote" value="downvote">
                        <?php if ($answer->is_downvoted_by($user)) : ?>
                            <i class="fas fa-thumbs-down fa-lg red_color"></i>
                            <?php else : ?>
                                <i class="far fa-thumbs-down fa-lg"></i>
                            <?php endif; ?>
                            </button>
                    <input type="hidden" name="id" value="<?= $answer->get_id() ?>" />
                </form>
            </div>
            <div class="col-md-10 px-0 mx-0 spec mt-2">
                <?= $body ?>
            </div>

        </div>
        <div class="row d-flex align-items-center ml-5">
            <p class="time_info my-0 py-0 ml-5"><?= $answer->get_time_info(); ?></p>
            <?php if ($answer->can_be_deleted($user)) : ?>
                <div class="delete_buton">
                    <a class="downvote_button" href="post/delete/<?= $answer->get_id() ?>">
                        <i class="far fa-trash-alt fa-1x"></i>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($answer->can_be_edited($user)) : ?>
                <div class="delete_buton">
                    <a class="downvote_button" href="post/edit/<?= $answer->get_id() ?>">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($answer->must_show_accept_ico($user)) : ?>
                <form class="unform" action="user/accept" method="post">
                    <button class="upvote_button" type="submit" name="accept" value="<?= $answer->get_id() ?>">
                        <i class="far fa-check-circle fa-1x"></i>
                    </button>
                <?php endif; ?>
                </form>
        </div>
    </div>
    <div class="comments mt-5 mb-5">
        <div class="comment-div">
            <?php foreach ($answer->get_comments() as $comment) : ?>
                <?php require("view/template_comment.php"); ?>
            <?php endforeach; ?>
        </div>
        <?php if ($user) : ?>
            <a href="comment/add/<?= $answer->get_id() ?>" id="<?=$answer->get_id()?>" class="mb-2 commentBtn">add Comment</a>
            <div class="dnone"></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>