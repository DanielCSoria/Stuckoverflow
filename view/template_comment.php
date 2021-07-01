                    <div class="col px-0 mx-0 spec">
                            <p class="commentaire px-0 mx-0 align-baseline"><?= $comment->get_body() ?> - <?= $comment->get_time_info() ?>
                            
                            <?php if($comment->can_be_deleted($user)):?>
                                <a href="comment/edit/<?=$comment->get_id()?>" data-value="<?=$comment->get_id()?>" class=" dd px-2 editBtn">edit</a>
                                <a href="comment/delete/<?=$comment->get_id()?>" data-value="<?=$comment->get_id()?>" class=" dd deleteBtn">delete</a>
                            <?php endif;?>
                            </p>
                            <div class="dnone"></div>
                    </div>